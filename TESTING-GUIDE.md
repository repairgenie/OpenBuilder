# OpenBuilder Testing Guide / Guía de Pruebas de OpenBuilder

## Overview / Visión General

**OpenBuilder** is a bilingual (English/Spanish) construction management web application built with PHP and SQLite. It provides tools for managing RFIs, daily logs, tasks, crews, budgets, documents, and more.

**OpenBuilder** es una aplicación web bilingüe (inglés/español) de gestión de construcción construida con PHP y SQLite. Proporciona herramientas para gestionar RFIs, diarios de obra, tareas, cuadrillas, presupuestos, documentos y más.

---

## Application Structure / Estructura de la Aplicación

### Tech Stack
- **Backend:** PHP with PDO/SQLite
- **Frontend:** TailAdmin CSS framework, Alpine.js, Chart.js
- **AI:** Gemini API integration for report generation
- **Auth:** Session-based with MFA support

### Database
- SQLite at `database/database.sqlite`
- 30+ tables covering all construction management domains

### Public Pages (No Auth Required)
| Page | URL | Purpose |
|------|-----|---------|
| Dashboard | `?page=dashboard` | Project overview, stats, quick actions |
| Health | `?page=health` | System health monitoring |
| Login | `?page=login` | User authentication |
| MFA | `?page=mfa` | Multi-factor authentication |

### Protected Pages (Require Auth)
All other pages require authentication via login.

---

## Pages & Functionality Reference / Páginas y Referencia de Funcionalidad

### 1. Dashboard (`?page=dashboard`) - PUBLIC
**Purpose:** Project overview and KPI display

**Elements:**
- Stats cards: Open RFIs, Total Logs, Budget Utilization %, Manpower Today
- Quick Actions: New Daily Log, Create RFI, New Cost Code
- Recent Activity feed
- Budget Distribution doughnut chart
- Project Timeline
- AI Schedule Prediction
- AI Risk Heatmap
- RFI Aging bar chart

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| D-01 | Load dashboard with default language | Shows English labels and content |
| D-02 | Change language to Spanish | All labels change to Spanish |
| D-03 | Click "New Daily Log" link | Navigates to `?page=create_daily_log` |
| D-04 | Click "Create RFI" link | Navigates to `?page=create_rfi` |
| D-05 | Click "New Cost Code" link | Navigates to `?page=create_cost_code` |
| D-06 | View AI Schedule Prediction | Displays prediction text |
| D-07 | View AI Risk Heatmap | Shows colored grid cells |
| D-08 | Budget chart renders | Doughnut chart shows budget data |
| D-09 | RFI Aging chart renders | Bar chart shows aging distribution |

---

### 2. Health (`?page=health`) - PUBLIC
**Purpose:** System health monitoring

**Elements:**
- Database status card (SQLite connection)
- AI Service status card (Gemini API key check)

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| H-01 | Load health page | Shows both service statuses |
| H-02 | Database healthy | Green "Healthy" indicator |
| H-03 | AI key missing | Red "Unavailable" indicator |
| H-04 | AI key present | Green "Healthy" indicator |

---

### 3. Login (`?page=login`) - PUBLIC
**Purpose:** User authentication

**Elements:**
- Email input field
- Password input field
- "Sign In / Iniciar Sesion" button
- "View site without account (read-only)" link

**Demo Credentials:**
| Email | Password | Role |
|-------|----------|------|
| admin@openbuilder.com | admin123 | Admin |
| manager@openbuilder.com | manager123 | Manager |
| sub@openbuilder.com | sub123 | Subcontractor |

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| L-01 | Load login page | Shows email/password form |
| L-02 | Submit valid admin credentials | Redirects to MFA verification |
| L-03 | Submit invalid credentials | Shows error "Invalid credentials" |
| L-04 | Submit empty email | HTML5 validation error |
| L-05 | Click "View without account" | Goes to dashboard (read-only) |
| L-06 | Switch to Spanish | Labels show in Spanish |

---

### 4. MFA Verification (`?page=mfa`) - PUBLIC
**Purpose:** Multi-factor authentication after login

**Elements:**
- 6-digit code input
- "Verify Code" button
- Resend code link (if implemented)

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| M-01 | Load MFA page after login | Shows code input |
| M-02 | Enter valid code | Redirects to dashboard |
| M-03 | Enter invalid code | Shows error |
| M-04 | Enter non-numeric code | HTML5 validation error |

---

### 5. RFIs (`?page=rfis`) - PROTECTED
**Purpose:** Request for Information management

**Elements:**
- Page title: "Requests for Information (RFIs)"
- Export CSV button
- Create RFI button
- Status filter dropdown (All, Open, Closed)
- Priority filter dropdown (All, High, Medium, Low)
- Filter button
- Clear link
- RFI List table with:
  - Checkbox (select all)
  - Ref # column
  - Subject column
  - Priority column
  - Status column (badge)
  - Action (View link)
- Bulk actions bar (appears when items selected):
  - Export PDF button
  - Close Selected button
- Pagination controls

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| R-01 | Load RFIs page | Shows RFI list table |
| R-02 | Filter by Open status | Shows only Open RFIs |
| R-03 | Filter by High priority | Shows only High priority RFIs |
| R-04 | Clear filters | Shows all RFIs |
| R-05 | Click Create RFI | Navigates to `?page=create_rfi` |
| R-06 | Export CSV | Downloads CSV file |
| R-07 | Select single RFI | Bulk bar appears with count |
| R-08 | Select all RFIs | All checkboxes checked |
| R-09 | Click View on RFI | Navigates to RFI detail |
| R-10 | Click Export PDF | Shows toast "Exporting PDFs..." |

---

### 6. Create RFI (`?page=create_rfi`) - PROTECTED
**Purpose:** Create new RFI

**Elements:**
- Reference Number input (required)
- Subject input (required)
- Due Date picker (required)
- Priority dropdown (Low, Medium, High)
- Submit RFI button

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| CR-01 | Load create RFI page | Shows empty form |
| CR-02 | Submit with all fields | Creates RFI, redirects to list |
| CR-03 | Submit without ref_number | HTML5 validation error |
| CR-04 | Submit without subject | HTML5 validation error |
| CR-05 | Submit without due_date | HTML5 validation error |
| CR-06 | Set priority to High | Form submits with High priority |
| CR-07 | Switch language to ES | Labels in Spanish |

---

### 7. Daily Logs (`?page=daily_logs`) - PROTECTED
**Purpose:** View and manage daily construction logs

**Elements:**
- Page title: "Daily Logs"
- Create Daily Log button
- Grid of log cards showing:
  - Date
  - Weather badge
  - Work description (truncated)
  - Manpower count
  - "View Details" link
- Pagination

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| DL-01 | Load daily logs page | Shows log cards grid |
| DL-02 | Click Create Daily Log | Navigates to create form |
| DL-03 | Click View Details | Navigates to log detail |
| DL-04 | Empty state | Shows "No daily logs found" |

---

### 8. Create Daily Log (`?page=create_daily_log`) - PROTECTED
**Purpose:** Create new daily log with GPS and AI

**Elements:**
- Date picker (pre-filled with today)
- Weather input (auto-filled from API)
- Manpower (number input)
- GPS display (lat/lon with status)
- Work Performed textarea
- Cancel button
- "Save & Generate Report" button

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| CD-01 | Load create daily log | Form with auto-filled date/weather |
| CD-02 | GPS auto-fetch | Shows lat/lon coordinates |
| CD-03 | GPS denied | Shows "FAILED" status |
| CD-04 | Submit with all fields | Creates log, redirects to view |
| CD-05 | Submit without date | HTML5 validation error |
| CD-06 | Submit without work_performed | Allows (AI report optional) |
| CD-07 | AI generates report | Report appears on view page |

---

### 9. Tasks (`?page=tasks`) - PROTECTED
**Purpose:** Task scheduling with Gantt chart and calendar

**Elements:**
- Page title and description
- View toggle button (Gantt/Calendar)
- New Task button
- Gantt View (default):
  - Task name column
  - Timeline visualization
  - Critical path indicator (red dots)
- Calendar View (toggle):
  - Monthly grouping of tasks
- Task List table:
  - Task name (with critical indicator)
  - Start Date
  - End Date
  - Crew
  - Cost Code
  - Status badge
  - Actions (Edit, Delete)
- Create/Edit Task modal:
  - Task Name (required)
  - Start Date (required)
  - End Date (required)
  - Assigned Crew dropdown
  - Cost Code dropdown
  - Status dropdown
  - Predecessor Task dropdown
  - Critical path checkbox

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| T-01 | Load tasks page | Shows Gantt view by default |
| T-02 | Toggle to Calendar view | Shows tasks grouped by month |
| T-03 | Toggle back to Gantt | Shows Gantt chart |
| T-04 | Click New Task | Opens create modal |
| T-05 | Fill and submit new task | Task appears in list |
| T-06 | Click Edit on task | Opens edit modal with data |
| T-07 | Update task | Task updates in list |
| T-08 | Click Delete on task | Shows confirmation dialog |
| T-09 | Confirm delete | Task removed from list |
| T-10 | Critical path tasks show red dot | Visual indicator present |
| T-11 | Predecessor dropdown shows tasks | Can link dependencies |

---

### 10. Budget (`?page=budget`) - PROTECTED
**Purpose:** Budget management and tracking

**Elements:**
- Page title: "Budget"
- Currency selector (USD, EUR)
- Export CSV button
- Scenario Simulator:
  - Variance slider (0-50%)
  - Projected Impact display
- Budget table:
  - Cost Code
  - Original Budget
  - Change Orders
  - Revised Budget
  - Committed
  - Variance
- Totals row

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| B-01 | Load budget page | Shows cost codes table |
| B-02 | Move variance slider | Impact updates dynamically |
| B-03 | Export CSV | Downloads budget CSV |
| B-04 | Negative variance shows red | Color coding correct |
| B-05 | Totals row calculates | Correct sums displayed |

---

### 11. Users (`?page=users`) - PROTECTED
**Purpose:** User management

**Elements:**
- Page title: "User Management"
- Add User button
- User table:
  - Avatar (from ui-avatars.com)
  - Name
  - Email
  - Role badge
  - Status badge
  - Actions: Edit, Delete, Reset Password
- Create/Edit modal:
  - Name input
  - Email input
  - Role dropdown
  - Status dropdown
  - Save/Cancel buttons

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| U-01 | Load users page | Shows user table |
| U-02 | Click Add User | Opens create modal |
| U-03 | Submit new user | User appears in table |
| U-04 | Click Edit | Opens edit modal |
| U-05 | Update user | Changes reflected in table |
| U-06 | Click Delete | Shows confirmation |
| U-07 | Confirm delete | User removed from table |
| U-08 | Click Reset Password | Shows confirmation |
| U-09 | Confirm reset | Toast shows success |

---

### 12. Roles (`?page=roles`) - PROTECTED
**Purpose:** Role and permission matrix management

**Elements:**
- Page title: "Role & Permission Matrix"
- Add Role button
- Role cards showing:
  - Role name
  - Description
  - Permission count
  - System/Custom badge
  - Edit/Delete actions
- Create/Edit modal with permission checkboxes grouped by category

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| RO-01 | Load roles page | Shows role cards |
| RO-02 | Click Add Role | Opens create modal |
| RO-03 | Select permissions | Multiple can be selected |
| RO-04 | Save new role | Role appears in cards |
| RO-05 | System roles cannot be deleted | Delete button hidden |
| RO-06 | Custom roles can be deleted | Confirmation shown |

---

### 13. Crew Management (`?page=crew_management`) - PROTECTED
**Purpose:** Crew and personnel management

**Elements:**
- Page title: "Crew Management"
- New Crew button
- Crew cards showing:
  - Crew name
  - Status badge
  - Trade
  - Member avatars
  - Add Member button
- Create crew modal
- Add member modal

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| CM-01 | Load crew page | Shows crew cards |
| CM-02 | Create new crew | Crew card appears |
| CM-03 | Add member to crew | Member count increases |
| CM-04 | Edit crew | Modal opens with data |
| CM-05 | Delete crew | Confirmation, then removed |

---

### 14. Docs (`?page=docs`) - PROTECTED
**Purpose:** Document control and management

**Elements:**
- Page title: "Document Control"
- Upload Document button
- Document table with:
  - Title
  - Type
  - Revision
  - Status
  - Checked Out info
  - Date
  - Actions
- Search bar
- Type filter
- Status filter

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| DOC-01 | Load docs page | Shows document table |
| DOC-02 | Upload document | Document appears in list |
| DOC-03 | Check out document | Status updates |
| DOC-04 | Check in document | Status updates |
| DOC-05 | Link document to RFI | Association created |
| DOC-06 | Delete document | Confirmation shown |

---

### 15. Safety Hazards (`?page=safety_hazards`) - PROTECTED
**Purpose:** Safety hazard tracking

**Elements:**
- Page title: "Safety Hazards"
- Report Hazard button
- Hazard cards/list

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| SH-01 | Load safety page | Shows hazards |
| SH-02 | Report new hazard | Form opens/submits |
| SH-03 | Update hazard status | Changes reflected |

---

### 16. Equipment (`?page=equipment`) - PROTECTED
**Purpose:** Equipment tracking and management

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| EQ-01 | Load equipment page | Shows equipment list |
| EQ-02 | Add equipment | Appears in list |
| EQ-03 | Log service | Service record created |

---

### 17. API Keys (`?page=api_keys`) - PROTECTED
**Purpose:** API key management

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| AK-01 | Load API keys page | Shows keys list |
| AK-02 | Create new key | Key generated and shown |
| AK-03 | Delete key | Confirmation, then removed |

---

### 18. Audit Logs (`?page=audit_logs`) - PROTECTED
**Purpose:** System audit trail

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| AL-01 | Load audit logs | Shows activity history |
| AL-02 | Filter by date | Shows filtered results |

---

### 19. Observations (`?page=observations`) - PROTECTED
**Purpose:** Site observation tracking

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| OB-01 | Load observations | Shows observations list |
| OB-02 | Create observation | Added to list |

---

### 20. Submittals (`?page=submittals`) - PROTECTED
**Purpose:** Submittal management

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| SUB-01 | Load submittals | Shows submittals list |
| SUB-02 | Create submittal | Appears in list |

---

### 21. Punch List (`?page=punch_list_v2`) - PROTECTED
**Purpose:** Punch list management

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| PL-01 | Load punch list | Shows items |
| PL-02 | Add item | Item added |
| PL-03 | Mark complete | Status changes |

---

### 22. Inspection Schedule (`?page=inspection_schedule`) - PROTECTED
**Purpose:** Inspection scheduling

**Test Cases:**
| ID | Description | Expected Result |
|----|-------------|-----------------|
| IS-01 | Load schedule | Shows inspections |
| IS-02 | Schedule inspection | Added to list |

---

## Bilingual Testing / Pruebas Bilingües

All pages must be tested in both English and Spanish:

| Test | Method |
|------|--------|
| Language switch | Add `&lang=es` to URL |
| All labels check | Verify EN/ES text differs |
| Date formats | EN uses MM/DD/YYYY, ES may use DD/MM/YYYY |
| Validation messages | Should appear in selected language |

---

## Test Data Requirements / Requisitos de Datos de Prueba

### Users
- Admin: admin@openbuilder.com / admin123
- Manager: manager@openbuilder.com / manager123
- Subcontractor: sub@openbuilder.com / sub123

### RFIs
- Seeded with 3 RFIs (#101, #102, #103)

### Daily Logs
- Seeded with 2 logs

### Cost Codes
- 5 cost codes seeded

### Tasks
- Tasks table created but may be empty

---

## Known Limitations / Limitaciones Conocidas

1. **MFA Bypass:** MFA is implemented but can be bypassed in development
2. **Read-only Dashboard:** Unauthenticated users can view dashboard but cannot perform actions
3. **No Email Sending:** Email notifications not actually sent (placeholder only)
4. **GPS Fallback:** GPS requires user permission; fails gracefully
5. **AI Reports:** Require GEMINI_API_KEY environment variable
6. **No Real-time Updates:** Pages require manual refresh

---

## Browser Compatibility / Compatibilidad de Navegador

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

**Mobile:** Bottom navigation bar on mobile devices

---

## Test Execution Checklist / Lista de Verificación de Ejecución

- [ ] Login flow (success and failure)
- [ ] MFA verification
- [ ] All CRUD operations on each entity
- [ ] Bilingual labels on all pages
- [ ] Filter and search functionality
- [ ] Pagination
- [ ] Export functionality
- [ ] Error states and validation
- [ ] Mobile responsive layout
- [ ] AI features (when API key configured)