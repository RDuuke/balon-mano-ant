import { test, expect, type BrowserContext, type Page } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

const baseURL = process.env.WP_URL || 'http://localhost:8080';
const adminUser = process.env.WP_ADMIN_USER || 'labm_demo_admin';
const adminPassword = process.env.WP_ADMIN_PASSWORD || 'demo_admin_password_change_me';
let pdfName: string;

type AuthState = Awaited<ReturnType<BrowserContext['storageState']>>;
type Fixture = { attachmentId: number; documentId: number; restNonce: string };

let authState: AuthState;
let fixture: Fixture;

async function login(page: Page) {
  await page.goto(`${baseURL}/wp-login.php`);
  await page.getByLabel(/nombre de usuario|username/i).fill(adminUser);
  await page.locator('#user_pass').fill(adminPassword);
  await page.locator('#wp-submit').click();
  await expect(page).toHaveURL(/wp-admin/);
}

async function openDocument(page: Page, documentId?: number, classic = false) {
  const target = documentId
    ? `/wp-admin/post.php?post=${documentId}&action=edit`
    : '/wp-admin/post-new.php?post_type=labm_documento';
  await page.goto(`${baseURL}${target}${classic ? '&labm-e2e-classic=1' : ''}`);
  await expect(page.locator('body')).toHaveClass(/post-type-labm_documento/);
  if (!classic) {
    await page.waitForFunction(() => Boolean((window as typeof window & { labmDocumentAdmin?: unknown }).labmDocumentAdmin));
    await page.evaluate(() => {
      const wpRuntime = (window as typeof window & {
        wp?: { data?: { dispatch: (store: string) => Record<string, (...args: string[]) => void>; select: (store: string) => Record<string, (...args: string[]) => boolean> } };
      }).wp;
      const panelName = 'labm-document-admin/labm-document-admin';
      const editor = wpRuntime?.data?.dispatch('core/editor');
      const selector = wpRuntime?.data?.select('core/editor');
      if (editor?.toggleEditorPanelOpened && selector?.isEditorPanelOpened && !selector.isEditorPanelOpened(panelName)) {
        editor.toggleEditorPanelOpened(panelName);
      }
      wpRuntime?.data?.dispatch('core/edit-post')?.openGeneralSidebar?.('edit-post/document');
    });
  }
  await expect(page.locator('[data-labm-document-admin]')).toBeVisible();
}

async function createFixture(context: BrowserContext, page: Page): Promise<Fixture> {
  await openDocument(page);
  const config = await page.evaluate(() => (window as typeof window & {
    labmDocumentAdmin: { restNonce: string; generalTermId: number };
  }).labmDocumentAdmin);
  const headers = { 'X-WP-Nonce': config.restNonce };
  const upload = await context.request.post(`${baseURL}/wp-json/wp/v2/media`, {
    headers: {
      ...headers,
      'Content-Disposition': `attachment; filename="${pdfName}"`,
      'Content-Type': 'application/pdf',
    },
    data: Buffer.from('%PDF-1.7\n1 0 obj<</Type/Catalog>>endobj\n%%EOF\n'),
  });
  expect(upload.ok(), await upload.text()).toBeTruthy();
  const attachment = await upload.json() as { id: number };
  const created = await context.request.post(`${baseURL}/wp-json/wp/v2/labm_documento`, {
    headers,
    data: {
      title: 'Documento E2E con PDF',
      status: 'draft',
      meta: { labm_documento_pdf_id: attachment.id, labm_documento_fecha: '2026-09-15' },
      labm_documento_categoria: [config.generalTermId],
    },
  });
  expect(created.ok(), await created.text()).toBeTruthy();
  const document = await created.json() as { id: number };
  return { attachmentId: attachment.id, documentId: document.id, restNonce: config.restNonce };
}

test.describe('experiencia administrativa de documentos PDF', () => {
  test.describe.configure({ mode: 'serial' });

  test.beforeAll(async ({ browser }, testInfo) => {
    pdfName = `labm-e2e-document-${testInfo.project.name}-${Date.now()}.pdf`;
    const context = await browser.newContext();
    const page = await context.newPage();
    await login(page);
    authState = await context.storageState();
    fixture = await createFixture(context, page);
    await context.close();
  });

  test.beforeEach(async ({ context }) => {
    await context.addCookies(authState.cookies);
  });

  test.afterAll(async ({ browser }) => {
    if (!fixture) return;
    const context = await browser.newContext({ storageState: authState });
    const headers = { 'X-WP-Nonce': fixture.restNonce };
    await context.request.delete(`${baseURL}/wp-json/wp/v2/labm_documento/${fixture.documentId}?force=true`, { headers });
    await context.request.delete(`${baseURL}/wp-json/wp/v2/media/${fixture.attachmentId}?force=true`, { headers });
    await context.close();
  });

  test('A1.1 selecciona un PDF desde Media Library sin mostrar ID ni URL', async ({ page }) => {
    await openDocument(page);
    await page.getByRole('button', { name: /seleccionar o subir pdf/i }).click();
    await expect(page.getByRole('dialog')).toBeVisible();
    await page.locator(`.attachment[data-id="${fixture.attachmentId}"]`).click();
    await page.getByRole('button', { name: /usar este pdf|use this pdf/i }).click();
    await expect(page.locator('[data-labm-document-admin]')).toContainText(pdfName);
    await expect(page.getByLabel(/id.*pdf|url.*pdf/i)).toHaveCount(0);
  });

  test('A1.2 permite reemplazar y retirar sin borrar el adjunto', async ({ page, request }) => {
    await openDocument(page, fixture.documentId);
    await expect(page.getByRole('button', { name: /reemplazar pdf/i })).toBeVisible();
    await page.getByRole('button', { name: /quitar pdf/i }).click();
    await expect(page.locator('[data-labm-document-admin]')).toContainText(/ningún pdf asociado/i);
    await expect(page.locator('[data-labm-document-admin]')).toContainText(/permanece en la biblioteca/i);
    const attachment = await request.get(`${baseURL}/wp-json/wp/v2/media/${fixture.attachmentId}`);
    expect(attachment.ok()).toBeTruthy();
  });

  test('A1.3 un fallo de Media Library conserva la asociación previa y orienta', async ({ page }) => {
    await openDocument(page, fixture.documentId);
    await page.evaluate(() => window.dispatchEvent(new CustomEvent('labm:media-error')));
    await expect(page.getByRole('alert')).toContainText(/no se pudo.*biblioteca|intenta de nuevo/i);
    await expect(page.locator('[data-labm-document-admin]')).toContainText(pdfName);
  });

  test('A2.1 muestra nombre, peso y estado textual del PDF', async ({ page }) => {
    await openDocument(page, fixture.documentId);
    const panel = page.getByRole('region', { name: /archivo pdf/i });
    await expect(panel).toContainText(pdfName);
    await expect(panel).toContainText(/\d+(?:[,.]\d+)?\s*(?:KB|MB)/i);
    await expect(panel).toContainText(/PDF válido/i);
  });

  test('A2.2 informa el menor límite efectivo configurado por WordPress', async ({ page }) => {
    await openDocument(page);
    const limit = page.locator('[data-labm-effective-max-bytes]');
    await expect(limit).toContainText(/30 MB/i);
    await expect(limit).toContainText(/WordPress/i);
    const configured = Number(await limit.getAttribute('data-labm-effective-max-bytes'));
    expect(configured).toBeGreaterThan(0);
    expect(configured).toBeLessThanOrEqual(30 * 1024 * 1024);
  });

  test('A2.3 rechaza un archivo sobredimensionado con acción correctiva', async ({ page }) => {
    await openDocument(page);
    await page.evaluate(() => window.dispatchEvent(new CustomEvent('labm:oversized-pdf')));
    await expect(page.getByRole('alert')).toContainText(/supera el límite.*elige.*pequeño/i);
    await expect(page.locator('[data-labm-effective-max-bytes]')).toBeVisible();
  });

  test('A3.1 completa el flujo del editor de bloques solo con teclado', async ({ page }) => {
    await openDocument(page);
    const select = page.getByRole('button', { name: /seleccionar o subir pdf/i });
    await select.focus();
    await page.keyboard.press('Enter');
    await expect(page.getByRole('dialog')).toBeVisible();
    await page.keyboard.press('Escape');
    await expect(select).toBeFocused();
  });

  test('A3.2 el editor clásico ofrece controles equivalentes y ayuda asociada', async ({ page }) => {
    await openDocument(page, fixture.documentId, true);
    const panel = page.locator('[data-labm-document-admin][data-labm-editor="classic"]');
    await expect(panel.getByRole('button', { name: /reemplazar pdf/i })).toBeVisible();
    await expect(panel.getByLabel(/fecha del documento/i)).toHaveValue('2026-09-15');
    await expect(panel.getByLabel(/tipo de documento/i)).toBeVisible();
    await expect(panel.locator('[data-labm-effective-max-bytes]')).toBeVisible();
  });

  test('A3.3 anuncia errores, enfoca el primer campo inválido y pasa axe', async ({ page }) => {
    await openDocument(page);
    await page.frameLocator('iframe[name="editor-canvas"]').locator('.editor-post-title__input').fill('Documento E2E incompleto');
    await page.locator('.editor-post-publish-panel__toggle').click();
    await expect(page.getByRole('alert')).toContainText(/selecciona un PDF/i);
    await expect(page.getByRole('button', { name: /seleccionar o subir pdf/i })).toBeFocused();
    const results = await new AxeBuilder({ page })
      .include('[data-labm-document-admin]')
      .withTags(['wcag2a', 'wcag2aa', 'wcag21aa', 'wcag22aa'])
      .analyze();
    expect(results.violations).toEqual([]);
  });

  test('E1 reemplaza por biblioteca, guarda por HTTP clásico y reintenta sin reselección', async ({ page, context }) => {
    await openDocument(page, fixture.documentId, true);
    const headers = { 'X-WP-Nonce': fixture.restNonce };
    const upload = await context.request.post(`${baseURL}/wp-json/wp/v2/media`, {
      headers: { ...headers, 'Content-Disposition': 'attachment; filename="labm-e2e-replacement.pdf"', 'Content-Type': 'application/pdf' },
      data: Buffer.from('%PDF-1.7\n1 0 obj<</Type/Catalog>>endobj\n%%EOF\n'),
    });
    expect(upload.ok()).toBeTruthy();
    const replacement = await upload.json() as { id: number };
    try {
      await page.getByRole('button', { name: /reemplazar pdf/i }).click();
      await page.locator(`.attachment[data-id="${replacement.id}"]`).click();
      await page.getByRole('button', { name: /usar este pdf/i }).click();
      await expect(page.locator('[data-labm-pdf-summary]')).toContainText('labm-e2e-replacement.pdf');
      await page.locator('#title').fill('Documento E2E sustitución HTTP');
      const [saved] = await Promise.all([
        page.waitForResponse(response => response.request().method() === 'POST' && new URL(response.url()).pathname === '/wp-admin/post.php'),
        page.locator('#save-post').click(),
      ]);
      expect(saved.status()).toBeLessThan(400);
      await expect.poll(async () => {
        const response = await context.request.get(`${baseURL}/wp-json/wp/v2/labm_documento/${fixture.documentId}?context=edit`, { headers });
        expect(response.ok()).toBeTruthy();
        const document = await response.json();
        return [document.meta.labm_documento_pdf_id, document.title.raw];
      }).toEqual([replacement.id, 'Documento E2E sustitución HTTP']);
      let persisted = await (await context.request.get(`${baseURL}/wp-json/wp/v2/labm_documento/${fixture.documentId}?context=edit`, { headers })).json();
      expect(persisted.meta.labm_documento_pdf_id).toBe(replacement.id);
      expect(persisted.title.raw).toBe('Documento E2E sustitución HTTP');
      await openDocument(page, fixture.documentId, true);
      // A rejected form submission preserves the current selection for correction.
      await page.locator('#title').fill('');
      await page.locator('#save-post').click();
      await expect(page.locator('[data-labm-admin-alert]')).toContainText(/título/i);
      await expect(page.locator('[data-labm-pdf-summary]')).toContainText('labm-e2e-replacement.pdf');
      await page.locator('#title').fill('Documento E2E reintento HTTP');
      const [retried] = await Promise.all([
        page.waitForResponse(response => response.request().method() === 'POST' && new URL(response.url()).pathname === '/wp-admin/post.php'),
        page.locator('#save-post').click(),
      ]);
      expect(retried.status()).toBeLessThan(400);
      await expect.poll(async () => {
        const response = await context.request.get(`${baseURL}/wp-json/wp/v2/labm_documento/${fixture.documentId}?context=edit`, { headers });
        expect(response.ok()).toBeTruthy();
        const document = await response.json();
        return [document.meta.labm_documento_pdf_id, document.title.raw];
      }).toEqual([replacement.id, 'Documento E2E reintento HTTP']);
      persisted = await (await context.request.get(`${baseURL}/wp-json/wp/v2/labm_documento/${fixture.documentId}?context=edit`, { headers })).json();
      expect(persisted.meta.labm_documento_pdf_id).toBe(replacement.id);
      expect(persisted.title.raw).toBe('Documento E2E reintento HTTP');
      await openDocument(page, fixture.documentId, true);
      await page.getByRole('button', { name: /quitar pdf/i }).click();
      await expect(page.locator('[data-labm-pdf-summary]')).toContainText(/ningún pdf/i);
      expect((await context.request.get(`${baseURL}/wp-json/wp/v2/media/${replacement.id}`, { headers })).ok()).toBeTruthy();
      expect((await context.request.get(`${baseURL}/wp-json/wp/v2/media/${fixture.attachmentId}`, { headers })).ok()).toBeTruthy();
    } finally {
      await context.request.post(`${baseURL}/wp-json/wp/v2/labm_documento/${fixture.documentId}`, {
        headers, data: { title: 'Documento E2E con PDF', meta: { labm_documento_pdf_id: fixture.attachmentId } },
      });
      await context.request.delete(`${baseURL}/wp-json/wp/v2/media/${replacement.id}?force=true`, { headers });
    }
  });

  test('E2 identifica ayuda y estado al enfocar controles de ambos editores', async ({ page }) => {
    for (const classic of [false, true]) {
      await openDocument(page, fixture.documentId, classic);
      const panel = page.locator('[data-labm-document-admin]');
      const pdf = panel.getByRole('button', { name: /reemplazar pdf/i });
      await pdf.focus();
      await expect(pdf).toBeFocused();
      await expect(panel.locator('fieldset')).toHaveAccessibleDescription(/permanece.*biblioteca.*límite efectivo/i);
      await expect(panel.locator('fieldset')).toContainText(/PDF válido/i);
      const date = panel.getByLabel(/fecha del documento/i);
      await date.focus();
      await expect(date).toBeFocused();
      await expect(date).toHaveAccessibleDescription(/opcional.*fecha oficial/i);
      const type = panel.getByLabel(/tipo de documento/i);
      await type.focus();
      await expect(type).toBeFocused();
      await expect(type).toHaveAccessibleDescription(/selecciona un solo tipo/i);
    }
  });

  test('E3 fallo real de carga conserva asociación y ofrece error accionable', async ({ page }) => {
    await openDocument(page, fixture.documentId, true);
    await page.route('**/async-upload.php', route => route.abort('failed'));
    await page.getByRole('button', { name: /reemplazar pdf/i }).click();
    await page.getByRole('tab', { name: /^(?:subir archivos|upload files)$/i }).click();
    await page.locator('input[type="file"]').setInputFiles({ name: 'labm-e2e-failed.pdf', mimeType: 'application/pdf', buffer: Buffer.from('%PDF-1.7\n%%EOF') });
    await expect(page.locator('.upload-errors')).toContainText(/error|fall|intenta|unexpected response from the server/i);
    await page.keyboard.press('Escape');
    await expect(page.locator('[data-labm-pdf-summary]')).toContainText(pdfName);
    await expect(page.locator('[data-labm-pdf-id]')).toHaveValue(String(fixture.attachmentId));
  });

  test('E4 carga real sobredimensionada se rechaza con límite visible', async ({ page, context }) => {
    await openDocument(page, fixture.documentId, true);
    const limit = Number(await page.locator('[data-labm-effective-max-bytes]').getAttribute('data-labm-effective-max-bytes'));
    await page.getByRole('button', { name: /reemplazar pdf/i }).click();
    await page.getByRole('tab', { name: /^(?:subir archivos|upload files)$/i }).click();
    const filename = `labm-e2e-too-large-${Date.now()}.pdf`;
    const buffer = Buffer.alloc(limit + 1, 32);
    buffer.write('%PDF-1.7\n1 0 obj<</Type/Catalog>>endobj\n');
    buffer.write('\n%%EOF\n', buffer.length - 8);
    try {
      await page.locator('input[type="file"]').setInputFiles({ name: filename, mimeType: 'application/pdf', buffer });
      const use = page.getByRole('button', { name: /usar este pdf/i });
      await expect.poll(async () => (await page.locator('.upload-errors').isVisible()) || (await use.isEnabled())).toBe(true);
      if (await page.locator('.upload-errors').isVisible()) {
        await expect(page.locator('.upload-errors')).toContainText(/tamaño|grande|límite|exceeds.*(?:size|limit)|too large|maximum upload/i);
        await page.keyboard.press('Escape');
      } else {
        await expect(use).toBeEnabled({ timeout: 30000 });
        await use.click();
        await expect(page.locator('[data-labm-admin-alert]')).toContainText(/supera el límite.*pequeño/i);
      }
      await expect(page.locator('[data-labm-pdf-id]')).toHaveValue(String(fixture.attachmentId));
    } finally {
      const headers = { 'X-WP-Nonce': fixture.restNonce };
      const matches = await (await context.request.get(`${baseURL}/wp-json/wp/v2/media?search=${encodeURIComponent(filename.replace('.pdf', ''))}&context=edit`, { headers })).json() as { id: number }[];
      for (const attachment of matches) await context.request.delete(`${baseURL}/wp-json/wp/v2/media/${attachment.id}?force=true`, { headers });
    }
  });

  test('E5 selecciona, reemplaza, retira y corrige con activación por teclado', async ({ page }) => {
    await openDocument(page, fixture.documentId, true);
    const select = page.getByRole('button', { name: /reemplazar pdf|seleccionar o subir pdf/i });
    await select.focus();
    await page.keyboard.press('Enter');
    await expect(page.getByRole('dialog')).toBeVisible();
    const activeDialog = page.getByRole('dialog');
    const attachment = activeDialog.locator(`.attachment[data-id="${fixture.attachmentId}"]`);
    await expect(attachment).toBeVisible();
    await attachment.focus();
    await expect(attachment).toBeFocused();
    if (await attachment.getAttribute('aria-checked') !== 'true') await attachment.press('Space');
    await expect(attachment).toHaveAttribute('aria-checked', 'true');
    const use = activeDialog.getByRole('button', { name: /usar este pdf/i });
    await expect(use).toBeEnabled();
    await use.press('Enter');
    await expect(select).toBeFocused();
    await expect(page.locator('[data-labm-pdf-summary]')).toContainText(pdfName);
    const remove = page.getByRole('button', { name: /quitar pdf/i });
    await remove.focus();
    await page.keyboard.press('Space');
    await expect(select).toBeFocused();
    await expect(page.locator('[data-labm-pdf-summary]')).toContainText(/ningún pdf/i);
    await page.locator('#save-post').focus();
    await page.keyboard.press('Enter');
    await expect(select).toBeFocused();
    await expect(page.locator('[data-labm-admin-alert]')).toContainText(/selecciona un pdf/i);
    await page.keyboard.press('Enter');
    await expect(page.getByRole('dialog')).toBeVisible();
    await expect(attachment).toBeVisible();
    await attachment.focus();
    await expect(attachment).toBeFocused();
    if (await attachment.getAttribute('aria-checked') !== 'true') await attachment.press('Space');
    await expect(attachment).toHaveAttribute('aria-checked', 'true');
    await expect(use).toBeEnabled();
    await use.press('Enter');
    await expect(page.locator('[data-labm-pdf-id]')).toHaveValue(String(fixture.attachmentId));
    await expect(page.locator('[data-labm-admin-alert]')).toBeHidden();
    await expect(select).toBeFocused();
  });
});
