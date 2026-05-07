<!-- templates/commitments.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Compromisos (Contratos/PO)' : 'Commitments (Contracts/POs)'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestión de contratos con subcontratistas y órdenes de compra.' : 'Management of subcontractor contracts and purchase orders.'; ?></p>
    </div>
    <button class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
        <?php echo $lang === 'es' ? 'Nuevo Compromiso' : 'New Commitment'; ?>
    </button>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600">ID</th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Título' : 'Title'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Proveedor' : 'Vendor'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Monto' : 'Amount'; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 text-black">SC-001</td>
                    <td class="py-4 px-4">
                        <p class="font-bold text-black">Structural Steel Fabrication</p>
                        <p class="text-[10px] text-slate-400">Type: Subcontract</p>
                    </td>
                    <td class="py-4 px-4 text-xs">SteelWorks LLC</td>
                    <td class="py-4 px-4 text-black font-bold">$245,000.00</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
