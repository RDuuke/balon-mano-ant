# Especificación completa: Entorno de desarrollo

## Requisitos

### Requirement: Primer incremento reproducible
El sistema MUST permitir levantar WordPress y su base de datos mediante Docker Compose en un equipo nuevo que tenga Docker instalado.

#### Scenario: Arranque limpio
- DADO un equipo sin instalación previa del proyecto
- CUANDO una persona sigue los comandos documentados
- ENTONCES WordPress y la base de datos alcanzan un estado saludable y el sitio responde.

#### Scenario: Reinicio con persistencia
- DADO un sitio inicializado con contenido dummy
- CUANDO los servicios se detienen y vuelven a iniciar sin solicitar borrado
- ENTONCES la configuración y el contenido persisten.

#### Scenario: Dependencia no disponible
- DADO que un servicio requerido no puede iniciar
- CUANDO se ejecuta el arranque
- ENTONCES el proceso falla de forma visible y ofrece evidencia diagnóstica reproducible.

### Requirement: Configuración y ciclo de vida seguros
El repositorio SHALL ofrecer configuración de ejemplo sin secretos y SHALL documentar inicio, parada, reinicio, inspección, pruebas y eliminación voluntaria de datos locales.

#### Scenario: Configuración desde ejemplo
- DADO un clon nuevo del repositorio
- CUANDO se prepara la configuración siguiendo el ejemplo
- ENTONCES todos los valores requeridos quedan identificados sin credenciales reales versionadas.

#### Scenario: Personalización local
- DADO que una persona cambia puertos o credenciales solo en su configuración local
- CUANDO inicia el entorno
- ENTONCES el ejemplo compartido permanece inalterado y el entorno usa los valores locales.

#### Scenario: Configuración incompleta
- DADO que falta un valor obligatorio
- CUANDO se intenta iniciar o validar el entorno
- ENTONCES se informa el valor ausente sin revelar secretos ni continuar con un estado ambiguo.

### Requirement: Fixtures ficticios e idempotentes
El primer incremento MUST proporcionar datos dummy suficientes para comprobar sitio, tema y plugin; SHALL marcarlos como ficticios y SHALL evitar datos personales o institucionales extraídos de los PDF.

#### Scenario: Carga inicial
- DADO un sitio vacío
- CUANDO se cargan los fixtures
- ENTONCES existen muestras ficticias identificables para las vistas y estados críticos del primer incremento.

#### Scenario: Carga repetida
- DADO que los fixtures ya fueron cargados
- CUANDO se ejecuta nuevamente la carga
- ENTONCES no aparecen duplicados ni se sobrescribe contenido editorial ajeno a los fixtures.

#### Scenario: Fuente sensible
- DADO que existen PDF pendientes de revisión en `docs/`
- CUANDO se generan o actualizan fixtures
- ENTONCES ningún dato personal de esos archivos se publica, copia o presenta como contenido real.

### Requirement: Higiene del repositorio
El proyecto MUST excluir secretos, configuración local real, volúmenes, dumps, uploads y núcleo mutable generado; esta fase SHALL NOT realizar commit ni push al remoto público.

#### Scenario: Estado versionable
- DADO un entorno usado localmente
- CUANDO se inspeccionan los archivos candidatos a versión
- ENTONCES solo quedan configuración reproducible y personalizaciones fuente permitidas.

#### Scenario: Archivos locales existentes
- DADO que existen datos o uploads locales
- CUANDO se reinicia el entorno
- ENTONCES pueden persistir localmente sin convertirse en archivos versionables.

#### Scenario: Secreto accidental
- DADO que un archivo real de entorno o credencial queda en el área de trabajo
- CUANDO se ejecuta la validación de higiene
- ENTONCES se detecta antes de cualquier operación con el remoto y la validación falla.
