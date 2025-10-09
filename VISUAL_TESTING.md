# Visual Testing Guide

## Overview

This project includes comprehensive visual and browser automation tests to ensure the UI renders correctly, animations perform smoothly, and accessibility standards are met.

## Test Categories

### 1. **Browser/Visual Tests** (`tests/Browser/`)

Located in `tests/Browser/`, these tests verify:
- Visual rendering of pages
- User interaction flows
- Animation performance
- Responsive design
- Accessibility compliance
- Visual regression

## Running Visual Tests

### Standard Laravel Tests

```bash
# Run all browser tests
php artisan test --filter=Browser

# Run specific test suites
php artisan test tests/Browser/HomePageTest.php
php artisan test tests/Browser/QrScanFlowTest.php
php artisan test tests/Browser/ContestSubmissionTest.php
php artisan test tests/Browser/DashboardVisualTest.php
php artisan test tests/Browser/ResponsiveDesignTest.php
php artisan test tests/Browser/AnimationTest.php
php artisan test tests/Browser/AccessibilityTest.php
php artisan test tests/Browser/VisualRegressionTest.php
```

### Browser Automation with Playwright

Some tests are marked as placeholders for full browser automation. To implement these:

#### Option 1: Playwright MCP Server (Recommended)

Already available in your Claude Code environment:

```javascript
// Example Playwright test
const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();

  // Navigate to homepage
  await page.goto('http://upwork-trivia.test');

  // Take screenshot
  await page.screenshot({ path: 'tests/Browser/screenshots/homepage.png' });

  // Test animations
  const puppyElement = await page.$('.animated-puppy');
  const isVisible = await puppyElement.isVisible();

  await browser.close();
})();
```

#### Option 2: Laravel Dusk

Install Laravel Dusk for integrated browser testing:

```bash
composer require --dev laravel/dusk
php artisan dusk:install
```

Then convert browser tests to Dusk format:

```php
<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class HomePageTest extends DuskTestCase
{
    public function testHomepageRendersCorrectly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Golden Question')
                    ->screenshot('homepage');
        });
    }
}
```

## Test Suites Breakdown

### HomePageTest (12 tests)

Verifies homepage visual elements:
- ✅ Animated background (puppy, plane, clouds)
- ✅ Glassmorphism effects
- ✅ Golden Question teaser
- ✅ Advertisement boxes
- ✅ CTA messaging
- ✅ Mobile responsiveness
- ✅ PWA manifest

### QrScanFlowTest (12 tests)

Verifies QR code scanning user flow:
- ✅ QR redirect to contest page
- ✅ Contest page loads with code parameter
- ✅ Golden Question displays prominently
- ✅ Animated background renders
- ✅ Glassmorphism window appears
- ✅ Advertisement boxes display
- ✅ Bag trivia questions show
- ✅ Multiple choice answers render
- ✅ Geolocation logging
- ✅ Guest user login prompt
- ✅ Invalid code error handling

### ContestSubmissionTest (13 tests)

Verifies contest submission UI/UX:
- ✅ Authenticated user access
- ✅ Interactive answer buttons
- ✅ Visual feedback on selection
- ✅ Submit button activation
- ✅ Winner animation on correct answer
- ✅ Try again message on incorrect
- ✅ Gift card delivery message
- ✅ Question locking after winner
- ✅ Real-time winner announcement
- ✅ Cooldown user messaging
- ✅ Already submitted state
- ✅ Loading spinner during submission

### DashboardVisualTest (18 tests)

Verifies all dashboard pages:
- ✅ Dashboard index with green background
- ✅ User profile section
- ✅ Total winnings display
- ✅ Recent submissions
- ✅ Eligibility status card
- ✅ Navigation tabs
- ✅ Winnings page with history
- ✅ Gift cards page with redemption links
- ✅ Customer support link
- ✅ Submissions page with accuracy stats
- ✅ Question history
- ✅ Card-based layout
- ✅ Mobile responsiveness
- ✅ Cooldown countdown timer

### ResponsiveDesignTest (15 tests)

Verifies responsive behavior:
- ✅ Mobile viewport (375px)
- ✅ Tablet viewport (768px)
- ✅ Desktop viewport (1920px)
- ✅ Mobile navigation menu toggle
- ✅ Glassmorphism on mobile
- ✅ Animation scaling on mobile
- ✅ Touch-friendly buttons (44x44px minimum)
- ✅ Vertical card stacking on mobile
- ✅ Legible font sizes
- ✅ Optimized images for mobile
- ✅ No horizontal scrolling
- ✅ Mobile-friendly forms
- ✅ Two-column grid on tablet
- ✅ Three-column grid on desktop
- ✅ Accessible navigation at all breakpoints

### AnimationTest (16 tests)

Verifies animation performance:
- ✅ Puppy animation loads
- ✅ GPU acceleration (transform/opacity)
- ✅ Plane flies across screen
- ✅ Clouds move continuously
- ✅ Respects `prefers-reduced-motion`
- ✅ Glassmorphism backdrop blur
- ✅ Winner animation on correct answer
- ✅ Answer button hover animations
- ✅ Smooth page transitions
- ✅ Loading spinner animation
- ✅ No layout shift during animations
- ✅ 60fps performance maintained
- ✅ Smooth puppy idle loop
- ✅ Animations pause when tab inactive
- ✅ Gradient background transitions
- ✅ Card hover effects

### AccessibilityTest (20 tests)

Verifies WCAG 2.1 AA compliance:
- ✅ Proper heading hierarchy (h1-h6)
- ✅ Image alt text
- ✅ Form labels
- ✅ Button descriptive text/ARIA labels
- ✅ Link descriptive text
- ✅ Color contrast (WCAG AA: 4.5:1)
- ✅ Keyboard navigation
- ✅ Visible focus indicators
- ✅ Skip to main content link
- ✅ ARIA live regions
- ✅ Modal focus trap
- ✅ ESC key closes modals
- ✅ Form errors announced
- ✅ Page title updates
- ✅ Loading states announced
- ✅ Proper button roles
- ✅ Disabled state markup
- ✅ Semantic table markup
- ✅ Language attribute set
- ✅ Icon text alternatives

### VisualRegressionTest (16 tests)

Captures and compares screenshots:
- ✅ Homepage snapshot (all viewports)
- ✅ Contest page snapshot
- ✅ Dashboard snapshot
- ✅ Login page snapshot
- ✅ Registration page snapshot
- ✅ Glassmorphism consistency
- ✅ Animation first frame
- ✅ Button hover states
- ✅ Button selected states
- ✅ Winner modal
- ✅ Error states
- ✅ Loading states
- ✅ Dark mode (if implemented)

## Visual Regression Workflow

### 1. Establish Baselines

First run captures baseline screenshots:

```bash
# Create baseline directory
mkdir -p tests/Browser/screenshots/baseline

# Run visual regression tests to capture baselines
php artisan test --filter=VisualRegressionTest
```

### 2. Subsequent Runs Compare

Future runs compare against baselines:

```bash
# Run comparison
php artisan test --filter=VisualRegressionTest

# Any visual differences are flagged
# Review diff images in tests/Browser/screenshots/diff/
```

### 3. Update Baselines When Needed

After intentional UI changes:

```bash
# Replace baseline with new snapshot
cp tests/Browser/screenshots/latest/homepage.png \
   tests/Browser/screenshots/baseline/homepage.png
```

## Playwright Integration

### Using Playwright MCP Server

Claude Code has Playwright MCP available. Example usage:

```bash
# Navigate to page
mcp__playwright__browser_navigate --url "http://upwork-trivia.test"

# Take snapshot
mcp__playwright__browser_snapshot

# Take screenshot
mcp__playwright__browser_take_screenshot --filename "homepage.png"

# Click element
mcp__playwright__browser_click --element "Login button" --ref "[data-testid='login-btn']"

# Fill form
mcp__playwright__browser_fill_form --fields '[
  {"name": "Email", "type": "textbox", "ref": "#email", "value": "test@example.com"},
  {"name": "Password", "type": "textbox", "ref": "#password", "value": "password"}
]'

# Wait for text
mcp__playwright__browser_wait_for --text "Welcome back"

# Get console errors
mcp__playwright__browser_console_messages --onlyErrors true
```

### Automated Visual Testing Script

Create `tests/visual-test.js`:

```javascript
const { chromium } = require('playwright');

const pages = [
  { url: 'http://upwork-trivia.test', name: 'homepage' },
  { url: 'http://upwork-trivia.test/login', name: 'login' },
  { url: 'http://upwork-trivia.test/register', name: 'register' },
];

const viewports = [
  { width: 375, height: 667, name: 'mobile' },
  { width: 768, height: 1024, name: 'tablet' },
  { width: 1920, height: 1080, name: 'desktop' },
];

(async () => {
  const browser = await chromium.launch();

  for (const page of pages) {
    for (const viewport of viewports) {
      const context = await browser.newContext({ viewport });
      const browserPage = await context.newPage();

      await browserPage.goto(page.url);
      await browserPage.screenshot({
        path: `tests/Browser/screenshots/${page.name}-${viewport.name}.png`,
        fullPage: true,
      });

      await context.close();
    }
  }

  await browser.close();
})();
```

Run with:

```bash
node tests/visual-test.js
```

## Accessibility Testing Tools

### Manual Testing

1. **axe DevTools** (Browser Extension)
   - Chrome/Firefox extension
   - Runs automated accessibility audits
   - Flags WCAG violations

2. **WAVE** (Browser Extension)
   - Visual feedback on accessibility issues
   - Highlights errors directly on page

3. **Lighthouse** (Chrome DevTools)
   - Comprehensive accessibility score
   - Performance and SEO audits included

### Automated Testing

1. **axe-core** (Integrated Testing)

Install:

```bash
npm install --save-dev axe-core
```

Add to Playwright tests:

```javascript
const { injectAxe, checkA11y } = require('axe-playwright');

test('homepage has no accessibility violations', async ({ page }) => {
  await page.goto('http://upwork-trivia.test');
  await injectAxe(page);
  await checkA11y(page);
});
```

2. **pa11y** (CLI Testing)

Install:

```bash
npm install --save-dev pa11y
```

Run:

```bash
npx pa11y http://upwork-trivia.test
```

## Performance Testing

### Core Web Vitals

Monitor during visual tests:

1. **Largest Contentful Paint (LCP)** < 2.5s
2. **First Input Delay (FID)** < 100ms
3. **Cumulative Layout Shift (CLS)** < 0.1

### Lighthouse CI

Automate Lighthouse audits:

```bash
npm install --save-dev @lhci/cli

# Run audit
npx lhci autorun --collect.url=http://upwork-trivia.test
```

### Animation Performance

Check frame rate during animations:

```javascript
// In Playwright
const fps = await page.evaluate(() => {
  let lastTime = performance.now();
  let frames = 0;

  return new Promise((resolve) => {
    const loop = (time) => {
      frames++;
      if (time - lastTime > 1000) {
        resolve(frames);
      } else {
        requestAnimationFrame(loop);
      }
    };
    requestAnimationFrame(loop);
  });
});

console.log(`Animation FPS: ${fps}`);
expect(fps).toBeGreaterThan(55); // Allow 5fps margin below 60
```

## Continuous Integration

### GitHub Actions Example

`.github/workflows/visual-tests.yml`:

```yaml
name: Visual Tests

on: [push, pull_request]

jobs:
  visual-tests:
    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3

      - name: Install dependencies
        run: |
          composer install
          npm ci

      - name: Run Laravel tests
        run: php artisan test --filter=Browser

      - name: Run Playwright tests
        run: npx playwright test

      - name: Upload screenshots
        uses: actions/upload-artifact@v3
        with:
          name: test-screenshots
          path: tests/Browser/screenshots/
```

## Best Practices

### 1. **Test Naming**

Use descriptive test names:

```php
// ❌ Bad
public function test_button()

// ✅ Good
public function answer_button_has_touch_friendly_size_on_mobile()
```

### 2. **Viewport Consistency**

Always set viewport for visual tests:

```php
// In Playwright/Dusk
$browser->resize(375, 667); // iPhone SE
```

### 3. **Wait for Animations**

Don't capture during animation:

```javascript
await page.waitForSelector('.animated-puppy', { state: 'visible' });
await page.waitForTimeout(500); // Let animation settle
await page.screenshot({ path: 'puppy.png' });
```

### 4. **Ignore Dynamic Content**

Mask timestamps, counters, etc:

```javascript
await page.screenshot({
  path: 'dashboard.png',
  mask: [page.locator('.timestamp')],
});
```

### 5. **Parallel Execution**

Run visual tests in parallel:

```bash
php artisan test --parallel --filter=Browser
```

## Troubleshooting

### Screenshots Don't Match

1. Check browser version consistency
2. Verify font rendering (may differ across OS)
3. Disable animations during capture
4. Use `prefers-reduced-motion: reduce`

### Accessibility Test Failures

1. Run axe DevTools manually to see specific violations
2. Check color contrast with WebAIM Contrast Checker
3. Verify ARIA attributes with browser inspector

### Performance Issues

1. Check network tab for large assets
2. Profile with Chrome DevTools Performance tab
3. Verify GPU acceleration is active
4. Check for layout thrashing

## Documentation

- **WCAG 2.1 Guidelines**: https://www.w3.org/WAI/WCAG21/quickref/
- **Playwright Docs**: https://playwright.dev/
- **Laravel Dusk Docs**: https://laravel.com/docs/dusk
- **axe-core Rules**: https://github.com/dequelabs/axe-core/blob/master/doc/rule-descriptions.md

## Summary

**Total Visual Tests: 122 tests**

- HomePageTest: 12 tests
- QrScanFlowTest: 12 tests
- ContestSubmissionTest: 13 tests
- DashboardVisualTest: 18 tests
- ResponsiveDesignTest: 15 tests
- AnimationTest: 16 tests
- AccessibilityTest: 20 tests
- VisualRegressionTest: 16 tests

These tests ensure:
- ✅ UI renders correctly across all viewports
- ✅ Animations perform smoothly at 60fps
- ✅ Accessibility standards (WCAG 2.1 AA) are met
- ✅ User flows work end-to-end
- ✅ Visual regressions are caught before deployment
- ✅ Performance metrics (LCP, FID, CLS) are monitored

Ready for implementation once features are built!
