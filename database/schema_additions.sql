-- OpenBuilder schema additions from full monty security audit
-- Run these against the SQLite database to add ownership tracking columns

-- crews
ALTER TABLE crews ADD COLUMN created_by INTEGER;

-- change_orders
ALTER TABLE change_orders ADD COLUMN created_by INTEGER;

-- equipment
ALTER TABLE equipment ADD COLUMN created_by INTEGER;

-- inspections
ALTER TABLE inspections ADD COLUMN created_by INTEGER;

-- vendors
ALTER TABLE vendors ADD COLUMN created_by INTEGER;

-- safety_hazards (uses reported_by_user instead of created_by)
ALTER TABLE safety_hazards ADD COLUMN reported_by_user INTEGER;

-- media
ALTER TABLE media ADD COLUMN created_by INTEGER;

-- submittals
ALTER TABLE submittals ADD COLUMN created_by INTEGER;

-- punch_list_items
ALTER TABLE punch_list_items ADD COLUMN created_by INTEGER;

-- prime_contracts
ALTER TABLE prime_contracts ADD COLUMN created_by INTEGER;

-- daily_logs (owned by app.php inline handler)
ALTER TABLE daily_logs ADD COLUMN created_by INTEGER;

-- cost_codes (owned by app.php inline handler)
ALTER TABLE cost_codes ADD COLUMN created_by INTEGER;

-- rfis (owned by app.php inline handler)
ALTER TABLE rfis ADD COLUMN created_by INTEGER;

-- observations (owned by observations_handler)
ALTER TABLE observations ADD COLUMN created_by INTEGER;

-- timesheets (already has created_by from earlier fix)
-- Verify: PRAGMA table_info(timesheets);