<!-- templates/users.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();
$stmt = $pdo->query("SELECT id, name, email, role, status, created_at FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$roles = ['Admin', 'Manager', 'Viewer', 'Subcontractor'];
$statuses = ['Active', 'Inactive', 'Pending'];

$labels = [
    'en' => [
        'title'      => 'User Management',
        'add'        => 'Add User',
        'edit'       => 'Edit User',
        'name'       => 'Full Name',
        'email'      => 'Email Address',
        'role'       => 'Role',
        'status'     => 'Status',
        'actions'    => 'Actions',
        'save'       => 'Save Changes',
        'cancel'     => 'Cancel',
        'delete'     => 'Delete',
        'reset_pw'   => 'Reset Password',
        'confirm_del'=> 'Delete this user? This cannot be undone.',
        'pw_reset'   => 'Password will be reset to: User123!',
        'no_users'   => 'No users found.',
        'admin'      => 'Admin',
        'manager'    => 'Manager',
        'viewer'     => 'Viewer',
        'sub'        => 'Subcontractor',
    ],
    'es' => [
        'title'      => 'Gestión de Usuarios',
        'add'        => 'Añadir Usuario',
        'edit'       => 'Editar Usuario',
        'name'       => 'Nombre Completo',
        'email'      => 'Correo Electrónico',
        'role'       => 'Rol',
        'status'     => 'Estado',
        'actions'    => 'Acciones',
        'save'       => 'Guardar',
        'cancel'     => 'Cancelar',
        'delete'     => 'Eliminar',
        'reset_pw'   => 'Restaurar Contraseña',
        'confirm_del'=> '¿Eliminar este usuario? No se puede deshacer.',
        'pw_reset'   => 'La contraseña se restaurará a: User123!',
        'no_users'   => 'No se encontraron usuarios.',
        'admin'      => 'Administrador',
        'manager'    => 'Gerente',
        'viewer'     => 'Visor',
        'sub'        => 'Subcontratista',
    ],
];
$l = $labels[$lang] ?? $labels['en'];
?>
<style>
.modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 40; }
.modal-backdrop.open { display: flex; align-items: center; justify-content: center; }
.modal { background: white; border-radius: 12px; padding: 24px; width: 100%; max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
</style>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $l['title']; ?></h2>
    </div>
    <button onclick="openModal('create')" class="inline-flex items-center gap-2 rounded-md bg-primary py-2.5 px-6 text-center font-medium text-white hover:bg-opacity-90 shadow-md transition-all">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v12M2 8h12"/></svg>
        <?php echo $l['add']; ?>
    </button>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['name']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['email']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['role']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['status']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-right"><?php echo $l['actions']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                <tr><td colspan="5" class="py-8 text-center text-slate-400"><?php echo $l['no_users']; ?></td></tr>
                <?php endif; ?>
                <?php foreach ($users as $u): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors" id="user-row-<?php echo $u['id']; ?>">
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($u['name']); ?>&background=3C50E0&color=fff" class="h-10 w-10 rounded-full" alt="">
                            <div>
                                <p class="text-sm font-bold text-black"><?php echo htmlspecialchars($u['name']); ?></p>
                                <p class="text-xs text-slate-400">ID #<?php echo $u['id']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-sm text-slate-600"><?php echo htmlspecialchars($u['email']); ?></td>
                    <td class="py-4 px-4">
                        <?php
                        $roleClass = [
                            'Admin' => 'bg-danger bg-opacity-10 text-danger',
                            'Manager' => 'bg-primary bg-opacity-10 text-primary',
                            'Viewer' => 'bg-slate-200 text-slate-600',
                            'Subcontractor' => 'bg-warning bg-opacity-10 text-warning',
                        ][$u['role']] ?? 'bg-slate-200 text-slate-600';
                        ?>
                        <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold <?php echo $roleClass; ?>">
                            <?php echo htmlspecialchars($u['role']); ?>
                        </span>
                    </td>
                    <td class="py-4 px-4">
                        <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold <?php echo $u['status'] === 'Active' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning'; ?>">
                            <?php echo htmlspecialchars($u['status']); ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button onclick='openModal("edit", <?php echo json_encode($u); ?>)' class="text-primary hover:underline text-sm font-medium"><?php echo $l['edit']; ?></button>
                            <form method="POST" action="index.php?page=user_handler&lang=<?php echo $lang; ?>" onsubmit="event.preventDefault(); Swal.fire({title:'<?php echo addslashes($l['confirm_del']); ?>',icon:'warning',showCancelButton:true,confirmButtonText:'<?php echo addslashes($l['delete']); ?>',cancelButtonText:'<?php echo addslashes($lang==='es'?'Cancelar':'Cancel'); ?>'}).then(r=>r.isConfirmed&&this.submit());" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                <button type="submit" class="text-danger hover:underline text-sm font-medium"><?php echo $l['delete']; ?></button>
                            </form>
                            <form method="POST" action="index.php?page=user_handler&lang=<?php echo $lang; ?>" onsubmit="event.preventDefault(); Swal.fire({title:'<?php echo addslashes($l['pw_reset']); ?>',icon:'warning',showCancelButton:true,confirmButtonText:'<?php echo addslashes($l['reset_pw']); ?>',cancelButtonText:'<?php echo addslashes($lang==='es'?'Cancelar':'Cancel'); ?>'}).then(r=>r.isConfirmed&&this.submit());" style="display:inline;">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="reset_password">
                                <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                <button type="submit" class="text-warning hover:underline text-sm font-medium"><?php echo $l['reset_pw']; ?></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
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
        <form id="user-form" method="POST" action="index.php?page=user_handler&lang=<?php echo $lang; ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" id="form-action" value="create_user">
            <input type="hidden" name="id" id="form-id" value="">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['name']; ?> *</label>
                <input type="text" name="name" id="form-name" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['email']; ?> *</label>
                <input type="email" name="email" id="form-email" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['role']; ?></label>
                    <select name="role" id="form-role" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                        <?php foreach ($roles as $r): ?>
                        <option value="<?php echo $r; ?>"><?php echo $r; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['status']; ?></label>
                    <select name="status" id="form-status" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                        <?php foreach ($statuses as $s): ?>
                        <option value="<?php echo $s; ?>"><?php echo $s; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-bold text-white hover:bg-opacity-90"><?php echo $l['save']; ?></button>
                <button type="button" onclick="closeModal()" class="flex-1 rounded border border-stroke py-3 font-bold text-slate-600 hover:bg-slate-50"><?php echo $l['cancel']; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(mode, data) {
    document.getElementById('modal-backdrop').classList.add('open');
    document.getElementById('form-action').value = mode === 'create' ? 'create_user' : 'update_user';
    document.getElementById('modal-title').textContent = mode === 'create' ? '<?php echo $l['add']; ?>' : '<?php echo $l['edit']; ?>';
    if (mode === 'create') {
        document.getElementById('form-id').value = '';
        document.getElementById('form-name').value = '';
        document.getElementById('form-email').value = '';
        document.getElementById('form-role').value = 'Viewer';
        document.getElementById('form-status').value = 'Active';
    } else {
        document.getElementById('form-id').value = data.id || '';
        document.getElementById('form-name').value = data.name || '';
        document.getElementById('form-email').value = data.email || '';
        document.getElementById('form-role').value = data.role || 'Viewer';
        document.getElementById('form-status').value = data.status || 'Active';
    }
}
function closeModal() {
    document.getElementById('modal-backdrop').classList.remove('open');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>
