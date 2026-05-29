<!-- templates/create_cost_code.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-2xl font-bold text-black">
        <?php echo $lang === 'es' ? 'Nuevo Código de Costo' : 'New Cost Code'; ?>
    </h2>
</div>

<div class="card">
    <form action="index.php?lang=<?php echo $lang; ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="action" value="create_cost_code">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Código (ej. 03-3000)' : 'Code (e.g. 03-3000)'; ?></label>
                <input type="text" name="code" placeholder="00-0000" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary" required>
            </div>
            <div>
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Nombre / Descripción' : 'Name / Description'; ?></label>
                <input type="text" name="name" placeholder="Name" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary" required>
            </div>
            <div>
                <label class="mb-3 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Presupuesto Original' : 'Original Budget'; ?></label>
                <input type="number" name="original_budget" placeholder="0.00" step="0.01" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary" required>
            </div>
        </div>
        <div class="mt-6 flex justify-end gap-4">
            <a href="?page=budget&lang=<?php echo $lang; ?>" class="rounded border border-stroke py-2 px-6 font-medium text-black hover:shadow-md transition-all"><?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?></a>
            <button type="submit" class="rounded bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90 shadow-md transition-all"><?php echo $lang === 'es' ? 'Crear Código' : 'Create Code'; ?></button>
        </div>
    </form>
</div>
