# Design: CI/CD y migración inicial a Hostinger

## Enfoque técnico

El flujo actual conservará `quality` para PR y `main`. En `main`, una cadena serializada `quality -> deploy-code -> bootstrap-content -> smoke` publica código, migra una vez el paquete y comprueba el sitio. `concurrency: hostinger-production` con `cancel-in-progress: false` impide carreras entre despliegues.

## Decisiones de arquitectura

### Decision: Publicación FTP mínima

| Opción | Coste | Decisión |
|---|---|---|
| Subir repositorio completo | Sobrescribe núcleo y configuración | No |
| Dos transferencias de directorio | Mantiene Hostinger como dueño de WordPress | Sí |

**Elección:** `SamKirkland/FTP-Deploy-Action@v4.3.5` transferirá `wp-content/themes/labm/` y `wp-content/plugins/labm-core/` a sus rutas bajo `/public_html/`, con `dangerous-clean-slate: false`.

**Justificación:** el núcleo, `wp-config.php` y uploads no están bajo control del repositorio.

### Decision: Bootstrap con base de datos remota y WP-CLI temporal

| Opción | Coste | Decisión |
|---|---|---|
| Importar SQL sin adaptación | Falla con prefijos distintos | No |
| Script PowerShell, MySQL y WP-CLI efímero | Más validaciones | Sí |

**Elección:** `scripts/bootstrap-hostinger-content.ps1` validará ZIP, manifiesto y `latest.json`; descargará WordPress/WP-CLI temporal y generará un `wp-config.php` temporal con secretos. Detectará el único prefijo destino a partir de las tablas `*_options` y `*_users`; si es distinto al prefijo deducido de `excludedTables`, importará las tablas fuente y las renombrará al prefijo destino. Actualizará exclusivamente la clave de roles dependiente del prefijo. Si hay cero o más de un candidato, abortará antes de importar.

**Justificación:** conserva los usuarios de Hostinger, incluidos los usados por el smoke, aun si el instalador eligió otro prefijo.

### Decision: Estado idempotente al final

**Elección:** el marcador será la opción `labm_content_sync_version`, con la versión de `latest.json`. El preflight la consulta por MySQL: si existe, `bootstrap-content` termina correctamente sin importar. Tras importar se elimina cualquier marcador arrastrado por el dump; solo después de subir uploads, ajustar `home`/`siteurl`, ejecutar `wp search-replace --all-tables-with-prefix --precise --recurse-objects` y vaciar caché se escribe el marcador.

**Alternativa:** marcar antes de subir medios. **Rechazada:** ocultaría una migración incompleta.

### Decision: Recuperación y evidencia sin secretos

**Elección:** antes de modificar tablas, el script crea un `mysqldump --single-transaction` comprimido, sin credenciales en argumentos (archivo temporal de permisos restringidos), y produce un informe redactado. El workflow publica el respaldo y el informe como artefactos privados de 14 días solo si se inició una migración; los borra del runner al finalizar. Ningún artefacto incluye `.env`, configuraciones temporales, cookies, cabeceras ni valores secretos.

**Justificación:** ante fallo no se restaura automáticamente una base que pudo recibir cambios; el informe indicará la restauración manual mediante el respaldo.

## Flujo de datos

```text
push main -> quality -> FTP tema/plugin -> preflight
preflight: marcador? -> sí: smoke
                     -> no: backup DB -> importar/renombrar SQL -> URLs -> FTP uploads -> marcador -> smoke
```

El preflight exige los nueve secretos y usa la URL no secreta fija `http://palevioletred-salamander-919245.hostingersite.com`. `DB_HOST` admite host o `host:puerto` (3306 por defecto). La versión de WordPress se obtiene del índice REST público; debe coincidir en major.minor con el manifiesto. La ausencia, incompatibilidad, checksum, rutas ZIP inseguras, conectividad o un paso posterior abortan sin crear marcador.

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `.github/workflows/quality.yml` | Modificar | Añadir despliegue, bootstrap, concurrencia, outputs, artefactos y smoke. |
| `scripts/bootstrap-hostinger-content.ps1` | Crear | Preflight, respaldo, importación, finalización y reporte redactado. |
| `docs/ci-cd-hostinger.md` | Crear | Provisionamiento, secretos, ejecución, recuperación y cambio futuro a HTTPS. |

## Interfaces / contratos

El script recibirá `-Phase Preflight|Import|Finalize`, `-Package`, `-Pointer`, `-TargetUrl` y secretos solo por variables de entorno. Preflight emite `should_migrate` y `content_version` a `GITHUB_OUTPUT`; Import produce la carpeta temporal `uploads`; Finalize requiere que la transferencia de uploads haya terminado. Los secretos son `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, `DB_HOST`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD`, `WP_USER` y `WP_PASSWORD`.

El smoke enviará las credenciales por POST a `wp-login.php` con cookie temporal y exigirá acceso 2xx a `wp-admin/profile.php`; no activará trazas de shell ni imprimirá respuesta/cookies.

## Estrategia de pruebas

| Capa | Qué probar | Enfoque |
|---|---|---|
| Estática | YAML, parámetros y LF | Parseo YAML y prueba Pester del script. |
| Script | ZIP, checksum, prefijo, marcador, secretos y reporte | Fixtures temporales y MySQL simulado/local. |
| Flujo | Condiciones y orden de jobs | Revisión de eventos/`needs`; ningún job remoto en PR. |
| Producción | Código, primera migración y sesión | Ejecución Actions, inspección de artefacto y smoke autenticado. |

## Migración / despliegue

Hostinger ya debe tener WordPress vacío en `/public_html/`, REST accesible y MySQL permitiendo al runner. La primera ejecución válida crea el respaldo antes de importar. Si falla después de importar, no habrá marcador: se restaura manualmente el artefacto y se corrige la causa antes de reintentar. El dominio HTTPS futuro requiere un cambio controlado de URL, no SQL manual.

## Preguntas abiertas

- [ ] Ninguna.
