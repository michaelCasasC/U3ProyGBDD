<?php
require_once 'config.php';
require_login();
$db = getDB();

$user = current_user();
$error = '';
$labs = [];
$devices = [];

// Obtener labs remotos para el select (si es estudiante, sólo su laboratorio)
try {
    if (($user['role'] ?? '') === 'student') {
        $stmt = $db->prepare("SELECT id, name FROM INFRA_SERVER.DB_Infrastructure.dbo.labs WHERE id = ?");
        $stmt->execute([$user['lab_id'] ?? 0]);
        $labs = $stmt->fetchAll();
    } else {
        $labs = $db->query("SELECT id, name FROM INFRA_SERVER.DB_Infrastructure.dbo.labs")->fetchAll();
    }
} catch (Exception $e) {
    $labs = [];
    $error = "No se pudo cargar la lista de laboratorios";
}

// Obtener dispositivos remotos para el select
try {
    $devices = $db->query("SELECT id, device_type, lab_id FROM INFRA_SERVER.DB_Infrastructure.dbo.devices ORDER BY device_type")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $devices = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Si el usuario es estudiante, forzar el lab_id de su sesión
        $labId = ($_SESSION['user']['role'] ?? '') === 'student' ? ($_SESSION['user']['lab_id'] ?? 0) : $_POST['lab_id'];

        // Llamada al Procedimiento Almacenado con transacciones internas
        $stmt = $db->prepare("{CALL sp_register_incident(?, ?, ?, ?, ?)}");
        $stmt->execute([
            $labId,
            $_POST['device_id'],
            $_POST['incident_type'],
            $_POST['severity'],
            $_POST['description']
        ]);
        
        // Registrar en auditoría
        $auditDetails = "Tipo: {$_POST['incident_type']} | Severidad: {$_POST['severity']} | Lab ID: $labId";
        log_audit('CREATE_INCIDENT', $auditDetails);
        
        header("Location: index.php?success=created");
        exit;
    } catch (Exception $e) {
        $error = "Error al registrar: " . $e->getMessage();
    }
}

require_once 'header.php';
?>

<div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-circle"></i> Registrar Nuevo Incidente</h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="lab_id" class="form-label">Laboratorio</label>
                            <select name="lab_id" id="lab_id" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach($labs as $l): ?>
                                    <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="device_id" class="form-label">Dispositivo</label>
                            <select name="device_id" id="device_id" class="form-select" required>
                                <option value="">-- Seleccionar Dispositivo --</option>
                                <?php foreach($devices as $d): ?>
                                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['device_type']) ?> (ID: <?= $d['id'] ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Selecciona el dispositivo afectado</small>
                        </div>

                        <div class="mb-3">
                            <label for="incident_type" class="form-label">Tipo de Incidente</label>
                            <input type="text" name="incident_type" id="incident_type" class="form-control" 
                                   placeholder="ej. Hardware, Software, Red" required>
                        </div>

                        <div class="mb-3">
                            <label for="severity" class="form-label">Severidad</label>
                            <select name="severity" id="severity" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <option value="LOW">Baja</option>
                                <option value="MEDIUM" selected>Media</option>
                                <option value="HIGH">Alta</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea name="description" id="description" class="form-control" rows="4" 
                                      placeholder="Describe el incidente en detalle..."></textarea>
                        </div>

                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Guardar Incidente
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>