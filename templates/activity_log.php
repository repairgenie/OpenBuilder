<!-- templates/activity_log.php -->
<?php
$activities = [
    ['user' => 'John Doe', 'action_en' => 'Created RFI #003', 'action_es' => 'Creó RFI #003', 'time' => '2 hours ago'],
    ['user' => 'Jane Smith', 'action_en' => 'Approved Budget for Sector A', 'action_es' => 'Aprobó Presupuesto para Sector A', 'time' => '4 hours ago'],
    ['user' => 'Maria Garcia', 'action_en' => 'Submitted Daily Log (Oct 24)', 'action_es' => 'Envió Diario (24 Oct)', 'time' => '1 day ago'],
];
?>
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Registro de Actividad' : 'Activity Log'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Historial completo de cambios en el proyecto.' : 'Full history of changes in the project.'; ?></p>
</div>

<div class="card">
    <div class="flow-root">
        <ul role="list" class="-mb-8">
            <?php foreach ($activities as $index => $a): ?>
            <li>
                <div class="relative pb-8">
                    <?php if ($index !== count($activities) - 1): ?>
                    <span class="absolute left-5 top-5 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                    <?php endif; ?>
                    <div class="relative flex items-start space-x-3">
                        <div class="relative">
                            <img class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 ring-8 ring-white" src="https://ui-avatars.com/api/?name=<?php echo urlencode($a['user']); ?>&background=random" alt="">
                        </div>
                        <div class="min-w-0 flex-1 py-1.5">
                            <div class="text-sm text-slate-500">
                                <span class="font-bold text-black"><?php echo $a['user']; ?></span>
                                <?php echo $lang === 'es' ? $a['action_es'] : $a['action_en']; ?>
                                <span class="whitespace-nowrap ml-2 text-xs font-medium"><?php echo $a['time']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
