# Especificación completa: Arquitectura CMS

## Requisitos

### Requirement: Componentes independientes en el primer incremento
El sistema MUST entregar un tema de bloques `labm` y un plugin `labm-core` activables de forma independiente, sin que la ausencia de uno produzca un error fatal en el otro.

#### Scenario: Activación independiente
- DADO un WordPress operativo con ambos componentes disponibles
- CUANDO se activa cada componente por separado
- ENTONCES la administración y el sitio siguen accesibles sin errores fatales.

#### Scenario: Cambio de tema
- DADO contenido de dominio creado mientras `labm-core` está activo
- CUANDO se activa otro tema
- ENTONCES el contenido, permisos y metadatos de dominio permanecen disponibles.

#### Scenario: Plugin inactivo
- DADO que `labm-core` está desactivado
- CUANDO el tema intenta mostrar una capacidad dependiente del dominio
- ENTONCES presenta una alternativa segura sin romper la vista ni inventar contenido.

### Requirement: Edición sin código
El sitio completo SHALL permitir a Editores y Administradores gestionar páginas, actualidad, selecciones, clubes, integrantes, horarios, datos institucionales y documentos mediante WordPress, respetando sus permisos.

#### Scenario: Publicación autorizada
- DADO un usuario con permisos editoriales suficientes
- CUANDO modifica, previsualiza y publica contenido
- ENTONCES el cambio aparece en la vista correspondiente y conserva su estructura responsive.

#### Scenario: Borrador
- DADO contenido guardado como borrador
- CUANDO un visitante consulta el sitio público
- ENTONCES el contenido no aparece ni queda enlazado por el sitio.

#### Scenario: Operación no autorizada
- DADO un usuario sin la capacidad requerida
- CUANDO intenta crear, publicar o eliminar contenido restringido
- ENTONCES la operación se rechaza sin modificar datos ni exponer detalles sensibles.

### Requirement: Contenido persistente y extensible
La funcionalidad de dominio SHALL sobrevivir a cambios de presentación y MUST admitir nuevas categorías o modalidades sin cambios de código.

#### Scenario: Nueva modalidad
- DADO un administrador autorizado
- CUANDO agrega una modalidad o categoría válida
- ENTONCES puede asignarla a contenido y verla en los filtros o agrupaciones correspondientes.

#### Scenario: Contenido sin presentación especializada
- DADO una categoría nueva sin diseño exclusivo
- CUANDO se publica contenido asociado
- ENTONCES se muestra mediante una presentación genérica usable.

#### Scenario: Identificador inválido
- DADO un valor que incumple las reglas editoriales o de seguridad
- CUANDO se intenta guardar como categoría, modalidad o metadato
- ENTONCES se rechaza o normaliza con un mensaje verificable y sin corrupción de datos.

### Requirement: Mantenibilidad editorial
Las cadenas visibles MUST estar preparadas para traducción, el español SHALL ser el idioma inicial y las actualizaciones de WordPress, tema o plugin SHOULD conservar personalizaciones y contenido.

#### Scenario: Interfaz inicial
- DADO una instalación nueva configurada en español
- CUANDO un visitante o editor recorre las funciones entregadas
- ENTONCES los textos propios aparecen en español y no contienen etiquetas técnicas expuestas.

#### Scenario: Texto no configurado
- DADO que falta un dato institucional
- CUANDO se renderiza una sección opcional
- ENTONCES se oculta o muestra un placeholder inequívocamente ficticio, sin presentarlo como real.

#### Scenario: Actualización incompatible
- DADO que una actualización no satisface la compatibilidad declarada
- CUANDO se ejecuta la validación previa
- ENTONCES la liberación se bloquea y se informa la incompatibilidad sin alterar el contenido.
