<!-- templates/daily_production_report.php -->
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Informe de Producción Diaria' : 'Daily Production Report'; ?></h2>
    <span class="text-slate-400 font-mono text-sm">Nov 07, 2023</span>
</div>

<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Labor Summary -->
        <div class="card bg-white">
            <h3 class="text-sm font-bold text-slate-500 uppercase mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Resumen de Mano de Obra' : 'Labor Summary'; ?></h3>
            <div class="space-y-2">
                <div class="flex justify-between text-sm"><span>Total Man-Hours</span><span class="font-bold">142.5 hrs</span></div>
                <div class="flex justify-between text-sm"><span>Active Workers</span><span class="font-bold text-primary">18</span></div>
                <div class="flex justify-between text-sm"><span>Labor Cost (Est.)</span><span class="font-bold text-black">$6,412.50</span></div>
            </div>
        </div>
        
        <!-- Progress Summary -->
        <div class="card bg-white">
            <h3 class="text-sm font-bold text-slate-500 uppercase mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Resumen de Instalación' : 'Installation Summary'; ?></h3>
            <div class="space-y-2">
                <div class="flex justify-between text-sm"><span>Concrete Poured</span><span class="font-bold">25.0 m³</span></div>
                <div class="flex justify-between text-sm"><span>Drywall Installed</span><span class="font-bold">120.0 m²</span></div>
                <div class="flex justify-between text-sm"><span>Forms Stripped</span><span class="font-bold">85.0 m²</span></div>
            </div>
        </div>
    </div>
</div>
