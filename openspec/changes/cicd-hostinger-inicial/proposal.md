# Propuesta: CI/CD y migración inicial a Hostinger

## Intención

Automatizar el despliegue del tema y plugin LABM y cargar una vez el contenido canónico en Hostinger.

## Alcance

### Incluye

- Encadenar `quality`, FTP selectivo y migración inicial en `push` exitoso a `main`.
- Publicar tema y plugin sin borrar archivos remotos ni tocar núcleo, configuración o `uploads` existentes.
- Importar una vez `content-sync/canonical.zip` por MySQL y WP-CLI, validar integridad, subir medios y reemplazar `http://localhost:8080` por `http://palevioletred-salamander-919245.hostingersite.com`.
- Registrar un marcador persistente de versión para impedir reimportaciones; documentar instalación manual de WordPress y secretos requeridos.

### Excluye

- Instalación automática de WordPress, gestión de usuarios, despliegues cotidianos de base de datos, dominio final/HTTPS y borrado de contenido remoto.

## Enfoque

El workflow se detendrá si fallan los gates. Tras FTP de `labm` y `labm-core`, verificará WordPress, manifiesto y ausencia del marcador. Ejecutará `wp search-replace` compatible con datos serializados y guardará el marcador al completar base de datos, URLs y medios.

## Áreas afectadas

| Área | Impacto | Descripción |
|---|---|---|
| `.github/workflows/quality.yml` | Modificado | Añade despliegue y bootstrap condicionados. |
| `scripts/bootstrap-hostinger-content.ps1` | Nuevo | Orquesta validación, importación, URLs y marcador. |
| `docs/ci-cd-hostinger.md` | Nuevo | Provisionamiento, secretos, operación y reversión. |

## Riesgos

| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Conectividad MySQL de Actions bloqueada | Media | Validar acceso antes de importar y no crear marcador. |
| Importación parcial | Media | Validar paquete y crear marcador al final. |
| Credenciales expuestas | Baja | Secretos, sin trazas de valores ni archivos persistentes. |

## Plan de reversión

Deshabilitar o revertir los jobs en `.github/workflows/quality.yml`; el FTP no elimina archivos. Antes de migrar, respaldar base de datos y `uploads` en Hostinger. Si falla, restaurar ese respaldo, retirar medios identificados por el manifiesto y eliminar el marcador tras confirmar la restauración.

## Dependencias

- WordPress vacío instalado manualmente en `/public_html/` y MySQL accesible desde GitHub Actions.
- Secretos `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, `DB_HOST`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD` y `WP_PRODUCTION_URL`.

## Criterios de éxito

- [ ] Un `push` a `main` despliega tema y plugin solo tras `quality` exitosa.
- [ ] La primera ejecución válida importa paquete, medios y URL temporal, y crea el marcador.
- [ ] Una ejecución posterior omite la migración y preserva contenido y medios.
- [ ] Ningún secreto aparece en registros ni queda versionado.
