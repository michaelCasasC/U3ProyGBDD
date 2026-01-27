<?php
require_once 'config.php';

try {
    $db = getDB();

    // Consulta DISTRIBUIDA usando Linked Server
   $sql = "
        SELECT 
            i.id,
            i.incident_type,
            i.severity,
            i.status,
            i.detected_at,
            l.name AS lab_name,
            d.device_type
        FROM incidents i
        LEFT JOIN [INFRA_SERVER].[DB_Infrastructure].[dbo].[labs] l
            ON i.lab_id = l.id
        LEFT JOIN [INFRA_SERVER].[DB_Infrastructure].[dbo].[devices] d
            ON i.device_id = d.id
        ORDER BY i.detected_at DESC
        ";


    $stmt = $db->query($sql);
    $incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al obtener los incidentes: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Incidentes Académicos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Sistema Distribuido de Incidentes Académicos</h2>
        <a href="create.php" class="btn btn-primary">Registrar Incidente</a>
    </div>

    <table class="table table-bordered table-hover bg-white shadow-sm">
        <thead class="table-dark">
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
                        <span class="badge 
                            <?= $inc['severity'] === 'HIGH' ? 'bg-danger' : 
                                ($inc['severity'] === 'MEDIUM' ? 'bg-warning text-dark' : 'bg-success') ?>">
                            <?= $inc['severity'] ?>
                        </span>
                    </td>
                    <td><?= $inc['status'] ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($inc['detected_at'])) ?></td>
                    <td>
                        <?php if ($inc['status'] === 'OPEN'): ?>
                            <a href="close.php?id=<?= $inc['id'] ?>" 
                               class="btn btn-sm btn-success"
                               onclick="return confirm('¿Cerrar este incidente?')">
                                Cerrar
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Cerrado</span>
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

</body>
</html>
