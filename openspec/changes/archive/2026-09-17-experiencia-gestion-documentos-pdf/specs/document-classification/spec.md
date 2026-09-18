# Especificación completa: Clasificación y compatibilidad de documentos

## Requisitos

### Requisito: Tipo único de documento

El sistema MUST permitir elegir un solo tipo entre Documento general, Acta, Certificado, Circular, Resolución, Reglamento, Informe, Convocatoria y Otro.

#### Escenario: Selección de tipo
- DADO un editor de Documento
- CUANDO selecciona uno de los tipos disponibles y guarda
- ENTONCES queda asociado exactamente ese tipo

#### Escenario: Nuevo documento sin selección
- DADO un Documento nuevo sin tipo elegido
- CUANDO se guarda con los demás datos válidos
- ENTONCES se asocia Documento general

#### Escenario: Selección múltiple o desconocida
- DADO que una solicitud contiene varios tipos o uno no disponible
- CUANDO intenta guardar
- ENTONCES se rechaza la clasificación sin alterar la asociación previa

### Requisito: Gobierno del vocabulario

El sistema MUST permitir que administradores gestionen los tipos y que editores solo seleccionen tipos existentes.

#### Escenario: Administración autorizada
- DADO un administrador con la capacidad requerida
- CUANDO crea, renombra o retira un tipo
- ENTONCES el vocabulario queda actualizado para selecciones posteriores

#### Escenario: Editor selecciona
- DADO un editor sin permiso para gestionar tipos
- CUANDO edita un Documento
- ENTONCES puede consultar y elegir un tipo existente

#### Escenario: Gestión no autorizada
- DADO un editor sin capacidad administrativa
- CUANDO intenta crear, renombrar o retirar un tipo
- ENTONCES se rechaza la operación sin modificar el vocabulario

### Requisito: Compatibilidad con documentos históricos

El sistema MUST conservar documentos y asociaciones existentes sin migración masiva ni despublicación automática, y MUST presentar Documento general como fallback administrativo cuando falte el tipo.

#### Escenario: Histórico válido
- DADO un Documento existente con ID numérico almacenado como texto y PDF válido
- CUANDO se abre para editar
- ENTONCES se muestran el PDF y sus datos sin exigir migración manual

#### Escenario: Histórico sin tipo
- DADO un Documento existente sin clasificación
- CUANDO se abre en administración
- ENTONCES se muestra Documento general sin modificarlo hasta un guardado explícito

#### Escenario: Histórico con PDF inválido
- DADO un Documento publicado cuyo adjunto falta o es inválido
- CUANDO se despliega la mejora o se intenta editar
- ENTONCES no se despublica automáticamente, pero el guardado exige reparar el PDF y preserva los datos si falla

### Requisito: Fallos sin pérdida de información

El sistema MUST preservar los valores previamente persistidos cuando falle cualquier validación o autorización.

#### Escenario: Corrección satisfactoria
- DADO un intento fallido con datos de formulario aún visibles
- CUANDO el usuario corrige los campos y reintenta
- ENTONCES se guardan los valores corregidos sin duplicar el Documento

#### Escenario: Reintento sin cambios
- DADO un error temporal sin una nueva selección
- CUANDO el usuario vuelve a la edición
- ENTONCES permanecen los datos persistidos antes del fallo

#### Escenario: Validación rechazada
- DADO una combinación inválida de PDF, fecha o tipo
- CUANDO el servidor la rechaza
- ENTONCES no se aplica ninguna mutación parcial ni se pierde la versión previa

## Límites

Este dominio no modifica catálogo, paginación, iconos ni render público.
