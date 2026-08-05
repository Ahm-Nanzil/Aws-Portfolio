<?php
// Configuration Editor - index.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// File paths
$trackingFile = 'tracking.json';
$emailConfigFile = 'emailconfiguration.php';

// Handle GET request for viewing log
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'view_log') {
    $logFile = '/var/log/email_cron.log';
    if (file_exists($logFile)) {
        header('Content-Type: text/plain');
        header('Content-Disposition: inline; filename="email_cron.log"');
        readfile($logFile);
    } else {
        header('Content-Type: text/plain');
        echo "Log file not found at: $logFile";
    }
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success = false;
    $error = '';
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_tracking':
                $lastSentIndex = (int)$_POST['last_sent_index'];
                $totalSent = (int)$_POST['total_sent'];
                
                $trackingData = [
                    'last_sent_index' => $lastSentIndex,
                    'total_sent' => $totalSent
                ];
                
                if (file_put_contents($trackingFile, json_encode($trackingData, JSON_PRETTY_PRINT))) {
                    $success = true;
                    $message = 'Tracking configuration updated successfully!';
                } else {
                    $error = 'Failed to update tracking configuration.';
                }
                break;
                
            case 'update_email':
                $emailConfig = [
                    'smtp_host' => $_POST['smtp_host'],
                    'smtp_port' => (int)$_POST['smtp_port'],
                    'smtp_username' => $_POST['smtp_username'],
                    'smtp_password' => $_POST['smtp_password'],
                    'from_email' => $_POST['from_email'],
                    'from_name' => $_POST['from_name'],
                    'is_smtp' => isset($_POST['is_smtp']),
                    'smtp_secure' => $_POST['smtp_secure']
                ];
                
                $phpContent = "<?php\nreturn " . var_export($emailConfig, true) . ";\n";
                
                if (file_put_contents($emailConfigFile, $phpContent)) {
                    $success = true;
                    $message = 'Email configuration updated successfully!';
                } else {
                    $error = 'Failed to update email configuration.';
                }
                break;
        }
    }
}

// Load current configurations
$trackingData = ['last_sent_index' => 0, 'total_sent' => 0];
if (file_exists($trackingFile)) {
    $trackingJson = file_get_contents($trackingFile);
    $trackingData = json_decode($trackingJson, true) ?: $trackingData;
}

$emailConfig = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => '',
    'smtp_password' => '',
    'from_email' => '',
    'from_name' => 'Webs',
    'is_smtp' => true,
    'smtp_secure' => 'tls'
];

if (file_exists($emailConfigFile)) {
    $loadedConfig = include $emailConfigFile;
    if (is_array($loadedConfig)) {
        $emailConfig = array_merge($emailConfig, $loadedConfig);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Editor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
        }
        
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .card h2::before {
            content: '';
            width: 4px;
            height: 24px;
            background: linear-gradient(45deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .btn {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .file-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .file-info h3 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .file-info pre {
            background: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .cards-container {
                grid-template-columns: 1fr;
            }
            
            .card {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔧 Configuration Editor</h1>
            <p>Manage your tracking and email configurations</p>
        </div>
        
        <?php if (isset($success) && $success): ?>
            <div class="alert alert-success">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-error">
                ❌ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="cards-container">
            <!-- Tracking Configuration Card -->
            <div class="card">
                <h2>📊 Tracking Configuration</h2>
                
                <div class="file-info">
                    <h3>Current tracking.json:</h3>
                    <pre><?php echo htmlspecialchars(json_encode($trackingData, JSON_PRETTY_PRINT)); ?></pre>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="action" value="update_tracking">
                    
                    <div class="form-group">
                        <label for="last_sent_index">Last Sent Index:</label>
                        <input type="number" id="last_sent_index" name="last_sent_index" 
                               value="<?php echo htmlspecialchars($trackingData['last_sent_index']); ?>" 
                               min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="total_sent">Total Sent:</label>
                        <input type="number" id="total_sent" name="total_sent" 
                               value="<?php echo htmlspecialchars($trackingData['total_sent']); ?>" 
                               min="0" required>
                    </div>
                    
                    <button type="submit" class="btn">Update Tracking Config</button>
                </form>
                
                <div class="file-info">
                    <h3>Email System Logs</h3>
                    <div style="margin-top: 10px;">
                        <a href="?action=view_log" target="_blank" class="btn">
                            View Full Log
                        </a>
                    </div>
                    <div style="margin-top: 15px;">
                        <h4>Last 5 Log Entries:</h4>
                        <pre style="max-height: 200px; overflow-y: auto;"><?php
                            $logFile = '/var/log/email_cron.log';
                            if (file_exists($logFile)) {
                                $logContent = file_get_contents($logFile);
                                $lines = explode("\n", trim($logContent));
                                $lastLines = array_slice($lines, -5);
                                echo htmlspecialchars(implode("\n", $lastLines));
                            } else {
                                echo "Log file not found";
                            }
                        ?></pre>
                    </div>
                </div>
            </div>
            
            <!-- Email Configuration Card -->
            <div class="card">
                <h2>📧 Email Configuration</h2>
                
                <div class="file-info">
                    <h3>Current emailconfiguration.php:</h3>
                    <pre><?php echo htmlspecialchars("<?php\nreturn " . var_export($emailConfig, true) . ";\n"); ?></pre>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="action" value="update_email">
                    
                    <div class="form-group">
                        <label for="smtp_host">SMTP Host:</label>
                        <input type="text" id="smtp_host" name="smtp_host" 
                               value="<?php echo htmlspecialchars($emailConfig['smtp_host']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_port">SMTP Port:</label>
                        <input type="number" id="smtp_port" name="smtp_port" 
                               value="<?php echo htmlspecialchars($emailConfig['smtp_port']); ?>" 
                               min="1" max="65535" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_username">SMTP Username:</label>
                        <input type="email" id="smtp_username" name="smtp_username" 
                               value="<?php echo htmlspecialchars($emailConfig['smtp_username']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_password">SMTP Password:</label>
                        <input type="password" id="smtp_password" name="smtp_password" 
                               value="<?php echo htmlspecialchars($emailConfig['smtp_password']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="from_email">From Email:</label>
                        <input type="email" id="from_email" name="from_email" 
                               value="<?php echo htmlspecialchars($emailConfig['from_email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="from_name">From Name:</label>
                        <input type="text" id="from_name" name="from_name" 
                               value="<?php echo htmlspecialchars($emailConfig['from_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_secure">SMTP Security:</label>
                        <select id="smtp_secure" name="smtp_secure" required>
                            <option value="tls" <?php echo $emailConfig['smtp_secure'] === 'tls' ? 'selected' : ''; ?>>TLS</option>
                            <option value="ssl" <?php echo $emailConfig['smtp_secure'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                            <option value="" <?php echo $emailConfig['smtp_secure'] === '' ? 'selected' : ''; ?>>None</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <div class="checkbox-group">
                            <input type="checkbox" id="is_smtp" name="is_smtp" 
                                   <?php echo $emailConfig['is_smtp'] ? 'checked' : ''; ?>>
                            <label for="is_smtp">Enable SMTP</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Update Email Config</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>