# CausalMMA Admin Panel

**PHP-based admin dashboard for managing CausalMMA API operations.**

Standalone admin panel for business operations: manage organizations, API keys, users, and generate trial keys in bulk.

---

## 🎯 Features

- ✅ **Dashboard** - Real-time statistics and metrics
- ✅ **Organizations** - Manage clients, suspend/activate, extend trials
- ✅ **API Keys** - View, search, revoke keys
- ✅ **Users** - User management and search
- ✅ **Trial Keys** - **Bulk generate trial keys** (1-100 at once)
- ✅ **Analytics** - Usage statistics, top endpoints, timeline
- ✅ **Secure** - Password protected, API key authenticated
- ✅ **Free Hosting** - Works on InfinityFree, 000webhost, etc.

---

## 📋 Prerequisites

1. **CausalMMA API** deployed on Render
   - Repository: https://github.com/yourusername/causalmma
   - Must have admin endpoints enabled

2. **Admin API Key**
   - Generate from main repo: `python scripts/generate_admin_key.py`

3. **Free PHP Hosting** (recommended)
   - [InfinityFree](https://infinityfree.net) - Free, no ads
   - [000webhost](https://www.000webhost.com) - Free with ads
   - [AwardSpace](https://www.awardspace.com) - Free tier

---

## 🚀 Quick Deployment (5 minutes)

### Step 1: Configure

```bash
# Copy example config
cp config.example.php config.php

# Edit config.php:
# 1. Set API_BASE_URL to your Render API URL
# 2. Set ADMIN_API_KEY (from generate_admin_key.py)
# 3. Change ADMIN_PASSWORD_HASH
```

Generate password hash:
```bash
php -r "echo password_hash('YourSecurePassword123', PASSWORD_BCRYPT);"
```

### Step 2: Deploy to Hosting

**Option A: InfinityFree (Recommended)**

1. Sign up: https://infinityfree.net
2. Create account with subdomain (e.g., `causalmma-admin.infinityfreeapp.com`)
3. Go to Control Panel → File Manager
4. Navigate to `htdocs/`
5. Upload all files from this directory
6. Access: `https://your-subdomain.infinityfreeapp.com`

**Option B: FTP Upload**

```bash
# Install FTP client (FileZilla)
# Or use command line:
ftp ftp.yourdomain.com
# Upload all files to htdocs/
```

**Option C: Local Testing**

```bash
php -S localhost:8080
# Open: http://localhost:8080
```

### Step 3: Login & Test

1. Open your admin panel URL
2. Login with credentials from `config.php`
3. Test trial key generation

---

## 📁 File Structure

```
causalmma-admin/
├── config.example.php      # Example configuration
├── config.php             # Your config (gitignored)
├── .htaccess              # Apache security rules
├── login.php              # Login page
├── logout.php             # Logout handler
├── index.php              # Dashboard
├── header.php             # Layout header
├── footer.php             # Layout footer
├── organizations.php      # Organization management
├── api_keys.php          # API key management
├── users.php             # User management
├── trial_keys.php        # Trial key generator ⭐
├── analytics.php         # Usage analytics
└── README.md             # This file
```

---

## 🔧 Configuration

### Required Settings

Edit `config.php`:

```php
// API endpoint (your Render URL)
define('API_BASE_URL', 'https://causalmma-api.onrender.com');

// Admin API key (from main repo generator)
define('ADMIN_API_KEY', 'admin_xxxxxxxxx...');

// Admin password hash
define('ADMIN_PASSWORD_HASH', password_hash('YourPassword', PASSWORD_BCRYPT));
```

### Optional Settings

```php
// Change admin username
define('ADMIN_USERNAME', 'your_username');

// Timezone
date_default_timezone_set('America/New_York');

// Error reporting (disable in production)
error_reporting(0);
ini_set('display_errors', 0);
```

---

## 🎫 Trial Key Management

### Generate Bulk Trial Keys

1. Navigate to **Trial Keys** page
2. Fill in form:
   - **Number of keys:** 1-100
   - **Trial duration:** 7-90 days
   - **Organization prefix:** "Trial" or custom
   - **Monthly limit:** 3000 (or custom)
   - **Rate limit:** 5 req/min (or custom)
3. Click **"Generate Trial Keys"**
4. **Download CSV** with all keys
5. Distribute to customers/testers

### Monitor Active Trials

- View all active trials
- See days remaining
- Check usage statistics
- Extend trials with one click

---

## 🔒 Security

### Essential Security Steps

**1. Change Default Password**
```bash
php -r "echo password_hash('YOUR_STRONG_PASSWORD', PASSWORD_BCRYPT);"
# Update config.php with new hash
```

**2. Change Admin Username**
```php
// In config.php
define('ADMIN_USERNAME', 'your_unique_username');
```

**3. Protect config.php**

Already configured in `.htaccess`:
```apache
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>
```

**4. Enable HTTPS**

Most free hosts provide free SSL:
- InfinityFree: Auto-enabled
- 000webhost: Settings → SSL → Enable

**5. IP Whitelisting (Optional)**

Add to `.htaccess`:
```apache
Order Deny,Allow
Deny from all
Allow from YOUR_IP_ADDRESS
```

**6. Rotate Admin API Key**

Every 90 days:
```bash
# In main repo
python scripts/generate_admin_key.py

# Update Render env var: ADMIN_API_KEY_HASH
# Update config.php: ADMIN_API_KEY
```

---

## 🐛 Troubleshooting

### Issue: "Cannot connect to API"

**Check:**
1. `API_BASE_URL` in `config.php` is correct
2. Render service is running
3. Test API: `curl https://your-api.onrender.com/health`

**Fix:**
```php
define('API_BASE_URL', 'https://correct-url.onrender.com');
```

### Issue: "Invalid admin API key"

**Check:**
1. `ADMIN_API_KEY` in `config.php` matches key from generator
2. `ADMIN_API_KEY_HASH` is set in Render environment variables
3. Both use the same key/hash pair

**Fix:**
```bash
# Regenerate in main repo
python scripts/generate_admin_key.py

# Update both locations
```

### Issue: Login fails

**Fix:**
```bash
# Generate new password hash
php -r "echo password_hash('NewPassword123', PASSWORD_BCRYPT);"

# Update config.php
define('ADMIN_PASSWORD_HASH', 'paste_new_hash_here');
```

### Issue: PHP errors

**Check:**
- PHP version 7.4+ required
- cURL extension enabled
- Check hosting error logs

**Fix in config.php:**
```php
// Enable for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Disable in production
error_reporting(0);
ini_set('display_errors', 0);
```

---

## 🔄 Updating

When admin API endpoints are updated:

```bash
# 1. Pull latest from main repo
cd /path/to/causalmma
git pull origin main

# 2. Render auto-deploys (if GitHub connected)

# 3. No changes needed in PHP panel
#    (it uses REST API, automatically gets updates)
```

To update PHP panel itself:
```bash
cd /path/to/causalmma-admin
git pull origin main

# Re-upload changed files to hosting
# Keep your config.php (don't overwrite)
```

---

## 💰 Cost

- **Admin Panel Hosting:** **FREE** (InfinityFree)
- **API (Render):** $7/month
- **Database (Neon):** Free tier
- **Redis (Upstash):** Free tier

**Total:** ~$7/month for complete infrastructure

---

## 🔗 Links

- **Main API Repository:** https://github.com/yourusername/causalmma
- **API Documentation:** https://api.causalmma.com/docs

---

## 📞 Support

- **Issues:** Create issue in main repo
- **Email:** durai@infinidatum.net

---

## ✅ Deployment Checklist

Before going live:

- [ ] `config.php` created from `config.example.php`
- [ ] `API_BASE_URL` updated with Render URL
- [ ] `ADMIN_API_KEY` set (from generator)
- [ ] Admin password changed from default
- [ ] `ADMIN_API_KEY_HASH` set in Render environment
- [ ] Files uploaded to hosting
- [ ] HTTPS enabled on hosting
- [ ] Login tested successfully
- [ ] Dashboard loads with correct data
- [ ] Trial key creation tested
- [ ] CSV download works
- [ ] Organization management tested

---

**Version:** 1.0.0
**Last Updated:** 2025-01-06
