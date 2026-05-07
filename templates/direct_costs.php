<!-- templates/direct_costs.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Costos Directos' : 'Direct Costs'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Seguimiento de gastos internos y materiales de campo.' : 'Tracking internal expenses and field materials.'; ?></p>
    </div>
    <button class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
        <?php echo $lang === 'es' ? 'Nuevo Gasto' : 'New Expense'; ?>
    </button>
</div>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 border-b border-stroke">
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Fecha' : 'Date'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Descripción' : 'Description'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Monto' : 'Amount'; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-stroke">
                <td class="py-4 px-4 text-xs">Nov 02, 2023</td>
                <td class="py-4 px-4">
                    <p class="text-sm font-bold text-black">Site Office Supplies</p>
                    <p class="text-[10px] text-slate-400">Paid to: OfficeMax</p>
                </td>
                <td class="py-4 px-4 text-black font-bold">$125.40</td>
            </tr>
        </tbody>
    </table>
</div>
