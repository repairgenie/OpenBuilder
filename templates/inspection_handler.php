<?php
// templates/inspection_handler.php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_POST['lang'] ?? 'en';
$csrf = $_POST['csrf_token'] ?? '';

require_auth();

$csrf = $_POST['csrf_token'] ?? '';
if (!csrf_valid($csrf)) {
    $_SESSION['flash_error'] = $lang === 'es' ? 'Token de seguridad inválido.' : 'Invalid security token.';
    header("Location: ../index.php?page=inspection_schedule&lang=$lang");
    exit;
}

$pdo = Database::connect();
$action = $_POST['action'] ?? '';
$redirect = "Location: ../index.php?page=inspection_schedule&lang=$lang";

switch ($action) {
    case 'schedule_inspection':
        $stmt = $pdo->prepare("INSERT INTO inspections (title, template_id, inspector_id, location, project_id, scheduled_date, created_at) VALUES (?, ?, ?, ?, ?, ?, datetime('now'))");
        $stmt->execute([
            $_POST['title'],
            $_POST['template_id'] ?: null,
            $_POST['inspector_id'],
            $_POST['location'] ?: '',
            $_POST['project_id'] ?: null,
            $_POST['scheduled_date']
        ]);
        $inspection_id = $pdo->lastInsertId();
        // If a template was selected, populate items from it
        if (!empty($_POST['template_id'])) {
            $tstmt = $pdo->prepare("SELECT sections_json FROM inspection_templates WHERE id=?");
            $tstmt->execute([$_POST['template_id']]);
            $template = $tstmt->fetch(PDO::FETCH_ASSOC);
            if ($template) {
                $sections = json_decode($template['sections_json'] ?? '[]', true);
                foreach ($sections as $section) {
                    foreach ($section['items'] ?? [] as $item_text) {
                        $pdo->prepare("INSERT INTO inspection_items (inspection_id, section_name, item_text) VALUES (?, ?, ?)")
                            ->execute([$inspection_id, $section['name'] ?? '', $item_text]);
                    }
                }
            }
        }
        $_SESSION['flash_success'] = $lang === 'es' ? 'Inspección programada.' : 'Inspection scheduled.';
        break;

    case 'update_inspection':
        $stmt = $pdo->prepare("UPDATE inspections SET title=?, template_id=?, inspector_id=?, location=?, project_id=?, scheduled_date=? WHERE id=?");
        $stmt->execute([
            $_POST['title'],
            $_POST['template_id'] ?: null,
            $_POST['inspector_id'],
            $_POST['location'] ?: '',
            $_POST['project_id'] ?: null,
            $_POST['scheduled_date'],
            $_POST['id']
        ]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Inspección actualizada.' : 'Inspection updated.';
        break;

    case 'delete_inspection':
        $pdo->prepare("DELETE FROM inspection_items WHERE inspection_id=?")->execute([$_POST['id']]);
        $pdo->prepare("DELETE FROM inspections WHERE id=?")->execute([$_POST['id']]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Inspección eliminada.' : 'Inspection deleted.';
        break;

    case 'save_results':
        $inspection_id = intval($_POST['inspection_id'] ?? 0);
        // Update each item's result and comments
        foreach ($_POST as $key => $value) {
            if (preg_match('/^item_(\d+)_result$/', $key, $m)) {
                $item_id = intval($m[1]);
                $comments_key = 'item_' . $item_id . '_comments';
                $pdo->prepare("UPDATE inspection_items SET result=?, comments=?, inspected_at=datetime('now') WHERE id=?")
                    ->execute([$value, $_POST[$comments_key] ?? '', $item_id]);
            }
        }
        // Mark inspection as Completed
        $pdo->prepare("UPDATE inspections SET status='Completed' WHERE id=?")->execute([$inspection_id]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Resultados guardados.' : 'Results saved.';
        header($redirect);
        exit;

    case 'create_template':
        $title = trim($_POST['template_title'] ?? '');
        $sections_json = $_POST['sections_json'] ?? '[]';
        if (empty($title)) {
            $_SESSION['flash_error'] = $lang === 'es' ? 'El título de la plantilla es requerido.' : 'Template title is required.';
            header($redirect);
            exit;
        }
        $pdo->prepare("INSERT INTO inspection_templates (title, sections_json, created_at) VALUES (?, ?, datetime('now'))")
            ->execute([$title, $sections_json]);
        $_SESSION['flash_success'] = $lang === 'es' ? 'Plantilla creada.' : 'Template created.';
        break;

    default:
        $_SESSION['flash_error'] = $lang === 'es' ? 'Acción desconocida.' : 'Unknown action.';
}

header($redirect);
exit;
