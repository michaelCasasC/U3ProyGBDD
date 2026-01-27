<?php
define('DB_DSN', "sqlsrv:Server=USUARIO\\SQLEXPRESS;Database=DB_Incidents");
define('DB_USER', "AdminU3");
define('DB_PASS', "123456");
define('DEBUG', true);

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(DB_DSN, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            if (defined('DEBUG') && DEBUG) {
                die("Error de conexión: " . $e->getMessage());
            }
            throw $e;
        }
    }
    return $pdo;
}