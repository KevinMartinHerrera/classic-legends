const fs = require('fs');
const { chromium } = require('playwright');
const config = require('./playwright.config');
const {
  parseArgs,
  normalizeUrl,
  buildPageUrl,
  unique,
  autoScroll,
  loadPage,
  ensureParentDirectory,
} = require('./helpers');

const args = parseArgs(process.argv.slice(2));
const requestedPages = Number.isFinite(Number(args.pages)) ? Number(args.pages) : null;
const maxPages = Number.isFinite(Number(config.maxPages)) ? Number(config.maxPages) : Number.POSITIVE_INFINITY;
const pageLimit = Math.max(1, Math.min(requestedPages ?? maxPages, maxPages));
const startPage = Number.isFinite(Number(args['start-page'])) ? Number(args['start-page']) : 1;
const baseUrl = typeof args.url === 'string' && args.url ? args.url : config.baseUrl;
const outputFile = typeof args['output-file'] === 'string' && args['output-file']
  ? args['output-file']
  : config.outputFile;

async function scrapeAlbumIndex(page, pageNumber) {
  const pageUrl = buildPageUrl(baseUrl, pageNumber);

  console.log(`Pagina ${pageNumber}: leyendo listado...`);
    await loadPage(page, pageUrl, config.timeoutMs, config.retries);
  await autoScroll(page);

  const result = await page.evaluate((currentPageUrl) => {
    const clean = (value) => String(value ?? '').replace(/\s+/g, ' ').trim();
    const pageTitle = clean(document.title || '');

    const categoryFromTitle = () => {
      if (!pageTitle) return null;

      const parts = pageTitle.split(' | ');
      const categoryIndex = parts.findIndex((part) => /category/i.test(part));

      if (categoryIndex > 0) {
        return clean(parts[categoryIndex - 1]);
      }

      return clean(parts[0] || '');
    };

    const isPhotoUrl = (value) => {
      try {
        const url = new URL(value);
        return url.protocol === 'https:' && url.hostname === 'photo.yupoo.com';
      } catch {
        return false;
      }
    };

    const normalize = (value) => {
      if (typeof value !== 'string') return null;

      const raw = clean(value).replace(/&amp;/g, '&');
      if (!raw || raw.startsWith('data:')) return null;

      let normalized = raw;
      if (normalized.startsWith('//')) {
        normalized = `https:${normalized}`;
      } else if (normalized.startsWith('/')) {
        normalized = new URL(normalized, currentPageUrl).toString();
      } else if (!/^https?:\/\//i.test(normalized)) {
        normalized = new URL(normalized, currentPageUrl).toString();
      }

      if (normalized.startsWith('http://')) {
        normalized = `https://${normalized.slice('http://'.length)}`;
      }

      try {
        return new URL(normalized).toString();
      } catch {
        return null;
      }
    };

    const extractAlbumId = (value) => {
      try {
        const url = new URL(value);
        const match = url.pathname.match(/\/(?:albums|photos)\/(\d+)/);
        return match ? match[1] : null;
      } catch {
        return null;
      }
    };

    const cards = Array.from(
      document.querySelectorAll(
        'main.showindex__gallerycardwrap a.album__main, main.showindex__gallerycardwrap a.album3__main, a.album__main[href*="/albums/"], a.album3__main[href*="/albums/"]'
      )
    );

    const albums = cards.map((element) => {
      const href = element.getAttribute('href') || element.href || '';
      const url = normalize(href);
      const albumId = url ? extractAlbumId(url) : null;

      if (!url || !albumId) {
        return null;
      }

      const titleNode = element.querySelector('.album3__title, .album__title, .showindex__title, [class*="title"]');
      const imageNode = element.querySelector('img[data-origin-src], img[data-src], img[src]');
      const portada = normalize(
        imageNode?.getAttribute('data-origin-src') || imageNode?.getAttribute('data-src') || imageNode?.getAttribute('src')
      );

      return {
        album_id: albumId,
        titulo: clean(titleNode?.textContent || element.getAttribute('title') || element.getAttribute('aria-label') || `Album ${albumId}`),
        url,
        source_page: currentPageUrl,
        categoria: null,
        portada: portada && isPhotoUrl(portada) ? portada : null,
      };
    });

    return {
      category: categoryFromTitle(),
      albums: albums.filter(Boolean),
    };
  }, pageUrl);
  const uniqueAlbums = unique(result.albums.map((album) => JSON.stringify({
    ...album,
    categoria: result.category || album.categoria || null,
  }))).map((item) => JSON.parse(item));

  console.log(`Pagina ${pageNumber}: ${uniqueAlbums.length} albumes detectados`);
  return uniqueAlbums;
}

async function scrapeAlbumDetail(page, album) {
  console.log(`  -> Album: ${album.titulo}`);
  try {
    await loadPage(page, album.url, config.timeoutMs, config.retries, album.source_page || baseUrl);
  } catch {
    console.log('     pagina bloqueada por EdgeOne 567, saltando album');
    return null;
  }

  for (let attempt = 1; attempt <= 2; attempt++) {
    await autoScroll(page);

    const detail = await page.evaluate((currentAlbum) => {
      const clean = (value) => String(value ?? '').replace(/\s+/g, ' ').trim();
      const isPhotoUrl = (value) => {
        try {
          const url = new URL(value);
          return url.protocol === 'https:' && url.hostname === 'photo.yupoo.com';
        } catch {
          return false;
        }
      };

      const normalize = (value) => {
        if (typeof value !== 'string') return null;
        const raw = clean(value).replace(/&amp;/g, '&');
        if (!raw || raw.startsWith('data:')) return null;

        let normalized = raw;
        if (normalized.startsWith('//')) {
          normalized = `https:${normalized}`;
        } else if (normalized.startsWith('/')) {
          normalized = new URL(normalized, currentAlbum.url).toString();
        } else if (!/^https?:\/\//i.test(normalized)) {
          normalized = new URL(normalized, currentAlbum.url).toString();
        }

        if (normalized.startsWith('http://')) {
          normalized = `https://${normalized.slice('http://'.length)}`;
        }

        try {
          return new URL(normalized).toString();
        } catch {
          return null;
        }
      };

      const canonicalizePhotoUrl = (value) => {
        const normalized = normalize(value);

        if (!normalized) {
          return null;
        }

        const url = new URL(normalized);
        const parts = url.pathname.split('/').filter(Boolean);

        if (parts.length >= 2) {
          const last = parts[parts.length - 1].toLowerCase();

          if (['big.jpg', 'small.jpg', 'mid.jpg', 'thumb.jpg', 'large.jpg'].includes(last)) {
            parts[parts.length - 1] = 'big.jpg';
            url.pathname = '/' + parts.join('/');
          }
        }

        return url.toString();
      };

      const bodyText = clean(document.body?.innerText || '');
      const pageTitle = clean(document.title || '');
      const isBlocked = /567|restricted access|blocked|access denied|forbidden/i.test(bodyText + ' ' + pageTitle);

      if (isBlocked) {
        return {
          blocked: true,
          titulo: currentAlbum.titulo,
          categoria: currentAlbum.categoria || null,
          portada: null,
          imagenes: [],
        };
      }

      const imageUrls = [];
      const seen = new Set();
      const stripVariant = (value) => value.replace(/([?&#].*)$/, '');
      const push = (raw, key) => {
        const normalized = canonicalizePhotoUrl(raw);
        if (!normalized) return;
        if (!isPhotoUrl(normalized)) return;

        const stable = stripVariant(normalized);
        const bucket = key ? `${key}::${stable}` : stable;

        if (seen.has(bucket)) return;
        seen.add(bucket);
        imageUrls.push(stable);
      };

      const cards = Array.from(document.querySelectorAll(
        '.showalbum__imagecardwrap .showalbum__children.image__main[data-id], .showalbum__imagecardwrap .image__imagewrap[data-photoid], .showalbum__imagecardwrap .image__clickhandle[data-photoid]'
      ));

      if (cards.length > 0) {
        cards.forEach((card) => {
          const key = card.getAttribute('data-id') || card.getAttribute('data-photoid') || card.querySelector('[data-photoid]')?.getAttribute('data-photoid') || null;
          const node = card.querySelector('img[data-origin-src], img[data-src], img[src], source[data-src], source[src]');

          if (!node) {
            return;
          }

          push(node.getAttribute('data-origin-src'), key);
          push(node.getAttribute('data-src'), key);
          push(node.getAttribute('src'), key);

          const srcset = node.getAttribute('srcset');
          if (srcset) {
            srcset.split(',').forEach((part) => {
              const candidate = part.trim().split(/\s+/)[0];
              push(candidate, key);
            });
          }
        });
      }

      if (imageUrls.length === 0) {
        document.querySelectorAll(
          '.showalbum__imagecardwrap img, .showalbum__imagecardwrap source, #viewer_thumbnails img, .viewer__img'
        ).forEach((node) => {
          push(node.getAttribute('data-origin-src'));
          push(node.getAttribute('data-src'));
          push(node.getAttribute('src'));

          const srcset = node.getAttribute('srcset');
          if (srcset) {
            srcset.split(',').forEach((part) => {
              const candidate = part.trim().split(/\s+/)[0];
              push(candidate);
            });
          }
        });
      }

      const isSizeVariant = (value) => /\/(?:big|small|mid|thumb|large)\.jpg$/i.test(value);
      const byFolder = new Map();

      for (const url of imageUrls) {
        const parsed = new URL(url);
        const segments = parsed.pathname.split('/').filter(Boolean);
        const folderKey = segments.slice(0, -1).join('/');
        const current = byFolder.get(folderKey);

        if (!current) {
          byFolder.set(folderKey, url);
          continue;
        }

        const currentIsVariant = isSizeVariant(current);
        const nextIsVariant = isSizeVariant(url);

        if (currentIsVariant && !nextIsVariant) {
          byFolder.set(folderKey, url);
        }
      }

    const titleNode = document.querySelector('.showalbum__title, h1, meta[property="og:title"]');
    let title = '';

    if (titleNode && titleNode.tagName === 'META') {
      title = clean(titleNode.getAttribute('content'));
    } else {
      title = clean(titleNode?.textContent || '');
    }

    if (!title) {
      title = clean(document.title);
    }

    if (title.includes(' | ')) {
      title = clean(title.split(' | ')[0]);
    }

      const categoryNode = document.querySelector('.yupoo-crumbs a[href*="/categories/"]');
      const category = clean(categoryNode?.textContent || '');

      return {
        blocked: false,
        titulo: title,
        categoria: category || null,
        portada: imageUrls[0] || null,
        imagenes: Array.from(byFolder.values()),
      };
    }, album);

    if (detail.blocked) {
      console.log('     pagina bloqueada por EdgeOne 567, saltando album');
      return null;
    }

    if (detail.imagenes.length > 0 || attempt === 2) {
      return {
        ...album,
        titulo: detail.titulo || album.titulo,
        categoria: detail.categoria || album.categoria,
        portada: detail.portada || album.portada,
        imagenes: unique(detail.imagenes),
      };
    }

    console.log('     reintentando extraccion de imagenes...');
  }

  return {
    ...album,
    imagenes: [],
  };
}

async function main() {
  const browser = await chromium.launch({
    headless: config.headless,
    args: ['--disable-blink-features=AutomationControlled', '--no-sandbox'],
  });

  const context = await browser.newContext({
    userAgent: config.userAgent,
    viewport: config.viewport,
    locale: 'es-ES',
    ignoreHTTPSErrors: true,
    extraHTTPHeaders: {
      'Accept-Language': 'es-ES,es;q=0.9,en;q=0.8',
    },
  });

  await context.addInitScript(() => {
    Object.defineProperty(navigator, 'webdriver', {
      get: () => undefined,
    });
  });

  const page = await context.newPage();
  page.setDefaultTimeout(config.timeoutMs);
  page.setDefaultNavigationTimeout(config.timeoutMs);

  try {
    const albums = [];

    for (let offset = 0; offset < pageLimit; offset++) {
      const pageNumber = startPage + offset;
      const indexAlbums = await scrapeAlbumIndex(page, pageNumber);

      if (indexAlbums.length === 0) {
        console.log(`Pagina ${pageNumber}: no se encontraron albumes, deteniendo.`);
        break;
      }

      for (const album of indexAlbums) {
        const detail = await scrapeAlbumDetail(page, album);

        if (!detail) {
          continue;
        }

        if (!detail.portada && detail.imagenes.length > 0) {
          detail.portada = detail.imagenes[0];
        }

        detail.imagenes = unique(detail.imagenes);
        albums.push(detail);

        console.log(`__YPOO_ALBUM__${JSON.stringify(detail)}`);
        console.log(`     guardado: ${detail.titulo} | ${detail.imagenes.length} imagenes`);
      }
    }

    ensureParentDirectory(outputFile);
    fs.writeFileSync(outputFile, `${JSON.stringify(albums, null, 2)}\n`, 'utf8');

    console.log(`JSON generado: ${outputFile}`);
    console.log(`Total albumes: ${albums.length}`);
  } finally {
    await context.close();
    await browser.close();
  }
}

main().catch((error) => {
  console.error(error.message || error);
  process.exitCode = 1;
});
