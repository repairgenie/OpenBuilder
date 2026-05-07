<!-- templates/resource_management.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Equilibrio de Recursos' : 'Resource Balancing'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Optimizar la asignación de personal clave entre proyectos.' : 'Optimize the allocation of key personnel across projects.'; ?></p>
</div>

<div class="card p-0 overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-slate-50 border-b border-stroke">
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Personal' : 'Personnel'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Proyecto Actual' : 'Current Project'; ?></th>
                <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Carga de Trabajo' : 'Workload'; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-stroke">
                <td class="py-4 px-4">
                    <p class="font-bold text-black">John Doe</p>
                    <p class="text-xs text-slate-500">Superintendent</p>
                </td>
                <td class="py-4 px-4 text-sm">Hotel Plaza Extension</td>
                <td class="py-4 px-4">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-24 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-danger w-[95%]"></div>
                        </div>
                        <span class="text-[10px] font-bold text-danger">95%</span>
                    </div>
                </td>
            </tr>
            <tr class="border-b border-stroke">
                <td class="py-4 px-4">
                    <p class="font-bold text-black">Jane Smith</p>
                    <p class="text-xs text-slate-500">Safety Officer</p>
                </td>
                <td class="py-4 px-4 text-sm">Downtown Office Park</td>
                <td class="py-4 px-4">
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-24 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-primary w-[60%]"></div>
                        </div>
                        <span class="text-[10px] font-bold text-primary">60%</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
