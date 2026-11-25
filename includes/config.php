<?php 
// DB credentials from Environment variables or fallback defaults.
$db_url = getenv('DATABASE_URL');
if (!empty($db_url)) {
    $dbparts = parse_url($db_url);
    $db_host = $dbparts['host'] ?? 'localhost';
    $db_port = $dbparts['port'] ?? 3306;
    $db_user = $dbparts['user'] ?? 'root';
    $db_pass = isset($dbparts['pass']) ? urldecode($dbparts['pass']) : '';
    $db_name = isset($dbparts['path']) ? ltrim($dbparts['path'], '/') : 'carrental';
} else {
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_port = getenv('DB_PORT') ?: '3306';
    $db_user = getenv('DB_USER') ?: 'root';
    $db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    $db_name = getenv('DB_NAME') ?: 'carrental';
}

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
    );

    // SSL configuration for Aiven / Cloud MySQL
    $is_remote = (getenv('DB_SSL') === 'true' || strpos(DB_HOST, 'aivencloud.com') !== false || DB_PORT != 3306);
    if ($is_remote) {
        if (defined('PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT')) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }
        $ca_cert = __DIR__ . '/ca.pem';
        if (file_exists($ca_cert)) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $ca_cert;
        }
    }

    $dbh = new PDO($dsn, DB_USER, DB_PASS, $options);
}
catch (PDOException $e)
{
    exit("Database Connection Error: " . $e->getMessage() . "<br><small>Tip: Verify DB_HOST, DB_PORT, DB_USER, DB_PASS, DB_NAME in Render Environment Variables.</small>");
}
?>
