# ✦ Lexio — AI Email Drafting Assistant

> Draft professional, casual, or persuasive emails in seconds — powered by Google Gemini AI.

**Live Demo:** [lexio-production-ea3c.up.railway.app](https://lexio-production-ea3c.up.railway.app)

---

## ✨ Features

- 🔐 **User Authentication** — Secure register, login & logout with session management
- 🤖 **AI Email Generation** — Powered by Google Gemini Flash API
- 🎨 **Tone Selector** — Professional, Casual, or Persuasive
- 📏 **Length Selector** — Short, Medium, or Long
- 👤 **Sender / Recipient** — Personalise every email
- 📋 **One-click Copy** — Copy subject, body, or the full email
- ✏️ **Inline Editing** — Edit the AI draft before you send
- 📜 **Draft History** — View and reload your last 30 generated emails
- 📱 **Fully Responsive** — Works on mobile, tablet, and desktop

---

## 🖥️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2 |
| Database | MySQL (PDO, prepared statements) |
| AI | Google Gemini Flash API |
| Frontend | HTML5, Vanilla JS, Bootstrap 5.3 |
| Icons | Bootstrap Icons 1.11 |
| Font | Inter (Google Fonts) |
| Hosting | Railway (Docker / Apache) |

---

## 🗂️ Project Structure

```
lexio/
├── index.php              ← Login / Register page
├── dashboard.php          ← Main app (auth-protected)
├── config.php             ← DB + Gemini config (reads .env / Railway vars)
├── setup_db.php           ← Token-gated one-time DB initialiser
├── health.php             ← Railway healthcheck endpoint (returns 200 OK)
├── .htaccess              ← Security headers + HTTPS redirect
├── Dockerfile             ← PHP 8.2 Apache image
├── railway.json           ← Railway deploy config
├── .env.example           ← Environment variable reference (safe to commit)
├── api/
│   ├── auth.php           ← Login / Register / Logout JSON API
│   ├── generate_email.php ← Gemini AI email generation API
│   └── fetch_history.php  ← Email draft history API
├── css/
│   └── style.css          ← Custom styles (CSS variables, Inter font)
└── js/
    └── app.js             ← All frontend logic
```

---

## 🚀 Deploy on Railway

### Prerequisites
- A [Railway](https://railway.app) account
- A [Google Gemini API key](https://aistudio.google.com/app/apikey) (free)
- This repo pushed to GitHub

### Step 1 — Create Railway Project
1. Go to [railway.app](https://railway.app) → **New Project**
2. **Deploy from GitHub repo** → select `skillnest05/Lexio`
3. Railway will auto-detect the `Dockerfile` and start building

### Step 2 — Add MySQL Database
1. Inside your Railway project → click **"+ New"**
2. Select **"Database"** → **"Add MySQL"**

### Step 3 — Link MySQL Variables to Lexio Service
Go to **Lexio service** → **Variables** tab → add these reference variables:

| Variable | Value |
|---|---|
| `MYSQLHOST` | `${{MySQL.MYSQLHOST}}` |
| `MYSQLPORT` | `${{MySQL.MYSQLPORT}}` |
| `MYSQLUSER` | `${{MySQL.MYSQLUSER}}` |
| `MYSQLPASSWORD` | `${{MySQL.MYSQLPASSWORD}}` |
| `MYSQLDATABASE` | `${{MySQL.MYSQLDATABASE}}` |

### Step 4 — Set App Variables
Still in the **Variables** tab, add:

| Variable | Value |
|---|---|
| `GEMINI_API_KEY` | Your Gemini API key |
| `APP_ENV` | `production` |
| `SETUP_TOKEN` | Any random secret string |

### Step 5 — Initialise the Database
After the deploy goes green, visit:
```
https://your-app.railway.app/setup_db.php?token=YOUR_SETUP_TOKEN
```
Expected response:
```json
{"success": true, "message": "Database and tables set up successfully. Remove SETUP_TOKEN..."}
```

### Step 6 — Lock setup_db.php
In Railway Variables, **delete `SETUP_TOKEN`**.
This permanently returns `403 Forbidden` to anyone who visits `setup_db.php`. 🔒

### Step 7 — Done!
Visit your Railway URL, register an account, and start generating emails. 🎉

---

## 💻 Local Development (XAMPP)

### 1. Clone & place in XAMPP
```bash
git clone https://github.com/skillnest05/Lexio.git
# Place inside xampp/htdocs/lexio
```

### 2. Configure environment
```bash
cp .env.example .env
```
Edit `.env` with your local values:
```env
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=lexio
GEMINI_API_KEY=your_gemini_api_key_here
APP_ENV=local
```

### 3. Start XAMPP
Start **Apache** and **MySQL** from the XAMPP Control Panel.

### 4. Initialise the database
Visit:
```
http://localhost/lexio/setup_db.php
```
> **Note:** `SETUP_TOKEN` is not required locally — the gate only activates when the env var is set.

### 5. Open the app
```
http://localhost/lexio/
```

---

## 🔑 Environment Variables Reference

| Variable | Required | Where | Description |
|---|---|---|---|
| `GEMINI_API_KEY` | ✅ Always | Manual | Google Gemini API key |
| `APP_ENV` | ✅ Always | Manual | `local` or `production` |
| `MYSQLHOST` | ✅ Production | Railway MySQL plugin | Auto-injected via reference variable |
| `MYSQLPORT` | ✅ Production | Railway MySQL plugin | Auto-injected via reference variable |
| `MYSQLUSER` | ✅ Production | Railway MySQL plugin | Auto-injected via reference variable |
| `MYSQLPASSWORD` | ✅ Production | Railway MySQL plugin | Auto-injected via reference variable |
| `MYSQLDATABASE` | ✅ Production | Railway MySQL plugin | Auto-injected via reference variable |
| `DB_HOST` | ✅ Local | `.env` | `localhost` for XAMPP |
| `DB_USER` | ✅ Local | `.env` | `root` for XAMPP default |
| `DB_PASSWORD` | ✅ Local | `.env` | Empty for XAMPP default |
| `DB_NAME` | ✅ Local | `.env` | `lexio` |
| `SETUP_TOKEN` | ⚠️ One-time | Manual | Gates `setup_db.php` — remove after use |

---

## 🗄️ Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| `id` | INT UNSIGNED | Primary Key, Auto Increment |
| `first_name` | VARCHAR(50) | |
| `last_name` | VARCHAR(50) | |
| `email` | VARCHAR(100) | UNIQUE |
| `password_hash` | VARCHAR(255) | bcrypt |
| `created_at` | TIMESTAMP | |

### `emails`
| Column | Type | Notes |
|---|---|---|
| `id` | INT UNSIGNED | Primary Key, Auto Increment |
| `user_id` | INT UNSIGNED | FK → users.id (CASCADE DELETE) |
| `sender_name` | VARCHAR(100) | |
| `recipient_name` | VARCHAR(100) | |
| `tone` | VARCHAR(50) | professional / casual / persuasive |
| `length` | VARCHAR(20) | short / medium / long |
| `prompt_text` | TEXT | User's description input |
| `generated_email` | LONGTEXT | AI-generated email body |
| `subject_line` | VARCHAR(255) | AI-generated subject line |
| `created_at` | TIMESTAMP | |

---

## 🛡️ Security

- **Passwords** hashed with `bcrypt` (`password_hash` / `password_verify`)
- **SQL Injection** prevented — 100% PDO prepared statements
- **Session cookies** — `HttpOnly`, `SameSite=Lax`, `Secure` flag in production
- **Error messages** — generic in production (no internal detail leaked)
- **`.env`** excluded from git via `.gitignore` — never committed
- **`setup_db.php`** token-gated — returns `403` without a valid `SETUP_TOKEN`
- **`.htaccess`** blocks direct access to `.env`, `Dockerfile`, `README.md`, etc.
- **Security headers** — `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`
- **HTTPS** — automatic HTTP → HTTPS redirect on Railway

---

## 📄 License

MIT — feel free to use, modify, and distribute.
