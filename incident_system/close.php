<?php
require_once 'config.php';
if (isset($_GET['id'])) {
    try {
        $db = getDB();
        $stmt = $db->prepare("{CALL sp_close_incident(?)}");
        $stmt->execute([$_GET['id']]);
    } catch (Exception $e) {
        // Manejar error
    }
}
header("Location: index.php");