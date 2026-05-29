<!-- templates/punch_list_v2.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();
$user = getCurrentUser();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$users = $pdo->query("SELECT id, name FROM users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$items = $pdo->query("
    SELECT p.*, u.name as assignee_name, c.name as creator_name
    FROM punch_list_items p
    LEFT JOIN users u ON p.assigned_to = u.id
    LEFT JOIN users c ON p.created_by = c.id
    ORDER BY p.status ASC, p.priority DESC, p.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

function priority_badge($p) {
    $map = ['High'=>'text-danger font-bold','Medium'=>'text-warning','Low'=>'text-slate-500'];
    return $map[$p] ?? 'text-slate-500';
}
function status_badge($s) {
    $map = ['Open'=>'bg-danger text-white','In Progress'=>'bg-warning text-white','Verified'=>'bg-primary text-white','Closed'=>'bg-success text-white'];
    return $map[$s] ?? 'bg-slate-100 text-slate-600';
}
?>
<!-- Flash Messages -->
<?php if ($flash_success): ?>
<div id="toast-success" class="fixed top-4 right-4 z-50 flex items-center gap-3 bg-success text-white px-6 py-4 rounded-lg shadow-xl">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
    <span class="font-medium"><?php echo htmlspecialchars($flash_success); ?></span>
    <button onclick="document.getElementById('toast-success').remove()" class="ml-2 hover:opacity-80">✕</button>
</div>
<script>setTimeout(() => document.getElementById('toast-success')?.remove(), 4000);</script>
<?php endif; ?>

<?php if ($flash_error): ?>
<div id="toast-error" class="fixed top-4 right-4 z-50 flex items-center gap-3 bg-danger text-white px-6 py-4 rounded-lg shadow-xl">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
    <span class="font-medium"><?php echo htmlspecialchars($flash_error); ?></span>
    <button onclick="document.getElementById('toast-error').remove()" class="ml-2 hover:opacity-80">✕</button>
</div>
<script>setTimeout(() => document.getElementById('toast-error')?.remove(), 4000);</script>
<?php endif; ?>

<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Lista de Punch' : 'Punch List'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Seguimiento de defectos y elementos.' : 'Defect and punch item tracking.'; ?></p>
    </div>
    <div class="flex gap-2 flex-wrap">
        <button onclick="openModal('create-punch-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Agregar Item' : 'Add Item'; ?>
        </button>
        <button onclick="batchSelect()" class="rounded-md bg-white border border-stroke py-2 px-4 font-medium text-black hover:bg-slate-50">
            <?php echo $lang === 'es' ? 'Asignar en Lote' : 'Batch Assign'; ?>
        </button>
        <button onclick="batchClose()" class="rounded-md bg-white border border-stroke py-2 px-4 font-medium text-black hover:bg-slate-50">
            <?php echo $lang === 'es' ? 'Cerrar en Lote' : 'Batch Close'; ?>
        </button>
        <a href="?page=export_punch_csv&lang=<?php echo $lang; ?>" class="rounded-md bg-white border border-stroke py-2 px-4 font-medium text-black hover:bg-slate-50">
            <?php echo $lang === 'es' ? 'Exportar CSV' : 'Export CSV'; ?>
        </a>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 w-8">
                        <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-stroke text-primary">
                    </th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Descripción' : 'Description'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Ubicación' : 'Location'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Asignado A' : 'Assigned To'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Prioridad' : 'Priority'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Fecha Límite' : 'Due Date'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-center"><?php echo $lang === 'es' ? 'Acciones' : 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody id="punch-table-body">
                <?php if (empty($items)): ?>
                <tr>
                    <td colspan="8" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin items de punch list.' : 'No punch list items.'; ?></td>
                </tr>
                <?php else: foreach ($items as $item): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4"><input type="checkbox" class="punch-checkbox h-4 w-4 rounded border-stroke text-primary" value="<?php echo $item['id']; ?>"></td>
                    <td class="py-4 px-4 text-sm font-bold text-black"><?php echo htmlspecialchars($item['description']); ?></td>
                    <td class="py-4 px-4 text-sm text-slate-500"><?php echo htmlspecialchars($item['location'] ?? '-'); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($item['assignee_name'] ?? '-'); ?></td>
                    <td class="py-4 px-4 text-sm <?php echo priority_badge($item['priority']); ?>"><?php echo htmlspecialchars($item['priority']); ?></td>
                    <td class="py-4 px-4 text-sm text-slate-500"><?php echo $item['due_date'] ?? '-'; ?></td>
                    <td class="py-4 px-4"><span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo status_badge($item['status']); ?>"><?php echo htmlspecialchars($item['status']); ?></span></td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick='editItem(<?php echo json_encode($item); ?>)' class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <?php if ($item['status'] !== 'Closed'): ?>
                            <button onclick="markVerified(<?php echo $item['id']; ?>)" class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Verificar' : 'Verify'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Batch Action Bar -->
<div id="batch-bar" class="fixed bottom-4 left-1/2 -translate-x-1/2 bg-primary text-white px-6 py-3 rounded-lg shadow-2xl flex items-center gap-4 hidden z-50">
    <span id="batch-count" class="font-bold">0 <?php echo $lang === 'es' ? 'seleccionados' : 'selected'; ?></span>
    <button onclick="openModal('batch-assign-modal')" class="rounded bg-white text-primary py-1 px-3 text-sm font-bold hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Asignar' : 'Assign'; ?></button>
    <button onclick="batchCloseSelected()" class="rounded bg-success py-1 px-3 text-sm font-bold hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Cerrar' : 'Close'; ?></button>
    <button onclick="clearBatch()" class="text-white hover:text-slate-200">✕</button>
</div>

<!-- Create/Edit Punch Item Modal -->
<div id="create-punch-modal" class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="punch-modal-title"><?php echo $lang === 'es' ? 'Agregar Punch Item' : 'Add Punch Item'; ?></h3>
            <button onclick="closeModal('create-punch-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/punch_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="punch-action" value="create_punch">
            <input type="hidden" name="id" id="punch-id">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="created_by" value="<?php echo $user['id']; ?>">
            <input type="hidden" name="latitude" id="punch-latitude">
            <input type="hidden" name="longitude" id="punch-longitude">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Descripción' : 'Description'; ?> *</label>
                <textarea name="description" id="punch-description" rows="3" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Ubicación' : 'Location'; ?></label>
                    <input type="text" name="location" id="punch-location" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha Límite' : 'Due Date'; ?></label>
                    <input type="date" name="due_date" id="punch-due_date" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Asignado A' : 'Assigned To'; ?></label>
                    <select name="assigned_to" id="punch-assigned" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="">-- <?php echo $lang === 'es' ? 'Nadie' : 'None'; ?> --</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Prioridad' : 'Priority'; ?></label>
                    <select name="priority" id="punch-priority" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="Low"><?php echo $lang === 'es' ? 'Baja' : 'Low'; ?></option>
                        <option value="Medium" selected><?php echo $lang === 'es' ? 'Media' : 'Medium'; ?></option>
                        <option value="High"><?php echo $lang === 'es' ? 'Alta' : 'High'; ?></option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-punch-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Batch Assign Modal -->
<div id="batch-assign-modal" class="fixed inset-0 z-50 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg"><?php echo $lang === 'es' ? 'Asignar en Lote' : 'Batch Assign'; ?></h3>
            <button onclick="closeModal('batch-assign-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/punch_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" value="batch_assign">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="item_ids" id="batch-item-ids">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Asignado A' : 'Assign To'; ?> *</label>
                <select name="assigned_to" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                    <option value="">-- <?php echo $lang === 'es' ? 'Seleccionar' : 'Select'; ?> --</option>
                    <?php foreach ($users as $u): ?>
                    <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('batch-assign-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Asignar' : 'Assign'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

// GPS auto-capture
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
        document.getElementById('punch-latitude').value = pos.coords.latitude;
        document.getElementById('punch-longitude').value = pos.coords.longitude;
    });
}

function editItem(item) {
    document.getElementById('punch-action').value = 'update_punch';
    document.getElementById('punch-id').value = item.id;
    document.getElementById('punch-description').value = item.description || '';
    document.getElementById('punch-location').value = item.location || '';
    document.getElementById('punch-due_date').value = item.due_date || '';
    document.getElementById('punch-assigned').value = item.assigned_to || '';
    document.getElementById('punch-priority').value = item.priority || 'Medium';
    document.getElementById('punch-latitude').value = item.latitude || '';
    document.getElementById('punch-longitude').value = item.longitude || '';
    document.getElementById('punch-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Punch Item' : 'Edit Punch Item'; ?>';
    openModal('create-punch-modal');
}

function markVerified(id) {
    const form_el = document.createElement('form');
    form_el.method = 'POST';
    form_el.action = 'templates/punch_handler.php';
    form_el.innerHTML = `
        <input type="hidden" name="action" value="verify_punch">
        <input type="hidden" name="id" value="${id}">
        <input type="hidden" name="lang" value="<?php echo $lang; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    `;
    document.body.appendChild(form_el);
    form_el.submit();
}

// Batch operations
let selected_ids = [];
const batchBar = document.getElementById('batch-bar');
const countLabel = document.getElementById('batch-count');

document.getElementById('select-all')?.addEventListener('change', (e) => {
    document.querySelectorAll('.punch-checkbox').forEach(cb => cb.checked = e.target.checked);
    updateBatchBar();
});

document.querySelectorAll('.punch-checkbox').forEach(cb => cb.addEventListener('change', updateBatchBar));

function updateBatchBar() {
    selected_ids = Array.from(document.querySelectorAll('.punch-checkbox:checked')).map(cb => cb.value);
    if (selected_ids.length > 0) {
        batchBar.classList.remove('hidden');
        countLabel.textContent = selected_ids.length + ' <?php echo $lang === 'es' ? 'seleccionados' : 'selected'; ?>';
    } else {
        batchBar.classList.add('hidden');
    }
}

function batchSelect() {
    document.querySelectorAll('.punch-checkbox').forEach(cb => cb.checked = true);
    updateBatchBar();
    openModal('batch-assign-modal');
}

function batchClose() {
    document.querySelectorAll('.punch-checkbox').forEach(cb => cb.checked = true);
    updateBatchBar();
    batchCloseSelected();
}

function batchCloseSelected() {
    if (selected_ids.length === 0) return;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'templates/punch_handler.php';
    form.innerHTML = `
        <input type="hidden" name="action" value="batch_close">
        <input type="hidden" name="item_ids" value="${selected_ids.join(',')}">
        <input type="hidden" name="lang" value="<?php echo $lang; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    `;
    document.body.appendChild(form);
    form.submit();
}

function clearBatch() {
    document.querySelectorAll('.punch-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('select-all').checked = false;
    updateBatchBar();
}
</script>