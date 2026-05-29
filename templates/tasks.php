<!-- templates/tasks.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Fetch cost codes for searchableSelect
$cost_codes = $pdo->query("SELECT id, code, description FROM cost_codes ORDER BY code")->fetchAll(PDO::FETCH_ASSOC);
// Fetch crews
$crews = $pdo->query("SELECT id, name FROM crews ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
// Fetch all tasks for the list/Gantt
$tasks = $pdo->query("
    SELECT t.*, c.name as crew_name, cc.code as cost_code 
    FROM tasks t 
    LEFT JOIN crews c ON t.assigned_crew_id = c.id 
    LEFT JOIN cost_codes cc ON t.cost_code_id = cc.id 
    ORDER BY t.start_date ASC, t.id ASC
")->fetchAll(PDO::FETCH_ASSOC);

function status_badge($status) {
    $map = [
        'Not Started' => 'bg-slate-100 text-slate-600',
        'In Progress' => 'bg-primary bg-opacity-10 text-primary',
        'Complete' => 'bg-success bg-opacity-10 text-success',
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Programación de Tareas' : 'Task Scheduling'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestión de tareas con ruta crítica y visualización Gantt.' : 'Task management with critical path and Gantt visualization.'; ?></p>
    </div>
    <div class="flex gap-2">
        <button onclick="toggleView()" id="view-toggle-btn" class="rounded-md bg-white border border-stroke py-2 px-4 font-medium text-black hover:bg-slate-50">
            <?php echo $lang === 'es' ? 'Ver Calendario' : 'Calendar View'; ?>
        </button>
        <button onclick="openModal('create-task-modal')" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Nueva Tarea' : 'New Task'; ?>
        </button>
    </div>
</div>

<!-- Gantt View -->
<div id="gantt-view" class="card overflow-hidden mb-6">
    <div class="p-4 border-b border-stroke">
        <h3 class="font-bold text-black"><? Php echo $lang === 'es' ? 'Diagrama Gantt' : 'Gantt Chart'; ?></h3>
    </div>
    <div class="overflow-x-auto">
        <div class="gantt-container min-h-[200px]">
            <?php
            // Calculate date range for Gantt
            $all_dates = array_merge(array_column($tasks, 'start_date'), array_column($tasks, 'end_date'));
            $min_date = $all_dates ? min(array_map('strtotime', $all_dates)) : time();
            $max_date = $all_dates ? max(array_map('strtotime', $all_dates)) : time() + 86400*30;
            $total_days = max(1, ceil(($max_date - $min_date) / 86400));
            $col_width = min(40, max(10, floor(800 / max(1, $total_days))));
            ?>
            <div class="flex font-bold text-xs text-slate-500 border-b border-stroke" style="min-width:<?php echo max(400, $total_days * $col_width + 300); ?>px;">
                <div class="w-[300px] p-2 border-r border-stroke"><?php echo $lang === 'es' ? 'Tarea' : 'Task'; ?></div>
                <div class="flex-1 p-2 text-center"><?php echo $lang === 'es' ? 'Línea de Tiempo' : 'Timeline'; ?></div>
            </div>
            <?php foreach ($tasks as $task): 
                $start_ts = strtotime($task['start_date']);
                $end_ts = strtotime($task['end_date']);
                $left_px = max(0, floor(($start_ts - $min_date) / 86400) * $col_width);
                $width_px = max($col_width, floor(($end_ts - $start_ts) / 86400 + 1) * $col_width);
                $is_critical = $task['is_critical'];
            ?>
            <div class="flex items-center border-b border-stroke hover:bg-slate-50" style="min-width:<?php echo max(400, $total_days * $col_width + 300); ?>px; min-height:40px;">
                <div class="w-[300px] p-2 border-r border-stroke truncate">
                    <div class="flex items-center gap-2">
                        <?php if ($is_critical): ?>
                        <span class="h-2 w-2 rounded-full bg-danger" title="<?php echo $lang === 'es' ? 'Ruta Crítica' : 'Critical Path'; ?>"></span>
                        <?php endif; ?>
                        <span class="font-bold text-black text-sm truncate"><?php echo htmlspecialchars($task['task_name']); ?></span>
                    </div>
                    <div class="text-xs text-slate-400"><?php echo htmlspecialchars($task['crew_name'] ?? ''); ?></div>
                </div>
                <div class="flex-1 p-2 relative">
                    <div class="absolute top-1/2 -translate-y-1/2 h-6 rounded <?php echo $is_critical ? 'bg-danger' : 'bg-primary'; ?> bg-opacity-20 border border-<?php echo $is_critical ? 'danger' : 'primary'; ?> border-opacity-30" 
                         style="left:<?php echo $left_px; ?>px; width:<?php echo $width_px; ?>px;">
                        <div class="h-full rounded bg-<?php echo $is_critical ? 'danger' : 'primary'; ?> opacity-60"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($tasks)): ?>
            <div class="p-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin tareas registradas.' : 'No tasks recorded.'; ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Calendar View (hidden by default) -->
<div id="calendar-view" class="card overflow-hidden mb-6 hidden">
    <div class="p-4 border-b border-stroke">
        <h3 class="font-bold text-black"><?php echo $lang === 'es' ? 'Vista Calendario' : 'Calendar View'; ?></h3>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
        <?php
        // Group tasks by month
        $calendar_tasks = [];
        foreach ($tasks as $t) {
            $month_key = date('Y-m', strtotime($t['start_date']));
            if (!isset($calendar_tasks[$month_key])) $calendar_tasks[$month_key] = [];
            $calendar_tasks[$month_key][] = $t;
        }
        foreach ($calendar_tasks as $month => $month_tasks):
        ?>
        <div class="border border-stroke rounded-lg p-4">
            <h4 class="font-bold text-black mb-3"><?php echo date('F Y', strtotime($month . '-01')); ?></h4>
            <?php foreach ($month_tasks as $t): ?>
            <div class="mb-2 p-2 rounded bg-slate-50 border border-stroke">
                <div class="font-bold text-sm text-black"><?php echo htmlspecialchars($t['task_name']); ?></div>
                <div class="text-xs text-slate-500"><?php echo $t['start_date']; ?> — <?php echo $t['end_date']; ?></div>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded uppercase <?php echo status_badge($t['status']); ?>"><?php echo htmlspecialchars($t['status']); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
        <?php if (empty($calendar_tasks)): ?>
        <div class="col-span-3 p-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin tareas este mes.' : 'No tasks this month.'; ?></div>
        <?php endif; ?>
    </div>
</div>

<!-- Task List View -->
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Tarea' : 'Task'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Fecha Inicio' : 'Start Date'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Fecha Fin' : 'End Date'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Cuadrilla' : 'Crew'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Código de Costo' : 'Cost Code'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-center"><?php echo $lang === 'es' ? 'Acciones' : 'Actions'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tasks)): ?>
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin tareas registradas.' : 'No tasks recorded.'; ?></td>
                </tr>
                <?php else: foreach ($tasks as $t): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-2">
                            <?php if ($t['is_critical']): ?>
                            <span class="h-2 w-2 rounded-full bg-danger" title="<?php echo $lang === 'es' ? 'Ruta Crítica' : 'Critical Path'; ?>"></span>
                            <?php endif; ?>
                            <span class="font-bold text-black"><?php echo htmlspecialchars($t['task__name']); ?></span>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($t['start_date']); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($t['end_date']); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($t['crew_name'] ?? '-'); ?></td>
                    <td class="py-4 px-4 text-xs font-mono"><?php echo htmlspecialchars($t['cost_code'] ?? '-'); ?></td>
                    <td class="py-4 px-4"><span class="px-2 py-1 text-[10px] font-bold rounded uppercase <?php echo status_badge($t['status']); ?>"><?php echo htmlspecialchars($t['status']); ?></span></td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick='editTask(<?php echo json_encode($t); ?>)' class="p-2 hover:bg-slate-100 rounded" title="<?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?>">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button onclick="confirmDelete(<?php echo $t['id']; ?>)" class="p-2 hover:bg-slate-100 rounded text-danger" title="<?php echo $lang === 'es' ? 'Eliminar' : 'Delete'; ?>">
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

<!-- Create/Edit Task Modal -->
<div id="create-task-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-lg mx-4 overflow-hidden">
        <div class="bg-primary px-6 py-4 flex items-center justify-between">
            <h3 class="text-white font-bold text-lg" id="task-modal-title"><?php echo $lang === 'es' ? 'Nueva Tarea' : 'New Task'; ?></h3>
            <button onclick="closeModal('create-task-modal')" class="text-white hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <form method="POST" action="templates/tasks_handler.php" class="p-6 space-y-4">
            <input type="hidden" name="action" id="task-action" value="create_task">
            <input type="hidden" name="id" id="task-id">
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">

            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre de Tarea' : 'Task Name'; ?> *</label>
                <input type="text" name="task_name" id="task-name" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha Inicio' : 'Start Date'; ?> *</label>
                    <input type="date" name="start_date" id="task-start_date" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Fecha Fin' : 'End Date'; ?> *</label>
                    <input type="date" name="end_date" id="task-end_date" required class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Cuadrilla Asignada' : 'Assigned Crew'; ?></label>
                    <select name="assigned_crew_id" id="task-crew" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary searchable-select">
                        <option value="">-- <?php echo $lang === 'es' ? 'Ninguna' : 'None'; ?> --</option>
                        <?php foreach ($crews as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Código de Costo' : 'Cost Code'; ?></label>
                    <select name="cost_code_id" id="task-cost_code" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary searchable-select">
                        <option value="">-- <?php echo $lang === 'es' ? 'Ninguno' : 'None'; ?> --</option>
                        <?php foreach ($cost_codes as $cc): ?>
                        <option value="<?php echo $cc['id']; ?>"><?php echo htmlspecialchars($cc['code'] . ' - ' . $cc['description']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></label>
                    <select name="status" id="task-status" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary">
                        <option value="Not Started"><?php echo $lang === 'es' ? 'No Iniciado' : 'Not Started'; ?></option>
                        <option value="In Progress"><?php echo $lang === 'es' ? 'En Progreso' : 'In Progress'; ?></option>
                        <option value="Complete"><?php echo $lang === 'es' ? 'Completado' : 'Complete'; ?></option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Tarea Predecesora' : 'Predecessor Task'; ?></label>
                    <select name="predecessor_task_id" id="task-predecessor" class="w-full rounded border-[1.5px] border-stroke py-3 px-4 outline-none transition focus:border-primary searchable-select">
                        <option value="">-- <?php echo $lang === 'es' ? 'Ninguna' : 'None'; ?> --</option>
                        <?php foreach ($tasks as $t): ?>
                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['task_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_critical" id="task-critical" value="1" class="h-4 w-4 rounded border-stroke text-primary focus:ring-primary">
                <label for="task-critical" class="text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Marcar como ruta crítica' : 'Mark as critical path'; ?></label>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal('create-task-modal')" class="flex-1 rounded border border-stroke py-3 font-medium text-slate-600 hover:bg-slate-50"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></button>
                <button type="submit" class="flex-1 rounded bg-primary py-3 font-medium text-white hover:bg-opacity-90"><?php echo $lang === 'es' ? 'Guardar' : 'Save'; ?></button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

let ganttVisible = true;
function toggleView() {
    const gantt = document.getElementById('gantt-view');
    const calendar = document.getElementById('calendar-view');
    const btn = document.getElementById('view-toggle-btn');
    if (ganttVisible) {
        gantt.classList.add('hidden');
        calendar.classList.remove('hidden');
        btn.textContent = '<?php echo $lang === 'es' ? 'Ver Gantt' : 'Gantt View'; ?>';
    } else {
        calendar.classList.add('hidden');
        gantt.classList.remove('hidden');
        btn.textContent = '<?php echo $lang === 'es' ? 'Ver Calendario' : 'Calendar View'; ?>';
    }
    ganttVisible = !ganttVisible;
}

function editTask(t) {
    document.getElementById('task-action').value = 'update_task';
    document.getElementById('task-id').value = t.id;
    document.getElementById('task-name').value = t.task_name || '';
    document.getElementById('task-start_date').value = t.start_date || '';
    document.getElementById('task-end_date').value = t.end_date || '';
    document.getElementById('task-crew').value = t.assigned_crew_id || '';
    document.getElementById('task-cost_code').value = t.cost_code_id || '';
    document.getElementById('task-status').value = t.status || 'Not Started';
    document.getElementById('task-predecessor').value = t.predecessor_task_id || '';
    document.getElementById('task-critical').checked = t.is_critical == 1;
    document.getElementById('task-modal-title').textContent = '<?php echo $lang === 'es' ? 'Editar Tarea' : 'Edit Task'; ?>';
    openModal('create-task-modal');
}

function confirmDelete(id) {
    Swal.fire({
        title: '<?php echo $lang === 'es' ? '¿Eliminar esta tarea?' : 'Delete this task?'; ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<?php echo $lang === 'es' ? 'Sí, eliminar' : 'Yes, delete'; ?>',
        cancelButtonText: '<?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>',
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'templates/tasks_handler.php';
            form.innerHTML = `
                <input type="hidden" name="action" value="delete_task">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="lang" value="<?php echo $lang; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Initialize searchable selects if select2 is available
if (typeof initSearchableSelects === 'function') {
    initSearchableSelects();
}
</script>