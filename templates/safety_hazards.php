<!-- templates/safety_hazards.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();

// User from session (PermissionHelper pattern)
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? 'Viewer';
$user_name = $_SESSION['user_name'] ?? 'Guest';

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Fetch crews and users for dropdowns
$crews = $pdo->query("SELECT id, name FROM crews ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$users = $pdo->query("SELECT id, name FROM users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch safety hazards with joins
$hazards = $pdo->query("
    SELECT h.*, c.name as crew_name, u.name as reporter_name
    FROM safety_hazards h
    LEFT JOIN crews c ON h.assigned_crew_id = c.id
    LEFT JOIN users u ON h.reported_by = u.id
    ORDER BY h.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

function severity_badge($sev) {
    $map = [
        'Critical' => 'bg-danger text-white',
        'High' => 'bg-warning text-white',
        'Medium' => 'bg-primary bg-opacity-10 text-primary',
        'Low' => 'bg-slate-100 text-slate-600',
    ];
    return $map[$sev] ?? 'bg-slate-100 text-slate-600';
}

function status_badge($status) {
    $map = [
        'Open' => 'bg-danger text-white',
        'In Progress' => 'bg-warning text-white',
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Registro de Peligros' : 'Safety Hazard Log'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Reporte y seguimiento de riesgos de seguridad.' : 'Safety hazard reporting and tracking.'; ?></p>
    </div>
    <div class="flex gap-2">
        <button onclick="openModal('create-hazard-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Reportar Peligro' : 'Report Hazard'; ?>
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
    <?php foreach ($hazards as $h): ?>
    <div class="card overflow-hidden">
        <?php if (!empty($h['image_path']) && file_exists(__DIR__ . '/../' . $h['image_path'])): ?>
        <div class="h-32 bg-slate-100 overflow-hidden">
            <img src="<?php echo htmlspecialchars($h['image_path']); ?>" alt="Hazard" class="w-full h-full object-cover">
        </div>
        <?php else: ?>
        <div class="h-32 bg-slate-100 flex items-center justify-center">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" class="text-slate-400"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <?php endif; ?>
        <div class="p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo severity_badge($h['severity']); ?>"><?php echo htmlspecialchars($h['severity']); ?></span>
                <span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo status_badge($h['status']); ?>"><?php echo htmlspecialchars($h['status']); ?></span>
            </div>
            <h4 class="font-bold text-black mb-1 truncate"><?php echo htmlspecialchars($h['description']); ?></h4>
            <p class="text-xs text-slate-500 mb-2">📍 <?php echo htmlspecialchars($h['location'] ?? '-'); ?></p>
            <div class="flex items-center justify-between text-xs text-slate-400">
                <span><?php echo htmlspecialchars($h['reporter_name'] ?? '-'); ?></span>
                <span><?php echo date('M d', strtotime($h['reported_date'])); ?></span>
            </div>
            <?php if (!empty($h['corrective_action'])): ?>
            <div class="mt-2 p-2 bg-success bg-opacity-10 rounded text-xs">
                <strong><?php echo $lang === 'es' ? 'Acción Correctiva:' : 'Corrective Action:'; ?></strong>
                <?php echo htmlspecialchars(substr($h['corrective_action'], 0, 80)); ?>...
            </div>
            <?php endif; ?>
            <?php if (!empty($h['crew_name'])): ?>
            <div class="mt-1 text-xs text-slate-400">
                👷 <?php echo $lang === 'es' ? 'Cuadrilla:' : 'Crew:'; ?> <?php echo htmlspecialchars($h['crew_name']); ?>
            </div>
            <?php endif; ?>
        </div>
        <div class="px-4 pb-4 flex gap-2">
            <button onclick='editHazard(<?php echo htmlspecialchars(json_encode($h)); ?>)' class="flex-1 rounded border border-stroke py-2 text-xs font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?></button>
            <button onclick="confirmDelete(<?php echo $h['id']; ?>)" class="rounded border border-danger py-2 px-3 text-xs font-medium text-danger hover:bg-danger hover:bg-opacity-10">🗑</button>
            <?php if ($h['status'] !== 'Closed'): ?>
            <button onclick="closeHazard(<?php echo $h['id']; ?>)" class="flex-1 rounded bg-success py-2 text-xs font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Cerrar' : 'Close'; ?></button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($hazards)): ?>
    <div class="col-span-3 py-12 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'No hay peligros reportados.' : 'No hazards reported.'; ?></div>
    <?php endif; ?>
</div>

<!-- Create/Edit Hazard Modal -->
<div id="create-hazard-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-danger px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="hazard-modal-title"><?php echo $lang === 'es' ? 'Reportar Peligro' : 'Report Hazard'; ?></h3>
            <button onclick="closeModal('create-hazard-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/safety_handler.php" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="action" id="hazard-action" value="create_hazard">
            <input type="hidden" name="id" id="hazard-id">
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <input type="hidden" name="reported_by" id="hazard-reported-by" value="<?php echo htmlspecialchars($user_id); ?>">
            <input type="hidden" name="latitude" id="hazard-latitude">
            <input type="hidden" name="longitude" id="hazard-longitude">
            <input type="hidden" name="existing_image_path" id="hazard-existing-image">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Descripción' : 'Description'; ?> *</label>
                <textarea name="description" id="hazard-description" rows="3" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Ubicación' : 'Location'; ?> *</label>
                    <input type="text" name="location" id="hazard-location" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Severidad' : 'Severity'; ?> *</label>
                    <select name="severity" id="hazard-severity" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="Low"><?php echo $lang === 'es' ? 'Baja' : 'Low'; ?></option>
                        <option value="Medium"><?php echo $lang === 'es' ? 'Media' : 'Medium'; ?></option>
                        <option value="High"><?php echo $lang === 'es' ? 'Alta' : 'High'; ?></option>
                        <option value="Critical"><?php echo $lang === 'es' ? 'Crítica' : 'Critical'; ?></option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha de Reporte' : 'Report Date'; ?> *</label>
                    <input type="date" name="reported_date" id="hazard-date" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Cuadrilla Asignada' : 'Assigned Crew'; ?></label>
                    <select name="assigned_crew_id" id="hazard-crew" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="">-- <?php echo $lang === 'es' ? 'Ninguna' : 'None'; ?> --</option>
                        <?php foreach ($crews as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Acción Correctiva' : 'Corrective Action'; ?></label>
                <textarea name="corrective_action" id="hazard-corrective" rows="2" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary"></textarea>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></label>
                <select name="status" id="hazard-status" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                    <option value="Open"><?php echo $lang === 'es' ? 'Abierto' : 'Open'; ?></option>
                    <option value="In Progress"><?php echo $lang === 'es' ? 'En Progreso' : 'In Progress'; ?></option>
                    <option value="Closed"><?php echo $lang === 'es' ? 'Cerrado' : 'Closed'; ?></option>
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Foto' : 'Photo'; ?></label>
                <input type="file" name="hazard_image" accept="image/*" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                <p class="mt-1 text-xs text-slate-400"><?php echo $lang === 'es' ? 'JPG, PNG o WebP máx 5MB' : 'JPG, PNG or WebP max 5MB'; ?></p>
            </div>

            <div id="gps-display" class="hidden text-xs text-slate-500 px-2">
                📍 <span id="gps-coords"></span>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-hazard-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-danger py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

// Auto-capture GPS on page load
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(pos) {
        document.getElementById('hazard-latitude').value = pos.coords.latitude;
        document.getElementById('hazard-longitude').value = pos.coords.longitude;
        document.getElementById('gps-coords').textContent = pos.coords.latitude.toFixed(6) + ', ' + pos.coords.longitude.toFixed(6);
        document.getElementById('gps-display').classList.remove('hidden');
    }, function(err) {
        console.log('Geolocation error: ' + err.message);
    });
}

function editHazard(h) {
    document.getElementById('hazard-action').value = 'update_hazard';
    document.getElementById('hazard-id').value = h.id;
    document.getElementById('hazard-description').value = h.description || '';
    document.getElementById('hazard-location').value = h.location || '';
    document.getElementById('hazard-severity').value = h.severity || 'Medium';
    document.getElementById('hazard-date').value = h.reported_date || '';
    document.getElementById('hazard-crew').value = h.assigned_crew_id || '';
    document.getElementById('hazard-corrective').value = h.corrective_action || '';
    document.getElementById('hazard-status').value = h.status || 'Open';
    document.getElementById('hazard-latitude').value = h.latitude || '';
    document.getElementById('hazard-longitude').value = h.longitude || '';
    document.getElementById('hazard-reported-by').value = h.reported_by || '';
    document.getElementById('hazard-existing-image').value = h.image_path || '';
    document.getElementById('hazard-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Peligro' : 'Edit Hazard'; ?>';
    if (h.latitude && h.longitude) {
        document.getElementById('gps-coords').textContent = parseFloat(h.latitude).toFixed(6) + ', ' + parseFloat(h.longitude).toFixed(6);
        document.getElementById('gps-display').classList.remove('hidden');
    }
    openModal('create-hazard-modal');
}

function closeHazard(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Cerrar este peligro?' : 'Close this hazard?'; ?>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, cerrar' : 'Yes, close'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/safety_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="close_hazard">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmDelete(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar este registro?' : 'Delete this hazard record?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, eliminar' : 'Yes, delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/safety_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_hazard">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>