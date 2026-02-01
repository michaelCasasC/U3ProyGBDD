<?php
require_once 'config.php';

// Registrar logout en auditoría antes de cerrar la sesión
$user = current_user();
if ($user) {
    $auditDetails = "Rol: {$user['role']}";
    log_audit('LOGOUT', $auditDetails);
}

logout_user();
header('Location: login.php');
exit;
