<!-- templates/submittals.php -->
<?php
require_once __DIR__ . '/../src/Database.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$submittals = $pdo->query("SELECT s.*, u.name as assignee_name FROM submittals s LEFT JOIN users u ON s.ball_in_court = u.id ORDER BY s.id DESC")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT id, name FROM users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Helper for status badge color
function status_badge($status) {
    $map = [
        'Draft' => 'bg-slate-100 text-slate-600',
        'Submitted' => 'bg-primary bg-opacity-10 text-primary',
        'Under Review' => 'bg-warning bg-opacity-10 text-warning',
        'Approved' => 'bg-success bg-opacity-10 text-success',
        'Rejected' => 'bg-danger bg-opacity-10 text-danger',
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Submittals (Entregables)' : 'Submittals'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestión del flujo de aprobación de materiales y especificaciones.' : 'Management of material and specification approval workflows.'; ?></p>
    </div>
    <div class="flex gap-2">
        <div class="inline-flex rounded-md shadow-sm" role="group">
            <button onclick="filterSubmittals('items')" id="btn-items" class="px-4 py-2 text-sm font-medium text-primary bg-white border border-stroke rounded-l-md hover:bg-slate-50"><?php echo $lang === 'es' ? 'Items' : 'Items'; ?></button>
            <button onclick="filterSubmittals('packages')" id="btn-packages" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border-t border-b border-r border-stroke rounded-r-md hover:bg-slate-50"><?php echo $lang === 'es' ? 'Packages' : 'Packages'; ?></button>
        </div>
        <button onclick="openModal('create-submittal-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Nuevo Submittal' : 'New Submittal'; ?>
        </button>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600">ID</th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Título' : 'Title'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Sección' : 'Spec Section'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Responsable' : 'Ball In Court'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Fecha Límite' : 'Due Date'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-center"><?php echo $lang === 'es' ? 'Acciones' : 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody id="submittals-table-body">
                <?php if (empty($submittals)): ?>
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'No hay submittals registrados.' : 'No submittals registered.'; ?></td>
                </tr>
                <?php else: foreach ($submittals as $s): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors" data-type="item">
                    <td class="py-4 px-4 text-black font-mono text-sm">SUB-<?php echo str_pad($s['id'], 3, '0', STR_PAD_LEFT); ?></td>
                    <td class="py-4 px-4">
                        <p class="font-bold text-black"><?php echo htmlspecialchars($lang === 'es' ? $s['title_es'] : $s['title_en']); ?></p>
                    </td>
                    <td class="py-4 px-4 text-xs text-slate-500"><?php echo htmlspecialchars($s['spec_section']); ?></td>
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-2">
                            <?php if ($s['assignee_name']): ?>
                            <div class="h-6 w-6 rounded-full bg-primary flex items-center justify-center text-[10px] text-white font-bold"><?php echo strtoupper(substr($s['assignee_name'], 0, 2)); ?></div>
                            <span class="text-xs text-black"><?php echo htmlspecialchars($s['assignee_name']); ?></span>
                            <?php else: ?>
                            <span class="text-xs text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin asignar' : 'Unassigned'; ?></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-sm text-slate-600"><?php echo $s['due_date'] ? htmlspecialchars($s['due_date']) : '-'; ?></td>
                    <td class="py-4 px-4"><span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo status_badge($s['status']); ?>"><?php echo htmlspecialchars($s['status']); ?></span></td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick='editSubmittal(<?php echo json_encode($s); ?>)' class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button onclick="confirmDelete('submittal', <?php echo $s['id']; ?>)" class="p-2 hover:bg-slate-100 rounded text-danger" title="<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>">
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

<!-- Create/Edit Submittal Modal -->
<div id="create-submittal-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="submittal-modal-title"><?php echo $lang === 'es' ? 'Nuevo Submittal' : 'New Submittal'; ?></h3>
            <button onclick="closeModal('create-submittal-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/submittal_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="submittal-action" value="create_submittal">
            <input type="hidden" name="id" id="submittal-id">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Título (EN)' : 'Title (EN)'; ?> *</label>
                <input type="text" name="title_en" id="submittal-title_en" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Título (ES)' : 'Title (ES)'; ?></label>
                <input type="text" name="title_es" id="submittal-title_es" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Sección de Especificación' : 'Spec Section'; ?> *</label>
                <input type="text" name="spec_section" id="submittal-spec_section" required placeholder="e.g. 05-1200" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Responsable' : 'Ball In Court'; ?></label>
                    <select name="ball_in_court" id="submittal-ball_in_court" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="">-- <?php echo $lang === 'es' ? 'Seleccionar' : 'Select'; ?> --</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha Límite' : 'Due Date'; ?></label>
                    <input type="date" name="due_date" id="submittal-due_date" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></label>
                <select name="status" id="submittal-status" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                    <option value="Draft"><?php echo $lang === 'es' ? 'Borrador' : 'Draft'; ?></option>
                    <option value="Submitted"><?php echo $lang === 'es' ? 'Enviado' : 'Submitted'; ?></option>
                    <option value="Under Review"><?php echo $lang === 'es' ? 'En Revisión' : 'Under Review'; ?></option>
                    <option value="Approved"><?php echo $lang === 'es' ? 'Aprobado' : 'Approved'; ?></option>
                    <option value="Rejected"><?php echo $lang === 'es' ? 'Rechazado' : 'Rejected'; ?></option>
                </select>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-submittal-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function editSubmittal(s) {
    document.getElementById('submittal-action').value = 'update_submittal';
    document.getElementById('submittal-id').value = s.id;
    document.getElementById('submittal-title_en').value = s.title_en || '';
    document.getElementById('submittal-title_es').value = s.title_es || '';
    document.getElementById('submittal-spec_section').value = s.spec_section || '';
    document.getElementById('submittal-ball_in_court').value = s.ball_in_court || '';
    document.getElementById('submittal-due_date').value = s.due_date || '';
    document.getElementById('submittal-status').value = s.status || 'Draft';
    document.getElementById('submittal-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Submittal' : 'Edit Submittal'; ?>';
    openModal('create-submittal-modal');
}

function confirmDelete(module, id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar este registro?' : 'Delete this record?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/' + module + '_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_${module}">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function filterSubmittals(type) {
    const rows = document.querySelectorAll('#submittals-table-body tr');
    const btnItems = document.getElementById('btn-items');
    const btnPackages = document.getElementById('btn-packages');

    if (type === 'items') {
        btnItems.classList.replace('text-slate-600', 'text-primary');
        btnPackages.classList.replace('text-primary', 'text-slate-600');
    } else {
        btnPackages.classList.replace('text-slate-600', 'text-primary');
        btnItems.classList.replace('text-primary', 'text-slate-600');
    }
}
</script>