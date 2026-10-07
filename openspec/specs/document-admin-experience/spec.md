# Especificación completa: Experiencia administrativa de documentos

## Requisitos

### Requirement: Asociación visual del PDF
La asociación MUST corresponder exactamente al PDF elegido por la persona autorizada; nombre y tamaño mostrados MUST coincidir con el archivo realmente asociado. Una colisión de nombres MAY modificar el nombre almacenado, pero MUST NOT cambiar la identidad de la selección ni eliminar archivos ajenos.

El sistema MUST permitir crear o editar un Documento en wp-admin seleccionando o subiendo un PDF desde la biblioteca, sin ver ni manipular identificadores o URL.

#### Scenario: PDF reemplazado
- DADO un Documento con PDF previo y otro PDF válido disponible
- CUANDO se selecciona y guarda el reemplazo
- ENTONCES se asocia el PDF elegido y se muestran su nombre real, tamaño y validez.

#### Scenario: Nombre ya utilizado
- DADO dos archivos distintos con el mismo nombre solicitado
- CUANDO se carga y selecciona el segundo
- ENTONCES la asociación corresponde al segundo y muestra su nombre real sin sobrescribir el primero.

#### Scenario: Carga fallida o selección inválida
- DADO una asociación previa y un archivo inválido o fallo de carga
- CUANDO se intenta el reemplazo
- ENTONCES se conserva la asociación previa y aparece un error accionable sin datos internos.

#### Escenario: Selección satisfactoria
- DADO un usuario autorizado en la edición de un Documento
- CUANDO selecciona o sube un PDF válido
- ENTONCES el archivo queda asociado y se muestran su nombre, tamaño y estado válido

#### Escenario: Reemplazo y retiro
- DADO un Documento con un PDF asociado
- CUANDO el usuario lo reemplaza o lo quita
- ENTONCES la interfaz refleja la nueva selección o la ausencia del archivo sin eliminar adjuntos de la biblioteca

#### Escenario: Fallo de la biblioteca
- DADO que la selección o carga no puede completarse
- CUANDO la operación falla
- ENTONCES se conserva la asociación previa y se presenta un mensaje accionable

### Requisito: Información y límite antes de guardar

El sistema MUST informar que el límite efectivo es el menor entre 30 MB y el permitido por WordPress, y MUST mostrar nombre, tamaño y validez del archivo elegido.

#### Escenario: Archivo dentro del límite
- DADO un PDF cuyo tamaño no supera el límite efectivo
- CUANDO se selecciona
- ENTONCES se muestran sus datos y un estado textual de validez

#### Escenario: Límite menor de WordPress
- DADO que WordPress permite menos de 30 MB
- CUANDO se abre el control del PDF
- ENTONCES se informa ese valor menor como límite efectivo

#### Escenario: Archivo sobredimensionado
- DADO un archivo que supera el límite efectivo
- CUANDO se intenta asociar
- ENTONCES se rechaza con un mensaje que indica el límite y cómo corregirlo

### Requisito: Operación accesible

El sistema MUST permitir completar selección, carga, reemplazo, retiro y corrección usando teclado, con etiquetas, foco visible y mensajes perceptibles sin depender solo del color.

#### Escenario: Flujo completo por teclado
- DADO un usuario que no utiliza puntero
- CUANDO recorre y activa todos los controles
- ENTONCES completa la asociación y el foco sigue un orden comprensible

#### Escenario: Ayuda contextual
- DADO un control sin error
- CUANDO recibe foco
- ENTONCES su etiqueta, ayuda y estado son identificables por tecnología de asistencia

#### Escenario: Error accesible
- DADO un campo inválido
- CUANDO se intenta guardar
- ENTONCES el mensaje se anuncia, identifica el campo y permite llevar el foco a la corrección

## Límites

Esta especificación excluye frontend, catálogo, Pencil, paginación, iconos y render público.
