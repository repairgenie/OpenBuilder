<!-- templates/rfis.php -->
<?php
// Fetch RFIs with pagination and filtering
$rfi_search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$priority_filter = $_GET['priority'] ?? '';

$query = "SELECT * FROM rfis WHERE 1=1";
$params = [];

if ($rfi_search) {
    $query .= " AND (subject LIKE :search OR ref_number LIKE :search)";
    $params[':search'] = "%$rfi_search%";
}
if ($status_filter) {
    $query .= " AND status = :status";
    $params[':status'] = $status_filter;
}
if ($priority_filter) {
    $query .= " AND priority = :priority";
    $params[':priority'] = $priority_filter;
}

$query .= " ORDER BY id DESC";

$pagination = paginate_results($pdo, $query, $params, 5);
$rfis = $pagination['items'];

function get_status_class($status) {
    switch ($status) {
        case 'Open': return 'bg-warning text-white';
        case 'Closed': return 'bg-success text-white';
        default: return 'bg-slate-500 text-white';
    }
}

function get_priority_class($priority) {
    switch ($priority) {
        case 'High': return 'text-danger font-bold';
        case 'Medium': return 'text-warning';
        case 'Low': return 'text-success';
        default: return 'text-slate-500';
    }
}
?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-2xl font-bold text-black">
        <?php echo $lang === 'es' ? 'Solicitudes de Información (RFIs)' : 'Requests for Information (RFIs)'; ?>
    </h2>
    <div class="flex gap-3">
        <a href="?action=export_rfis&lang=<?php echo $lang; ?>" class="inline-flex items-center justify-center rounded-md border border-stroke bg-white py-2 px-6 text-center font-medium text-black hover:shadow-md transition-all">
            <?php echo $lang === 'es' ? 'Exportar CSV' : 'Export CSV'; ?>
        </a>
        <a href="?page=create_rfi&lang=<?php echo $lang; ?>" class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90 shadow-md transition-all active:scale-95">
            <?php echo $lang === 'es' ? 'Crear RFI' : 'Create RFI'; ?>
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-6 py-4">
    <form action="index.php" method="GET" class="flex flex-wrap items-end gap-4">
        <input type="hidden" name="page" value="rfis">
        <input type="hidden" name="lang" value="<?php echo $lang; ?>">
        
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></label>
            <select name="status" class="w-full rounded border border-stroke bg-white py-2 px-3 text-sm focus:border-primary outline-none">
                <option value=""><?php echo $lang === 'es' ? 'Todos los Estados' : 'All Statuses'; ?></option>
                <option value="Open" <?php echo $status_filter === 'Open' ? 'selected' : ''; ?>>Open</option>
                <option value="Closed" <?php echo $status_filter === 'Closed' ? 'selected' : ''; ?>>Closed</option>
            </select>
        </div>

        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-bold text-slate-500 uppercase mb-1"><?php echo $lang === 'es' ? 'Prioridad' : 'Priority'; ?></label>
            <select name="priority" class="w-full rounded border border-stroke bg-white py-2 px-3 text-sm focus:border-primary outline-none">
                <option value=""><?php echo $lang === 'es' ? 'Todas las Prioridades' : 'All Priorities'; ?></option>
                <option value="High" <?php echo $priority_filter === 'High' ? 'selected' : ''; ?>>High</option>
                <option value="Medium" <?php echo $priority_filter === 'Medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="Low" <?php echo $priority_filter === 'Low' ? 'selected' : ''; ?>>Low</option>
            </select>
        </div>

        <button type="submit" class="bg-primary text-white py-2 px-6 rounded text-sm font-medium hover:bg-opacity-90 shadow-sm transition-all">
            <?php echo $lang === 'es' ? 'Filtrar' : 'Filter'; ?>
        </button>
        <a href="?page=rfis&lang=<?php echo $lang; ?>" class="text-slate-500 text-sm py-2 hover:underline">
            <?php echo $lang === 'es' ? 'Limpiar' : 'Clear'; ?>
        </a>
    </form>
</div>

<!-- RFI List Table -->
<div class="card overflow-hidden">
    <div class="mb-4 px-4 pt-4">
        <h4 class="text-xl font-bold text-black">
            <?php echo $lang === 'es' ? 'Lista de RFIs' : 'RFI List'; ?>
        </h4>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 w-10">
                        <input type="checkbox" id="select-all" class="h-4 w-4 rounded border-stroke text-primary focus:ring-primary">
                    </th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Referencia' : 'Ref #'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Asunto' : 'Subject'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 hidden sm:table-cell"><?php echo $lang === 'es' ? 'Prioridad' : 'Priority'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-right"><?php echo $lang === 'es' ? 'Acción' : 'Action'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rfis as $rfi): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4">
                        <input type="checkbox" class="rfi-checkbox h-4 w-4 rounded border-stroke text-primary focus:ring-primary">
                    </td>
                    <td class="py-4 px-4 text-sm font-bold text-black">#<?php echo htmlspecialchars($rfi['ref_number']); ?></td>
                    <td class="py-4 px-4 text-sm"><?php echo htmlspecialchars($rfi['subject']); ?></td>
                    <td class="py-4 px-4 text-sm hidden sm:table-cell">
                        <span class="<?php echo get_priority_class($rfi['priority']); ?>">
                            <?php echo htmlspecialchars($rfi['priority']); ?>
                        </span>
                    </td>
                    <td class="py-4 px-4">
                        <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold <?php echo get_status_class($rfi['status']); ?>">
                            <?php echo htmlspecialchars($rfi['status']); ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <a href="?page=view_rfi&id=<?php echo $rfi['id']; ?>&lang=<?php echo $lang; ?>" class="text-primary hover:underline text-sm font-medium"><?php echo $lang === 'es' ? 'Ver' : 'View'; ?></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulk-actions" class="bg-primary bg-opacity-5 border-t border-primary border-opacity-20 p-4 flex items-center justify-between hidden">
        <div class="flex items-center gap-4">
            <span class="text-sm font-bold text-black"><span id="selected-count">0</span> <?php echo $lang === 'es' ? 'Seleccionados' : 'Selected'; ?></span>
            <button onclick="window.showToast('<?php echo $lang === 'es' ? 'Exportando PDFs...' : 'Exporting PDFs...'; ?>', 'success')" class="rounded-md bg-black py-2 px-4 text-xs font-bold text-white hover:bg-opacity-90">
                <?php echo $lang === 'es' ? 'Exportar PDF' : 'Export PDF'; ?>
            </button>
            <button class="rounded-md bg-danger py-2 px-4 text-xs font-bold text-white hover:bg-opacity-90">
                <?php echo $lang === 'es' ? 'Cerrar Seleccionados' : 'Close Selected'; ?>
            </button>
        </div>
    </div>

    <?php include __DIR__ . '/../layouts/pagination.php'; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.rfi-checkbox');
    const bulkBar = document.getElementById('bulk-actions');
    const countLabel = document.getElementById('selected-count');

    function updateBulkBar() {
        const checkedCount = document.querySelectorAll('.rfi-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkBar.classList.remove('hidden');
            countLabel.textContent = checkedCount;
        } else {
            bulkBar.classList.add('hidden');
        }
    }

    selectAll?.addEventListener('change', (e) => {
        checkboxes.forEach(cb => cb.checked = e.target.checked);
        updateBulkBar();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkBar);
    });
});
</script>
