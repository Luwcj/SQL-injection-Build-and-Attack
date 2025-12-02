<?php  
function make_connection($database_name = null){
    $host = getenv('DB_HOST') ?: 'db';
    $port = getenv('DB_PORT') ?: '5432';
    $user = getenv('DB_USER') ?: 'luc123';
    $pass = getenv('DB_PASS') ?: 'luc321';
    $db = $database_name ?: (getenv('DB_NAME') ?: 'labdb');

    $conn_str = "host={$host} port={$port} dbname={$db} user={$user} password={$pass}";
    $conn = @pg_connect($conn_str);
    if(!$conn){
        $error = error_get_last();
        error_log("PG connect failed: " . ($error['message'] ?? 'Unknown error'));
        return false;
    }
    return $conn;
}
?>