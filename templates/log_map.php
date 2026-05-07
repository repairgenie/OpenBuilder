<!-- templates/log_map.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Mapa de Trabajo Diario' : 'Daily Work Map'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Visualización de las áreas de trabajo activas hoy.' : 'Visualization of active work areas today.'; ?></p>
</div>

<div class="card relative bg-slate-100 p-0 overflow-hidden aspect-video flex items-center justify-center border-dashed border-2 border-slate-300">
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/graphy.png')]"></div>
    
    <!-- Active Zones -->
    <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-primary bg-opacity-20 border-2 border-primary border-dashed rounded flex items-center justify-center">
        <span class="text-[10px] font-bold text-primary uppercase">Sector A</span>
    </div>
    <div class="absolute top-1/2 left-1/2 w-48 h-24 bg-success bg-opacity-20 border-2 border-success border-dashed rounded flex items-center justify-center">
        <span class="text-[10px] font-bold text-success uppercase">Sector B</span>
    </div>

    <div class="z-10 text-slate-400 font-bold uppercase tracking-widest pointer-events-none">
        <?php echo $lang === 'es' ? 'Plano de Sitio (Simulación)' : 'Site Plan (Simulation)'; ?>
    </div>
</div>

<div class="mt-6 card">
    <h3 class="text-sm font-bold text-black mb-4"><?php echo $lang === 'es' ? 'Leyenda' : 'Legend'; ?></h3>
    <div class="flex gap-6">
        <div class="flex items-center gap-2">
            <span class="h-3 w-3 bg-primary bg-opacity-20 border border-primary border-dashed rounded"></span>
            <span class="text-xs text-slate-600"><?php echo $lang === 'es' ? 'Estructura' : 'Framing'; ?></span>
        </div>
        <div class="flex items-center gap-2">
            <span class="h-3 w-3 bg-success bg-opacity-20 border border-success border-dashed rounded"></span>
            <span class="text-xs text-slate-600"><?php echo $lang === 'es' ? 'Acabados' : 'Finishing'; ?></span>
        </div>
    </div>
</div>
