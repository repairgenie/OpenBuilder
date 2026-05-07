<!-- templates/submittals.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Submittals (Entregables)' : 'Submittals'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestión del flujo de aprobación de materiales y especificaciones.' : 'Management of material and specification approval workflows.'; ?></p>
    </div>
    <div class="flex gap-2">
        <div class="inline-flex rounded-md shadow-sm" role="group">
            <button class="px-4 py-2 text-sm font-medium text-primary bg-white border border-stroke rounded-l-md hover:bg-slate-50">Items</button>
            <button class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border-t border-b border-r border-stroke rounded-r-md hover:bg-slate-50">Packages</button>
        </div>
        <button class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Nuevo Submittal' : 'New Submittal'; ?>
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
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Responsable' : 'Ball In Court'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 text-black">SUB-012</td>
                    <td class="py-4 px-4">
                        <p class="font-bold text-black">Structural Steel Shop Drawings</p>
                        <p class="text-[10px] text-slate-400">Spec: 05-1200</p>
                    </td>
                    <td class="py-4 px-4">
                        <div class="flex items-center gap-2">
                            <div class="h-6 w-6 rounded-full bg-primary flex items-center justify-center text-[10px] text-white font-bold">AS</div>
                            <span class="text-xs text-black">Architect Smith</span>
                        </div>
                    </td>
                    <td class="py-4 px-4"><span class="px-2 py-1 bg-warning bg-opacity-10 text-warning text-[10px] font-bold rounded uppercase">Under Review</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
