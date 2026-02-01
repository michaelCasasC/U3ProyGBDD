<?php
require_once 'config.php';
require_admin();

$db = getDB();
$incident = null;
$error = '';

// Obtener el incidente a editar
if (isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM incidents WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $incident = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$incident) {
        header("Location: index.php?error=not_found");
        exit;
    }
}

// Procesar actualización
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['id'])) {
    try {
        $stmt = $db->prepare("UPDATE incidents SET incident_type = ?, severity = ?, status = ? WHERE id = ?");
        $stmt->execute([
            $_POST['incident_type'],
            $_POST['severity'],
            $_POST['status'],
            $_GET['id']
        ]);
        
        // Registrar en auditoría
        $auditDetails = "Incident ID: {$_GET['id']} | Tipo: {$_POST['incident_type']} | Severidad: {$_POST['severity']} | Estado: {$_POST['status']}";
        log_audit('UPDATE_INCIDENT', $auditDetails);
        
        header("Location: index.php?success=updated");
        exit;
    } catch (Exception $e) {
        $error = "Error al actualizar: " . $e->getMessage();
    }
}

require_once 'header.php';
?>

<div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Editar Incidente</h5>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($incident): ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="incident_type" class="form-label">Tipo de Incidente</label>
                                <input type="text" class="form-control" id="incident_type" name="incident_type" 
                                       value="<?= htmlspecialchars($incident['incident_type']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="severity" class="form-label">Severidad</label>
                                <select class="form-select" id="severity" name="severity" required>
                                    <option value="LOW" <?= $incident['severity'] === 'LOW' ? 'selected' : '' ?>>Baja</option>
                                    <option value="MEDIUM" <?= $incident['severity'] === 'MEDIUM' ? 'selected' : '' ?>>Media</option>
                                    <option value="HIGH" <?= $incident['severity'] === 'HIGH' ? 'selected' : '' ?>>Alta</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="OPEN" <?= $incident['status'] === 'OPEN' ? 'selected' : '' ?>>Abierto</option>
                                    <option value="IN_PROGRESS" <?= $incident['status'] === 'IN_PROGRESS' ? 'selected' : '' ?>>En Progreso</option>
                                    <option value="CLOSED" <?= $incident['status'] === 'CLOSED' ? 'selected' : '' ?>>Cerrado</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-sm-flex justify-content-sm-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Guardar Cambios
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Incidente no encontrado
                        </div>
                        <a href="index.php" class="btn btn-secondary">Volver al listado</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>