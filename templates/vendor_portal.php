<!-- templates/vendor_portal.php -->
<?php
require_once __DIR__ . '/../src/Database.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();

// Handle flash messages
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Fetch vendors
$vendors = $pdo->query("SELECT * FROM vendors ORDER BY company_name")->fetchAll(PDO::FETCH_ASSOC);
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Portal de Proveedores' : 'Vendor Portal'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestión de proveedores y sus datos de contacto.' : 'Manage vendors and their contact information.'; ?></p>
    </div>
    <button onclick="openModal('create-vendor-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
        <?php echo $lang === 'es' ? 'Nuevo Proveedor' : 'New Vendor'; ?>
    </button>
</div>

<!-- Vendors Table -->
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Empresa' : 'Company'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Contacto' : 'Contact'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600">Email</th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Giro' : 'Trade'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Calificación' : 'Rating'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-center"><?php echo $lang === 'es' ? 'Acciones' : 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($vendors)): ?>
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'No hay proveedores registrados.' : 'No vendors registered.'; ?></td>
                </tr>
                <?php else: foreach ($vendors as $v): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4">
                        <p class="font-bold text-black"><?php echo htmlspecialchars($v['company_name']); ?></p>
                    </td>
                    <td class="py-4 px-4 text-sm text-black"><?php echo htmlspecialchars($v['contact_name']); ?></td>
                    <td class="py-4 px-4 text-sm text-primary"><?php echo htmlspecialchars($v['email']); ?></td>
                    <td class="py-4 px-4 text-sm text-slate-600"><?php echo htmlspecialchars($lang === 'es' ? $v['trade_es'] : $v['trade_en']); ?></td>
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-1">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="<?php echo $i <= round($v['rating']) ? 'text-warning' : 'text-slate-300'; ?>">★</span>
                            <?php endfor; ?>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick='editVendor(<?php echo json_encode($v); ?>)' class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button onclick="confirmDelete('vendor', <?php echo $v['id']; ?>)" class="p-2 hover:bg-slate-100 rounded text-danger" title="<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>">
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

<!-- Create/Edit Vendor Modal -->
<div id="create-vendor-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="vendor-modal-title"><?php echo $lang === 'es' ? 'Nuevo Proveedor' : 'New Vendor'; ?></h3>
            <button onclick="closeModal('create-vendor-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/vendor_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="vendor-action" value="create_vendor">
            <input type="hidden" name="id" id="vendor-id">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre de Empresa' : 'Company Name'; ?> *</label>
                <input type="text" name="company_name" id="vendor-company_name" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre de Contacto' : 'Contact Name'; ?> *</label>
                <input type="text" name="contact_name" id="vendor-contact_name" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black">Email *</label>
                <input type="email" name="email" id="vendor-email" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Giro (EN)' : 'Trade (EN)'; ?></label>
                    <input type="text" name="trade_en" id="vendor-trade_en" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Giro (ES)' : 'Trade (ES)'; ?></label>
                    <input type="text" name="trade_es" id="vendor-trade_es" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Calificación (1-5)' : 'Rating (1-5)'; ?></label>
                <input type="number" name="rating" id="vendor-rating" min="0" max="5" step="0.5" value="0" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-vendor-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function editVendor(vendor) {
    document.getElementById('vendor-action').value = 'update_vendor';
    document.getElementById('vendor-id').value = vendor.id;
    document.getElementById('vendor-company_name').value = vendor.company_name || '';
    document.getElementById('vendor-contact_name').value = vendor.contact_name || '';
    document.getElementById('vendor-email').value = vendor.email || '';
    document.getElementById('vendor-trade_en').value = vendor.trade_en || '';
    document.getElementById('vendor-trade_es').value = vendor.trade_es || '';
    document.getElementById('vendor-rating').value = vendor.rating || 0;
    document.getElementById('vendor-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Proveedor' : 'Edit Vendor'; ?>';
    openModal('create-vendor-modal');
}

function confirmDelete(module, id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar este registro?' : 'Delete this record?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, eliminar' : 'Yes, delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
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
</script>