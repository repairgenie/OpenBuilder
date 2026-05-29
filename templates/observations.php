<!-- templates/observations.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/PermissionHelper.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();
$user = PermissionHelper::getCurrentUser();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

$projects = $pdo->query("SELECT id, name FROM projects ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT id, name FROM users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$observations = $pdo->query("
    SELECT o.*, u.name as observer_name, a.name as assignee_name, p.name as project_name
    FROM observations o
    LEFT JOIN users u ON o.observer_id = u.id
    LEFT JOIN users a ON o.assigned_to = a.id
    LEFT JOIN projects p ON o.project_id = p.id
    ORDER BY o.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

function category_badge($cat) {
    $map = [
        'Safety' => 'bg-danger bg-opacity-10 text-danger',
        'Quality' => 'bg-primary bg-opacity-10 text-primary',
        'Progress' => 'bg-success bg-opacity-10 text-success',
        'Issue' => 'bg-warning bg-opacity-10 text-warning',
    ];
    return $map[$cat] ?? 'bg-slate-100 text-slate-600';
}

function status_badge($status) {
    $map = [
        'Open' => 'bg-warning text-white',
        'In Progress' => 'bg-primary text-white',
        'Closed' => 'bg-success text-white',
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Registro de Observaciones' : 'Observations Log'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Seguimiento diario de observaciones de campo.' : 'Daily field observation tracking.'; ?></p>
    </div>
    <div class="flex gap-2">
        <button onclick="openModal('create-observation-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Nueva Observación' : 'New Observation'; ?>
        </button>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Fecha' : 'Date'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Categoría' : 'Category'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Observación' : 'Observation'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Prioridad' : 'Priority'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Asignado A' : 'Assigned To'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-center"><?php echo $lang === 'es' ? 'Acciones' : 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($observations)): ?>
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin observaciones registradas.' : 'No observations recorded.'; ?></td>
                </tr>
                <?php else: foreach ($observations as $obs): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 text-sm text-slate-600"><?php echo date('M d, Y', strtotime($obs['created_at'])); ?></td>
                    <td class="py-4 px-4"><span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo category_badge($obs['category']); ?>"><?php echo htmlspecialchars($obs['category']); ?></span></td>
                    <td class="py-4 px-4 text-sm max-w-xs truncate"><?php echo htmlspecialchars($obs['observation_text']); ?></td>
                    <td class="py-4 px-4 text-sm font-bold <?php echo $obs['priority'] === 'High' ? 'text-danger' : ($obs['priority'] === 'Medium' ? 'text-warning' : 'text-slate-600'); ?>"><?php echo htmlspecialchars($obs['priority']); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($obs['assignee_name'] ?? '-'); ?></td>
                    <td class="py-4 px-4"><span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo status_badge($obs['status']); ?>"><?php echo htmlspecialchars($obs['status']); ?></span></td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick='editObservation(<?php echo json_encode($obs); ?>)' class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button onclick="confirmDelete(<?php echo $obs['id']; ?>)" class="p-2 hover:bg-slate-100 rounded text-danger" title="<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>">
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

<!-- Create/Edit Observation Modal -->
<div id="create-observation-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="obs-modal-title"><?php echo $lang === 'es' ? 'Nueva Observación' : 'New Observation'; ?></h3>
            <button onclick="closeModal('create-observation-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/observations_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="obs-action" value="create_observation">
            <input type="hidden" name="id" id="obs-id">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="observer_id" value="<?php echo $user['id']; ?>">
            <input type="hidden" name="latitude" id="obs-latitude">
            <input type="hidden" name="longitude" id="obs-longitude">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Proyecto' : 'Project'; ?> *</label>
                <select name="project_id" id="obs-project_id" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                    <option value="">-- <?php echo $lang === 'es' ? 'Seleccionar' : 'Select'; ?> --</option>
                    <?php foreach ($projects as $p): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Categoría' : 'Category'; ?> *</label>
                <select name="category" id="obs-category" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                    <option value="Safety"><?php echo $lang === 'es' ? 'Seguridad' : 'Safety'; ?></option>
                    <option value="Quality"><?php echo $lang === 'es' ? 'Calidad' : 'Quality'; ?></option>
                    <option value="Progress"><?php echo $lang === 'es' ? 'Progreso' : 'Progress'; ?></option>
                    <option value="Issue"><?php echo $lang === 'es' ? 'Problema' : 'Issue'; ?></option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Descripción de Observación' : 'Observation Description'; ?> *</label>
                <textarea name="observation_text" id="obs-text" rows="4" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Asignado A' : 'Assigned To'; ?></label>
                    <select name="assigned_to" id="obs-assigned_to" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="">-- <?php echo $lang === 'es' ? 'Nadie' : 'None'; ?> --</option>
                        <?php foreach ($users as $u): ?>
                        <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Prioridad' : 'Priority'; ?></label>
                    <select name="priority" id="obs-priority" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="Low"><?php echo $lang === 'es' ? 'Baja' : 'Low'; ?></option>
                        <option value="Medium" selected><?php echo $lang === 'es' ? 'Media' : 'Medium'; ?></option>
                        <option value="High"><?php echo $lang === 'es' ? 'Alta' : 'High'; ?></option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></label>
                    <select name="status" id="obs-status" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="Open"><?php echo $lang === 'es' ? 'Abierto' : 'Open'; ?></option>
                        <option value="In Progress"><?php echo $lang === 'es' ? 'En Progreso' : 'In Progress'; ?></option>
                        <option value="Verified"><?php echo $lang === 'es' ? 'Verificado' : 'Verified'; ?></option>
                        <option value="Closed"><?php echo $lang === 'es' ? 'Cerrado' : 'Closed'; ?></option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'GPS' : 'GPS'; ?></label>
                    <input type="text" id="obs-gps-display" disabled placeholder="<?php echo $lang === 'es' ? 'Obteniendo...' : 'Fetching...'; ?>" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 bg-slate-50 text-slate-500">
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-observation-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
        document.getElementById('obs-latitude').value = pos.coords.latitude;
        document.getElementById('obs-longitude').value = pos.coords.longitude;
        document.getElementById('obs-gps-display').value = pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6);
    });
}

function editObservation(obs) {
    document.getElementById('obs-action').value = 'update_observation';
    document.getElementById('obs-id').value = obs.id;
    document.getElementById('obs-project_id').value = obs.project_id || '';
    document.getElementById('obs-category').value = obs.category || 'Safety';
    document.getElementById('obs-text').value = obs.observation_text || '';
    document.getElementById('obs-assigned_to').value = obs.assigned_to || '';
    document.getElementById('obs-priority').value = obs.priority || 'Medium';
    document.getElementById('obs-status').value = obs.status || 'Open';
    document.getElementById('obs-latitude').value = obs.latitude || '';
    document.getElementById('obs-longitude').value = obs.longitude || '';
    if (obs.latitude && obs.longitude) {
        document.getElementById('obs-gps-display').value = parseFloat(obs.latitude).toFixed(6) + ', ' + parseFloat(obs.longitude).toFixed(6);
    }
    document.getElementById('obs-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Observación' : 'Edit Observation'; ?>';
    openModal('create-observation-modal');
}

function confirmDelete(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar esta observación?' : 'Delete this observation?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, eliminar' : 'Yes, delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/observations_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_observation">
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