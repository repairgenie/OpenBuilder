<!-- templates/change_management.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Gestión de Cambios' : 'Change Management'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Rastrear eventos de cambio y órdenes de cambio financieras.' : 'Track change events and financial change orders.'; ?></p>
    </div>
    <div class="flex gap-2">
        <div class="inline-flex rounded-md shadow-sm" role="group">
            <button class="px-4 py-2 text-sm font-medium text-primary bg-white border border-stroke rounded-l-md hover:bg-slate-50">PCO</button>
            <button class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border-t border-b border-stroke hover:bg-slate-50">PCCO</button>
            <button class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border-t border-b border-r border-stroke rounded-r-md hover:bg-slate-50">CCO</button>
        </div>
        <button class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Nuevo PCO' : 'New PCO'; ?>
        </button>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600">ID</th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Título' : 'Title'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Monto' : 'Amount'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-stroke hover:bg-slate-50 cursor-pointer">
                    <td class="py-4 px-4 text-black">PCO-001</td>
                    <td class="py-4 px-4">
                        <p class="font-bold text-black">Foundation Redesign</p>
                        <p class="text-xs text-slate-500">Related to RFI #012</p>
                    </td>
                    <td class="py-4 px-4 text-danger font-bold">$12,450.00</td>
                    <td class="py-4 px-4"><span class="px-2 py-1 bg-warning bg-opacity-10 text-warning text-xs font-bold rounded uppercase">Pending</span></td>
                </tr>
                <tr class="border-b border-stroke hover:bg-slate-50 cursor-pointer">
                    <td class="py-4 px-4 text-black">PCO-002</td>
                    <td class="py-4 px-4">
                        <p class="font-bold text-black">Electrical Upgrade - Lobby</p>
                        <p class="text-xs text-slate-500">Owner Requested</p>
                    </td>
                    <td class="py-4 px-4 text-black font-bold">$8,200.00</td>
                    <td class="py-4 px-4"><span class="px-2 py-1 bg-success bg-opacity-10 text-success text-xs font-bold rounded uppercase">Approved</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
