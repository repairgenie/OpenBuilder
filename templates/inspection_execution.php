<!-- templates/inspection_execution.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$lang = $_GET['lang'] ?? 'en';
$pdo = Database::connect();
$user_id = $_SESSION['user_id'] ?? null;

$inspection_id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM inspections WHERE id=?");
$stmt->execute([$inspection_id]);
$inspection = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inspection) {
    header("Location: ../index.php?page=inspection_schedule&lang=$lang");
    exit;
}

// Fetch items — these are inserted from a template or manually
$istmt = $pdo->prepare("SELECT * FROM inspection_items WHERE inspection_id=? ORDER BY id ASC");
$istmt->execute([$inspection_id]);
$items = $istmt->fetchAll(PDO::FETCH_ASSOC);

$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
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
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Ejecutar Inspección' : 'Execute Inspection'; ?></h2>
        <p class="text-slate-500"><?php echo htmlspecialchars($inspection['title']); ?> — <?php echo htmlspecialchars($inspection['scheduled_date']); ?></p>
    </div>
    <div class="flex gap-2">
        <a href="?page=inspection_schedule&lang=<?php echo htmlspecialchars($lang); ?>" class="rounded-md border border-stroke py-2 px-6 font-medium text-slate-600 hover:bg-slate-50">
            <?php echo $lang === 'es' ? 'Volver' : 'Back'; ?>
        </a>
        <button onclick="saveResults()" class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Guardar Resultados' : 'Save Results'; ?>
        </button>
    </div>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-stroke">
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Ítem' : 'Item'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Resultado' : 'Result'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Comentarios' : 'Comments'; ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr class="border-b border-stroke">
                <td class="py-4 px-4 text-sm">
                    <?php if (!empty($item['section_name'])): ?>
                    <span class="text-xs font-bold text-primary uppercase"><?php echo htmlspecialchars($item['section_name']); ?></span><br>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($item['item_text'] ?? $item['checkpoint_en'] ?? ''); ?>
                </td>
                <td class="py-4 px-4">
                    <select name="result_<?php echo $item['id']; ?>" class="rounded border border-stroke py-2 px-3 text-sm">
                        <option value="">--</option>
                        <option value="Pass" <?php echo ($item['result'] ?? '') === 'Pass' ? 'selected' : ''; ?>>✓ <?php echo $lang === 'es' ? 'Pasa' : 'Pass'; ?></option>
                        <option value="Fail" <?php echo ($item['result'] ?? '') === 'Fail' ? 'selected' : ''; ?>>✗ <?php echo $lang === 'es' ? 'Falla' : 'Fail'; ?></option>
                        <option value="N/A" <?php echo ($item['result'] ?? '') === 'N/A' ? 'selected' : ''; ?>>N/A</option>
                    </select>
                </td>
                <td class="py-4 px-4">
                    <input type="text" name="comments_<?php echo $item['id']; ?>" value="<?php echo htmlspecialchars($item['comments'] ?? ''); ?>" class="w-full rounded border border-stroke py-2 px-3 text-sm" placeholder="<?php echo $lang === 'es' ? 'Agregar comentarios...' : 'Add comments...'; ?>">
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?>
            <tr>
                <td colspan="3" class="py-8 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Sin ítems para esta inspección. Agregue ítems desde la plantilla.' : 'No items for this inspection. Add items from the template.'; ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function saveResults() {
    const params = new URLSearchParams({
        action: 'save_results',
        inspection_id: '<?php echo $inspection_id; ?>',
        lang: '<?php echo htmlspecialchars($lang); ?>',
        csrf_token: '<?php echo csrf_token(); ?>'
    });
    document.querySelectorAll('select[name^="result_"]').forEach(sel => {
        const id = sel.name.replace('result_', '');
        const result = sel.value;
        const comments = document.querySelector('input[name="comments_' + id + '"]')?.value || '';
        params.append('item_' + id + '_result', result);
        params.append('item_' + id + '_comments', comments);
    });
    fetch('templates/inspection_handler.php', { method: 'POST', body: params })
        .then(() => {
            Swal.fire({
                title: '<?php echo $lang === 'es' ? 'Resultados guardados' : 'Results saved'; ?>',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => window.location.href = 'index.php?page=inspection_schedule&lang=<?php echo htmlspecialchars($lang); ?>');
        });
}
</script>
