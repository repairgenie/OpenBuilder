# OpenBuilder Testing Guide (English)

## Core Testing Principles
1. **Bilingual Integrity**: Every UI element must support English and Spanish. Use the `lang` parameter in URLs for validation.
2. **AI Reliability**: AI-generated content (Daily Reports, RFI Drafts) must be checked for technical relevance and formatting.
3. **Data Consistency**: Ensure budget metrics (Spent vs. Remaining) are mathematically consistent across Dashboard and Budget views.

## Automated Test Coverage (Playwright)
- **Navigation**: Verifies topbar, sidebar, and mobile bottom nav visibility and links.
- **RFI Lifecycle**: Tests creation, filtering, map pin interaction, and bulk PDF export.
- **Financials**: Validates budget tables and the interactive "What-If" simulator.
- **Admin**: Tests user management role labels and project settings persistence (Simulation).

## Manual Testing Scenarios

### 1. Interactive Widgets
- [ ] **Budget Simulator**: Slide the variance to 25%. Verify the "Projected Impact" label updates correctly based on committed costs.
- [ ] **RFI Map**: Hover over pins on the floor plan. Verify tooltips show correct RFI titles. Click a pin to navigate to the RFI details.
- [ ] **Notification Toggles**: Toggle budget alerts. Verify toast notification confirms the change.

### 2. AI Feature Validation
- [ ] **RFI Drafting**: Generate an AI draft for a "Slab Crack" issue. Verify the response includes Subject and Question.
- [ ] **Daily Report**: Convert field notes to a report. Check if "Safety Risks" are explicitly listed in a Markdown section.

### 3. Security & Access
- [ ] **MFA Verification**: Enter a simulated 6-digit code. Verify success toast and dashboard redirection.
- [ ] **RBAC**: Log in as a Subcontractor (Simulation). Verify "Project Settings" and "Audit Logs" are hidden or restricted.

## Backend Verification
Run the core logic test suite to ensure provider integrity:
```bash
php tests/run_tests.php
```
