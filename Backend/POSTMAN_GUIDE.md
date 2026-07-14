# Postman Collection Guide

**Complete API Testing & Documentation with Postman**

---

## 📥 Import Collection

### Option 1: Direct Import
1. Open Postman
2. Click **Import** → **Choose Files**
3. Select `postman_collection.json`
4. Collection added to your workspace

### Option 2: Import from URL
1. **Import** → **Link**
2. Paste: `file:///path/to/postman_collection.json`
3. Click **Continue**

---

## ⚙️ Setup Environment Variables

### Create Environment
1. **Environments** (bottom left)
2. Click **+** to create new
3. Name: **PISE-PP Local**

### Add Variables
```
base_url        http://localhost:8000
access_token    (leave empty - will be set after login)
```

---

## 🔑 Authentication Flow

### Step 1: Register User
1. **Authentication** → **Register**
2. Click **Send**
3. Get access token from response

### Step 2: Set Access Token
1. Copy `access_token` from register response
2. **Environments** → **PISE-PP Local**
3. In `access_token` field, paste the token
4. Click **Save**

### Step 3: Login Alternative
If already have credentials:
1. **Authentication** → **Login**
2. Modify email/password
3. Click **Send**
4. Copy token to environment

### Step 4: Use Token in Requests
All authenticated endpoints automatically use `{{access_token}}` from environment

---

## 📋 Collection Structure

### Authentication (8 requests)
- ✅ **Register** - Create new user account
- ✅ **Login** - Authenticate user
- ✅ **Get Profile** - Retrieve user info
- ✅ **Logout** - End session
- ✅ **Refresh Token** - Get new access token
- ✅ **Enable 2FA** - Setup TOTP
- ✅ **Verify 2FA Setup** - Confirm OTP
- ✅ **Verify 2FA Code** - Login with OTP
- ✅ **Get 2FA Status** - Check if 2FA enabled

### Projects (7 requests)
- 📋 **List Projects** - Get all projects (paginated)
- ➕ **Create Project** - Create new project
- 👁️ **Get Project** - View specific project
- ✏️ **Update Project** - Modify project
- 🗑️ **Delete Project** - Archive project
- 📊 **Project Dashboard** - View metrics
- ⚠️ **Project Risk Score** - Get risk level

### Activities (3 requests)
- 📝 **List Activities** - All activities in project
- ➕ **Create Activity** - Add new activity
- ✅ **Mark Complete** - Complete activity

### KPIs (5 requests)
- 📊 **List KPIs** - All KPIs in project
- ➕ **Create KPI** - Add performance indicator
- 📈 **Add Measurement** - Record measurement
- 🎯 **KPI Performance** - Get performance %
- 📉 **KPI History** - View measurement history

### Finance (5 requests)
- 💰 **List Budgets** - View budget lines
- ➕ **Create Budget Line** - Add budget category
- 💸 **Submit Expense** - Record expense
- ✅ **Validate Expense** - Approve expense
- 📊 **Finance Summary** - Budget overview

### Field/Terrain (6 requests)
- 📋 **List Forms** - View data collection forms
- ➕ **Create Form** - Create new form
- ✍️ **Submit Form Data** - Record field data
- 📋 **List Submissions** - View submissions
- 🔄 **Sync Batch** - Sync offline data
- 🗺️ **Map Data** - Get geolocation data

---

## 🧪 Testing Workflow

### Workflow 1: Complete Project Setup
```
1. Register → Copy token
2. Create Project
3. Create Activities
4. Create KPIs
5. Add KPI Measurements
6. View Dashboard
```

### Workflow 2: Budget & Expenses
```
1. Create Project
2. Create Budget Line
3. Submit Expense
4. Validate Expense
5. View Finance Summary
```

### Workflow 3: Field Data Collection
```
1. Create Project
2. Create Field Form
3. Submit Form Data
4. Sync Batch (if offline)
5. View Map Data
```

### Workflow 4: 2FA Setup
```
1. Register or Login
2. Enable 2FA
3. Verify Setup (with code from authenticator)
4. Logout
5. Login again
6. Verify 2FA Code
```

---

## 💡 Tips & Tricks

### Bulk Test All Endpoints
1. Select all requests in a folder
2. **Run** → **Start Run**
3. Postman executes sequentially
4. View results in **Test Results**

### Auto-extract Tokens
Add this script to **Register** request, **Tests** tab:
```javascript
var jsonData = pm.response.json();
pm.environment.set("access_token", jsonData.access_token);
```

### Test Pagination
In **List Projects** query params:
```
page        1
per_page    15
status      active      (optional)
search      water       (optional)
```

### Debug Responses
1. Send request
2. Check **Status** (should be 200 or 201)
3. View **Body** (JSON response)
4. Check **Headers** for rate limiting

### Save Request Body as Template
1. Modify request body with sample data
2. **Save as** → Choose collection
3. Reuse for future requests

---

## 🔐 Security Testing

### Test Authorization
1. Remove `Authorization` header
2. Send request
3. Should get 401 Unauthorized

### Test Cross-org Access
1. As User A, create project
2. Login as User B (different org)
3. Try to access User A's project
4. Should get 403 Forbidden

### Test Rate Limiting
1. Add **Pre-request Script**:
```javascript
for (let i = 0; i < 100; i++) {
  pm.sendRequest({
    url: pm.environment.get("base_url") + "/api/v1/projects",
    method: "GET",
    header: {
      "Authorization": "Bearer " + pm.environment.get("access_token")
    }
  }, function(err, response) {
    console.log(response.status);
  });
}
```

---

## 📊 Performance Testing

### Load Test with Runner
1. **Collection** → **Run**
2. Select all requests
3. Set **Iterations**: 10
4. Set **Delay**: 100ms
5. Click **Run** → Monitor performance

### Check Response Times
Each response shows timing:
- **Total**: Full request time
- **Connect**: Server connection time
- **Receive**: Download time

---

## 🐛 Troubleshooting

### 401 Unauthorized
- [ ] Token expired - refresh or re-login
- [ ] Token not set in environment
- [ ] Wrong environment selected

### 403 Forbidden
- [ ] User lacks permission for action
- [ ] Accessing another org's resource
- [ ] User role doesn't allow this

### 422 Validation Error
- [ ] Required fields missing
- [ ] Wrong data types
- [ ] Check error message in response

### 500 Server Error
- [ ] Bug in API code
- [ ] Check Laravel logs
- [ ] Restart server

### CORS Error
- [ ] Browser only - Postman unaffected
- [ ] Check API CORS configuration
- [ ] Frontend proxy needed

---

## 📚 API Documentation from Collection

### Generate Documentation
1. **Collection** → **... (menu)**
2. **View Documentation**
3. Auto-generated docs from collection
4. Share link: **View Collection** → **Share**

### Export as OpenAPI/Swagger
1. **Collection** → **... (menu)**
2. **Export**
3. Choose **OpenAPI 3.0**
4. Import to Swagger UI

---

## 🔄 Continuous Testing

### Setup Automated Monitors
1. **Monitors** → **Create Monitor**
2. Select collection
3. Choose environment
4. Set schedule (hourly/daily)
5. Get alerts on failures

### Integrate with CI/CD
```bash
# Run collection via Newman
npm install -g newman
newman run postman_collection.json \
  -e environment.json \
  --reporters cli,json
```

---

## 📋 Common Request Examples

### Create Project with Dates
```json
{
  "name": "Rural Health Project",
  "description": "Improve healthcare access",
  "budget_total": 100000,
  "start_date": "2025-06-01",
  "end_date": "2027-06-01",
  "programme_id": 1
}
```

### Add KPI with History
```json
{
  "name": "Patients Treated",
  "description": "Monthly patient count",
  "target_value": 500,
  "baseline": 100,
  "unit": "persons",
  "frequency": "monthly"
}
```

### Submit Expense
```json
{
  "amount": 2500,
  "description": "Medical supplies purchase",
  "proof_url": "https://storage.example.com/receipt.pdf"
}
```

### Create Field Form
```json
{
  "name": "Patient Intake Survey",
  "description": "Initial patient assessment",
  "schema": {
    "type": "object",
    "properties": {
      "age": {"type": "integer"},
      "gender": {"type": "string", "enum": ["M", "F", "Other"]},
      "symptoms": {"type": "array", "items": {"type": "string"}},
      "temperature": {"type": "number"}
    },
    "required": ["age", "gender"]
  }
}
```

---

## ✅ Checklist

- [ ] Collection imported to Postman
- [ ] Environment created and configured
- [ ] User registered successfully
- [ ] Access token saved to environment
- [ ] Can call `/auth/me` successfully
- [ ] Can create project
- [ ] Can view project dashboard
- [ ] All 50+ endpoints tested
- [ ] No 401 or 403 errors
- [ ] Response times acceptable (< 200ms)
- [ ] Ready for frontend integration

---

**Version:** 1.0.0  
**Last Updated:** June 2025  
**Total Endpoints:** 50+  
**All Methods:** GET, POST, PUT, DELETE
