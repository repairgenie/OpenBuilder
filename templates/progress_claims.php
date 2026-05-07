<!-- templates/progress_claims.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Reclamos de Progreso (Invoices)' : 'Progress Claims (Invoices)'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Facturación mensual y pagos a subcontratistas.' : 'Monthly billing and subcontractor payments.'; ?></p>
</div>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 border-b border-stroke">
                <th class="py-4 px-4 font-semibold text-slate-600">ID</th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Proveedor' : 'Vendor'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Monto' : 'Amount'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                <td class="py-4 px-4 text-black">INV-012</td>
                <td class="py-4 px-4 text-sm">SteelWorks LLC</td>
                <td class="py-4 px-4 text-black font-bold">$42,500.00</td>
                <td class="py-4 px-4"><span class="px-2 py-1 bg-warning bg-opacity-10 text-warning text-[10px] font-bold rounded uppercase">Pending Approval</span></td>
            </tr>
        </tbody>
    </table>
</div>
