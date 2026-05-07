<!-- templates/portfolio_benchmarking.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Benchmarking de Proyectos' : 'Project Benchmarking'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Comparar el rendimiento de costos y cronograma entre proyectos similares.' : 'Compare cost and schedule performance across similar projects.'; ?></p>
</div>

<div class="card h-[400px]">
    <canvas id="benchmarkChart"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('benchmarkChart').getContext('2d');
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Safety', 'Quality', 'Cost', 'Schedule', 'Admin'],
            datasets: [
                { label: 'Project Avg', data: [80, 85, 75, 90, 88], borderColor: '#3C50E0', backgroundColor: 'rgba(60, 80, 224, 0.2)' },
                { label: 'Portfolio Top 10%', data: [95, 98, 90, 95, 94], borderColor: '#10B981', backgroundColor: 'rgba(16, 185, 129, 0.1)' }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: { r: { beginAtZero: true, max: 100 } }
        }
    });
});
</script>
