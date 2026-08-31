# Contenido canónico

Esta carpeta es parte del repositorio. `scripts/content-sync.ps1 -Action Push` genera:

- `canonical.zip`: base de datos de contenido sin las tablas de usuarios/usermeta, más `uploads` y manifiesto de integridad.
- `latest.json`: versión, base y SHA-256 del paquete.

No edite ni fusione estos archivos manualmente. Consulte `docs/content-sync.md`.
