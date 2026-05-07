<!-- templates/bim_takeoff.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Cómputos desde el Modelo' : '3D Model Takeoff'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Extracción de cantidades directamente de objetos BIM.' : 'Quantity extraction directly from BIM objects.'; ?></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 card bg-slate-900 h-[400px] flex items-center justify-center">
        <p class="text-slate-500 font-mono text-xs">SELECT OBJECT FOR QUANTITY EXTRACTION...</p>
    </div>
    
    <div class="card bg-white">
        <h3 class="font-bold text-black mb-4"><?php echo $lang === 'es' ? 'Resultados' : 'Takeoff Results'; ?></h3>
        <div class="space-y-4">
            <div class="flex justify-between border-b border-stroke pb-2">
                <span class="text-sm text-slate-500">Concrete Volume</span>
                <span class="text-sm font-bold text-black">42.5 m³</span>
            </div>
            <div class="flex justify-between border-b border-stroke pb-2">
                <span class="text-sm text-slate-500">Surface Area</span>
                <span class="text-sm font-bold text-black">128.0 m²</span>
            </div>
            <div class="flex justify-between border-b border-stroke pb-2">
                <span class="text-sm text-slate-500">Object Count</span>
                <span class="text-sm font-bold text-black">12 Items</span>
            </div>
        </div>
        <button class="w-full mt-6 bg-primary text-white py-2 font-bold rounded shadow-md hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Exportar a Presupuesto' : 'Export to Budget'; ?>
        </button>
    </div>
</div>
