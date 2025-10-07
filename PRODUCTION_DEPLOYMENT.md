# Production Deployment Guide

**Complete guide for deploying CausalMMA Admin Panel to production.**

---

## 📋 Prerequisites

### 1. Production API (Already Running)
- ✅ API deployed at: **https://api.causalmma.com**
- ✅ Admin endpoints enabled
- ✅ Database connected (Neon PostgreSQL)

### 2. Admin API Key
You need the admin API key hash configured on Render. If you don't have it:

```bash
cd /path/to/causalmma
python scripts/generate_admin_key.py
```

**Output:**
- Admin API Key: `admin_xxxxx...` (for config.php)
- Admin API Key Hash: `xxxxx...` (for Render environment)

---

## 🚀 Step 1: Configure Render Environment

1. Go to https://render.com/dashboard
2. Select your `causalmma-api` service
3. Navigate to **Environment** tab
4. Add/verify this environment variable:

   ```
   Key: ADMIN_API_KEY_HASH
   Value: <your-admin-key-hash>
   ```

5. Click **Save Changes** (triggers redeploy)

---

## 🌐 Step 2: Deploy Admin Panel to Free Hosting

### Option A: InfinityFree (Recommended - Free)

#### 2.1 Sign Up
1. Go to https://infinityfree.net
2. Click **Create Account**
3. Choose a subdomain: `causalmma-admin.infinityfreeapp.com`
4. Complete registration
5. Wait for instant activation

#### 2.2 Prepare Files

```bash
cd /path/to/causalmma-admin

# Create production config
cp config.production.php config.php

# Edit config.php with your values:
# - ADMIN_API_KEY: Your admin API key from Step 1
# - ADMIN_USERNAME: Your admin username
# - ADMIN_PASSWORD_HASH: Generate with command below
```

**Generate Password Hash:**
```bash
php -r "echo password_hash('YOUR_SECURE_PASSWORD', PASSWORD_BCRYPT) . PHP_EOL;"
```

**Example config.php for production:**
```php
<?php
define('API_BASE_URL', 'https://api.causalmma.com');
define('ADMIN_API_KEY', 'admin_PpCV3HEpdKiXLuX6X4k3Oq25FM88n20ADddo1CT9CIg');
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', '$2y$12$...');  // Your generated hash
?>
```

#### 2.3 Upload Files

1. Login to InfinityFree Control Panel
2. Click **File Manager**
3. Navigate to `htdocs/`
4. Delete default files (index.html, default.php)
5. Upload all files from `causalmma-admin/`:
   - ✅ config.php (configured!)
   - ✅ All .php files (*.php)
   - ✅ .htaccess
   - ✅ assets/ folder (if exists)

#### 2.4 Set Permissions
```
Files: 644 (rw-r--r--)
Directories: 755 (rwxr-xr-x)
config.php: 600 (rw------) - IMPORTANT!
```

---

## 🔒 Step 3: Security Verification

### 3.1 Test HTTPS
```bash
curl https://your-admin-url.infinityfreeapp.com/login.php
```

Should return HTML (not redirect or error).

### 3.2 Verify Config Protection
```bash
curl https://your-admin-url.infinityfreeapp.com/config.php
```

Should return **403 Forbidden** (protected by .htaccess).

### 3.3 Test API Connection
```bash
curl https://your-admin-url.infinityfreeapp.com/test_api.php
```

(Create temporary test file if needed, then delete)

---

## ✅ Step 4: Verification Checklist

### 4.1 API Health
```bash
curl https://api.causalmma.com/health
```

**Expected:**
```json
{
  "status": "healthy",
  "version": "4.3.0",
  ...
}
```

### 4.2 Admin Dashboard
```bash
curl -H "X-Admin-Key: YOUR_KEY" https://api.causalmma.com/api/v1/admin/dashboard
```

**Expected:**
```json
{
  "total_organizations": 1,
  "total_users": 1,
  ...
}
```

### 4.3 Admin Panel Login

1. **Open in browser:** `https://your-admin-url.infinityfreeapp.com`
2. **Login** with your credentials
3. **Verify:**
   - ✅ Dashboard loads
   - ✅ Organizations page shows data
   - ✅ Trial Keys page accessible
   - ✅ All navigation works

### 4.4 Create Test Trial Keys

1. Go to **Trial Keys** page
2. Generate 5 trial keys (14 days)
3. Download CSV
4. Verify keys appear in "Active Trials"

---

## 📊 Step 5: Production Checklist

### Security
- [ ] HTTPS enabled on admin panel
- [ ] config.php has secure password hash (not admin123!)
- [ ] Admin username changed (not 'admin')
- [ ] Session cookies are HTTPS-only
- [ ] Error display disabled (error_reporting=0)
- [ ] config.php file permissions: 600
- [ ] .htaccess protecting config files

### Configuration
- [ ] API_BASE_URL = https://api.causalmma.com
- [ ] ADMIN_API_KEY configured correctly
- [ ] ADMIN_API_KEY_HASH set in Render
- [ ] Timezone set to UTC
- [ ] Session timeout configured (1 hour)

### Testing
- [ ] Can login to admin panel
- [ ] Dashboard displays statistics
- [ ] Organizations page loads
- [ ] Trial keys can be generated
- [ ] CSV download works
- [ ] API keys page functional
- [ ] Analytics page loads

### Documentation
- [ ] Admin credentials saved securely
- [ ] Admin API key backed up
- [ ] Production URLs documented
- [ ] Deployment date recorded

---

## 🐛 Troubleshooting

### Issue: "Cannot connect to API"

**Check:**
```bash
# 1. Verify API is running
curl https://api.causalmma.com/health

# 2. Test admin endpoint
curl -H "X-Admin-Key: YOUR_KEY" https://api.causalmma.com/api/v1/admin/dashboard

# 3. Check config.php
grep API_BASE_URL /path/to/config.php
```

**Fix:**
- Verify API_BASE_URL in config.php
- Check ADMIN_API_KEY matches Render hash
- Regenerate admin key if needed

### Issue: "Invalid admin API key"

**Fix:**
1. Regenerate admin key:
   ```bash
   python scripts/generate_admin_key.py
   ```
2. Update config.php with new key
3. Update ADMIN_API_KEY_HASH in Render
4. Redeploy Render service

### Issue: Login fails

**Check:**
```bash
# Test password hash
php -r "
\$hash = 'YOUR_HASH_FROM_CONFIG';
var_dump(password_verify('YOUR_PASSWORD', \$hash));
"
```

Should output: `bool(true)`

**Fix:**
1. Generate new hash:
   ```bash
   php -r "echo password_hash('NewPassword', PASSWORD_BCRYPT);"
   ```
2. Update ADMIN_PASSWORD_HASH in config.php
3. Re-upload config.php

### Issue: 403 Forbidden on all pages

**Check .htaccess:**
```apache
# Should NOT block .php files
# Should ONLY block config.php and sensitive files
```

**Fix:**
```apache
# Allow PHP files
<Files ~ "\.php$">
    Order allow,deny
    Allow from all
</Files>

# Block config
<Files "config.php">
    Order deny,allow
    Deny from all
</Files>
```

---

## 🔄 Maintenance

### Rotate Admin Key (Every 90 Days)

```bash
# 1. Generate new key
python scripts/generate_admin_key.py

# 2. Update config.php
vim config.php

# 3. Update Render environment
# Go to Render > Environment > ADMIN_API_KEY_HASH

# 4. Test admin panel login
```

### Update Password

```bash
# 1. Generate new hash
php -r "echo password_hash('NewPassword', PASSWORD_BCRYPT);"

# 2. Update config.php
vim config.php

# 3. Re-upload to hosting
```

### Monitor Logs

**On InfinityFree:**
- Go to Control Panel > Error Logs
- Check for PHP errors or warnings
- Review access logs for suspicious activity

**On Render:**
- Go to Logs tab
- Filter for "admin" endpoints
- Monitor for unauthorized access attempts

---

## 📞 Support

### Documentation
- Main API: https://github.com/rdmurugan/causalmma
- Admin Panel: https://github.com/rdmurugan/causalmma-admin
- Issues: Create issue on GitHub

### Quick Commands

```bash
# Test API health
curl https://api.causalmma.com/health

# Test admin dashboard
curl -H "X-Admin-Key: KEY" https://api.causalmma.com/api/v1/admin/dashboard

# Generate new password hash
php -r "echo password_hash('PASSWORD', PASSWORD_BCRYPT);"

# Generate new admin key
python scripts/generate_admin_key.py

# Check PHP errors
tail -f /tmp/causalmma_admin_errors.log
```

---

## 🎉 Production Ready!

Once all checklist items are complete:

1. ✅ API deployed and healthy at api.causalmma.com
2. ✅ Admin panel accessible at your-domain.infinityfreeapp.com
3. ✅ Login works with secure credentials
4. ✅ Trial keys can be created and downloaded
5. ✅ All security measures in place

**You're ready to start managing your CausalMMA API!**

---

**Deployment Date:** _____________
**Deployed By:** _____________
**API URL:** https://api.causalmma.com
**Admin URL:** _____________
