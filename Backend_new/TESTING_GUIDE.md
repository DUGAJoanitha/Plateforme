# Testing Guide - PISE-PP Backend

**Complete Testing Strategy & Execution Guide**

---

## 🧪 Test Structure

```
tests/
├── Feature/
│   ├── Auth/
│   │   └── AuthenticationTest.php        ✅ 8 tests
│   └── Api/
│       ├── ProjectTest.php                ✅ 8 tests
│       ├── ActivityTest.php               (coming)
│       ├── KPITest.php                    (coming)
│       ├── FinanceTest.php                (coming)
│       └── FieldTest.php                  (coming)
├── Unit/
│   └── Models/
│       ├── KPITest.php                    ✅ 6 tests
│       ├── ActivityTest.php               ✅ 6 tests
│       ├── BudgetLineTest.php             ✅ 8 tests
│       └── RiskTest.php                   ✅ 7 tests
└── TestCase.php                           (base class)
```

---

## 🚀 Quick Start

### Run All Tests
```bash
php artisan test

# Output:
# PASS  Tests/Unit/Models/KPITest.php (6 tests)
# PASS  Tests/Unit/Models/ActivityTest.php (6 tests)
# PASS  Tests/Unit/Models/BudgetLineTest.php (8 tests)
# PASS  Tests/Unit/Models/RiskTest.php (7 tests)
# PASS  Tests/Feature/Auth/AuthenticationTest.php (8 tests)
# PASS  Tests/Feature/Api/ProjectTest.php (8 tests)
#
# Tests: 43 passed (210ms)
```

### Run Specific Test File
```bash
php artisan test tests/Unit/Models/KPITest.php

# Output:
# PASS  Tests/Unit/Models/KPITest.php (6 tests)
# ✓ test_calculate_performance
# ✓ test_is_on_track
# ✓ test_calculate_performance_zero_target
# ✓ test_performance_exceeds_target
# ✓ test_kpi_has_many_measures
# ✓ test_kpi_belongs_to_project
```

### Run Specific Test
```bash
php artisan test tests/Unit/Models/KPITest.php --filter=test_calculate_performance
```

### Run with Coverage
```bash
php artisan test --coverage

# Output coverage report showing:
# - Overall coverage %
# - File-by-file coverage
# - Uncovered lines
```

### Generate HTML Coverage Report
```bash
php artisan test --coverage --coverage-html=coverage/
# Open coverage/index.html in browser
```

---

## 📊 Unit Tests (27 tests)

### KPI Tests (6 tests)
```bash
php artisan test tests/Unit/Models/KPITest.php
```

**Tests:**
- ✅ Calculate KPI performance percentage
- ✅ Check if KPI is on-track (>= 70%)
- ✅ Handle zero target value
- ✅ Performance exceeding target
- ✅ KPI relationships (many measures)
- ✅ KPI belongs to project

**What they test:**
```php
$kpi = KPI::factory()->create(['target_value' => 100, 'current_value' => 75]);
$this->assertEquals(75, $kpi->calculatePerformance());
$this->assertTrue($kpi->isOnTrack());  // >= 70% returns true
```

### Activity Tests (6 tests)
```bash
php artisan test tests/Unit/Models/ActivityTest.php
```

**Tests:**
- ✅ Activity blocked when dependency incomplete
- ✅ Activity not blocked when dependency completed
- ✅ Mark activity complete (sets status & progress)
- ✅ Activity without dependency not blocked
- ✅ Activity relationships
- ✅ Activity progress tracking (0-100%)

**What they test:**
```php
$parent = Activity::factory()->create(['status' => 'planned']);
$child = Activity::factory()->create(['depends_on' => $parent->id]);
$this->assertTrue($child->isBlocked());
$parent->update(['status' => 'completed']);
$this->assertFalse($child->isBlocked());
```

### BudgetLine Tests (8 tests)
```bash
php artisan test tests/Unit/Models/BudgetLineTest.php
```

**Tests:**
- ✅ Calculate budget balance
- ✅ Calculate consumption percentage
- ✅ Detect over-budget situations
- ✅ Trigger alerts at threshold
- ✅ No alert below threshold
- ✅ 100% consumption calculation
- ✅ Budget relationships
- ✅ Budget has many expenses

**What they test:**
```php
$budget = BudgetLine::factory()->create(['allocated' => 1000, 'spent' => 900]);
$this->assertEquals(100, $budget->getBalance());
$this->assertEquals(90, $budget->getConsumptionPercentage());
$this->assertTrue($budget->shouldTriggerAlert());  // 90% >= 90% threshold
```

### Risk Tests (7 tests)
```bash
php artisan test tests/Unit/Models/RiskTest.php
```

**Tests:**
- ✅ Calculate CRITICAL risk level (5x5)
- ✅ Calculate HIGH risk level (4x4)
- ✅ Calculate MEDIUM risk level (3x3)
- ✅ Calculate LOW risk level (1x1)
- ✅ Risk score calculation (probability × impact)
- ✅ Risk relationships
- ✅ Risk status enum values

**What they test:**
```php
$risk = Risk::factory()->create(['probability' => 5, 'impact' => 5]);
$this->assertEquals('CRITICAL', $risk->getRiskLevel());
$this->assertEquals(25, $risk->score);  // 5 * 5
```

---

## 🔌 Feature/Integration Tests (16 tests)

### Authentication Tests (8 tests)
```bash
php artisan test tests/Feature/Auth/AuthenticationTest.php
```

**Tests:**
- ✅ User can register
- ✅ User cannot register with weak password
- ✅ User can login
- ✅ User cannot login with wrong password
- ✅ User can get profile when authenticated
- ✅ Unauthenticated user cannot get profile
- ✅ User can logout
- ✅ User can refresh token

**What they test:**
```php
// Register flow
$this->postJson('/api/v1/auth/register', [
    'email' => 'john@example.com',
    'password' => 'SecurePass123!@'
])->assertStatus(201)
  ->assertJsonStructure(['access_token']);

// Login & access
$this->actingAs($user, 'sanctum')
    ->getJson('/api/v1/auth/me')
    ->assertStatus(200);

// Unauthorized access
$this->getJson('/api/v1/auth/me')
    ->assertStatus(401);
```

### Project Tests (8 tests)
```bash
php artisan test tests/Feature/Api/ProjectTest.php
```

**Tests:**
- ✅ Can list projects
- ✅ Can create project
- ✅ Can view project
- ✅ Can update project
- ✅ Can delete project (soft delete)
- ✅ Can view project dashboard
- ✅ Unauthenticated user cannot access
- ✅ Cannot access other org's projects

**What they test:**
```php
// CRUD operations
$this->postJson('/api/v1/projects', [...])
    ->assertStatus(201);

$this->putJson("/api/v1/projects/{$project->id}", [...])
    ->assertStatus(200);

$this->deleteJson("/api/v1/projects/{$project->id}")
    ->assertStatus(200);

// Authorization
$this->actingAs($userOtherOrg)
    ->getJson("/api/v1/projects/{$project->id}")
    ->assertStatus(403);  // Forbidden
```

---

## ⚙️ Test Configuration

### phpunit.xml
```xml
<phpunit>
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    
    <coverage>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
        <exclude>
            <directory>./app/Exceptions</directory>
            <directory>./app/Http/Middleware</directory>
        </exclude>
    </coverage>
</phpunit>
```

---

## 🔍 Database for Testing

### Test Database Setup
Tests use SQLite in-memory database:
```php
// tests/TestCase.php
use RefreshDatabase;  // Migrates fresh DB for each test
```

### Factories for Seeding
```php
// Create test data quickly
$user = User::factory()->create();
$project = Project::factory(5)->create();
$kpi = KPI::factory()->create([
    'target_value' => 100,
    'current_value' => 75,
]);
```

---

## 📈 Coverage Goals

### Current Coverage
- **Models**: 95%+ (all critical methods tested)
- **Controllers**: 0% (Phase 2)
- **Overall**: ~30% (Models only)

### Phase 2 Coverage Targets
- **Models**: 95%+
- **Controllers**: 80%+ (critical paths)
- **Middleware**: 70%+
- **Overall**: 75%+

### Phase 3 Coverage Targets
- Everything: 85%+

---

## 🧬 Writing New Tests

### Unit Test Template
```php
<?php
namespace Tests\Unit\Models;

use App\Models\MyModel;
use Tests\TestCase;

class MyModelTest extends TestCase
{
    public function test_my_method(): void
    {
        $model = MyModel::factory()->create(['field' => 'value']);
        $result = $model->myMethod();
        
        $this->assertEquals('expected', $result);
    }
    
    public function test_relationships(): void
    {
        $model = MyModel::factory()->create();
        $this->assertCount(5, $model->relatedModels);
    }
}
```

### Feature Test Template
```php
<?php
namespace Tests\Feature\Api;

use App\Models\User;
use Tests\TestCase;

class MyControllerTest extends TestCase
{
    public function test_endpoint(): void
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/endpoint');
        
        $response->assertStatus(200)
            ->assertJsonStructure(['data']);
    }
}
```

---

## 🔄 Continuous Testing

### GitHub Actions Workflow
Create `.github/workflows/tests.yml`:
```yaml
name: Tests
on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: postgres:16-alpine
        env:
          POSTGRES_PASSWORD: secret
    steps:
      - uses: actions/checkout@v3
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install
      - run: php artisan key:generate
      - run: php artisan migrate
      - run: php artisan test
```

### Run Before Commit
```bash
# Add to .git/hooks/pre-commit
#!/bin/bash
php artisan test
if [ $? -ne 0 ]; then
  echo "Tests failed! Commit aborted."
  exit 1
fi
```

---

## 📊 Test Results Example

```
   PASS  Tests/Unit/Models/KPITest.php (6 tests)
   PASS  Tests/Unit/Models/ActivityTest.php (6 tests)
   PASS  Tests/Unit/Models/BudgetLineTest.php (8 tests)
   PASS  Tests/Unit/Models/RiskTest.php (7 tests)
   PASS  Tests/Feature/Auth/AuthenticationTest.php (8 tests)
   PASS  Tests/Feature/Api/ProjectTest.php (8 tests)

Tests: 43 passed
Time: 0.214s
Code Coverage: 32.45% (1205/3710 LOC covered)
```

---

## 🚨 Common Issues

### Issue: "Class not found"
```bash
composer dump-autoload
php artisan test
```

### Issue: "Cannot resolve fixture"
```bash
php artisan migrate --database=sqlite
php artisan test
```

### Issue: "Port already in use"
```bash
# Kill existing process or use different port
php artisan serve --port=8001
```

### Issue: "Database connection refused"
Ensure SQLite is created:
```bash
touch database/database.sqlite
php artisan migrate
```

---

## 📋 Testing Checklist

- [ ] Run all tests locally
- [ ] Check code coverage (aim for 80%+)
- [ ] All tests pass before commit
- [ ] Add tests for new features
- [ ] Update tests when modifying code
- [ ] CI/CD runs tests on push
- [ ] Coverage reports generated
- [ ] No hardcoded test data
- [ ] Use factories for consistency
- [ ] Test both happy & sad paths

---

## 🎯 Next Phase Tests

**Phase 2 (To be created):**
- [ ] Controller endpoint tests (50+ endpoints)
- [ ] Validation rule tests
- [ ] Authorization tests
- [ ] Exception handling tests

**Phase 3 (To be created):**
- [ ] End-to-end workflows
- [ ] Performance benchmarks
- [ ] Load testing (k6)
- [ ] Security testing

---

## 📚 Resources

- [Laravel Testing Docs](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Factory Pattern](https://laravel.com/docs/eloquent-factories)
- [Test Coverage](https://phpunit.de/code-coverage.html)

---

**Status:** 43 Tests Implemented ✅  
**Coverage:** ~32% (Models complete)  
**Next:** Add controller & integration tests  
**Last Updated:** June 2025
