<?php
// header.php - Navbar reutilizable para todas las páginas
if (!function_exists('is_logged_in')) {
    require_once 'config.php';
}
$user = current_user();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #10b981;
            --danger-color: #ff6b6b;
        }
        
        * {
            transition: all 0.3s ease;
        }

        body {
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-custom {
            background: var(--primary-gradient);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            border-bottom: none;
            min-height: 15vh;
        }

        .navbar-custom .container-fluid {
            height: 100%;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .navbar-custom .navbar-brand {
            font-weight: 800;
            font-size: 1.4rem;
            color: white !important;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }
        
        .navbar-custom .navbar-brand i {
            margin-right: 12px;
            font-size: 1.8rem;
        }
        
        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            margin: 0 10px;
            font-weight: 500;
            border-radius: 8px;
            position: relative;
        }
        
        .navbar-custom .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 2px;
            transition: width 0.3s ease;
        }
        
        .navbar-custom .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .navbar-custom .nav-link:hover::after {
            width: 100%;
        }
        
        .navbar-custom .nav-link.active {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.2);
        }

        .navbar-custom .nav-link.active::after {
            width: 100%;
        }
        
        .user-info {
            color: white;
            font-size: 0.95rem;
            margin-right: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        
        .user-badge {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .user-badge.admin {
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.3) 0%, rgba(238, 90, 111, 0.3) 100%);
            color: #ffe0e0;
            border: 1px solid rgba(255, 107, 107, 0.5);
        }
        
        .user-badge.student {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.3) 0%, rgba(52, 211, 153, 0.3) 100%);
            color: #ccf5dd;
            border: 1px solid rgba(16, 185, 129, 0.5);
        }
        
        .btn-logout {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 16px !important;
            backdrop-filter: blur(10px);
        }
        
        .btn-logout:hover {
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.4) 0%, rgba(238, 90, 111, 0.4) 100%);
            color: white;
            border-color: rgba(255, 107, 107, 0.6);
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }

        
        main {
            min-height: calc(100vh - 15vh);
            padding-top: 2rem;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
    <div class="container-fluid">
        <!-- Logo/Marca -->
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-exclamation-triangle-fill"></i>
            Sistema de Incidentes
        </a>

        <!-- Botón toggle para móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú colapsable -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <!-- Enlace a inicio -->
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-house"></i> Inicio
                    </a>
                </li>

                <!-- Crear incidente -->
                <li class="nav-item">
                    <a class="nav-link" href="create.php">
                        <i class="bi bi-plus-circle"></i> Nuevo Incidente
                    </a>
                </li>

                <!-- Menú Admin (solo si es admin) -->
                <?php if (($user['role'] ?? '') === 'admin'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear"></i> Administración
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item" href="add_lab.php"><i class="bi bi-building"></i> Agregar Laboratorio</a></li>
                        <li><a class="dropdown-item" href="add_device.php"><i class="bi bi-hdd"></i> Agregar Dispositivo</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="audit.php"><i class="bi bi-file-earmark-text"></i> Ver Auditoría</a></li>
                    </ul>
                </li>
                <?php endif; ?>

                <!-- Separador -->
                <li class="nav-item">
                    <div style="width: 1px; height: 20px; background-color: rgba(255, 255, 255, 0.2);"></div>
                </li>

                <!-- Información del usuario -->
                <li class="nav-item">
                    <div class="user-info">
                        <i class="bi bi-person-circle" style="font-size: 1.2rem;"></i>
                        <span><?= htmlspecialchars($user['username'] ?? 'Usuario') ?></span>
                        <span class="user-badge <?= ($user['role'] ?? '') === 'admin' ? 'admin' : 'student' ?>">
                            <?= ($user['role'] ?? '') === 'admin' ? 'Admin' : 'Estudiante' ?>
                        </span>
                    </div>
                </li>

                <!-- Botón cerrar sesión -->
                <li class="nav-item">
                    <a href="logout.php" class="btn btn-logout btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Salir
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container-fluid">
