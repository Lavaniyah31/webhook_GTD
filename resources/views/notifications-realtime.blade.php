<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Notifications - Webhook System</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { 
            max-width: 1200px; 
            margin: 0 auto; 
            background: white; 
            border-radius: 15px; 
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
        }
        h1 { 
            color: #2d3748; 
            font-size: 32px;
        }
        .status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: #48bb78;
            color: white;
            border-radius: 25px;
            font-weight: 600;
        }
        .pulse {
            width: 12px;
            height: 12px;
            background: white;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 20px;
            border-radius: 10px;
            color: white;
        }
        .stat-label { font-size: 14px; opacity: 0.9; }
        .stat-value { font-size: 36px; font-weight: bold; margin-top: 5px; }
        .notifications {
            display: grid;
            gap: 15px;
        }
        .notification {
            background: #f7fafc;
            border-left: 4px solid #667eea;
            padding: 20px;
            border-radius: 8px;
            animation: slideIn 0.5s ease;
        }
        .notification.new {
            background: #e6ffed;
            border-left-color: #48bb78;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }
        .notification-title {
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }
        .notification-time {
            font-size: 12px;
            color: #718096;
        }
        .notification-message {
            color: #4a5568;
            margin-bottom: 10px;
        }
        .notification-data {
            background: white;
            padding: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #2d3748;
            max-height: 100px;
            overflow-y: auto;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }
        .empty-state svg {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📡 Real-Time Notifications</h1>
            <div class="status">
                <div class="pulse"></div>
                <span>Live</span>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-label">Total Received</div>
                <div class="stat-value" id="total-count">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Last Updated</div>
                <div class="stat-value" id="last-updated" style="font-size: 16px;">Never</div>
            </div>
        </div>

        <div id="notifications-container">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <h3>No notifications yet</h3>
                <p>Send a webhook to see real-time updates!</p>
            </div>
        </div>
    </div>

    <script>
        let lastId = 0;
        let isFirstLoad = true;

        async function fetchNotifications() {
            try {
                const response = await fetch('/notifications');
                const data = await response.json();
                
                if (data.notifications && data.notifications.length > 0) {
                    // Update stats
                    document.getElementById('total-count').textContent = data.total;
                    document.getElementById('last-updated').textContent = new Date().toLocaleTimeString();

                    // Check for new notifications
                    const latestId = data.notifications[0].id;
                    const hasNew = latestId > lastId;
                    
                    // Update UI
                    displayNotifications(data.notifications, hasNew && !isFirstLoad);
                    lastId = latestId;
                    isFirstLoad = false;

                    // Play sound for new notification (optional)
                    if (hasNew && !isFirstLoad) {
                        playNotificationSound();
                    }
                }
            } catch (error) {
                console.error('Error fetching notifications:', error);
            }
        }

        function displayNotifications(notifications, highlightNew) {
            const container = document.getElementById('notifications-container');
            
            if (notifications.length === 0) {
                return;
            }

            container.innerHTML = notifications.map((notif, index) => `
                <div class="notification ${index === 0 && highlightNew ? 'new' : ''}">
                    <div class="notification-header">
                        <div class="notification-title">
                            ${index === 0 && highlightNew ? '🆕 ' : ''}${notif.title || 'Notification'}
                        </div>
                        <div class="notification-time">
                            ID: ${notif.id} • ${new Date(notif.created_at).toLocaleString()}
                        </div>
                    </div>
                    <div class="notification-message">
                        ${notif.message || 'No message'}
                    </div>
                    <div class="notification-data">
                        ${JSON.stringify(notif.data, null, 2)}
                    </div>
                </div>
            `).join('');
        }

        function playNotificationSound() {
            // Create a subtle notification sound
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

        // Fetch immediately on load
        fetchNotifications();

        // Poll every 2 seconds for real-time updates
        setInterval(fetchNotifications, 2000);

        console.log('🚀 Real-time notification system active!');
        console.log('📡 Polling every 2 seconds for new webhooks');
    </script>
</body>
</html>
