<?php
// Prevent timeout for large database imports
set_time_limit(300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/includes/config.php';

$sql_file = __DIR__ . '/sql/carrental.sql';
$messages = [];
$status = "IDLE";
$executed_queries = 0;

if (isset($_POST['import'])) {
    if (!file_exists($sql_file)) {
        $status = "ERROR";
        $messages[] = "SQL file not found at: " . htmlspecialchars($sql_file);
    } else {
        try {
            $sql_content = file_get_contents($sql_file);
            
            // Temporary disable foreign key checks and autocommit
            $dbh->exec("SET foreign_key_checks = 0;");
            
            // Split SQL into individual statements
            // Remove comments and split by semicolon
            $lines = explode("\n", $sql_content);
            $clean_sql = "";
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (substr($trimmed, 0, 2) === '--' || substr($trimmed, 0, 2) === '/*' || $trimmed === '') {
                    continue;
                }
                $clean_sql .= $line . "\n";
            }

            $queries = explode(";\n", $clean_sql);
            foreach ($queries as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $dbh->exec($query);
                    $executed_queries++;
                }
            }

            $dbh->exec("SET foreign_key_checks = 1;");
            $status = "SUCCESS";
            $messages[] = "Successfully executed $executed_queries SQL statements!";
            $messages[] = "Tables created and seed data inserted.";
        } catch (Exception $e) {
            $status = "ERROR";
            $messages[] = "Import Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Database to Aiven MySQL</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #f4f6f9; padding: 40px 15px; font-family: system-ui, sans-serif; }
        .card { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border: none; }
    </style>
</head>
<body>
<div class="container" style="max-width: 700px;">
    <div class="card p-4">
        <h3 class="mb-3">Aiven MySQL Database Importer</h3>
        <p class="text-muted">Import the <code>sql/carrental.sql</code> database schema and data into your Aiven MySQL database automatically.</p>
        
        <hr>

        <?php if ($status === 'SUCCESS'): ?>
            <div class="alert alert-success">
                <h5>🎉 Database Imported Successfully!</h5>
                <?php foreach ($messages as $msg): ?>
                    <div><?= htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex gap-2 mt-3">
                    <a href="test_db.php" class="btn btn-outline-success">Check Diagnostics</a>
                    <a href="index.php" class="btn btn-success">Open Website</a>
                    <a href="admin/index.php" class="btn btn-dark">Open Admin Panel</a>
                </div>
            </div>
            <div class="alert alert-warning small mt-3">
                <strong>Security Note:</strong> After confirming your site works, please delete or rename <code>import_db.php</code> from your repository.
            </div>
        <?php elseif ($status === 'ERROR'): ?>
            <div class="alert alert-danger">
                <h5>⚠️ Import Failed</h5>
                <?php foreach ($messages as $msg): ?>
                    <div class="font-monospace small"><?= htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($status !== 'SUCCESS'): ?>
            <div class="alert alert-secondary">
                <strong>Target Database:</strong> <code><?= htmlspecialchars(defined('DB_NAME') ? DB_NAME : 'carrental'); ?></code> on <code><?= htmlspecialchars(defined('DB_HOST') ? DB_HOST : 'localhost'); ?>:<?= htmlspecialchars(defined('DB_PORT') ? (string)DB_PORT : '3306'); ?></code>
            </div>

            <form method="post" onsubmit="return confirm('Do you want to import carrental.sql into this database? Any existing conflicting tables may be overwritten.');">
                <button type="submit" name="import" class="btn btn-primary btn-lg w-100 py-3 fw-bold">
                    🚀 Start Database Import
                </button>
            </form>
            <div class="mt-3 text-center">
                <a href="test_db.php" class="text-muted text-decoration-none small">Verify connection first (test_db.php)</a>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
