<!-- templates/crew_management.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Gestión de Cuadrillas' : 'Crew Management'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Asignación diaria de personal a actividades de obra.' : 'Daily assignment of personnel to site activities.'; ?></p>
    </div>
    <button class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
        <?php echo $lang === 'es' ? 'Nueva Cuadrilla' : 'New Crew'; ?>
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Crew Card -->
    <div class="card bg-white">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-black text-sm">Concrete Crew A</h4>
            <span class="px-2 py-0.5 bg-success bg-opacity-10 text-success text-[10px] font-bold rounded uppercase">On Site</span>
        </div>
        
        <div class="flex -space-x-2 mb-4">
            <div class="h-8 w-8 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-[10px] text-slate-500">JD</div>
            <div class="h-8 w-8 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-[10px] text-slate-500">JS</div>
            <div class="h-8 w-8 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-[10px] text-slate-500">RB</div>
            <div class="h-8 w-8 rounded-full bg-primary border-2 border-white flex items-center justify-center text-[10px] text-white font-bold">+5</div>
        </div>
        
        <div class="p-3 bg-slate-50 rounded text-xs text-slate-600 mb-4">
            <strong>Current Task:</strong> Pouring Level 2 Deck
        </div>
        
        <button class="w-full py-2 border border-stroke text-slate-600 text-xs font-bold rounded hover:bg-slate-50">
            <?php echo $lang === 'es' ? 'Asignar Tarea' : 'Assign Task'; ?>
        </button>
    </div>
</div>
