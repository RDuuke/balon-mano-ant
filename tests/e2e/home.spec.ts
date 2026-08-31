import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

const targetWidths = [320, 768, 1024, 1200, 1440];

test('slider muestra dots circulares alineados y sin desborde vertical normal', async ({ page }) => {
  for (const width of [320, 390, 768, 1200, 1440]) {
    await page.setViewportSize({ width, height: 900 });
    await page.goto('/');
    const slider = page.locator('[data-labm-slider]');
    const dots = slider.locator('[data-labm-slide-to]');
    const controls = slider.locator('.labm-home-slider__controls');
    const indicators = slider.locator('.labm-home-slider__indicators');

    await expect(dots).toHaveCount(2);
    const geometry = await slider.evaluate((element) => ({
      clientHeight: element.clientHeight,
      scrollHeight: element.scrollHeight,
      overflowY: getComputedStyle(element).overflowY,
    }));
    expect(geometry.scrollHeight, `desborde vertical del slider a ${width}px`).toBe(geometry.clientHeight);
    expect(geometry.overflowY).toBe('hidden');

    const alignment = await Promise.all([controls, indicators].map((locator) => locator.evaluate((element) => {
      const rect = element.getBoundingClientRect();
      return rect.top + (rect.height / 2);
    })));
    expect(Math.abs(alignment[0] - alignment[1]), `alineacion de controles a ${width}px`).toBeLessThanOrEqual(1);

    const contentBottom = await slider.locator('[data-labm-slide]:visible').locator(':scope > h2, :scope > p, :scope > a').evaluateAll((elements) =>
      Math.max(...elements.map((element) => element.getBoundingClientRect().bottom)),
    );
    const controlBandTop = await Promise.all([controls, indicators].map((locator) => locator.evaluate((element) =>
      element.getBoundingClientRect().top,
    )));
    expect(contentBottom, `contenido separado de controles a ${width}px`).toBeLessThanOrEqual(Math.min(...controlBandTop) - 8);

    const dotStyles = await dots.evaluateAll((elements) => elements.map((element) => {
      const hitArea = getComputedStyle(element);
      const visibleDot = getComputedStyle(element, '::before');
      return {
        hitWidth: Number.parseFloat(hitArea.width),
        hitHeight: Number.parseFloat(hitArea.height),
        width: Number.parseFloat(visibleDot.width),
        height: Number.parseFloat(visibleDot.height),
        radius: visibleDot.borderRadius,
        color: visibleDot.backgroundColor,
        current: element.getAttribute('aria-current'),
      };
    }));
    for (const dot of dotStyles) {
      expect(dot.hitWidth).toBeGreaterThanOrEqual(44);
      expect(dot.hitHeight).toBeGreaterThanOrEqual(44);
      expect(dot.width).toBe(10);
      expect(dot.height).toBe(10);
      expect(dot.radius).toBe('50%');
      expect(dot.color).toBe(dot.current === 'true' ? 'rgb(174, 205, 37)' : 'rgba(255, 255, 255, 0.6)');
    }

    await slider.getByRole('button', { name: /siguiente/i }).click();
    await expect(dots.nth(1)).toHaveAttribute('aria-current', 'true');
    const activeColor = await dots.nth(1).evaluate((element) => getComputedStyle(element, '::before').backgroundColor);
    expect(activeColor).toBe('rgb(174, 205, 37)');
  }
});

test('secciones del inicio usan las superficies exactas del diseño', async ({ page }) => {
  await page.goto('/');
  const expected = new Map([
    ['presentacion', 'rgb(255, 255, 255)'],
    ['clubes', 'rgb(243, 246, 232)'],
    ['evento', 'rgb(0, 0, 0)'],
    ['actualidad', 'rgb(255, 255, 255)'],
    ['vinculacion', 'rgb(0, 0, 0)'],
    ['aliados', 'rgb(243, 246, 232)'],
  ]);
  for (const [section, color] of expected) {
    await expect(page.locator(`[data-labm-section="${section}"]`)).toHaveCSS('background-color', color);
  }
});

test('slider mantiene altura al usar anterior, siguiente e indicadores', async ({ page }) => {
  test.setTimeout(90_000);
  for (const width of targetWidths) {
    await page.setViewportSize({ width, height: 900 });
    await page.goto('/');
    const slider = page.locator('[data-labm-slider]');
    const height = async () => slider.evaluate((element) => element.getBoundingClientRect().height);
    const initialHeight = await height();

    await slider.locator('[data-labm-slide]').nth(1).locator('p').evaluate((paragraph) => {
      paragraph.textContent = `${paragraph.textContent} ${'Contenido editorial extremo '.repeat(180)}`;
    });
    await slider.getByRole('button', { name: /siguiente/i }).click();
    expect(await height(), `siguiente a ${width}px`).toBe(initialHeight);
    await slider.getByRole('button', { name: /anterior/i }).click();
    expect(await height(), `anterior a ${width}px`).toBe(initialHeight);
    await slider.locator('[data-labm-slide-to="1"]').click();
    expect(await height(), `indicador a ${width}px`).toBe(initialHeight);
    await expect(slider.locator('[data-labm-slider-next]')).toBeVisible();
  }
});

test('inicio responde y no presenta violaciones axe automaticas', async ({ page }) => {
  await page.goto('/');
  await expect(page).toHaveTitle(/LABM/i);
  await expect(page.locator('main h1')).toHaveText(/Formamos deportistas/i);
  const results = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21aa', 'wcag22aa']).analyze();
  expect(results.violations).toEqual([]);
});

test('inicio conserva el orden aprobado y excluye secciones retiradas', async ({ page }) => {
  await page.goto('/');
  const sections = await page.locator('main [data-labm-section]').evaluateAll((nodes) =>
    nodes.map((node) => node.getAttribute('data-labm-section')),
  );
  const approvedOrder = ['slider', 'presentacion', 'clubes', 'evento', 'actualidad', 'vinculacion', 'aliados'];
  expect(sections).toEqual(approvedOrder.filter((section) => sections.includes(section)));
  expect(sections).toContain('presentacion');
  expect(sections).toContain('vinculacion');
  await expect(page.getByRole('heading', { name: /balonmano de piso|balonmano playa|horarios|escenarios/i })).toHaveCount(0);
});

test('slider y aliados ofrecen controles accesibles y pausa', async ({ page }) => {
  await page.goto('/');
  const slider = page.locator('[data-labm-slider]');
  await expect(slider).toBeVisible();
  await expect(slider.locator('[data-labm-slide]')).toHaveCount(2);
  await expect(slider.getByText(/slide editorial en borrador/i)).toHaveCount(0);
  const initialIndicator = slider.locator('[data-labm-slide-to][aria-current="true"]');
  await expect(initialIndicator).toHaveAttribute('data-labm-slide-to', '0');
  await slider.getByRole('button', { name: /siguiente/i }).click();
  await expect(slider.locator('[data-labm-slide-to][aria-current="true"]')).toHaveAttribute('data-labm-slide-to', '1');
  await expect(slider.locator('[data-labm-slide]').nth(1)).toBeVisible();
  const pauseSlider = slider.locator('[data-labm-slider-pause]');
  await pauseSlider.click();
  await expect(slider).toHaveAttribute('data-labm-paused', 'true');
  await expect(pauseSlider).toHaveAttribute('aria-pressed', 'true');
  await expect(pauseSlider).toHaveText(/reanudar/i);

  const allies = page.locator('[data-labm-allies]');
  await expect(allies).toBeVisible();
  await expect(allies.getByRole('heading', { name: /aliados oficiales/i })).toBeVisible();
  await expect(allies.locator('.labm-allies__list > li')).toHaveCount(2);
  await expect(allies.getByText(/aliado editorial en borrador/i)).toHaveCount(0);
  const visualCopy = allies.locator('.labm-allies__visual');
  await expect(visualCopy).toHaveAttribute('aria-hidden', 'true');
  await expect(visualCopy).toHaveAttribute('inert', '');
  await expect(visualCopy).toHaveCSS('animation-name', 'labm-marquee');
  await expect(visualCopy).toHaveCSS('animation-direction', 'normal');
  await allies.getByRole('button', { name: /pausar/i }).click();
  await expect(allies).toHaveAttribute('data-labm-paused', 'true');
  await expect(visualCopy).toHaveCSS('animation-play-state', 'paused');
});

test('slider y aliados quedan estaticos con movimiento reducido', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.goto('/');
  const slider = page.locator('[data-labm-slider]');
  const allies = page.locator('[data-labm-allies]');
  await expect(slider).toBeVisible();
  await expect(allies).toBeVisible();
  await expect(allies).toHaveAttribute('data-labm-paused', 'true');
  await expect(allies.locator('.labm-allies__visual')).toHaveCSS('animation-name', 'none');
  const active = slider.locator('[data-labm-slide-to][aria-current="true"]');
  await expect(active).toHaveAttribute('data-labm-slide-to', '0');
  await page.waitForTimeout(7100);
  await expect(active).toHaveAttribute('data-labm-slide-to', '0');
});
