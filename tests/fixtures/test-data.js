/**
 * Test data fixtures — reusable test data for all test suites.
 */

/** Demo user credentials */
const USERS = {
  admin: { email: 'admin@openbuilder.com', password: 'admin123', role: 'Admin' },
  manager: { email: 'manager@openbuilder.com', password: 'manager123', role: 'Manager' },
  sub: { email: 'sub@openbuilder.com', password: 'sub123', role: 'Subcontractor' },
};

/** RFI test data */
const RFI_DATA = {
  valid: {
    ref_number: 'RFI-' + Date.now(),
    subject: 'Test RFI Subject',
    due_date: '2026-07-15',
    priority: 'High',
  },
  minimal: {
    ref_number: 'RFI-MIN-001',
    subject: 'Minimal RFI',
    due_date: '2026-06-30',
    priority: 'Low',
  },
  edge: {
    // Max length subject (200 chars)
    ref_number: 'RFI-EDGE-MAX',
    subject: 'A'.repeat(200),
    due_date: '2026-12-31',
    priority: 'Medium',
  },
  xssPayload: {
    ref_number: 'RFI-XSS-001',
    subject: '<script>alert("XSS")</script>',
    due_date: '2026-07-01',
    priority: 'Low',
  },
  sqlInjection: {
    ref_number: "RFI-SQL-001'; DROP TABLE rfis; --",
    subject: 'SQL Injection Test',
    due_date: '2026-07-01',
    priority: 'Low',
  },
};

/** Daily Log test data */
const DAILY_LOG_DATA = {
  valid: {
    date: '2026-06-10',
    weather: 'Sunny',
    manpower: 15,
    work_performed: 'Completed foundation pour for Building A. Set rebar for columns.',
  },
  minimal: {
    date: '2026-06-09',
    weather: 'Cloudy',
    manpower: 5,
    work_performed: 'Site cleanup and material staging.',
  },
  boundary: {
    date: '2026-06-10',
    weather: 'Rainy',
    manpower: 9999, // Max boundary
    work_performed: 'A'.repeat(5000), // Long text
  },
};

/** Task test data */
const TASK_DATA = {
  valid: {
    task_name: 'Foundation Pour',
    start_date: '2026-06-15',
    end_date: '2026-06-20',
    status: 'In Progress',
    critical: true,
  },
  minimal: {
    task_name: 'Site Prep',
    start_date: '2026-06-10',
    end_date: '2026-06-12',
    status: 'Not Started',
    critical: false,
  },
  invalid: {
    // Start date after end date
    task_name: 'Invalid Task',
    start_date: '2026-06-25',
    end_date: '2026-06-10',
    status: 'Not Started',
    critical: false,
  },
};

/** User management test data */
const USER_DATA = {
  valid: {
    name: 'Test User ' + Date.now(),
    email: 'testuser' + Date.now() + '@example.com',
    role: 'Subcontractor',
    status: 'Active',
  },
  invalidEmail: {
    name: 'Bad Email User',
    email: 'notanemail',
    role: 'Subcontractor',
    status: 'Active',
  },
  duplicateEmail: {
    name: 'Duplicate Test',
    email: 'admin@openbuilder.com', // Already exists
    role: 'Manager',
    status: 'Active',
  },
};

/** Budget test data */
const BUDGET_DATA = {
  valid: {
    cost_code: '01 - General Conditions',
    original_budget: 50000,
    change_orders: 5000,
    committed: 45000,
  },
 variance: {
    cost_code: '02 - Concrete',
    original_budget: 100000,
    change_orders: 10000,
    committed: 95000,
  },
};

/** Edge case inputs */
const EDGE_INPUTS = {
  empty: '',
  spaces: '   ',
  singleChar: 'A',
  maxLength: 'A'.repeat(200),
  veryLong: 'A'.repeat(1000),
  specialChars: "!@#$%^&*()_+-=[]{}|;':\",./<>?`~",
  unicode: '中文测试 🎉👍 éèêëàâäùûüôöîïç',
  xssScript: '<script>alert("XSS")</script>',
  xssImg: '<img src=x onerror=alert(1)>',
  xssSvg: '<svg onload=alert(1)>',
  sqlInjection: "' OR '1'='1' --",
  sqlDrop: "'; DROP TABLE users; --",
  sqlUnion: "UNION SELECT * FROM users",
  templateInjection: '{{7*7}}',
  nullByte: '\x00null',
  newline: '\n\t\r',
  negative: -1,
  zero: 0,
  maxInt: 999999999,
  overflow: 1000000000,
  pastDate: '2020-01-01',
  futureDate: '2030-12-31',
  invalidDate: 'not-a-date',
};

module.exports = {
  USERS,
  RFI_DATA,
  DAILY_LOG_DATA,
  TASK_DATA,
  USER_DATA,
  BUDGET_DATA,
  EDGE_INPUTS,
};
