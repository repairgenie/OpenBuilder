<!-- templates/audit_logs.php -->
<?php
require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/security_helper.php';

$pdo = Database::connect();

// Fetch audit logs from activity_logs table, newest first
$stmt = $pdo->query("
    SELECT al.*, u.name as user_name
    FROM activity_logs al
    LEFT JOIN users u ON al.user_id = u.id
    ORDER BY al.created_at DESC
    LIMIT 100
");
$audit_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [
    'en' => [
        'title'    => 'Audit Logs',
        'subtitle' => 'Track critical administrative actions.',
        'user'     => 'User',
        'action'   => 'Action',
        'module'   => 'Module',
        'ip'       => 'IP',
        'datetime' => 'Date/Time',
        'no_logs'  => 'No audit logs found.',
        'system'   => 'System',
    ],
    'es' => [
        'title'    => 'Registros de Auditoría',
        'subtitle' => 'Seguimiento de acciones críticas de administración.',
        'user'     => 'Usuario',
        'action'   => 'Acción',
        'module'   => 'Módulo',
        'ip'       => 'IP',
        'datetime' => 'Fecha/Hora',
        'no_logs'  => 'No se encontraron registros.',
        'system'   => 'Sistema',
    ],
];
$l = $labels[$lang] ?? $labels['en'];
?>
<style>
.modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 40; }
.modal-backdrop.open { display: flex; align-items: center; justify-content: center; }
</style>

<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $l['title']; ?></h2>
    <p class="text-slate-500"><?php echo $l['subtitle']; ?></p>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['user']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['action']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['module']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['ip']; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $l['datetime']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($audit_logs)): ?>
                <tr>
                    <td colspan="5" class="py-8 text-center text-slate-400"><?php echo $l['no_logs']; ?></td>
                </tr>
                <?php endif; ?>
                <?php foreach ($audit_logs as $log): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 text-sm font-bold text-black">
                        <?php echo htmlspecialchars($log['user_name'] ?? ($log['username'] ?? $l['system'])); ?>
                    </td>
                    <td class="py-4 px-4 text-sm text-slate-600">
                        <?php echo htmlspecialchars($lang === 'es' ? ($log['action_es'] ?? $log['action_en']) : $log['action_en']); ?>
                    </td>
                    <td class="py-4 px-4 text-sm text-slate-500">
                        <?php echo htmlspecialchars($log['module'] ?? '-'); ?>
                    </td>
                    <td class="py-4 px-4 text-sm text-slate-500">
                        <?php echo htmlspecialchars($log['ip_address'] ?? '-'); ?>
                    </td>
                    <td class="py-4 px-4 text-sm text-slate-500">
                        <?php echo htmlspecialchars($log['created_at'] ?? '-'); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
