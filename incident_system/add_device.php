<?php
require_once 'config.php';
require_admin();

$db = getDB();
$error = '';
$labs = [];

// Obtener labs para asignar al dispositivo
try {
    $labs = $db->query("SELECT id, name FROM [INFRA_SERVER].[DB_Infrastructure].[dbo].[labs]")->fetchAll();
} catch (Exception $e) {
    $error = 'Error al cargar laboratorios: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $labId = $_POST['lab_id'];
        $deviceType = $_POST['device_type'];
        $stmt = $db->prepare("INSERT INTO [INFRA_SERVER].[DB_Infrastructure].[dbo].[devices] (lab_id, device_type) VALUES (?, ?)");
        $stmt->execute([$labId, $deviceType]);
        
        // Registrar en auditoría
        $auditDetails = "Tipo: $deviceType | Lab ID: $labId";
        log_audit('CREATE_DEVICE', $auditDetails);
        
        header('Location: index.php?success=device_added');
        exit;
    } catch (Exception $e) {
        $error = 'Error al agregar dispositivo: ' . $e->getMessage();
    }
}

require_once 'header.php';
?>

<div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">Agregar Dispositivo</div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Laboratorio</label>
                            <select name="lab_id" class="form-select" required>
                                <option value="">-- Seleccionar --</option>
                                <?php foreach ($labs as $l): ?>
                                    <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Dispositivo</label>
                            <input name="device_type" class="form-control" required>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="index.php" class="btn btn-secondary me-2">Cancelar</a>
                            <button class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>