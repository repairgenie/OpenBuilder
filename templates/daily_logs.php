<!-- templates/daily_logs.php -->
<?php
$query = "SELECT * FROM daily_logs ORDER BY log_date DESC, id DESC";
$pagination = paginate_results($pdo, $query, [], 6);
$daily_logs = $pagination['items'];
?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-2xl font-bold text-black">
        <?php echo $lang === 'es' ? 'Diarios de Obra' : 'Daily Logs'; ?>
    </h2>
    <a href="?page=create_daily_log&lang=<?php echo $lang; ?>" class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90 shadow-md transition-all active:scale-95">
        <?php echo $lang === 'es' ? 'Crear Diario' : 'Create Daily Log'; ?>
    </a>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
    <?php foreach ($daily_logs as $log): ?>
    <div class="card hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">
                <?php echo date('M d, Y', strtotime($log['log_date'])); ?>
            </span>
            <span class="inline-flex rounded-full bg-success bg-opacity-10 py-1 px-3 text-xs font-bold text-success">
                <?php echo htmlspecialchars($log['weather']); ?>
            </span>
        </div>
        <h3 class="text-lg font-bold text-black mb-2 truncate">
            <?php echo $lang === 'es' ? 'Informe de Obra' : 'Project Report'; ?>
        </h3>
        <p class="text-sm text-slate-600 line-clamp-3 mb-4">
            <?php echo htmlspecialchars(substr($log['work_performed'], 0, 150)); ?>...
        </p>
        <div class="flex items-center justify-between border-t border-stroke pt-4">
            <div class="flex items-center gap-2">
                <svg class="fill-slate-400" width="16" height="16" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                </svg>
                <span class="text-xs font-medium text-slate-500"><?php echo $log['manpower']; ?> <?php echo $lang === 'es' ? 'Personal' : 'Crew'; ?></span>
            </div>
            <a href="?page=view_daily_log&id=<?php echo $log['id']; ?>&lang=<?php echo $lang; ?>" class="text-primary hover:underline text-sm font-bold">
                <?php echo $lang === 'es' ? 'Ver Detalles' : 'View Details'; ?>
            </a>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="mt-6">
    <?php include __DIR__ . '/../layouts/pagination.php'; ?>
</div>

<?php if (empty($daily_logs)): ?>
<div class="py-20 text-center text-slate-500 italic">
    <?php echo $lang === 'es' ? 'No se encontraron registros diarios.' : 'No daily logs found.'; ?>
</div>
<?php endif; ?>
