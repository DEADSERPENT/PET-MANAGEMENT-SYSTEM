<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'admin');
define('DB_NAME', 'sarpams');

function getDB() {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die('<div style="font-family:sans-serif;padding:40px;background:#fff3cd;border-left:5px solid #ffc107;margin:20px;">
                <h2>Database Connection Failed</h2>
                <p>' . htmlspecialchars($conn->connect_error) . '</p>
                <p>Please ensure MySQL is running and the database <strong>sarpams</strong> has been imported from <code>database.sql</code>.</p>
                </div>');
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

function escape($value) {
    return getDB()->real_escape_string(trim($value));
}

function query($sql) {
    $result = getDB()->query($sql);
    if ($result === false) {
        error_log("SQL Error: " . getDB()->error . " | Query: " . $sql);
        return false;
    }
    return $result;
}

function fetchAll($sql) {
    $result = query($sql);
    if (!$result) return [];
    $rows = [];
    while ($row = $result->fetch_assoc()) $rows[] = $row;
    return $rows;
}

function fetchOne($sql) {
    $result = query($sql);
    if (!$result) return null;
    return $result->fetch_assoc();
}

function lastInsertId() {
    return getDB()->insert_id;
}

function affectedRows() {
    return getDB()->affected_rows;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function flash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

session_start();
