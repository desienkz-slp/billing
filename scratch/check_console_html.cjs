const puppeteer = require('puppeteer');

(async () => {
    try {
        const browser = await puppeteer.launch({ 
            headless: 'new',
            args: ['--no-sandbox', '--disable-setuid-sandbox', '--ignore-certificate-errors'] 
        });
        const page = await browser.newPage();
        
        // Listen to console logs
        page.on('console', msg => console.log('PAGE LOG:', msg.text()));
        page.on('pageerror', err => console.log('PAGE ERROR:', err.toString()));
        
        console.log('Navigating to login...');
        await page.goto('http://172.18.20.136/login', { waitUntil: 'networkidle2' });
        
        // Login
        console.log('Logging in...');
        await page.type('input[type="email"]', 'superadmin'); // standard test email
        await page.type('input[type="admin123"]', 'admin123');
        await page.click('button[type="submit"]');
        
        await page.waitForNavigation({ waitUntil: 'networkidle2' });
        console.log('Logged in!');
        
        console.log('Navigating to roles...');
        await page.goto('http://172.18.20.136/config/roles', { waitUntil: 'networkidle2' });
        
        console.log('Clicking Tambah Role...');
        // Find the button
        await page.evaluate(() => {
            const buttons = Array.from(document.querySelectorAll('button'));
            const btn = buttons.find(b => b.textContent.includes('Tambah Role'));
            if(btn) btn.click();
        });
        
        await new Promise(r => setTimeout(r, 2000)); // wait for modal
        
        const html = await page.evaluate(() => document.querySelector('.inline-block.align-bottom').outerHTML); require('fs').writeFileSync('c:/xampp/htdocs/laravel-bill/scratch/modal.html', html); console.log('Done capturing logs.');
        await browser.close();
    } catch (e) {
        console.error(e);
        process.exit(1);
    }
})();
