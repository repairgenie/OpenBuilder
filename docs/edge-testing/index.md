# Edge-Testing Guide (English)
[Versión en Español](./es_index.md)

## Critical Failure Points & Boundary Conditions

### 1. API & Intelligence Failures
- **Scenario**: GEMINI_API_KEY is missing or invalid.
  - **Expected**: System should fallback gracefully. UI must show "AI Service Unavailable" instead of breaking. `AIProvider` must return descriptive error strings.
- **Scenario**: Weather Provider API Timeout.
  - **Expected**: Daily log should still allow submission. Weather section should show "Weather data unavailable".

### 2. Financial Boundary Cases
- **Scenario**: 100% Budget Utilization.
  - **Expected**: Dashboard progress bars should remain capped at 100% (no overflow). Variance alerts should trigger as "Critical".
- **Scenario**: Zero-Budget Cost Codes.
  - **Expected**: Revised budget and variance calculations must handle division by zero (result should be 0%).

### 3. Concurrency & Collision
- **Scenario**: Concurrent RFI Closure.
  - **Expected**: Simulation of two users closing the same RFI. System should notify the second user that the RFI status has already changed.

### 4. Input & Validation Stress
- **Scenario**: Excessively Large Daily Log Notes (10,000+ characters).
  - **Expected**: AI generation should truncate input to stay within token limits. UI should remain responsive during processing.
- **Scenario**: Invalid MFA Codes (Sequential failures).
  - **Expected**: After 3 failed attempts, system should require a 60-second cooldown before resending (Simulation).

### 5. UI/UX Edge Cases
- **Scenario**: Extreme Mobile Viewports (280px width).
  - **Expected**: Bottom Nav should collapse gracefully. Modal headers should wrap without breaking close buttons.
- **Scenario**: Print Media Queries.
  - **Expected**: Verify that navigation, sidebars, and buttons are hidden when printing RFI reports.
