<?php
// src/Database.php

class Database {
    private static $pdo = null;

    public static function connect() {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $db_file = getenv('DB_PATH') ?: (__DIR__ . '/../database.sqlite');
        $db_dir  = dirname($db_file);
        if (!is_dir($db_dir)) {
            @mkdir($db_dir, 0775, true);
        }
        try {
            self::$pdo = new PDO("sqlite:$db_file");
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::initTables();
            return self::$pdo;
        } catch (PDOException $e) {
            die("Database Connection failed: " . $e->getMessage());
        }
    }

    private static function initTables() {
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS projects (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                client_id INTEGER,
                status TEXT DEFAULT 'active',
                description TEXT,
                created_at TEXT DEFAULT (datetime('now')),
                updated_at TEXT DEFAULT (datetime('now'))
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS rfis (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ref_number TEXT NOT NULL,
                subject TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'Open',
                priority TEXT NOT NULL DEFAULT 'Medium',
                due_date TEXT NOT NULL,
                created_by INTEGER,
                created_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS timesheets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                worker_name TEXT NOT NULL,
                trade_en TEXT NOT NULL,
                trade_es TEXT NOT NULL,
                hours REAL NOT NULL,
                date TEXT NOT NULL,
                cost_code_id INTEGER,
                latitude REAL,
                longitude REAL,
                gps_stamp TEXT,
                status TEXT NOT NULL DEFAULT 'Submitted',
                created_by INTEGER,
                created_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS daily_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                log_date TEXT NOT NULL,
                weather TEXT,
                manpower INTEGER,
                work_performed TEXT,
                ai_report TEXT,
                latitude REAL,
                longitude REAL,
                gps_stamp TEXT,
                created_by INTEGER,
                created_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS production_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cost_code_id INTEGER NOT NULL,
                quantity REAL NOT NULL,
                unit TEXT NOT NULL,
                date TEXT NOT NULL,
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS cost_codes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT NOT NULL,
                name TEXT NOT NULL,
                original_budget REAL NOT NULL DEFAULT 0,
                change_orders REAL NOT NULL DEFAULT 0,
                committed_costs REAL NOT NULL DEFAULT 0,
                created_by INTEGER,
                created_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                user_id INTEGER,
                action_en TEXT NOT NULL,
                action_es TEXT NOT NULL,
                ref_id INTEGER,
                module TEXT NOT NULL,
                ip_address TEXT DEFAULT '127.0.0.1',
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS vendors (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                company_name TEXT NOT NULL,
                contact_name TEXT NOT NULL,
                email TEXT NOT NULL,
                trade_en TEXT NOT NULL,
                trade_es TEXT NOT NULL,
                rating REAL DEFAULT 0
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS bids (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                package_name TEXT NOT NULL,
                vendor_id INTEGER NOT NULL,
                amount REAL NOT NULL,
                status TEXT NOT NULL DEFAULT 'Submitted',
                submitted_at TEXT NOT NULL,
                FOREIGN KEY (vendor_id) REFERENCES vendors(id)
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS bim_issues (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                model_id TEXT NOT NULL,
                object_guid TEXT NOT NULL,
                description_en TEXT NOT NULL,
                description_es TEXT NOT NULL,
                rfi_id INTEGER,
                status TEXT NOT NULL DEFAULT 'Open',
                FOREIGN KEY (rfi_id) REFERENCES rfis(id)
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS change_events (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title_en TEXT NOT NULL,
                title_es TEXT NOT NULL,
                description_en TEXT,
                description_es TEXT,
                status TEXT NOT NULL DEFAULT 'Open',
                estimated_cost REAL DEFAULT 0,
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS change_orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                event_id INTEGER NOT NULL,
                co_number TEXT NOT NULL,
                amount REAL NOT NULL,
                type TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'Draft',
                budget_committed INTEGER DEFAULT 0,
                cost_code_id INTEGER,
                FOREIGN KEY (event_id) REFERENCES change_events(id)
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS inspections (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title_en TEXT NOT NULL,
                title_es TEXT NOT NULL,
                template_id INTEGER,
                status TEXT NOT NULL DEFAULT 'Initiated',
                inspector_id INTEGER,
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS inspection_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                inspection_id INTEGER NOT NULL,
                checkpoint_en TEXT NOT NULL,
                checkpoint_es TEXT NOT NULL,
                result TEXT DEFAULT 'Pending', -- Pass, Fail, N/A, Pending
                comments TEXT,
                FOREIGN KEY (inspection_id) REFERENCES inspections(id)
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS punch_list_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                description_en TEXT NOT NULL,
                description_es TEXT NOT NULL,
                location TEXT,
                assigned_to INTEGER,
                status TEXT NOT NULL DEFAULT 'Work Required', -- Work Required, Ready for Review, Verified
                priority TEXT DEFAULT 'Medium',
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS closeout_documents (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title_en TEXT NOT NULL,
                title_es TEXT NOT NULL,
                type TEXT NOT NULL, -- Warranty, Manual, As-built
                status TEXT NOT NULL DEFAULT 'Pending',
                file_path TEXT,
                checked_out_by TEXT,
                checked_out_at TEXT,
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS doc_versions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                doc_id INTEGER NOT NULL,
                revision TEXT NOT NULL DEFAULT 'A',
                file_path TEXT NOT NULL,
                file_size INTEGER,
                uploaded_by INTEGER REFERENCES users(id),
                created_at TEXT NOT NULL,
                notes TEXT,
                UNIQUE(doc_id, revision)
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS doc_links (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                doc_id INTEGER NOT NULL,
                link_type TEXT NOT NULL,
                link_id INTEGER NOT NULL,
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS submittals (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title_en TEXT NOT NULL,
                title_es TEXT NOT NULL,
                spec_section TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'Draft',
                ball_in_court INTEGER, -- User ID
                due_date TEXT,
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS submittal_responses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                submittal_id INTEGER NOT NULL,
                responder_id INTEGER NOT NULL,
                response_en TEXT NOT NULL,
                response_es TEXT NOT NULL,
                attachment_path TEXT,
                created_at TEXT NOT NULL,
                FOREIGN KEY (submittal_id) REFERENCES submittals(id)
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS commitments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title_en TEXT NOT NULL,
                title_es TEXT NOT NULL,
                vendor_id INTEGER NOT NULL,
                type TEXT NOT NULL, -- Purchase Order or Subcontract
                original_value REAL NOT NULL,
                status TEXT NOT NULL DEFAULT 'Draft',
                created_at TEXT NOT NULL,
                FOREIGN KEY (vendor_id) REFERENCES vendors(id)
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS progress_claims (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                commitment_id INTEGER NOT NULL,
                claim_number TEXT NOT NULL,
                amount_claimed REAL NOT NULL,
                status TEXT NOT NULL DEFAULT 'Pending',
                submitted_at TEXT NOT NULL,
                FOREIGN KEY (commitment_id) REFERENCES commitments(id)
            )
        ");

// timesheets table already defined above (lines 36-51) with lat/lon/gps_stamp
        // ensuring consistent schema across initTables()

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS production_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                cost_code_id INTEGER NOT NULL,
                quantity REAL NOT NULL,
                unit TEXT NOT NULL,
                date TEXT NOT NULL,
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS crews (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                trade_en TEXT NOT NULL,
                trade_es TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'On Site',
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS crew_members (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                crew_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                role TEXT,
                phone TEXT,
                FOREIGN KEY (crew_id) REFERENCES crews(id)
            )
        ");

        // ----- QUICK WIN 1: Users table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                role TEXT NOT NULL DEFAULT 'Viewer',
                status TEXT NOT NULL DEFAULT 'Active',
                created_at TEXT NOT NULL
            )
        ");

        // Seed default admin user (password: admin123)
        // Only seed in non-production environments to avoid hardcoded credentials in production
        $env = getenv('APP_ENV') ?: 'development';
        if ($env !== 'production') {
            $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute(['admin@openbuilder.com']);
            if ((int)$stmt->fetchColumn() === 0) {
                $hash = password_hash('admin123', PASSWORD_DEFAULT);
                $ins = self::$pdo->prepare("INSERT INTO users (name, email, password_hash, role, status, created_at) VALUES (?, ?, ?, 'Admin', 'Active', datetime('now'))");
                $ins->execute(['Admin User', 'admin@openbuilder.com', $hash]);
            }
        }

        // ----- Tasks table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                task_name TEXT NOT NULL,
                start_date TEXT NOT NULL,
                end_date TEXT NOT NULL,
                assigned_crew_id INTEGER,
                cost_code_id INTEGER,
                status TEXT NOT NULL DEFAULT 'Not Started',
                is_critical INTEGER DEFAULT 0,
                predecessor_task_id INTEGER,
                created_by INTEGER,
                created_at TEXT NOT NULL,
                FOREIGN KEY (assigned_crew_id) REFERENCES crews(id),
                FOREIGN KEY (cost_code_id) REFERENCES cost_codes(id),
                FOREIGN KEY (predecessor_task_id) REFERENCES tasks(id)
            )
        ");

        // ----- QUICK WIN 1: System settings (key/value store) -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS system_settings (
                key TEXT PRIMARY KEY,
                value TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )
        ");

        // Seed defaults
        $defaults = [
            'project_name'      => 'OpenBuilder HQ',
            'project_location'  => 'San Francisco, CA',
            'budget_alerts'     => '1',
            'new_rfi_notifications' => '1',
            'currency'          => 'USD',
            'date_format'       => 'Y-m-d',
            'timezone'          => 'America/Los_Angeles',
        ];
        foreach ($defaults as $k => $v) {
            $stmt = self::$pdo->prepare("INSERT OR IGNORE INTO system_settings (key, value, updated_at) VALUES (?, ?, datetime('now'))");
            $stmt->execute([$k, $v]);
        }

        // ----- API Keys table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS api_keys (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                api_key TEXT NOT NULL UNIQUE,
                last_used TEXT,
                created_at TEXT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ");

        // ----- API Keys table (for authentication) -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS api_keys (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                key_hash TEXT NOT NULL UNIQUE,
                name TEXT NOT NULL,
                permissions TEXT NOT NULL,
                rate_limit INTEGER DEFAULT 100,
                is_active INTEGER DEFAULT 1,
                last_used_at TEXT,
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS webhooks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                url TEXT NOT NULL,
                events TEXT NOT NULL,
                secret TEXT NOT NULL,
                is_active INTEGER DEFAULT 1,
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS api_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                api_key_id INTEGER,
                endpoint TEXT NOT NULL,
                method TEXT NOT NULL,
                response_code INTEGER,
                created_at TEXT NOT NULL,
                FOREIGN KEY (api_key_id) REFERENCES api_keys(id)
            )
        ");

        // ----- User Notification Preferences table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS user_notification_prefs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL UNIQUE,
                email_rfis INTEGER NOT NULL DEFAULT 1,
                email_daily_logs INTEGER NOT NULL DEFAULT 1,
                email_budget_alerts INTEGER NOT NULL DEFAULT 1,
                email_submittals INTEGER NOT NULL DEFAULT 1,
                email_inspections INTEGER NOT NULL DEFAULT 1,
                updated_at TEXT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ");

        // ----- System Roles table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS regions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name_en TEXT NOT NULL,
                name_es TEXT NOT NULL DEFAULT '',
                color TEXT NOT NULL DEFAULT '#3B82F6',
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL DEFAULT (datetime('now'))
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS system_roles (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                role_name TEXT NOT NULL UNIQUE,
                permissions TEXT NOT NULL,
                description_en TEXT,
                description_es TEXT,
                is_system INTEGER NOT NULL DEFAULT 0,
                created_at TEXT NOT NULL,
                updated_at TEXT NOT NULL
            )
        ");

        // Seed default roles
        $stmt = self::$pdo->prepare("SELECT COUNT(*) FROM system_roles");
        $stmt->execute();
        if ((int)$stmt->fetchColumn() === 0) {
            $default_roles = [
                [
                    'role_name' => 'Admin',
                    'permissions' => json_encode([
                        'view_dashboard', 'view_rfis', 'create_rfis', 'edit_rfis', 'delete_rfis', 'close_rfis',
                        'view_daily_logs', 'create_daily_logs', 'edit_daily_logs', 'delete_daily_logs',
                        'view_budget', 'edit_budget', 'view_reports', 'export_data',
                        'view_users', 'create_users', 'edit_users', 'delete_users', 'reset_user_passwords',
                        'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
                        'view_api_keys', 'create_api_keys', 'delete_api_keys',
                        'view_audit_logs', 'manage_settings', 'manage_submittals', 'manage_inspections',
                        'manage_commitments', 'manage_punch_list', 'manage_bim', 'manage_drawings',
                        'view_financials', 'edit_financials', 'manage_vendors'
                    ]),
                    'description_en' => 'Full system access with all permissions',
                    'description_es' => 'Acceso completo al sistema con todos los permisos',
                    'is_system' => 1,
                ],
                [
                    'role_name' => 'Manager',
                    'permissions' => json_encode([
                        'view_dashboard', 'view_rfis', 'create_rfis', 'edit_rfis', 'close_rfis',
                        'view_daily_logs', 'create_daily_logs', 'edit_daily_logs',
                        'view_budget', 'edit_budget', 'view_reports', 'export_data',
                        'view_users', 'view_roles',
                        'view_api_keys', 'create_api_keys',
                        'view_audit_logs', 'manage_submittals', 'manage_inspections',
                        'manage_commitments', 'manage_punch_list', 'manage_bim', 'manage_drawings',
                        'view_financials', 'edit_financials', 'manage_vendors'
                    ]),
                    'description_en' => 'Project management with operational permissions',
                    'description_es' => 'Gestión de proyectos con permisos operativos',
                    'is_system' => 1,
                ],
                [
                    'role_name' => 'Viewer',
                    'permissions' => json_encode([
                        'view_dashboard', 'view_rfis',
                        'view_daily_logs',
                        'view_budget', 'view_reports',
                        'view_users', 'view_roles',
                        'view_api_keys',
                        'view_audit_logs'
                    ]),
                    'description_en' => 'Read-only access to project data',
                    'description_es' => 'Acceso de solo lectura a datos del proyecto',
                    'is_system' => 1,
                ],
                [
                    'role_name' => 'Subcontractor',
                    'permissions' => json_encode([
                        'view_dashboard', 'view_rfis', 'create_rfis',
                        'view_daily_logs', 'create_daily_logs',
                        'view_budget',
                        'view_api_keys', 'create_api_keys',
                        'manage_punch_list'
                    ]),
                    'description_en' => 'Limited access for subcontractor work',
                    'description_es' => 'Acceso limitado para trabajo de subcontratista',
                    'is_system' => 1,
                ],
            ];
            $ins = self::$pdo->prepare("INSERT INTO system_roles (role_name, permissions, description_en, description_es, is_system, created_at, updated_at) VALUES (?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
            foreach ($default_roles as $r) {
                $ins->execute([$r['role_name'], $r['permissions'], $r['description_en'], $r['description_es'], $r['is_system']]);
            }
        }

        // ----- Equipment table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS equipment (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                asset_tag TEXT UNIQUE NOT NULL,
                name TEXT NOT NULL,
                type TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'active',
                assigned_project INTEGER,
                assigned_crew_id INTEGER,
                last_service_date TEXT,
                next_service_date TEXT,
                notes TEXT,
                created_at TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS equipment_service_log (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                equipment_id INTEGER NOT NULL,
                service_date TEXT NOT NULL,
                description TEXT NOT NULL,
                cost REAL,
                performed_by TEXT,
                created_at TEXT NOT NULL,
                FOREIGN KEY (equipment_id) REFERENCES equipment(id)
            )
        ");

        // ----- Safety Hazards table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS safety_hazards (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                description TEXT NOT NULL,
                location TEXT,
                severity TEXT NOT NULL DEFAULT 'Medium',
                reported_date TEXT NOT NULL,
                reported_by INTEGER,
                assigned_crew_id INTEGER,
                corrective_action TEXT,
                status TEXT NOT NULL DEFAULT 'Open',
                image_path TEXT,
                latitude REAL,
                longitude REAL,
                gps_stamp TEXT,
                created_at TEXT NOT NULL,
                FOREIGN KEY (reported_by) REFERENCES users(id),
                FOREIGN KEY (assigned_crew_id) REFERENCES crews(id)
            )
        ");

        // ----- Inspection Templates table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS inspection_templates (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                sections_json TEXT NOT NULL,
                created_at TEXT NOT NULL
            )
        ");

        // ----- Observations table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS observations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                project_id INTEGER NOT NULL,
                observer_id INTEGER NOT NULL,
                observation_text TEXT NOT NULL,
                category TEXT NOT NULL,
                assigned_to INTEGER,
                priority TEXT NOT NULL DEFAULT 'Medium',
                status TEXT NOT NULL DEFAULT 'Open',
                latitude REAL,
                longitude REAL,
                photo_path TEXT,
                created_at TEXT NOT NULL,
                FOREIGN KEY (project_id) REFERENCES projects(id),
                FOREIGN KEY (observer_id) REFERENCES users(id),
                FOREIGN KEY (assigned_to) REFERENCES users(id)
            )
        ");

        // ----- Punch List Items table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS punch_list_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                description TEXT NOT NULL,
                location TEXT,
                assigned_to INTEGER,
                priority TEXT NOT NULL DEFAULT 'Medium',
                status TEXT NOT NULL DEFAULT 'Open',
                due_date TEXT,
                created_by INTEGER,
                created_at TEXT NOT NULL,
                latitude REAL,
                longitude REAL,
                FOREIGN KEY (assigned_to) REFERENCES users(id),
                FOREIGN KEY (created_by) REFERENCES users(id)
            )
        ");

        // ----- Media table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS media (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                filename TEXT NOT NULL,
                title TEXT,
                project_id INTEGER,
                cost_code_id INTEGER,
                date_taken TEXT,
                tags TEXT,
                file_path TEXT NOT NULL,
                file_size INTEGER,
                mime_type TEXT,
                uploaded_by INTEGER,
                created_at TEXT NOT NULL,
                FOREIGN KEY (project_id) REFERENCES projects(id),
                FOREIGN KEY (cost_code_id) REFERENCES cost_codes(id)
            )
        ");

        // ----- Media Annotations table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS media_annotations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                media_id INTEGER NOT NULL,
                annotation_data TEXT NOT NULL,
                annotated_by INTEGER,
                created_at TEXT NOT NULL,
                FOREIGN KEY (media_id) REFERENCES media(id)
            )
        ");

        // ----- Media Links table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS media_links (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                media_id INTEGER NOT NULL,
                linked_type TEXT NOT NULL,
                linked_id INTEGER NOT NULL,
                created_at TEXT NOT NULL,
                FOREIGN KEY (media_id) REFERENCES media(id)
            )
        ");

        // ----- Prime Contracts table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS prime_contracts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                contract_number TEXT UNIQUE NOT NULL,
                contractor_name TEXT NOT NULL,
                contract_value REAL NOT NULL,
                start_date TEXT NOT NULL,
                end_date TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'Active',
                change_order_value REAL DEFAULT 0,
                revised_contract_value REAL,
                retention_percent REAL DEFAULT 0,
                billing_frequency TEXT DEFAULT 'Monthly',
                notes TEXT,
                created_at TEXT NOT NULL
            )
        ");

        // ----- Prime Contract Versions table -----
        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS prime_contract_versions (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                contract_id INTEGER NOT NULL,
                version_number INTEGER NOT NULL,
                contract_value REAL NOT NULL,
                change_order_value REAL DEFAULT 0,
                revised_contract_value REAL,
                status TEXT NOT NULL,
                notes TEXT,
                created_at TEXT NOT NULL,
                created_by INTEGER,
                FOREIGN KEY (contract_id) REFERENCES prime_contracts(id)
            )
        ");
    }
}
