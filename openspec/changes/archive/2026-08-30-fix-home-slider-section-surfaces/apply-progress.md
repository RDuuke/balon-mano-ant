# Progreso de APPLY

## Tarea QUICK-1 — Superficies semánticas de la portada

- **RED:** test `tests/e2e/home.spec.ts::secciones del inicio usan las superficies exactas del diseño` falla porque `evento` recibe `rgba(0, 0, 0, 0)` en lugar de `rgb(0, 0, 0)` en los cuatro proyectos Playwright.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`; 64 pruebas Playwright pasan.
- **REFACTOR:** se sustituyeron las clases desalineadas y la alternancia posicional por selectores `data-labm-section` estables ante secciones ausentes.

## Tarea QUICK-2 — Franja segura del slider

- **RED:** test `tests/e2e/home.spec.ts::slider muestra dots circulares alineados y sin desborde vertical normal` falla porque el contenido invade la franja de controles por 13,28 px a 1200 px y por 10,78 px a 1440 px.
- **GREEN:** implementación en `wp-content/themes/labm/style.css`; la prueba focalizada pasa y la regresión final termina con 64 de 64 pruebas Playwright aprobadas.
- **TRIANGULATE:** el test comprueba separación, altura, indicadores y alineación en 320, 390, 768, 1200 y 1440 px.
- **REFACTOR:** se acotó la escala tipográfica del título a `clamp(2.25rem, 4.5vw, 4rem)` sin truncar el contenido editorial.

## Evidencia visual

- Capturas finales: `artifacts/apply-home-320.png`, `artifacts/apply-home-768.png`, `artifacts/apply-home-1024.png` y `artifacts/apply-home-1440.png`.
- La secuencia de superficies coincide con el frame `Inicio — Desktop` del archivo Pen: blanco, verde suave, negro, blanco, negro y verde suave.
- No se observa desborde horizontal del documento ni solapamiento del contenido del slider con sus controles en los anchos exigidos.
- Se detecta un recorte preexistente de enlaces de aliados en 320 y 768 px causado por `.labm-allies { overflow: hidden }` junto con una lista flex sin salto; no fue introducido por este APPLY y no forma parte del alcance de superficies del quick.

## Tarea QUICK-FIX-1 — Normalización focalizada de finales de línea

- **RED:** el gate de VERIFY falla en `composer-lint` con `Generic.Files.LineEndings.InvalidEOLChar` porque cinco archivos PHP contienen CRLF.
- **GREEN:** se convirtieron exclusivamente esos cinco archivos de CRLF a LF; PHPCS focalizado termina con código 0.
- **REFACTOR:** no hubo cambio de comportamiento; `git diff --ignore-space-at-eol --exit-code` termina con código 0 para los cinco archivos y la inspección binaria confirma cero bytes CR restantes.

## Tarea QUICK-FIX-2 — Normalización final de archivos del quick

- **RED:** la inspección binaria detecta 154 bytes CR en `tests/e2e/home.spec.ts` y 118 bytes CR en `wp-content/themes/labm/style.css`.
- **GREEN:** se eliminaron únicamente los bytes CR de pares CRLF; ambos archivos quedan con cero bytes CR y conservan el mismo `git diff --numstat` previo (8/0 y 8/9), evidencia de equivalencia de contenido salvo finales de línea.
- **REFACTOR:** no hubo cambios semánticos adicionales; `git diff --check` termina con código 0.

## Tarea QUICK-FIX-3 — Política general de niveles de prueba

- **RED:** `openspec/config.yaml` no declaraba niveles de prueba ni el carácter consultivo de Lighthouse, por lo que VERIFY podía tratarlo como bloqueante en cualquier quick.
- **GREEN:** se declararon los niveles `quick_local`, `verify_pr` y `lighthouse`; el YAML se carga correctamente y conserva `scripts/gate.ps1 -IncludeBrowser` como gate canónico de VERIFY/PR.
- **REFACTOR:** Lighthouse queda como observabilidad consultiva en `nightly_release`, con fallos de infraestructura no bloqueantes y bloqueo condicional solo en releases o cambios de alto impacto definidos.
