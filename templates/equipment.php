<!-- templates/equipment.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/PermissionHelper.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();
$user = PermissionHelper::getCurrentUser();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$equipment = $pdo->query("
    SELECT e.*, p.name as project_name, c.name as crew_name
    FROM equipment e
    LEFT JOIN projects p ON e.assigned_project = p.id
    LEFT JOIN crews c ON e.assigned_crew_id = c.id
    ORDER BY e.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$projects = $pdo->query("SELECT id, name FROM projects ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$crews = $pdo->query("SELECT id, name FROM crews ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

function status_badge($status) {
    $map = [
        'active' => 'bg-success bg-opacity-10 text-success',
        'maintenance' => 'bg-warning bg-opacity-10 text-warning',
        'retired' => 'bg-slate-100 text-slate-600',
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Equipo y Flota' : 'Equipment Fleet'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestión de activos de equipo, servicio y asignaciones.' : 'Track equipment assets, service history, and assignments.'; ?></p>
    </div>
    <div class="flex gap-2">
        <button onclick="openModal('create-equipment-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Agregar Equipo' : 'Add Equipment'; ?>
        </button>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Etiqueta' : 'Asset Tag'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Nombre' : 'Name'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Tipo' : 'Type'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Proyecto' : 'Project'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Cuadrilla' : 'Crew'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Último Servicio' : 'Last Service'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-center"><?php echo $lang === 'es' ? 'Acciones' : 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($equipment)): ?>
                <tr>
                    <td colspan="8" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'No hay equipo registrado.' : 'No equipment registered.'; ?></td>
                </tr>
                <?php else: foreach ($equipment as $eq): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 font-mono font-bold text-black"><?php echo htmlspecialchars($eq['asset_tag']); ?></td>
                    <td class="py-4 px-4 font-bold text-black"><?php echo htmlspecialchars($eq['name']); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($eq['type']); ?></td>
                    <td class="py-4 px-4"><span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo status_badge($eq['status']); ?>"><?php echo htmlspecialchars($eq['status']); ?></span></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($eq['project_name'] ?? '-'); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($eq['crew_name'] ?? '-'); ?></td>
                    <td class="py-4 px-4 text-sm text-slate-500"><?php echo $eq['last_service_date'] ?? '-'; ?></td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick='editEquipment(<?php echo json_encode($eq); ?>)' class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button onclick="openServiceLog(<?php echo $eq['id']; ?>)" class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Log de Servicio' : 'Service Log'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"></path></svg>
                            </button>
                            <?php if ($eq['status'] !== 'retired'): ?>
                            <button onclick="retireEquipment(<?php echo $eq['id']; ?>)" class="p-2 hover:bg-slate-100 rounded text-warning" title="<?php echo $lang === 'es' ? 'Retirar' : 'Retire'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
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

<!-- Create/Edit Equipment Modal -->
<div id="create-equipment-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="equipment-modal-title"><?php echo $lang === 'es' ? 'Agregar Equipo' : 'Add Equipment'; ?></h3>
            <button onclick="closeModal('create-equipment-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/equipment_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="equipment-action" value="create_equipment">
            <input type="hidden" name="id" id="equipment-id">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Etiqueta de Activo' : 'Asset Tag'; ?> *</label>
                    <input type="text" name="asset_tag" id="equipment-asset_tag" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre' : 'Name'; ?> *</label>
                    <input type="text" name="name" id="equipment-name" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Tipo' : 'Type'; ?> *</label>
                    <input type="text" name="type" id="equipment-type" required placeholder="e.g. Excavator, Truck, Generator" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></label>
                    <select name="status" id="equipment-status" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="active"><?php echo $lang === 'es' ? 'Activo' : 'Active'; ?></option>
                        <option value="maintenance"><?php echo $lang === 'es' ? 'En Mantenimiento' : 'Maintenance'; ?></option>
                        <option value="retired"><?php echo $lang === 'es' ? 'Retirado' : 'Retired'; ?></option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Proyecto Asignado' : 'Assigned Project'; ?></label>
                    <select name="assigned_project" id="equipment-project" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="">-- <?php echo $lang === 'es' ? 'Ninguno' : 'None'; ?> --</option>
                        <?php foreach ($projects as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Cuadrilla Asignada' : 'Assigned Crew'; ?></label>
                    <select name="assigned_crew_id" id="equipment-crew" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="">-- <?php echo $lang === 'es' ? 'Ninguna' : 'None'; ?> --</option>
                        <?php foreach ($crews as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Último Servicio' : 'Last Service Date'; ?></label>
                    <input type="date" name="last_service_date" id="equipment-last_service" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Próximo Servicio' : 'Next Service Date'; ?></label>
                    <input type="date" name="next_service_date" id="equipment-next_service" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Notas' : 'Notes'; ?></label>
                <textarea name="notes" id="equipment-notes" rows="2" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary"></textarea>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-equipment-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Service Log Modal -->
<div id="service-log-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-warning px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg"><?php echo $lang === 'es' ? 'Registro de Servicio' : 'Service Log'; ?></h3>
            <button onclick="closeModal('service-log-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/equipment_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" value="add_service_log">
            <input type="hidden" name="equipment_id" id="service-equipment_id">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha de Servicio' : 'Service Date'; ?> *</label>
                <input type="date" name="service_date" id="service-date" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Descripción' : 'Description'; ?> *</label>
                <textarea name="description" id="service-description" rows="3" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary"></textarea>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Costo' : 'Cost'; ?></label>
                <input type="number" step="0.01" name="cost" id="service-cost" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Realizado Por' : 'Performed By'; ?></label>
                <input type="text" name="performed_by" id="service-performed_by" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('service-log-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-warning py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function editEquipment(eq) {
    document.getElementById('equipment-action').value = 'update_equipment';
    document.getElementById('equipment-id').value = eq.id;
    document.getElementById('equipment-asset_tag').value = eq.asset_tag || '';
    document.getElementById('equipment-name').value = eq.name || '';
    document.getElementById('equipment-type').value = eq.type || '';
    document.getElementById('equipment-status').value = eq.status || 'active';
    document.getElementById('equipment-project').value = eq.assigned_project || '';
    document.getElementById('equipment-crew').value = eq.assigned_crew_id || '';
    document.getElementById('equipment-last_service').value = eq.last_service_date || '';
    document.getElementById('equipment-next_service').value = eq.next_service_date || '';
    document.getElementById('equipment-notes').value = eq.notes || '';
    document.getElementById('equipment-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Equipo' : 'Edit Equipment'; ?>';
    openModal('create-equipment-modal');
}

function openServiceLog(id) {
    document.getElementById('service-equipment_id').value = id;
    openModal('service-log-modal');
}

function retireEquipment(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Retirar este equipo?' : 'Retire this equipment?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, retirar' : 'Yes, retire'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/equipment_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="retire_equipment">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>