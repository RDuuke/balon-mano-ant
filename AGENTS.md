# Reglas de agentes del proyecto

## Finales de línea obligatorios

- Todo archivo de texto o código creado o modificado debe guardarse con finales de línea LF (`\n`), nunca CRLF (`\r\n`).
- Los agentes no deben introducir CRLF, aunque trabajen desde Windows o la herramienta usada lo adopte por defecto.
- Antes de cerrar cualquier fase o entregar cambios, el agente ejecutor debe verificar los finales de línea de todos los archivos modificados y corregir cualquier CRLF introducido por su trabajo.
- La corrección debe limitarse a los archivos modificados dentro del alcance vigente; no se deben normalizar archivos ajenos de forma masiva.

## NEA Flow: orquestación estricta mediante subagentes

Cuando NEA Flow esté activo, el agente raíz actúa exclusivamente como orquestador. El agente raíz **NO DEBE ejecutar directamente** ninguna acción operativa, incluidos comandos, lecturas de archivos, escrituras o ediciones, pruebas, builds ni fases del flujo. Toda acción operativa debe delegarse a uno o más subagentes.

El agente raíz debe limitarse a:

- coordinar y delegar el trabajo entre subagentes;
- validar los sobres, contratos y resultados devueltos por los subagentes;
- decidir la progresión del flujo con base en esos resultados;
- comunicar al usuario el estado, los riesgos, las aprobaciones requeridas y el resultado final.

Esta regla es obligatoria durante NEA Flow y prevalece ante cualquier instrucción operativa del flujo que sugiera que el agente raíz puede ejecutar acciones directamente.
