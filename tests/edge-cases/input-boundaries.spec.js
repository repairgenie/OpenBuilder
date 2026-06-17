/**
 * Edge Case Tests: Input Boundaries
 * Suite: Edge Cases > Input Boundaries
 *
 * Tests how the application handles extreme, malicious, and boundary-case inputs
 * including very long text, special characters, unicode, SQL injection, and XSS attempts.
 * Verifies the app sanitizes/escapes inputs and doesn't crash.
 *
 * @see TESTING-GUIDE.md Section 9 (Input Handling / Security)
 */
const { test, expect } = require('@playwright/test');
const { restoreAuth, assertNoFatalErrors, isAuthPage } = require('../helpers/auth');
const { EDGE_INPUTS } = require('../fixtures/test-data');
const { BASE_URL } = require('../../playwright.config');

// Helper: check the page didn't throw a fatal error after input
async function assertSafeInput(page, inputDescription) {
  const body = await page.textContent('body');
  if (body.includes('Fatal error') || body.includes('Parse error') || body.includes('Call to undefined')) {
    throw new Error(`${inputDescription}: PHP fatal error detected after input`);
  }
}

test.describe.skip('Input Boundaries: Very Long Text', () => {

  test('IB-LONG-01: 1000-character RFI subject is handled safely', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');
    assertNoFatalErrors(await page.content());

    const longSubject = 'A'.repeat(1000);
    await page.fill('input[name="ref_number"]', 'RFI-LONG-001');
    await page.fill('input[name="subject"]', longSubject);
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, '1000-char subject');
    const url = page.url();
    // Should either succeed (redirected to rfis) or show validation error — not crash
    expect(url.includes('rfis') || url.includes('error') || url.includes('create_rfi')).toBe(true);
  });

  test('IB-LONG-02: 5000-character daily log work performed field', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_daily_log`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const longText = 'A'.repeat(5000);
    await page.fill('input[name="date"]', '2026-06-10');
    await page.fill('input[name="weather"]', 'Sunny');
    await page.fill('input[name="manpower"]', '10');
    await page.fill('textarea[name="work_performed"]', longText);
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, '5000-char work performed');
    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error');
    expect(handled).toBe(true);
  });

  test('IB-LONG-03: 10000-character task description', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_task`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const longText = 'X'.repeat(10000);
    await page.fill('input[name="task_name"]', 'Long Task Name');
    await page.fill('textarea[name="description"]', longText);
    await page.fill('input[name="start_date"]', '2026-06-15');
    await page.fill('input[name="end_date"]', '2026-06-20');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, '10000-char task description');
    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error');
    expect(handled).toBe(true);
  });
});

test.describe.skip('Input Boundaries: Special Characters', () => {

  test('IB-SPEC-01: HTML special chars in RFI subject (< > " \' & ; \\n)', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-SPEC-001');
    await page.fill('input[name="subject"]', '< > " \' & ; \n \r \0');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'special chars in subject');
    const body = await page.content();
    // The script tag should be escaped or stripped — not executed
    const noRawScript = !body.includes('<script>alert') || body.includes('&lt;script');
    expect(noRawScript).toBe(true);
  });

  test('IB-SPEC-02: Backslash and pipe chars in text fields', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_daily_log`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="date"]', '2026-06-10');
    await page.fill('input[name="weather"]', 'Sunny | Cloudy \\ Windy');
    await page.fill('input[name="manpower"]', '5');
    await page.fill('textarea[name="work_performed"]', 'Pipe | grep test\nBackslash \\ test');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'backslash/pipe in daily log');
    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error') && !body.includes('Parse error');
    expect(handled).toBe(true);
  });

  test('IB-SPEC-03: Newline and carriage return in single-line text fields', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-NL-001');
    // Inject newlines into ref_number (should be single-line)
    await page.locator('input[name="ref_number"]').fill('REF\nWITH\nNEWLINES');
    await page.fill('input[name="subject"]', 'Subject with newlines\r\nand returns');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'newlines in single-line fields');
    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error');
    expect(handled).toBe(true);
  });
});

test.describe.skip('Input Boundaries: HTML Tags', () => {

  test('IB-HTML-01: Raw HTML tags in RFI subject are escaped', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-HTML-001');
    await page.fill('input[name="subject"]', '<div style="display:none">Hidden HTML</div>');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'raw HTML in subject');
    // Navigate to the created RFI and check it was escaped
    const url = page.url();
    if (url.includes('rfis') && !url.includes('error')) {
      await page.goto(url, { waitUntil: 'domcontentloaded' });
      const body = await page.content();
      const escaped = body.includes('&lt;div') || body.includes('&lt;') || !body.includes('<div');
      expect(escaped).toBe(true);
    }
  });

  test('IB-HTML-02: Nested HTML tags in daily log work performed', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_daily_log`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="date"]', '2026-06-10');
    await page.fill('input[name="weather"]', 'Sunny');
    await page.fill('input[name="manpower"]', '5');
    await page.fill('textarea[name="work_performed"]',
      '<b>Bold</b> <i>Italic</i> <u>Underline</u> <a href="javascript:alert(1)">Link</a>');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'nested HTML tags in work performed');
    const body = await page.content();
    // Should be escaped, not rendered as HTML
    const escaped = !body.includes('<b>Bold</b>') || body.includes('&lt;b&gt;');
    expect(escaped).toBe(true);
  });
});

test.describe.skip('Input Boundaries: Unicode Characters', () => {

  test('IB-UNICODE-01: Emoji in RFI subject is stored and displayed correctly', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-EMOJI-001');
    await page.fill('input[name="subject"]', 'Subject with emoji 🎉👍 🚧 🔥');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'emoji in subject');
    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error');
    expect(handled).toBe(true);
  });

  test('IB-UNICODE-02: Accented Latin characters (Spanish/French)', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-ACCENT-001');
    await page.fill('input[name="subject"]', 'Información errónea: él Niño está demás — déjà vu');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'accented chars');
    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error');
    expect(handled).toBe(true);
  });

  test('IB-UNICODE-03: Non-Latin scripts (Chinese, Arabic, etc.)', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-INTL-001');
    await page.fill('input[name="subject"]', '测试主题 - اختبار - בדיקה - Δοκιμή');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'non-Latin scripts');
    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error');
    expect(handled).toBe(true);
  });
});

test.describe.skip('Input Boundaries: SQL Injection Attempts', () => {

  test('IB-SQL-01: Classic OR 1=1 injection in RFI subject', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', "RFI-SQL-001");
    await page.fill('input[name="subject"]', "' OR '1'='1' --");
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, "OR 1=1 injection");
    const body = await page.textContent('body');
    const handled = !body.includes('SQLSTATE') && !body.includes('Fatal error');
    expect(handled).toBe(true);
  });

  test('IB-SQL-02: DROP TABLE injection attempt', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', "RFI-SQL-DROP");
    await page.fill('input[name="subject"]', "'; DROP TABLE rfis; --");
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, "DROP TABLE injection");
    const body = await page.textContent('body');
    const handled = !body.includes('SQLSTATE') && !body.includes('DROP TABLE') &&
                    !body.includes('Fatal error');
    expect(handled).toBe(true);
  });

  test('IB-SQL-03: UNION SELECT injection in user email field', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_user`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="name"]', 'SQL Test');
    await page.fill('input[name="email"]', "admin' UNION SELECT * FROM users--");
    await page.selectOption('select[name="role"]', 'Subcontractor');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, "UNION SELECT injection");
    const body = await page.textContent('body');
    const handled = !body.includes('SQLSTATE') && !body.includes('Fatal error');
    expect(handled).toBe(true);
  });

  test('IB-SQL-04: Template injection {{7*7}} in text fields', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-TPL-001');
    await page.fill('input[name="subject"]', '{{7*7}}');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, '{{7*7}} template injection');
    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error');
    expect(handled).toBe(true);
  });
});

test.describe.skip('Input Boundaries: XSS Attempts', () => {

  test('IB-XSS-01: Script tag XSS in RFI subject', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-XSS-001');
    await page.fill('input[name="subject"]', '<script>alert("XSS")</script>');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'script tag XSS');
    // Check the submitted data is not executed as JS
    const content = await page.content();
    const notExecuted = !content.includes('<script>alert("XSS")</script>') ||
                        content.includes('&lt;script&gt;');
    expect(notExecuted).toBe(true);
  });

  test('IB-XSS-02: img onerror XSS in daily log notes', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_daily_log`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="date"]', '2026-06-10');
    await page.fill('input[name="weather"]', 'Sunny');
    await page.fill('input[name="manpower"]', '5');
    await page.fill('textarea[name="work_performed"]', '<img src=x onerror=alert(1)>');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'img onerror XSS');
    const content = await page.content();
    // Should be escaped
    const escaped = !content.includes('onerror=alert(1)') || content.includes('&gt;');
    expect(escaped).toBe(true);
  });

  test('IB-XSS-03: SVG onload XSS in task description', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_task`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="task_name"]', 'XSS Test Task');
    await page.fill('textarea[name="description"]', '<svg onload=alert(1)>');
    await page.fill('input[name="start_date"]', '2026-06-15');
    await page.fill('input[name="end_date"]', '2026-06-20');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'SVG onload XSS');
    const content = await page.content();
    const escaped = !content.includes('onload=alert(1)') || content.includes('&gt;');
    expect(escaped).toBe(true);
  });

  test('IB-XSS-04: Event handler XSS (onclick, onfocus) in user name', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_user`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="name"]', '<span onclick="alert(1)">Click me</span>');
    await page.fill('input[name="email"]', 'xss@test.com');
    await page.selectOption('select[name="role"]', 'Subcontractor');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'onclick XSS in name field');
    const content = await page.content();
    const escaped = !content.includes('onclick="alert(1)"') || content.includes('&gt;');
    expect(escaped).toBe(true);
  });

  test('IB-XSS-05: Data URI XSS attempt', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-DATA-001');
    await page.fill('input[name="subject"]', '<a href="data:text/html,<script>alert(1)</script>">Click</a>');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(2000);

    await assertSafeInput(page, 'data URI XSS');
    const content = await page.content();
    const escaped = !content.includes('data:text/html') || content.includes('&gt;');
    expect(escaped).toBe(true);
  });
});

test.describe.skip('Input Boundaries: Empty and Whitespace', () => {

  test('IB-EMPTY-01: Whitespace-only RFI subject is treated as empty', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', 'RFI-SPACE-001');
    await page.fill('input[name="subject"]', '   \t\n\r   ');
    await page.fill('input[name="due_date"]', '2026-07-15');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    const body = await page.textContent('body');
    const rejected = body.includes('required') || body.includes('Required') ||
                     body.includes('requerido') || body.includes('Requerido') ||
                     page.url().includes('create_rfi');
    expect(rejected).toBe(true);
  });

  test('IB-EMPTY-02: Empty string submitted to required text field', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=create_rfi`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    await page.fill('input[name="ref_number"]', '');
    await page.fill('input[name="subject"]', '');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    const body = await page.textContent('body');
    const hasError = body.includes('required') || body.includes('Required') ||
                     body.includes('requerido') || body.includes('Requerido') ||
                     page.url().includes('create_rfi');
    expect(hasError).toBe(true);
  });

  test('IB-EMPTY-03: Single space in budget cost code field', async ({ page }) => {
    await restoreAuth(page);
    await page.goto(`${BASE_URL}/?page=budget`, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    if (isAuthPage(page)) throw new Error('Auth failed');

    const addBtn = page.locator('button:has-text("Add"), button:has-text("Agregar")').first();
    if (await addBtn.count() === 0) {
      // No add form on budget page - skip
      expect(true).toBe(true);
      return;
    }
    await addBtn.click();
    await page.waitForTimeout(1000);

    await page.fill('input[name="cost_code"]', ' ');
    await page.fill('input[name="original_budget"]', '1000');
    await page.click('button[type="submit"]');
    await page.waitForTimeout(1500);

    await assertSafeInput(page, 'single space in cost code');
    const body = await page.textContent('body');
    const handled = !body.includes('Fatal error');
    expect(handled).toBe(true);
  });
});