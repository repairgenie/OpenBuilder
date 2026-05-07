<!-- templates/labor_productivity.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Productividad Laboral' : 'Labor Productivity'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Seguimiento de horas hombre vs avance físico.' : 'Tracking man-hours vs physical progress.'; ?></p>
</div>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 border-b border-stroke">
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Código de Costo' : 'Cost Code'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Presupuestado (Hrs)' : 'Budgeted (Hrs)'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Real (Hrs)' : 'Actual (Hrs)'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Eficiencia' : 'Efficiency'; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-stroke">
                <td class="py-4 px-4 font-bold text-black">03-300 Concrete</td>
                <td class="py-4 px-4 text-sm text-slate-500">1,200</td>
                <td class="py-4 px-4 text-sm text-black">850</td>
                <td class="py-4 px-4"><span class="px-2 py-1 bg-success bg-opacity-10 text-success text-[10px] font-bold rounded uppercase">1.41 (High)</span></td>
            </tr>
            <tr class="border-b border-stroke">
                <td class="py-4 px-4 font-bold text-black">09-200 Drywall</td>
                <td class="py-4 px-4 text-sm text-slate-500">800</td>
                <td class="py-4 px-4 text-sm text-black">920</td>
                <td class="py-4 px-4"><span class="px-2 py-1 bg-danger bg-opacity-10 text-danger text-[10px] font-bold rounded uppercase">0.87 (Low)</span></td>
            </tr>
        </tbody>
    </table>
</div>
