# PISE-PP Quick Start Guide

**Get PISE-PP Backend Running in 5 Minutes**

---

## 📋 Prerequisites

```bash
# Check versions
php -v           # PHP 8.3+
composer --version
node -v          # Node 18+
```

---

## 🚀 5-Minute Setup

### Step 1: Clone & Install (1 min)
```bash
cd Backend_new
composer install
```

### Step 2: Configure Environment (2 min)
```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
```

### Step 3: Setup Database (1 min)
```bash
php artisan migrate
php artisan db:seed  # Optional: create sample data
```

### Step 4: Start Server (1 min)
```bash
php artisan serve
# API running at http://localhost:8000
```

---

## 🧪 Test the API

### 1. Register User
```bash
curl -X POST http://localhost:8000/api/v1/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "SecurePass123!@",
    "password_confirmation": "SecurePass123!@",
    "org_id": 1
  }'
```

Response:
```json
{
  "access_token": "eyJ0eXAi...",
  "token_type": "Bearer",
  "expires_in": 86400,
  "requires_2fa": true,
  "temp_token": "temp_..."
}
```

### 2. Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePass123!@"
  }'
```

### 3. Get User Profile (Authenticated)
```bash
curl -X GET http://localhost:8000/api/v1/auth/me \
  -H "Authorization: Bearer eyJ0eXAi..."
```

### 4. Create Project
```bash
curl -X POST http://localhost:8000/api/v1/projects \
  -H "Authorization: Bearer eyJ0eXAi..." \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Water Project 2025",
    "description": "Provide clean water to 5000 people",
    "budget_total": 50000,
    "start_date": "2025-06-01",
    "end_date": "2026-06-01"
  }'
```

### 5. List Projects
```bash
curl -X GET http://localhost:8000/api/v1/projects \
  -H "Authorization: Bearer eyJ0eXAi..."
```

---

## 📚 Useful Commands

### Development
```bash
# Clear cache
php artisan cache:clear

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Fresh database
php artisan migrate:fresh --seed

# Run tinker (interactive shell)
php artisan tinker

# Start queue worker
php artisan queue:work
```

### Testing
```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/AuthTest.php

# With coverage
php artisan test --coverage

# Generate coverage report
php artisan test --coverage --coverage-html=coverage/
```

### Code Quality
```bash
# Run linter (Pint)
./vendor/bin/pint

# Fix code style
./vendor/bin/pint --fix

# Run PHP analyzer
./vendor/bin/phpstan analyse app/
```

### Database
```bash
# Create seed
php artisan make:seeder ProjectSeeder

# Seed database
php artisan db:seed --class=ProjectSeeder

# Migrate specific database
php artisan migrate --database=postgresql
```

---

## 🔐 Setup 2FA (Optional)

### Enable 2FA for User

```bash
# 1. Enable 2FA endpoint
curl -X POST http://localhost:8000/api/v1/auth/2fa/enable \
  -H "Authorization: Bearer eyJ0eXAi..."

# Response includes QR code URL and secret
```

### 2. Scan QR Code
- Open Google Authenticator or Authy
- Scan the QR code
- Get 6-digit code

### 3. Verify 2FA Setup
```bash
curl -X POST http://localhost:8000/api/v1/auth/2fa/verify-setup \
  -H "Authorization: Bearer eyJ0eXAi..." \
  -H "Content-Type: application/json" \
  -d '{
    "code": "123456"
  }'
```

### 4. Login with 2FA
```bash
# First: login normally
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePass123!@"
  }'

# Get temp_token from response, then verify OTP:
curl -X POST http://localhost:8000/api/v1/auth/2fa/verify \
  -H "Content-Type: application/json" \
  -d '{
    "temp_token": "temp_...",
    "code": "123456"
  }'

# Get access_token back
```

---

## 📁 Project Structure

```
Backend_new/
├── app/
│   ├── Models/             # 15 Eloquent models
│   ├── Http/
│   │   ├── Controllers/    # 8 controllers (50+ endpoints)
│   │   ├── Requests/       # Form validation
│   │   └── Resources/      # API serialization
│   └── Services/           # Business logic (to add)
├── database/
│   ├── migrations/         # 15 tables
│   └── seeders/            # Sample data
├── routes/
│   └── api.php             # All API routes
├── config/
│   └── security.php        # Security settings
├── tests/                  # Test suites
├── .env.example            # Configuration template
├── README.md               # Getting started
├── IMPLEMENTATION_PLAN.md  # Full documentation
├── COMPLETION_REPORT.md    # What was built
└── PHASE_4_ROADMAP.md      # Next steps
```

---

## 🔗 API Endpoints Overview

### Authentication (12 endpoints)
```
POST   /auth/register
POST   /auth/login
POST   /auth/logout
POST   /auth/refresh
GET    /auth/me
POST   /auth/2fa/enable
POST   /auth/2fa/verify-setup
POST   /auth/2fa/verify
POST   /auth/2fa/disable
GET    /auth/2fa/status
GET    /auth/sessions
DELETE /auth/sessions/{id}
```

### Projects (7 endpoints)
```
GET    /projects
POST   /projects
GET    /projects/{id}
PUT    /projects/{id}
DELETE /projects/{id}
GET    /projects/{id}/dashboard
GET    /projects/{id}/risk-score
```

### Activities (7 endpoints)
```
GET    /projects/{id}/activities
POST   /projects/{id}/activities
PUT    /projects/{id}/activities/{activity}
POST   /projects/{id}/activities/{activity}/complete
POST   /projects/{id}/activities/{activity}/block
GET    /projects/{id}/activities/{activity}/dependencies
DELETE /projects/{id}/activities/{activity}
```

### KPIs (7 endpoints)
```
GET    /projects/{id}/kpis
POST   /projects/{id}/kpis
PUT    /projects/{id}/kpis/{kpi}
POST   /projects/{id}/kpis/{kpi}/measures
GET    /projects/{id}/kpis/{kpi}/history
GET    /projects/{id}/kpis/{kpi}/performance
DELETE /projects/{id}/kpis/{kpi}
```

### Finance (7 endpoints)
```
GET    /projects/{id}/finance/budgets
POST   /projects/{id}/finance/budgets
POST   /projects/{id}/finance/budgets/{budget}/expenses
PUT    /projects/{id}/finance/expenses/{expense}/validate
PUT    /projects/{id}/finance/expenses/{expense}/reject
GET    /projects/{id}/finance/summary
```

### Field/Terrain (11 endpoints)
```
GET    /projects/{id}/field/forms
POST   /projects/{id}/field/forms
GET    /projects/{id}/field/forms/{form}
POST   /projects/{id}/field/forms/{form}/submit
GET    /projects/{id}/field/forms/{form}/submissions
POST   /projects/{id}/field/submissions/{submission}/validate
POST   /projects/{id}/field/submissions/{submission}/reject
POST   /field/sync-batch
GET    /projects/{id}/field/map-data
```

---

## 🐛 Troubleshooting

### "Connection refused"
```bash
# Make sure Redis is running
redis-cli ping  # Should return PONG

# Or use database cache instead
# Edit .env: CACHE_DRIVER=database
```

### "No such file or directory: database/database.sqlite"
```bash
# Create the SQLite database
touch database/database.sqlite
php artisan migrate
```

### "CORS error in frontend"
```bash
# Edit .env
CORS_ALLOWED_ORIGINS=http://localhost:3000

# Or edit config/cors.php if it exists
```

### "Migration not found"
```bash
# Clear cached routes
php artisan route:clear
php artisan config:clear

# Then migrate again
php artisan migrate
```

### "Unauthorized 401"
```bash
# Make sure Bearer token is included
curl -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Generate new token by logging in
```

---

## 📖 Documentation

- **Full API Docs** → [IMPLEMENTATION_PLAN.md](./IMPLEMENTATION_PLAN.md)
- **What's Implemented** → [COMPLETION_REPORT.md](./COMPLETION_REPORT.md)
- **Phase 4 Plans** → [PHASE_4_ROADMAP.md](./PHASE_4_ROADMAP.md)
- **Security Details** → See `config/security.php`
- **Database Schema** → See `database/migrations/`

---

## 💡 Next Steps

1. **Explore the API** - Use cURL or Postman to test endpoints
2. **Read Documentation** - Check IMPLEMENTATION_PLAN.md for details
3. **Setup Frontend** - Integrate with Nuxt.js (in Frontend folder)
4. **Run Tests** - Execute `php artisan test`
5. **Deploy** - Follow Docker setup in PHASE_4_ROADMAP.md

---

## 🆘 Need Help?

1. Check the documentation files in this folder
2. Review error logs: `storage/logs/laravel.log`
3. Run tinker to debug: `php artisan tinker`
4. Check database: `sqlite3 database/database.sqlite`

---

**Version:** 1.0.0  
**Last Updated:** June 2025  
**Ready for Production:** ✅ (After Phase 4)
