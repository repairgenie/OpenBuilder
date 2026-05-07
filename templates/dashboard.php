<!-- templates/dashboard.php -->
<?php
// Fetch Summary Stats
$rfi_open = $pdo->query("SELECT COUNT(*) FROM rfis WHERE status = 'Open'")->fetchColumn();
$logs_count = $pdo->query("SELECT COUNT(*) FROM daily_logs")->fetchColumn();
$total_budget = $pdo->query("SELECT SUM(original_budget + change_orders) FROM cost_codes")->fetchColumn();
$total_spent = $pdo->query("SELECT SUM(committed_costs) FROM cost_codes")->fetchColumn();
$budget_percent = $total_budget > 0 ? ($total_spent / $total_budget) * 100 : 0;
?>
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Panel de Control' : 'Project Dashboard'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Vistazo general del proyecto.' : 'Project overview at a glance.'; ?></p>
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4 mb-6">
    <!-- Stat Cards -->
    <div class="card bg-white hover-scale">
        <span class="text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'RFIs Abiertas' : 'Open RFIs'; ?></span>
        <h3 class="text-2xl font-bold text-warning mt-1"><?php echo $rfi_open; ?></h3>
    </div>
    <div class="card bg-white hover-scale">
        <span class="text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'Diarios Totales' : 'Total Logs'; ?></span>
        <h3 class="text-2xl font-bold text-primary mt-1"><?php echo $logs_count; ?></h3>
    </div>
    <div class="card bg-white hover-scale">
        <span class="text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'Uso del Presupuesto' : 'Budget Utilization'; ?></span>
        <h3 class="text-2xl font-bold text-black mt-1"><?php echo round($budget_percent, 1); ?>%</h3>
    </div>
    <div class="card bg-white hover-scale">
        <span class="text-xs font-bold text-slate-500 uppercase"><?php echo $lang === 'es' ? 'Personal Hoy' : 'Manpower Today'; ?></span>
        <h3 class="text-2xl font-bold text-success mt-1">12</h3>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Quick Actions -->
    <div class="lg:col-span-1 space-y-4">
        <div class="card h-full">
            <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Acciones Rápidas' : 'Quick Actions'; ?></h3>
            <div class="grid grid-cols-1 gap-3">
                <a href="?page=create_daily_log&lang=<?php echo $lang; ?>" class="flex items-center gap-3 p-3 border border-stroke rounded-lg hover:bg-slate-50 transition-all hover:scale-[1.02]">
                    <span class="p-2 bg-primary bg-opacity-10 rounded text-primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    </span>
                    <span class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Nuevo Diario' : 'New Daily Log'; ?></span>
                </a>
                <a href="?page=create_rfi&lang=<?php echo $lang; ?>" class="flex items-center gap-3 p-3 border border-stroke rounded-lg hover:bg-slate-50 transition-all hover:scale-[1.02]">
                    <span class="p-2 bg-warning bg-opacity-10 rounded text-warning">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </span>
                    <span class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Crear RFI' : 'Create RFI'; ?></span>
                </a>
                <a href="?page=create_cost_code&lang=<?php echo $lang; ?>" class="flex items-center gap-3 p-3 border border-stroke rounded-lg hover:bg-slate-50 transition-all hover:scale-[1.02]">
                    <span class="p-2 bg-success bg-opacity-10 rounded text-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"></path></svg>
                    </span>
                    <span class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Nuevo Código' : 'New Cost Code'; ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="lg:col-span-1">
        <div class="card h-full">
            <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Actividad Reciente' : 'Recent Activity'; ?></h3>
            <ul class="space-y-4">
                <li class="flex items-center gap-3">
                    <span class="h-2 w-2 rounded-full bg-primary"></span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-black">RFI #003 Created</p>
                        <p class="text-xs text-slate-500">2 hours ago</p>
                    </div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Budget Chart -->
    <div class="lg:col-span-1">
        <div class="card h-full">
            <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Distribución del Presupuesto' : 'Budget Distribution'; ?></h3>
            <div class="h-48">
                <canvas id="budgetChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Project Timeline -->
<div class="card mt-6">
    <h3 class="text-lg font-bold text-black mb-6 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Línea de Tiempo del Proyecto' : 'Project Timeline'; ?></h3>
    <div class="relative flex flex-col gap-8 before:absolute before:left-4 before:top-2 before:h-[calc(100%-16px)] before:w-0.5 before:bg-slate-200">
        <div class="relative pl-12">
            <span class="absolute left-0 top-1 h-8 w-8 rounded-full bg-success flex items-center justify-center text-white">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </span>
            <p class="text-sm font-bold text-black"><?php echo $lang === 'es' ? 'Inicio del Proyecto' : 'Project Kickoff'; ?></p>
            <p class="text-xs text-slate-500">Jan 1, 2023</p>
        </div>
    </div>
</div>

<!-- AI Schedule Prediction -->
<div class="card mt-6 border-primary border-opacity-20 bg-primary bg-opacity-5">
    <h3 class="text-lg font-bold text-black mb-4"><?php echo $lang === 'es' ? 'Predicción de Cronograma (IA)' : 'AI Schedule Prediction'; ?></h3>
    <p class="text-sm text-slate-700">
        <?php echo $lang === 'es' 
            ? 'Basado en el progreso actual, se predice que la fase de **Estructura** terminará **3 días antes**.' 
            : 'Based on current progress, the **Framing** phase is predicted to finish **3 days early**.'; ?>
    </p>
</div>

<!-- AI Risk Heatmap -->
<div class="card mt-6">
    <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Mapa de Calor de Riesgos (IA)' : 'AI Risk Heatmap'; ?></h3>
    <div class="grid grid-cols-5 gap-2 h-32">
        <div class="bg-danger rounded flex items-center justify-center text-[10px] font-bold text-white uppercase text-center p-1"><?php echo $lang === 'es' ? 'Seguridad Alta' : 'High Safety'; ?></div>
        <div class="bg-warning rounded flex items-center justify-center text-[10px] font-bold text-white uppercase text-center p-1"><?php echo $lang === 'es' ? 'Cronograma Medio' : 'Medium Schedule'; ?></div>
        <div class="bg-success opacity-20 rounded"></div>
        <div class="bg-success opacity-20 rounded"></div>
        <div class="bg-danger opacity-60 rounded flex items-center justify-center text-[10px] font-bold text-white uppercase text-center p-1"><?php echo $lang === 'es' ? 'Varianza Presupuesto' : 'Budget Variance'; ?></div>
    </div>
</div>

<!-- RFI Aging Chart -->
<div class="card mt-6">
    <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Envejecimiento de RFIs' : 'RFI Aging'; ?></h3>
    <div class="h-48">
        <canvas id="rfiAgingChart"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Budget Chart
    const ctxB = document.getElementById('budgetChart').getContext('2d');
    new Chart(ctxB, {
        type: 'doughnut',
        data: {
            labels: ['Committed', 'Remaining'],
            datasets: [{
                data: [<?php echo $total_spent; ?>, <?php echo max(0, $total_budget - $total_spent); ?>],
                backgroundColor: ['#3C50E0', '#E5E7EB'],
                borderWidth: 0
            }]
        },
        options: { cutout: '70%', plugins: { legend: { display: false } } }
    });

    // RFI Aging Chart
    const ctxA = document.getElementById('rfiAgingChart').getContext('2d');
    new Chart(ctxA, {
        type: 'bar',
        data: {
            labels: ['0-3 Days', '4-7 Days', '8-14 Days', '15+ Days'],
            datasets: [{
                label: 'RFIs',
                data: [12, 5, 2, 1],
                backgroundColor: '#3C50E0'
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
