# Reporte de aplicación

## Resultado

Se aplicó el contenido centrado con máximo de 1200 px, gutters compartidos y breakpoints base/768/1024. El slider conserva una altura exterior fija por viewport, reserva espacio para controles y permite desplazamiento interno ante contenido extremo. `home.js` permanece intacto.

## Validación

- Gate PHP/estático: compose, PHPUnit, integración WordPress, cobertura, WPCS y PHPStan en PASS.
- Playwright: 56/56 pruebas aprobadas en 320, 768, 1024 y 1440 px; el recorrido geométrico incluye 1200 px.
- Axe: baseline sin violaciones en los recorridos existentes.

## Reaplicación tras VERIFY fallido

Los cinco bloqueos reportados se reprodujeron cuando Playwright accedió mediante `host.docker.internal`, pero WordPress generó las URL absolutas de `style.css` y `home.js` con `localhost`. Desde el contenedor del navegador esos activos no eran alcanzables. La ausencia simultánea de CSS y JavaScript produjo los falsos fallos de gutter, overflow, tamaño de controles, `aria-current` y movimiento reducido.

No fue necesario modificar código de producto: `scripts/browser-gate.ps1` ya sincroniza temporalmente `home` y `siteurl` con `WP_URL` y los restaura al finalizar. Con ese entorno coherente, el subset pasa 5/5, Playwright completo pasa 44/44 y PHPUnit pasa 1/1 (2 aserciones). La altura del slider permanece estable.

La siguiente verificación debe invocar el script como archivo (`pwsh -NoProfile -File scripts/browser-gate.ps1 -Task playwright`) y no evaluar su contenido como `scriptblock`, porque esa variante pierde `$PSScriptRoot`.

## Alcance y rollback

Se conservaron fondos de ancho completo, identidad y composición existentes. No se sustituyó el slider ni se cambiaron datos o administración. Para revertir, restaurar conjuntamente `theme.json`, `style.css`, `functions.php` y las pruebas modificadas; no hay migración de datos.
