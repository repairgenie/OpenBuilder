<!-- templates/change_orders.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Fetch cost codes for searchableSelect
$cost_codes = $pdo->query("SELECT id, code, name FROM cost_codes ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);

// Fetch change events for linking
$change_events = $pdo->query("SELECT id, title_en, title_es, status, estimated_cost FROM change_events ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch change orders with joined data
$change_orders = $pdo->query("
    SELECT co.*, 
           ce.title_en as event_title_en, 
           ce.title_es as event_title_es,
           cc.code as cost_code, 
           cc.name as cost_code_name
    FROM change_orders co 
    LEFT JOIN change_events ce ON co.event_id = ce.id 
    LEFT JOIN cost_codes cc ON co.cost_code_id = cc.id 
    ORDER BY co.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Summary cards
$summary = $pdo->query("
    SELECT status, COUNT(*) as count, SUM(amount) as total 
    FROM change_orders GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);
$totals = ['Draft'=>0,'Submitted'=>0,'Reviewed'=>0,'Approved'=>0,'Issued'=>0];
foreach ($summary as $s) { $totals[$s['status']] = $s['total'] ?? 0; }

function status_badge($status) {
    $map = [
        'Draft' => 'bg-slate-100 text-slate-600',
        'Submitted' => 'bg-primary bg-opacity-10 text-primary',
        'Reviewed' => 'bg-warning bg-opacity-10 text-warning',
        'Approved' => 'bg-success bg-opacity-10 text-success',
        'Issued' => 'bg-success text-white',
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Órdenes de Cambio' : 'Change Orders'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestión del ciclo de vida completo de órdenes de cambio.' : 'Full change order lifecycle management.'; ?></p>
    </div>
    <div class="flex gap-2">
        <button onclick="openModal('create-co-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? '+ Nueva Orden de Cambio' : '+ New Change Order'; ?>
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="card py-4 px-4">
        <div class="text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'Borrador' : 'Draft'; ?></div>
        <div class="text-2xl font-bold text-slate-600">$<?php echo number_format($totals['Draft'], 0); ?></div>
    </div>
    <div class="card py-4 px-4">
        <div class="text-xs font-bold text-primary uppercase"><?php echo $lang === 'es' ? 'Enviado' : 'Submitted'; ?></div>
        <div class="text-2xl font-bold text-primary">$<?php echo number_format($totals['Submitted'], 0); ?></div>
    </div>
    <div class="card py-4 px-4">
        <div class="text-xs font-bold text-warning uppercase"><?php echo $lang === 'es' ? 'Revisado' : 'Reviewed'; ?></div>
        <div class="text-2xl font-bold text-warning">$<?php echo number_format($totals['Reviewed'], 0); ?></div>
    </div>
    <div class="card py-4 px-4">
        <div class="text-xs font-bold text-success uppercase"><?php echo $lang === 'es' ? 'Aprobado' : 'Approved'; ?></div>
        <div class="text-2xl font-bold text-success">$<?php echo number_format($totals['Approved'] + $totals['Issued'], 0); ?></div>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'CO #' : 'CO #'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Tipo' : 'Type'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Monto' : 'Amount'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Evento de Cambio' : 'Change Event'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Código de Costo' : 'Cost Code'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-center"><?php echo $lang === 'es' ? 'Acciones' : 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($change_orders)): ?>
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin órdenes de cambio registradas.' : 'No change orders recorded.'; ?></td>
                </tr>
                <?php else: foreach ($change_orders as $co): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 font-mono font-bold text-black">CO-<?php echo str_pad($co['id'], 3, '0', STR_PAD_LEFT); ?></td>
                    <td class="py-4 px-4 text-xs"><span class="px-2 py-1 rounded bg-slate-100 text-slate-600 font-bold"><?php echo htmlspecialchars($co['type']); ?></span></td>
                    <td class="py-4 px-4 font-bold text-black">$<?php echo number_format($co['amount'], 2); ?></td>
                    <td class="py-4 px-4 text-xs">
                        <?php if ($co['event_id']): ?>
                        CE-<?php echo str_pad($co['event_id'], 3, '0', STR_PAD_LEFT); ?>
                        <span class="text-slate-400 block text-[10px]"><?php echo htmlspecialchars($lang === 'es' ? ($co['event_title_es'] ?: '-') : ($co['event_title_en'] ?: '-')); ?></span>
                        <?php else: ?>
                        <span class="text-slate-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-4 text-xs font-mono">
                        <?php if ($co['cost_code']): ?>
                        <?php echo htmlspecialchars($co['cost_code']); ?>
                        <span class="text-slate-400 block text-[10px]"><?php echo htmlspecialchars($co['cost_code_name'] ?? ''); ?></span>
                        <?php else: ?>
                        <span class="text-slate-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-4"><span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo status_badge($co['status']); ?>"><?php echo htmlspecialchars($co['status']); ?></span></td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick='editCO(<?php echo json_encode($co); ?>)' class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <?php if ($co['status'] === 'Approved'): ?>
                            <button onclick="commitToBudget(<?php echo $co['id']; ?>)" class="p-2 hover:bg-success hover:text-white rounded" title="<?php echo $lang === 'es' ? 'Comprometer a Presupuesto' : 'Commit to Budget'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </button>
                            <?php endif; ?>
                            <button onclick="confirmDelete(<?php echo $co['id']; ?>)" class="p-2 hover:bg-slate-100 rounded text-danger" title="<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>">
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

<!-- Create/Edit CO Modal -->
<div id="create-co-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="co-modal-title"><?php echo $lang === 'es' ? 'Nueva Orden de Cambio' : 'New Change Order'; ?></h3>
            <button onclick="closeModal('create-co-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/change_order_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="co-action" value="create_change_order">
            <input type="hidden" name="id" id="co-id">
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Tipo de CO' : 'CO Type'; ?> *</label>
                <select name="type" id="co-type" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary searchable-select">
                    <option value="NCC">NCC — <?php echo $lang === 'es' ? 'Cambio No Contemplado' : 'Not Contemplated Change'; ?></option>
                    <option value="CCD">CCD — <?php echo $lang === 'es' ? 'Cambio Contemplado' : 'Contemplated Change'; ?></option>
                    <option value="CO">CO — <?php echo $lang === 'es' ? 'Orden de Cambio' : 'Change Order'; ?></option>
                    <option value="CR">CR — <?php echo $lang === 'es' ? 'Solicitud de Cambio' : 'Change Request'; ?></option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Monto' : 'Amount'; ?> *</label>
                    <input type="number" step="0.01" name="amount" id="co-amount" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></label>
                    <select name="status" id="co-status" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="Draft"><?php echo $lang === 'es' ? 'Borrador' : 'Draft'; ?></option>
                        <option value="Submitted"><?php echo $lang === 'es' ? 'Enviado' : 'Submitted'; ?></option>
                        <option value="Reviewed"><?php echo $lang === 'es' ? 'Revisado' : 'Reviewed'; ?></option>
                        <option value="Approved"><?php echo $lang === 'es' ? 'Aprobado' : 'Approved'; ?></option>
                        <option value="Issued"><?php echo $lang === 'es' ? 'Emitido' : 'Issued'; ?></option>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Evento de Cambio Vinculado' : 'Linked Change Event'; ?></label>
                <select name="event_id" id="co-event_id" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary searchable-select">
                    <option value="">-- <?php echo $lang === 'es' ? 'Ninguno' : 'None'; ?> --</option>
                    <?php foreach ($change_events as $ce): ?>
                    <option value="<?php echo $ce['id']; ?>">CE-<?php echo str_pad($ce['id'], 3, '0', STR_PAD_LEFT); ?> — <?php echo htmlspecialchars($lang === 'es' ? ($ce['title_es'] ?: $ce['title_en']) : $ce['title_en']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Código de Costo' : 'Cost Code'; ?></label>
                <select name="cost_code_id" id="co-cost_code_id" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary searchable-select">
                    <option value="">-- <?php echo $lang === 'es' ? 'Seleccionar' : 'Select'; ?> --</option>
                    <?php foreach ($cost_codes as $cc): ?>
                    <option value="<?php echo $cc['id']; ?>"><?php echo htmlspecialchars($cc['code'] . ' - ' . $cc['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-co-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function editCO(co) {
    document.getElementById('co-action').value = 'update_change_order';
    document.getElementById('co-id').value = co.id;
    document.getElementById('co-type').value = co.type || 'NCC';
    document.getElementById('co-amount').value = co.amount || '';
    document.getElementById('co-status').value = co.status || 'Draft';
    document.getElementById('co-event_id').value = co.event_id || '';
    document.getElementById('co-cost_code_id').value = co.cost_code_id || '';
    document.getElementById('co-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Orden de Cambio' : 'Edit Change Order'; ?>';
    openModal('create-co-modal');
}

function confirmDelete(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar esta orden de cambio?' : 'Delete this change order?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, eliminar' : 'Yes, delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/change_order_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_change_order">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function commitToBudget(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Comprometer al presupuesto?' : 'Commit this CO to the budget?'; ?>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, comprometer' : 'Yes, commit'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/change_order_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="commit_to_budget">
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