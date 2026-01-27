<?php
require_once 'config.php';
$db = getDB();

// Obtener labs remotos para el select
$labs = $db->query("SELECT id, name FROM INFRA_SERVER.DB_Infrastructure.dbo.labs")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Llamada al Procedimiento Almacenado con transacciones internas
        $stmt = $db->prepare("{CALL sp_register_incident(?, ?, ?, ?, ?)}");
        $stmt->execute([
            $_POST['lab_id'],
            $_POST['device_id'],
            $_POST['incident_type'],
            $_POST['severity'],
            $_POST['description']
        ]);
        header("Location: index.php");
    } catch (Exception $e) {
        $error = "Error al registrar: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Incidente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h3>Registrar Incidente</h3>
    <form method="POST">
        <div class="mb-3">
            <label>Laboratorio (Cargado vía Linked Server):</label>
            <select name="lab_id" class="form-control" required>
                <?php foreach($labs as $l): ?>
                    <option value="<?= $l['id'] ?>"><?= $l['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>ID Dispositivo (Referencia remota):</label>
            <input type="number" name="device_id" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Tipo de Incidente:</label>
            <input type="text" name="incident_type" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Severidad:</label>
            <select name="severity" class="form-control">
                <option value="LOW">LOW</option>
                <option value="MEDIUM">MEDIUM</option>
                <option value="HIGH">HIGH</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Descripción:</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Incidente</button>
        <a href="index.php" class="btn btn-secondary">Volver</a>
    </form>
</body>
</html>