<!-- templates/cash_flow.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Flujo de Caja (Cash Flow)' : 'Cash Flow'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Análisis de ingresos vs gastos proyectados.' : 'Income vs expense analysis and projections.'; ?></p>
</div>

<div class="card h-[400px]">
    <canvas id="cashFlowChart"></canvas>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('cashFlowChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [
                {
                    label: 'Income (Owner Payments)',
                    data: [100000, 250000, 400000, 550000, 700000, 850000],
                    borderColor: '#10B981',
                    fill: false
                },
                {
                    label: 'Expenses (Subcontractors)',
                    data: [80000, 180000, 320000, 480000, 620000, 780000],
                    borderColor: '#FF6766',
                    fill: false
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
