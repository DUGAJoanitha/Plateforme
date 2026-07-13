#!/usr/bin/env pwsh
#Requires -Version 5.1

<#
.SYNOPSIS
    Script de test complet pour la Plateforme Backend
    Tests unitaires, feature (end-to-end), export Postman
.DESCRIPTION
    Lance tous les tests PHPUnit, puis génère la doc API Swagger/OpenAPI
    et exporte la collection Postman pour les tests manuels.
.EXAMPLE
    .\run_all_tests.ps1
    .\run_all_tests.ps1 -Filter Unit
    .\run_all_tests.ps1 -Filter Feature -Verbose
#>

param(
    [string]$Filter = "",
    [switch]$Coverage,
    [switch]$Verbose
)

$ProjectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path

function Write-Banner([string]$Text, [string]$Color = "Cyan") {
    $line = "=" * 60
    Write-Host "`n$line" -ForegroundColor $Color
    Write-Host "  $Text" -ForegroundColor $Color
    Write-Host "$line`n" -ForegroundColor $Color
}

function Run-Step([string]$Title, [scriptblock]$Block) {
    Write-Host ">> $Title" -ForegroundColor Yellow
    $result = & $Block
    if ($LASTEXITCODE -ne 0) {
        Write-Host "[ECHEC] $Title" -ForegroundColor Red
        return $false
    }
    Write-Host "[OK] $Title`n" -ForegroundColor Green
    return $true
}

# ─────────────────────────────────────────────
Write-Banner "PLATEFORME - SUITE DE TESTS COMPLÈTE"
Write-Host "Répertoire : $ProjectRoot"
Write-Host "Heure      : $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')`n"

$AllPassed = $true

# ─────────────────────────────────────────────
# 1. TESTS UNITAIRES
# ─────────────────────────────────────────────
Write-Banner "ÉTAPE 1 : Tests Unitaires" "Magenta"

$unitArgs = @("test", "--filter", "Unit")
if ($Verbose) { $unitArgs += "--verbose" }
if ($Coverage) { $unitArgs += @("--coverage-text") }

php artisan @unitArgs
if ($LASTEXITCODE -ne 0) { $AllPassed = $false }

# ─────────────────────────────────────────────
# 2. TESTS FEATURE / END-TO-END
# ─────────────────────────────────────────────
Write-Banner "ÉTAPE 2 : Tests Feature (End-to-End)" "Magenta"

$featureArgs = @("test", "--filter", "Feature")
if ($Verbose) { $featureArgs += "--verbose" }

php artisan @featureArgs
if ($LASTEXITCODE -ne 0) { $AllPassed = $false }

# ─────────────────────────────────────────────
# 3. TOUS LES TESTS (résumé complet)
# ─────────────────────────────────────────────
if (-not $Filter) {
    Write-Banner "ÉTAPE 3 : Rapport Final Complet" "Magenta"
    php artisan test
    if ($LASTEXITCODE -ne 0) { $AllPassed = $false }
}

# ─────────────────────────────────────────────
# 4. GÉNÉRATION DOC SWAGGER / POSTMAN
# ─────────────────────────────────────────────
Write-Banner "ÉTAPE 4 : Documentation API (Swagger/OpenAPI)" "Blue"

# Vérifier si Scribe est installé
$scribeInstalled = (php artisan list 2>&1) -match "scribe"

if ($scribeInstalled) {
    php artisan scribe:generate
    if ($LASTEXITCODE -eq 0) {
        Write-Host "[OK] Documentation générée dans public/docs/" -ForegroundColor Green
        Write-Host "     Swagger UI : http://localhost:8000/docs" -ForegroundColor Cyan
        Write-Host "     OpenAPI JSON: http://localhost:8000/docs.json" -ForegroundColor Cyan
        
        # Export OpenAPI pour import Postman
        if (Test-Path "public/docs/openapi.yaml") {
            Copy-Item "public/docs/openapi.yaml" "postman/openapi.yaml"
            Write-Host "[OK] openapi.yaml copié dans postman/" -ForegroundColor Green
        }
    }
} else {
    Write-Host "[INFO] Scribe non installé. Pour générer la doc Swagger :" -ForegroundColor Yellow
    Write-Host "       composer require --dev knuckleswtf/scribe" -ForegroundColor Gray
    Write-Host "       php artisan vendor:publish --provider='Knuckles\Scribe\ScribeServiceProvider' --tag='scribe-config'" -ForegroundColor Gray
    Write-Host "       php artisan scribe:generate" -ForegroundColor Gray
}

# ─────────────────────────────────────────────
# 5. COLLECTION POSTMAN
# ─────────────────────────────────────────────
Write-Banner "ÉTAPE 5 : Collection Postman" "Blue"

$postmanFile = Join-Path $ProjectRoot "postman\Plateforme_API.postman_collection.json"
if (Test-Path $postmanFile) {
    Write-Host "[OK] Collection Postman disponible :" -ForegroundColor Green
    Write-Host "     $postmanFile" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "  Pour l'importer dans Postman :" -ForegroundColor White
    Write-Host "  1. Ouvrez Postman" -ForegroundColor White
    Write-Host "  2. Cliquez sur 'Import'" -ForegroundColor White
    Write-Host "  3. Sélectionnez le fichier ci-dessus" -ForegroundColor White
    Write-Host "  4. Importez aussi : postman\Local.postman_environment.json" -ForegroundColor White
    Write-Host ""

    # Lancer tests Postman avec Newman si disponible
    $newmanPath = Get-Command newman -ErrorAction SilentlyContinue
    if ($newmanPath) {
        Write-Host "[INFO] Newman détecté. Lancement des tests Postman automatisés..." -ForegroundColor Yellow
        newman run $postmanFile --environment "postman\Local.postman_environment.json" --reporters cli,json --reporter-json-export "postman\results.json"
        if ($LASTEXITCODE -eq 0) {
            Write-Host "[OK] Tests Postman (Newman) réussis" -ForegroundColor Green
        } else {
            Write-Host "[ATTENTION] Certains tests Postman ont échoué (le serveur doit être démarré)" -ForegroundColor Yellow
        }
    } else {
        Write-Host "[INFO] Installez Newman pour exécuter la collection Postman en ligne de commande :" -ForegroundColor Gray
        Write-Host "       npm install -g newman" -ForegroundColor Gray
        Write-Host "       newman run postman\Plateforme_API.postman_collection.json -e postman\Local.postman_environment.json" -ForegroundColor Gray
    }
} else {
    Write-Host "[ATTENTION] Collection Postman introuvable" -ForegroundColor Yellow
}

# ─────────────────────────────────────────────
# 6. E2E TESTS (PLAYWRIGHT)
# ─────────────────────────────────────────────
Write-Banner "ÉTAPE 6 : Tests E2E (Playwright) - Frontend" "Magenta"

$frontendDir = Join-Path $ProjectRoot "..\Frontend"
if (Test-Path $frontendDir) {
    Push-Location $frontendDir
    # Vérifier si Playwright est installé
    if (Test-Path "node_modules\@playwright") {
        Write-Host "[INFO] Exécution des tests Playwright..." -ForegroundColor Yellow
        npx playwright test
        if ($LASTEXITCODE -ne 0) { $AllPassed = $false }
    } else {
        Write-Host "[ATTENTION] Playwright n'est pas installé dans le dossier Frontend." -ForegroundColor Yellow
    }
    Pop-Location
} else {
    Write-Host "[ATTENTION] Dossier Frontend introuvable." -ForegroundColor Yellow
}

# ─────────────────────────────────────────────
# 7. SMOKE TESTS & PERFORMANCE
# ─────────────────────────────────────────────
Write-Banner "ÉTAPE 7 : Smoke Tests & JMeter" "Magenta"

$smokeScript = Join-Path $ProjectRoot "tests\Smoke\smoke_test.php"
if (Test-Path $smokeScript) {
    Write-Host "[INFO] Exécution des Smoke Tests..." -ForegroundColor Yellow
    php $smokeScript
    if ($LASTEXITCODE -ne 0) { $AllPassed = $false }
}

$jmeterScript = Join-Path $ProjectRoot "tests\Performance\LoadTest.jmx"
if (Test-Path $jmeterScript) {
    $jmeterPath = Get-Command jmeter -ErrorAction SilentlyContinue
    if ($jmeterPath) {
        Write-Host "[INFO] Exécution des tests de charge JMeter..." -ForegroundColor Yellow
        jmeter -n -t $jmeterScript -l "tests\Performance\results.jtl"
        if ($LASTEXITCODE -ne 0) { $AllPassed = $false }
    } else {
        Write-Host "[ATTENTION] JMeter n'est pas installé ou n'est pas dans le PATH." -ForegroundColor Yellow
    }
}

# ─────────────────────────────────────────────
# RÉSUMÉ FINAL
# ─────────────────────────────────────────────
Write-Banner "RÉSUMÉ" $(if ($AllPassed) { "Green" } else { "Red" })

if ($AllPassed) {
    Write-Host "  ✅ Tous les tests sont passés !" -ForegroundColor Green
} else {
    Write-Host "  ❌ Des tests ont échoué. Consultez les logs ci-dessus." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "  Serveur de développement : php artisan serve" -ForegroundColor Cyan
Write-Host "  Swagger UI (après scribe): http://localhost:8000/docs" -ForegroundColor Cyan
Write-Host ""
