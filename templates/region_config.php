<!-- templates/region_config.php -->
<?php
require_once __DIR__ . '/../src/Database.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Fetch regions
$regions = $pdo->query("SELECT * FROM regions ORDER BY name_en")->fetchAll(PDO::FETCH_ASSOC);

// Fetch system settings
$settings = [];
$stmt = $pdo->query("SELECT key, value FROM system_settings");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Configuración Regional' : 'Region Configuration'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Definir regiones del proyecto y preferencias generales.' : 'Define project regions and general preferences.'; ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Regions Panel -->
    <div class="card bg-white">
        <div class="p-6 border-b border-stroke">
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-black"><?php echo $lang === 'es' ? 'Regiones del Proyecto' : 'Project Regions'; ?></h3>
                <button onclick="openModal('add-region-modal')" class="rounded-md bg-primary py-2 px-4 font-medium text-white text-sm hover:bg-opacity-90">
                    <?php echo $lang === 'es' ? 'Añadir Región' : 'Add Region'; ?>
                </button>
            </div>
        </div>
        <div class="p-6">
            <?php if (empty($regions)): ?>
            <p class="text-center text-slate-400 italic py-8"><?php echo $lang === 'es' ? 'No hay regiones configuradas.' : 'No regions configured.'; ?></p>
            <?php else: ?>
            <div class="space-y-3" id="regions-list">
                <?php foreach ($regions as $r): ?>
                <div class="flex items-center justify-between p-3 rounded-lg border border-stroke hover:bg-slate-50 transition-colors" data-region-id="<?php echo $r['id']; ?>">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded" style="background-color: <?php echo htmlspecialchars($r['color']); ?>;"></div>
                        <div>
                            <p class="font-medium text-black"><?php echo htmlspecialchars($r['name_en']); ?></p>
                            <?php if ($r['name_es']): ?>
                            <p class="text-xs text-slate-500"><?php echo htmlspecialchars($r['name_es']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="toggleRegion(<?php echo $r['id']; ?>)" class="p-2 rounded hover:bg-slate-200 <?php echo $r['is_active'] ? 'text-success' : 'text-slate-400'; ?>" title="<?php echo $r['is_active'] ? ($lang === 'es' ? 'Desactivar' : 'Deactivate') : ($lang === 'es' ? 'Activar' : 'Activate'); ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        </button>
                        <button onclick="editRegion(<?php echo htmlspecialchars(json_encode($r)); ?>)" class="p-2 rounded hover:bg-slate-200 text-slate-500" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                        </button>
                        <button onclick="deleteRegion(<?php echo $r['id']; ?>)" class="p-2 rounded hover:bg-slate-200 text-danger" title="<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- General Settings Panel -->
    <div class="card bg-white">
        <div class="p-6 border-b border-stroke">
            <h3 class="font-bold text-black"><?php echo $lang === 'es' ? 'Configuración General' : 'General Settings'; ?></h3>
        </div>
        <form method="POST" action="templates/config/region_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" value="save_settings">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre del Proyecto' : 'Project Name'; ?></label>
                <input type="text" name="project_name" value="<?php echo htmlspecialchars($settings['project_name'] ?? 'OpenBuilder HQ'); ?>" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Ubicación del Proyecto' : 'Project Location'; ?></label>
                <input type="text" name="project_location" value="<?php echo htmlspecialchars($settings['project_location'] ?? 'San Francisco, CA'); ?>" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Moneda' : 'Currency'; ?></label>
                    <select name="currency" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="USD" <?php echo ($settings['currency'] ?? 'USD') === 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                        <option value="EUR" <?php echo ($settings['currency'] ?? 'USD') === 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                        <option value="MXN" <?php echo ($settings['currency'] ?? 'USD') === 'MXN' ? 'selected' : ''; ?>>MXN - Mexican Peso</option>
                        <option value="CAD" <?php echo ($settings['currency'] ?? 'USD') === 'CAD' ? 'selected' : ''; ?>>CAD - Canadian Dollar</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Zona Horaria' : 'Timezone'; ?></label>
                    <select name="timezone" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="America/Los_Angeles" <?php echo ($settings['timezone'] ?? 'America/Los_Angeles') === 'America/Los_Angeles' ? 'selected' : ''; ?>>Pacific (PT)</option>
                        <option value="America/Denver" <?php echo ($settings['timezone'] ?? 'America/Los_Angeles') === 'America/Denver' ? 'selected' : ''; ?>>Mountain (MT)</option>
                        <option value="America/Chicago" <?php echo ($settings['timezone'] ?? 'America/Los_Angeles') === 'America/Chicago' ? 'selected' : ''; ?>>Central (CT)</option>
                        <option value="America/New_York" <?php echo ($settings['timezone'] ?? 'America/Los_Angeles') === 'America/New_York' ? 'selected' : ''; ?>>Eastern (ET)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Formato de Fecha' : 'Date Format'; ?></label>
                <select name="date_format" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                    <option value="Y-m-d" <?php echo ($settings['date_format'] ?? 'Y-m-d') === 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD</option>
                    <option value="m/d/Y" <?php echo ($settings['date_format'] ?? 'Y-m-d') === 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY</option>
                    <option value="d/m/Y" <?php echo ($settings['date_format'] ?? 'Y-m-d') === 'd/m/Y' ? 'selected' : ''; ?>>DD/MM/YYYY</option>
                </select>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90">
                    <?php echo $lang === 'es' ? 'Guardar Configuración' : 'Save Settings'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Region Modal -->
<div id="add-region-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="region-modal-title"><?php echo $lang === 'es' ? 'Añadir Región' : 'Add Region'; ?></h3>
            <button onclick="closeModal('add-region-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/config/region_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="region-action" value="add_region">
            <input type="hidden" name="id" id="region-id">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre (EN)' : 'Name (EN)'; ?> *</label>
                <input type="text" name="name_en" id="region-name_en" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre (ES)' : 'Name (ES)'; ?></label>
                <input type="text" name="name_es" id="region-name_es" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Color' : 'Color'; ?></label>
                <input type="color" name="color" id="region-color" value="#3B82F6" class="w-full h-12 rounded border-[1.5px] border-stroke cursor-pointer">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('add-region-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function editRegion(region) {
    document.getElementById('region-action').value = 'add_region';
    document.getElementById('region-id').value = region.id;
    document.getElementById('region-name_en').value = region.name_en || '';
    document.getElementById('region-name_es').value = region.name_es || '';
    document.getElementById('region-color').value = region.color || '#3B82F6';
    document.getElementById('region-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Región' : 'Edit Region'; ?>';
    openModal('add-region-modal');
}

function toggleRegion(id) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'templates/config/region_handler.php';
    form.innerHTML = `
        <input type="hidden" name="action" value="toggle_region">
        <input type="hidden" name="id" value="${id}">
        <input type="hidden" name="lang" value="<?php echo $lang; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    `;
    document.body.appendChild(form);
    form.submit();
}

function deleteRegion(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar esta región?' : 'Delete this region?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/config/region_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_region">
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