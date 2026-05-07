<!-- templates/punch_list_v2.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Lista de Pendientes (Punch List)' : 'Punch List'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Captura rápida y resolución de deficiencias finales.' : 'Rapid capture and resolution of final deficiencies.'; ?></p>
    </div>
    <button class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90 shadow-md">
        <?php echo $lang === 'es' ? 'Nueva Deficiencia' : 'New Deficiency'; ?>
    </button>
</div>

<!-- Rapid Capture Interface -->
<div class="card mb-6 border-2 border-primary border-opacity-20 bg-primary bg-opacity-5">
    <div class="flex flex-col md:flex-row gap-4 items-end">
        <div class="flex-1">
            <label class="mb-2 block text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'Descripción Rápida' : 'Quick Description'; ?></label>
            <input type="text" placeholder="e.g. Paint touch up needed near door" class="w-full rounded border-[1.5px] border-stroke py-3 px-5 outline-none focus:border-primary">
        </div>
        <div class="w-full md:w-48">
            <label class="mb-2 block text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'Ubicación' : 'Location'; ?></label>
            <select class="w-full rounded border-[1.5px] border-stroke py-3 px-5 outline-none focus:border-primary">
                <option>Level 1, Lobby</option>
                <option>Level 2, Corridor</option>
            </select>
        </div>
        <button class="bg-primary text-white p-3 rounded font-bold hover:bg-opacity-90">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        </button>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 border-b border-stroke">
                <th class="py-4 px-4 font-semibold text-slate-600">ID</th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Deficiencia' : 'Deficiency'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                <td class="py-4 px-4 text-xs font-bold">PL-001</td>
                <td class="py-4 px-4">
                    <p class="text-sm font-bold text-black">Scuff marks on north wall</p>
                    <p class="text-[10px] text-slate-400">Assigned: Painter • Level 2</p>
                </td>
                <td class="py-4 px-4"><span class="px-2 py-1 bg-warning bg-opacity-10 text-warning text-[10px] font-bold rounded uppercase">In Review</span></td>
            </tr>
        </tbody>
    </table>
</div>
