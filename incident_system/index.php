<?php
require_once 'config.php';
require_login();

$db = getDB();
$user = current_user();
try {
    // Consulta DISTRIBUIDA usando Linked Server
    $baseSql = "
        SELECT 
            i.id,
            i.incident_type,
            i.severity,
            i.status,
            i.detected_at,
            l.name AS lab_name,
            d.device_type,
            i.lab_id
        FROM incidents i
        LEFT JOIN [INFRA_SERVER].[DB_Infrastructure].[dbo].[labs] l
            ON i.lab_id = l.id
        LEFT JOIN [INFRA_SERVER].[DB_Infrastructure].[dbo].[devices] d
            ON i.device_id = d.id
    ";

    if (($user['role'] ?? '') === 'student') {
        $sql = $baseSql . " WHERE i.lab_id = ? ORDER BY i.detected_at DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$user['lab_id'] ?? 0]);
    } else {
        $sql = $baseSql . " ORDER BY i.detected_at DESC";
        $stmt = $db->query($sql);
    }

    $incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener los incidentes: " . $e->getMessage());
}

require_once 'header.php';
?>

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">
                <i class="bi bi-exclamation-circle-fill" style="color: #667eea; margin-right: 10px;"></i>
                Incidentes Registrados
            </h2>
            <small class="text-muted">Gestión de incidentes del sistema</small>
        </div>
        <a href="create.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Nuevo Incidente
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body p-0">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Laboratorio</th>
                <th>Dispositivo</th>
                <th>Tipo</th>
                <th>Severidad</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>

        <?php if (count($incidents) > 0): ?>
            <?php foreach ($incidents as $inc): ?>
                <tr>
                    <td><?= $inc['id'] ?></td>
                    <td><?= htmlspecialchars($inc['lab_name'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($inc['device_type'] ?? 'N/A') ?></td>
                    <td><?= htmlspecialchars($inc['incident_type']) ?></td>
                    <td>
                        <span class="badge <?= get_badge_class($inc['severity']) ?>">
                            <?= translate_severity($inc['severity']) ?>
                        </span>
                    </td>
                    <td><?= translate_status($inc['status']) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($inc['detected_at'])) ?></td>
                    <td>
                        <a href="update.php?id=<?= $inc['id'] ?>" 
                           class="btn btn-sm btn-warning me-2"
                           title="Editar incidente">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <?php if ($inc['status'] === 'OPEN'): ?>
                            <a href="close.php?id=<?= $inc['id'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('¿Cerrar este incidente?')"
                               title="Cerrar incidente">
                                <i class="bi bi-check-circle"></i> Cerrar
                            </a>
                        <?php else: ?>
                            <span class="badge bg-secondary">Cerrado</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center text-muted">
                    No hay incidentes registrados
                </td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
