<!-- templates/budget_reallocation.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Reasignación de Presupuesto' : 'Budget Reallocation'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Transferir fondos entre códigos de costo.' : 'Transfer funds between cost codes.'; ?></p>
</div>

<div class="card max-w-xl">
    <form class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Origen' : 'From'; ?></label>
                <select class="w-full rounded border-[1.5px] border-stroke py-2 px-4 outline-none focus:border-primary">
                    <option>03-300 Concrete</option>
                    <option>09-200 Drywall</option>
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Destino' : 'To'; ?></label>
                <select class="w-full rounded border-[1.5px] border-stroke py-2 px-4 outline-none focus:border-primary">
                    <option>26-000 Electrical</option>
                    <option>22-000 Plumbing</option>
                </select>
            </div>
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Monto a Transferir' : 'Transfer Amount'; ?></label>
            <input type="number" class="w-full rounded border-[1.5px] border-stroke py-2 px-4 outline-none focus:border-primary">
        </div>
        <button type="button" onclick="window.showToast('Budget Reallocated!', 'success')" class="w-full rounded bg-black py-3 font-bold text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Confirmar Transferencia' : 'Confirm Transfer'; ?>
        </button>
    </form>
</div>
