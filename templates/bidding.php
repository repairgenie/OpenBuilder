<!-- templates/bidding.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Licitaciones y Adquisiciones' : 'Bidding & Procurement'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestionar paquetes de ofertas y selección de subcontratistas.' : 'Manage bid packages and subcontractor selection.'; ?></p>
    </div>
    <button class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90 shadow-md">
        <?php echo $lang === 'es' ? 'Nuevo Paquete' : 'New Bid Package'; ?>
    </button>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
    <!-- Bid Package Card -->
    <div class="card bg-white hover-scale">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'Abierto' : 'Open'; ?></span>
            <span class="text-xs text-slate-400">Due: Nov 15</span>
        </div>
        <h3 class="text-lg font-bold text-black mb-1">Drywall & Framing</h3>
        <p class="text-xs text-slate-500 mb-4"><?php echo $lang === 'es' ? 'Alcance completo del Sector B.' : 'Full scope for Sector B.'; ?></p>
        
        <div class="flex items-center justify-between border-t border-stroke pt-4">
            <div class="flex -space-x-2">
                <div class="h-6 w-6 rounded-full bg-primary border-2 border-white flex items-center justify-center text-[10px] text-white">V1</div>
                <div class="h-6 w-6 rounded-full bg-success border-2 border-white flex items-center justify-center text-[10px] text-white">V2</div>
                <div class="h-6 w-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-[10px] text-slate-500">+3</div>
            </div>
            <span class="text-xs font-bold text-primary">5 <?php echo $lang === 'es' ? 'Ofertas' : 'Bids'; ?></span>
        </div>
    </div>
</div>
