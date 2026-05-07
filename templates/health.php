<!-- templates/health.php -->
<?php
$db_status = 'Healthy';
$ai_status = getenv('GEMINI_API_KEY') ? 'Healthy' : 'Unavailable';
?>
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Estado del Sistema' : 'System Health'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Monitoreo en tiempo real de los servicios principales.' : 'Real-time monitoring of core services.'; ?></p>
</div>

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div class="card flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-full bg-success bg-opacity-10 flex items-center justify-center text-success">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Base de Datos (SQLite)' : 'Database (SQLite)'; ?></p>
                <p class="text-xs text-slate-500"><?php echo $lang === 'es' ? 'Conectado' : 'Connected'; ?></p>
            </div>
        </div>
        <span class="text-xs font-bold text-success uppercase"><?php echo $db_status; ?></span>
    </div>

    <div class="card flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-full <?php echo $ai_status === 'Healthy' ? 'bg-success' : 'bg-danger'; ?> bg-opacity-10 flex items-center justify-center <?php echo $ai_status === 'Healthy' ? 'text-success' : 'text-danger'; ?>">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Servicio de IA (Gemini)' : 'AI Service (Gemini)'; ?></p>
                <p class="text-xs text-slate-500"><?php echo $ai_status === 'Healthy' ? 'API Key Active' : 'API Key Missing'; ?></p>
            </div>
        </div>
        <span class="text-xs font-bold <?php echo $ai_status === 'Healthy' ? 'text-success' : 'text-danger'; ?> uppercase"><?php echo $ai_status; ?></span>
    </div>
</div>
