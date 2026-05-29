<!-- templates/notification_prefs.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$pdo = Database::connect();
$user_id = (int)($_SESSION['user_id'] ?? 0);

// Load or initialize prefs
$stmt = $pdo->prepare("SELECT * FROM user_notification_prefs WHERE user_id = ?");
$stmt->execute([$user_id]);
$prefs = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$prefs) {
    $prefs = [
        'email_rfis' => 1,
        'email_daily_logs' => 1,
        'email_budget_alerts' => 1,
        'email_submittals' => 1,
        'email_inspections' => 1,
    ];
}

$labels = [
    'en' => [
        'title'        => 'Notification Preferences',
        'subtitle'     => 'Choose what you want to be notified about.',
        'rfis'         => 'RFI Notifications',
        'rfis_desc'    => 'Receive emails when RFIs are created or updated.',
        'daily_logs'   => 'Daily Log Notifications',
        'daily_desc'   => 'Receive reminders and updates about daily logs.',
        'budget'       => 'Budget Alerts',
        'budget_desc'  => 'Get notified when budget thresholds are exceeded.',
        'submittals'   => 'Submittal Notifications',
        'submittals_desc' => 'Receive updates on submittal status changes.',
        'inspections'  => 'Inspection Notifications',
        'inspections_desc' => 'Get notified about upcoming and completed inspections.',
        'save'         => 'Save Preferences',
        'saved'        => 'Preferences saved successfully.',
    ],
    'es' => [
        'title'        => 'Preferencias de Notificación',
        'subtitle'     => 'Elija qué desea recibir notificaciones.',
        'rfis'         => 'Notificaciones de RFI',
        'rfis_desc'    => 'Reciba correos cuando se creen o actualicen RFIs.',
        'daily_logs'   => 'Notificaciones de Diarios',
        'daily_desc'   => 'Reciba recordatorios y actualizaciones sobre diarios.',
        'budget'       => 'Alertas de Presupuesto',
        'budget_desc'  => 'Reciba notificaciones cuando se excedan los umbrales.',
        'submittals'   => 'Notificaciones de Submittals',
        'submittals_desc' => 'Reciba actualizaciones sobre cambios de estado.',
        'inspections'  => 'Notificaciones de Inspecciones',
        'inspections_desc' => 'Reciba notificaciones sobre inspecciones próximas y completadas.',
        'save'         => 'Guardar Preferencias',
        'saved'        => 'Preferencias guardadas correctamente.',
    ],
];
$l = $labels[$lang] ?? $labels['en'];

$flash_success = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);
?>
<style>
.modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 40; }
.modal-backdrop.open { display: flex; align-items: center; justify-content: center; }
</style>

<?php if ($flash_success): ?>
<div class="mb-4 rounded-md bg-success bg-opacity-10 border border-success border-opacity-20 p-4">
    <p class="text-sm font-medium text-success"><?php echo htmlspecialchars($flash_success); ?></p>
</div>
<?php endif; ?>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $l['title']; ?></h2>
    <p class="text-slate-500"><?php echo $l['subtitle']; ?></p>
</div>

<div class="card">
    <form method="POST" action="index.php?page=notification_prefs_handler&lang=<?php echo $lang; ?>">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="action" value="save_prefs">

        <div class="divide-y divide-stroke">
            <!-- RFIs -->
            <div class="flex items-center justify-between py-5">
                <div>
                    <p class="text-sm font-bold text-black"><?php echo $l['rfis']; ?></p>
                    <p class="text-xs text-slate-500"><?php echo $l['rfis_desc']; ?></p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="hidden" name="email_rfis" value="0">
                    <input type="checkbox" name="email_rfis" value="1" <?php echo !empty($prefs['email_rfis']) ? 'checked' : ''; ?> class="sr-only peer">
                    <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary peer-checked:after:translate-x-full"></div>
                </label>
            </div>

            <!-- Daily Logs -->
            <div class="flex items-center justify-between py-5">
                <div>
                    <p class="text-sm font-bold text-black"><?php echo $l['daily_logs']; ?></p>
                    <p class="text-xs text-slate-500"><?php echo $l['daily_desc']; ?></p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="hidden" name="email_daily_logs" value="0">
                    <input type="checkbox" name="email_daily_logs" value="1" <?php echo !empty($prefs['email_daily_logs']) ? 'checked' : ''; ?> class="sr-only peer">
                    <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary peer-checked:after:translate-x-full"></div>
                </label>
            </div>

            <!-- Budget Alerts -->
            <div class="flex items-center justify-between py-5">
                <div>
                    <p class="text-sm font-bold text-black"><?php echo $l['budget']; ?></p>
                    <p class="text-xs text-slate-500"><?php echo $l['budget_desc']; ?></p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="hidden" name="email_budget_alerts" value="0">
                    <input type="checkbox" name="email_budget_alerts" value="1" <?php echo !empty($prefs['email_budget_alerts']) ? 'checked' : ''; ?> class="sr-only peer">
                    <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary peer-checked:after:translate-x-full"></div>
                </label>
            </div>

            <!-- Submittals -->
            <div class="flex items-center justify-between py-5">
                <div>
                    <p class="text-sm font-bold text-black"><?php echo $l['submittals']; ?></p>
                    <p class="text-xs text-slate-500"><?php echo $l['submittals_desc']; ?></p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="hidden" name="email_submittals" value="0">
                    <input type="checkbox" name="email_submittals" value="1" <?php echo !empty($prefs['email_submittals']) ? 'checked' : ''; ?> class="sr-only peer">
                    <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary peer-checked:after:translate-x-full"></div>
                </label>
            </div>

            <!-- Inspections -->
            <div class="flex items-center justify-between py-5">
                <div>
                    <p class="text-sm font-bold text-black"><?php echo $l['inspections']; ?></p>
                    <p class="text-xs text-slate-500"><?php echo $l['inspections_desc']; ?></p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="hidden" name="email_inspections" value="0">
                    <input type="checkbox" name="email_inspections" value="1" <?php echo !empty($prefs['email_inspections']) ? 'checked' : ''; ?> class="sr-only peer">
                    <div class="peer h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary peer-checked:after:translate-x-full"></div>
                </label>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="rounded bg-primary py-3 px-8 font-bold text-white hover:bg-opacity-90">
                <?php echo $l['save']; ?>
            </button>
        </div>
    </form>
</div>
