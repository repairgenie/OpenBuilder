# Manual Auth-Gated Test Checklist

> These pages require an active login session. Run after completing the full login flow.

---

## 1. Budget Scenario Simulator
**URL:** `/index.php?page=budget`

**Prerequisites:** Logged in as any user

**Steps:**
1. Navigate to `index.php?page=budget`
2. Verify page loads without redirect to login
3. Locate `#variance-slider` — drag to change value
4. Verify `#projected-impact` label updates in real-time
5. Verify AI risk commentary renders below slider
6. Toggle language switcher to Spanish — labels update

**Expected:** Slider functional, impact label updates, bilingual labels correct

---

## 2. RFI Spatial Mapping
**URL:** `/index.php?page=rfi_map`

**Prerequisites:** Logged in as any user

**Steps:**
1. Navigate to `index.php?page=rfi_map`
2. Verify map renders with RFI pins visible
3. Hover/click pin `[data-tooltip*="RFI #001"]`
4. Click through to detail view — URL should be `index.php?page=view_rfi&id=1`
5. Verify detail page shows RFI data (title, status, assigned_to)

**Expected:** Map with pins, tooltip on hover, detail page accessible

---

## 3. Project Settings Persistence
**URL:** `/index.php?page=project_settings`

**Prerequisites:** Logged in as project admin or manager

**Steps:**
1. Navigate to `index.php?page=project_settings`
2. Locate input pre-filled with `OpenBuilder HQ`
3. Change project name to `Updated Project Name`
4. Click "Save Changes"
5. Verify toast notification appears
6. Refresh page — verify new name persists
7. Restore original name and save

**Expected:** Settings saved, toast shown, data persists after refresh

---

## 4. User Management & Roles
**URL:** `/index.php?page=users`

**Prerequisites:** Logged in as admin

**Steps:**
1. Navigate to `index.php?page=users`
2. Verify user table renders with columns (Name, Email, Role, Status)
3. Click "Add User" / edit icon on a row
4. Verify modal or form loads with correct fields
5. Change a user's role (e.g., Manager → Viewer)
6. Save and verify change reflected in table
7. Restore original role

**Expected:** Table renders, role changes save correctly

---

## 5. MFA Verification Flow
**URL:** `/index.php?page=mfa`

**Prerequisites:** Logged in — triggered automatically by `login_handler.php` after email/password verification. MFA code is printed to server `error_log` in dev mode.

**Steps:**
1. Complete login with valid credentials
2. On MFA challenge, note the 6-digit code from `error_log` (or PHP console)
3. Enter the 6 digits into the 6 individual inputs (auto-advances between fields)
4. Click "Verify"
5. Verify redirect to `index.php?page=dashboard`
6. Attempt with wrong code — verify `?invalid=1` param and error message shown

**Expected:** Valid code → dashboard. Invalid code → error message, stays on MFA.

---

## 6. Full Login + Session E2E (Optional)
**URLs:** Login flow → Dashboard

**Steps (complete flow):**
1. POST to `index.php?page=login_handler` with valid email + password
2. Verify redirects to `index.php?page=mfa&sent=1`
3. Retrieve MFA code from `error_log`
4. POST MFA code to `index.php?page=mfa_handler`
5. Verify redirect to `index.php?page=dashboard`
6. Navigate to each auth-gated page above in the same session
7. Log out — verify auth-gated pages redirect to login

**Expected:** Full session works end-to-end, logout invalidates session

---

## Running the Automated (Unauthenticated) Suite

```bash
cd ~/Documents/GitHub/OpenBuilder
php -S localhost:9000 > /dev/null 2>&1 &
sleep 2
npx playwright test --reporter=line
```

Report generated at: `playwright-report/index.html`
