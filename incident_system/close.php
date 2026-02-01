<?php
require_once 'config.php';
require_admin();

if (isset($_GET['id'])) {
    try {
        $db = getDB();
        $stmt = $db->prepare("{CALL sp_close_incident(?)}");
        $stmt->execute([$_GET['id']]);
        
        // Registrar en auditoría
        $auditDetails = "Incident ID: {$_GET['id']}";
        log_audit('CLOSE_INCIDENT', $auditDetails);
        
    } catch (Exception $e) {
        // Manejar error
    }
}
header("Location: index.php");
exit;