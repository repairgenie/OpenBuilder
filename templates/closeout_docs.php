<!-- templates/closeout_docs.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Documentación de Cierre' : 'Closeout Documentation'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestión de garantías, manuales y planos conforme a obra.' : 'Management of warranties, manuals, and as-builts.'; ?></p>
    </div>
    <button class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
        <?php echo $lang === 'es' ? 'Solicitar Documentos' : 'Request Documents'; ?>
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <!-- Closeout Card -->
    <div class="card bg-white hover-scale">
        <div class="flex items-center justify-between mb-4">
            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded uppercase"><?php echo $lang === 'es' ? 'Garantía' : 'Warranty'; ?></span>
            <span class="text-xs text-danger font-bold uppercase">Missing</span>
        </div>
        <h3 class="text-lg font-bold text-black mb-1">HVAC System Warranty</h3>
        <p class="text-xs text-slate-500 mb-4">Subcontractor: AirTech Inc.</p>
        
        <div class="border-t border-stroke pt-4 flex justify-between items-center">
            <span class="text-[10px] text-slate-400">Due: Dec 31</span>
            <button class="text-xs font-bold text-primary hover:underline">Send Reminder</button>
        </div>
    </div>
    
    <div class="card bg-white hover-scale">
        <div class="flex items-center justify-between mb-4">
            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-bold rounded uppercase"><?php echo $lang === 'es' ? 'Manual' : 'Manual'; ?></span>
            <span class="text-xs text-success font-bold uppercase">Received</span>
        </div>
        <h3 class="text-lg font-bold text-black mb-1">Elevator Ops Manual</h3>
        <p class="text-xs text-slate-500 mb-4">Subcontractor: Vertical Lift</p>
        
        <div class="border-t border-stroke pt-4 flex justify-between items-center">
            <span class="text-[10px] text-slate-400">Uploaded: Oct 12</span>
            <button class="text-xs font-bold text-primary hover:underline">View PDF</button>
        </div>
    </div>
</div>
