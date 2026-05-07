<!-- templates/inspection_templates.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Plantillas de Inspección' : 'Inspection Templates'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Crear formularios personalizados para control de calidad y seguridad.' : 'Create custom forms for quality and safety control.'; ?></p>
    </div>
    <button class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
        <?php echo $lang === 'es' ? 'Nueva Plantilla' : 'New Template'; ?>
    </button>
</div>

<div class="card bg-white">
    <div class="space-y-4">
        <!-- Template Row -->
        <div class="flex items-center justify-between p-4 border border-stroke rounded-lg hover:bg-slate-50 cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 bg-success bg-opacity-10 text-success rounded flex items-center justify-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-black">Pre-Concrete Pour Checklist</h4>
                    <p class="text-xs text-slate-500"><?php echo $lang === 'es' ? '12 puntos de verificación' : '12 Checkpoints'; ?></p>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="text-xs font-bold text-primary hover:underline">Edit</button>
                <button class="text-xs font-bold text-slate-400">Clone</button>
            </div>
        </div>
        
        <div class="flex items-center justify-between p-4 border border-stroke rounded-lg hover:bg-slate-50 cursor-pointer">
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 bg-danger bg-opacity-10 text-danger rounded flex items-center justify-center">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-black">Weekly Safety Walk</h4>
                    <p class="text-xs text-slate-500"><?php echo $lang === 'es' ? '25 puntos de verificación' : '25 Checkpoints'; ?></p>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="text-xs font-bold text-primary hover:underline">Edit</button>
                <button class="text-xs font-bold text-slate-400">Clone</button>
            </div>
        </div>
    </div>
</div>
