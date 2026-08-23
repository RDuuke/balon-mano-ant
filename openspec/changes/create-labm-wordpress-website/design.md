# Diseño: Sitio WordPress de LABM

## Enfoque técnico

El primer incremento será local: Compose levanta WordPress, MariaDB y WP-CLI; monta el tema `labm` y `labm-core`. Un bootstrap idempotente instala, activa, carga fixtures ficticios y genera evidencia. Excluye despliegue, correo real, PDF y contenido oficial.

## Decisiones de arquitectura

| Decisión | Elección | Alternativas / trade-off | Justificación |
|---|---|---|---|
| Topología | `db` saludable; `wordpress` dependiente; `wp-cli` en perfil `tools`; volúmenes para DB/core/uploads; bind mounts de tema/plugin | Imagen monolítica mezcla código y estado | Persistencia reproducible sin versionar core mutable |
| Versiones | Imágenes fijadas por tag compatible y digest; dependencias de desarrollo bloqueadas en lockfiles; actualización deliberada y validada en una matriz WP/PHP/DB | `latest` reduce mantenimiento, pero rompe reproducibilidad | Hosting productivo aún desconocido; no se promete compatibilidad sin evidencia |
| Configuración | `.env` ignorado; `.env.example` ficticio y validado; sin defaults productivos | Credenciales embebidas son inseguras | Personalización local auditable |
| Componentes | Tema de bloques `labm`; dominio y seguridad en `labm-core`; fallback si falta plugin | Dominio en tema se pierde al cambiarlo | Preserva contenido y activación independiente |
| Fixtures | `wp labm fixtures load`; clave estable y upsert exclusivo de `[DEMO LABM — FICTICIO]` | WXR controla peor la repetición | Cubre vistas/estados críticos sin adjuntos sensibles, duplicados ni cambios editoriales |
| Seguridad documental | Los dos `docs/*.pdf` quedan como fuentes restringidas: sin lectura automática, extracción, copia a uploads ni publicación; revisión humana previa y registro de autorización | Automatizar extracción acelera, pero puede filtrar datos personales | Cumple procedencia, privacidad y mínimo privilegio |
| Rollback | Parada conserva volúmenes; reset exige confirmación; respaldo antes de esquema/medios | Borrar volúmenes pierde estado | No hay migraciones irreversibles inicialmente |

## Flujo de datos

`env validado -> Compose saludable -> WP-CLI instala -> activa plugin/tema por separado -> fixtures upsert -> smoke/E2E recopilan evidencia`

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `compose.yaml`, `.env.example`, `.gitignore` | Crear | Servicios, configuración segura e higiene |
| `docker/healthcheck/`, `scripts/bootstrap.*` | Crear | Salud, validación y bootstrap idempotente |
| `wp-content/themes/labm/` | Crear | Tema de bloques, tokens y fallback |
| `wp-content/plugins/labm-core/` | Crear | Dominio persistente y comando de fixtures |
| `composer.json`, `phpstan.neon.dist`, `phpcs.xml.dist` | Crear | PHPUnit, WPCS y análisis estático bloqueados |
| `tests/php/`, `tests/e2e/`, `playwright.config.*` | Crear | Integración y navegación |
| `docs/development.md`, `docs/testing.md` | Crear | Ciclo de vida y evidencia reproducible |

## Interfaces / contratos

Comandos: `docker compose config`, `up -d --wait`, bootstrap, fixtures, `composer test|lint|analyse` y `pnpm exec playwright test`. Cada gate registra comando, versiones, salida y retorno en un directorio ignorado. Git incluye fuentes, ejemplos, locks y documentación; ignora `.env`, secretos, volúmenes, dumps, uploads, dependencias, core y evidencia. DESIGN no inicia Git ni hace push.

## Estrategia de pruebas

| Lote | Capa | Cobertura / enfoque |
|---|---|---|
| Primero | Smoke Compose | Validar configuración, healthchecks, HTTP, persistencia y fallo visible |
| Primero | PHPUnit + suite WordPress | Activación independiente, registro mínimo, capacidades y fixtures RED/GREEN |
| Primero | PHPCS/WPCS + PHPStan | Estilo, tipos y errores seguros; baseline solo justificado |
| Primero | Playwright + axe | Rutas mínimas, teclado, 320/768/1024/1440 y violaciones automáticas |
| Primero | Lighthouse CI | Baseline reproducible; umbrales finales cuando existan vistas completas |
| Posteriores | Integración/E2E/manual | Consultas, permisos, PDF, SMTP simulado, WCAG manual y umbrales 85/90 |

CI recreará `.env`, levantará Compose limpio, ejecutará gates y conservará reportes; una herramienta ausente no aprobará.

## Trazabilidad

| Spec | Decisiones que cubren escenarios |
|---|---|
| Entorno | Compose, configuración, fixtures, Git y rollback: arranque, reinicio, fallos, repetición e higiene |
| Arquitectura CMS | separación tema/plugin, fallback y suite WP: activación, cambio de tema, edición y extensibilidad |
| Experiencia pública | tema de bloques, fixtures y Playwright/axe: navegación, estados, responsive y accesibilidad |
| Documentos/contacto | plugin, PDF restringidos y pruebas posteriores: permisos, filtros, adjuntos y entrega simulada |
| Calidad/seguridad | gates, evidencia, pinning y revisión humana: ejecución parcial, entradas hostiles, WCAG y Lighthouse |

## Migración / despliegue

No hay migración. Tras aprobar hosting se validará la matriz, configurará SMTP fuera de Git y promoverán lotes respaldados.

## Preguntas abiertas

- [ ] ¿Qué hosting, PHP, DB, límites de subida, dominio y política de backups serán productivos?
- [ ] ¿Qué SMTP, destinatario, antispam y retención se autorizan?
- [ ] ¿Quién autoriza los PDF y los datos institucionales reales antes de publicarlos?
