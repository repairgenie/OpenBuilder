<!-- templates/prime_contracts.php -->
<?php
// Database and security_helper are loaded via app.php in index.php

$lang = $_GET['lang'] ?? 'en';
$user = getCurrentUser();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$contracts = $pdo->query("SELECT * FROM prime_contracts ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

function status_badge($status) {
    $map = [
        'Active' => 'bg-success bg-opacity-10 text-success',
        'Completed' => 'bg-primary bg-opacity-10 text-primary',
        'Terminated' => 'bg-danger bg-opacity-10 text-danger',
        'Pending' => 'bg-warning bg-opacity-10 text-warning',
    ];
    return $map[$status] ?? 'bg-slate-100 text-slate-600';
}
?>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Contratos Prime' : 'Prime Contracts'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestión de contratos principales y valores de órdenes de cambio.' : 'Track prime contracts and change order values.'; ?></p>
    </div>
    <div class="flex gap-2">
        <button onclick="openModal('create-contract-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Nuevo Contrato' : 'New Contract'; ?>
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <?php
    $total_value = array_sum(array_column($contracts, 'contract_value'));
    $total_co = array_sum(array_column($contracts, 'change_order_value'));
    $active_count = count(array_filter($contracts, fn($c) => $c['status'] === 'Active'));
    ?>
    <div class="card py-4 px-4">
        <div class="text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'Total Contratos' : 'Total Contracts'; ?></div>
        <div class="text-2xl font-bold text-black">$<?php echo number_format($total_value, 0); ?></div>
    </div>
    <div class="card py-4 px-4">
        <div class="text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'Valor CO' : 'Change Order Value'; ?></div>
        <div class="text-2xl font-bold text-warning">$<?php echo number_format($total_co, 0); ?></div>
    </div>
    <div class="card py-4 px-4">
        <div class="text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'Contratos Activos' : 'Active Contracts'; ?></div>
        <div class="text-2xl font-bold text-success"><?php echo $active_count; ?></div>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Número de Contrato' : 'Contract #'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Contratista' : 'Contractor'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Valor Original' : 'Original Value'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Valor CO' : 'CO Value'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Valor Revisado' : 'Revised Value'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-center"><?php echo $lang === 'es' ? 'Acciones' : 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contracts)): ?>
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin contratos registrados.' : 'No contracts recorded.'; ?></td>
                </tr>
                <?php else: foreach ($contracts as $c): ?>
                <?php $revised = $c['contract_value'] + $c['change_order_value']; ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 font-mono font-bold text-black"><?php echo htmlspecialchars($c['contract_number']); ?></td>
                    <td class="py-4 px-4 font-bold text-black"><?php echo htmlspecialchars($c['contractor_name']); ?></td>
                    <td class="py-4 px-4 font-bold text-black">$<?php echo number_format($c['contract_value'], 2); ?></td>
                    <td class="py-4 px-4 font-bold text-warning">$<?php echo number_format($c['change_order_value'], 2); ?></td>
                    <td class="py-4 px-4 font-bold text-success">$<?php echo number_format($revised, 2); ?></td>
                    <td class="py-4 px-4"><span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo status_badge($c['status']); ?>"><?php echo htmlspecialchars($c['status']); ?></span></td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick='editContract(<?php echo json_encode($c); ?>)' class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button onclick="confirmDelete(<?php echo $c['id']; ?>)" class="p-2 hover:bg-slate-100 rounded text-danger" title="<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>">
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

<!-- Create/Edit Contract Modal -->
<div id="create-contract-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="contract-modal-title"><?php echo $lang === 'es' ? 'Nuevo Contrato' : 'New Contract'; ?></h3>
            <button onclick="closeModal('create-contract-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/prime_contract_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="contract-action" value="create_contract">
            <input type="hidden" name="id" id="contract-id">
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Número de Contrato' : 'Contract Number'; ?> *</label>
                    <input type="text" name="contract_number" id="contract-number" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Contratista' : 'Contractor Name'; ?> *</label>
                    <input type="text" name="contractor_name" id="contract-name" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Valor del Contrato' : 'Contract Value'; ?> *</label>
                <input type="number" step="0.01" name="contract_value" id="contract-value" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha de Inicio' : 'Start Date'; ?> *</label>
                    <input type="date" name="start_date" id="contract-start" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha de Fin' : 'End Date'; ?> *</label>
                    <input type="date" name="end_date" id="contract-end" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></label>
                    <select name="status" id="contract-status" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="Pending"><?php echo $lang === 'es' ? 'Pendiente' : 'Pending'; ?></option>
                        <option value="Active"><?php echo $lang === 'es' ? 'Activo' : 'Active'; ?></option>
                        <option value="Completed"><?php echo $lang === 'es' ? 'Completado' : 'Completed'; ?></option>
                        <option value="Terminated"><?php echo $lang === 'es' ? 'Terminado' : 'Terminated'; ?></option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Frecuencia de Facturación' : 'Billing Frequency'; ?></label>
                    <select name="billing_frequency" id="contract-billing" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="Monthly"><?php echo $lang === 'es' ? 'Mensual' : 'Monthly'; ?></option>
                        <option value="Bi-Weekly"><?php echo $lang === 'es' ? 'Quincenal' : 'Bi-Weekly'; ?></option>
                        <option value="Weekly"><?php echo $lang === 'es' ? 'Semanal' : 'Weekly'; ?></option>
                        <option value="Progress"><?php echo $lang === 'es' ? 'Por Progreso' : 'Progress'; ?></option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Valor de Órdenes de Cambio' : 'Change Order Value'; ?></label>
                    <input type="number" step="0.01" name="change_order_value" id="contract-co-value" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Porcentaje de Retención' : 'Retention Percent'; ?></label>
                    <input type="number" step="0.1" name="retention_percent" id="contract-retention" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Notas' : 'Notes'; ?></label>
                <textarea name="notes" id="contract-notes" rows="2" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary"></textarea>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-contract-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function editContract(c) {
    document.getElementById('contract-action').value = 'update_contract';
    document.getElementById('contract-id').value = c.id;
    document.getElementById('contract-number').value = c.contract_number || '';
    document.getElementById('contract-name').value = c.contractor_name || '';
    document.getElementById('contract-value').value = c.contract_value || '';
    document.getElementById('contract-start').value = c.start_date || '';
    document.getElementById('contract-end').value = c.end_date || '';
    document.getElementById('contract-status').value = c.status || 'Pending';
    document.getElementById('contract-billing').value = c.billing_frequency || 'Monthly';
    document.getElementById('contract-co-value').value = c.change_order_value || 0;
    document.getElementById('contract-retention').value = c.retention_percent || 0;
    document.getElementById('contract-notes').value = c.notes || '';
    document.getElementById('contract-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Contrato' : 'Edit Contract'; ?>';
    openModal('create-contract-modal');
}

function confirmDelete(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar este contrato?' : 'Delete this contract?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, eliminar' : 'Yes, delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/prime_contract_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_contract">
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