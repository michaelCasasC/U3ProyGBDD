<?php
require_once './incident_system/config.php';

header('Content-Type: text/html; charset=utf-8');

echo "<h2>Panel de Diagnóstico de Base de Datos Distribuida</h2>";

try {
    $db = getDB();
    echo "<p style='color: green;'>✅ 1. Conexión local exitosa (DB_Incidents).</p>";

    // Prueba 2: Verificar tablas locales
    $stmt = $db->query("SELECT COUNT(*) FROM incidents");
    $count = $stmt->fetchColumn();
    echo "<p style='color: green;'>✅ 2. Acceso a tabla local 'incidents' correcto (Registros: $count).</p>";

    // Prueba 3: Verificar Linked Server (Cruce de fronteras)
    echo "<h3>Verificando Enlace Distribuido (Linked Server)...</h3>";
    try {
        // Intentamos leer un dato del servidor remoto de tu compañera
        $stmtRemote = $db->query("SELECT TOP 1 name FROM INFRA_SERVER.DB_Infrastructure.dbo.labs");
        $remoteLab = $stmtRemote->fetchColumn();
        
        if ($remoteLab) {
            echo "<p style='color: green;'>✅ 3. Linked Server FUNCIONAL. Se pudo leer el laboratorio: <strong>$remoteLab</strong> del servidor remoto.</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ 3. Linked Server conectado, pero la tabla remota parece estar vacía.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ 3. Error en Linked Server: " . $e->getMessage() . "</p>";
        echo "<blockquote style='background: #fee; padding: 10px; border-left: 5px solid red;'>";
        echo "<strong>Posibles causas:</strong><br>";
        echo "- El Linked Server no se llama 'INFRA_SERVER'.<br>";
        echo "- La IP 172.27.54.27 no es alcanzable o el firewall bloquea el puerto 1433.<br>";
        echo "- El usuario AdminU3 no tiene permisos en el servidor remoto.";
        echo "</blockquote>";
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error crítico de configuración: " . $e->getMessage() . "</p>";
}

echo "<br><a href='index.php'>Volver al Inicio</a>";