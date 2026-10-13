// In-browser stand-in for the Laravel API. Installed as the default axios
// adapter, so every axios instance in the app (api, docApi, cloudinaryApi)
// is answered from a small database kept in localStorage.
import axios, { AxiosError } from 'axios'
import { createSeed } from './seed'
import { emitBroadcast } from '../echo-stub'

const DB_KEY = 'umsDemoDb'
const PER_PAGE = 10
const LATENCY_MS = 250

export const API_ROOT = import.meta.env.VITE_API_BASE_URL.replace(/\/$/, '')

let db = load()

function load() {
  try {
    const saved = localStorage.getItem(DB_KEY)
    if (saved) return JSON.parse(saved)
  } catch (e) { /* storage unavailable: fall back to fresh seed */ }
  return createSeed()
}

function save() {
  try { localStorage.setItem(DB_KEY, JSON.stringify(db)) } catch (e) { /* quota or private mode */ }
}

export function resetDemoData() {
  try { localStorage.removeItem(DB_KEY) } catch (e) { /* ignore */ }
  db = createSeed()
}

// ---------- helpers ----------

const now = () => new Date().toISOString().replace('Z', '000Z')
const nextId = (rows) => rows.reduce((m, r) => Math.max(m, r.id), 0) + 1
const byId = (rows, id) => rows.find(r => String(r.id) === String(id))
const byNewest = (a, b) => (b.created_at || '').localeCompare(a.created_at || '') || b.id - a.id
const toDateTime = (d) => (d && !String(d).includes('T') ? `${d}T00:00:00.000000Z` : d || null)

const batchOf = (id) => byId(db.batches, id) || null
const withBatch = (u) => ({ ...u, batches: batchOf(u.batchId) })
const withDocs = (u) => ({ ...withBatch(u), student_doc: db.studentDocs.filter(d => d.userId === u.id) })
const feeWithBatch = (f) => ({ ...f, batches: batchOf(f.batchId) })
const paidFor = (userId, feeId) => db.feeStudents
  .filter(p => String(p.userId) === String(userId) && String(p.feeId) === String(feeId))
  .reduce((s, p) => s + Number(p.paid_amount), 0)

// Mirrors Laravel's LengthAwarePaginator JSON so the existing pagination UI works
function paginate(items, page, path) {
  const total = items.length
  const lastPage = Math.max(1, Math.ceil(total / PER_PAGE))
  const current = Math.min(Math.max(1, Number(page) || 1), lastPage)
  const start = (current - 1) * PER_PAGE
  const url = (p) => `${API_ROOT}${path}?page=${p}`
  const links = [{ url: current > 1 ? url(current - 1) : null, label: '&laquo; Previous', active: false }]
  for (let p = 1; p <= lastPage; p++) links.push({ url: url(p), label: String(p), active: p === current })
  links.push({ url: current < lastPage ? url(current + 1) : null, label: 'Next &raquo;', active: false })
  return {
    current_page: current,
    data: items.slice(start, start + PER_PAGE),
    first_page_url: url(1),
    from: total ? start + 1 : null,
    last_page: lastPage,
    last_page_url: url(lastPage),
    links,
    next_page_url: current < lastPage ? url(current + 1) : null,
    path: `${API_ROOT}${path}`,
    per_page: PER_PAGE,
    prev_page_url: current > 1 ? url(current - 1) : null,
    to: total ? Math.min(start + PER_PAGE, total) : null,
    total,
  }
}

const isStaffRole = (u) => u.role === 'STAFF' || u.role === 'ADMIN'
function usersFor(isStaff) {
  if (String(isStaff) === '1') return db.users.filter(isStaffRole)
  if (String(isStaff) === '0') return db.users.filter(u => u.role === 'STUDENT').map(withDocs)
  return db.users
}

function applyUserPayload(user, body) {
  const d = body.data || {}
  const fields = ['name', 'username', 'email', 'mobile', 'gender', 'course', 'rollNo', 'university', 'visaType', 'visaStatus',
    'designation', 'department', 'location', 'passportNumber', 'passportExpiryDate', 'passportIssuingCountry',
    'passportIssueDate', 'insuranceStatus', 'insuranceExpiryDate']
  fields.forEach(f => { user[f] = d[f] ?? null })
  user.dob = toDateTime(d.dob)
  user.batchId = d.batchId ? Number(d.batchId) : null
  user.address = body.address || []
  user.parentDetails = body.parentDetails || []
  user.role = body.isStaff === 1 ? 'STAFF' : 'STUDENT'
  if (d.profile_picture) user.profile_picture = d.profile_picture
  if (d.password) db.passwords[user.username] = d.password
  user.updated_at = now()
}

function validateUser(body, ignoreId) {
  const d = body.data || {}
  const missing = ['name', 'username', 'email', 'dob', 'gender'].filter(f => !d[f])
  if (missing.length) return `The ${missing[0]} field is required.`
  if (db.users.some(u => u.username === d.username && u.id !== ignoreId)) return 'The username has already been taken.'
  if (db.users.some(u => u.email === d.email && u.id !== ignoreId)) return 'The email has already been taken.'
  return null
}

function noticesForUser(user) {
  const sorted = [...db.notices].sort(byNewest)
  const pub = sorted.filter(n => n.audience === 'ALL')
  if (isStaffRole(user)) return [...pub, ...sorted.filter(n => n.audience === 'STAFF' || n.audience === 'ADMIN')]
  return [...pub, ...sorted.filter(n => n.audience === 'STUDENT' && String(n.customBatch) === String(user.batchId))]
}

const readFileAsDataUrl = (file) => new Promise((resolve) => {
  const reader = new FileReader()
  reader.onload = () => resolve(reader.result)
  reader.onerror = () => resolve(URL.createObjectURL(file))
  reader.readAsDataURL(file)
})

// Small files become data URLs so they survive a reload; large ones only last the session
async function fakeCloudinaryUpload(form) {
  const file = form.get('file')
  const url = file.size < 400 * 1024 ? await readFileAsDataUrl(file) : URL.createObjectURL(file)
  const dot = file.name.lastIndexOf('.')
  return {
    secure_url: url,
    original_filename: dot > 0 ? file.name.slice(0, dot) : file.name,
    format: dot > 0 ? file.name.slice(dot + 1) : 'bin',
    delete_token: `demo-${Date.now()}`,
  }
}

// ---------- routes ----------

const routes = [
  ['POST', '/users/authenticate', ({ body }) => {
    const user = db.users.find(u => u.username === body.username)
    if (!user || db.passwords[user.username] !== body.password) {
      return { status: 0, message: 'Invalid username/password', data: '' }
    }
    return { status: 1, message: 'Validated successfully', data: withBatch(user), token: `demo-token-${user.id}` }
  }],

  ['POST', '/users/create', ({ body }) => {
    const error = validateUser(body)
    if (error) return { status: 0, message: error, data: {} }
    const user = { id: nextId(db.users), profile_picture: null, created_at: now() }
    applyUserPayload(user, body)
    db.passwords[user.username] = body.data.password || user.username
    db.users.push(user)
    return { status: 1, message: 'New user added successfully', data: user }
  }],
  ['GET', '/users', ({ query }) => ({
    status: 1, message: 'Data fetched successfully',
    data: paginate(usersFor(query.isStaff).sort(byNewest), query.page, '/users'),
  })],
  ['POST', '/users/byParams', ({ body, query }) => {
    const { param, value } = body
    let rows = db.users
    if (value) {
      rows = rows.filter(u => (param === 'id' || param === 'gender')
        ? String(u[param]) === String(value)
        : String(u[param] ?? '').toLowerCase().includes(String(value).toLowerCase()))
    }
    return { status: 1, message: 'Data fetched successfully', data: paginate([...rows].sort(byNewest), query.page, '/users/byParams') }
  }],
  ['POST', '/users/find', ({ body, query }) => {
    if (!body.name) return { message: 'name is required', status: 1 }
    const rows = usersFor(body.isStaff).filter(u => u.name.toLowerCase().includes(body.name.toLowerCase()))
    return { data: paginate(rows.map(withBatch), query.page, '/users/find'), status: 1 }
  }],
  ['POST', '/users/checking', ({ body }) => {
    if (!body.username) return { message: 'username is required', status: 0 }
    const field = body.field || 'username'
    return { exists: db.users.some(u => u[field] === body.username), status: 1 }
  }],
  ['POST', '/users/getById', ({ body }) => {
    const user = byId(db.users, body.id)
    return user ? withBatch(user) : [404, { message: 'User not found', status: 0 }]
  }],
  ['GET', '/users/bybatchId/:id', ({ params }) => ({
    status: 1, message: 'fetched successfully',
    data: db.users.filter(u => String(u.batchId) === params.id).sort((a, b) => a.id - b.id),
  })],
  ['PUT', '/users/:id', ({ params, body }) => {
    const user = byId(db.users, params.id)
    if (!user) return { status: 0, message: 'User not found to update !', data: params.id }
    const error = validateUser(body, user.id)
    if (error) return { status: 0, message: error, data: {} }
    const oldUsername = user.username
    applyUserPayload(user, body)
    if (oldUsername !== user.username && !db.passwords[user.username]) db.passwords[user.username] = db.passwords[oldUsername]
    return { status: 1, message: 'Updated successfully', data: params.id }
  }],
  ['DELETE', '/users/:id', ({ params }) => {
    const user = byId(db.users, params.id)
    if (!user) return { status: 0, message: 'User not found', data: params.id }
    db.users = db.users.filter(u => u !== user)
    return { status: 1, message: 'Deleted successfully', data: '' }
  }],
  // The real API resets the password to the username on this GET
  ['GET', '/users/:id', ({ params }) => {
    const user = byId(db.users, params.id)
    if (!user) return { status: 0, message: 'User not found for password reset', data: params.id }
    db.passwords[user.username] = user.username
    return { status: 1, message: 'Password reset completed successfully', data: user }
  }],

  ['GET', '/batches', ({ query }) => ({ data: paginate([...db.batches].sort((a, b) => b.id - a.id), query.page, '/batches') })],
  ['GET', '/batches/all', () => ({ data: [...db.batches].sort((a, b) => b.id - a.id) })],
  ['POST', '/batches/create', ({ body }) => {
    const batch = { id: nextId(db.batches), ...pickBatch(body), is_passout: 0, image: null, created_by: 1, updated_by: 1, created_at: now(), updated_at: now() }
    db.batches.push(batch)
    return { message: 'Batch created', data: batch, status: 1 }
  }],
  ['POST', '/batches/find', ({ body, query }) => {
    if (!body.name) return { message: 'Batch name is required', status: 1 }
    const rows = db.batches.filter(b => b.name.toLowerCase().includes(body.name.toLowerCase()))
    return { data: paginate(rows, query.page, '/batches/find'), status: 1 }
  }],
  ['PUT', '/batches/:id', ({ params, body }) => {
    const batch = byId(db.batches, params.id)
    if (batch) Object.assign(batch, pickBatch(body), { updated_at: now() })
    return { message: `${body.name} updated`, status: 1, data: batch ? 1 : 0 }
  }],
  ['DELETE', '/batches/:id', ({ params }) => {
    db.batches = db.batches.filter(b => String(b.id) !== params.id)
    return { message: 'Batch removed', status: 1 }
  }],

  ['GET', '/fees', () => ({ data: [...db.fees].sort((a, b) => b.id - a.id).map(feeWithBatch), status: 1 })],
  ['GET', '/fees/getActive', () => ({
    message: 'fetched successfully', status: 1,
    data: db.fees.filter(f => f.status === 'ACTIVE').sort(byNewest).map(feeWithBatch),
  })],
  ['GET', '/fees/bybatch/:id', ({ params }) => ({
    message: 'found succesfully', status: 1,
    data: db.fees.filter(f => String(f.batchId) === params.id).sort((a, b) => b.id - a.id),
  })],
  ['POST', '/fees/feePayment', ({ body }) => {
    const fee = byId(db.fees, body.feeId)
    if (!fee) return { status: 0, message: 'Fee rule not found' }
    const amount = Number(body.paid_amount) || 0
    if (amount > fee.amount) return { status: 0, message: 'Overplay restriction!' }
    if (paidFor(body.userId, fee.id) + amount > fee.amount) return { status: 0, message: 'Overpay blocked by system or Already paid' }
    const entry = {
      id: nextId(db.feeStudents), feeId: fee.id, userId: Number(body.userId), paid_amount: amount,
      payment: JSON.stringify(body.payment || {}), created_by: 1, created_at: now(), updated_at: now(),
    }
    db.feeStudents.push(entry)
    return { status: 1, message: 'Fee paid successfully', data: entry }
  }],
  ['GET', '/fees/paymentSummary/:id', ({ params }) => ({
    status: 1,
    data: db.fees
      .filter(f => db.feeStudents.some(p => String(p.userId) === params.id && p.feeId === f.id))
      .map(f => {
        const paid = paidFor(params.id, f.id)
        return { id: f.id, type: f.type, total_allocated: f.amount, due: f.due, total_paid: paid, balance: f.amount - paid }
      }),
  })],
  ['POST', '/fees/paidFee', ({ body }) => ({ data: paidFor(body.userId, body.feeId) })],
  ['GET', '/fees/yearly', () => {
    const year = String(new Date().getFullYear())
    const thisYear = (row) => (row.created_at || '').startsWith(year)
    const totalAssigned = db.fees.filter(thisYear)
      .reduce((s, f) => s + f.amount * db.users.filter(u => u.batchId === f.batchId).length, 0)
    const recentRecords = [...db.feeStudents].sort((a, b) => b.id - a.id).slice(0, 5)
      .map(p => ({ ...p, fee: byId(db.fees, p.feeId) || {}, student: byId(db.users, p.userId) || { name: 'Removed user' } }))
    return {
      message: 'fetched successfully', status: 1,
      data: { year, totalAmount: db.feeStudents.filter(thisYear).reduce((s, p) => s + Number(p.paid_amount), 0), totalAssigned, recentRecords },
    }
  }],
  ['POST', '/fees/assign', ({ body }) => {
    if (!Array.isArray(body.batchId) || !body.batchId.length || !body.type || body.amount === '' || body.amount == null) {
      return [422, { status: 0, message: 'Validation failed', errors: {} }]
    }
    const created = body.batchId.map(batchId => {
      const fee = { id: nextId(db.fees), batchId: Number(batchId), type: body.type, amount: Number(body.amount), status: body.status, due: body.due || null, created_by: 1, created_at: now(), updated_at: now() }
      db.fees.push(fee)
      return fee
    })
    return { status: 1, message: 'Fee created successfully', data: created }
  }],
  ['PUT', '/fees/:id', ({ params, body }) => {
    const fee = byId(db.fees, params.id)
    if (!fee) return [422, { status: 0, message: 'fee header not found' }]
    if (body.batchId?.length) fee.batchId = Number(body.batchId.join(''))
    if (body.type) fee.type = body.type
    if (body.amount) fee.amount = Number(body.amount)
    if (body.status) fee.status = body.status
    fee.due = body.due || null
    fee.updated_at = now()
    return { status: 1, message: 'Updated successfully', data: fee }
  }],

  ['POST', '/documents/userdocs', ({ body }) => {
    if (!body.url || !body.userId) return [400, { message: 'user or doc not found', status: 1 }]
    const doc = { id: nextId(db.studentDocs), userId: Number(body.userId), url: body.url, title: body.title || 'Document', created_at: now(), updated_at: now() }
    db.studentDocs.push(doc)
    return { message: 'Uploaded successfully', status: 1, data: doc }
  }],
  ['GET', '/documents/:id', ({ params }) => ({
    message: 'Fetched successfully', status: 1,
    data: db.studentDocs.filter(d => String(d.userId) === params.id).sort((a, b) => b.id - a.id),
  })],
  ['DELETE', '/documents/:id', ({ params }) => {
    db.studentDocs = db.studentDocs.filter(d => String(d.id) !== params.id)
    return { status: 1, message: 'Deleted successfully', data: '' }
  }],

  ['POST', '/notice/create', ({ body }) => {
    if (!body.title || !body.type || !body.audience || !body.html || !body.expiry) return [422, { errors: { title: ['Required fields are missing'] } }]
    const notice = {
      id: nextId(db.notices), title: body.title, type: body.type, audience: body.audience,
      customBatch: body.customBatch ? Number(body.customBatch) : null, html: body.html, expiry: body.expiry,
      show: 1, created_at: now(), updated_at: now(),
    }
    db.notices.push(notice)
    // Same channels the Laravel events broadcast on
    if (notice.audience === 'ALL') emitBroadcast('notifications', 'NewNotification', 'New notice is published')
    else if (notice.audience === 'STUDENT') emitBroadcast(notice.customBatch ? `student-notice-${notice.customBatch}` : 'student-notice', 'UserNotice', 'New notice is published')
    else emitBroadcast('staff-notice', 'UserNotice', 'New notice is published')
    return { message: 'notice created successfully', data: notice, status: 1 }
  }],
  ['GET', '/notice/all', ({ query }) => ({
    status: 1, message: 'fetched successfully',
    data: paginate([...db.notices].sort(byNewest).map(n => ({ ...n, batches: batchOf(n.customBatch) })), query.page, '/notice/all'),
  })],
  ['POST', '/notices/find', ({ body, query }) => ({
    status: 1, message: 'fetched successfully',
    data: paginate(db.notices.filter(n => n.title.toLowerCase().includes((body.title || '').toLowerCase())).sort(byNewest)
      .map(n => ({ ...n, batches: batchOf(n.customBatch) })), query.page, '/notices/find'),
  })],
  ['GET', '/notice/getDetails/:id', ({ params }) => {
    const notice = byId(db.notices, params.id)
    return notice ? { status: 1, data: notice, message: 'fetched successfully' } : [400, { message: 'notice not found' }]
  }],
  ['GET', '/notice/getForUser/:id', ({ params }) => {
    const user = byId(db.users, params.id)
    return user ? { status: 1, data: noticesForUser(user), message: 'fetched successfully' } : [500, { message: 'error while fetching notices' }]
  }],

  ['POST', '/images/remove', () => ({ status: 1, message: 'Image removed' })],
  ['POST', '/images/upload-image', async ({ body }) => {
    const res = await fakeCloudinaryUpload(body)
    return { message: 'Image uploaded!', url: res.secure_url }
  }],
  ['POST', '/documents/upload', async ({ body }) => {
    const res = await fakeCloudinaryUpload(body)
    return { message: 'Image uploaded!', url: res.secure_url, fileName: body.get('file').name }
  }],
].map(([method, pattern, handler]) => {
  const keys = []
  const regex = new RegExp('^' + pattern.replace(/:(\w+)/g, (_, k) => { keys.push(k); return '([^/]+)' }) + '/?$')
  return { method, regex, keys, handler }
})

function pickBatch(body) {
  const { name, description, start_date, end_date, status, semester } = body
  return { name, description, start_date, end_date, status, semester: Number(semester) }
}

const cloudinaryRoutes = {
  '/image/upload': ({ body }) => fakeCloudinaryUpload(body),
  '/auto/upload': ({ body }) => fakeCloudinaryUpload(body),
  '/delete_by_token': () => ({ result: 'ok' }),
}

// ---------- axios adapter ----------

function fullUrl(config) {
  const url = config.url || ''
  if (/^https?:\/\//i.test(url)) return url
  const base = (config.baseURL || API_ROOT).replace(/\/+$/, '')
  return `${base}/${url.replace(/^\/+/, '')}`
}

function parseBody(data) {
  if (data instanceof FormData) return data
  if (typeof data === 'string' && data) {
    try { return JSON.parse(data) } catch (e) { return {} }
  }
  return data || {}
}

async function demoAdapter(config) {
  const url = new URL(fullUrl(config), window.location.origin)
  const method = (config.method || 'get').toUpperCase()
  const query = { ...Object.fromEntries(url.searchParams), ...(config.params || {}) }
  const body = parseBody(config.data)

  await new Promise(r => setTimeout(r, LATENCY_MS))

  let handler
  let params = {}
  if (url.hostname === 'api.cloudinary.com') {
    handler = cloudinaryRoutes[url.pathname.replace(/^\/v1_1\/[^/]+/, '')]
  } else {
    const path = url.pathname.replace(/\/{2,}/g, '/').replace(/^\/api(?=\/|$)/, '') || '/'
    const route = routes.find(r => r.method === method && r.regex.test(path))
    if (route) {
      const match = path.match(route.regex)
      params = Object.fromEntries(route.keys.map((k, i) => [k, decodeURIComponent(match[i + 1])]))
      handler = route.handler
    }
  }

  let status = 200
  let data
  if (!handler) {
    status = 404
    data = { message: `Demo API has no route for ${method} ${url.pathname}` }
    console.warn('[demo]', data.message)
  } else {
    const result = await handler({ params, query, body })
    if (Array.isArray(result) && typeof result[0] === 'number') [status, data] = result
    else data = result
    if (method !== 'GET') save()
  }

  const response = { data: structuredClone(data), status, statusText: status === 200 ? 'OK' : 'Error', headers: {}, config, request: {} }
  if (status >= 400) {
    throw new AxiosError(`Request failed with status code ${status}`, AxiosError.ERR_BAD_REQUEST, config, response.request, response)
  }
  return response
}

// Must run before any axios.create() so the instances inherit it
axios.defaults.adapter = demoAdapter
