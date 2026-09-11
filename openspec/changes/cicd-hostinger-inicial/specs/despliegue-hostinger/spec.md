# Especificación completa: Despliegue inicial en Hostinger

## Requisitos

### Requirement: Despliegue selectivo condicionado por calidad
El sistema MUST desplegar a Hostinger únicamente después de una verificación de calidad satisfactoria originada por un `push` a `main`. SHALL publicar solo el tema LABM y el plugin `labm-core`, y MUST preservar el núcleo de WordPress, su configuración, el contenido existente y `uploads` remotos.

#### Scenario: Publicación aprobada
- DADO un `push` a `main` cuyas verificaciones de calidad terminan correctamente
- CUANDO se ejecuta el despliegue
- ENTONCES el tema y el plugin publicados reflejan esa revisión sin modificar las demás áreas remotas.

#### Scenario: Evento fuera de alcance
- DADO una solicitud de extracción o un `push` en una rama distinta de `main`
- CUANDO concluyen las verificaciones aplicables
- ENTONCES no se inicia ninguna publicación ni modificación en Hostinger.

#### Scenario: Calidad fallida
- DADO un `push` a `main` con una verificación de calidad fallida
- CUANDO finaliza el flujo
- ENTONCES el despliegue queda bloqueado y el estado informa el fallo sin publicar archivos.

### Requirement: Migración inicial canónica, recuperable e idempotente
El sistema MUST migrar el contenido canónico y sus medios a la instalación ya existente de Hostinger una sola vez, tras validar su integridad y compatibilidad. SHALL adaptar las URLs de contenido a `http://palevioletred-salamander-919245.hostingersite.com`, conservar una recuperación previa y registrar un marcador persistente solo al terminar satisfactoriamente toda la migración.

#### Scenario: Primera migración válida
- DADO WordPress instalado, un paquete canónico válido y ausencia de marcador
- CUANDO se ejecuta la migración inicial posterior al despliegue
- ENTONCES el contenido, opciones y medios quedan disponibles con la URL temporal y se registra el marcador de versión.

#### Scenario: Migración ya completada
- DADO que el marcador persistente identifica una migración canónica completada
- CUANDO se ejecuta un despliegue posterior
- ENTONCES se omite la migración y se preservan el contenido y los medios remotos.

#### Scenario: Paquete o acceso inválido
- DADO un paquete no íntegro, incompatible o una conexión remota no disponible
- CUANDO se intenta la migración inicial
- ENTONCES el flujo falla con evidencia diagnóstica, no registra el marcador y deja disponible la recuperación previa.

### Requirement: Secretos y smoke autenticado sin exposición
El sistema MUST obtener `FTP_SERVER`, `FTP_USERNAME`, `FTP_PASSWORD`, `DB_HOST`, `DB_NAME`, `DB_USERNAME`, `DB_PASSWORD`, `WP_USER` y `WP_PASSWORD` exclusivamente desde secretos de GitHub. MUST usar `WP_USER` y `WP_PASSWORD` solo para un smoke autenticado de WordPress, y MUST NOT versionar, mostrar ni incluir sus valores en registros, artefactos o mensajes de error.

#### Scenario: Smoke autenticado satisfactorio
- DADO un despliegue o migración completados y credenciales válidas de WordPress
- CUANDO se ejecuta el smoke autenticado
- ENTONCES se confirma el acceso autorizado y el resultado no revela valores de credenciales.

#### Scenario: Secreto ausente
- DADO que falta uno de los secretos requeridos
- CUANDO el flujo alcanza la operación que lo requiere
- ENTONCES falla antes de esa operación, identifica solo el nombre del secreto ausente y no expone ningún valor.

#### Scenario: Credencial inválida o salida sensible
- DADO una credencial de WordPress inválida o una salida que contenga un valor secreto
- CUANDO se ejecuta el smoke o se procesa el resultado
- ENTONCES el flujo falla, protege el valor sensible y no realiza acciones administrativas con `WP_USER` ni `WP_PASSWORD`.
