# Local Testing Guide

Test the admin panel locally before deploying.

---

## ✅ Configuration Complete

**Admin Panel is configured for local testing:**

- **API URL:** `http://localhost:8000` (FastAPI)
- **Admin Panel:** `http://localhost:8080` (PHP)
- **Username:** `admin`
- **Password:** `admin123`

**Admin API Key:**
- Key: `admin_PpCV3HEpdKiXLuX6X4k3Oq25FM88n20ADddo1CT9CIg`
- Hash: `52bbd5ed79f9f61e71bdd66b7ea5e151aab43ca8842b83431525cb2190bd5aa1`

---

## 🚀 Start Services

### Terminal 1: Start FastAPI Backend

```bash
cd /path/to/causalmma

# Set admin key hash for testing
export ADMIN_API_KEY_HASH="52bbd5ed79f9f61e71bdd66b7ea5e151aab43ca8842b83431525cb2190bd5aa1"

# Start API
uvicorn causalmma.api.app:app --host 0.0.0.0 --port 8000 --reload
```

### Terminal 2: Start PHP Admin Panel

```bash
cd /path/to/causalmma-admin

# Start PHP server
php -S localhost:8080
```

---

## 🧪 Test the System

### 1. Test API Health

```bash
curl http://localhost:8000/health
```

**Expected:**
```json
{"status":"healthy","version":"..."}
```

### 2. Test Admin Endpoint

```bash
curl -H "X-Admin-Key: admin_PpCV3HEpdKiXLuX6X4k3Oq25FM88n20ADddo1CT9CIg" \
  http://localhost:8000/api/v1/admin/dashboard
```

**Expected:**
```json
{
  "total_organizations": 0,
  "total_users": 0,
  ...
}
```

### 3. Test Admin Panel Login

1. Open browser: **http://localhost:8080**
2. Login:
   - Username: `admin`
   - Password: `admin123`
3. Should see dashboard

### 4. Test Trial Key Creation

1. Navigate to "Trial Keys" page
2. Fill form:
   - Number: 5
   - Duration: 14 days
3. Click "Generate Trial Keys"
4. Verify keys are created
5. Download CSV

---

## 🐛 Troubleshooting

### Issue: "Cannot connect to API"

**Check API is running:**
```bash
curl http://localhost:8000/health
```

**Check admin endpoint:**
```bash
curl -H "X-Admin-Key: admin_PpCV3HEpdKiXLuX6X4k3Oq25FM88n20ADddo1CT9CIg" \
  http://localhost:8000/api/v1/admin/dashboard
```

### Issue: Login fails

**Verify credentials in config.php:**
- Username: `admin`
- Password: `admin123`

**Test password hash:**
```bash
php -r "
\$hash = '\$2y\$12\$p8IVwFum7tK5tllYQ72YZu6xj3.2aMfWwl6m2bypmkcGTuyIlfi8q';
var_dump(password_verify('admin123', \$hash));
"
# Should output: bool(true)
```

### Issue: PHP server won't start

**Check if port is in use:**
```bash
lsof -i :8080
```

**Kill existing PHP server:**
```bash
kill $(cat /tmp/php_server.pid)
```

**Try different port:**
```bash
php -S localhost:8081
```

---

## 📝 Database Setup

**If testing with real database:**

```bash
# Set DATABASE_URL
export DATABASE_URL="postgresql://user:pass@localhost:5432/causalmma"

# Initialize database
cd /path/to/causalmma
python -c "from causalmma.mlops.database import init_database; init_database()"

# Create test organization
python -c "
from causalmma.mlops.database import get_session, Organization, TierEnum, OrganizationStatusEnum
from datetime import datetime, timedelta

db = get_session()
org = Organization(
    name='Test Organization',
    tier=TierEnum.FREE,
    status=OrganizationStatusEnum.ACTIVE,
    monthly_request_limit=3000,
    rate_limit_per_minute=5,
    trial_ends_at=datetime.utcnow() + timedelta(days=14)
)
db.add(org)
db.commit()
print(f'Created org: {org.id}')
"
```

---

## ✅ Testing Checklist

**API Tests:**
- [ ] Health endpoint responds
- [ ] Admin dashboard endpoint responds
- [ ] Admin key authentication works
- [ ] Database connection works (if using DB)

**Admin Panel Tests:**
- [ ] Login page loads
- [ ] Login with admin/admin123 works
- [ ] Dashboard displays statistics
- [ ] Navigation works
- [ ] Trial keys page loads
- [ ] Can create trial keys
- [ ] CSV download works

---

## 🎯 Next Steps

**After local testing succeeds:**

1. **Deploy API to Render:**
   - Set `ADMIN_API_KEY_HASH` in environment
   - Update config.php with Render URL

2. **Deploy Admin Panel:**
   - Upload to InfinityFree
   - Update config.php for production

3. **Update config.php for production:**
   ```php
   define('API_BASE_URL', 'https://your-api.onrender.com');
   ini_set('session.cookie_secure', 1);  // HTTPS only
   ```

---

## 🛑 Stop Services

**Stop PHP server:**
```bash
kill $(cat /tmp/php_server.pid)
# or press Ctrl+C in the PHP terminal
```

**Stop API:**
```bash
# Press Ctrl+C in the API terminal
```

---

**Test Credentials:**
- Username: `admin`
- Password: `admin123`
- API Key: `admin_PpCV3HEpdKiXLuX6X4k3Oq25FM88n20ADddo1CT9CIg`

**Local URLs:**
- API: http://localhost:8000
- Admin Panel: http://localhost:8080
- API Docs: http://localhost:8000/docs
