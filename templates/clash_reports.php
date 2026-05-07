<!-- templates/clash_reports.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Detección de Conflictos' : 'Clash Detection'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Conflictos geométricos identificados en el modelo coordinado.' : 'Geometric conflicts identified in the coordinated model.'; ?></p>
</div>

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <!-- Clash Card -->
    <div class="card bg-white border-l-4 border-danger">
        <div class="flex items-center justify-between mb-4">
            <span class="px-2 py-1 bg-danger bg-opacity-10 text-danger text-[10px] font-bold rounded uppercase">Active</span>
            <span class="text-xs text-slate-400">ID: CL-042</span>
        </div>
        <h3 class="text-lg font-bold text-black mb-1">HVAC Duct vs. Structural Beam</h3>
        <p class="text-xs text-slate-500 mb-4">Location: Level 2, Sector B-4</p>
        
        <div class="flex items-center gap-2 mb-4">
            <div class="h-10 w-10 bg-slate-100 rounded flex items-center justify-center text-slate-400">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
            </div>
            <div class="text-xs">
                <p class="font-bold text-black">Conflict Depth: 4.5cm</p>
                <p class="text-slate-400">Assigned: John Doe</p>
            </div>
        </div>
        
        <button class="w-full text-center py-2 text-sm font-bold text-primary hover:underline border-t border-stroke mt-2">
            <?php echo $lang === 'es' ? 'Ver en el Modelo' : 'View in Model'; ?>
        </button>
    </div>
</div>
