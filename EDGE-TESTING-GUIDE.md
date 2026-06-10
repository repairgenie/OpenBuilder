# OpenBuilder Edge Testing Guide / Guía de Pruebas de Casos Límite

## Purpose / Propósito

This guide covers edge cases, boundary testing, security testing, and performance considerations for the OpenBuilder application.

---

## 1. Input Validation & Boundary Testing / Validación de Entradas y Pruebas de Límites

### 1.1 Text Input Boundaries

| Field | Min Length | Max Length | Edge Test |
|-------|------------|-------------|-----------|
| RFI Reference Number | 1 | 50 | Test "", 1 char, 50 chars, 51 chars |
| RFI Subject | 1 | 200 | Test "", 1 char, 200 chars, 500 chars |
| User Name | 1 | 100 | Test "", 1 char, 100 chars, 200 chars |
| User Email | 5 | 100 | Test "", invalid email, valid, 100+ chars |
| Password | 6 | 128 | Test "12345", "123456", 128 chars, 200 chars |

**Edge Test Cases:**
| ID | Test | Expected |
|----|------|----------|
| E-01 | RFI subject with 500 chars | Should truncate or reject |
| E-02 | Email without @ symbol | HTML5 validation error |
| E-03 | Email with multiple @ symbols | Validation error |
| E-04 | Password "12345" (5 chars) | Validation error "minimum 6" |
| E-05 | Very long input (>1000 chars) | Should truncate at display |

### 1.2 Number Input Boundaries

| Field | Min | Max | Edge Test |
|-------|-----|-----|-----------|
| Manpower | 0 | 9999 | Test -1, 0, 1, 9999, 10000 |
| Budget amounts | 0 | 999999999 | Test negative, zero, max, overflow |
| Task dates | Valid date | Valid date | Test invalid dates |

**Edge Test Cases:**
| ID | Test | Expected |
|----|------|----------|
| E-10 | Manpower = -1 | Validation error |
| E-11 | Manpower = 0 | Allowed (valid) |
| E-12 | Manpower = 10000 | Should reject or cap |
| E-13 | Budget = -100 | Validation error |
| E-14 | Budget = 0 | Allowed |
| E-15 | Budget = 999999999 | Allowed |
| E-16 | Budget = 1000000000 | May overflow, test carefully |

### 1.3 Date/Time Boundaries

| ID | Test | Expected |
|----|------|----------|
| E-20 | Due date in past | Should warn but allow |
| E-21 | Due date = today | Allowed |
| E-22 | Task start > end date | Validation error |
| E-23 | Task duration = 0 days | Validation error |
| E-24 | Task duration = 365 days | Allowed |
| E-25 | Log date = future | May be allowed or warned |

### 1.4 Special Characters in Text Fields

| ID | Test Input | Expected |
|----|------------|----------|
| E-30 | `<script>alert(1)</script>` | Should be escaped, no XSS |
| E-31 | `" OR 1=1 --` | Should be escaped, no SQL injection |
| E-32 | `'; DROP TABLE users; --` | Should be escaped |
| E-33 | `null` | Should be treated as text |
| E-34 | `\n\t\r` | Should be preserved or stripped |
| E-35 | `中文测试` | Should be preserved (UTF-8) |
| E-36 | `emoji 🎉👍` | Should be preserved |
| E-37 | Very long URL | Should not crash |

---

## 2. Authentication & Authorization Testing / Pruebas de Autenticación y Autorización

### 2.1 Login Edge Cases

| ID | Test | Expected |
|----|------|----------|
| A-01 | Empty email field | HTML5 validation |
| A-02 | Empty password field | HTML5 validation |
| A-03 | Invalid email format | HTML5 validation |
| A-04 | Correct email, wrong password | "Invalid credentials" |
| A-05 | Non-existent email | "Invalid credentials" |
| A-06 | SQL injection in email field | "Invalid credentials" |
| A-07 | Very long email (>200 chars) | Should reject |
| A-08 | Multiple rapid login attempts | Rate limiting if implemented |

### 2.2 MFA Edge Cases

| ID | Test | Expected |
|----|------|----------|
| M-01 | Empty MFA code | Validation error |
| M-02 | 5 digits (too short) | Validation error |
| M-03 | 7 digits (too long) | Validation error |
| M-04 | Letters in code | Should reject non-numeric |
| M-05 | Expired session | Redirect to login |

### 2.3 Session Management

| ID | Test | Expected |
|----|------|----------|
| S-01 | Session timeout | Redirect to login |
| S-02 | CSRF token missing | 403 Forbidden |
| S-03 | CSRF token invalid | 403 Forbidden |
| S-04 | Cookie theft simulation | Should fail (if protected) |
| S-05 | Parallel sessions | Should allow or warn |

### 2.4 Authorization Edge Cases

| ID | Test | Expected |
|----|------|----------|
| AUTH-01 | Subcontractor accessing Admin page | 403 Forbidden |
| AUTH-02 | Manager accessing User delete | 403 Forbidden |
| AUTH-03 | Direct URL manipulation | Proper redirect |
| AUTH-04 | Role changed during session | New permissions apply |

---

## 3. Security Testing / Pruebas de Seguridad

### 3.1 XSS (Cross-Site Scripting)

| ID | Payload | Target | Expected |
|----|---------|--------|----------|
| X-01 | `<script>alert('XSS')</script>` | RFI Subject | Escaped output |
| X-02 | `<img src=x onerror=alert(1)>` | Daily Log notes | Escaped output |
| X-03 | `javascript:alert(1)` | Any link href | Safe rendering |
| X-04 | `<svg onload=alert(1)>` | User name | Escaped output |
| X-05 | `{{7*7}}` | Any text field | Literal output, no evaluation |

### 3.2 SQL Injection

| ID | Payload | Target | Expected |
|----|---------|--------|----------|
| SQL-01 | `' OR '1'='1` | RFI search | Safe handling |
| SQL-02 | `'; DROP TABLE rfis; --` | RFI form | Error or safe |
| SQL-03 | `UNION SELECT * FROM users` | Search field | Error or safe |
| SQL-04 | `1; DELETE FROM daily_logs` | Any form | Error or safe |

### 3.3 CSRF (Cross-Site Request Forgery)

| ID | Test | Expected |
|----|------|----------|
| CS-01 | Form without CSRF token | 403 error |
| CS-02 | Form with invalid token | 403 error |
| CS-03 | Form with expired token | "Session expired" error |
| CS-04 | Cross-origin form submission | Should reject |

### 3.4 Path Traversal / File Access

| ID | Test | Expected |
|----|------|----------|
| F-01 | `?page=../../../etc/passwd` | URL parameter | 404 or safe |
| F-02 | `?page=../../database.sqlite` | URL parameter | 404 or safe |
| F-03 | File upload with `../` filename | Upload | Validation error |

### 3.5 Input Sanitization

| ID | Test | Expected |
|----|------|----------|
| SAN-01 | HTML tags in RFI subject | Stripped or escaped |
| SAN-02 | SQL keywords in search | Safe handling |
| SAN-03 | Null bytes in input | Stripped |
| SAN-04 | Unicode homoglyphs | Safe handling |

---

## 4. Performance Testing / Pruebas de Rendimiento

### 4.1 Page Load Performance

| ID | Test | Expected |
|----|------|----------|
| P-01 | Dashboard load time | < 500ms |
| P-02 | RFI list with 100 items | < 1s |
| P-03 | RFI list with 1000 items | < 3s with pagination |
| P-04 | Budget with 50 cost codes | < 1s |
| P-05 | Task Gantt with 100 tasks | < 2s |

### 4.2 Database Performance

| ID | Test | Expected |
|----|------|----------|
| DB-01 | Full text search on large table | < 1s |
| DB-02 | Pagination through large dataset | < 500ms per page |
| DB-03 | Concurrent writes | No data corruption |
| DB-04 | Large INSERT transaction | Completes or times out |

### 4.3 Memory & Resource Limits

| ID | Test | Expected |
|----|------|----------|
| R-01 | Upload 10MB file | Success or clear error |
| R-02 | Upload 100MB file | Clear size limit error |
| R-03 | Rapid page navigation | No memory leak |
| R-04 | Leave page open for 1 hour | No degradation |

### 4.4 Network Conditions

| ID | Test | Expected |
|----|------|----------|
| N-01 | Slow connection (3G) | Graceful degradation |
| N-02 | Connection lost mid-submit | Error handling, no data loss |
| N-03 | Timeout after 30s | Clear timeout message |
| N-04 | Reconnection after disconnect | Resume normally |

---

## 5. Race Condition Testing / Pruebas de Condiciones de Carrera

### 5.1 Concurrent Operations

| ID | Test | Expected |
|----|------|----------|
| RC-01 | Two users edit same RFI simultaneously | Last write wins or conflict error |
| RC-02 | Multiple users create RFI at same time | All succeed with unique IDs |
| RC-03 | Bulk delete while individual delete | One succeeds, proper handling |
| RC-04 | Update budget while creating cost code | Proper transaction handling |

### 5.2 State Consistency

| ID | Test | Expected |
|----|------|----------|
| SC-01 | Close RFI while editing | Conflict warning |
| SC-02 | Delete user assigned to task | Warning or prevent |
| SC-03 | Change role permissions during use | Session-level consistency |

---

## 6. Error Handling / Manejo de Errores

### 6.1 Database Errors

| ID | Test | Expected |
|----|------|----------|
| ERR-01 | Database file deleted | Clear error message, graceful fail |
| ERR-02 | Database locked | Retry or clear message |
| ERR-03 | Query syntax error | 500 page or handled error |
| ERR-04 | Connection lost mid-query | Reconnect or clear error |

### 6.2 Form Validation Errors

| ID | Test | Expected |
|----|------|----------|
| ERR-10 | Submit form with missing required field | Inline error message |
| ERR-11 | Submit form with invalid format | Specific format error |
| ERR-12 | Submit form with SQL injection attempt | Error, no injection |
| ERR-13 | Double submit (rapid click) | Only one record created |

### 6.3 Page Errors

| ID | Test | Expected |
|----|------|----------|
| ERR-20 | Access non-existent page | 404 page displayed |
| ERR-21 | Access page with invalid ID | "Not found" message |
| ERR-22 | Missing template file | 404 or error page |
| ERR-23 | PHP fatal error | Error page, not blank |

---

## 7. Cross-Browser Compatibility / Compatibilidad Cross-Browser

### 7.1 CSS/Layout Testing

| ID | Browser | Test | Expected |
|----|---------|------|----------|
| CB-01 | Chrome | All pages render | No visual breaks |
| CB-02 | Firefox | All pages render | No visual breaks |
| CB-03 | Safari | All pages render | No visual breaks |
| CB-04 | Edge | All pages render | No visual breaks |
| CB-05 | Mobile Safari | Bottom nav visible | Touch targets work |
| CB-06 | Chrome Mobile | Responsive layout | Stacked elements |

### 7.2 JavaScript Compatibility

| ID | Test | Expected |
|----|------|----------|
| JS-01 | Alpine.js directives | Work in all browsers |
| JS-02 | Chart.js rendering | Charts display correctly |
| JS-03 | Form validation | Consistent behavior |
| JS-04 | Modal open/close | Works in all browsers |

---

## 8. Localization & Internationalization / Localización e Internacionalización

### 8.1 Language Switching

| ID | Test | Expected |
|----|------|----------|
| L10N-01 | Switch from EN to ES | All labels change |
| L10N-02 | Switch from ES to EN | All labels change |
| L10N-03 | Invalid lang parameter | Falls back to EN |
| L10N-04 | Special chars in EN/ES | Properly displayed |

### 8.2 Date/Time Formats

| ID | Test | Expected |
|----|------|----------|
| DT-01 | Date display in EN | MM/DD/YYYY |
| DT-02 | Date display in ES | DD/MM/YYYY or similar |
| DT-03 | Time display (12h vs 24h) | Follows locale |
| DT-04 | Date picker selection | Works correctly |

### 8.3 Currency

| ID | Test | Expected |
|----|------|----------|
| C-01 | USD selected | $ symbol, 2 decimals |
| C-02 | EUR selected | € symbol, 2 decimals |
| C-03 | Large numbers | Proper thousand separators |

---

## 9. Accessibility Testing / Pruebas de Accesibilidad

| ID | Test | Expected |
|----|------|----------|
| A11Y-01 | Keyboard navigation | All interactive elements reachable |
| A11Y-02 | Tab order | Logical sequence |
| A11Y-03 | Focus indicators | Visible focus states |
| A11Y-04 | Form labels | Properly associated |
| A11Y-05 | Color contrast | WCAG AA compliant |
| A11Y-06 | Screen reader | Labels announced correctly |

---

## 10. Data Integrity / Integridad de Datos

### 10.1 Required Fields

| ID | Entity | Test | Expected |
|----|--------|------|----------|
| DI-01 | RFI | Create without ref_number | Validation error |
| DI-02 | RFI | Create without subject | Validation error |
| DI-03 | User | Create without email | Validation error |
| DI-04 | Task | Create without start_date | Validation error |
| DI-05 | Daily Log | Create without log_date | Validation error |

### 10.2 Foreign Key Integrity

| ID | Test | Expected |
|----|------|----------|
| FK-01 | Delete crew with tasks | Warning or prevent |
| FK-02 | Delete cost code used in tasks | Warning or prevent |
| FK-03 | Delete user who created logs | Logs retain created_by |

### 10.3 Data Consistency

| ID | Test | Expected |
|----|------|----------|
| DC-01 | Budget totals recalculated | Correct sums |
| DC-02 | RFI status transitions | Valid states only |
| DC-03 | Task status transitions | Valid states only |

---

## 11. Edge Case Scenarios / Escenarios de Casos Límite

### 11.1 Empty States

| ID | Page | Test | Expected |
|----|------|------|----------|
| ES-01 | RFIs | No RFIs exist | "No RFIs found" message |
| ES-02 | Daily Logs | No logs exist | "No daily logs found" |
| ES-03 | Tasks | No tasks exist | "No tasks recorded" |
| ES-04 | Users | No users exist | "No users found" |
| ES-05 | Budget | No cost codes | Empty table, no crash |

### 11.2 Maximum Data States

| ID | Entity | Count | Test |
|----|--------|-------|------|
| MAX-01 | RFIs | 1000+ | Pagination works, no timeout |
| MAX-02 | Daily Logs | 1000+ | Grid displays correctly |
| MAX-03 | Tasks | 500+ | Gantt renders, scrollable |
| MAX-04 | Users | 100+ | Table scrollable, no lag |
| MAX-05 | Document uploads | 50+ | List scrollable |

### 11.3 Unusual Input Combinations

| ID | Test | Expected |
|----|------|----------|
| UC-01 | All fields at max length simultaneously | Handles gracefully |
| UC-02 | Rapid multi-page navigation | No memory issues |
| UC-03 | Leave form open for 30 min, then submit | Session valid or expired gracefully |
| UC-04 | Browser back button after submit | No duplicate submission |
| UC-05 | Refresh page during form submission | Handles properly |

---

## 12. Security Headers & HTTPS / Encabezados de Seguridad

| ID | Test | Expected |
|----|------|----------|
| SEC-01 | X-Frame-Options header | Set to prevent clickjacking |
| SEC-02 | X-Content-Type-Options | Set to prevent MIME sniffing |
| SEC-03 | CSP headers | Proper Content-Security-Policy |
| SEC-04 | HTTPS redirect | Redirects HTTP to HTTPS |
| SEC-05 | Secure cookie flags | HttpOnly, Secure, SameSite |

---

## 13. Test Execution Matrix / Matriz de Ejecución de Pruebas

| Category | Priority | Time Required |
|----------|----------|---------------|
| Input Validation | High | 1-2 hours |
| Authentication | High | 1-2 hours |
| XSS/SQL Injection | High | 2-3 hours |
| CSRF | High | 1 hour |
| Performance | Medium | 2-3 hours |
| Race Conditions | Medium | 1-2 hours |
| Error Handling | Medium | 1-2 hours |
| Cross-Browser | Medium | 2-4 hours |
| Accessibility | Low | 1-2 hours |
| Localization | Low | 1 hour |

**Total estimated time:** 12-20 hours for comprehensive edge testing

---

## 14. Quick Smoke Test / Prueba Rápida de Funcionalidad

Run these first for a quick sanity check:

1. **Login:** admin@openbuilder.com / admin123
2. **Create RFI:** Fill all required fields → Submit → Verify in list
3. **Create Daily Log:** Fill fields → Submit → Verify AI report generated
4. **Create Task:** Fill all fields → Save → Verify in Gantt
5. **User Management:** Add user → Edit → Delete
6. **Budget:** View → Change variance slider → Verify impact updates
7. **Language Toggle:** EN → ES → Verify all labels change

If smoke test passes, proceed to detailed edge testing.