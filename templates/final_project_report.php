<!-- templates/final_project_report.php -->
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Informe Final de Proyecto' : 'Final Project Report'; ?></h2>
    <button class="bg-black text-white px-6 py-2 rounded font-bold text-sm hover:bg-opacity-90">
        <?php echo $lang === 'es' ? 'Exportar PDF' : 'Export PDF'; ?>
    </button>
</div>

<div class="space-y-6">
    <div class="card bg-white">
        <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Resumen Ejecutivo' : 'Executive Summary'; ?></h3>
        <p class="text-sm text-slate-600 leading-relaxed">
            The project was completed within 12 months with a final budget variance of +2.4%. All major Punch List items have been verified and closed.
        </p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card">
            <h4 class="text-sm font-bold text-slate-500 uppercase mb-4"><?php echo $lang === 'es' ? 'Métricas de Calidad' : 'Quality Metrics'; ?></h4>
            <div class="space-y-2">
                <div class="flex justify-between text-sm"><span>Total Inspections</span><span class="font-bold">450</span></div>
                <div class="flex justify-between text-sm"><span>Pass Rate</span><span class="font-bold text-success">94%</span></div>
                <div class="flex justify-between text-sm"><span>Punch Items</span><span class="font-bold">128</span></div>
            </div>
        </div>
        <div class="card">
            <h4 class="text-sm font-bold text-slate-500 uppercase mb-4"><?php echo $lang === 'es' ? 'Métricas Financieras' : 'Financial Metrics'; ?></h4>
            <div class="space-y-2">
                <div class="flex justify-between text-sm"><span>Original Budget</span><span class="font-bold">$1.2M</span></div>
                <div class="flex justify-between text-sm"><span>Approved Changes</span><span class="font-bold">$45K</span></div>
                <div class="flex justify-between text-sm"><span>Final Cost</span><span class="font-bold text-primary">$1.24M</span></div>
            </div>
        </div>
    </div>
</div>
