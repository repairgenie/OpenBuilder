<!-- templates/roles.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();
$stmt = $pdo->query("SELECT * FROM system_roles ORDER BY id ASC");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all available permissions for display
$all_permissions = [
    'view_dashboard', 'view_rfis', 'create_rfis', 'edit_rfis', 'delete_rfis', 'close_rfis',
    'view_daily_logs', 'create_daily_logs', 'edit_daily_logs', 'delete_daily_logs',
    'view_budget', 'edit_budget', 'view_reports', 'export_data',
    'view_users', 'create_users', 'edit_users', 'delete_users', 'reset_user_passwords',
    'view_roles', 'create_roles', 'edit_roles', 'delete_roles',
    'view_api_keys', 'create_api_keys', 'delete_api_keys',
    'view_audit_logs', 'manage_settings', 'manage_submittals', 'manage_inspections',
    'manage_commitments', 'manage_punch_list', 'manage_bim', 'manage_drawings',
    'view_financials', 'edit_financials', 'manage_vendors', 'view_docs', 'delete_docs'
];

// Group permissions by category for display
$permission_groups = [
    'en' => [
        'Dashboard' => ['view_dashboard'],
        'RFIs' => ['view_rfis', 'create_rfis', 'edit_rfis', 'delete_rfis', 'close_rfis'],
        'Daily Logs' => ['view_daily_logs', 'create_daily_logs', 'edit_daily_logs', 'delete_daily_logs'],
        'Budget' => ['view_budget', 'edit_budget'],
        'Reports' => ['view_reports', 'export_data'],
        'Users' => ['view_users', 'create_users', 'edit_users', 'delete_users', 'reset_user_passwords'],
        'Roles' => ['view_roles', 'create_roles', 'edit_roles', 'delete_roles'],
        'API Keys' => ['view_api_keys', 'create_api_keys', 'delete_api_keys'],
        'Audit & Settings' => ['view_audit_logs', 'manage_settings'],
        'Submittals & Inspections' => ['manage_submittals', 'manage_inspections'],
        'Commitments & Punch List' => ['manage_commitments', 'manage_punch_list'],
        'BIM & Drawings' => ['manage_bim', 'manage_drawings'],
        'Financials' => ['view_financials', 'edit_financials', 'manage_vendors'],
        'Documents' => ['view_docs', 'delete_docs'],
    ],
    'es' => [
        'Panel' => ['view_dashboard'],
        'RFIs' => ['view_rfis', 'create_rfis', 'edit_rfis', 'delete_rfis', 'close_rfis'],
        'Diarios' => ['view_daily_logs', 'create_daily_logs', 'edit_daily_logs', 'delete_daily_logs'],
        'Presupuesto' => ['view_budget', 'edit_budget'],
        'Reportes' => ['view_reports', 'export_data'],
        'Usuarios' => ['view_users', 'create_users', 'edit_users', 'delete_users', 'reset_user_passwords'],
        'Roles' => ['view_roles', 'create_roles', 'edit_roles', 'delete_roles'],
        'Claves API' => ['view_api_keys', 'create_api_keys', 'delete_api_keys'],
        'Auditoría y Ajustes' => ['view_audit_logs', 'manage_settings'],
        'Submittals e Inspecciones' => ['manage_submittals', 'manage_inspections'],
        'Compromisos y Punch List' => ['manage_commitments', 'manage_punch_list'],
        'BIM y Planos' => ['manage_bim', 'manage_drawings'],
        'Finanzas' => ['view_financials', 'edit_financials', 'manage_vendors'],
        'Documentos' => ['view_docs', 'delete_docs'],
    ],
];

$labels = [
    'en' => [
        'title'           => 'Role & Permission Matrix',
        'role'            => 'Role',
        'description'     => 'Description',
        'permissions'     => 'Permissions',
        'actions'         => 'Actions',
        'edit'            => 'Edit',
        'delete'          => 'Delete',
        'save'            => 'Save Changes',
        'cancel'          => 'Cancel',
        'confirm_del'     => 'Delete this role? This cannot be undone.',
        'no_roles'        => 'No roles found.',
        'add_role'        => 'Add Role',
        'system_role'     => 'System',
        'editable_role'   => 'Custom',
        'permission_count'=> 'permissions',
        'create_role'     => 'Create Role',
        'role_name'       => 'Role Name',
        'role_name_ph'    => 'e.g. Field Supervisor',
        'role_desc_en'    => 'Description (English)',
        'role_desc_es'    => 'Description (Spanish)',
        'perms'           => 'Permissions',
    ],
    'es' => [
        'title'           => 'Matriz de Roles y Permisos',
        'role'            => 'Rol',
        'description'     => 'Descripción',
        'permissions'     => 'Permisos',
        'actions'         => 'Acciones',
        'edit'            => 'Editar',
        'delete'          => 'Eliminar',
        'save'            => 'Guardar',
        'cancel'          => 'Cancelar',
        'confirm_del'     => '¿Eliminar este rol? No se puede deshacer.',
        'no_roles'        => 'No se encontraron roles.',
        'add_role'        => 'Añadir Rol',
        'system_role'     => 'Sistema',
        'editable_role'   => 'Personalizado',
        'permission_count'=> 'permisos',
        'create_role'     => 'Crear Rol',
        'role_name'       => 'Nombre del Rol',
        'role_name_ph'    => 'ej. Supervisor de Campo',
        'role_desc_en'    => 'Descripción (Inglés)',
        'role_desc_es'    => 'Descripción (Español)',
        'perms'           => 'Permisos',
    ],
];
$l = $labels[$lang] ?? $labels['en'];
$perm_groups = $permission_groups[$lang] ?? $permission_groups['en'];
?>
<style>
.modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 40; }
.modal-backdrop.open { display: flex; align-items: center; justify-content: center; }
.modal { background: white; border-radius: 12px; padding: 24px; width: 100%; max-width: 600px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); max-height: 90vh; overflow-y: auto; }
.permission-badge { display: inline-flex; border-radius: 4px; padding: 2px 8px; font-size: 11px; font-weight: 500; margin: 1px; }
.permission-badge.granted { background: rgba(34,197,94,0.1); color: #16a34a; }
.permission-badge.denied { background: rgba(241,245,249,1); color: #94a3b8; text-decoration: line-through; }
.perm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 4px; }
</style>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $l['title']; ?></h2>
    </div>
    <button onclick="openModal('create')" class="inline-flex items-center gap-2 rounded-md bg-primary py-2.5 px-6 text-center font-medium text-white hover:bg-opacity-90 shadow-md transition-all">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
        <?php echo $l['add_role']; ?>
    </button>
</div>

<!-- Role Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php if (empty($roles)): ?>
    <div class="col-span-full card py-12 text-center text-slate-400"><?php echo $l['no_roles']; ?></div>
    <?php endif; ?>
    <?php foreach ($roles as $role): 
        $perms = json_decode($role['permissions'], true) ?: [];
        $desc_key = $lang === 'es' ? 'description_es' : 'description_en';
    ?>
    <div class="card p-6 flex flex-col gap-4">
        <div class="flex items-start justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h3 class="text-lg font-bold text-black"><?php echo htmlspecialchars($role['role_name']); ?></h3>
                    <?php if ($role['is_system']): ?>
                    <span class="inline-flex rounded-full py-0.5 px-2 text-xs font-bold bg-slate-100 text-slate-500"><?php echo $l['system_role']; ?></span>
                    <?php else: ?>
                    <span class="inline-flex rounded-full py-0.5 px-2 text-xs font-bold bg-primary bg-opacity-10 text-primary"><?php echo $l['editable_role']; ?></span>
                    <?php endif; ?>
                </div>
                <p class="text-sm text-slate-500"><?php echo htmlspecialchars($role[$desc_key] ?? ''); ?></p>
            </div>
        </div>
        
        <div>
            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2"><?php echo $l['permissions']; ?> (<?php echo count($perms); ?>)</p>
            <div class="flex flex-wrap gap-1">
                <?php foreach ($all_permissions as $p): 
                    $granted = in_array($p, $perms);
                ?>
                    <span class="permission-badge <?php echo $granted ? 'granted' : 'denied'; ?>">
                        <?php echo htmlspecialchars($p); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!$role['is_system']): ?>
        <div class="flex gap-2 pt-2 border-t border-stroke">
            <button onclick='openModal("edit", <?php echo json_encode($role); ?>)' class="flex-1 rounded bg-primary py-2 text-sm font-bold text-white hover:bg-opacity-90"><?php echo $l['edit']; ?></button>
            <form method="POST" action="index.php?page=roles_handler&lang=<?php echo $lang; ?>" onsubmit="event.preventDefault(); Swal.fire({title:'<?php echo addslashes($l['confirm_del']); ?>',icon:'warning',showCancelButton:true,confirmButtonText:'<?php echo addslashes($l['delete']); ?>',cancelButtonText:'<?php echo addslashes($lang==='es'?'Cancelar':'Cancel'); ?>'}).then(r=>r.isConfirmed&&this.submit());" style="display:inline; flex:1;">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="action" value="delete_role">
                <input type="hidden" name="id" value="<?php echo $role['id']; ?>">
                <button type="submit" class="w-full rounded border border-danger py-2 text-sm font-bold text-danger hover:bg-danger hover:text-white"><?php echo $l['delete']; ?></button>
            </form>
        </div>
        <?php else: ?>
        <div class="flex items-center justify-center pt-2 border-t border-stroke">
            <span class="text-xs text-slate-400 italic"><?php echo $l['system_role']; ?></span>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal Backdrop -->
<div id="modal-backdrop" class="modal-backdrop" onclick="if(event.target===this)closeModal()">
    <div class="modal" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-6">
            <h3 id="modal-title" class="text-xl font-bold text-black"></h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="role-form" method="POST" action="index.php?page=roles_handler&lang=<?php echo $lang; ?>" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="action" id="form-action" value="create_role">
            <input type="hidden" name="id" id="form-id" value="">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['role_name']; ?> *</label>
                <input type="text" name="role_name" id="form-role_name" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary" placeholder="<?php echo $l['role_name_ph']; ?>">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['role_desc_en']; ?></label>
                <input type="text" name="description_en" id="form-description_en" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['role_desc_es']; ?></label>
                <input type="text" name="description_es" id="form-description_es" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
            </div>

            <div>
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $l['perms']; ?></label>
                <div class="space-y-4">
                    <?php foreach ($perm_groups as $group_name => $group_perms): ?>
                    <div class="border border-stroke rounded-lg p-4">
                        <p class="text-sm font-semibold text-slate-600 mb-3"><?php echo htmlspecialchars($group_name); ?></p>
                        <div class="grid grid-cols-2 gap-2">
                            <?php foreach ($group_perms as $p): ?>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox" name="permissions[]" value="<?php echo htmlspecialchars($p); ?>" id="perm-<?php echo htmlspecialchars($p); ?>" class="w-4 h-4 rounded border-stroke text-primary focus:ring-primary">
                                <span class="text-sm text-slate-600 group-hover:text-black transition-colors"><?php echo htmlspecialchars($p); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-bold text-white hover:bg-opacity-90"><?php echo $l['save']; ?></button>
                <button type="button" onclick="closeModal()" class="flex-1 rounded border border-stroke py-3 font-bold text-slate-600 hover:bg-slate-50"><?php echo $l['cancel']; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(mode, data) {
    document.getElementById('modal-backdrop').classList.add('open');
    document.getElementById('form-action').value = mode === 'create' ? 'create_role' : 'update_role';
    document.getElementById('modal-title').textContent = mode === 'create' ? '<?php echo $l['create_role']; ?>' : '<?php echo $l['edit']; ?>';
    
    // Clear all permission checkboxes
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);

    if (mode === 'create') {
        document.getElementById('form-id').value = '';
        document.getElementById('form-role_name').value = '';
        document.getElementById('form-description_en').value = '';
        document.getElementById('form-description_es').value = '';
    } else {
        document.getElementById('form-id').value = data.id || '';
        document.getElementById('form-role_name').value = data.role_name || '';
        document.getElementById('form-description_en').value = data.description_en || '';
        document.getElementById('form-description_es').value = data.description_es || '';
        // Check the permissions
        try {
            var perms = JSON.parse(data.permissions) || [];
            perms.forEach(function(p) {
                var cb = document.getElementById('perm-' + p);
                if (cb) cb.checked = true;
            });
        } catch(e) {}
    }
}
function closeModal() {
    document.getElementById('modal-backdrop').classList.remove('open');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>