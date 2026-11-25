<?php
// Prevent error suppression for diagnostics
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Central database config
require_once __DIR__ . '/includes/config.php';

$connection_status = "UNKNOWN";
$error_message = "";
$tables = [];

try {
    if (isset($dbh) && $dbh instanceof PDO) {
        $connection_status = "SUCCESS";
        $stmt = $dbh->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } else {
        $connection_status = "FAILED";
        $error_message = "PDO object ($dbh) not initialized.";
    }
} catch (Exception $e) {
    $connection_status = "FAILED";
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Diagnostic - Car Rental</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f9; padding: 40px 15px; font-family: system-ui, sans-serif; }
        .card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: none; }
        .status-badge { font-size: 1.1rem; padding: 6px 16px; border-radius: 20px; }
    </style>
</head>
<body>
<div class="container" style="max-width: 750px;">
    <div class="card p-4">
        <h3 class="mb-3">Database Connection Diagnostic</h3>
        <p class="text-muted">Use this page to verify if Render is successfully connected to your Aiven MySQL database.</p>
        
        <hr>

        <h5>Connection Status:</h5>
        <?php if ($connection_status === 'SUCCESS'): ?>
            <div class="alert alert-success d-flex align-items-center">
                <span class="badge bg-success status-badge me-3">CONNECTED</span>
                <div><strong>Success!</strong> Successfully connected to MySQL database on Aiven.</div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <div class="d-flex align-items-center mb-2">
                    <span class="badge bg-danger status-badge me-3">FAILED</span>
                    <strong>Connection Failed!</strong>
                </div>
                <div class="small bg-white p-2 border rounded mt-2 text-danger font-monospace">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            </div>
        <?php endif; ?>

        <h5 class="mt-4">Configuration Loaded:</h5>
        <table class="table table-bordered table-sm bg-white">
            <tbody>
                <tr>
                    <th width="30%">Host (DB_HOST)</th>
                    <td><code><?= htmlspecialchars(defined('DB_HOST') ? DB_HOST : 'NOT SET'); ?></code></td>
                </tr>
                <tr>
                    <th>Port (DB_PORT)</th>
                    <td><code><?= htmlspecialchars(defined('DB_PORT') ? (string)DB_PORT : '3306'); ?></code></td>
                </tr>
                <tr>
                    <th>Database (DB_NAME)</th>
                    <td><code><?= htmlspecialchars(defined('DB_NAME') ? DB_NAME : 'NOT SET'); ?></code></td>
                </tr>
                <tr>
                    <th>User (DB_USER)</th>
                    <td><code><?= htmlspecialchars(defined('DB_USER') ? DB_USER : 'NOT SET'); ?></code></td>
                </tr>
                <tr>
                    <th>Password (DB_PASS)</th>
                    <td><code><?= defined('DB_PASS') && strlen(DB_PASS) > 0 ? str_repeat('*', min(strlen(DB_PASS), 12)) : '<span class="text-danger">EMPTY</span>'; ?></code></td>
                </tr>
            </tbody>
        </table>

        <h5 class="mt-4">Database Tables (<?= count($tables); ?> found):</h5>
        <?php if (count($tables) > 0): ?>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <?php foreach ($tables as $t): ?>
                    <span class="badge bg-secondary p-2"><?= htmlspecialchars($t); ?></span>
                <?php endforeach; ?>
            </div>
            <div class="alert alert-info py-2">
                All tables are available. You can visit the <a href="index.php" class="fw-bold">Website Home</a> or <a href="admin/index.php" class="fw-bold">Admin Panel</a>.
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <strong>No tables found!</strong> The database is connected, but the tables have not been imported yet.
                <br>
                <a href="import_db.php" class="btn btn-warning btn-sm mt-2 fw-bold">Click here to Import Tables (import_db.php)</a>
            </div>
        <?php endif; ?>

        <div class="mt-3 text-end">
            <a href="index.php" class="btn btn-primary">Go to Home Page</a>
        </div>
    </div>
</div>
</body>
</html>
