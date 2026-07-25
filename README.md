# ✦ Lexio — AI Email Drafting Assistant

> Draft professional, casual, or persuasive emails in seconds using Google Gemini AI.

[![Deploy on Railway](https://railway.app/button.svg)](https://railway.app/new/template)

---

## 🚀 Deploy on Railway

### Prerequisites
- A [Railway](https://railway.app) account
- A [Google Gemini API key](https://aistudio.google.com/app/apikey) (free)
- This repo pushed to GitHub

### Step-by-step

1. **Create a new Railway project** → "Deploy from GitHub repo" → select this repo

2. **Add MySQL plugin**
   - In your Railway project → "+ New" → "Database" → "MySQL"
   - Railway auto-injects `MYSQLHOST`, `MYSQLPORT`, `MYSQLUSER`, `MYSQLPASSWORD`, `MYSQLDATABASE`

3. **Set environment variables** in Railway → your service → "Variables":

   | Variable | Value |
   |---|---|
   | `GEMINI_API_KEY` | Your Gemini API key |
   | `APP_ENV` | `production` |
   | `SETUP_TOKEN` | Any random secret (e.g. `openssl rand -hex 16`) |

4. **Deploy** — Railway builds the Docker image automatically

5. **Initialise the database** — visit:
   ```
   https://your-app.railway.app/setup_db.php?token=YOUR_SETUP_TOKEN
   ```
   You should see: `{"success":true,"message":"Database and tables set up successfully..."}`

6. **Remove `SETUP_TOKEN`** from Railway Variables — this locks `setup_db.php` permanently

7. **Done!** Visit your Railway URL and register your first account.

---

## 💻 Local Development (XAMPP)

1. Clone the repo into `xampp/htdocs/lexio`
2. Copy `.env.example` → `.env` and fill in your values
3. Start Apache + MySQL in XAMPP
4. Visit `http://localhost/lexio/setup_db.php` to create tables
5. Visit `http://localhost/lexio/`

---

## 🗂️ Project Structure

```
lexio/
├── index.php              ← Login / Register
├── dashboard.php          ← Main app (auth-protected)
├── config.php             ← DB + Gemini config
├── setup_db.php           ← Token-gated DB initialiser
├── .htaccess              ← Security headers + HTTPS redirect
├── Dockerfile             ← PHP 8.2 Apache image for Railway
├── railway.json           ← Railway deploy config
├── .env.example           ← Environment variable reference
├── api/
│   ├── auth.php           ← Login / Register / Logout API
│   ├── generate_email.php ← Gemini AI email generation
│   └── fetch_history.php  ← Email draft history
├── css/style.css          ← Custom styles
└── js/app.js              ← Frontend logic
```

---

## 🔑 Environment Variables

| Variable | Required | Description |
|---|---|---|
| `GEMINI_API_KEY` | ✅ | Google Gemini API key |
| `APP_ENV` | ✅ | `local` or `production` |
| `MYSQLHOST` | ✅ (Railway) | Auto-injected by MySQL plugin |
| `MYSQLPORT` | ✅ (Railway) | Auto-injected by MySQL plugin |
| `MYSQLUSER` | ✅ (Railway) | Auto-injected by MySQL plugin |
| `MYSQLPASSWORD` | ✅ (Railway) | Auto-injected by MySQL plugin |
| `MYSQLDATABASE` | ✅ (Railway) | Auto-injected by MySQL plugin |
| `DB_HOST` | ✅ (local) | `localhost` for XAMPP |
| `DB_USER` | ✅ (local) | `root` for XAMPP |
| `DB_PASSWORD` | ✅ (local) | Empty for XAMPP default |
| `DB_NAME` | ✅ (local) | `lexio` |
| `SETUP_TOKEN` | ⚠️ One-time | Token to gate `setup_db.php` |

---

## 🛡️ Security

- Session cookies: `HttpOnly`, `SameSite=Lax`, `Secure` (production only)
- Passwords hashed with `bcrypt` (`password_hash` / `password_verify`)
- All SQL via PDO prepared statements — no SQL injection possible
- `.env` excluded from git, never committed
- `setup_db.php` token-gated — returns 403 without valid token
- `.htaccess` blocks direct access to `.env`, `Dockerfile`, `README.md`, etc.
- Automatic HTTP → HTTPS redirect on Railway

---

## ⚙️ Tech Stack

| Layer | Tech |
|---|---|
| Backend | PHP 8.2 |
| Database | MySQL (PDO) |
| AI | Google Gemini Flash |
| Frontend | HTML5, Vanilla JS, Bootstrap 5.3 |
| Hosting | Railway (Docker) |
