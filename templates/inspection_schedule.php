<!-- templates/inspection_schedule.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();
$user_id = $_SESSION['user_id'] ?? null;

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$projects = $pdo->query("SELECT id, name FROM projects ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT id, name FROM users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$templates = $pdo->query("SELECT * FROM inspection_templates ORDER BY title")->fetchAll(PDO::FETCH_ASSOC);

$inspections = $pdo->query("
    SELECT i.*, u.name as inspector_name, p.name as project_name
    FROM inspections i
    LEFT JOIN users u ON i.inspector_id = u.id
    LEFT JOIN projects p ON i.project_id = p.id
    ORDER BY i.scheduled_date DESC, i.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

function status_badge($status) {
    $map = [
        'Scheduled' => 'bg-warning bg-opacity-10 text-warning',
        'In Progress' => 'bg-primary bg-opacity-10 text-primary',
        'Completed' => 'bg-success bg-opacity-10 text-success',
        'Failed' => 'bg-danger bg-opacity-10 text-danger',
    ];
    return $map[$status] ?? 'bg-slate-100 text-slate-600';
}
?>
<!-- Flash Messages -->
<?php if ($flash_success): ?>
<div id="toast-success" class="fixed top-4 right-4 z-9999 flex items-center gap-3 bg-success text-white px-6 py-4 rounded-lg shadow-xl">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
    <span class="font-medium"><?php echo htmlspecialchars($flash_success); ?></span>
    <button onclick="document.getElementById('toast-success').remove()" class="ml-2 hover:opacity-80">✕</button>
</div>
<script>setTimeout(() => document.getElementById('toast-success')?.remove(), 4000);</script>
<?php endif; ?>

<?php if ($flash_error): ?>
<div id="toast-error" class="fixed top-4 right-4 z-9999 flex items-center gap-3 bg-danger text-white px-6 py-4 rounded-lg shadow-xl">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
    <span class="font-medium"><?php echo htmlspecialchars($flash_error); ?></span>
    <button onclick="document.getElementById('toast-error').remove()" class="ml-2 hover:opacity-80">✕</button>
</div>
<script>setTimeout(() => document.getElementById('toast-error')?.remove(), 4000);</script>
<?php endif; ?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Programación de Inspecciones' : 'Inspection Schedule'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Calendario de inspecciones de campo.' : 'Field inspection scheduling and tracking.'; ?></p>
    </div>
    <div class="flex gap-2">
        <button onclick="openModal('schedule-inspection-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Programar Inspección' : 'Schedule Inspection'; ?>
        </button>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Título' : 'Title'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Inspector' : 'Inspector'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Ubicación' : 'Location'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Fecha' : 'Date'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-center"><?php echo $lang === 'es' ? 'Acciones' : 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inspections)): ?>
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin inspecciones programadas.' : 'No inspections scheduled.'; ?></td>
                </tr>
                <?php else: foreach ($inspections as $insp): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 font-bold text-black"><?php echo htmlspecialchars($insp['title']); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($insp['inspector_name'] ?? '-'); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($insp['location'] ?? '-'); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($insp['scheduled_date']); ?></td>
                    <td class="py-4 px-4"><span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo status_badge($insp['status']); ?>"><?php echo htmlspecialchars($insp['status']); ?></span></td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <?php if ($insp['status'] === 'Scheduled'): ?>
                            <a href="?page=inspection_execution&id=<?php echo $insp['id']; ?>&lang=<?php echo $lang; ?>" class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Ejecutar' : 'Execute'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                            </a>
                            <?php endif; ?>
                            <button onclick='editInspection(<?php echo htmlspecialchars(json_encode($insp)); ?>)' class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button onclick="confirmDelete(<?php echo $insp['id']; ?>)" class="p-2 hover:bg-slate-100 rounded text-danger" title="<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Schedule Inspection Modal -->
<div id="schedule-inspection-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="insp-modal-title"><?php echo $lang === 'es' ? 'Programar Inspección' : 'Schedule Inspection'; ?></h3>
            <button onclick="closeModal('schedule-inspection-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/inspection_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="insp-action" value="schedule_inspection">
            <input type="hidden" name="id" id="insp-id">
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Título' : 'Title'; ?> *</label>
                <input type="text" name="title" id="insp-title" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Inspector' : 'Inspector'; ?> *</label>
                    <select name="inspector_id" id="insp-inspector" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="">-- <?php echo $lang === 'es' ? 'Seleccionar' : 'Select'; ?> --</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha Programada' : 'Scheduled Date'; ?> *</label>
                    <input type="date" name="scheduled_date" id="insp-date" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Proyecto' : 'Project'; ?></label>
                    <select name="project_id" id="insp-project" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="">-- <?php echo $lang === 'es' ? 'Ninguno' : 'None'; ?> --</option>
                        <?php foreach ($projects as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Plantilla' : 'Template'; ?></label>
                    <select name="template_id" id="insp-template" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="">-- <?php echo $lang === 'es' ? 'Ninguna' : 'None'; ?> --</option>
                        <?php foreach ($templates as $t): ?>
                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['title']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Ubicación' : 'Location'; ?></label>
                <input type="text" name="location" id="insp-location" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('schedule-inspection-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function editInspection(insp) {
    document.getElementById('insp-action').value = 'update_inspection';
    document.getElementById('insp-id').value = insp.id;
    document.getElementById('insp-title').value = insp.title || '';
    document.getElementById('insp-inspector').value = insp.inspector_id || '';
    document.getElementById('insp-date').value = insp.scheduled_date || '';
    document.getElementById('insp-project').value = insp.project_id || '';
    document.getElementById('insp-template').value = insp.template_id || '';
    document.getElementById('insp-location').value = insp.location || '';
    document.getElementById('insp-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Inspección' : 'Edit Inspection'; ?>';
    openModal('schedule-inspection-modal');
}

function confirmDelete(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar esta inspección?' : 'Delete this inspection?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, eliminar' : 'Yes, delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/inspection_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_inspection">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
