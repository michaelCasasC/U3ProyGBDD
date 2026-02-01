<?php
require_once 'config.php';

$error = '';
$users = [
    ['username' => 'admin', 'password' => 'admin123', 'role' => 'admin'],
    ['username' => 'student', 'password' => 'student123', 'role' => 'student', 'lab_id' => 1],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    foreach ($users as $user) {
        if ($user['username'] === $u && $user['password'] === $p) {
            $sess = $user;
            unset($sess['password']);
            login_user($sess);
            
            // Registrar login en auditoría
            $auditDetails = "Rol: {$user['role']}";
            log_audit('LOGIN', $auditDetails);
            
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Usuario o contraseña inválidos';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - Sistema de Reportes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            display: grid;
            grid-template-columns: 55% 45%;
            height: 100vh;
            width: 100vw;
        }

        /* SECCIÓN IZQUIERDA - IMAGEN */
        .login-left {
            position: relative;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.05" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            animation: wave 15s ease-in-out infinite;
        }

        @keyframes wave {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1;
        }

        .lab-image-container {
            position: relative;
            z-index: 2;
            width: 90%;
            height: 90%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
        }

        .lab-image {
            width: 100%;
            height: 70%;
            background: url('https://th.bing.com/th/id/R.ac36d0cb4a4e63a4690aa7d89ee443af?rik=siGWIHb%2f2lggzQ&riu=http%3a%2f%2fwww.galileo.edu%2fwp-content%2fblogs.dir%2f1%2ffiles%2flaboratorio-computacion%2fdsc_0009.jpg&ehk=ZJ1wcpRsfHp4kCzr6lQkrkapXJ2l%2ffdbtG8nyo1hv7A%3d&risl=&pid=ImgRaw&r=0') center/cover;
            border-radius: 30px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
            border: 5px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .lab-image::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(30, 60, 114, 0.3) 0%, rgba(126, 34, 206, 0.3) 100%);
        }

        .welcome-text {
            margin-top: 40px;
            text-align: center;
            color: white;
            z-index: 3;
        }

        .welcome-text h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 15px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            letter-spacing: -1px;
            animation: fadeInUp 1s ease-out;
        }

        .welcome-text p {
            font-size: 20px;
            font-weight: 300;
            opacity: 0.95;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1.2s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
        }

        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 300px;
            height: 300px;
            top: 10%;
            left: -10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: 20%;
            right: -5%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 60%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        /* SECCIÓN DERECHA - FORMULARIO */
        .login-right {
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 50px;
            position: relative;
            overflow: hidden;
        }

        .login-right::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }

        .form-container {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 2;
            animation: slideInRight 0.8s ease-out;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            margin: 0 auto 18px;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.4);
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .login-header h2 {
            color: #1a1a1a;
            font-size: 32px;
            margin-bottom: 8px;
            font-weight: 800;
        }

        .login-header p {
            color: #666;
            font-size: 15px;
            font-weight: 400;
        }

        .alert-error {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 5px 20px rgba(255, 107, 107, 0.3);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #1a1a1a;
            font-weight: 600;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 22px;
            z-index: 1;
            transition: all 0.3s ease;
        }

        .form-group input {
            width: 100%;
            padding: 16px 20px 16px 55px;
            border: 3px solid #e8e8e8;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fafafa;
            font-weight: 500;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-group input:focus ~ .input-icon {
            transform: translateY(-50%) scale(1.1);
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            margin-top: 10px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.5);
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .credentials-info {
            margin-top: 20px;
            padding: 16px;
            background: linear-gradient(135deg, #f0f3ff 0%, #faf0ff 100%);
            border-radius: 12px;
            border: 2px solid #e8e8ff;
        }

        .credentials-info h3 {
            font-size: 12px;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .credentials-info p {
            font-size: 12px;
            color: #555;
            margin: 4px 0;
            font-weight: 500;
        }

        .credentials-info strong {
            color: #1a1a1a;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .login-container {
                grid-template-columns: 50% 50%;
            }
        }

        @media (max-width: 768px) {
            .login-container {
                grid-template-columns: 1fr;
            }

            .login-left {
                display: none;
            }

            .login-right {
                padding: 40px 30px;
            }

            .form-container {
                max-width: 100%;
            }

            .login-header h2 {
                font-size: 28px;
            }

            .logo-icon {
                width: 70px;
                height: 70px;
                font-size: 35px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- SECCIÓN IZQUIERDA - IMAGEN -->
        <div class="login-left">
            <div class="floating-shapes">
                <div class="shape"></div>
                <div class="shape"></div>
                <div class="shape"></div>
            </div>
            <div class="image-overlay"></div>
            <div class="lab-image-container">
                <div class="lab-image"></div>
                <div class="welcome-text">
                    <h1>Bienvenido</h1>
                    <p>Sistema de Reporte de Incidentes en Laboratorios</p>
                </div>
            </div>
        </div>

        <!-- SECCIÓN DERECHA - FORMULARIO -->
        <div class="login-right">
            <div class="form-container">
                <div class="login-header">
                    <div class="logo-icon">💻</div>
                    <h2>Iniciar Sesión</h2>
                    <p>Accede a tu cuenta</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert-error">
                        ⚠️ <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <!-- Usuario -->
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <div class="input-wrapper">
                            <input type="text" id="username" name="username" placeholder="Ingresa tu usuario" required>
                            <div class="input-icon">👤</div>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
                            <div class="input-icon">🔒</div>
                        </div>
                    </div>

                    <!-- Botón de Inicio de Sesión -->
                    <button type="submit" class="btn-login">Iniciar Sesión</button>
                </form>

                <!-- Credenciales de prueba -->
                <div class="credentials-info">
                    <h3>🔑 Credenciales de Prueba</h3>
                    <p><strong>Administrador:</strong> admin / admin123</p>
                    <p><strong>Estudiante:</strong> student / student123</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>