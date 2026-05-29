# Edge-Testing Guide (English)
[Versión en Español](./es_index.md)

## Critical Failure Points & Boundary Conditions

### 1. API & Intelligence Failures
- **Scenario**: GEMINI_API_KEY is missing or invalid.
  - **Expected**: System should fallback gracefully. UI must show "AI Service Unavailable" instead of breaking. `AIProvider` must return descriptive error strings.
- **Scenario**: Weather Provider API Timeout.
  - **Expected**: Daily log should still allow submission. Weather section should show "Weather data unavailable".
- **Scenario**: API rate limit exceeded (>100 req/min).
  - **Expected**: Return HTTP 429 with `{"error": "Rate limit exceeded"}`. Log attempt in `api_logs`.

### 2. Financial Boundary Cases
- **Scenario**: 100% Budget Utilization.
  - **Expected**: Dashboard progress bars should remain capped at 100% (no overflow). Variance alerts should trigger as "Critical".
- **Scenario**: Zero-Budget Cost Codes.
  - **Expected**: Revised budget and variance calculations must handle division by zero (result should be 0%).
- **Scenario**: Change Order with $0 amount.
  - **Expected**: CO saves without error. No division-by-zero in variance math. `budget_committed` logic still works.
- **Scenario**: CO committed when already Issued.
  - **Expected**: Handler rejects with error flash. No duplicate commit.

### 3. Concurrency & Collision
- **Scenario**: Concurrent RFI Closure.
  - **Expected**: Simulation of two users closing the same RFI. System should notify the second user that the RFI status has already changed.
- **Scenario**: Concurrent timesheet approval by two foremen.
  - **Expected**: First approval wins. Second sees "Already approved" flash message.

### 4. Input & Validation Stress
- **Scenario**: Excessively Large Daily Log Notes (10,000+ characters).
  - **Expected**: AI generation should truncate input to stay within token limits. UI should remain responsive during processing.
- **Scenario**: Invalid MFA Codes (Sequential failures).
  - **Expected**: After 3 failed attempts, system should require a 60-second cooldown before resending (Simulation).
- **Scenario**: Photo upload > 5MB on Safety Hazard form.
  - **Expected**: Handler rejects with error flash: "File too large (max 5MB)". No partial upload.
- **Scenario**: CSRF token missing on handler POST.
  - **Expected**: Handler returns HTTP 400 and redirects with error flash.
- **Scenario**: GPS permission denied on timesheet/safety form.
  - **Expected**: Form submits successfully. `latitude`/`longitude` default to NULL. No blocking error.

### 5. UI/UX Edge Cases
- **Scenario**: Extreme Mobile Viewports (280px width).
  - **Expected**: Bottom Nav should collapse gracefully. Modal headers should wrap without breaking close buttons.
- **Scenario**: Print Media Queries.
  - **Expected**: Verify that navigation, sidebars, and buttons are hidden when printing RFI reports.
- **Scenario**: Bilingual toggle with `?lang=es`.
  - **Expected**: All 14 new modules (Timesheets, Equipment, Safety Hazards, Inspections, Observations, Punch List, Media, Change Orders, Tasks, Prime Contracts, Docs, API) render all labels, badges, and messages in Spanish.

### 6. GPS Field Integration Edge Cases
- **Scenario**: GPS coordinates out of range (lat > 90, lon > 180).
  - **Expected**: `GPSEngine::isValidCoords()` returns false. Form still saves with NULL GPS. No crash.
- **Scenario**: GPSEngine called with null values.
  - **Expected**: `formatStamp(null, null)` returns `"Unknown location"`. No exception thrown.
- **Scenario**: rand()/mt_rand() still present in GPSEngine.
  - **Expected**: Test fails. GPS coordinates must come from browser Geolocation API only — no simulation.

### 7. Timesheet Edge Cases
- **Scenario**: Timesheet with hours = 0.
  - **Expected**: Form either blocks submission or accepts with `hours=0` in DB (取决于业务规则). No crash.
- **Scenario**: Timesheet with future date.
  - **Expected**: Accepted and stored. Pre-dating for scheduling purposes allowed.
- **Scenario**: Timesheet approved by non-foreman role.
  - **Expected**: PermissionHelper blocks the action. Error flash shown.

### 8. Equipment & Safety Edge Cases
- **Scenario**: Retire already-retired equipment.
  - **Expected**: No duplicate DB update. No error. Idempotent.
- **Scenario**: Add service log to retired equipment.
  - **Expected**: Allowed. Equipment can be maintained after retirement.
- **Scenario**: Close an already-closed safety hazard.
  - **Expected**: No duplicate status update. Idempotent.
- **Scenario**: Safety hazard photo upload with wrong MIME type.
  - **Expected**: Handler validates MIME type. Rejects with error if not image/*.

### 9. Inspection & Observation Edge Cases
- **Scenario**: Execute inspection with no items assigned.
  - **Expected**: Form loads without error. Save should succeed with zero items.
- **Scenario**: All inspection items marked N/A.
  - **Expected**: Inspection saves successfully. No error from empty Pass/Fail result set.
- **Scenario**: Observation status transition backwards (Closed → Open).
  - **Expected**: Allowed? (取决于业务规则 — document the expected behavior). No crash either way.

### 10. Punch List Edge Cases
- **Scenario**: Batch assign to crew that has been deleted.
  - **Expected**: DB foreign key constraint either cascades or rejects. Handler should handle gracefully.
- **Scenario**: Export CSV with 0 items selected.
  - **Expected**: Download empty CSV or show "No items selected" message. No PHP error.

### 11. API & Webhook Edge Cases
- **Scenario**: Request with expired or revoked API key.
  - **Expected**: HTTP 401 with `{"error": "Invalid or revoked API key"}`. Log in `api_logs`.
- **Scenario**: Malformed JSON in POST body.
  - **Expected**: HTTP 400 with `{"error": "Invalid JSON"}`. Do not crash or expose stack trace.
- **Scenario**: Webhook received with HMAC mismatch.
  - **Expected**: HTTP 401. Log as failed delivery. Do not process payload.
- **Scenario**: Webhook delivery timeout.
  - **Expected**: Delivery marked as failed in `webhooks` table after 3 retries.

### 12. Document Control Edge Cases
- **Scenario**: Check out a document already checked out by another user.
  - **Expected**: Show "Document is currently checked out by [name]" error. Do not overwrite.
- **Scenario**: Check in a document that is not checked out.
  - **Expected**: Handler silently ignores or shows warning. No error.
- **Scenario**: Upload revision with same revision number.
  - **Expected**: Allow it or warn. Document version history should reflect duplicates clearly.

### 13. Task Scheduling Edge Cases
- **Scenario**: Predecessor task deleted while dependent exists.
  - **Expected**: `predecessor_task_id` becomes NULL or shows "Task no longer exists". No orphan reference crash.
- **Scenario**: Task end date before start date.
  - **Expected**: Form validation should prevent saving. Show "End date must be after start date".
- **Scenario**: Gantt view with 500+ tasks.
  - **Expected**: Rendering should not freeze the browser. Consider pagination or virtual scrolling.

### 14. Prime Contract Edge Cases
- **Scenario**: Delete a prime contract with existing COs linked to it.
  - **Expected**: Either cascade delete (cascade to `prime_contract_versions`) or reject deletion with "Contract has linked change orders" error.
- **Scenario**: `contract_value + change_order_value` exceeds PHP integer max.
  - **Expected**: UseBCmath or float. No integer overflow. Display "N/A" if value too large.