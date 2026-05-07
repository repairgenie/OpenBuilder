<?php
// src/Database.php

class Database {
    private static $pdo = null;

    public static function connect() {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $db_file = __DIR__ . '/../database.sqlite';
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
            CREATE TABLE IF NOT EXISTS rfis (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                ref_number TEXT NOT NULL,
                subject TEXT NOT NULL,
                status TEXT NOT NULL DEFAULT 'Open',
                priority TEXT NOT NULL DEFAULT 'Medium',
                due_date TEXT NOT NULL
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS daily_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                log_date TEXT NOT NULL,
                weather TEXT,
                manpower INTEGER,
                work_performed TEXT,
                ai_report TEXT
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS cost_codes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                code TEXT NOT NULL,
                name TEXT NOT NULL,
                original_budget REAL NOT NULL DEFAULT 0,
                change_orders REAL NOT NULL DEFAULT 0,
                committed_costs REAL NOT NULL DEFAULT 0
            )
        ");

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS activity_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                action_en TEXT NOT NULL,
                action_es TEXT NOT NULL,
                ref_id INTEGER,
                module TEXT NOT NULL,
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
                type TEXT NOT NULL, -- Prime or Commitment
                status TEXT NOT NULL DEFAULT 'Draft',
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

        self::$pdo->exec("
            CREATE TABLE IF NOT EXISTS timesheets (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                worker_name TEXT NOT NULL,
                trade_en TEXT NOT NULL,
                trade_es TEXT NOT NULL,
                hours REAL NOT NULL,
                date TEXT NOT NULL,
                cost_code_id INTEGER,
                status TEXT NOT NULL DEFAULT 'Submitted'
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
    }
}
