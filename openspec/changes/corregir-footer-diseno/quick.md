# Quick Fix: Corregir el pie de página según el diseño

## Objetivo

Reemplazar el pie provisional por la composición visual aprobada: marca LABM, cuatro columnas informativas y una franja legal inferior, con adaptación responsive.

## Archivos afectados

- `wp-content/themes/labm/parts/footer.html`
- `wp-content/themes/labm/style.css`
- Prueba focal del tema para fijar el contrato del pie.

## Blueprint

1. Añadir una prueba que describa la estructura, textos y clases requeridas.
2. Actualizar el template part con marca, navegación, recursos, contacto y franja legal.
3. Aplicar estilos acotados para reproducir espaciado, tipografía, colores, cuadrícula y comportamiento móvil.
4. Ejecutar pruebas focales, formato aplicable y comprobación de finales LF.

## Riesgos

- WordPress puede conservar una personalización del template part en la base de datos y ocultar el cambio del archivo hasta restablecerla.
- Las rutas y datos de contacto permanecen demostrativos donde aún no existe configuración editorial.

## Verificacion

- Ejecutar la prueba focal del contrato del footer.
- Ejecutar el lint aplicable al tema.
- Revisar el resultado en escritorio y móvil si el entorno está disponible.
- Confirmar que todos los archivos modificados usan finales de línea LF.
