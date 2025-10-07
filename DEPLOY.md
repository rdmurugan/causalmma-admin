# Deployment Instructions

Quick deployment guide for CausalMMA Admin Panel.

## 🎯 Before You Start

You need:
1. ✅ CausalMMA API deployed on Render
2. ✅ Admin API key generated (from main repo)
3. ✅ Free PHP hosting account

## 📝 Step-by-Step Deployment

### 1. Get Admin API Key

In the **main causalmma repository**:

```bash
cd /path/to/causalmma
python scripts/generate_admin_key.py
```

**Save two values:**
- `Admin API Key`: `admin_xxxxxxxxx...` → Use in Step 3
- `Admin API Key Hash`: `abc123...` → Use in Step 2

### 2. Configure Render API

1. Go to https://render.com/dashboard
2. Click your API service (e.g., `causalmma-api`)
3. Click "Environment" tab
4. Add environment variable:
   - **Name:** `ADMIN_API_KEY_HASH`
   - **Value:** (paste hash from Step 1)
5. Click "Save Changes"
6. Wait for redeploy (1-2 minutes)

### 3. Configure This Admin Panel

```bash
cd /path/to/causalmma-admin

# Copy example config
cp config.example.php config.php

# Edit config.php
nano config.php  # or use your preferred editor
```

**Update these values:**

```php
// 1. Your Render API URL
define('API_BASE_URL', 'https://YOUR-APP.onrender.com');

// 2. Admin API Key from Step 1
define('ADMIN_API_KEY', 'admin_YOUR_KEY_FROM_STEP_1');

// 3. Change admin password!
define('ADMIN_PASSWORD_HASH', password_hash('YourSecurePassword', PASSWORD_BCRYPT));
```

**Generate password hash:**
```bash
php -r "echo password_hash('YourSecurePassword', PASSWORD_BCRYPT);"
```

### 4. Deploy to Free Hosting

#### Option A: InfinityFree (Recommended)

**Signup:**
1. Go to https://infinityfree.net
2. Click "Sign Up" → "Create Account"
3. Choose subdomain: `your-name-admin.infinityfreeapp.com`
4. Complete registration
5. Wait for activation (instant)

**Upload Files:**
1. Login to InfinityFree Control Panel
2. Click "Online File Manager" or "File Manager"
3. Navigate to `htdocs/` folder
4. **Delete default files** (index.html, etc.)
5. Click "Upload" button
6. Select all files from `causalmma-admin/`:
   - `config.php` (your configured version!)
   - All `.php` files
   - `.htaccess`
7. Wait for upload to complete

**Access:**
- URL: `https://your-name-admin.infinityfreeapp.com`
- Username: (from config.php)
- Password: (from config.php)

#### Option B: 000webhost

**Signup:**
1. Go to https://www.000webhost.com
2. Click "Free Sign Up"
3. Create account
4. Create website

**Upload Files:**
1. Go to Dashboard → File Manager
2. Navigate to `public_html/`
3. Upload all files from `causalmma-admin/`

**Enable SSL:**
1. Dashboard → Settings → General
2. Force HTTPS: ON

#### Option C: FTP Upload (Any Host)

```bash
# Install FileZilla or use command line FTP
ftp ftp.yourhost.com

# Login with credentials from hosting provider
# Upload all files to htdocs/ or public_html/
```

### 5. Test Deployment

**Test Login:**
1. Open your admin panel URL
2. Enter username and password
3. Should see dashboard

**Test API Connection:**
1. Dashboard should show statistics
2. If you see errors:
   - Check `API_BASE_URL` in config.php
   - Verify Render service is running
   - Check `ADMIN_API_KEY_HASH` in Render

**Test Trial Keys:**
1. Click "Trial Keys" in sidebar
2. Create 5 trial keys
3. Download CSV
4. Verify keys are created

## ✅ Deployment Checklist

- [ ] Generated admin API key
- [ ] Set `ADMIN_API_KEY_HASH` in Render
- [ ] Configured `config.php`
- [ ] Changed admin password
- [ ] Uploaded files to hosting
- [ ] Accessed admin panel URL
- [ ] Successfully logged in
- [ ] Dashboard shows data
- [ ] Trial key creation works
- [ ] CSV download works

## 🔒 Post-Deployment Security

**Immediately after deployment:**

1. **Change default credentials** in `config.php`
2. **Enable HTTPS** (usually automatic on free hosts)
3. **Test all features** to ensure they work
4. **Bookmark** your admin URL securely

**Optional hardening:**

```apache
# Add to .htaccess for IP whitelisting
Order Deny,Allow
Deny from all
Allow from YOUR_IP_ADDRESS
```

## 🔄 Future Updates

**To update admin panel:**

```bash
cd /path/to/causalmma-admin
git pull origin main

# Re-upload changed files to hosting
# Don't overwrite config.php
```

**To update API:**
```bash
cd /path/to/causalmma
git pull origin main
# Render auto-deploys if GitHub connected
```

## 🐛 Common Issues

### "Cannot connect to API"

```bash
# Test API directly
curl https://YOUR-APP.onrender.com/health

# Should return: {"status":"healthy",...}
```

**Fix:** Update `API_BASE_URL` in config.php

### "Invalid admin API key"

**Fix:** Regenerate admin key:
```bash
cd /path/to/causalmma
python scripts/generate_admin_key.py
```

Then update both:
- Render: `ADMIN_API_KEY_HASH`
- config.php: `ADMIN_API_KEY`

### Login fails

**Fix:** Regenerate password hash:
```bash
php -r "echo password_hash('NewPassword', PASSWORD_BCRYPT);"
```

Update `ADMIN_PASSWORD_HASH` in config.php

## 📞 Need Help?

- Check main README.md
- Create issue in main repo
- Email: durai@infinidatum.net

---

**Estimated Time:** 15 minutes
**Cost:** $0 for admin panel + $7/month for API
