const { chromium } = require('playwright');

(async () => {
    const browser = await chromium.launch({ headless: true });
    const page = await browser.newPage();

    console.log('Testing site: https://hotel.darkube.app');

    try {
        // Test homepage
        const response = await page.goto('https://hotel.darkube.app', { timeout: 30000 });
        console.log(`Homepage status: ${response.status()}`);

        // Take screenshot
        await page.screenshot({ path: 'screenshot-home.png' });
        console.log('Screenshot saved: screenshot-home.png');

        // Check if login page loads
        const title = await page.title();
        console.log(`Page title: ${title}`);

        // Check for error messages
        const bodyText = await page.textContent('body');
        if (bodyText.includes('500') || bodyText.includes('Error')) {
            console.log('WARNING: Page contains error!');
        } else {
            console.log('Page loaded successfully!');
        }

    } catch (error) {
        console.error('Error:', error.message);
    }

    await browser.close();
})();
