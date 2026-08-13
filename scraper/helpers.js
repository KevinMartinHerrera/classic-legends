const fs = require('fs');
const path = require('path');

function parseArgs(argv) {
  const args = {};

  for (const item of argv) {
    if (!item.startsWith('--')) continue;

    const trimmed = item.slice(2);
    const separatorIndex = trimmed.indexOf('=');

    if (separatorIndex === -1) {
      args[trimmed] = true;
      continue;
    }

    const key = trimmed.slice(0, separatorIndex);
    const value = trimmed.slice(separatorIndex + 1);
    args[key] = value;
  }

  return args;
}

function cleanText(value) {
  return String(value ?? '')
    .replace(/\s+/g, ' ')
    .trim();
}

function normalizeUrl(value, baseUrl) {
  if (typeof value !== 'string') return null;

  const raw = cleanText(value).replace(/&amp;/g, '&');
  if (!raw || raw.startsWith('data:')) return null;

  let normalized = raw;

  if (normalized.startsWith('//')) {
    normalized = `https:${normalized}`;
  } else if (normalized.startsWith('/')) {
    normalized = new URL(normalized, baseUrl).toString();
  } else if (!/^https?:\/\//i.test(normalized)) {
    normalized = new URL(normalized, baseUrl).toString();
  }

  if (normalized.startsWith('http://')) {
    normalized = `https://${normalized.slice('http://'.length)}`;
  }

  try {
    return new URL(normalized).toString();
  } catch {
    return null;
  }
}

function buildPageUrl(baseUrl, pageNumber) {
  const url = new URL(baseUrl);
  url.searchParams.set('page', String(pageNumber));
  return url.toString();
}

function isPhotoYupooUrl(value) {
  try {
    const url = new URL(value);
    return url.protocol === 'https:' && url.hostname === 'photo.yupoo.com';
  } catch {
    return false;
  }
}

function unique(values) {
  return [...new Set(values.filter(Boolean))];
}

async function retry(fn, attempts, delayMs, label = 'operation') {
  let lastError = null;

  for (let attempt = 1; attempt <= attempts; attempt++) {
    try {
      return await fn(attempt);
    } catch (error) {
      lastError = error;

      if (attempt === attempts) {
        break;
      }

      console.log(`[retry] ${label} failed on attempt ${attempt}/${attempts}: ${error.message}`);
      await new Promise((resolve) => setTimeout(resolve, delayMs * attempt));
    }
  }

  throw lastError;
}

async function waitForPageSettled(page, timeoutMs) {
  try {
    await page.waitForLoadState('domcontentloaded', { timeout: timeoutMs });
  } catch {
    // Ignore timeout and continue with DOM checks.
  }

  try {
    await page.waitForLoadState('networkidle', { timeout: Math.min(timeoutMs, 10000) });
  } catch {
    // Yupoo can keep background requests open.
  }
}

async function autoScroll(page) {
  let previousHeight = -1;

  for (let round = 0; round < 30; round++) {
    const height = await page.evaluate(() => document.body.scrollHeight);

    if (height === previousHeight) {
      break;
    }

    previousHeight = height;

    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    await page.waitForTimeout(300);
  }

  await page.evaluate(() => window.scrollTo(0, 0));
}

async function loadPage(page, url, timeoutMs, attempts, referer = null) {
  return retry(
    async () => {
      await page.goto(url, {
        waitUntil: 'domcontentloaded',
        timeout: timeoutMs,
        referer: referer || undefined,
      });

      await waitForPageSettled(page, timeoutMs);

      const bodyText = await page.locator('body').innerText().catch(() => '');

      if (/567|access denied|forbidden/i.test(bodyText)) {
        throw new Error('Yupoo returned a blocked page (possibly EdgeOne 567).');
      }
    },
    attempts,
    1000,
    `load ${url}`
  );
}

function ensureParentDirectory(filePath) {
  fs.mkdirSync(path.dirname(filePath), { recursive: true });
}

module.exports = {
  parseArgs,
  cleanText,
  normalizeUrl,
  buildPageUrl,
  isPhotoYupooUrl,
  unique,
  retry,
  waitForPageSettled,
  autoScroll,
  loadPage,
  ensureParentDirectory,
};
