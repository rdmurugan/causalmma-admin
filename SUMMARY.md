# CausalMMA Admin Panel - Summary

**Standalone PHP admin dashboard for CausalMMA API business operations.**

---

## 📊 Project Stats

- **Total Lines:** ~2,150 lines
- **Files:** 16 files
- **Language:** PHP 7.4+
- **Dependencies:** None (pure PHP)
- **Cost:** $0 (free hosting)
- **Version:** 1.0.0

---

## 🎯 What This Is

A complete admin panel for managing your CausalMMA API:

1. **Dashboard** - Real-time stats and metrics
2. **Organizations** - Client management
3. **API Keys** - Key lifecycle management
4. **Users** - User administration
5. **Trial Keys** - **Bulk trial key generation** (your priority!)
6. **Analytics** - Usage insights and trends

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────┐
│         Free PHP Hosting                 │
│       (InfinityFree, 000webhost)        │
│                                          │
│    causalmma-admin (THIS REPO)          │
│    ├── login.php                        │
│    ├── index.php (dashboard)            │
│    ├── trial_keys.php ⭐                │
│    └── ... other pages                  │
│                                          │
└──────────────┬──────────────────────────┘
               │ REST API calls
               │ X-Admin-Key header
               ▼
┌─────────────────────────────────────────┐
│         Render ($7/month)                │
│                                          │
│    causalmma API (separate repo)        │
│    ├── admin_endpoints.py               │
│    ├── key_management_endpoints.py      │
│    └── ... other endpoints              │
│                                          │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│    Neon PostgreSQL (Free)               │
│    └── Organizations, Users, Keys       │
└─────────────────────────────────────────┘
```

---

## 📁 Repository Structure

```
causalmma-admin/                 # THIS REPO (separate from API)
├── README.md                    # Main documentation
├── DEPLOY.md                    # Step-by-step deployment
├── CHANGELOG.md                 # Version history
├── LICENSE                      # MIT License
├── .gitignore                  # Config files excluded
├── .htaccess                   # Security rules
│
├── config.example.php          # Template (copy to config.php)
├── config.php                  # Your config (NOT in git)
│
├── login.php                   # Login page
├── logout.php                  # Logout handler
├── header.php                  # Layout header
├── footer.php                  # Layout footer
│
├── index.php                   # Dashboard
├── organizations.php           # Org management
├── api_keys.php               # Key management
├── users.php                  # User management
├── trial_keys.php             # Trial key generator ⭐
└── analytics.php              # Usage analytics
```

---

## 🚀 Quick Start

### 1. Clone/Download

```bash
# If you haven't already
cd /path/to/projects/
# This directory should already exist from setup
```

### 2. Configure

```bash
cd causalmma-admin

# Copy example config
cp config.example.php config.php

# Edit with your settings
nano config.php
```

### 3. Deploy

**Upload to free hosting:**
- InfinityFree (recommended)
- 000webhost
- Any PHP hosting

See `DEPLOY.md` for detailed instructions.

### 4. Access

```
https://your-admin-site.infinityfreeapp.com
Username: admin (or custom)
Password: (from config.php)
```

---

## 🎯 Main Features Detail

### Trial Key Generation (Primary Feature)

**Bulk create trial keys:**
1. Navigate to "Trial Keys" page
2. Set parameters:
   - Count: 1-100 keys
   - Duration: 7-90 days
   - Limits: Custom or default
3. Click "Generate"
4. Download CSV
5. Distribute to customers

**CSV Output:**
```csv
Organization,API Key,Key Prefix,Expires
Trial 1,ca_live_xxx...,ca_live_xxx,2025-01-20T00:00:00
Trial 2,ca_live_yyy...,ca_live_yyy,2025-01-20T00:00:00
```

**Monitor Active Trials:**
- See all active trials
- Days remaining
- Usage statistics
- Extend with one click (+30 days)

### Organization Management

- **View:** All organizations with filters
- **Search:** By name, tier, status
- **Suspend/Activate:** One-click actions
- **Extend Trials:** Add 30 days instantly
- **Usage:** See request counts, API keys, users

### API Key Management

- **List:** All API keys across all orgs
- **Filter:** By environment, status, org
- **Search:** By key prefix or org name
- **Revoke:** Instant key revocation
- **Monitor:** Last used, request counts

### Analytics

- **Date Range:** Custom periods
- **Metrics:** Requests, tokens, response time, errors
- **Top Endpoints:** Visual bar charts
- **Top Organizations:** Usage leaders
- **Timeline:** Daily request trend

---

## 🔒 Security Features

1. **Login Protection:**
   - Password-based authentication
   - Session management
   - Auto-logout on inactivity

2. **API Security:**
   - Admin API key authentication
   - HTTPS enforcement
   - Secure headers

3. **File Protection:**
   - config.php blocked via .htaccess
   - No directory listing
   - Gitignored sensitive files

4. **Optional Hardening:**
   - IP whitelisting
   - Custom admin URL
   - Password rotation reminders

---

## 💰 Cost Breakdown

| Component | Provider | Cost |
|-----------|----------|------|
| Admin Panel | InfinityFree | **$0/month** |
| API Backend | Render | $7/month |
| Database | Neon | $0 (free tier) |
| Redis | Upstash | $0 (free tier) |
| **TOTAL** | | **$7/month** |

---

## 📚 Documentation

- **README.md** - Main documentation (this file is SUMMARY.md)
- **DEPLOY.md** - Deployment guide
- **CHANGELOG.md** - Version history
- **config.example.php** - Configuration template

**In main causalmma repo:**
- `docs/ADMIN_PANEL_DEPLOYMENT.md` - Complete deployment
- `docs/ADMIN_REPO_SETUP.md` - Repo structure
- `QUICKSTART_ADMIN.md` - 15-minute setup

---

## 🔗 Related Repositories

- **Main API:** `causalmma/` (Python/FastAPI)
- **Admin Panel:** `causalmma-admin/` (This repo - PHP)

---

## 🎓 Learning Resources

**If you're new to PHP admin panels:**
1. Start with `DEPLOY.md`
2. Test locally: `php -S localhost:8080`
3. Review `config.example.php`
4. Read code comments in each PHP file

**If you want to customize:**
- Edit `header.php` for UI changes
- Edit individual pages for features
- All pages use same `apiRequest()` function

---

## ✨ Key Advantages

1. **Free Hosting:** $0 monthly cost
2. **Standalone:** Independent from API
3. **Easy Updates:** Just upload changed files
4. **No Dependencies:** Pure PHP, no frameworks
5. **Trial Focus:** Built for trial key management
6. **Professional UI:** Clean, responsive design

---

## 🔄 Update Process

**When API adds new endpoints:**
1. Main repo: Deploy to Render
2. Admin panel: No changes needed (auto-uses new endpoints)

**When admin UI needs updates:**
1. Edit PHP files in this repo
2. Upload changed files to hosting
3. Done!

---

## 📞 Support

- **Documentation:** See README.md and DEPLOY.md
- **Issues:** Create issue in main causalmma repo
- **Email:** durai@infinidatum.net

---

## ✅ Pre-Deployment Checklist

- [ ] Cloned/downloaded this repo
- [ ] Generated admin API key (from main repo)
- [ ] Configured `config.php`
- [ ] Changed admin password
- [ ] Set Render environment variable
- [ ] Signed up for free PHP hosting
- [ ] Uploaded files to hosting
- [ ] Tested login
- [ ] Created trial keys successfully

---

**Next Step:** See `DEPLOY.md` for deployment instructions.

---

**Repository:** causalmma-admin
**Version:** 1.0.0
**License:** MIT
**Author:** Durai Rajamanickam / InfiniDatum
**Date:** 2025-01-06
