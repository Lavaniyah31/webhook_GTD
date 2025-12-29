# Test Send Webhook

Write-Host "Testing SEND Webhook Endpoint..." -ForegroundColor Cyan

$body = @{
    title = "Test Send Webhook"
    message = "Testing the send functionality"
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/webhook/send" `
        -Method Post `
        -ContentType "application/json" `
        -Body $body
    
    Write-Host "`nSUCCESS!" -ForegroundColor Green
    Write-Host "Response:" -ForegroundColor Yellow
    $response | ConvertTo-Json
    
    Write-Host "`n✅ Webhook sent successfully!" -ForegroundColor Green
    Write-Host "✅ Sent counter should increase!" -ForegroundColor Green
    Write-Host "✅ Check http://127.0.0.1:8000/dashboard" -ForegroundColor Green
    
} catch {
    Write-Host "`nERROR!" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "`nError Details:" -ForegroundColor Yellow
        Write-Host $responseBody -ForegroundColor Red
    }
}

Write-Host "`nPress any key to close..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
