# Propuesta: Envío de correos de Contacto y corrección del catálogo

## Intención

Cerrar la verificación de Contacto y corregir cuatro pruebas bloqueantes del catálogo de Documentos, alineándolas con el contrato filtrable ya publicado.

## Alcance

### Incluye

- SMTP seguro, entrega, `Reply-To` y reintento de Contacto ya implementados.
- Actualizar las expectativas del patrón y las pruebas PHP de Documentos al catálogo filtrable actual.
- Verificar consulta vacía, filtros, paginación y exclusión de adjuntos inválidos.

### Excluye

- Cambios al diseño, campos o ruta de Contacto.
- Cambiar el comportamiento público del catálogo, migraciones o adjuntos.

## Enfoque

Mantener el catálogo actual, que toma filtros de la solicitud y usa «No encontramos documentos». Las pruebas heredadas se ajustarán para comprobar ese contrato en vez del catálogo simple sin filtros.

## Áreas afectadas

| Área | Impacto | Descripción |
|---|---|---|
| `tests/php/PublicExperienceTest.php` | Modificado | Patrón con filtros actuales. |
| `tests/php/DocumentContactTest.php` | Modificado | Catálogo filtrable y mensaje vacío. |
| `tests/php/VerifyCorrectivesTest.php` | Modificado | Consulta vacía vigente. |
| `docs/development.md` | Modificado | Contrato de pruebas del catálogo, si procede. |

## Riesgos

| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| Corregir una expectativa y ocultar regresión | Baja | Pruebas focales con resultados filtrados y vacíos. |

## Plan de reversión

Restaurar los archivos de prueba y documentación modificados. No hay cambios de datos, configuración ni comportamiento público.

## Dependencias

- Suite PHPUnit y fixtures de Documentos disponibles localmente.

## Criterios de éxito

- [ ] Las cuatro pruebas bloqueantes verifican el contrato actual y pasan.
- [ ] La suite PHPUnit completa pasa sin alterar el catálogo público.
- [ ] Se mantienen LF y `git diff --check` correcto.
