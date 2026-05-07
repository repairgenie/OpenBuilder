<!-- templates/submittal_schedule.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Cronograma de Submittals' : 'Submittal Schedule'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Previsión de tiempos de entrega y fechas críticas de aprobación.' : 'Forecasting lead times and critical approval dates.'; ?></p>
</div>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 border-b border-stroke">
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Ítem' : 'Item'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Días de Entrega' : 'Lead Time'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Fecha Requerida' : 'Date Needed'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-stroke">
                <td class="py-4 px-4 font-bold text-black">Structural Steel</td>
                <td class="py-4 px-4 text-xs text-danger font-bold">12 Weeks</td>
                <td class="py-4 px-4 text-xs">Jan 15, 2024</td>
                <td class="py-4 px-4"><span class="px-2 py-1 bg-danger bg-opacity-10 text-danger text-[10px] font-bold rounded uppercase">At Risk</span></td>
            </tr>
            <tr class="border-b border-stroke">
                <td class="py-4 px-4 font-bold text-black">HVAC Chillers</td>
                <td class="py-4 px-4 text-xs">8 Weeks</td>
                <td class="py-4 px-4 text-xs">Feb 20, 2024</td>
                <td class="py-4 px-4"><span class="px-2 py-1 bg-success bg-opacity-10 text-success text-[10px] font-bold rounded uppercase">On Track</span></td>
            </tr>
        </tbody>
    </table>
</div>
