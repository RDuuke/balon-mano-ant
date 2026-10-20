# Seguimiento de revisión del manual

## Objetivo

Organizar la revisión de los apartados del manual entre las dos personas responsables y registrar el avance y las observaciones de cada revisión.

## Responsables

- **Daniel Carcamo:** revisará cuatro apartados consecutivos comenzando por «Actualidad».
- **Juan Duque:** revisará cuatro apartados consecutivos comenzando por «Aliados Oficiales».

La distribución sigue el orden de trabajo acordado y no implica cambiar la numeración global de la cartilla.

## Seguimiento

Los únicos estados permitidos son **no iniciada**, **en proceso** y **finalizada**. El estado inicial de cada apartado es **no iniciada**; la tabla registra el avance de la revisión personal de su responsable.

| Responsable | Apartado | Alcance | Estado |
|---|---|---|---|
| Daniel Carcamo | Actualidad | Revisión del apartado | finalizada |
| Daniel Carcamo | Selecciones | Revisión del apartado | finalizada |
| Daniel Carcamo | Documentos | Revisión del apartado | no iniciada |
| Daniel Carcamo | Slides de Inicio | Revisión del apartado | no iniciada |
| Juan Duque | Aliados Oficiales | Revisión del apartado | no iniciada |
| Juan Duque | Clubes | Revisión del apartado | no iniciada |
| Juan Duque | Integrantes | Revisión del apartado | no iniciada |
| Juan Duque | Nosotros | Revisión del apartado | no iniciada |

## Reglas de actualización

- Cambia el estado de cada fila según el avance: **no iniciada**, **en proceso** o **finalizada**.
- Al comenzar la revisión, marca el apartado **en proceso**; márcalo **finalizada** cuando la revisión haya concluido.
- Añade las observaciones debajo de la tabla, indicando el apartado y, si aplica, la página o elemento relacionado.
- Registra observaciones concretas y accionables. No borres observaciones previas; añade una nueva nota o actualiza la existente dejando claro el cambio.

## Observaciones

<!-- Añadir aquí notas con el formato: Apartado — observación — fecha (opcional). -->
Actualidad — Revisión realizada y documentada. — 2026-10-15

### Preparación documental de los apartados de Juan — versión 1.5 (2026-10-19)

Se actualizaron el [manual escrito](manual-usuario/manual-usuario.md), la fuente HTML y la [cartilla PDF](manual-usuario/cartilla-manual-usuario-labm.pdf) para personal no técnico. Esta preparación no equivale a la revisión personal de Juan: sus cuatro apartados conservan **no iniciada** hasta que comience su recorrido.

| Apartado | Mejoras incorporadas | Páginas de la cartilla | Revisión que queda pendiente |
|---|---|---|---|
| Aliados Oficiales | Guía propia para crear, reemplazar el logo, ordenar, modificar y retirar; comprobación en Inicio y problemas frecuentes. | 10 y 10A | Comprobar que las instrucciones y el resultado público sean claros para una persona no técnica. |
| Clubes | Pasos para nombre, logo, descripción, extracto y modalidad; límite de seis clubes en Inicio; ciudad como dato interno opcional. | 15 y 15A | Revisar creación y modificación, diferencia entre resumen y descripción, y comprobación de Inicio y página individual. |
| Integrantes | Ejemplo ficticio Ana Pérez / Entrenadora; registro y actualización de `labm_cargo`; fotografía, grupo, filtros y causas de ausencia. | 11 y 11A | Revisar el recorrido del cargo y la comprobación de la tarjeta y el grupo en Nosotros. |
| Nosotros | Localización de las entradas por identificador, edición separada de presentación y Misión/Visión, prioridad del extracto y comprobación pública. | 12, 12A y 12B | Confirmar que se pueda localizar la entrada correcta y modificar el texto sin alterar su identificador. |

Validación documental realizada: PDF de 23 páginas (cinco adicionales), 25 enlaces internos con destino válido, índice actualizado y revisión visual de páginas modificadas y adyacentes. Se comprobaron ausencia de desbordamientos y superposiciones, finales LF y `git diff --check`. Los detalles y límites constan en la [auditoría de limpieza y mejoras del manual](auditoria-limpieza-codigo.md#manual-y-cartilla--adaptación-para-personal-no-técnico-versión-15-2026-10-19).

Se reutilizaron capturas reales existentes con acercamientos; los ejemplos nuevos están identificados como ficticios. No se probó guardar ni publicar en WordPress, ni se cambiaron su contenido, ajustes o archivos. No se modificaron los estados ni la asignación de los apartados de Daniel.
