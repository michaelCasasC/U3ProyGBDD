<?php
require_once 'config.php';
require_admin();

$db = getDB();
$logs = [];

try {
    // Obtener todos los registros de auditoría ordenados por fecha descendente
    $sql = "SELECT id, action, details, user_name, created_at FROM audit_logs ORDER BY created_at DESC";
    $stmt = $db->query($sql);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error al obtener los registros de auditoría: " . $e->getMessage();
}

require_once 'header.php';
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h3><i class="bi bi-file-earmark-text"></i> Registros de Auditoría</h3>
        </div>
        <div class="col-md-6 text-end">
            <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <?php if (!empty($logs)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Acción</th>
                    <th>Usuario</th>
                    <th>Detalles</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td><span class="badge bg-primary"><?= $log['id'] ?></span></td>
                    <td>
                        <span class="badge 
                            <?php 
                            $action = $log['action'];
                            if (strpos($action, 'CREATE') !== false) {
                                echo 'bg-success';
                            } elseif (strpos($action, 'UPDATE') !== false) {
                                echo 'bg-info';
                            } elseif (strpos($action, 'CLOSE') !== false) {
                                echo 'bg-warning text-dark';
                            } elseif ($action === 'LOGIN' || $action === 'LOGOUT') {
                                echo 'bg-secondary';
                            } else {
                                echo 'bg-secondary';
                            }
                            ?>">
                            <?= translate_action($action) ?>
                        </span>
                    </td>
                    <td>
                        <strong><?= htmlspecialchars($log['user_name']) ?></strong>
                    </td>
                    <td>
                        <small class="text-muted">
                            <?= htmlspecialchars($log['details']) ?>
                        </small>
                    </td>
                    <td>
                        <small><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></small>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3 text-muted">
        <small>Total de registros: <strong><?= count($logs) ?></strong></small>
    </div>

    <?php else: ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No hay registros de auditoría aún.
    </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
