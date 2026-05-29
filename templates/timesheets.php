<!-- templates/timesheets.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();

// Get current user from session (PermissionHelper pattern)
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? 'Viewer';
$user_name = $_SESSION['user_name'] ?? 'Guest';

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Fetch cost codes for searchableSelect
$cost_codes = $pdo->query("SELECT id, code, description FROM cost_codes ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

// Fetch timesheets — show pending for foreman review, approved for payroll
$can_approve = in_array($user_role, ['admin', 'payroll']);
if ($can_approve) {
    $timesheets = $pdo->query("
        SELECT t.*, cc.code as cost_code 
        FROM timesheets t 
        LEFT JOIN cost_codes cc ON t.cost_code_id = cc.id 
        ORDER BY t.date DESC, t.id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->prepare("
        SELECT t.*, cc.code as cost_code 
        FROM timesheets t 
        LEFT JOIN cost_codes cc ON t.cost_code_id = cc.id 
        WHERE t.created_by = ? 
        ORDER BY t.date DESC, t.id DESC
    ");
    $stmt->execute([$user_id]);
    $timesheets = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function status_badge($status) {
    $map = [
        'Draft' => 'bg-slate-100 text-slate-600',
        'Submitted' => 'bg-warning bg-opacity-10 text-warning',
        'Approved' => 'bg-success bg-opacity-10 text-success',
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Partes de Horas' : 'Timesheets'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Registro de horas de trabajadores y aprobación de enfermería.' : 'Track worker hours and foreman/payroll approval workflow.'; ?></p>
    </div>
    <div class="flex gap-2">
        <button onclick="openModal('create-timesheet-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Nuevo Parte' : 'New Timesheet'; ?>
        </button>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Trabajador' : 'Worker'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Oficio' : 'Trade'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Fecha' : 'Date'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Horas' : 'Hours'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Código de Costo' : 'Cost Code'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'GPS' : 'GPS'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-center"><?php echo $lang === 'es' ? 'Acciones' : 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($timesheets)): ?>
                <tr>
                    <td colspan="8" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin registros.' : 'No timesheets recorded.'; ?></td>
                </tr>
                <?php else: foreach ($timesheets as $ts): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 font-bold text-black"><?php echo htmlspecialchars($ts['worker_name']); ?></td>
                    <td class="py-4 px-4"><?php echo htmlspecialchars($lang === 'es' ? ($ts['trade_es'] ?? '') : ($ts['trade_en'] ?? '')); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($ts['date']); ?></td>
                    <td class="py-4 px-4 font-bold"><?php echo htmlspecialchars($ts['hours']); ?></td>
                    <td class="py-4 px-4 text-xs font-mono"><?php echo htmlspecialchars($ts['cost_code'] ?? '-'); ?></td>
                    <td class="py-4 px-4 text-xs text-slate-500"><?php echo $ts['latitude'] && $ts['longitude'] ? round((float)$ts['latitude'],6).', '.round((float)$ts['longitude'],6) : '-'; ?></td>
                    <td class="py-4 px-4"><span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo status_badge($ts['status']); ?>"><?php echo htmlspecialchars($ts['status']); ?></span></td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <?php if ($can_approve && $ts['status'] === 'Submitted'): ?>
                            <button onclick="approveTimesheet(<?php echo (int)$ts['id']; ?>)" class="p-2 hover:bg-success hover:text-white rounded" title="<?php echo $lang === 'es' ? 'Aprobar' : 'Approve'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </button>
                            <?php endif; ?>
                            <button onclick='editTimesheet(<?php echo json_encode($ts); ?>)' class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button onclick="confirmDelete(<?php echo (int)$ts['id']; ?>)" class="p-2 hover:bg-slate-100 rounded text-danger" title="<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>">
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

<!-- Create/Edit Timesheet Modal -->
<div id="create-timesheet-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="timesheet-modal-title"><?php echo $lang === 'es' ? 'Nuevo Parte de Horas' : 'New Timesheet'; ?></h3>
            <button onclick="closeModal('create-timesheet-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/timesheet_handler.php" class="p-6 space-y-4" id="timesheet-form-new">
            <input type="hidden" name="action" id="timesheet-action" value="create_timesheet">
            <input type="hidden" name="id" id="timesheet-id">
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <input type="hidden" name="created_by" value="<?php echo (int)$user_id; ?>">
            <input type="hidden" name="latitude" id="timesheet-latitude">
            <input type="hidden" name="longitude" id="timesheet-longitude">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre del Trabajador' : 'Worker Name'; ?> *</label>
                <input type="text" name="worker_name" id="timesheet-worker_name" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Oficio (EN)' : 'Trade (EN)'; ?> *</label>
                    <input type="text" name="trade_en" id="timesheet-trade_en" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Oficio (ES)' : 'Trade (ES)'; ?> *</label>
                    <input type="text" name="trade_es" id="timesheet-trade_es" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha' : 'Date'; ?> *</label>
                    <input type="date" name="date" id="timesheet-date" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Horas' : 'Hours'; ?> *</label>
                    <input type="number" step="0.5" min="0" max="24" name="hours" id="timesheet-hours" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Código de Costo' : 'Cost Code'; ?></label>
                <select name="cost_code_id" id="timesheet-cost_code" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary searchable-select">
                    <option value="">-- <?php echo $lang === 'es' ? 'Seleccionar' : 'Select'; ?> --</option>
                    <?php foreach ($cost_codes as $cc): ?>
                    <option value="<?php echo (int)$cc['id']; ?>"><?php echo htmlspecialchars($cc['code'] . ' - ' . $cc['description']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'GPS Stamp' : 'GPS Stamp'; ?></label>
                <input type="text" id="timesheet-gps-display" disabled placeholder="<?php echo $lang === 'es' ? 'Obteniendo ubicación...' : 'Fetching location...'; ?>" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 bg-slate-50 text-slate-500">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-timesheet-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

// GPS capture using browser Geolocation API
function captureGPS() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            document.getElementById('timesheet-latitude').value = pos.coords.latitude;
            document.getElementById('timesheet-longitude').value = pos.coords.longitude;
            document.getElementById('timesheet-gps-display').value = pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6);
        }, function() {
            document.getElementById('timesheet-gps-display').placeholder = '<?php echo $lang === 'es' ? 'GPS no disponible' : 'GPS not available'; ?>';
        });
    }
}

// Auto-capture GPS when modal opens
document.getElementById('create-timesheet-modal').addEventListener('transitionend', function() {
    if (!document.getElementById('create-timesheet-modal').classList.contains('hidden')) {
        captureGPS();
    }
});

function editTimesheet(ts) {
    document.getElementById('timesheet-action').value = 'update_timesheet';
    document.getElementById('timesheet-id').value = ts.id;
    document.getElementById('timesheet-worker_name').value = ts.worker_name || '';
    document.getElementById('timesheet-trade_en').value = ts.trade_en || '';
    document.getElementById('timesheet-trade_es').value = ts.trade_es || '';
    document.getElementById('timesheet-date').value = ts.date || '';
    document.getElementById('timesheet-hours').value = ts.hours || '';
    document.getElementById('timesheet-cost_code').value = ts.cost_code_id || '';
    document.getElementById('timesheet-latitude').value = ts.latitude || '';
    document.getElementById('timesheet-longitude').value = ts.longitude || '';
    if (ts.latitude && ts.longitude) {
        document.getElementById('timesheet-gps-display').value = parseFloat(ts.latitude).toFixed(6) + ', ' + parseFloat(ts.longitude).toFixed(6);
    } else {
        document.getElementById('timesheet-gps-display').value = '';
    }
    document.getElementById('timesheet-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Parte' : 'Edit Timesheet'; ?>';
    openModal('create-timesheet-modal');
}

function confirmDelete(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar este registro?' : 'Delete this timesheet?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, eliminar' : 'Yes, delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/timesheet_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_timesheet">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function approveTimesheet(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Aprobar este parte?' : 'Approve this timesheet?'; ?>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, aprobar' : 'Yes, approve'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/timesheet_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="approve_timesheet">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Initialize searchableSelect on cost code dropdown if available
document.querySelectorAll('.searchable-select').forEach(function(el) {
    if (typeof searchableSelect !== 'undefined') {
        searchableSelect.init(el);
    }
});
</script>
