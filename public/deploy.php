<?php
/**
 * Laravel Deployment Script
 * Run this file directly in your browser to deploy your Laravel application
 * without SSH access
 */

// Security check - only allow from specific IPs or with a secret key
$allowed_ips = ['127.0.0.1', '::1']; // Add your IP here
$secret_key = 'your-secret-deployment-key-2024'; // Change this to a secure key

// Check if request is from allowed IP or has correct secret key
$client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
$requested_key = $_GET['key'] ?? '';

if (!in_array($client_ip, $allowed_ips) && $requested_key !== $secret_key) {
    http_response_code(403);
    die('Access denied. Please provide correct key parameter or access from allowed IP.');
}

// Set execution time limit
set_time_limit(300); // 5 minutes

// Function to run command and capture output
function runCommand($command) {
    $output = [];
    $return_var = 0;
    exec($command . ' 2>&1', $output, $return_var);
    return [
        'command' => $command,
        'output' => $output,
        'success' => $return_var === 0,
        'return_code' => $return_var
    ];
}

// Function to check if command exists
function commandExists($command) {
    $output = [];
    $return_var = 0;
    exec("which $command 2>/dev/null", $output, $return_var);
    return $return_var === 0;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Deployment</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .step { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
        .btn-warning { background: #ffc107; color: #212529; }
        h1, h2 { color: #333; }
        .status { font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Laravel Deployment Script</h1>
        <p><strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <?php
            $action = $_POST['action'] ?? '';
            $results = [];

            switch ($action) {
                case 'full_deploy':
                    // Step 1: Check PHP version
                    $php_version = PHP_VERSION;
                    $results[] = [
                        'title' => 'PHP Version Check',
                        'status' => version_compare($php_version, '8.2.0', '>=') ? 'success' : 'error',
                        'message' => "PHP Version: $php_version " . (version_compare($php_version, '8.2.0', '>=') ? '✅' : '❌ (Requires 8.2+)'),
                        'output' => []
                    ];

                    // Step 2: Check if we're in the right directory and find Laravel root
                    $current_dir = getcwd();
                    $laravel_root = $current_dir;

                    // If we're in public directory, go up one level to find Laravel root
                    if (basename($current_dir) === 'public' || strpos($current_dir, '/public') !== false) {
                        $laravel_root = dirname($current_dir);
                    }

                    // Check if Laravel files exist in the root directory
                    $artisan_path = $laravel_root . '/artisan';
                    $composer_path = $laravel_root . '/composer.json';
                    $laravel_check = file_exists($artisan_path) && file_exists($composer_path);

                    $results[] = [
                        'title' => 'Laravel Project Check',
                        'status' => $laravel_check ? 'success' : 'error',
                        'message' => $laravel_check ? '✅ Laravel project detected' : '❌ Laravel project not found',
                        'output' => [
                            "Current directory: $current_dir",
                            "Laravel root: $laravel_root",
                            "Artisan file: " . (file_exists($artisan_path) ? '✅ Found' : '❌ Missing'),
                            "Composer file: " . (file_exists($composer_path) ? '✅ Found' : '❌ Missing')
                        ]
                    ];

                    if ($laravel_check) {
                        // Change to Laravel root directory for all commands
                        chdir($laravel_root);

                        // Step 3: Install/Update Composer Dependencies
                        $composer_result = runCommand('composer install --no-dev --optimize-autoloader');
                        $results[] = [
                            'title' => 'Composer Dependencies',
                            'status' => $composer_result['success'] ? 'success' : 'error',
                            'message' => $composer_result['success'] ? '✅ Dependencies installed' : '❌ Composer install failed',
                            'output' => $composer_result['output']
                        ];

                        // Step 4: Generate Application Key
                        $key_result = runCommand('php artisan key:generate --force');
                        $results[] = [
                            'title' => 'Application Key',
                            'status' => $key_result['success'] ? 'success' : 'warning',
                            'message' => $key_result['success'] ? '✅ Application key generated' : '⚠️ Key generation failed (may already exist)',
                            'output' => $key_result['output']
                        ];

                        // Step 5: Clear and Cache Configuration
                        $cache_commands = [
                            'php artisan config:clear',
                            'php artisan cache:clear',
                            'php artisan route:clear',
                            'php artisan view:clear',
                            'php artisan config:cache',
                            'php artisan route:cache',
                            'php artisan view:cache'
                        ];

                        foreach ($cache_commands as $cmd) {
                            $result = runCommand($cmd);
                            $results[] = [
                                'title' => 'Cache: ' . explode(':', $cmd)[1],
                                'status' => $result['success'] ? 'success' : 'warning',
                                'message' => $result['success'] ? '✅ Success' : '⚠️ Warning',
                                'output' => $result['output']
                            ];
                        }

                        // Step 6: Run Migrations
                        $migrate_result = runCommand('php artisan migrate --force');
                        $results[] = [
                            'title' => 'Database Migrations',
                            'status' => $migrate_result['success'] ? 'success' : 'error',
                            'message' => $migrate_result['success'] ? '✅ Migrations completed' : '❌ Migration failed',
                            'output' => $migrate_result['output']
                        ];

                        // Step 7: Create Storage Link
                        $link_result = runCommand('php artisan storage:link');
                        $results[] = [
                            'title' => 'Storage Link',
                            'status' => $link_result['success'] ? 'success' : 'warning',
                            'message' => $link_result['success'] ? '✅ Storage linked' : '⚠️ Link may already exist',
                            'output' => $link_result['output']
                        ];

                        // Step 8: Set Permissions (if possible)
                        $permissions = [
                            'chmod -R 755 storage',
                            'chmod -R 755 bootstrap/cache'
                        ];

                        foreach ($permissions as $perm_cmd) {
                            $perm_result = runCommand($perm_cmd);
                            $results[] = [
                                'title' => 'Permissions: ' . explode(' ', $perm_cmd)[2],
                                'status' => $perm_result['success'] ? 'success' : 'warning',
                                'message' => $perm_result['success'] ? '✅ Permissions set' : '⚠️ Permission setting failed (may not be needed)',
                                'output' => $perm_result['output']
                            ];
                        }

                        // Step 9: Run Seeders (optional)
                        if (isset($_POST['run_seeders'])) {
                            $seed_result = runCommand('php artisan db:seed --force');
                            $results[] = [
                                'title' => 'Database Seeders',
                                'status' => $seed_result['success'] ? 'success' : 'error',
                                'message' => $seed_result['success'] ? '✅ Seeders completed' : '❌ Seeding failed',
                                'output' => $seed_result['output']
                            ];
                        }
                    }
                    break;

                case 'migrate_only':
                    // Find Laravel root directory
                    $current_dir = getcwd();
                    $laravel_root = $current_dir;
                    if (basename($current_dir) === 'public' || strpos($current_dir, '/public') !== false) {
                        $laravel_root = dirname($current_dir);
                    }

                    if (file_exists($laravel_root . '/artisan')) {
                        chdir($laravel_root);
                        $migrate_result = runCommand('php artisan migrate --force');
                        $results[] = [
                            'title' => 'Database Migrations Only',
                            'status' => $migrate_result['success'] ? 'success' : 'error',
                            'message' => $migrate_result['success'] ? '✅ Migrations completed' : '❌ Migration failed',
                            'output' => $migrate_result['output']
                        ];
                    } else {
                        $results[] = [
                            'title' => 'Database Migrations Only',
                            'status' => 'error',
                            'message' => '❌ Laravel project not found',
                            'output' => ['Could not find artisan file in: ' . $laravel_root]
                        ];
                    }
                    break;

                case 'storage_link':
                    // Find Laravel root directory
                    $current_dir = getcwd();
                    $laravel_root = $current_dir;
                    if (basename($current_dir) === 'public' || strpos($current_dir, '/public') !== false) {
                        $laravel_root = dirname($current_dir);
                    }

                    if (file_exists($laravel_root . '/artisan')) {
                        chdir($laravel_root);
                        $link_result = runCommand('php artisan storage:link');
                        $results[] = [
                            'title' => 'Storage Link Only',
                            'status' => $link_result['success'] ? 'success' : 'warning',
                            'message' => $link_result['success'] ? '✅ Storage linked' : '⚠️ Link may already exist',
                            'output' => $link_result['output']
                        ];
                    } else {
                        $results[] = [
                            'title' => 'Storage Link Only',
                            'status' => 'error',
                            'message' => '❌ Laravel project not found',
                            'output' => ['Could not find artisan file in: ' . $laravel_root]
                        ];
                    }
                    break;

                case 'clear_cache':
                    // Find Laravel root directory
                    $current_dir = getcwd();
                    $laravel_root = $current_dir;
                    if (basename($current_dir) === 'public' || strpos($current_dir, '/public') !== false) {
                        $laravel_root = dirname($current_dir);
                    }

                    if (file_exists($laravel_root . '/artisan')) {
                        chdir($laravel_root);
                        $cache_commands = [
                            'php artisan config:clear',
                            'php artisan cache:clear',
                            'php artisan route:clear',
                            'php artisan view:clear'
                        ];

                        foreach ($cache_commands as $cmd) {
                            $result = runCommand($cmd);
                            $results[] = [
                                'title' => 'Clear: ' . explode(':', $cmd)[1],
                                'status' => $result['success'] ? 'success' : 'warning',
                                'message' => $result['success'] ? '✅ Cleared' : '⚠️ Warning',
                                'output' => $result['output']
                            ];
                        }
                    } else {
                        $results[] = [
                            'title' => 'Clear Cache',
                            'status' => 'error',
                            'message' => '❌ Laravel project not found',
                            'output' => ['Could not find artisan file in: ' . $laravel_root]
                        ];
                    }
                    break;
            }
            ?>

            <h2>Deployment Results</h2>
            <?php foreach ($results as $result): ?>
                <div class="step <?php echo $result['status']; ?>">
                    <h3><?php echo $result['title']; ?></h3>
                    <p class="status"><?php echo $result['message']; ?></p>
                    <?php if (!empty($result['output'])): ?>
                        <pre><?php echo htmlspecialchars(implode("\n", $result['output'])); ?></pre>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="step info">
                <h3>Next Steps</h3>
                <p>✅ Deployment completed! Your Laravel application should now be working.</p>
                <p><strong>Important:</strong> Delete this deployment script after successful deployment for security.</p>
                <a href="?key=<?php echo $secret_key; ?>" class="btn">Run Deployment Again</a>
                <a href="/" class="btn btn-success">Visit Your Site</a>
            </div>

        <?php else: ?>
            <div class="step info">
                <h3>⚠️ Security Notice</h3>
                <p>This deployment script should be deleted after successful deployment for security reasons.</p>
            </div>

            <h2>Deployment Options</h2>

            <form method="POST" style="margin: 20px 0;">
                <input type="hidden" name="action" value="full_deploy">
                <div class="step">
                    <h3>🚀 Full Deployment</h3>
                    <p>Complete deployment including dependencies, migrations, cache optimization, and storage linking.</p>
                    <label>
                        <input type="checkbox" name="run_seeders" value="1"> Also run database seeders
                    </label><br><br>
                    <button type="submit" class="btn btn-success">Run Full Deployment</button>
                </div>
            </form>

            <form method="POST" style="margin: 20px 0;">
                <input type="hidden" name="action" value="migrate_only">
                <div class="step">
                    <h3>🗄️ Migrations Only</h3>
                    <p>Run database migrations only.</p>
                    <button type="submit" class="btn">Run Migrations</button>
                </div>
            </form>

            <form method="POST" style="margin: 20px 0;">
                <input type="hidden" name="action" value="storage_link">
                <div class="step">
                    <h3>🔗 Storage Link Only</h3>
                    <p>Create storage symbolic link only.</p>
                    <button type="submit" class="btn">Create Storage Link</button>
                </div>
            </form>

            <form method="POST" style="margin: 20px 0;">
                <input type="hidden" name="action" value="clear_cache">
                <div class="step">
                    <h3>🧹 Clear Cache Only</h3>
                    <p>Clear all Laravel caches.</p>
                    <button type="submit" class="btn btn-warning">Clear Cache</button>
                </div>
            </form>

            <div class="step">
                <h3>📋 System Information</h3>
                <?php
                $current_dir = getcwd();
                $laravel_root = $current_dir;
                if (basename($current_dir) === 'public' || strpos($current_dir, '/public') !== false) {
                    $laravel_root = dirname($current_dir);
                }
                $artisan_exists = file_exists($laravel_root . '/artisan');
                $composer_exists = file_exists($laravel_root . '/composer.json');
                ?>
                <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
                <p><strong>Current Directory:</strong> <?php echo $current_dir; ?></p>
                <p><strong>Laravel Root:</strong> <?php echo $laravel_root; ?></p>
                <p><strong>Laravel Detected:</strong> <?php echo ($artisan_exists && $composer_exists) ? '✅ Yes' : '❌ No'; ?></p>
                <p><strong>Composer Available:</strong> <?php echo commandExists('composer') ? '✅ Yes' : '❌ No'; ?></p>
                <p><strong>Artisan Available:</strong> <?php echo $artisan_exists ? '✅ Yes' : '❌ No'; ?></p>
                <p><strong>Composer.json Available:</strong> <?php echo $composer_exists ? '✅ Yes' : '❌ No'; ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
