<!-- templates/project_settings.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$pdo = Database::connect();
$stmt = $pdo->query("SELECT `key`, value FROM system_settings");
$settings = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $settings[$row['key']] = $row['value'];
}

$labels = [
    'en' => [
        'title'       => 'Project Settings',
        'general'     => 'General',
        'notifications'=> 'Notifications',
        'regional'    => 'Regional & Locale',
        'danger'      => 'Danger Zone',
        'project_name'=> 'Project Name',
        'location'    => 'Location',
        'start_date'  => 'Start Date',
        'end_date'    => 'End Date',
        'client'      => 'Client',
        'contract_val'=> 'Contract Value',
        'save'        => 'Save Changes',
        'cancel'      => 'Cancel',
        'archive'     => 'Archive Project',
        'archive_desc'=> 'Make the project read-only. Type the project name to confirm.',
        'type_confirm'=> 'Type project name to confirm',
    ],
    'es' => [
        'title'       => 'Configuración del Proyecto',
        'general'     => 'General',
        'notifications'=> 'Notificaciones',
        'regional'    => 'Regional e Idioma',
        'danger'      => 'Zona de Peligro',
        'project_name'=> 'Nombre del Proyecto',
        'location'    => 'Ubicación',
        'start_date'  => 'Fecha de Inicio',
        'end_date'    => 'Fecha de Fin',
        'client'      => 'Cliente',
        'contract_val'=> 'Valor del Contrato',
        'save'        => 'Guardar Cambios',
        'cancel'      => 'Cancelar',
        'archive'     => 'Archivar Proyecto',
        'archive_desc'=> 'Hacer el proyecto de solo lectura. Escriba el nombre para confirmar.',
        'type_confirm'=> 'Escriba el nombre del proyecto para confirmar',
    ],
];
$l = $labels[$lang] ?? $labels['en'];
$currencies = ['USD','MXN','EUR','GBP'];
$date_formats = ['Y-m-d','m/d/Y','d/m/Y','d-M-y'];
$timezones = [
    'America/Los_Angeles' => 'Pacific (Los Angeles)',
    'America/Denver'     => 'Mountain (Denver)',
    'America/Chicago'    => 'Central (Chicago)',
    'America/New_York'   => 'Eastern (New York)',
    'America/Mexico_City'=> 'Mexico City',
    'Europe/Madrid'      => 'Madrid',
];
$regions = ['USA','MEX','ESP'];
?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $l['title']; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Administrar preferencias y metadatos del proyecto.' : 'Manage project preferences and metadata.'; ?></p>
</div>

<div class="flex flex-col gap-6">

    <!-- General Settings -->
    <div class="card">
        <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $l['general']; ?></h3>
        <form method="POST" action="index.php?page=settings_handler&lang=<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="section" value="general">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['project_name']; ?></label>
                    <input type="text" name="project_name" value="<?php echo htmlspecialchars($settings['project_name'] ?? ''); ?>" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['location']; ?></label>
                    <input type="text" name="project_location" value="<?php echo htmlspecialchars($settings['project_location'] ?? ''); ?>" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['start_date']; ?></label>
                    <input type="date" name="project_start_date" value="<?php echo htmlspecialchars($settings['project_start_date'] ?? ''); ?>" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['end_date']; ?></label>
                    <input type="date" name="project_end_date" value="<?php echo htmlspecialchars($settings['project_end_date'] ?? ''); ?>" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['client']; ?></label>
                    <input type="text" name="project_client" value="<?php echo htmlspecialchars($settings['project_client'] ?? ''); ?>" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $l['contract_val']; ?></label>
                    <input type="number" step="0.01" name="contract_value" value="<?php echo htmlspecialchars($settings['contract_value'] ?? ''); ?>" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="rounded bg-primary py-3 px-8 font-bold text-white hover:bg-opacity-90"><?php echo $l['save']; ?></button>
            </div>
        </form>
    </div>

    <!-- Notification Settings -->
    <div class="card">
        <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $l['notifications']; ?></h3>
        <form method="POST" action="index.php?page=settings_handler&lang=<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="section" value="notifications">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Alertas de Presupuesto' : 'Budget Alerts'; ?></p>
                        <p class="text-xs text-slate-500"><?php echo $lang === 'es' ? 'Notificar cuando costos excedan 90%.' : 'Notify when costs exceed 90%.'; ?></p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="hidden" name="budget_alerts" value="0">
                        <input type="checkbox" name="budget_alerts" value="1" <?php echo ($settings['budget_alerts'] ?? '0') === '1' ? 'checked' : ''; ?> class="sr-only peer">
                        <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Nuevas RFIs' : 'New RFI Notifications'; ?></p>
                        <p class="text-xs text-slate-500"><?php echo $lang === 'es' ? 'Notificar al equipo sobre nuevas consultas.' : 'Notify team about new RFIs.'; ?></p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="hidden" name="new_rfi_notifications" value="0">
                        <input type="checkbox" name="new_rfi_notifications" value="1" <?php echo ($settings['new_rfi_notifications'] ?? '0') === '1' ? 'checked' : ''; ?> class="sr-only peer">
                        <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Recordatorios de Diarios' : 'Daily Log Reminders'; ?></p>
                        <p class="text-xs text-slate-500"><?php echo $lang === 'es' ? 'Notificar a usuarios que no han enviado diario.' : 'Notify users who haven\'t submitted daily log.'; ?></p>
                    </div>
                    <label class="relative inline-flex cursor-pointer items-center">
                        <input type="hidden" name="daily_log_reminders" value="0">
                        <input type="checkbox" name="daily_log_reminders" value="1" <?php echo ($settings['daily_log_reminders'] ?? '0') === '1' ? 'checked' : ''; ?> class="sr-only peer">
                        <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary peer-checked:after:translate-x-full"></div>
                    </label>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="rounded bg-primary py-3 px-8 font-bold text-white hover:bg-opacity-90"><?php echo $l['save']; ?></button>
            </div>
        </form>
    </div>

    <!-- Regional & Locale -->
    <div class="card">
        <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $l['regional']; ?></h3>
        <form method="POST" action="index.php?page=settings_handler&lang=<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="section" value="regional">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Moneda' : 'Currency'; ?></label>
                    <select name="currency" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                        <?php foreach ($currencies as $c): ?>
                        <option value="<?php echo $c; ?>" <?php echo ($settings['currency'] ?? 'USD') === $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Formato de Fecha' : 'Date Format'; ?></label>
                    <select name="date_format" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                        <?php foreach ($date_formats as $f): ?>
                        <option value="<?php echo $f; ?>" <?php echo ($settings['date_format'] ?? 'Y-m-d') === $f ? 'selected' : ''; ?>><?php echo date($f); ?> (<?php echo $f; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Zona Horaria' : 'Timezone'; ?></label>
                    <select name="timezone" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                        <?php foreach ($timezones as $tz => $label): ?>
                        <option value="<?php echo $tz; ?>" <?php echo ($settings['timezone'] ?? 'America/Los_Angeles') === $tz ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Región Predeterminada' : 'Default Region'; ?></label>
                    <select name="default_region" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-4 font-medium outline-none transition focus:border-primary">
                        <?php foreach ($regions as $r): ?>
                        <option value="<?php echo $r; ?>" <?php echo ($settings['default_region'] ?? 'USA') === $r ? 'selected' : ''; ?>><?php echo $r; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="rounded bg-primary py-3 px-8 font-bold text-white hover:bg-opacity-90"><?php echo $l['save']; ?></button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="card border-danger border-opacity-20 bg-danger bg-opacity-5">
        <h3 class="text-lg font-bold text-danger mb-4 border-b border-danger border-opacity-10 pb-2"><?php echo $l['danger']; ?></h3>
        <form method="POST" action="index.php?page=settings_handler&lang=<?php echo $lang; ?>">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <input type="hidden" name="section" value="danger">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-bold text-black"><?php echo $l['archive']; ?></p>
                    <p class="text-xs text-slate-500"><?php echo $l['archive_desc']; ?></p>
                </div>
                <div class="flex flex-col gap-2 min-w-0 sm:min-w-[240px]">
                    <input type="text" name="project_name" placeholder="<?php echo $l['type_confirm']; ?>" class="w-full rounded border border-stroke py-2 px-3 text-sm outline-none focus:border-danger">
                    <button type="submit" class="rounded bg-danger py-2 px-4 font-bold text-white hover:bg-opacity-90 text-sm"><?php echo $l['archive']; ?></button>
                </div>
            </div>
        </form>
    </div>

</div>
