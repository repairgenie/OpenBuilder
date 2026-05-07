<!-- templates/funding_sources.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Fuentes de Financiamiento' : 'Funding Sources'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestión de subvenciones, préstamos y capital del propietario.' : 'Management of grants, loans, and owner equity.'; ?></p>
    </div>
    <button class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
        <?php echo $lang === 'es' ? 'Nueva Fuente' : 'New Source'; ?>
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Funding Source Card -->
    <div class="card bg-white">
        <div class="flex items-center justify-between mb-4">
            <h4 class="font-bold text-black text-sm">Commercial Construction Loan</h4>
            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded uppercase">Active</span>
        </div>
        
        <div class="space-y-2 mb-6">
            <div class="flex justify-between text-xs"><span>Total Facility</span><span class="font-bold text-black">$5,000,000</span></div>
            <div class="flex justify-between text-xs"><span>Drawn Amount</span><span class="font-bold text-primary">$1,245,000</span></div>
            <div class="h-1 w-full bg-slate-100 rounded-full overflow-hidden mt-1">
                <div class="h-full bg-primary w-[25%]"></div>
            </div>
        </div>
        
        <button class="w-full py-2 bg-slate-100 text-slate-600 text-xs font-bold rounded hover:bg-slate-200">
            <?php echo $lang === 'es' ? 'Ver Detalles de Giro' : 'View Draw Details'; ?>
        </button>
    </div>
</div>
