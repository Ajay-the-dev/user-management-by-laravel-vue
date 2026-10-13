// Seed data for the demo backend. Shapes mirror what the Laravel API returns.

const ts = (date) => `${date}T09:30:00.000000Z`

const batches = [
  { id: 1, name: 'MBBS 2023', description: 'Batch of 2023 - General Medicine', start_date: '2023-09-01', end_date: '2029-06-30', status: 'active', semester: 6, is_passout: 0, image: null },
  { id: 2, name: 'MBBS 2024', description: 'Batch of 2024 - General Medicine', start_date: '2024-09-01', end_date: '2030-06-30', status: 'active', semester: 4, is_passout: 0, image: null },
  { id: 3, name: 'MBBS 2025', description: 'Batch of 2025 - General Medicine', start_date: '2025-09-01', end_date: '2031-06-30', status: 'active', semester: 2, is_passout: 0, image: null },
  { id: 4, name: 'MBBS 2019', description: 'Batch of 2019 - Graduated', start_date: '2019-09-01', end_date: '2025-06-30', status: 'inactive', semester: 10, is_passout: 1, image: null },
].map(b => ({ ...b, created_by: 1, updated_by: 1, deleted_by: null, created_at: ts(b.start_date), updated_at: ts(b.start_date), deleted_at: null }))

const staffSeed = [
  ['Admin User', 'admin', 'ADMIN', 'Administrator', 'Administration'],
  ['Dr. Meera Nair', 'meera', 'STAFF', 'Professor', 'Anatomy'],
  ['Dr. Arjun Menon', 'arjun', 'STAFF', 'Associate Professor', 'Physiology'],
  ['Kavya Iyer', 'kavya', 'STAFF', 'Accountant', 'Finance'],
]

const studentNames = [
  'Demo Student', 'Aisha Khan', 'Rahul Sharma', 'Sneha Patel', 'Vikram Rao', 'Ananya Das',
  'Rohan Gupta', 'Priya Reddy', 'Karthik Pillai', 'Fatima Sheikh', 'Aditya Joshi', 'Nisha Verma',
  'Siddharth Bose', 'Lakshmi Krishnan', 'Imran Ali', 'Divya Kapoor', 'Manoj Kumar', 'Riya Thomas',
]
const cities = ['Kochi', 'Chennai', 'Bengaluru', 'Mumbai', 'Hyderabad', 'Delhi']

const baseUser = (id, name, username, extra) => ({
  id,
  name,
  username,
  email: `${username}@demo-college.edu`,
  email_verified_at: null,
  mobile: `98${String(4700000 + id * 7919).padStart(8, '0')}`,
  dob: ts(`${1996 + (id % 8)}-0${(id % 9) + 1}-1${id % 9}`).replace('09:30:00', '00:00:00'),
  gender: id % 2 ? 'male' : 'female',
  address: [{ street: `${10 + id}, MG Road`, city: cities[id % cities.length], country: 'India' }],
  profile_picture: null,
  course: null, rollNo: null, university: null, location: null,
  visaType: null, visaStatus: null, visaExpiryDate: null,
  insuranceStatus: null, insuranceExpiryDate: null,
  designation: null, department: null, batchId: null,
  parentDetails: [],
  passportNumber: null, passportExpiryDate: null, passportIssuingCountry: null, passportIssueDate: null,
  created_at: ts(`2025-0${(id % 9) + 1}-0${(id % 9) + 1}`),
  updated_at: ts('2025-10-01'),
  ...extra,
})

const users = []
const passwords = {}

staffSeed.forEach(([name, username, role, designation, department], i) => {
  users.push(baseUser(i + 1, name, username, { role, designation, department, location: 'Main Campus' }))
  passwords[username] = username
})

studentNames.forEach((name, i) => {
  const id = staffSeed.length + i + 1
  const username = i === 0 ? 'student' : name.split(' ')[0].toLowerCase() + id
  const batchId = (i % 3) + 1
  users.push(baseUser(id, name, username, {
    role: 'STUDENT',
    batchId,
    course: 'MBBS',
    rollNo: `MB${2022 + batchId}-${String(i + 1).padStart(3, '0')}`,
    university: 'OSH - IMF',
    location: 'active',
    visaType: 'EDUCATION',
    visaStatus: 'active',
    insuranceStatus: 'active',
    insuranceExpiryDate: '2027-08-31',
    passportNumber: `P${String(8812300 + id)}`,
    passportIssuingCountry: 'India',
    passportIssueDate: '2022-05-10',
    passportExpiryDate: '2032-05-09',
    parentDetails: [{ name: `Parent of ${name.split(' ')[0]}`, relation: i % 2 ? 'Father' : 'Mother', email: `parent${id}@mail.com`, mobile: `97${String(1100000 + id * 313).padStart(8, '0')}` }],
  }))
  passwords[username] = username
})

const feeSeed = [
  ['TUTION', 85000, '2026-07-15'],
  ['HOSTEL', 36000, '2026-08-01'],
  ['EXAM', 4500, '2026-11-20'],
  ['LIBRARY', 2000, null],
]
const fees = []
batches.slice(0, 3).forEach((b) => {
  feeSeed.forEach(([type, amount, due]) => {
    fees.push({
      id: fees.length + 1, batchId: b.id, type, amount, status: 'ACTIVE', due,
      created_by: 1, updated_by: null, deleted_by: null,
      created_at: ts('2026-06-01'), updated_at: ts('2026-06-01'), deleted_at: null,
    })
  })
})

// A spread of full and partial payments so the dashboards have something to show
const modes = ['CASH', 'UPI', 'TRANSFER']
const feeStudents = []
users.filter(u => u.role === 'STUDENT').forEach((s, i) => {
  fees.filter(f => f.batchId === s.batchId).forEach((f, j) => {
    if ((i + j) % 3 === 2) return
    const partial = (i + j) % 4 === 0
    const day = String(((i * 3 + j * 5) % 27) + 1).padStart(2, '0')
    const month = String(6 + ((i + j) % 4)).padStart(2, '0')
    feeStudents.push({
      id: feeStudents.length + 1,
      feeId: f.id,
      userId: s.id,
      paid_amount: partial ? Math.round(f.amount / 2) : f.amount,
      payment: JSON.stringify({ selectedPaymentMode: modes[(i + j) % 3], paymentReference: `TXN${100230 + feeStudents.length}` }),
      created_by: 1, updated_by: null, deleted_by: null,
      created_at: ts(`2026-${month}-${day}`), updated_at: ts(`2026-${month}-${day}`), deleted_at: null,
    })
  })
})

const notices = [
  { title: 'Diwali holidays announced', type: 'general', audience: 'ALL', customBatch: null, expiry: '2026-11-10', created: '2026-10-08',
    html: '<p>The college will remain closed from <strong>October 19 to October 23</strong> for Diwali. Classes resume on October 26.</p>' },
  { title: 'Semester exam schedule published', type: 'exam', audience: 'STUDENT', customBatch: 1, expiry: '2026-12-15', created: '2026-10-05',
    html: '<p>The end-semester theory exams start on <strong>November 24</strong>. Hall tickets will be issued from the exam cell.</p><ul><li>Anatomy - Nov 24</li><li>Physiology - Nov 27</li><li>Biochemistry - Dec 1</li></ul>' },
  { title: 'Fee payment deadline reminder', type: 'urgent', audience: 'STUDENT', customBatch: 2, expiry: '2026-10-31', created: '2026-10-02',
    html: '<p>Pending hostel fees must be cleared by <strong>October 31</strong> to avoid a late fee.</p>' },
  { title: 'Annual sports meet - volunteers needed', type: 'event', audience: 'ALL', customBatch: null, expiry: '2026-11-30', created: '2026-09-28',
    html: '<p>The annual sports meet is on <em>November 14-15</em>. Register with the sports committee by November 5.</p>' },
  { title: 'Faculty meeting on curriculum review', type: 'general', audience: 'STAFF', customBatch: null, expiry: '2026-10-25', created: '2026-09-25',
    html: '<p>All faculty are requested to attend the curriculum review meeting in the conference hall on October 20 at 3 PM.</p>' },
].map((n, i) => ({ id: i + 1, ...n, show: 1, created_at: ts(n.created), updated_at: ts(n.created) }))
notices.forEach(n => delete n.created)

const studentDocs = [
  { id: 1, userId: 5, title: 'passport_copy.pdf', url: 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf', created_at: ts('2026-08-12') },
  { id: 2, userId: 5, title: 'admission_letter.pdf', url: 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf', created_at: ts('2026-08-14') },
].map(d => ({ ...d, created_by: null, updated_by: null, deleted_by: null, updated_at: d.created_at, deleted_at: null }))

export function createSeed() {
  return structuredClone({ batches, users, passwords, fees, feeStudents, notices, studentDocs })
}
