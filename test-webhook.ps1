# Test Webhook Script

Write-Host "Testing RECEIVE Webhook..." -ForegroundColor Cyan

$body = @{
    title = "Learning Webhooks"
    message = "This is a webhook I am sending to my own app!"
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/webhook/notification" `
        -Method Post `
        -ContentType "application/json" `
        -Body $body
    
    Write-Host "`nSUCCESS!" -ForegroundColor Green
    Write-Host "Response:" -ForegroundColor Yellow
    $response | ConvertTo-Json
    
    Write-Host "`n✅ Webhook received and saved to database!" -ForegroundColor Green
    Write-Host "✅ Check http://127.0.0.1:8000/dashboard to see it!" -ForegroundColor Green
    
} catch {
    Write-Host "`nERROR!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host "`n⚠️ Make sure server is running with: php artisan serve" -ForegroundColor Yellow
}

Write-Host "`nPress any key to close..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
