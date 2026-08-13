const path = require('path');

module.exports = {
  baseUrl: 'https://classic-football-fhirts052.x.yupoo.com/albums',
  outputFile: path.resolve(__dirname, '../storage/app/yupoo/products.json'),
  maxPages: null,
  timeoutMs: 45000,
  retries: 3,
  headless: true,
  userAgent:
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36',
  viewport: {
    width: 1440,
    height: 2200,
  },
};
