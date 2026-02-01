# Cambios necesarios en la Base de Datos

## Para que todo esté completamente en español, necesitas cambiar los siguientes valores en la BD:

### 1. Tabla `incidents` - Columna `severity`

**Valores a cambiar:**

- `LOW` → se mostrará como "Baja" (automático, ya traducido en el código)
- `MEDIUM` → se mostrará como "Media" (automático, ya traducido en el código)
- `HIGH` → se mostrará como "Alta" (automático, ya traducido en el código)

**O si prefieres guardar directamente en español en la BD, ejecuta:**

```sql
UPDATE incidents SET severity = 'Baja' WHERE severity = 'LOW';
UPDATE incidents SET severity = 'Media' WHERE severity = 'MEDIUM';
UPDATE incidents SET severity = 'Alta' WHERE severity = 'HIGH';
```

### 2. Tabla `incidents` - Columna `status`

**Valores a cambiar:**

- `OPEN` → se mostrará como "Abierto" (automático, ya traducido en el código)
- `IN_PROGRESS` → se mostrará como "En Progreso" (automático, ya traducido en el código)
- `CLOSED` → se mostrará como "Cerrado" (automático, ya traducido en el código)

**O si prefieres guardar directamente en español en la BD, ejecuta:**

```sql
UPDATE incidents SET status = 'Abierto' WHERE status = 'OPEN';
UPDATE incidents SET status = 'En Progreso' WHERE status = 'IN_PROGRESS';
UPDATE incidents SET status = 'Cerrado' WHERE status = 'CLOSED';
```

### 3. Tabla `audit_logs` - Columna `action`

**Valores a cambiar (se mostrarán automáticamente traducidos):**

- `CREATE_INCIDENT` → se mostrará como "Crear Incidente"
- `UPDATE_INCIDENT` → se mostrará como "Actualizar Incidente"
- `CLOSE_INCIDENT` → se mostrará como "Cerrar Incidente"
- `CREATE_LAB` → se mostrará como "Crear Laboratorio"
- `CREATE_DEVICE` → se mostrará como "Crear Dispositivo"
- `LOGIN` → se mostrará como "Iniciar Sesión"
- `LOGOUT` → se mostrará como "Cerrar Sesión"

## Recomendación:

**Opción 1 (Recomendada): Guardar en inglés en la BD, traducir en la vista**

- Los valores se guardan en inglés (LOW, MEDIUM, HIGH, etc.)
- El código PHP traduce automáticamente para mostrar en español
- Mejor normalización de datos en la BD
- ✅ **ESTO YA ESTÁ HECHO EN EL CÓDIGO**

**Opción 2: Guardar directamente en español en la BD**

- Ejecuta los SQL anteriores para convertir todos los valores
- Cambia los procedimientos almacenados para que guarden en español
- Si lo haces, debemos actualizar el código PHP también

## Status Actual:

✅ Interfaz completamente en español
✅ Traducción automática de valores de la BD
✅ Sin cambios necesarios en código, solo en datos si quieres guardar en español
