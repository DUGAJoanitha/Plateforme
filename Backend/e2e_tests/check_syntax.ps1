$files = @("config.py","helpers.py","test_01_auth.py","test_02_projects.py","test_03_activities.py","test_04_kpis.py","test_05_finance.py","test_06_field.py","test_07_reports.py","test_08_ai.py","run_e2e.py")
$errors = 0
foreach ($f in $files) {
    $result = python -m py_compile $f 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "OK : $f" -ForegroundColor Green
    } else {
        Write-Host "ERREUR : $f" -ForegroundColor Red
        Write-Host $result
        $errors++
    }
}
if ($errors -eq 0) { Write-Host "`nTous les fichiers sont valides!" -ForegroundColor Green }
else { Write-Host "`n$errors erreur(s) trouvee(s)" -ForegroundColor Red }
