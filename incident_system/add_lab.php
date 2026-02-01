<?php
require_once 'config.php';
require_admin();

$db = getDB();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'] ?? '';
        $stmt = $db->prepare("INSERT INTO [INFRA_SERVER].[DB_Infrastructure].[dbo].[labs] (name) VALUES (?)");
        $stmt->execute([$name]);
        
        // Registrar en auditoría
        $auditDetails = "Nombre: $name";
        log_audit('CREATE_LAB', $auditDetails);
        
        header('Location: index.php?success=lab_added');
        exit;
    } catch (Exception $e) {
        $error = 'Error al agregar laboratorio: ' . $e->getMessage();
    }
}

require_once 'header.php';
?>

<div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">Agregar Laboratorio</div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nombre del Laboratorio</label>
                            <input name="name" class="form-control" required>
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