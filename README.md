# Lexio — AI Email Draft Helper

Lexio is an AI-powered email drafting tool built with PHP, MySQL, Bootstrap 5, and the Google Gemini API.

---

## Tech Stack
- **Backend**: PHP 8.2
- **Database**: MySQL
- **Frontend**: HTML, Bootstrap 5, Vanilla JS
- **AI**: Google Gemini API (`gemini-flash-latest`)

---

## Local Development (XAMPP)

1. Clone/copy the project into `C:\xampp\htdocs\lexio`
2. Copy `.env.example` to `.env` and fill in your values
3. Start Apache and MySQL in XAMPP
4. Visit `http://localhost/lexio/setup_db.php` once to create the database
5. Go to `http://localhost/lexio`

---

## Deploy to Railway

### Step 1 — Push to GitHub
```bash
cd C:\xampp\htdocs\lexio
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/YOUR_USERNAME/lexio.git
git push -u origin main
```

### Step 2 — Create Railway Project
1. Go to [railway.app](https://railway.app) → **New Project**
2. Choose **Deploy from GitHub repo** → select your `lexio` repo
3. Railway will auto-detect PHP via Nixpacks

### Step 3 — Add MySQL Database
1. In your Railway project dashboard, click **+ New** → **Database** → **MySQL**
2. Railway will provision a MySQL instance automatically

### Step 4 — Set Environment Variables
In Railway → your PHP service → **Variables** tab, add:

| Variable | Value |
|---|---|
| `DB_HOST` | (from Railway MySQL → `MYSQLHOST`) |
| `DB_USER` | (from Railway MySQL → `MYSQLUSER`) |
| `DB_PASSWORD` | (from Railway MySQL → `MYSQLPASSWORD`) |
| `DB_NAME` | `lexio` |
| `GEMINI_API_KEY` | your Gemini API key |

> **Tip:** Railway auto-exposes MySQL variables. You can reference them directly as `${{MySQL.MYSQLHOST}}` etc.

### Step 5 — Initialize the Database
After deployment, visit:
```
https://your-railway-domain.up.railway.app/setup_db.php
```
You should see: `{"success":true,"message":"Database and tables set up successfully..."}`

> ⚠️ **Delete or rename `setup_db.php`** after running it once for security.

### Step 6 — Done!
Visit your Railway app URL and start drafting emails.

---

## Deployment Platform Comparison

| Feature | Railway ✅ | Render ✅ | Vercel ❌ |
|---|---|---|---|
| PHP Support | Native (Nixpacks) | Via Docker | ❌ Not supported |
| MySQL | Built-in plugin | Managed DB (paid) | ❌ Not supported |
| Free Tier | $5 credit/month | 750 hrs/month | N/A for PHP |
| Setup Difficulty | Easy | Medium (needs Docker) | Not recommended |
| **Best For Lexio** | ✅ Yes | ✅ Yes (with Dockerfile) | ❌ No |

---

## Project Structure

```
lexio/
├── api/
│   ├── auth.php            # Login / Register / Logout
│   ├── generate_email.php  # Gemini API integration
│   └── fetch_history.php   # Load past drafts
├── css/
│   └── style.css
├── js/
│   └── app.js
├── config.php              # DB connection + env loader
├── index.php               # Sign In / Sign Up page
├── dashboard.php           # Main email drafting UI
├── setup_db.php            # Run once to create tables
├── .env                    # ← NOT committed (gitignored)
├── .env.example            # ← Committed (safe template)
├── .gitignore
├── Dockerfile              # For Docker-based deployments
├── docker-entrypoint.sh    # Handles Railway's dynamic PORT
├── nixpacks.toml           # Railway Nixpacks config
└── railway.json            # Railway project config
```
