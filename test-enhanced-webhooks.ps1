# Test Enhanced Webhooks with Rich Data

Write-Host "Testing Enhanced Webhook System" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Test 1: Send rich webhook
Write-Host "1. Sending webhook with rich data..." -ForegroundColor Yellow

$body = @{
    title = "Order Placed"
    message = "Customer John Doe placed order #12345"
    order_id = 12345
    customer_name = "John Doe"
    customer_email = "john@example.com"
    amount = 99.99
    currency = "USD"
    items = @(
        @{ name = "Product A"; quantity = 2; price = 29.99 }
        @{ name = "Product B"; quantity = 1; price = 40.01 }
    )
} | ConvertTo-Json -Depth 10

try {
    $response = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/webhook/send" `
        -Method Post `
        -ContentType "application/json" `
        -Body $body
    
    Write-Host "✅ Webhook sent successfully!" -ForegroundColor Green
    Write-Host "   ID: $($response.id)" -ForegroundColor Gray
    Write-Host "   Forwarded to: $($response.forwarded_to) endpoint(s)" -ForegroundColor Gray
    Write-Host ""
} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
}

Start-Sleep -Seconds 1

# Test 2: Simulate receiving external webhook
Write-Host "2. Simulating external webhook with metadata..." -ForegroundColor Yellow

$body2 = @{
    title = "Payment Received"
    message = "Payment of $199.99 received from customer"
    payment_id = "pay_abc123xyz"
    customer_id = "cust_456"
    amount = 199.99
    currency = "USD"
    payment_method = "credit_card"
    card_last4 = "4242"
} | ConvertTo-Json -Depth 10

try {
    $response2 = Invoke-RestMethod -Uri "http://127.0.0.1:8000/api/webhook/notification" `
        -Method Post `
        -ContentType "application/json" `
        -Body $body2
    
    Write-Host "✅ Webhook received successfully!" -ForegroundColor Green
    Write-Host "   ID: $($response2.id)" -ForegroundColor Gray
    Write-Host ""
} catch {
    Write-Host "❌ Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""
Write-Host "Check your results:" -ForegroundColor Cyan
Write-Host "   Dashboard: http://127.0.0.1:8000/dashboard" -ForegroundColor White
Write-Host "   webhook.site URL from config" -ForegroundColor White
Write-Host ""
Write-Host "You should see:" -ForegroundColor Yellow
Write-Host "   - Rich JSON data on webhook.site" -ForegroundColor Gray
Write-Host "   - Real-time updates on dashboard every 2 seconds" -ForegroundColor Gray
Write-Host "   - Enhanced metadata with webhook_id and timestamps" -ForegroundColor Gray
Write-Host ""

Write-Host "Press any key to close..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
