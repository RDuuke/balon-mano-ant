# Delta: calidad y seguridad

## ADDED Requirements

### Requirement: Gate global íntegro y reproducible
La aceptación MUST ejecutar el gate oficial completo con navegador, pruebas PHP e integración, estándares y análisis estático. MUST exigir cobertura PHP fresca de al menos 80 %, cero errores de estándares/análisis y pruebas satisfactorias sin omisiones derivadas de fallos. MUST NOT reducir umbrales, fuentes, suites ni garantías para obtener aprobación.

#### Scenario: Ejecución completa satisfactoria
- DADO entorno limpio y todas las herramientas disponibles
- CUANDO se ejecuta el gate oficial completo
- ENTONCES cada etapa aprueba y la evidencia identifica comandos, resultados y cobertura fresca mínima de 80 %.

#### Scenario: Exactamente el umbral
- DADO cobertura fresca de 80 % y demás etapas satisfactorias
- CUANDO se evalúa el gate
- ENTONCES aprueba cobertura sin ampliar la conclusión a auditorías no realizadas.

#### Scenario: Fallo, omisión o evidencia antigua
- DADO una etapa fallida, omitida por fallo o cobertura de otra ejecución
- CUANDO se revisa la aceptación
- ENTONCES falla y conserva el diagnóstico sin declarar el cambio completo.

### Requirement: Evidencia de ramas y aislamiento
La cobertura adicional MUST demostrar comportamientos reales de éxito, borde y error. Las pruebas mutables MUST delimitar recursos propios y restaurar asociaciones previas; su limpieza MUST ser idempotente y preservar contenido ajeno. La implementación posterior MUST aportar evidencia TDD strict RED/GREEN por tarea.

#### Scenario: Prueba mutable satisfactoria
- DADO recursos propios identificados y asociaciones previas registradas
- CUANDO termina una prueba
- ENTONCES elimina sus recursos temporales y restaura el estado previo sin alterar contenido ajeno.

#### Scenario: Limpieza repetida
- DADO una prueba ya limpiada
- CUANDO se repite su limpieza
- ENTONCES conserva el mismo estado sin errores por ausencia ni eliminaciones ajenas.

#### Scenario: Prueba interrumpida
- DADO un fallo después de crear recursos temporales
- CUANDO se recupera el entorno
- ENTONCES se identifican y limpian solo esos recursos y el fallo permanece registrado.

### Requirement: Persistencia y continuidad entre cambios
Si se altera estado persistido, MUST sincronizarlo por el proceso oficial, verificar versión/hash y excluir datos temporales, cuentas y credenciales del canónico. MUST conservar snapshots y pendientes de Selecciones y Detalle; MUST entregar un handoff explícito sin archivarlos automáticamente.

#### Scenario: Estado persistido modificado
- DADO runtime limpio tras una modificación persistente autorizada
- CUANDO se prepara la entrega
- ENTONCES el paquete oficial y su metadata coinciden en versión/hash y excluyen datos temporales y sensibles.

#### Scenario: Cambio sin persistencia
- DADO únicamente cambios de código o artefactos
- CUANDO se prepara la entrega
- ENTONCES se documenta que no procede sincronización y se conserva el canónico existente.

#### Scenario: Contaminación o cierre ajeno
- DADO datos de prueba en el paquete o intento de cerrar Selecciones/Detalle por el gate global
- CUANDO se evalúa la entrega
- ENTONCES se rechaza hasta limpiar el paquete o preservar sus estados y emitir el handoff correspondiente.
