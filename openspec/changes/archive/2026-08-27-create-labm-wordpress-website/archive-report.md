# Informe de archivo ARCHIVE+

## Resultado

El cambio `create-labm-wordpress-website` queda archivado el 27 de agosto de 2026 mediante una exención explícita del usuario. No se ejecutaron pruebas, VERIFY, Docker ni BuildKit durante este archivo.

## Exención auditable

El informe VERIFY previo registraba 59 de 60 escenarios `COMPLIANT`. El único escenario restante, `calidad-seguridad / Gate satisfactorio`, se acepta expresamente como **no bloqueante** porque corresponde al gate agregado y no a una regresión funcional: las suites independientes, cobertura, WPCS, PHPStan, Compose, contratos, smokes y Playwright ya tenían evidencia positiva disponible. El usuario indicó que no era necesario relanzar ese gate ni repetir VERIFY.

Esta exención anula únicamente la precondición operativa de VERIFY para este ARCHIVE+; no convierte el escenario en `COMPLIANT` ni borra el resultado histórico del informe VERIFY.

## Alcance diferido y excluido

- Lighthouse y SEO quedan diferidos a un cambio futuro.
- La garantía integral WCAG 2.2 AA queda diferida; solo se conserva la evidencia concreta Playwright/axe ejecutada.
- La tarea 5.4 y la compatibilidad productiva (hosting, matriz WordPress/PHP/DB, SMTP real, PDF reales y datos reales) quedan fuera de alcance.

## Persistencia

Las cinco especificaciones delta se sincronizarán con `openspec/specs/` y el cambio completo se conservará bajo `openspec/changes/archive/2026-08-27-create-labm-wordpress-website/`.
