# Diseño: Administrar la sección Nuestra Liga desde WordPress

## Enfoque técnico

Se ampliará `labm-core` con una opción editorial cerrada llamada `labm_home_settings` y una pantalla hija de **LABM → Inicio**. La pantalla reutilizará el patrón administrativo existente de `class-labm-footer-settings.php`: esquema declarado en PHP, valores predeterminados, saneamiento en la Settings API, capacidad `edit_theme_options` y formulario generado desde el esquema.

El patrón `labm/inicio` seguirá siendo responsable de la composición pública. Antes de escribir el marcado resolverá la configuración efectiva mediante `labm_core_get_home_settings()` cuando LABM Core esté activo; si la función no existe, usará los mismos valores predeterminados locales que ya están presentes en el patrón. Así, la desactivación del plugin no provoca un error fatal ni elimina el bloque.

La configuración pública no permitirá HTML ni shortcodes. Los siete valores se escaparán en el contexto de texto, atributo o URL. El CTA se renderizará únicamente cuando la URL resultante sea segura y no esté vacía; el resto del bloque conservará su marcado, clases, orden y jerarquía actuales.

## Decisiones de arquitectura

### Decisión: Extender el módulo de contenido de portada

**Elección:** añadir el esquema y el contrato de lectura de Nuestra Liga a `class-labm-home-content.php`.

**Alternativas:** crear una clase o archivo independiente de ajustes de portada; guardar los textos en el tema; usar campos personalizados de una página.

**Razonamiento:** el plugin ya concentra los contratos editoriales de Inicio y se carga antes de que el tema procese el patrón. Mantener el módulo evita duplicar integración con WordPress y deja la opción independiente de plantillas, publicaciones y contenido editorial.

### Decisión: Reutilizar el menú superior LABM

**Elección:** registrar `Inicio` como submenú del slug administrativo `labm-footer`, que actualmente representa la pantalla superior **LABM**.

**Alternativas:** crear otro menú superior; añadir los campos a Footer LABM; registrar una pantalla bajo Ajustes.

**Razonamiento:** el usuario debe encontrar una sola familia de configuraciones LABM y la capacidad ya usada por Footer LABM es `edit_theme_options`. Un submenú separado mantiene el alcance de Inicio sin mezclar sus campos con el footer.

### Decisión: Esquema cerrado con lector de configuración efectiva

**Elección:** declarar exactamente estas claves: `section_label`, `title`, `description`, `button_label`, `button_url`, `stat_value` y `stat_label`. El lector sanea la opción y combina los campos válidos con defaults; ignora claves desconocidas.

**Alternativas:** devolver directamente el array de `get_option()`; aceptar claves dinámicas; usar bloques editables sin contrato de plugin.

**Razonamiento:** el esquema explícito evita ampliar accidentalmente la superficie editorial y permite que el tema reciba siempre una estructura completa. Los defaults conservan la salida aprobada cuando la opción no existe o falta un campo.

### Decisión: Saneamiento diferenciado para texto, indicador y URL

**Elección:** aplicar `sanitize_text_field` a rótulos y título, `sanitize_textarea_field` a la descripción, una normalización de dígitos al indicador y `esc_url_raw` limitado a rutas internas y protocolos `http`/`https` a la URL. El renderizado aplicará además `esc_html`, `esc_attr` y `esc_url` según el contexto.

**Alternativas:** permitir HTML confiable; escapar únicamente en la salida; aceptar cualquier protocolo que entregue el navegador.

**Razonamiento:** los requisitos piden texto plano y defensa en profundidad. El indicador actual `01` conserva ceros iniciales, por lo que se trata como texto numérico validado, no como entero convertido. Una URL con protocolo no permitido o formato inválido se conserva como destino vacío y el botón se omite; una URL ausente o explícitamente vacía vuelve al destino predeterminado seguro `/nosotros/`.

### Decisión: Mantener el patrón como frontera de fallback público

**Elección:** `patterns/inicio.php` resolverá la configuración antes de emitir el bloque y mantendrá un fallback local para la ausencia de LABM Core.

**Alternativas:** hacer que el plugin genere todo el HTML; exigir que el plugin esté activo; copiar la configuración a la base de datos durante activación.

**Razonamiento:** el patrón ya contiene la composición de Nuestra Liga y sus clases CSS están cubiertas por pruebas de experiencia pública. El plugin debe entregar datos, no apropiarse del layout; no se introduce una migración ni una dependencia fatal del plugin.

### Decisión: Guardado diferido y sin migración destructiva

**Elección:** no crear ni modificar `labm_home_settings` durante activación o despliegue. La opción aparecerá cuando un editor autorizado guarde el formulario.

**Alternativas:** crear la opción en el hook de activación; insertar los defaults directamente en la base de datos; migrar textos existentes desde plantillas.

**Razonamiento:** los valores actuales ya son el fallback aprobado. El guardado diferido evita cambios persistidos durante la planificación y reduce el riesgo de alterar entornos existentes. Si se guarda una personalización, el estado de la opción deberá pasar por el flujo oficial de sincronización de contenido antes de cerrar APPLY/VERIFY.

## Flujo de datos

```text
Editor autorizado
  -> LABM -> Inicio
  -> Settings API + nonce + edit_theme_options
  -> labm_home_settings
  -> labm_core_get_home_settings()
  -> patrón labm/inicio
  -> escape contextual y CTA opcional
  -> bloque público Nuestra Liga
```

Si LABM Core no está disponible:

```text
patrón labm/inicio -> defaults locales aprobados -> mismo marcado público seguro
```

## Cambios de archivos

| Archivo | Acción | Descripción |
|---|---|---|
| `wp-content/plugins/labm-core/includes/class-labm-home-content.php` | Modificar | Añadir capacidad, esquema cerrado, defaults, saneamiento, lector efectivo, registro de opción, submenú LABM → Inicio y formulario protegido por Settings API. |
| `wp-content/themes/labm/patterns/inicio.php` | Modificar | Sustituir los siete literales de Nuestra Liga por la configuración efectiva, conservar el fallback y omitir solo el CTA sin URL válida. |
| `tests/php/HomeContentTest.php` | Modificar | Cubrir esquema, defaults, claves desconocidas, saneamiento, URL, capacidad, registro y rechazo de acceso. |
| `tests/php/HomePresentationTest.php` | Modificar | Cubrir valores administrados, fallback, escape, URL segura, omisión del botón y ausencia del plugin. |
| `tests/php/PublicExperienceTest.php` | Modificar | Confirmar que el patrón conserva la sección, la jerarquía, las clases y el contrato de accesibilidad existente. |
| `tests/e2e/home.spec.ts` | Modificar | Comprobar valores públicos, CTA, responsive, teclado, foco, ausencia de desborde y Axe en los anchos configurados. |
| `docs/manual-usuario/manual-usuario.md` | Modificar | Documentar LABM → Inicio, los siete campos, defaults, URL, campo vacío y comprobación pública. |
| `docs/manual-usuario/cartilla-manual-usuario-labm.html` | Modificar | Añadir la misma ruta y explicación en la cartilla visual, sin describir controles inexistentes. |
| `docs/manual-usuario/cartilla-manual-usuario-labm.pdf` | Regenerar | Exportar la cartilla desde el HTML actualizado y revisar las páginas modificadas y adyacentes. |
| `openspec/changes/administra-nuestra-liga/validation-plan.yaml` | Crear | Registrar criterios, comprobaciones, gates y evidencia esperada para APPLY/VERIFY. |

No se modifican `style.css`, plantillas globales ni otras secciones de Inicio salvo que una prueba revele una regresión concreta de composición causada por textos más extensos. No se crean posts, opciones ni uploads durante DESIGN.

## Interfaces y contratos

- `labm_core_home_field_schema(): array`: devuelve únicamente las siete claves editoriales y su tipo/etiqueta.
- `labm_core_home_defaults(): array`: devuelve los valores aprobados actualmente visibles en el patrón.
- `labm_core_sanitize_home_settings( mixed $input ): array`: reduce la entrada al esquema y devuelve solo valores saneados.
- `labm_core_get_home_settings(): array`: devuelve una configuración completa y efectiva para consumo público; no contiene datos administrativos ni secretos.
- Opción WordPress: `labm_home_settings`, registrada con `register_setting()` en el grupo `labm_home`, sin exposición REST.
- Pantalla administrativa: slug `labm-home`, submenú de `labm-footer`, capacidad `edit_theme_options` y formulario enviado a `options.php` mediante `settings_fields( 'labm_home' )`.
- Contrato de presentación: `patterns/inicio.php` conserva `data-labm-section="presentacion"`, las clases `labm-home-presentation*`, el encabezado principal y la relación semántica del indicador; únicamente reemplaza valores y puede omitir el enlace del CTA.

## Estrategia de validación

La matriz completa está en `validation-plan.yaml`. La implementación deberá respetar el TDD estricto configurado en `openspec/config.yaml`: las pruebas focales de administración y presentación se escriben y se ejecutan en RED antes del cambio, y vuelven a ejecutarse en GREEN después. Los gates obligatorios son PHPUnit focal, PHPCS, PHPStan, pruebas públicas Playwright, cobertura mínima del 80 por ciento, revisión visual responsive, documentación coherente, finales LF y el gate configurado `scripts/gate.ps1 -IncludeBrowser`.

La evidencia debe distinguir comportamiento ejecutado de inspección estática. Si la pantalla administrativa se valida manualmente, se registrarán pasos reproducibles y el resultado real. La validación de la opción persistida debe incluir `scripts/content-sync.ps1 -Action Push` después de cualquier guardado en la base local, verificando `content-sync/canonical.zip`, `content-sync/latest.json`, versión, SHA-256 y exclusión de `wp_users`/`wp_usermeta`.

## Migración y despliegue

No se requiere migración de base de datos. El despliegue puede publicar el plugin y el tema en la misma entrega; el fallback hace segura la lectura durante una actualización parcial. La opción `labm_home_settings` se crea únicamente al guardar desde la pantalla administrativa.

Para desplegar una personalización guardada, se debe respaldar y publicar el estado mediante el flujo oficial de `content-sync` antes de la entrega. El paquete canónico debe incluir la opción, pero nunca credenciales ni tablas de usuarios. La regeneración del PDF parte del HTML de la cartilla y se valida visualmente; la herramienta concreta de exportación se resolverá con la disponible en el entorno de APPLY y se documentará en la evidencia.

## Rollback

1. Restaurar las versiones anteriores de `class-labm-home-content.php`, `patterns/inicio.php`, las pruebas y los dos formatos del manual.
2. Dejar `labm_home_settings` intacta si la pantalla se retira: el código anterior la ignorará y los textos rígidos volverán a mostrarse.
3. Si se requiere eliminar la personalización persistida, crear primero un respaldo mediante `content-sync`, restaurar el paquete aprobado o ejecutar una eliminación administrativa explícita y reversible; no borrar publicaciones, medios ni usuarios.
4. Volver a ejecutar las comprobaciones públicas, de seguridad, documentación y LF. Si el rollback modifica la base local, publicar el estado canónico resultante con `content-sync`.

## Riesgos y mitigaciones

| Riesgo | Mitigación |
|---|---|
| Un texto largo altera la composición responsive | Mantener las clases y límites CSS actuales; probar 320, 768, 1024 y 1440 píxeles, además de foco y ampliación de texto. |
| Un editor sin permisos accede a la opción | Aplicar la capacidad en menú, callback y filtro `option_page_capability_labm_home`; conservar nonce de Settings API. |
| Una URL insegura llega al HTML | Saneamiento limitado a rutas internas, `http` y `https`, validación en lectura y omisión del CTA si queda vacía. |
| El plugin no está activo durante una petición pública | Comprobar `function_exists()` y resolver defaults locales sin error fatal. |
| El valor persistido queda fuera del paquete canónico | Hacer obligatorio el `Push` oficial de content-sync en APPLY/VERIFY y verificar versión/hash. |

## Preguntas abiertas

- [ ] Confirmar en APPLY la herramienta concreta disponible para regenerar `cartilla-manual-usuario-labm.pdf` desde su HTML; el repositorio documenta el flujo y la inspección visual, pero no expone un comando dedicado en `package.json` o `scripts/`.
