<!-- templates/audit_logs.php -->
<?php
$audit_logs = [
    ['user' => 'Admin', 'action_en' => 'Changed Project Name', 'action_es' => 'Cambió el nombre del proyecto', 'ip' => '192.168.1.1', 'time' => 'Oct 25, 10:30 AM'],
    ['user' => 'Admin', 'action_en' => 'Updated User Permissions (John Doe)', 'action_es' => 'Actualizó permisos de usuario (John Doe)', 'ip' => '192.168.1.1', 'time' => 'Oct 25, 09:15 AM'],
    ['user' => 'System', 'action_en' => 'Automated Database Backup', 'action_es' => 'Copia de seguridad automática', 'ip' => 'localhost', 'time' => 'Oct 25, 02:00 AM'],
];
?>
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Registros de Auditoría' : 'Audit Logs'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Seguimiento de acciones críticas de administración.' : 'Track critical administrative actions.'; ?></p>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Usuario' : 'User'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Acción' : 'Action'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600">IP</th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Fecha/Hora' : 'Date/Time'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($audit_logs as $log): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 text-sm font-bold text-black"><?php echo $log['user']; ?></td>
                    <td class="py-4 px-4 text-sm text-slate-600"><?php echo $lang === 'es' ? $log['action_es'] : $log['action_en']; ?></td>
                    <td class="py-4 px-4 text-sm text-slate-500"><?php echo $log['ip']; ?></td>
                    <td class="py-4 px-4 text-sm text-slate-500"><?php echo $log['time']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
