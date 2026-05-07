<!-- templates/users.php -->
<?php
$users = [
    ['name' => 'John Doe', 'email' => 'john@openbuilder.com', 'role_en' => 'Project Manager', 'role_es' => 'Gerente de Proyecto', 'status' => 'Active'],
    ['name' => 'Jane Smith', 'email' => 'jane@openbuilder.com', 'role_en' => 'Architect', 'role_es' => 'Arquitecto', 'status' => 'Active'],
    ['name' => 'Robert Fox', 'email' => 'robert@sub.com', 'role_en' => 'Subcontractor', 'role_es' => 'Subcontratista', 'status' => 'Pending'],
    ['name' => 'Maria Garcia', 'email' => 'maria@openbuilder.com', 'role_en' => 'Foreman', 'role_es' => 'Capataz', 'status' => 'Active'],
];
?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-2xl font-bold text-black">
        <?php echo $lang === 'es' ? 'Gestión de Usuarios' : 'User Management'; ?>
    </h2>
    <button class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90 shadow-md transition-all active:scale-95">
        <?php echo $lang === 'es' ? 'Añadir Usuario' : 'Add User'; ?>
    </button>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Nombre' : 'Name'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Rol' : 'Role'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600 text-right"><?php echo $lang === 'es' ? 'Acción' : 'Action'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($u['name']); ?>&background=random" class="h-10 w-10 rounded-full" alt="">
                            <div>
                                <p class="text-sm font-bold text-black"><?php echo $u['name']; ?></p>
                                <p class="text-xs text-slate-500"><?php echo $u['email']; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-sm font-medium">
                        <?php echo $lang === 'es' ? $u['role_es'] : $u['role_en']; ?>
                    </td>
                    <td class="py-4 px-4">
                        <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold <?php echo $u['status'] === 'Active' ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning'; ?>">
                            <?php echo $u['status']; ?>
                        </span>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <button class="text-primary hover:underline text-sm font-medium"><?php echo $lang === 'es' ? 'Editar' : 'Edit'; ?></button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
