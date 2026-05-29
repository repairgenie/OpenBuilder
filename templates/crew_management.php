<!-- templates/crew_management.php -->
<?php
require_once __DIR__ . '/../src/Database.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Fetch crews
$crew_stmt = $pdo->query("SELECT * FROM crews ORDER BY name");
$crews = $crew_stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper for status badge
function crew_status_badge($status) {
    $map = [
        'On Site' => 'bg-success bg-opacity-10 text-success',
        'Off Site' => 'bg-warning bg-opacity-10 text-warning',
        'Standby' => 'bg-slate-100 text-slate-600',
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Gestión de Cuadrillas' : 'Crew Management'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Asignación diaria de personal a actividades de obra.' : 'Daily assignment of personnel to site activities.'; ?></p>
    </div>
    <button onclick="openModal('create-crew-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
        <?php echo $lang === 'es' ? 'Nueva Cuadrilla' : 'New Crew'; ?>
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (empty($crews)): ?>
    <div class="col-span-full text-center py-12 text-slate-400 italic">
        <?php echo $lang === 'es' ? 'No hay cuadrillas registradas.' : 'No crews registered.'; ?>
    </div>
    <?php else: ?>
        <?php foreach ($crews as $crew): ?>
        <?php
        $mem_stmt = $pdo->prepare("SELECT * FROM crew_members WHERE crew_id = ?");
        $mem_stmt->execute([$crew['id']]);
        $member_list = $mem_stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="card bg-white">
            <div class="flex items-center justify-between mb-4">
                <h4 class="font-bold text-black text-sm"><?php echo htmlspecialchars($crew['name']); ?></h4>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase <?php echo crew_status_badge($crew['status']); ?>">
                    <?php echo htmlspecialchars($crew['status']); ?>
                </span>
            </div>
            
            <div class="text-xs text-slate-500 mb-3">
                <strong><?php echo $lang === 'es' ? 'Oficio:' : 'Trade'; ?>: </strong> 
                <?php echo htmlspecialchars($lang === 'es' ? $crew['trade_es'] : $crew['trade_en']); ?>
            </div>

            <div class="flex -space-x-2 mb-4">
                <?php if (empty($member_list)): ?>
                    <span class="text-xs text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin miembros' : 'No members'; ?></span>
                <?php else: ?>
                    <?php foreach (array_slice($member_list, 0, 4) as $m): ?>
                    <div class="h-8 w-8 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-[10px] text-slate-500" title="<?php echo htmlspecialchars($m['name']); ?>">
                        <?php echo strtoupper(substr($m['name'], 0, 2)); ?>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($member_list) > 4): ?>
                    <div class="h-8 w-8 rounded-full bg-primary border-2 border-white flex items-center justify-center text-[10px] text-white font-bold">+<?php echo count($member_list) - 4; ?></div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="flex gap-2 mb-3">
                <button onclick="openMemberModal(<?php echo $crew['id']; ?>)" class="flex-1 py-2 border border-stroke text-slate-600 text-xs font-bold rounded hover:bg-slate-50">
                    <?php echo $lang === 'es' ? 'Añadir Miembro' : 'Add Member'; ?>
                </button>
            </div>
            
            <div class="flex gap-2">
                <button onclick='editCrew(<?php echo json_encode($crew); ?>)' class="flex-1 py-2 border border-stroke text-slate-600 text-xs font-bold rounded hover:bg-slate-50">
                    <?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>
                </button>
                <button onclick="confirmDeleteCrew(<?php echo $crew['id']; ?>)" class="py-2 px-3 border border-stroke text-danger text-xs font-bold rounded hover:bg-slate-50">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"></path></svg>
                </button>
            </div>

            <?php if (!empty($member_list)): ?>
            <div class="mt-4 border-t border-stroke pt-3">
                <p class="text-xs font-semibold text-slate-500 mb-2"><?php echo $lang === 'es' ? 'Miembros' : 'Members'; ?></p>
                <div class="space-y-1">
                    <?php foreach ($member_list as $m): ?>
                    <div class="flex items-center justify-between text-xs py-1 px-2 rounded hover:bg-slate-50">
                        <span class="text-black"><?php echo htmlspecialchars($m['name']); ?>
                            <?php if ($m['role']): ?><span class="text-slate-400"> - <?php echo htmlspecialchars($m['role']); ?></span><?php endif; ?>
                        </span>
                        <button onclick="removeMember(<?php echo $m['id']; ?>)" class="text-danger hover:opacity-70">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Create/Edit Crew Modal -->
<div id="create-crew-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="crew-modal-title"><?php echo $lang === 'es' ? 'Nueva Cuadrilla' : 'New Crew'; ?></h3>
            <button onclick="closeModal('create-crew-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/crew_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="crew-action" value="create_crew">
            <input type="hidden" name="id" id="crew-id">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre de Cuadrilla' : 'Crew Name'; ?> *</label>
                <input type="text" name="name" id="crew-name" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Oficio (EN)' : 'Trade (EN)'; ?> *</label>
                    <input type="text" name="trade_en" id="crew-trade_en" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Oficio (ES)' : 'Trade (ES)'; ?></label>
                    <input type="text" name="trade_es" id="crew-trade_es" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></label>
                <select name="status" id="crew-status" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                    <option value="On Site"><?php echo $lang === 'es' ? 'En Sitio' : 'On Site'; ?></option>
                    <option value="Off Site"><?php echo $lang === 'es' ? 'Fuera de Sitio' : 'Off Site'; ?></option>
                    <option value="Standby"><?php echo $lang === 'es' ? 'En Espera' : 'Standby'; ?></option>
                </select>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-crew-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Add Member Modal -->
<div id="add-member-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg"><?php echo $lang === 'es' ? 'Añadir Miembro' : 'Add Member'; ?></h3>
            <button onclick="closeModal('add-member-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/crew_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" value="add_member">
            <input type="hidden" name="crew_id" id="member-crew_id">
            <input type="hidden" name="lang" value="<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre' : 'Name'; ?> *</label>
                <input type="text" name="member_name" id="member-name" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Rol' : 'Role'; ?></label>
                <input type="text" name="role" id="member-role" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Teléfono' : 'Phone'; ?></label>
                <input type="text" name="phone" id="member-phone" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('add-member-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Añadir' : 'Add'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function editCrew(crew) {
    document.getElementById('crew-action').value = 'update_crew';
    document.getElementById('crew-id').value = crew.id;
    document.getElementById('crew-name').value = crew.name || '';
    document.getElementById('crew-trade_en').value = crew.trade_en || '';
    document.getElementById('crew-trade_es').value = crew.trade_es || '';
    document.getElementById('crew-status').value = crew.status || 'On Site';
    document.getElementById('crew-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Cuadrilla' : 'Edit Crew'; ?>';
    openModal('create-crew-modal');
}

function openMemberModal(crewId) {
    document.getElementById('member-crew_id').value = crewId;
    document.getElementById('member-name').value = '';
    document.getElementById('member-role').value = '';
    document.getElementById('member-phone').value = '';
    openModal('add-member-modal');
}

function removeMember(memberId) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar este miembro?' : 'Remove this member?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/crew_handler.php';
            form.innerHTML = '<input type="hidden" name="action" value="remove_member"><input type="hidden" name="member_id" value="' + memberId + '"><input type="hidden" name="lang" value="<?php echo $lang; ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function confirmDeleteCrew(crewId) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar esta cuadrilla?' : 'Delete this crew?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/crew_handler.php';
            form.innerHTML = '<input type="hidden" name="action" value="delete_crew"><input type="hidden" name="id" value="' + crewId + '"><input type="hidden" name="lang" value="<?php echo $lang; ?>"><input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">';
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>