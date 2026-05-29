<!-- templates/api_keys.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();
$stmt = $pdo->query("SELECT ak.id, ak.name, ak.api_key, ak.last_used, ak.created_at, u.name as user_name, u.email as user_email
    FROM api_keys ak
    JOIN users u ON ak.user_id = u.id
    ORDER BY ak.created_at DESC");
$api_keys = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [
    'en' => [
        'title'       => 'API Keys',
        'add'         => 'Create API Key',
        'name'        => 'Key Name',
        'user'        => 'Owner',
        'key'         => 'API Key',
        'last_used'   => 'Last Used',
        'created'     => 'Created',
        'actions'     => 'Actions',
        'save'        => 'Save',
        'cancel'      => 'Cancel',
        'delete'      => 'Delete',
        'confirm_del' => 'Delete this API key? This cannot be undone.',
        'no_keys'     => 'No API keys found.',
        'never'       => 'Never',
        'copy_key'    => 'Copy Key',
        'copied'      => 'Copied!',
        'new_key'     => 'New API Key',
        'regenerate'  => 'Regenerate',
    ],
    'es' => [
        'title'       => 'Claves API',
        'add'         => 'Crear Clave API',
        'name'        => 'Nombre de Clave',
        'user'        => 'Propietario',
        'key'         => 'Clave API',
        'last_used'   => 'Último Uso',
        'created'     => 'Creado',
        'actions'     => 'Acciones',
        'save'        => 'Guardar',
        'cancel'      => 'Cancelar',
        'delete'      => 'Eliminar',
        'confirm_del' => '¿Eliminar esta clave API? No se puede deshacer.',
        'no_keys'     => 'No se encontraron claves API.',
        'never'       => 'Nunca',
        'copy_key'    => 'Copiar Clave',
        'copied'      => '¡Copiado!',
        'new_key'     => 'Nueva Clave API',
        'regenerate'  => 'Regenerar',
    ],
];
$l = $labels[$lang] ?? $labels['en'];
?>
<style>
.modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 40; }
.modal-backdrop.open { display: flex; align-items: center; justify-content: center; }
.modal { background: white; border-radius: 12px; padding: 24px; width: 100%; max-width: 520px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
.key-display { font-family: monospace; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; word-break: break-all; font-size: 13px; color: #334155; }
</style>

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $l['title']; ?></h2>
    </div>
    <button onclick="openModal()" class="inline-flex items-center gap-2 rounded-md bg-primary py-2.5 px-6 text-center font-medium text-white hover:bg-opacity-90 shadow-md transition-all">
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
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['user']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['key']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['last_used']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['created']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-right"><?php echo $l['actions']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($api_keys)): ?>
                <tr><td colspan="6" class="py-8 text-center text-slate-400"><?php echo $l['no_keys']; ?></td></tr>
                <?php endif; ?>
                <?php foreach ($api_keys as $key): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-primary bg-opacity-10 flex items-center justify-center">
                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" class="text-primary"><path d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-black"><?php echo htmlspecialchars($key['name']); ?></p>
                                <p class="text-xs text-slate-400">ID #<?php echo $key['id']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <p class="text-sm font-medium text-black"><?php echo htmlspecialchars($key['user_name']); ?></p>
                        <p class="text-xs text-slate-400"><?php echo htmlspecialchars($key['user_email']); ?></p>
                    </td>
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-2">
                            <code class="text-xs bg-slate-100 rounded px-2 py-1 font-mono text-slate-600 max-w-[120px] truncate"><?php echo htmlspecialchars(substr($key['api_key'], 0, 16)); ?>...</code>
                            <button onclick="copyKey('<?php echo htmlspecialchars($key['api_key']); ?>')" class="text-primary hover:text-primary/80 text-xs font-medium" title="<?php echo $l['copy_key']; ?>">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            </button>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-sm text-slate-600">
                        <?php echo $key['last_used'] ? date('Y-m-d H:i', strtotime($key['last_used'])) : '<span class="text-slate-400">'.$l['never'].'</span>'; ?>
                    </td>
                    <td class="py-4 px-4 text-sm text-slate-600">
                        <?php echo date('Y-m-d', strtotime($key['created_at'])); ?>
                    </td>
                    <td class="py-4 px-4 text-right">
<form method="POST" action="index.php?page=api_handler&lang=<?php echo $lang; ?>" onsubmit="event.preventDefault(); Swal.fire({title:'<?php echo addslashes($l['confirm_del']); ?>',icon:'warning',showCancelButton:true,confirmButtonText:'<?php echo addslashes($l['delete']); ?>',cancelButtonText:'<?php echo addslashes($lang === 'es' ? 'Cancelar' : 'Cancel'); ?>'}).then(r=>r.isConfirmed&&this.submit());" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="action" value="delete_api_key">
                            <input type="hidden" name="id" value="<?php echo $key['id']; ?>">
                            <button type="submit" class="text-danger hover:underline text-sm font-medium"><?php echo $l['delete']; ?></button>
                        </form>
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
            <h3 class="text-xl font-bold text-black"><?php echo $l['new_key']; ?></h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="api-key-form" method="POST" action="index.php?page=api_handler&lang=<?php echo $lang; ?>" class="space-y-4">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="create_api_key">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['name']; ?> *</label>
                <input type="text" name="name" id="form-name" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary" placeholder="e.g. Production App">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['user']; ?> *</label>
                <select name="user_id" id="form-user_id" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                    <?php
                    $users = $pdo->query("SELECT id, name, email FROM users WHERE status = 'Active' ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($users as $u): ?>
                    <option value="<?php echo $u['id']; ?>"><?php echo htmlspecialchars($u['name'] . ' (' . $u['email'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-bold text-white hover:bg-opacity-90"><?php echo $l['save']; ?></button>
                <button type="button" onclick="closeModal()" class="flex-1 rounded border border-stroke py-3 font-bold text-slate-600 hover:bg-slate-50"><?php echo $l['cancel']; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modal-backdrop').classList.add('open');
}
function closeModal() {
    document.getElementById('modal-backdrop').classList.remove('open');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
function copyKey(key) {
    navigator.clipboard.writeText(key).then(function() {
        Swal.fire({title: '<?php echo addslashes($l['copied']); ?>', icon: 'success', timer: 1500, showConfirmButton: false});
    });
}
</script>