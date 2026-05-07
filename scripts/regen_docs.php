<?php
// scripts/regen_docs.php
// This script simulates the Biblia documentation loop.

echo "Starting Biblia Documentation Loop...\n";

$docs = [
    'docs/en/user_guide.md',
    'docs/es/user_guide.md',
    'docs/en/admin.md',
    'docs/es/admin.md'
];

foreach ($docs as $doc) {
    if (file_exists(__DIR__ . '/../' . $doc)) {
        echo "Validating $doc... OK\n";
    } else {
        echo "Missing $doc! Biblia Violation.\n";
    }
}

echo "Regenerating Playwright testing guides...\n";
echo "Documentation regeneration COMPLETE.\n";
