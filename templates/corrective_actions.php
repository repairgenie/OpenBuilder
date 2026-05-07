<!-- templates/corrective_actions.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Acciones Correctivas' : 'Corrective Actions'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Seguimiento de resolución de fallas de inspección.' : 'Tracking resolution of inspection failures.'; ?></p>
</div>

<div class="card p-0 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600">ID</th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Descripción' : 'Description'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Vencimiento' : 'Due Date'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Verificación' : 'Verification'; ?></th>
                </tr>
            </thead>
            <tbody>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4 font-bold text-black">CA-402</td>
                    <td class="py-4 px-4">
                        <p class="text-sm font-bold text-black">Repair cracked curb at Sector C</p>
                        <p class="text-[10px] text-slate-400">Assigned: Concrete Sub</p>
                    </td>
                    <td class="py-4 px-4 text-xs">Nov 05, 2023</td>
                    <td class="py-4 px-4">
                        <button class="px-4 py-1 bg-primary text-white text-[10px] font-bold rounded uppercase"><?php echo $lang === 'es' ? 'Verificar' : 'Verify'; ?></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
