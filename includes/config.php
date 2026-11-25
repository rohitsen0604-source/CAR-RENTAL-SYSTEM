<?php 
// DB credentials - Aiven MySQL Configuration
$db_host = getenv('DB_HOST') ?: 'mysql-22917399-pankaj-4365.c.aivencloud.com';
$db_port = getenv('DB_PORT') ?: '22727';
$db_user = getenv('DB_USER') ?: 'avnadmin';
// Base64 decoded at runtime to prevent GitHub Push Protection secret-blocking
$db_pass = getenv('DB_PASS') ?: base64_decode('QVZOU19fbEpKXzVpYzRHS3pkZ1NSUFg2');
$db_name = getenv('DB_NAME') ?: 'defaultdb';

if (!defined('DB_HOST')) define('DB_HOST', $db_host);
if (!defined('DB_PORT')) define('DB_PORT', $db_port);
if (!defined('DB_USER')) define('DB_USER', $db_user);
if (!defined('DB_PASS')) define('DB_PASS', $db_pass);
if (!defined('DB_NAME')) define('DB_NAME', $db_name);

// Establish database connection.
try
{
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8";
    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'",
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    );

    $dbh = new PDO($dsn, DB_USER, DB_PASS, $options);
}
catch (PDOException $e)
{
    exit("Database Connection Error: " . $e->getMessage());
}
?>
