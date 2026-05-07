<!-- templates/portfolio_dashboard.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Análisis de Cartera' : 'Portfolio Analytics'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Rendimiento consolidado de todos los proyectos activos.' : 'Consolidated performance of all active projects.'; ?></p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card bg-white">
        <h4 class="text-[10px] font-bold text-slate-500 uppercase mb-1">Active Projects</h4>
        <p class="text-2xl font-bold text-black">12</p>
    </div>
    <div class="card bg-white">
        <h4 class="text-[10px] font-bold text-slate-500 uppercase mb-1">Total Portfolio Value</h4>
        <p class="text-2xl font-bold text-black">$85.4M</p>
    </div>
    <div class="card bg-white">
        <h4 class="text-[10px] font-bold text-slate-500 uppercase mb-1">Avg. Margin</h4>
        <p class="text-2xl font-bold text-success">14.2%</p>
    </div>
    <div class="card bg-white">
        <h4 class="text-[10px] font-bold text-slate-500 uppercase mb-1">Safety Incidents (YTD)</h4>
        <p class="text-2xl font-bold text-danger">3</p>
    </div>
</div>

<div class="card h-64 mb-6">
    <canvas id="portfolioChart"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('portfolioChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Hotel A', 'Office B', 'Mall C', 'Apts D'],
            datasets: [
                { label: 'Budget', data: [12, 18, 25, 10], backgroundColor: '#E2E8F0' },
                { label: 'Actual', data: [12.4, 17.5, 26.2, 9.8], backgroundColor: '#3C50E0' }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true, title: { display: true, text: 'Millions ($)' } } }
        }
    });
});
</script>
