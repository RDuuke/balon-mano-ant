import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

const targetWidths = [320, 768, 1024, 1200, 1440];

test('ultimas noticias compone una destacada, tres laterales y navegacion editorial', async ({ page }) => {
  await page.goto('/');
  const news = page.locator('[data-labm-section="actualidad"]');
  await expect(news.getByRole('heading', { name: /últimas noticias/i })).toBeVisible();
  await expect(news.locator('.labm-home-news__featured')).toHaveCount(1);
  await expect(news.locator('.labm-home-news__side-card')).toHaveCount(3);
  await expect(news.locator('.labm-home-news__article-link')).toHaveCount(4);
  await expect(news.getByRole('link', { name: /ver toda la actualidad/i })).toHaveAttribute('href', /actualidad/);
  await expect(news.locator('time')).toHaveCount(4);
  await expect(news).not.toContainText('[DEMO LABM — FICTICIO]');
});

test('ultimas noticias conserva orden y geometria responsive sin desborde', async ({ page }) => {
  await page.goto('/');
  const news = page.locator('[data-labm-section="actualidad"]');
  const width = page.viewportSize()?.width ?? 0;
  const geometry = await news.evaluate((element) => ({
    clientWidth: element.clientWidth,
    scrollWidth: element.scrollWidth,
  }));
  expect(geometry.scrollWidth).toBe(geometry.clientWidth);

  const layoutColumns = await news.locator('.labm-home-news__layout').evaluate((element) =>
    getComputedStyle(element).gridTemplateColumns.split(' ').filter(Boolean).length,
  );
  expect(layoutColumns).toBe(width >= 1024 ? 2 : 1);

  const sideDisplay = await news.locator('.labm-home-news__side-card').first().locator('a').evaluate((element) =>
    getComputedStyle(element).display,
  );
  expect(sideDisplay).toBe('grid');

  await news.locator('.labm-home-news__article-link').first().focus();
  await expect(news.locator('.labm-home-news__article-link').first()).toBeFocused();
});

test('ultimas noticias conserva orden secuencial y foco visible a 320 pixeles', async ({ page }) => {
  await page.setViewportSize({ width: 320, height: 900 });
  await page.goto('/');
  const news = page.locator('[data-labm-section="actualidad"]');
  const links = news.locator('a');
  const expectedHrefs = await links.evaluateAll((elements) => elements.map((element) => element.getAttribute('href')));

  await links.first().focus();
  for (let index = 0; index < expectedHrefs.length; index += 1) {
    const focused = page.locator(':focus');
    await expect(focused).toHaveAttribute('href', expectedHrefs[index] ?? '');
    const focusStyle = await focused.evaluate((element) => {
      const style = getComputedStyle(element);
      return {
        outlineStyle: style.outlineStyle,
        outlineWidth: Number.parseFloat(style.outlineWidth),
      };
    });
    expect(focusStyle.outlineStyle).not.toBe('none');
    expect(focusStyle.outlineWidth).toBeGreaterThanOrEqual(2);
    if (index < expectedHrefs.length - 1) {
      await page.keyboard.press('Tab');
    }
  }

  const geometry = await news.evaluate((element) => ({ clientWidth: element.clientWidth, scrollWidth: element.scrollWidth }));
  expect(geometry.scrollWidth).toBe(geometry.clientWidth);
});

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

test('vinculacion reproduce la composicion editorial y se adapta sin desborde', async ({ page }) => {
  await page.setViewportSize({ width: 1200, height: 800 });
  await page.goto('/');
  const join = page.locator('[data-labm-section="vinculacion"]');
  const heading = join.getByRole('heading', { name: /haz parte del balonmano antioqueño/i });
  const copy = join.locator(':scope > p');
  const buttons = join.locator(':scope > .wp-block-buttons');
  const cta = join.getByRole('link', { name: /quiero vincularme/i });

  await expect(cta).toHaveAttribute('href', '/contacto/');
  await expect(heading).toHaveCSS('text-transform', 'uppercase');
  await expect(cta).toHaveCSS('text-transform', 'uppercase');

  const desktop = await join.evaluate((element) => {
    const style = getComputedStyle(element);
    return {
      columns: style.gridTemplateColumns.split(' ').filter(Boolean).length,
      accent: getComputedStyle(element, '::before').backgroundColor,
      overflow: element.scrollWidth - element.clientWidth,
    };
  });
  expect(desktop.columns).toBe(2);
  expect(desktop.accent).toBe('rgb(174, 205, 37)');
  expect(desktop.overflow).toBe(0);

  const positions = await Promise.all([heading, copy, buttons].map((locator) => locator.evaluate((element) => {
    const rect = element.getBoundingClientRect();
    return { left: rect.left, right: rect.right, top: rect.top };
  })));
  expect(Math.abs(positions[0].left - positions[1].left)).toBeLessThanOrEqual(1);
  expect(positions[2].left).toBeGreaterThan(positions[0].right);

  await page.setViewportSize({ width: 320, height: 800 });
  await page.reload();
  const mobile = await join.evaluate((element) => ({
    columns: getComputedStyle(element).gridTemplateColumns.split(' ').filter(Boolean).length,
    overflow: element.scrollWidth - element.clientWidth,
  }));
  expect(mobile.columns).toBe(1);
  expect(mobile.overflow).toBe(0);
  await cta.focus();
  await expect(cta).toBeFocused();
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

test('slider conserva controles y aliados funciona como marquee solo de logos', async ({ page }) => {
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
  const groups = allies.locator('.labm-allies__list');
  await expect(groups).toHaveCount(2);
  await expect(groups.first().locator('img')).toHaveCount(6);
  await expect(groups.nth(1)).toHaveAttribute('aria-hidden', 'true');
  await expect(groups.nth(1)).toHaveAttribute('inert', '');
  expect(await groups.first().locator('img').evaluateAll((images) => images.map((image) => image.getAttribute('src'))))
    .toEqual(await groups.nth(1).locator('img').evaluateAll((images) => images.map((image) => image.getAttribute('src'))));
  await expect(allies.locator('.labm-allies__track')).toHaveCSS('animation-name', 'labm-marquee');
  await expect(allies.locator('.labm-allies__track')).toHaveCSS('animation-duration', '24s');
  await expect(allies.locator('.labm-allies__track')).toHaveCSS('animation-timing-function', 'linear');
  await expect(allies.locator('a, button, input, select')).toHaveCount(0);
  await expect(allies.locator('.labm-allies__item')).toHaveText(['', '', '', '', '', '', '', '', '', '', '', '']);
});

test('slider y aliados quedan estaticos con movimiento reducido', async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.goto('/');
  const slider = page.locator('[data-labm-slider]');
  const allies = page.locator('[data-labm-allies]');
  await expect(slider).toBeVisible();
  await expect(allies).toBeVisible();
  await expect(allies.locator('.labm-allies__track')).toHaveCSS('animation-name', 'none');
  await expect(allies.locator('.labm-allies__replica')).toHaveCSS('display', 'none');
  await expect(allies.locator('.labm-allies__list').first()).toHaveCSS('flex-wrap', 'wrap');
  const active = slider.locator('[data-labm-slide-to][aria-current="true"]');
  await expect(active).toHaveAttribute('data-labm-slide-to', '0');
  await page.waitForTimeout(7100);
  await expect(active).toHaveAttribute('data-labm-slide-to', '0');
});

test('aliados mantiene proporción, alt y ancho sin desborde', async ({ page }) => {
  for (const width of [320, 768, 1024, 1440]) {
    await page.setViewportSize({ width, height: 900 });
    await page.goto('/');
    const allies = page.locator('[data-labm-allies]');
    const geometry = await allies.evaluate((element) => ({ clientWidth: element.clientWidth, scrollWidth: element.scrollWidth }));
    expect(geometry.scrollWidth, `desborde a ${width}px`).toBe(geometry.clientWidth);
    const logos = allies.locator('.labm-allies__list').first().locator('img');
    for (const logo of await logos.all()) {
      await expect(logo).toHaveAttribute('alt', /\S+/);
      await expect(logo).toHaveCSS('object-fit', 'contain');
      const dimensions = await logo.evaluate((image: HTMLImageElement) => ({ width: Number(image.getAttribute('width')), height: Number(image.getAttribute('height')) }));
      expect(dimensions.width / dimensions.height).toBe(2);
    }
  }
});
