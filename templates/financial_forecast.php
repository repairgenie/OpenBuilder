<!-- templates/financial_forecast.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Proyecciones Financieras' : 'Financial Forecast'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Impacto proyectado incluyendo cambios pendientes.' : 'Projected impact including pending changes.'; ?></p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="card bg-white">
        <h4 class="text-sm font-bold text-slate-500 uppercase mb-2"><?php echo $lang === 'es' ? 'Comprometido' : 'Committed'; ?></h4>
        <p class="text-3xl font-bold text-black">$598,000</p>
    </div>
    <div class="card bg-white">
        <h4 class="text-sm font-bold text-slate-500 uppercase mb-2"><?php echo $lang === 'es' ? 'Cambios Pendientes' : 'Pending Changes'; ?></h4>
        <p class="text-3xl font-bold text-warning">$45,200</p>
    </div>
    <div class="card bg-primary bg-opacity-5 border-primary border">
        <h4 class="text-sm font-bold text-primary uppercase mb-2"><?php echo $lang === 'es' ? 'Pronóstico Final' : 'Final Forecast'; ?></h4>
        <p class="text-3xl font-bold text-primary">$643,200</p>
    </div>
</div>

<div class="card mt-6 h-64">
    <canvas id="forecastChart"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('forecastChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Original', 'Revised', 'Forecast'],
            datasets: [{
                label: 'Project Value',
                data: [600000, 615000, 643200],
                backgroundColor: ['#64748b', '#3C50E0', '#10B981']
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
