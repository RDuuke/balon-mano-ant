# Delta: calidad y seguridad

## ADDED Requirements

### Requirement: Gate reproducible con navegador focal autorizado
Solo para `corregir-gate-global`, la autorización explícita «Sí, acotar y detener la suite completa» MUST prevalecer sobre la exigencia de suite Playwright completa de `openspec/config.yaml` y los artefactos previos de este cambio. La aceptación browser MUST cubrir el checklist de pruebas históricamente fallidas y pruebas afectadas por las correcciones, sin exigir las 220 pruebas Playwright. Cada entrada MUST mapear fallo o cambio afectado, escenario/prueba y evidencia PASS real actual o reutilizada válida, identificando ejecución, resultado y vigencia respecto del código y entorno evaluados. MUST NOT omitir entradas por fallo ni atribuir PASS a pruebas no ejecutadas.

Pruebas PHP e integración, cobertura PHP fresca de al menos 80 % y cero errores de estándares/análisis MUST conservarse. Evidencias PASS válidas de builds, unitarias, lint y análisis MUST reutilizarse sin repetirlas; los resultados anteriores MUST permanecer intactos. Esta excepción solo acota navegador y MUST NOT reducir umbrales PHP, alterar fuentes/suites o extenderse a otros cambios.

#### Scenario: Checklist focal satisfactorio
- DADO checklist de fallos históricos y pruebas afectadas, con mapping y evidencia vigente
- CUANDO se evalúa el cierre autorizado con navegador focal
- ENTONCES todas sus entradas tienen PASS real trazable, se conservan las demás etapas PASS y la cobertura fresca mínima de 80 %, sin exigir 220 pruebas.

#### Scenario: Exactamente el umbral
- DADO cobertura fresca de 80 % y demás etapas satisfactorias
- CUANDO se evalúa el gate
- ENTONCES aprueba cobertura sin ampliar la conclusión a auditorías no realizadas.

#### Scenario: Fallo, omisión o evidencia inválida
- DADO una entrada focal fallida, sin mapping o sin evidencia vigente, una etapa requerida fallida o cobertura de otra ejecución
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
