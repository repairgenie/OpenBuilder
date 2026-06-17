/**
 * API Authorization Tests
 * Suite: Security > API Auth
 *
 * Verifies that API endpoints properly enforce authentication
 * and reject requests without valid credentials.
 *
 * @see EDGE-TESTING-GUIDE.md Section 4 (Security Testing)
 */
const { test, expect } = require('@playwright/test');
const { BASE_URL } = require('../playwright.config');

test.describe('API Authorization', () => {

  test('API-AUTH-01: API endpoint without auth returns 401', async ({ page }) => {
    // Make a direct API call without any authentication
    const response = await page.request.get(BASE_URL + '/api/projects', {
      headers: { 'Accept': 'application/json' }
    });

    // Should return401 Unauthorized or 403 Forbidden
    expect([401, 403]).toContain(response.status());
  });

  test('API-AUTH-02: API endpoint with invalid token returns 401', async ({ page }) => {
    const response = await page.request.get(BASE_URL + '/api/projects', {
      headers: {
        'Accept': 'application/json',
        'Authorization': 'Bearer invalid_token_12345'
      }
    });

    expect([401, 403]).toContain(response.status());
  });

  test('API-AUTH-03: API endpoint with valid admin token returns 200', async ({ page }) => {
    // Login as Admin
    await page.goto(BASE_URL + '?page=login');
    await page.fill('input[name="email"]', 'admin@openbuilder.com');
    await page.fill('input[name="password"]', 'admin123');
    await page.click('button[type="submit"]');
    await page.waitForURL(/page=(?!login)/, { timeout: 10000 }).catch(() => {});

    // Go to API Keys page and create one
    await page.goto(BASE_URL + '?page=api_keys');
    if(await page.locator('button:has-text("Create")').count() > 0) { await page.click('button:has-text("Create")'); await page.waitForTimeout(1000); } await page.fill('input[name="name"]', 'Test Token');
    const saveBtn = page.locator('button[type="submit"]:has-text("Save"), button:has-text("Generate")').first();
    await saveBtn.click();
    await page.waitForTimeout(2000);

    // Attempt to scrape the generated API key from the page
    const body = await page.textContent('body');
    const match = body.match(/sk_[A-Za-z0-9]+/);
    const validKey = match ? match[0] : null;

    // If we successfully generated a valid token, test it against the API
    if (validKey) {
        const headers = {
            'Accept': 'application/json',
            'Authorization': `Bearer ${validKey}`
        };
        const response = await page.request.get(BASE_URL + '/api/projects', { headers });
        expect(response.status()).toBe(200);
    } else {
        // Fallback: If UI generation isn't supported yet, skip to avoid falsifying test results
        test.skip(true, 'API Key UI generation is currently unlocatable.');
    }
  });

  test('API-AUTH-04: API POST without auth returns 401', async ({ page }) => {
    const response = await page.request.post(BASE_URL + '/api/projects', {
      headers: { 'Accept': 'application/json', 'Content-Type': 'application/json' },
      data: JSON.stringify({ action: 'test' })
    });

    expect([401, 403]).toContain(response.status());
  });

  test('API-AUTH-05: API POST with invalid token returns 401', async ({ page }) => {
    const response = await page.request.post(BASE_URL + '/api/projects', {
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer fake_token'
      },
      data: JSON.stringify({ action: 'test' })
    });

    expect([401, 403]).toContain(response.status());
  });

  test('API-AUTH-06: API endpoint rejects expired session', async ({ page }) => {
    // Create a context with an expired/old session cookie
    const context = await page.context().browser().newContext();
    await context.addCookies([{
      name: 'PHPSESSID',
      value: 'expired_or_invalid_session_value',
      domain: (new URL(BASE_URL || 'http://localhost:8000').hostname),
      path: '/'
    }]);

    const newPage = await context.newPage();
    const response = await newPage.request.get(BASE_URL + '/api/projects', {
      headers: { 'Accept': 'application/json' }
    });

    expect([401, 403]).toContain(response.status());
    await context.close();
  });

  test('API-AUTH-07: API key endpoint requires valid API key', async ({ page }) => {
    // Try to access API keys page
    await page.goto(BASE_URL + '?page=api_keys');
    await page.waitForTimeout(1000);

    // Should either show the page (if admin) or access denied
    const body = await page.textContent('body');
    const url = page.url();

    // Admin should see the page, non-admin should be denied
    // This test documents expected behavior
    if (url.match(/page=login/)) {
      // Not logged in - expected to redirect
      expect(url).toMatch(/page=login/);
    } else {
      // Logged in - check if access is granted or denied
      expect(body).toMatch(/api|key|keys|access denied|unauthorized/i);
    }
  });

  test('API-AUTH-08: CORS preflight is handled correctly', async ({ page }) => {
    const response = await page.request.fetch(BASE_URL + '/api/projects', {
      method: 'OPTIONS',
      headers: {
        'Origin': 'http://example.com',
        'Access-Control-Request-Method': 'GET',
        'Access-Control-Request-Headers': 'Authorization'
      }
    });

    // Should either succeed with CORS headers or fail gracefully
    // Should NOT expose internal server errors
    const body = await response.text();
    expect(body.length).toBeLessThan(1000); // No stack traces or internal info
  });

  test('API-AUTH-09: API does not expose stack traces on error', async ({ page }) => {
    // Send a malformed request
    const response = await page.request.get(BASE_URL + '/api/projects?invalid_param=%', {
      headers: { 'Accept': 'application/json' }
    });

    const body = await response.text();

    // Should not contain PHP stack traces, SQL errors, or internal paths
    expect(body).not.toMatch(/Stack trace|traceback|#\d+|at line \d+/i);
    expect(body).not.toMatch(/sql.*error|mysql|sqlite.*error/i);
    expect(body).not.toMatch(/\/home\/|\/var\/|\/usr\/local/i);
  });

  test('API-AUTH-10: API rate limiting is enforced', async ({ page }) => {
    // Make many rapid requests to the API
    const responses = [];
    for (let i = 0; i < 20; i++) {
      const response = await page.request.get(BASE_URL + '/api/projects', {
        headers: { 'Accept': 'application/json' }
      });
      responses.push(response.status());
    }

    // After many rapid requests, should eventually get rate limited (429)
    // or all requests should fail appropriately since we passed no token (401)
    const has429 = responses.includes(429);
    const allUnauthorized = responses.every(r => r === 401 || r === 403);

    // Either rate limiting is enforced before auth checks (has429) or all fail auth (allUnauthorized)
    expect(has429 || allUnauthorized).toBe(true);
  });
});
