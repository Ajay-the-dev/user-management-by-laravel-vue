# Demo build (Netlify)

A static build of the Vue front end that runs without the Laravel backend.
Every API call is answered in the browser by a mock API with sample data,
so the site can be hosted on Netlify (or any static host) for demos.

## Logins

| Role    | Username  | Password  |
|---------|-----------|-----------|
| Admin   | `admin`   | `admin`   |
| Student | `student` | `student` |

Every other seeded user's password is their username.

## Build and run locally

Requires Node 22.

```bash
npm install
npm run build:demo     # outputs dist-demo/
npm run preview:demo   # serves dist-demo/ at http://localhost:4173
```

For live reloading while you work on it:

```bash
npm run dev:demo
```

## Deploy to Netlify

### Option A: drag and drop

1. Run `npm run build:demo`.
2. Open https://app.netlify.com/drop (or your site's **Deploys** tab).
3. Drag the `dist-demo/` folder onto the page.

To update the site, rebuild and drag `dist-demo/` onto the same site's **Deploys** tab.

### Option B: from Git

1. Push the repo to GitHub, GitLab or Bitbucket.
2. In Netlify, choose **Add new site → Import an existing project** and pick the repo.
3. Keep the defaults. `netlify.toml` already sets:
   - build command: `npm run build:demo`
   - publish directory: `dist-demo`
   - Node version: 22

Every push to the connected branch redeploys the site.

If the Git build fails while installing PHP dependencies (Netlify sees `composer.json`), use option A.

### Netlify CLI (alternative to A)

```bash
npm run build:demo
npx netlify-cli deploy --prod --dir=dist-demo
```

## How it works

| File | Purpose |
|------|---------|
| `vite.demo.config.js` | Standalone Vite config (no Laravel plugin). Builds into `dist-demo/`, swaps the Echo client for a stub, copies `image.webp` and `favicon.ico`, and writes `_redirects`. |
| `demo/index.html` | Page shell and the demo banner (logins and the **Reset data** button). |
| `demo/main.js` | Entry point. Installs the mock API first, then boots `resources/js/app.js` unchanged. |
| `demo/mock/api.js` | Mock of every `routes/api.php` route, installed as the axios adapter. Also fakes Cloudinary uploads. |
| `demo/mock/seed.js` | Sample batches, staff, students, fees, payments, notices and documents. |
| `demo/echo-stub.js` | In-memory stand-in for Laravel Echo/Reverb, so publishing a notice still shows a live toast. |
| `demo/.env.demo` | Env vars for the demo build. The real `.env` (with Cloudinary and Reverb keys) is not used. |

### Data

- Data is stored in each visitor's browser (`localStorage`), so changes survive a reload but are not shared between visitors.
- **Reset data** in the banner restores the sample data and logs out.
- Uploaded files under 400 KB are kept as data URLs; larger ones only last until the tab is closed.

### Page refresh on Netlify

The build writes `dist-demo/_redirects` (`/*  /index.html  200`) so that refreshing a page such as `/home/fee` loads the app instead of Netlify's "Page not found". `netlify.toml` has the same rule for Git deploys.

## Keeping the demo in sync

- **New or changed API endpoint:** add or update its handler in `demo/mock/api.js`, returning the same JSON shape as the Laravel controller.
- **New `VITE_*` variable:** add a demo-safe value to `demo/.env.demo`.
- **New file from `public/` used by the app:** add it to `publicAssets` in `vite.demo.config.js`.
