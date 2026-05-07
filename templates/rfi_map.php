<!-- templates/rfi_map.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Mapa de RFIs' : 'RFI Map'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Localización espacial de las consultas en el plano.' : 'Spatial location of queries on the floor plan.'; ?></p>
</div>

<div class="card relative bg-slate-100 p-0 overflow-hidden aspect-video flex items-center justify-center border-dashed border-2 border-slate-300">
    <!-- Simulated Floor Plan -->
    <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/graphy.png')]"></div>
    
    <!-- Pins -->
    <div onclick="window.location.href='?page=view_rfi&id=1&lang=<?php echo $lang; ?>'" class="absolute top-1/4 left-1/3 h-6 w-6 bg-danger rounded-full border-2 border-white cursor-pointer hover:scale-125 transition-all shadow-lg animate-bounce" data-tooltip="RFI #001: Slab Cracks"></div>
    <div onclick="window.location.href='?page=view_rfi&id=2&lang=<?php echo $lang; ?>'" class="absolute top-1/2 left-2/3 h-6 w-6 bg-warning rounded-full border-2 border-white cursor-pointer hover:scale-125 transition-all shadow-lg" data-tooltip="RFI #002: Conduit Conflict"></div>

    <div class="z-10 text-slate-400 font-bold uppercase tracking-widest pointer-events-none">
        <?php echo $lang === 'es' ? 'Plano de Planta (Simulación)' : 'Floor Plan (Simulation)'; ?>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="card p-4 flex items-center gap-3">
        <span class="h-3 w-3 rounded-full bg-danger"></span>
        <span class="text-sm font-medium"><?php echo $lang === 'es' ? 'Alta Prioridad' : 'High Priority'; ?></span>
    </div>
    <div class="card p-4 flex items-center gap-3">
        <span class="h-3 w-3 rounded-full bg-warning"></span>
        <span class="text-sm font-medium"><?php echo $lang === 'es' ? 'Abierto' : 'Open'; ?></span>
    </div>
    <div class="card p-4 flex items-center gap-3">
        <span class="h-3 w-3 rounded-full bg-success"></span>
        <span class="text-sm font-medium"><?php echo $lang === 'es' ? 'Cerrado' : 'Closed'; ?></span>
    </div>
</div>
