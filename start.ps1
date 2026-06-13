# Levanta el backend (Laravel) y el frontend (Astro) con un solo comando.
# Uso:  .\start.ps1

$raiz = $PSScriptRoot

# Refrescar PATH por si la terminal es vieja (no reconoce php/npm)
$env:Path = [Environment]::GetEnvironmentVariable('Path','Machine') + ';' + [Environment]::GetEnvironmentVariable('Path','User')

Write-Host "Iniciando backend (Laravel)  -> http://localhost:8000" -ForegroundColor Cyan
Start-Process powershell -ArgumentList "-NoExit", "-Command", "Set-Location '$raiz\backend'; php artisan serve"

Write-Host "Iniciando frontend (Astro)   -> http://localhost:4321" -ForegroundColor Cyan
Start-Process powershell -ArgumentList "-NoExit", "-Command", "Set-Location '$raiz\frontend'; npm run dev"

Start-Sleep -Seconds 3
Write-Host ""
Write-Host "Listo. Abriendo la web en el navegador..." -ForegroundColor Green
Start-Process "http://localhost:4321"
