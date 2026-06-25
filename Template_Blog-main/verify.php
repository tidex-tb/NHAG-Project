<?php

/**
 * NHAG Savings Platform - Environment & Setup Verification Script
 * Place this file in project root and visit: http://localhost/Template_Blog/verify.php
 * This script checks if your local environment is properly configured
 */

// Start with header
?>
<!DOCTYPE html>
<html>

<head>
    <title>NHAG System Verification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
            background: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .check {
            margin-bottom: 15px;
            padding: 15px;
            border-left: 4px solid #ddd;
        }

        .check.pass {
            background: #e8f5e9;
            border-left-color: #27ae60;
        }

        .check.fail {
            background: #ffebee;
            border-left-color: #e74c3c;
        }

        .check.warning {
            background: #fff3e0;
            border-left-color: #f39c12;
        }

        .icon {
            font-weight: bold;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>🏠 NHAG Savings Platform - System Verification</h2>
            <p class="text-muted">This page checks if your local environment is properly configured</p>
        </div>

        <?php
        $checks = array(
            'passed' => 0,
            'failed' => 0,
            'warnings' => 0
        );

        // ============================================
        // PHP VERSION CHECK
        // ============================================
        echo '<h5 class="mb-3">PHP Configuration</h5>';

        $php_version = phpversion();
        $php_required = '7.4';
        if (version_compare($php_version, $php_required, '>=')) {
            echo '<div class="check pass">';
            echo '<span class="icon">✓</span>';
            echo '<strong>PHP Version:</strong> ' . $php_version . ' <small class="text-muted">(Required: ≥' . $php_required . ')</small>';
            echo '</div>';
            $checks['passed']++;
        } else {
            echo '<div class="check fail">';
            echo '<span class="icon">✗</span>';
            echo '<strong>PHP Version:</strong> ' . $php_version . ' <small class="text-muted">(Required: ≥' . $php_required . ')</small>';
            echo '</div>';
            $checks['failed']++;
        }

        // ============================================
        // REQUIRED EXTENSIONS
        // ============================================
        $required_extensions = array('mysqli', 'pdo', 'curl', 'gd', 'json');
        foreach ($required_extensions as $ext) {
            if (extension_loaded($ext)) {
                echo '<div class="check pass">';
                echo '<span class="icon">✓</span>';
                echo '<strong>Extension:</strong> ' . strtoupper($ext) . ' loaded';
                echo '</div>';
                $checks['passed']++;
            } else {
                echo '<div class="check fail">';
                echo '<span class="icon">✗</span>';
                echo '<strong>Extension:</strong> ' . strtoupper($ext) . ' NOT loaded (required)';
                echo '</div>';
                $checks['failed']++;
            }
        }

        // ============================================
        // FILE & DIRECTORY PERMISSIONS
        // ============================================
        echo '<h5 class="mt-4 mb-3">Directories & Permissions</h5>';

        $directories = array(
            'application/logs' => 'Logs Directory',
            'uploads' => 'Uploads Directory',
            'assets' => 'Assets Directory'
        );

        foreach ($directories as $dir => $name) {
            if (is_dir($dir)) {
                if (is_writable($dir)) {
                    echo '<div class="check pass">';
                    echo '<span class="icon">✓</span>';
                    echo '<strong>' . $name . ':</strong> Writable (' . $dir . ')';
                    echo '</div>';
                    $checks['passed']++;
                } else {
                    echo '<div class="check warning">';
                    echo '<span class="icon">⚠</span>';
                    echo '<strong>' . $name . ':</strong> Not writable (' . $dir . ') - Please set permissions to 755';
                    echo '</div>';
                    $checks['warnings']++;
                }
            } else {
                echo '<div class="check fail">';
                echo '<span class="icon">✗</span>';
                echo '<strong>' . $name . ':</strong> Directory not found (' . $dir . ')';
                echo '</div>';
                $checks['failed']++;
            }
        }

        // ============================================
        // DATABASE CONNECTION
        // ============================================
        echo '<h5 class="mt-4 mb-3">Database Connection</h5>';

        try {
            $mysqli = @new mysqli('localhost', 'root', '', '');
            if ($mysqli->connect_error) {
                echo '<div class="check fail">';
                echo '<span class="icon">✗</span>';
                echo '<strong>MySQL Connection:</strong> Failed - ' . $mysqli->connect_error;
                echo '</div>';
                $checks['failed']++;
            } else {
                echo '<div class="check pass">';
                echo '<span class="icon">✓</span>';
                echo '<strong>MySQL Connection:</strong> Connected to localhost';
                echo '</div>';
                $checks['passed']++;

                // Check if database exists
                $db_result = $mysqli->query("SHOW DATABASES LIKE 'nhag_savings'");
                if ($db_result && $db_result->num_rows > 0) {
                    echo '<div class="check pass">';
                    echo '<span class="icon">✓</span>';
                    echo '<strong>Database:</strong> nhag_savings found';
                    echo '</div>';
                    $checks['passed']++;

                    // Check if tables exist
                    $mysqli->select_db('nhag_savings');
                    $tables_result = $mysqli->query("SHOW TABLES");
                    $table_count = $tables_result->num_rows;

                    if ($table_count >= 15) {
                        echo '<div class="check pass">';
                        echo '<span class="icon">✓</span>';
                        echo '<strong>Tables:</strong> ' . $table_count . ' tables found (Expected: 15+)';
                        echo '</div>';
                        $checks['passed']++;
                    } else {
                        echo '<div class="check warning">';
                        echo '<span class="icon">⚠</span>';
                        echo '<strong>Tables:</strong> Only ' . $table_count . ' tables found (Expected: 15+)';
                        echo '</div>';
                        $checks['warnings']++;
                    }
                } else {
                    echo '<div class="check fail">';
                    echo '<span class="icon">✗</span>';
                    echo '<strong>Database:</strong> nhag_savings NOT found';
                    echo '</div>';
                    $checks['failed']++;
                }

                $mysqli->close();
            }
        } catch (Exception $e) {
            echo '<div class="check fail">';
            echo '<span class="icon">✗</span>';
            echo '<strong>MySQL Connection:</strong> Error - ' . $e->getMessage();
            echo '</div>';
            $checks['failed']++;
        }

        // ============================================
        // CODEIGNITER CONFIGURATION
        // ============================================
        echo '<h5 class="mt-4 mb-3">CodeIgniter Configuration</h5>';

        if (file_exists('application/config/config.php')) {
            echo '<div class="check pass">';
            echo '<span class="icon">✓</span>';
            echo '<strong>Config File:</strong> Found (application/config/config.php)';
            echo '</div>';
            $checks['passed']++;
        } else {
            echo '<div class="check fail">';
            echo '<span class="icon">✗</span>';
            echo '<strong>Config File:</strong> Not found (application/config/config.php)';
            echo '</div>';
            $checks['failed']++;
        }

        if (file_exists('application/config/database.php')) {
            echo '<div class="check pass">';
            echo '<span class="icon">✓</span>';
            echo '<strong>Database Config:</strong> Found (application/config/database.php)';
            echo '</div>';
            $checks['passed']++;
        } else {
            echo '<div class="check fail">';
            echo '<span class="icon">✗</span>';
            echo '<strong>Database Config:</strong> Not found (application/config/database.php)';
            echo '</div>';
            $checks['failed']++;
        }

        if (file_exists('.htaccess')) {
            echo '<div class="check pass">';
            echo '<span class="icon">✓</span>';
            echo '<strong>Apache Config:</strong> .htaccess found (URL rewriting ready)';
            echo '</div>';
            $checks['passed']++;
        } else {
            echo '<div class="check warning">';
            echo '<span class="icon">⚠</span>';
            echo '<strong>Apache Config:</strong> .htaccess not found (URLs may include index.php)';
            echo '</div>';
            $checks['warnings']++;
        }

        // ============================================
        // SUMMARY
        // ============================================
        echo '<div style="margin-top: 30px; padding-top: 30px; border-top: 2px solid #eee;">';
        echo '<h5>Summary</h5>';

        $total = $checks['passed'] + $checks['failed'] + $checks['warnings'];
        $percentage = ($checks['passed'] / $total) * 100;

        echo '<div class="row">';
        echo '<div class="col-md-4">';
        echo '<div class="text-center p-3" style="background: #e8f5e9; border-radius: 8px;">';
        echo '<h3 style="color: #27ae60;">' . $checks['passed'] . '</h3>';
        echo '<p class="text-muted">Passed</p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="col-md-4">';
        echo '<div class="text-center p-3" style="background: #fff3e0; border-radius: 8px;">';
        echo '<h3 style="color: #f39c12;">' . $checks['warnings'] . '</h3>';
        echo '<p class="text-muted">Warnings</p>';
        echo '</div>';
        echo '</div>';

        echo '<div class="col-md-4">';
        echo '<div class="text-center p-3" style="background: #ffebee; border-radius: 8px;">';
        echo '<h3 style="color: #e74c3c;">' . $checks['failed'] . '</h3>';
        echo '<p class="text-muted">Failed</p>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '<div class="mt-3 p-3" style="background: #f5f5f5; border-radius: 8px;">';
        echo '<strong>Overall Status:</strong> ';

        if ($checks['failed'] === 0) {
            echo '<span class="badge badge-success" style="font-size: 1rem;">✓ READY</span>';
            echo '<p class="mt-2 text-muted">Your environment is properly configured. You can proceed with testing.</p>';
        } else {
            echo '<span class="badge badge-danger" style="font-size: 1rem;">✗ ISSUES FOUND</span>';
            echo '<p class="mt-2 text-muted">Please fix the failed items before proceeding.</p>';
        }

        if ($checks['warnings'] > 0) {
            echo '<p class="mt-2 text-muted"><strong>Warnings:</strong> ' . $checks['warnings'] . ' warning(s) - these should be addressed for production.</p>';
        }

        echo '</div>';

        echo '</div>';
        ?>

        <div style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
            <h6>Next Steps</h6>
            <ol>
                <li>Fix any <strong>Failed</strong> checks above</li>
                <li>Address any <strong>Warnings</strong> for production readiness</li>
                <li>Follow the setup guide in <strong>LOCAL_TESTING_SETUP.md</strong></li>
                <li>Test the system by registering and logging in</li>
            </ol>
        </div>

        <div style="margin-top: 20px; text-align: center; color: #999; font-size: 0.9rem;">
            <p>NHAG Savings Management Platform | System Verification v1.0</p>
            <p><strong>Note:</strong> Delete this file (verify.php) before deploying to production.</p>
        </div>
    </div>
</body>

</html>