# Sincronización de contenido WordPress entre entornos Docker locales

## Solicitud para NEA Flow

Usar este documento como fuente de entrada para explorar, especificar, diseñar y planificar el cambio `docker-content-sync`.

Antes de decidir la solución, la fase EXPLORE debe inspeccionar la configuración Docker, los scripts, los volúmenes, WordPress y las prácticas de respaldo existentes en el repositorio. No se debe asumir que los nombres o comandos propuestos aquí coinciden con la implementación actual.

## Contexto y problema

Dos desarrolladores trabajan sobre el mismo sitio WordPress, cada uno en un entorno local ejecutado con Docker. El código se comparte mediante Git, pero no existe un entorno de staging y el contenido ha divergido entre las dos instalaciones.

La información que debe sincronizarse incluye:

- La base de datos de WordPress almacenada en MariaDB/MySQL.
- Los archivos multimedia de `wp-content/uploads`.
- Páginas, entradas, menús, taxonomías, metadatos, opciones y relaciones relevantes.

Copiar o fusionar manualmente volcados SQL puede sobrescribir información, producir conflictos de identificadores o romper relaciones internas de WordPress. Compartir directamente los archivos internos del volumen de MariaDB tampoco es una operación segura.

## Objetivo

Proporcionar un mecanismo local, reproducible y seguro para consolidar el contenido existente y mantener sincronizados los dos entornos Docker sin requerir staging y sin perder información.

## Alcance

- Respaldar la base de datos y `wp-content/uploads` antes de cualquier importación.
- Exportar un paquete portable de contenido desde el entorno canónico.
- Importar el paquete en el otro entorno local.
- Incluir un manifiesto con versión, fecha, origen y, cuando sea viable, checksums.
- Adaptar de forma segura las URLs y rutas específicas de cada entorno mediante WP-CLI u otra herramienta compatible con datos serializados de WordPress.
- Definir una política de escritor único o un bloqueo explícito para evitar publicaciones simultáneas que se sobrescriban.
- Documentar el procedimiento normal de sincronización y el procedimiento de recuperación.
- Consolidar inicialmente los contenidos distintos que ya existen en ambos entornos.
- Mantener credenciales, archivos sensibles y respaldos privados fuera de Git.

## Fuera de alcance

- Construir o contratar un entorno de staging.
- Implementar una fusión automática y bidireccional de dos bases de datos WordPress completas.
- Sincronizar código, plugins o temas por este mecanismo; estos continúan gestionándose mediante Git y el proceso normal del proyecto.
- Publicar respaldos, credenciales o contenido privado en el repositorio.
- Eliminar volúmenes o restaurar datos destructivamente sin confirmación y respaldo verificado.

## Flujo esperado

### Operación cotidiana

1. La persona que va a editar ejecuta el proceso de `pull` o importación antes de comenzar.
2. El mecanismo verifica el estado, crea un respaldo local y confirma que el paquete es válido.
3. Durante la edición existe un único escritor autorizado o un bloqueo visible para el equipo.
4. Al terminar, la persona autorizada ejecuta el proceso de `push` o exportación.
5. La otra persona importa la nueva versión antes de continuar.

Los nombres finales de los comandos deben definirse según las convenciones existentes. Como referencia, podrían existir scripts equivalentes a:

- `scripts/content-backup.ps1`
- `scripts/content-export.ps1`
- `scripts/content-import.ps1`
- `scripts/content-pull.ps1`
- `scripts/content-push.ps1`

No es obligatorio crear cinco scripts si una interfaz más pequeña y clara cubre los mismos casos.

### Consolidación inicial

1. Generar y verificar respaldos independientes de ambos entornos, incluyendo base de datos y `uploads`.
2. Elegir explícitamente cuál copia será la base canónica.
3. Identificar el contenido exclusivo o más reciente del segundo entorno.
4. Migrar selectivamente ese contenido mediante exportación/importación WXR, WP-CLI o una estrategia equivalente que preserve las relaciones necesarias.
5. Transferir y validar los archivos multimedia correspondientes.
6. Revisar páginas, entradas, medios, menús, taxonomías y enlaces después de la consolidación.
7. Crear el primer paquete canónico y restaurarlo en ambos entornos.
8. Conservar los respaldos originales hasta que ambas personas aprueben el resultado.

La consolidación no debe intentar fusionar ciegamente los dos dumps SQL.

## Requisitos funcionales

1. El sistema debe crear un respaldo recuperable antes de importar o reemplazar contenido.
2. El paquete debe contener un dump lógico consistente de la base de datos y una copia de `wp-content/uploads`.
3. La importación debe validar la presencia y estructura mínima del paquete antes de modificar el entorno.
4. Los comandos deben fallar de manera segura cuando Docker o los servicios requeridos no estén disponibles.
5. La restauración debe manejar correctamente datos serializados de WordPress al reemplazar URLs.
6. Debe existir una forma inequívoca de conocer la versión de contenido instalada y la última versión exportada.
7. Debe prevenirse o advertirse claramente un `push` basado en una versión obsoleta.
8. Debe documentarse cómo recuperar el estado anterior usando el respaldo automático.
9. La solución debe incluir `uploads`; sincronizar solo la base de datos no es suficiente.
10. Los errores deben producir mensajes accionables sin imprimir secretos.

## Requisitos no funcionales

- Compatibilidad con PowerShell y el flujo Docker utilizado actualmente por el proyecto.
- Operaciones idempotentes cuando sea razonable.
- Dependencias mínimas y documentadas.
- Respaldos identificables por fecha y versión, con una política explícita de conservación.
- Evidencia verificable de integridad del paquete y resultado de la importación.
- Ningún comando destructivo debe ejecutarse por defecto ni sobre rutas o volúmenes no resueltos.
- La solución debe preservar el bootstrap y las pruebas existentes del proyecto.

## Seguridad y manejo de datos

- No versionar archivos `.env`, contraseñas, tokens, cookies, salts ni dumps con secretos sin sanear.
- El paquete canónico se guarda en Git por decisión del equipo, pero debe excluir usuarios, sesiones y credenciales; solo puede contener contenido público o demostrativo autorizado.
- El paquete canónico se versiona en Git; los respaldos locales y las credenciales permanecen excluidos.
- No leer, copiar ni publicar los PDF restringidos de `docs/` durante la sincronización.
- Mantener la marca `[DEMO LABM — FICTICIO]` donde corresponda al contenido demostrativo.
- Requerir confirmación humana para eliminar volúmenes, reemplazar datos sin respaldo o ejecutar una recuperación destructiva.

## Artefactos esperados

- Scripts PowerShell para respaldo y sincronización, ajustados a la arquitectura real encontrada.
- Formato documentado del paquete de contenido.
- Manifiesto de versión e integridad.
- Exclusiones apropiadas en `.gitignore` para respaldos y paquetes privados, si son necesarias.
- Documentación de uso normal, consolidación inicial, resolución de conflictos y recuperación.
- Pruebas o comprobaciones automatizadas proporcionales al riesgo.

## Criterios de aceptación

1. Desde un entorno con contenido canónico se puede generar un paquete completo sin copiar directamente los archivos internos activos de MariaDB.
2. Un segundo entorno limpio o divergente puede crear su respaldo e importar el paquete mediante un procedimiento documentado.
3. Después de importar, ambos entornos muestran las mismas páginas, entradas, menús, taxonomías y medios incluidos en el paquete.
4. Los medios referenciados cargan correctamente y no quedan URLs del entorno de origen donde deban reemplazarse.
5. Una importación con paquete incompleto, corrupto o incompatible se detiene antes de reemplazar datos.
6. Un `push` obsoleto se bloquea o requiere una confirmación explícita acompañada de una advertencia clara.
7. Se puede restaurar el respaldo previo y recuperar el estado anterior.
8. Los logs y mensajes no exponen credenciales.
9. Solo el paquete canónico sanitizado aparece versionado; los respaldos privados no aparecen accidentalmente en Git.
10. El procedimiento de consolidación inicial conserva evidencia de los dos estados originales hasta su aprobación.

## Riesgos

- Pérdida de contenido por importaciones completas realizadas sobre una versión más reciente.
- Ruptura de datos serializados si las URLs se reemplazan como texto SQL simple.
- Bases y archivos multimedia desincronizados.
- Paquetes demasiado grandes para Git o almacenamiento frecuente.
- Exposición de datos o credenciales dentro de dumps.
- Diferencias en versiones de WordPress, plugins o esquema entre las dos máquinas.
- Sensación falsa de fusión concurrente cuando el modelo real es de escritor único.

## Decisiones abiertas para EXPLORE y DESIGN

- **Decidido:** el paquete canónico se almacena en `content-sync/` dentro del repositorio Git; no se usa almacenamiento externo.
- ¿Cuál de los dos entornos será inicialmente la fuente canónica?
- ¿Qué mecanismo de bloqueo o control de versión evitará escrituras simultáneas?
- ¿Cuánto ocupan actualmente el dump y `uploads`?
- ¿Qué herramientas ya están disponibles dentro de los contenedores, especialmente WP-CLI y utilidades de archivado?
- ¿Los nombres de dominio locales son iguales o diferentes?
- ¿Qué contenido divergente debe conservarse de cada instalación?
- ¿Qué retención y ubicación tendrán los respaldos?

## Comando sugerido

Iniciar la planificación estructurada desde el hilo principal con:

```text
/flow-nea-ff docker-content-sync
```

La fase EXPLORE debe usar este documento como fuente y validar todas sus suposiciones contra el repositorio antes de producir la propuesta.
