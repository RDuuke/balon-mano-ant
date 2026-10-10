import { test, expect } from '@playwright/test';

test('navegacion escritorio completa y ubicacion activa', async ({ page }) => {
  await page.goto('/');
  const open = page.getByRole('button', { name: /open menu|abrir menú/i });
  if (await open.isVisible()) await open.click();
  const nav = page.getByRole('navigation', { name: /principal/i });
  for (const label of ['Inicio', 'Nosotros', 'Actualidad', 'Documentos', 'Contacto']) {
    await expect(nav.getByRole('link', { name: label, exact: true })).toBeVisible();
  }
  await expect(nav.getByRole('link', { name: 'Inicio', exact: true })).toHaveAttribute('aria-current', 'page');
});

test('submenú de Selecciones se cierra al abandonar su contexto', async ({ page }) => {
  await page.goto('/');
  const menu = page.getByRole('button', { name: /abrir men/i });
  if (await menu.isVisible()) await menu.click();
  const submenuToggle = page.getByRole('button', { name: 'Selecciones', exact: true });
  await submenuToggle.focus();
  await page.keyboard.press('Enter');
  await expect(submenuToggle).toHaveAttribute('aria-expanded', 'true');
  const nav = page.getByRole('navigation', { name: /principal/i });
  await nav.getByRole('link', { name: 'Actualidad', exact: true }).click();
  await expect(page).toHaveURL(/\/actualidad\/$/);
  await expect(page.locator('[data-labm-submenu-toggle]')).toHaveAttribute('aria-expanded', 'false');
});

test('jerarquia de header permanece estable en Inicio y Actualidad', async ({ page }) => {
  for (const route of ['/', '/actualidad/']) {
    await page.goto(route);
    const open = page.getByRole('button', { name: /open menu|abrir menú/i });
    if (await open.isVisible()) {
      await open.click();
    }
    const nav = page.getByRole('navigation', { name: /principal/i });
    const submenuToggle = nav.getByRole('button', { name: 'Selecciones', exact: true });
    if ('false' === await submenuToggle.getAttribute('aria-expanded')) {
      await submenuToggle.focus();
      await page.keyboard.press('Enter');
    }
    const parent = submenuToggle;
    await expect(nav.getByRole('link', { name: 'Selecciones', exact: true })).toHaveCount(0);
    await expect(parent).not.toHaveAttribute('href');
    await expect(nav.getByRole('link', { name: 'Balonmano Piso', exact: true })).toHaveAttribute('href', /modalidad=Piso/);
    await expect(nav.getByRole('link', { name: 'Balonmano Playa', exact: true })).toHaveAttribute('href', /modalidad=Playa/);
    await expect(nav.getByRole('link', { name: route === '/' ? 'Inicio' : 'Actualidad', exact: true })).toHaveAttribute('aria-current', 'page');
    await expect(parent).not.toHaveAttribute('aria-current', 'page');

    const headerPosition = await page.locator('header.wp-block-template-part').evaluate((element) => getComputedStyle(element).position);
    const expectedHeaderPositions = (page.viewportSize()?.width ?? 1024) < 768 ? ['static'] : ['sticky', 'fixed'];
    expect(expectedHeaderPositions).toContain(headerPosition);
  }
});

test('navegacion movil conserva foco visible y recorrido predecible', async ({ page }) => {
  await page.setViewportSize({ width: 320, height: 800 });
  await page.goto('/');
  const open = page.getByRole('button', { name: /open menu|abrir menú/i });
  await open.focus();
  await expect(page.locator(':focus-visible')).toHaveCount(1);
  await page.keyboard.press('Enter');
  const links = page.getByRole('navigation', { name: /principal/i }).getByRole('link');
  await expect(links).toHaveCount(5);
  const submenuToggle = page.getByRole('button', { name: 'Selecciones', exact: true });
  await submenuToggle.focus();
  await page.keyboard.press('Enter');
  await expect(page.getByRole('link', { name: 'Balonmano Piso', exact: true })).toBeVisible();
  await page.keyboard.press('Escape');
  await expect(submenuToggle).toHaveAttribute('aria-expanded', 'false');
  await expect(submenuToggle).toBeFocused();
  const close = page.getByRole('button', { name: /close menu|cerrar menú/i });
  await close.click();
  await expect(open).toBeFocused();
});

test('recorrido accesible llega al contenido y mantiene mensajes identificables', async ({ page }) => {
  await page.goto('/');
  const skip = page.getByRole('link', { name: /saltar al contenido/i });
  await skip.focus();
  await page.keyboard.press('Enter');
  await expect(page).toHaveURL(/#contenido-principal$/);
  await expect(page.locator('#contenido-principal')).toBeVisible();
});


test('submenu Selecciones abre por hover y conserva cierre por clic y Escape', async ({ page }) => {
  test.skip((page.viewportSize()?.width ?? 320) < 768, 'Hover en escritorio');
  await page.goto('/');
  const toggle = page.getByRole('button', { name: 'Selecciones', exact: true });
  const group = page.locator('[data-labm-submenu]');
  await toggle.hover();
  await expect(toggle).toHaveAttribute('aria-expanded', 'true');
  await page.getByRole('link', { name: 'Balonmano Piso', exact: true }).hover();
  await expect(toggle).toHaveAttribute('aria-expanded', 'true');
  await page.getByRole('navigation', { name: /principal/i }).getByRole('link', { name: 'Inicio', exact: true }).hover();
  await expect(toggle).toHaveAttribute('aria-expanded', 'false');
  await toggle.hover();
  await toggle.click();
  await expect(toggle).toHaveAttribute('aria-expanded', 'false');
  await expect(page).toHaveURL(/\/$/);
  await page.mouse.move(0, 0);
  await toggle.hover();
  await page.keyboard.press('Escape');
  await expect(toggle).toHaveAttribute('aria-expanded', 'false');
  await expect(toggle).toBeFocused();
  await expect(group).not.toHaveClass(/is-current-section/);
});

test('submenu Selecciones conserva padre activo y teclado en ambas modalidades', async ({ page }) => {
  for (const modalidad of ['Piso', 'Playa']) {
    await page.goto(`/selecciones/?modalidad=${modalidad}`);
    const menu = page.getByRole('button', { name: /abrir men/i });
    if (await menu.isVisible()) await menu.click();
    const toggle = page.getByRole('button', { name: 'Selecciones', exact: true });
    const group = page.locator('[data-labm-submenu]');
    await expect(group).toHaveClass(/is-current-section/);
    await expect(toggle).toHaveAttribute('aria-expanded', 'false');
    const activeColor = await toggle.evaluate(el => getComputedStyle(el).color);
    await page.keyboard.press('Tab');
    await toggle.focus();
    await expect(page.locator(':focus-visible')).toHaveCount(1);
    await page.keyboard.press('Space');
    await expect(toggle).toHaveAttribute('aria-expanded', 'true');
    expect(await toggle.evaluate(el => getComputedStyle(el).color)).toBe(activeColor);
    await page.keyboard.press('Tab');
    await expect(page.getByRole('link', { name: 'Balonmano Piso', exact: true })).toBeFocused();
    await expect(group.locator('[aria-current="page"]')).toHaveCount(1);
    await expect(page.getByRole('link', { name: `Balonmano ${modalidad}`, exact: true })).toHaveAttribute('aria-current', 'page');
    await page.keyboard.press('Escape');
    await expect(toggle).toBeFocused();
    await expect(toggle).toHaveAttribute('aria-expanded', 'false');
    await page.keyboard.press('Enter');
    await page.getByRole('link', { name: `Balonmano ${modalidad === 'Piso' ? 'Playa' : 'Piso'}`, exact: true }).click();
    await expect(group).toHaveClass(/is-current-section/);
  }
});

test('header Selecciones mantiene padre y modalidad seleccionados en internas', async ({ page }) => {
  // Canonical WordPress keeps localhost URLs; route assets without changing persisted options.
  await page.route('http://localhost:8080/**', route => route.continue({
    url: route.request().url().replace('http://localhost:8080', 'http://host.docker.internal:8080')
  }));
  for (const modalidad of ['Piso', 'Playa']) {
    await page.goto(`/selecciones/?modalidad=${modalidad}`);
    const publication = page.locator('[data-labm-seleccion-row] a.labm-selecciones__publication-link').first();
    await expect(publication).toBeVisible();
    const target = new URL((await publication.getAttribute('href'))!);
    await page.goto(`${target.pathname}?modalidad=${modalidad === 'Piso' ? 'Playa' : 'Piso'}`);
    const menu = page.getByRole('button', { name: /abrir men/i });
    if (await menu.isVisible()) await menu.click();
    const group = page.locator('[data-labm-submenu]');
    const toggle = group.getByRole('button', { name: 'Selecciones', exact: true });
    await expect(group).toHaveClass(/is-current-section/);
    await expect(toggle).toHaveAttribute('aria-expanded', 'false');
    await toggle.focus();
    await page.keyboard.press('Enter');
    const selected = group.getByRole('link', { name: `Balonmano ${modalidad}`, exact: true });
    await expect(selected).toHaveAttribute('aria-current', 'location');
    await expect(group.locator('[aria-current]')).toHaveCount(1);
    const selectedBackground = await selected.evaluate(el => getComputedStyle(el).backgroundColor);
    const other = group.getByRole('link', { name: `Balonmano ${modalidad === 'Piso' ? 'Playa' : 'Piso'}`, exact: true });
    expect(await other.evaluate(el => getComputedStyle(el).backgroundColor)).not.toBe(selectedBackground);
    await page.keyboard.press('Escape');
    await expect(group).toHaveClass(/is-current-section/);
    await expect(toggle).toHaveAttribute('aria-expanded', 'false');
  }
});
