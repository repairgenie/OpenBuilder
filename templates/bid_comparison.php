<!-- templates/bid_comparison.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Comparación de Ofertas' : 'Bid Comparison'; ?></h2>
    <p class="text-slate-500">Package: Drywall & Framing</p>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Proveedor' : 'Vendor'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Monto Base' : 'Base Bid'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Calificación' : 'Rating'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-stroke bg-success bg-opacity-5">
                    <td class="py-4 px-4 font-bold text-black">A-1 Drywall</td>
                    <td class="py-4 px-4 text-black font-bold">$125,000.00</td>
                    <td class="py-4 px-4 text-warning">★★★★★</td>
                    <td class="py-4 px-4"><span class="text-xs font-bold text-success uppercase">Lowest Bid</span></td>
                </tr>
                <tr class="border-b border-stroke">
                    <td class="py-4 px-4 font-bold text-black">Precision Framing</td>
                    <td class="py-4 px-4 text-black">$142,500.00</td>
                    <td class="py-4 px-4 text-warning">★★★★☆</td>
                    <td class="py-4 px-4"><span class="text-xs font-bold text-slate-400 uppercase">Submitted</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
