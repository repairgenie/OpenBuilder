<!-- templates/quality_analytics.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Análisis de Calidad' : 'Quality Analytics'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Tendencias de fallas y cumplimiento por subcontratista.' : 'Failure trends and compliance by subcontractor.'; ?></p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="card h-64">
        <h4 class="text-sm font-bold text-slate-500 uppercase mb-4"><?php echo $lang === 'es' ? 'Tasa de Cumplimiento' : 'Compliance Rate'; ?></h4>
        <canvas id="complianceChart"></canvas>
    </div>
    <div class="card h-64">
        <h4 class="text-sm font-bold text-slate-500 uppercase mb-4"><?php echo $lang === 'es' ? 'Fallas por Disciplina' : 'Fails by Discipline'; ?></h4>
        <canvas id="failsChart"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx1 = document.getElementById('complianceChart').getContext('2d');
    new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: ['Pass', 'Fail', 'N/A'],
            datasets: [{ data: [85, 10, 5], backgroundColor: ['#10B981', '#FF6766', '#E2E8F0'] }]
        },
        options: { maintainAspectRatio: false }
    });

    const ctx2 = document.getElementById('failsChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Struct', 'MEP', 'Arch', 'Safety'],
            datasets: [{ label: 'Failures', data: [5, 12, 3, 8], backgroundColor: '#3C50E0' }]
        },
        options: { maintainAspectRatio: false }
    });
});
</script>
