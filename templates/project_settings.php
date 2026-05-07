<!-- templates/project_settings.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Configuración del Proyecto' : 'Project Settings'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Administrar preferencias y metadatos del proyecto.' : 'Manage project preferences and metadata.'; ?></p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- General Settings -->
    <div class="card">
        <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'General' : 'General'; ?></h3>
        <form class="space-y-4">
            <div>
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre del Proyecto' : 'Project Name'; ?></label>
                <input type="text" value="OpenBuilder HQ" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
            </div>
            <div>
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Ubicación' : 'Location'; ?></label>
                <input type="text" value="San Francisco, CA" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
            </div>
            <button type="button" onclick="window.showToast('<?php echo $lang === 'es' ? 'Configuración guardada' : 'Settings saved'; ?>', 'success')" class="flex justify-center rounded bg-primary py-3 px-6 font-medium text-white hover:bg-opacity-90">
                <?php echo $lang === 'es' ? 'Guardar Cambios' : 'Save Changes'; ?>
            </button>
        </form>
    </div>

    <!-- Notification Settings -->
    <div class="card">
        <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Notificaciones' : 'Notifications'; ?></h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Alertas de Presupuesto' : 'Budget Alerts'; ?></p>
                    <p class="text-xs text-slate-500"><?php echo $lang === 'es' ? 'Recibir avisos cuando los costos excedan el 90%.' : 'Notify when costs exceed 90%.'; ?></p>
                </div>
                <input type="checkbox" checked class="h-5 w-5 rounded border-stroke text-primary focus:ring-primary">
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Nuevas RFIs' : 'New RFIs'; ?></p>
                    <p class="text-xs text-slate-500"><?php echo $lang === 'es' ? 'Notificar al equipo sobre nuevas consultas.' : 'Notify team about new queries.'; ?></p>
                </div>
                <input type="checkbox" checked class="h-5 w-5 rounded border-stroke text-primary focus:ring-primary">
            </div>
        </div>
    </div>

    <!-- Data Portability -->
    <div class="card mt-6 lg:col-span-2">
        <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Portabilidad de Datos' : 'Data Portability'; ?></h3>
        <div class="flex items-center justify-between">
            <p class="text-sm text-slate-500">
                <?php echo $lang === 'es' ? 'Descarga todos los datos del proyecto en formato JSON para copias de seguridad.' : 'Download all project data in JSON format for backups.'; ?>
            </p>
            <button onclick="window.showToast('<?php echo $lang === 'es' ? 'Preparando archivo...' : 'Preparing export...'; ?>', 'success')" class="rounded bg-black py-2 px-6 font-bold text-white hover:bg-opacity-90">
                <?php echo $lang === 'es' ? 'Exportar JSON' : 'Export JSON'; ?>
            </button>
        </div>
    </div>

    <!-- Theme Settings -->
    <div class="card mt-6 lg:col-span-2">
        <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Apariencia' : 'Appearance'; ?></h3>
        <div class="flex gap-4">
            <button class="h-10 w-10 rounded-full bg-primary ring-2 ring-offset-2 ring-primary"></button>
            <button class="h-10 w-10 rounded-full bg-danger opacity-50 hover:opacity-100 transition-opacity"></button>
            <button class="h-10 w-10 rounded-full bg-success opacity-50 hover:opacity-100 transition-opacity"></button>
            <button class="h-10 w-10 rounded-full bg-black opacity-50 hover:opacity-100 transition-opacity"></button>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="card mt-6 lg:col-span-2 border-danger border-opacity-20 bg-danger bg-opacity-5">
        <h3 class="text-lg font-bold text-danger mb-4 border-b border-danger border-opacity-10 pb-2"><?php echo $lang === 'es' ? 'Zona de Peligro' : 'Danger Zone'; ?></h3>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Archivar Proyecto' : 'Archive Project'; ?></p>
                <p class="text-xs text-slate-500"><?php echo $lang === 'es' ? 'Hacer que el proyecto sea de solo lectura.' : 'Make the project read-only.'; ?></p>
            </div>
            <button onclick="window.modals['confirm-modal'].open({ title: '<?php echo $lang === 'es' ? '¿Archivar Proyecto?' : 'Archive Project?'; ?>', message: '<?php echo $lang === 'es' ? 'Esta acción no se puede deshacer fácilmente.' : 'This action cannot be easily undone.'; ?>', onConfirm: () => window.showToast('Archived', 'success') })" class="rounded bg-danger py-2 px-6 font-bold text-white hover:bg-opacity-90">
                <?php echo $lang === 'es' ? 'Archivar' : 'Archive'; ?>
            </button>
        </div>
    </div>
</div>
