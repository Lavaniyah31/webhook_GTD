<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Webhook Testing Dashboard - Real-Time</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { 
            max-width: 1400px; 
            margin: 0 auto; 
        }
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 42px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 18px;
            opacity: 0.9;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
        }
        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .card h2 {
            color: #2d3748;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 600;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
            font-family: 'Courier New', monospace;
        }
        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn:active {
            transform: translateY(0);
        }
        .status {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 15px;
        }
        .status-card {
            flex: 1;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
        }
        .status-card.receive {
            background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
            color: white;
        }
        .status-card.send {
            background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
            color: white;
        }
        .status-label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }
        .status-value {
            font-size: 32px;
            font-weight: bold;
        }
        .pulse {
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }
        .notifications {
            max-height: 600px;
            overflow-y: auto;
        }
        .notification {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 12px;
            animation: slideIn 0.5s ease;
        }
        .notification.new {
            background: #e6ffed;
            border-left-color: #48bb78;
            animation: slideIn 0.5s ease, glow 2s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes glow {
            0%, 100% { box-shadow: 0 0 0 rgba(72, 187, 120, 0); }
            50% { box-shadow: 0 0 20px rgba(72, 187, 120, 0.5); }
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .notification-title {
            font-weight: 600;
            color: #2d3748;
        }
        .notification-time {
            font-size: 12px;
            color: #718096;
        }
        .notification-message {
            color: #4a5568;
            margin-bottom: 8px;
        }
        .notification-data {
            background: white;
            padding: 8px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 11px;
            color: #2d3748;
            max-height: 80px;
            overflow-y: auto;
        }
        .webhook-log {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
            margin-top: 20px;
        }
        .log-entry {
            margin-bottom: 5px;
            padding: 5px;
            border-left: 3px solid #4299e1;
            padding-left: 10px;
        }
        .log-entry.success { border-left-color: #48bb78; }
        .log-entry.error { border-left-color: #f56565; }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #718096;
        }
        @media (max-width: 1024px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔄 Webhook Testing Dashboard</h1>
            <p>Send & Receive Webhooks in Real-Time</p>
        </div>

        <div class="grid">
            <!-- LEFT: Send Webhooks -->
            <div class="card">
                <h2>📤 Send Webhook</h2>
                
                <form id="webhook-form">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" id="title" placeholder="e.g., User Registered" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Message</label>
                        <input type="text" id="message" placeholder="e.g., A new user joined the platform" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Additional Data (JSON)</label>
                        <textarea id="extra-data" placeholder='{"user_id": 123, "email": "user@example.com"}'></textarea>
                    </div>
                    
                    <button type="submit" class="btn">🚀 Send Webhook</button>
                </form>

                <div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e2e8f0;">
                    <h3 style="margin-bottom: 15px; color: #2d3748;">Test Receive Webhook</h3>
                    <p style="color: #718096; font-size: 14px; margin-bottom: 10px;">Simulate receiving a webhook from an external service</p>
                    <button type="button" class="btn" id="test-receive-btn" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
                        📥 Simulate External Webhook
                    </button>
                </div>

                <div class="webhook-log" id="send-log">
                    <strong>📋 Activity Log:</strong>
                    <div id="log-entries"></div>
                </div>
            </div>

            <!-- RIGHT: Receive Webhooks -->
            <div class="card">
                <h2>
                    📥 Received Webhooks 
                    <span class="pulse"></span>
                    <span id="live-status" style="font-size: 14px; color: #48bb78; margin-left: 10px;">
                        🟢 Live • Auto-refreshing every 2s
                    </span>
                </h2>
                
                <div class="status">
                    <div class="status-card receive">
                        <div class="status-label">Total Received</div>
                        <div class="status-value" id="receive-count">0</div>
                    </div>
                    <div class="status-card send">
                        <div class="status-label">Total Sent</div>
                        <div class="status-value" id="send-count">0</div>
                    </div>
                </div>

                <div style="text-align: center; padding: 10px; background: #f7fafc; border-radius: 8px; margin-bottom: 15px; font-size: 13px; color: #4a5568;">
                    <span id="last-update">Last updated: Never</span>
                </div>

                <div class="notifications" id="notifications-container">
                    <div class="empty-state">
                        <h3>📭 No webhooks yet</h3>
                        <p>Send a webhook using the form to see it appear here!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let lastId = 0;
        let isFirstLoad = true;
        let lastReceivedCount = 0;
        let lastSentCount = 0;
        let lastRenderedHtml = '';

        // Fetch notifications in real-time
        async function fetchNotifications() {
            try {
                const response = await fetch('/notifications');
                const data = await response.json();
                
                if (data.notifications && data.notifications.length > 0) {
                    const latestId = data.notifications[0].id;
                    const hasNew = latestId > lastId;
                    
                    // Only update counters if values actually changed
                    if (data.total_received !== lastReceivedCount) {
                        document.getElementById('receive-count').textContent = data.total_received;
                        lastReceivedCount = data.total_received;
                    }
                    
                    if (data.total_sent !== lastSentCount) {
                        document.getElementById('send-count').textContent = data.total_sent;
                        lastSentCount = data.total_sent;
                    }
                    
                    // Only update timestamp when there's actually new data
                    if (hasNew || isFirstLoad) {
                        const now = new Date();
                        document.getElementById('last-update').textContent = 
                            `Last updated: ${now.toLocaleTimeString()}`;
                        
                        // Only re-render notifications if there's new data
                        displayNotifications(data.notifications, hasNew && !isFirstLoad);
                    }
                    
                    if (hasNew) {
                        lastId = latestId;
                        
                        if (!isFirstLoad) {
                            playNotificationSound();
                        }
                    }
                    
                    isFirstLoad = false;
                } else {
                    // No notifications yet - only update if changed
                    if (lastReceivedCount !== 0) {
                        document.getElementById('receive-count').textContent = 0;
                        lastReceivedCount = 0;
                    }
                    if (lastSentCount !== 0) {
                        document.getElementById('send-count').textContent = 0;
                        lastSentCount = 0;
                    }
                    if (isFirstLoad) {
                        document.getElementById('last-update').textContent = 'Waiting for webhooks...';
                        isFirstLoad = false;
                    }
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
            }
        }

        // Display notifications
        function displayNotifications(notifications, highlightNew) {
            const container = document.getElementById('notifications-container');
            
            if (notifications.length === 0) {
                return;
            }

            const newHtml = notifications.map((notif, index) => {
                const data = notif.data || {};
                const isNew = index === 0 && highlightNew;
                const sourceIcon = notif.source === 'dashboard' ? '📤' : '📥';
                const sourceLabel = notif.source === 'dashboard' ? 'SENT' : 'RECEIVED';
                const sourceColor = notif.source === 'dashboard' ? '#3182ce' : '#38a169';
                
                return `
                <div class="notification ${isNew ? 'new' : ''}" style="position: relative;">
                    <div class="notification-header">
                        <div class="notification-title">
                            ${isNew ? '🆕 ' : ''}${notif.title || 'Notification'}
                        </div>
                        <div class="notification-time">
                            <span style="background: ${sourceColor}; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; margin-right: 8px;">
                                ${sourceIcon} ${sourceLabel}
                            </span>
                            ID: ${notif.id} • ${new Date(notif.created_at).toLocaleString()}
                        </div>
                    </div>
                    <div class="notification-message">
                        ${notif.message || 'No message'}
                    </div>
                    <div class="notification-data" style="max-height: 200px; overflow-y: auto;">
                        <strong>📦 Webhook Data:</strong><br>
                        ${JSON.stringify(data, null, 2)}
                    </div>
                </div>
            `}).join('');
            
            // Only update DOM if HTML actually changed (prevents flickering)
            if (newHtml !== lastRenderedHtml) {
                container.innerHTML = newHtml;
                lastRenderedHtml = newHtml;
            }
        }

        // Send webhook form
        document.getElementById('webhook-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const title = document.getElementById('title').value;
            const message = document.getElementById('message').value;
            const extraDataText = document.getElementById('extra-data').value;
            
            let webhookData = {
                title: title,
                message: message
            };

            // Parse additional JSON data
            if (extraDataText.trim()) {
                try {
                    const extraData = JSON.parse(extraDataText);
                    webhookData = { ...webhookData, ...extraData };
                } catch (error) {
                    addLog('❌ Invalid JSON in additional data', 'error');
                    return;
                }
            }

            // Add source identifier for dashboard-sent webhooks
            webhookData.source = 'dashboard';

            try {
                addLog(`⏳ Sending webhook: "${title}"...`, 'info');
                
                const response = await fetch('/api/webhook/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(webhookData)
                });

                const result = await response.json();
                
                if (response.ok) {
                    addLog(`✅ Webhook sent successfully! ID: ${result.id}`, 'success');
                    addLog(`📤 Forwarded to ${result.forwarded_to} endpoint(s)`, 'success');
                    
                    // Clear form
                    document.getElementById('title').value = '';
                    document.getElementById('message').value = '';
                    document.getElementById('extra-data').value = '';
                    
                    // Play success sound
                    playSuccessSound();
                } else {
                    addLog(`❌ Error: ${result.message}`, 'error');
                }
            } catch (error) {
                addLog(`❌ Network error: ${error.message}`, 'error');
            }
        });

        // Test Receive Webhook Button
        document.getElementById('test-receive-btn').addEventListener('click', async () => {
            const testData = {
                title: "Test: External Webhook",
                message: "Simulating webhook from Postman/External Service",
                test_type: "receive_simulation",
                timestamp: new Date().toISOString(),
                source: "external"
            };

            try {
                addLog(`📥 Simulating external webhook...`, 'info');
                
                const response = await fetch('/api/webhook/notification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(testData)
                });

                const result = await response.json();
                
                if (response.ok) {
                    addLog(`✅ External webhook received! ID: ${result.id}`, 'success');
                    addLog(`📊 Received counter updated`, 'success');
                    playNotificationSound();
                } else {
                    addLog(`❌ Error: ${result.message}`, 'error');
                }
            } catch (error) {
                addLog(`❌ Network error: ${error.message}`, 'error');
            }
        });

        // Add log entry
        function addLog(message, type = 'info') {
            const logEntries = document.getElementById('log-entries');
            const entry = document.createElement('div');
            entry.className = `log-entry ${type}`;
            entry.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
            logEntries.insertBefore(entry, logEntries.firstChild);
            
            // Keep only last 10 entries
            while (logEntries.children.length > 10) {
                logEntries.removeChild(logEntries.lastChild);
            }
        }

        // Notification sound
        function playNotificationSound() {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 800;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.1);
        }

        // Success sound
        function playSuccessSound() {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.value = 600;
            oscillator.type = 'sine';
            
            gainNode.gain.setValueAtTime(0.15, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.15);
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.15);
        }

        // Initial fetch and polling
        fetchNotifications();
        setInterval(fetchNotifications, 2000);

        addLog('🚀 Webhook dashboard initialized', 'success');
        addLog('📡 Auto-refresh every 2 seconds', 'info');
    </script>
</body>
</html>
