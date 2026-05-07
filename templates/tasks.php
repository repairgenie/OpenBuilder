<!-- templates/tasks.php -->
<?php
$tasks = [
    ['title_en' => 'Review structural drawings', 'title_es' => 'Revisar planos estructurales', 'due' => '2023-10-25', 'priority' => 'High'],
    ['title_en' => 'Order concrete for Sector B', 'title_es' => 'Pedir concreto para Sector B', 'due' => '2023-10-26', 'priority' => 'Medium'],
    ['title_en' => 'Safety walk-through', 'title_es' => 'Recorrido de seguridad', 'due' => '2023-10-27', 'priority' => 'High'],
];
?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Tareas del Proyecto' : 'Project Tasks'; ?></h2>
    <button class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90 shadow-md transition-all">
        <?php echo $lang === 'es' ? 'Nueva Tarea' : 'New Task'; ?>
    </button>
</div>

<div class="card">
    <ul class="divide-y divide-stroke">
        <?php foreach ($tasks as $t): ?>
        <li class="py-4 flex items-center justify-between hover:bg-slate-50 px-4 -mx-4 transition-colors">
            <div class="flex items-center gap-4">
                <input type="checkbox" class="h-5 w-5 rounded border-stroke text-primary focus:ring-primary">
                <div>
                    <p class="text-sm font-bold text-black"><?php echo $lang === 'es' ? $t['title_es'] : $t['title_en']; ?></p>
                    <p class="text-xs text-slate-500"><?php echo $lang === 'es' ? 'Vence: ' : 'Due: '; ?><?php echo $t['due']; ?></p>
                </div>
            </div>
            <span class="text-xs font-bold uppercase <?php echo $t['priority'] === 'High' ? 'text-danger' : 'text-warning'; ?>">
                <?php echo $t['priority']; ?>
            </span>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
