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

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Helpers de autenticación
function login_user(array $user) {
    $_SESSION['user'] = $user;
}

function logout_user() {
    unset($_SESSION['user']);
    session_regenerate_id(true);
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_logged_in() {
    return !empty($_SESSION['user']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function require_admin() {
    if (!is_logged_in() || ($_SESSION['user']['role'] ?? '') !== 'admin') {
        header('Location: index.php?error=forbidden');
        exit;
    }
}

// Función para registrar acciones en la tabla de auditoría
function log_audit($action, $details = '') {
    try {
        $db = getDB();
        $user = current_user();
        $username = $user['username'] ?? 'Desconocido';
        $role = $user['role'] ?? 'Desconocido';
        
        // Crear detalle completo con acción, detalles y rol
        $fullDetails = "Rol: $role | $details";
        
        $stmt = $db->prepare("INSERT INTO audit_logs (action, details, user_name) VALUES (?, ?, ?)");
        $stmt->execute([
            $action,
            $fullDetails,
            $username
        ]);
    } catch (Exception $e) {
        // Registrar el error pero no interrumpir la ejecución
        if (defined('DEBUG') && DEBUG) {
            error_log("Error al registrar auditoría: " . $e->getMessage());
        }
    }
}

// Funciones de traducción para valores de la BD
function translate_severity($severity) {
    $translations = [
        'LOW' => 'Baja',
        'MEDIUM' => 'Media',
        'HIGH' => 'Alta'
    ];
    return $translations[$severity] ?? $severity;
}

function translate_status($status) {
    $translations = [
        'OPEN' => 'Abierto',
        'IN_PROGRESS' => 'En Progreso',
        'CLOSED' => 'Cerrado'
    ];
    return $translations[$status] ?? $status;
}

function translate_action($action) {
    $translations = [
        'CREATE_INCIDENT' => 'Crear Incidente',
        'UPDATE_INCIDENT' => 'Actualizar Incidente',
        'CLOSE_INCIDENT' => 'Cerrar Incidente',
        'CREATE_LAB' => 'Crear Laboratorio',
        'CREATE_DEVICE' => 'Crear Dispositivo',
        'LOGIN' => 'Iniciar Sesión',
        'LOGOUT' => 'Cerrar Sesión'
    ];
    return $translations[$action] ?? $action;
}

function get_badge_class($severity) {
    $severity = strtoupper(trim($severity));
    $classes = [
        'LOW' => 'bg-success',
        'MEDIUM' => 'bg-warning text-dark',
        'HIGH' => 'bg-danger'
    ];
    return $classes[$severity] ?? 'bg-secondary';
}