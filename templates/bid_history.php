<!-- templates/bid_history.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Historial de Licitaciones' : 'Bidding History'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Tendencias de costos históricos por paquete.' : 'Historical cost trends by package.'; ?></p>
</div>

<div class="card">
    <div class="h-64">
        <canvas id="bidHistoryChart"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('bidHistoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Proj A', 'Proj B', 'Proj C', 'Current'],
            datasets: [{
                label: 'Drywall Cost/SF',
                data: [5.20, 5.45, 5.10, 5.60],
                borderColor: '#3C50E0',
                tension: 0.4
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: false } }
        }
    });
});
</script>
