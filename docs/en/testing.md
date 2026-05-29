# OpenBuilder Testing Guide (English)

## Core Testing Principles
1. **Bilingual Integrity**: Every UI element must support English and Spanish. Use the `lang` parameter in URLs for validation.
2. **AI Reliability**: AI-generated content (Daily Reports, RFI Drafts) must be checked for technical relevance and formatting.
3. **Data Consistency**: Ensure budget metrics (Spent vs. Remaining) are mathematically consistent across Dashboard and Budget views.

## New Feature Modules (v2 — Phase 100+)

The following modules were added in the Phase 100 implementation wave. All must pass bilingual, CSRF, and SweetAlert2 validation.

---

### 1. Timesheets
**Template**: `templates/timesheets.php`  
**Handler**: `templates/timesheet_handler.php`

#### Manual Test Scenarios
- [ ] **Create timesheet**: Select worker name, trade (EN/ES), date, enter hours, pick cost code from searchable dropdown. Submit. Verify entry appears in the list with correct data.
- [ ] **GPS capture**: After submitting a timesheet, verify `latitude`/`longitude`/`gps_stamp` columns are populated in the database. GPS is auto-captured via browser Geolocation API — no manual entry required.
- [ ] **Foreman approval**: Submit a timesheet as Worker. Log in as Foreman. Verify the approval action is visible. Click Approve and confirm status changes to "Approved".
- [ ] **Edit timesheet**: Click Edit on an existing entry. Modify hours and cost code. Save. Verify changes reflected in list and database.
- [ ] **Delete timesheet**: Click Delete. Confirm via SweetAlert2 dialog. Verify entry is removed from list and DB.
- [ ] **Bilingual toggle**: Append `?lang=es` to the URL. Verify all labels (Worker Name, Trade, Hours, Date, Cost Code, Status) display in Spanish. Same for success/error flash messages.

#### Edge Cases
- [ ] **No GPS permission**: Deny geolocation in browser. Verify form still submits with `latitude=NULL`, `longitude=NULL` — no blocking error.
- [ ] **Zero hours**: Enter 0 hours. Verify form validation prevents submission or allows it with appropriate DB state.
- [ ] **Future date**: Select a date in the future. Verify it is accepted (timesheets can be pre-dated for scheduling).

---

### 2. Equipment Tracking
**Template**: `templates/equipment.php`  
**Handler**: `templates/equipment_handler.php`

#### Manual Test Scenarios
- [ ] **Add equipment**: Fill in name, category, make, model, serial number, status (Active/Retired), assigned crew. Submit. Verify card appears in the equipment grid.
- [ ] **Service log**: Click on an equipment card. Open the Service Log tab. Add a log entry: date, type (Routine/Repair/Maintenance), description, cost. Verify it appears in the log list.
- [ ] **Retire equipment**: Click Retire on an Active equipment card. Confirm via SweetAlert2. Verify status changes to Retired and card is visually distinguished (greyed or badge).
- [ ] **Reassign crew**: Edit equipment, change assigned crew. Save. Verify the new crew name appears on the card.
- [ ] **Bilingual toggle**: Verify equipment categories, status labels, and service log fields render in Spanish when `?lang=es`.

#### Edge Cases
- [ ] **Retire already-retired**: Attempt to retire equipment already in Retired status. Verify no duplicate update or error.
- [ ] **Service log on retired**: Add a service log entry to a retired piece of equipment. Verify it is allowed (equipment can be maintained after retirement).

---

### 3. Safety Hazard Log
**Template**: `templates/safety_hazards.php`  
**Handler**: `templates/safety_handler.php`

#### Manual Test Scenarios
- [ ] **Report hazard**: Click "Report Hazard". Fill description, location, severity (Low/Medium/High/Critical), report date, assigned crew. Attach a photo. Submit.
- [ ] **Photo upload**: Upload a photo with the hazard. Verify thumbnail appears on the hazard card. Verify photo is saved in `uploads/safety/`.
- [ ] **GPS capture**: Verify lat/lon are captured automatically via browser Geolocation API when reporting a hazard.
- [ ] **Edit hazard**: Click Edit on an existing hazard. Change severity and add a corrective action. Save. Verify card updates.
- [ ] **Close hazard**: Click Close on an Open hazard. Confirm via SweetAlert2. Verify status badge changes to "Closed".
- [ ] **Bilingual toggle**: Verify severity labels, status badges, and form fields render in Spanish (`?lang=es`).

#### Edge Cases
- [ ] **No GPS permission**: Deny geolocation. Verify form still submits — GPS fields default to NULL.
- [ ] **Large photo upload**: Upload a photo > 5MB. Verify handler rejects it with an error message (file type + size validation).
- [ ] **Close already-closed**: Attempt to close an already-Closed hazard. Verify no duplicate update.

---

### 4. Inspections
**Templates**: `templates/inspection_schedule.php`, `templates/inspection_execution.php`, `templates/inspection_templates.php`  
**Handler**: `templates/inspection_handler.php`

#### Manual Test Scenarios
- [ ] **Schedule inspection**: Create a new inspection — title, inspector name, scheduled date, location, project, template (if templates exist). Submit. Verify it appears in the schedule list with status badge.
- [ ] **Execute inspection**: Open a scheduled inspection. For each item, select Pass/Fail/N/A. Add comments. Save results.
- [ ] **Inspection templates**: Create a template with sections and items. Use the template when scheduling a new inspection. Verify items pre-populate.
- [ ] **Bilingual toggle**: Verify all labels (Scheduled Date, Inspector, Status, Pass/Fail/N/A) render in Spanish.

#### Edge Cases
- [ ] **Execute without items**: Schedule an inspection without assigning a template or items. Execute it. Verify no error is thrown.
- [ ] **All items N/A**: Mark all inspection items as N/A. Save. Verify inspection saves successfully.

---

### 5. Observations
**Template**: `templates/observations.php`  
**Handler**: `templates/observations_handler.php`

#### Manual Test Scenarios
- [ ] **Log observation**: Create an observation — project, category (Safety/Quality/Progress/Issue), observation text, assigned to, priority (Low/Medium/High/Critical), status (Open/In Progress/Verified/Closed).
- [ ] **Photo attachment**: Attach a photo to an observation. Verify it displays on the observation card.
- [ ] **GPS capture**: Verify lat/lon are auto-captured.
- [ ] **Status workflow**: Create an Open observation. Progress it: Open → In Progress → Verified → Closed. Verify each transition updates the badge color.
- [ ] **Bilingual toggle**: Verify categories, priorities, and status labels render in Spanish.

---

### 6. Punch List
**Template**: `templates/punch_list_v2.php`  
**Handler**: `templates/punch_handler.php`

#### Manual Test Scenarios
- [ ] **Create punch item**: Enter description, location, priority, assignee, cost code. Submit. Verify card appears in the list.
- [ ] **Batch assign**: Select multiple punch items via checkboxes. Batch-assign to a crew. Verify all selected items update their assignee.
- [ ] **Batch close**: Select multiple items. Batch-close them. Verify all show Closed status.
- [ ] **CSV export**: Click Export CSV. Verify a CSV file downloads with correct columns (id, description, status, assignee, location).
- [ ] **Verify item**: Mark a punch item as Verified (separate from Closed). Verify badge changes color.
- [ ] **Bilingual toggle**: Verify status badges and form labels render in Spanish.

---

### 7. Media Gallery
**Template**: `templates/media.php`  
**Handler**: `templates/media_handler.php`

#### Manual Test Scenarios
- [ ] **Upload media**: Upload a photo — fill title, project, cost code, date taken, tags. Verify it appears in the gallery grid.
- [ ] **Link to RFI**: Open a media item. Link it to an existing RFI. Verify the link is saved and visible on the media detail view.
- [ ] **Link to Punch List**: Link a media item to a punch list item. Verify link is recorded in `media_links` table.
- [ ] **Delete media**: Delete a media item. Confirm via SweetAlert2. Verify file is removed from disk and DB record deleted.
- [ ] **Bilingual toggle**: Verify form labels and gallery metadata render in Spanish.

---

### 8. Change Orders
**Template**: `templates/change_orders.php`  
**Handler**: `templates/change_order_handler.php`

#### Manual Test Scenarios
- [ ] **Create change order**: Select change event, cost code, CO type (NCC/CCD/CO/CR), amount, status (Draft). Submit.
- [ ] **Status workflow**: Progress a CO through all stages — Draft → Submitted → Reviewed → Approved → Issued. Verify badge colors change at each stage.
- [ ] **Commit to budget**: When a CO is Approved, click "Commit to Budget". Confirm via SweetAlert2. Verify `budget_committed=1` and status changes to Issued.
- [ ] **Link to cost code**: Verify CO amount is reflected in the cost code's `change_orders` column and impacts `committed_costs`.
- [ ] **Bilingual toggle**: Verify type labels and status labels render in Spanish.

#### Edge Cases
- [ ] **Commit unapproved**: Attempt to commit a CO that is still in Draft or Submitted status. Verify action is blocked or confirmation requires further workflow steps.
- [ ] **Zero amount CO**: Create a CO with $0 amount. Verify it saves without error.

---

### 9. Task Scheduling
**Template**: `templates/tasks.php`  
**Handler**: `templates/tasks_handler.php`

#### Manual Test Scenarios
- [ ] **Create task**: Enter task name, start date, end date, assigned crew, cost code, predecessor task (dependency). Submit.
- [ ] **Critical path indicator**: Create a task marked as critical. Verify red dot or critical badge appears on the card.
- [ ] **Gantt chart view**: Toggle to Gantt chart view. Verify tasks display as bars spanning their start/end dates. Verify predecessor dependency arrows render correctly.
- [ ] **Calendar view**: Toggle to calendar view. Verify tasks appear on their scheduled dates.
- [ ] **Predecessor dependency**: Assign a predecessor to a task. Verify in Gantt view the dependency arrow is visible.
- [ ] **Edit/delete task**: Edit a task's dates and crew. Verify Gantt bar updates. Delete a task and verify it is removed from both list and Gantt.
- [ ] **Bilingual toggle**: Verify date labels, crew names, and status labels render in Spanish.

---

### 10. Prime Contracts
**Template**: `templates/prime_contracts.php`  
**Handler**: `templates/prime_contract_handler.php`

#### Manual Test Scenarios
- [ ] **Create contract**: Fill contract number, contractor name, value, start date, end date, status (Active/Completed/Terminated), retention %, billing frequency. Submit.
- [ ] **CO value tracking**: Create change orders linked to this contract. Verify `change_order_value` and `revised_contract_value` auto-calculate (`contract_value + change_order_value`).
- [ ] **Version history**: Update a contract's value. Verify a new version record is created in `prime_contract_versions`. View version history and verify all versions are listed.
- [ ] **Bilingual toggle**: Verify all labels render in Spanish.

---

### 11. Document Control
**Template**: `templates/docs.php`  
**Handler**: `templates/docs_handler.php`

#### Manual Test Scenarios
- [ ] **Upload document**: Upload a file — title, revision number. Verify it appears in the document list.
- [ ] **Check out document**: Click Check Out on a document. Verify `checked_out_by` and `checked_out_at` are set. Verify other users see it as checked out.
- [ ] **Check in document**: Return to the checked-out document. Check it back in. Verify `checked_out_by` is cleared.
- [ ] **New revision**: Upload a new revision of an existing document. Verify revision number increments and version history is preserved.
- [ ] **Link to submittals/RFIs/COs**: Link a document to an existing submittal or RFI. Verify the link is saved.
- [ ] **Bilingual toggle**: Verify form labels and status badges render in Spanish.

---

### 12. GPS Field Integration
**Components**: `src/GPSEngine.php`, `templates/timesheets.php`, `templates/safety_hazards.php`, `templates/create_daily_log.php`

#### Manual Test Scenarios
- [ ] **Real GPS (no simulation)**: On a timesheet or safety hazard form, verify coordinates are captured from the browser's actual Geolocation API — not random values.
- [ ] **GPSEngine validation**: Inspect `GPSEngine.php`. Confirm `isValidCoords()` validates lat range [-90, 90] and lon range [-180, 180]. Confirm no `rand()` or `mt_rand()` calls remain.
- [ ] **GPS stamp format**: Submit a timesheet with GPS. Verify `gps_stamp` column contains properly formatted string (e.g., `"37.774900,-122.419400"`).
- [ ] **Unknown location fallback**: Submit a form with GPS denied. Verify fallback label shows "Unknown location" in EN or "Ubicación desconocida" in ES.
- [ ] **Bilingual GPS labels**: With `?lang=es`, verify GPS status text shows "Solicitando ubicación..." and "Ubicación capturada".

---

### 13. REST API & Webhooks
**Files**: `src/api/project_api.php`, `src/api/middleware.php`, `src/api/webhooks.php`

#### Manual Test Scenarios
- [ ] **API key generation**: Navigate to API keys management. Create a new key. Verify it appears in the list with masked preview and creation timestamp.
- [ ] **Authenticated request**: Use the key as Bearer token. Call `GET /api/projects`. Verify JSON response with HTTP 200.
- [ ] **Unauthenticated request**: Call `/api/projects` without Bearer token. Verify HTTP 401 and `{"error": "Unauthorized"}`.
- [ ] **Rate limiting**: Send rapid requests (>100/min). Verify HTTP 429 after threshold is exceeded.
- [ ] **POST timesheet**: Send `POST /api/timesheets` with JSON body. Verify record is created in DB and HTTP 201 returned.
- [ ] **Webhook signature**: Trigger a webhook event. Verify the signature header (`X-Webhook-Signature`) uses HMAC-SHA256 and matches the payload.
- [ ] **Invalid webhook signature**: Send a webhook request with a wrong signature. Verify it is rejected with HTTP 401.

---

## Automated Test Coverage (Playwright)

All new features above should have corresponding test cases in `tests/full_suite.spec.js`. Key areas to cover:

- **Navigation**: Verifies topbar, sidebar, and mobile bottom nav visibility and links.
- **RFI Lifecycle**: Tests creation, filtering, map pin interaction, and bulk PDF export.
- **Financials**: Validates budget tables, change orders lifecycle, and the interactive "What-If" simulator.
- **Admin**: Tests user management role labels and project settings persistence.
- **Timesheets**: Create, edit, approve, delete timesheet entries with GPS capture verification.
- **Equipment**: Add equipment, add service log, retire equipment.
- **Safety Hazards**: Report hazard with photo, close hazard, edit corrective action.
- **Inspections**: Schedule, execute with Pass/Fail/N/A per item, save results.
- **Observations**: Log observation with photo, status transitions.
- **Punch List**: Create item, batch assign, batch close, CSV export.
- **Media Gallery**: Upload, annotate, link to RFI/Punch List.
- **Change Orders**: Full lifecycle Draft→Submitted→Reviewed→Approved→Issued, commit to budget.
- **Task Scheduling**: Create task, Gantt view, Calendar view, predecessor dependency.
- **API Auth**: Bearer token validation, rate limiting, 401/429 responses.
- **Webhooks**: HMAC-SHA256 signature verification.

Run the full suite:
```bash
npx playwright test
```

## Manual Testing Scenarios

### 1. Interactive Widgets
- [ ] **Budget Simulator**: Slide the variance to 25%. Verify the "Projected Impact" label updates correctly based on committed costs.
- [ ] **RFI Map**: Hover over pins on the floor plan. Verify tooltips show correct RFI titles. Click a pin to navigate to the RFI details.
- [ ] **Notification Toggles**: Toggle budget alerts. Verify toast notification confirms the change.
- [ ] **Task Gantt**: Create two tasks with a predecessor dependency. Verify arrow renders in Gantt view.

### 2. AI Feature Validation
- [ ] **RFI Drafting**: Generate an AI draft for a "Slab Crack" issue. Verify the response includes Subject and Question.
- [ ] **Daily Report**: Convert field notes to a report. Check if "Safety Risks" are explicitly listed in a Markdown section.

### 3. Security & Access
- [ ] **MFA Verification**: Enter a simulated 6-digit code. Verify success toast and dashboard redirection.
- [ ] **RBAC**: Log in as a Subcontractor (Simulation). Verify "Project Settings" and "Audit Logs" are hidden or restricted.
- [ ] **CSRF**: Attempt to submit a form without a valid CSRF token. Verify the request is rejected.

## Backend Verification
Run the core logic test suite to ensure provider integrity:
```bash
php tests/run_tests.php
```

## Biblia Compliance Checklist

For every new feature added, verify:

- [ ] **Bilingual**: All user-facing labels, buttons, badges, and messages have EN/ES variants
- [ ] **SweetAlert2**: All destructive actions (delete, retire, close) use SweetAlert2 confirmations — no native `alert()`/`confirm()`
- [ ] **CSRF**: All POST forms include a valid CSRF token; handler validates via `csrf_validate()`
- [ ] **PermissionHelper**: All handlers instantiate `PermissionHelper` and check permissions
- [ ] **htmlspecialchars**: All dynamic output (card titles, names, descriptions) is wrapped with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`
- [ ] **GPS**: Forms auto-capture GPS via browser Geolocation API with EN/ES fallback labels
- [ ] **searchableSelect**: All multi-option dropdowns (cost codes, crews, categories) use the `.searchable-select` CSS class