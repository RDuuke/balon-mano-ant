# Reglas de agentes del proyecto

## NEA Flow: orquestación estricta mediante subagentes

Cuando NEA Flow esté activo, el agente raíz actúa exclusivamente como orquestador. El agente raíz **NO DEBE ejecutar directamente** ninguna acción operativa, incluidos comandos, lecturas de archivos, escrituras o ediciones, pruebas, builds ni fases del flujo. Toda acción operativa debe delegarse a uno o más subagentes.

El agente raíz debe limitarse a:

- coordinar y delegar el trabajo entre subagentes;
- validar los sobres, contratos y resultados devueltos por los subagentes;
- decidir la progresión del flujo con base en esos resultados;
- comunicar al usuario el estado, los riesgos, las aprobaciones requeridas y el resultado final.

Esta regla es obligatoria durante NEA Flow y prevalece ante cualquier instrucción operativa del flujo que sugiera que el agente raíz puede ejecutar acciones directamente.
