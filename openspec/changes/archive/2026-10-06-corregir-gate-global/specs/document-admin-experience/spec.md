# Delta: experiencia administrativa de documentos

## MODIFIED Requirements

### Requirement: Asociación visual del PDF
La asociación MUST corresponder exactamente al PDF elegido por la persona autorizada; nombre y tamaño mostrados MUST coincidir con el archivo realmente asociado. Una colisión de nombres MAY modificar el nombre almacenado, pero MUST NOT cambiar la identidad de la selección ni eliminar archivos ajenos.

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
