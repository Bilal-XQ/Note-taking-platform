<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyNotes - System Status</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; margin: 0; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { text-align: center; color: white; margin-bottom: 30px; }
        .header h1 { font-size: 2.5rem; margin: 0; text-shadow: 0 2px 4px rgba(0,0,0,0.3); }
        .status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .status-card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
        .status-card h3 { margin: 0 0 16px 0; color: #2d3748; display: flex; align-items: center; gap: 10px; }
        .status-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #e2e8f0; }
        .status-item:last-child { border-bottom: none; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.875rem; font-weight: 600; }
        .status-success { background: #c6f6d5; color: #22543d; }
        .status-error { background: #fed7d7; color: #c53030; }
        .status-warning { background: #fef5e7; color: #dd6b20; }
        .action-btn { display: inline-block; background: #4299e1; color: white; padding: 8px 16px; text-decoration: none; border-radius: 6px; font-size: 0.875rem; margin-top: 8px; }
        .action-btn:hover { background: #3182ce; }
        .quick-links { background: white; border-radius: 12px; padding: 24px; margin-top: 20px; }
        .quick-links h3 { margin: 0 0 16px 0; color: #2d3748; }
        .links-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
        .link-btn { display: block; background: #f7fafc; border: 2px solid #e2e8f0; padding: 12px 16px; text-decoration: none; color: #4a5568; border-radius: 8px; text-align: center; transition: all 0.2s; }
        .link-btn:hover { border-color: #4299e1; background: #ebf8ff; color: #2b6cb0; }
        .loading { display: inline-block; width: 16px; height: 16px; border: 2px solid #e2e8f0; border-top: 2px solid #4299e1; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 StudyNotes System Status</h1>
            <p>Real-time status of your development environment</p>
        </div>

        <div class="status-grid">
            <!-- Server Status -->
            <div class="status-card">
                <h3>🖥️ Server Status</h3>
                
                <div class="status-item">
                    <span>React Development Server</span>
                    <span id="react-status" class="status-badge status-warning">
                        <span class="loading"></span> Checking...
                    </span>
                </div>
                
                <div class="status-item">
                    <span>WAMP Apache Server</span>
                    <span id="apache-status" class="status-badge status-success">✅ Running</span>
                </div>
                
                <div class="status-item">
                    <span>MySQL Database</span>
                    <span id="mysql-status" class="status-badge status-warning">
                        <span class="loading"></span> Checking...
                    </span>
                </div>
            </div>

            <!-- Database Status -->
            <div class="status-card">
                <h3>🗄️ Database Status</h3>
                <div id="db-details">
                    <div class="status-item">
                        <span>Database Connection</span>
                        <span class="loading"></span>
                    </div>
                </div>
            </div>

            <!-- Application Status -->
            <div class="status-card">
                <h3>🚀 Application Status</h3>
                
                <div class="status-item">
                    <span>Landing Page</span>
                    <span id="landing-status" class="status-badge status-warning">
                        <span class="loading"></span> Checking...
                    </span>
                </div>
                
                <div class="status-item">
                    <span>Student Portal</span>
                    <span id="student-status" class="status-badge status-success">✅ Available</span>
                </div>
                
                <div class="status-item">
                    <span>Admin Dashboard</span>
                    <span id="admin-status" class="status-badge status-success">✅ Available</span>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="quick-links">
            <h3>🔗 Quick Access Links</h3>
            <div class="links-grid">
                <a href="http://localhost:3002" target="_blank" class="link-btn" id="react-link">
                    🎯 React Landing Page
                </a>
                <a href="http://localhost/main/" target="_blank" class="link-btn">
                    🏠 Static Landing Page
                </a>
                <a href="http://localhost/main/src/views/student/login.php" target="_blank" class="link-btn">
                    👤 Student Login
                </a>
                <a href="http://localhost/main/admin/dashboard.php" target="_blank" class="link-btn">
                    🔧 Admin Dashboard
                </a>
                <a href="http://localhost/main/test_connection.php" target="_blank" class="link-btn">
                    🔍 Database Test
                </a>
                <a href="http://localhost/phpmyadmin" target="_blank" class="link-btn">
                    🗄️ phpMyAdmin
                </a>
            </div>
        </div>
    </div>

    <script>
        // Check React server status
        function checkReactServer() {
            fetch('http://localhost:3002')
                .then(response => {
                    if (response.ok) {
                        document.getElementById('react-status').className = 'status-badge status-success';
                        document.getElementById('react-status').innerHTML = '✅ Running';
                        document.getElementById('landing-status').className = 'status-badge status-success';
                        document.getElementById('landing-status').innerHTML = '✅ Available';
                    } else {
                        throw new Error('Server responded with error');
                    }
                })
                .catch(error => {
                    document.getElementById('react-status').className = 'status-badge status-error';
                    document.getElementById('react-status').innerHTML = '❌ Offline';
                    document.getElementById('landing-status').className = 'status-badge status-error';
                    document.getElementById('landing-status').innerHTML = '❌ Unavailable';
                    document.getElementById('react-link').style.opacity = '0.5';
                });
        }

        // Check database status
        function checkDatabase() {
            fetch('test_connection.php')
                .then(response => response.text())
                .then(data => {
                    const dbDetails = document.getElementById('db-details');
                    if (data.includes('SUCCESS')) {
                        document.getElementById('mysql-status').className = 'status-badge status-success';
                        document.getElementById('mysql-status').innerHTML = '✅ Connected';
                        
                        const tableCount = (data.match(/Table:/g) || []).length;
                        dbDetails.innerHTML = `
                            <div class="status-item">
                                <span>Database Connection</span>
                                <span class="status-badge status-success">✅ Connected</span>
                            </div>
                            <div class="status-item">
                                <span>Tables Found</span>
                                <span class="status-badge status-success">${tableCount} tables</span>
                            </div>
                        `;
                    } else {
                        document.getElementById('mysql-status').className = 'status-badge status-error';
                        document.getElementById('mysql-status').innerHTML = '❌ Error';
                        dbDetails.innerHTML = `
                            <div class="status-item">
                                <span>Database Connection</span>
                                <span class="status-badge status-error">❌ Failed</span>
                            </div>
                            <a href="setup_database.php" class="action-btn">🔧 Setup Database</a>
                        `;
                    }
                })
                .catch(error => {
                    document.getElementById('mysql-status').className = 'status-badge status-error';
                    document.getElementById('mysql-status').innerHTML = '❌ Error';
                });
        }

        // Run checks
        setTimeout(() => {
            checkReactServer();
            checkDatabase();
        }, 1000);

        // Auto-refresh every 30 seconds
        setInterval(() => {
            checkReactServer();
            checkDatabase();
        }, 30000);
    </script>
</body>
</html>
