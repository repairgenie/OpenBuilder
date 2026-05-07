<!-- templates/budget.php -->
<?php
// Fetch Cost Codes
$query = "SELECT * FROM cost_codes ORDER BY code ASC";
$cost_codes = $pdo->query($query)->fetchAll();

$total_original = 0;
$total_co = 0;
$total_committed = 0;

foreach ($cost_codes as $code) {
    $total_original += $code['original_budget'];
    $total_co += $code['change_orders'];
    $total_committed += $code['committed_costs'];
}

$total_revised = $total_original + $total_co;
$total_spent = $total_committed;
?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-2xl font-bold text-black">
        <?php echo $lang === 'es' ? 'Presupuesto' : 'Budget'; ?>
    </h2>
    <div class="flex gap-3">
        <select class="rounded border border-stroke bg-white py-2 px-3 text-sm font-medium focus:border-primary outline-none">
            <option value="USD">USD ($)</option>
            <option value="EUR">EUR (€)</option>
        </select>
        <a href="?action=export_budget&lang=<?php echo $lang; ?>" class="inline-flex items-center justify-center rounded-md border border-stroke bg-white py-2 px-6 text-center font-medium text-black hover:shadow-md transition-all">
            <?php echo $lang === 'es' ? 'Exportar CSV' : 'Export CSV'; ?>
        </a>
    </div>
</div>

<!-- Budget Simulator -->
<div class="card mb-6 bg-slate-50 border-dashed border-2 border-slate-200">
    <h3 class="text-sm font-bold text-slate-500 uppercase mb-4"><?php echo $lang === 'es' ? 'Simulador de Escenarios' : 'Scenario Simulator'; ?></h3>
    <div class="flex flex-col md:flex-row gap-8 items-center">
        <div class="flex-1 w-full">
            <label class="block text-xs font-bold text-black mb-2"><?php echo $lang === 'es' ? 'Aumento Estimado de Costos (%)' : 'Estimated Cost Increase (%)'; ?></label>
            <input type="range" id="variance-slider" min="0" max="50" value="0" class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-primary">
            <div class="flex justify-between mt-2 text-[10px] font-bold text-slate-400">
                <span>0%</span>
                <span>25%</span>
                <span>50%</span>
            </div>
        </div>
        <div class="p-4 bg-white rounded shadow-sm min-w-[150px] text-center">
            <p class="text-xs text-slate-500 uppercase font-bold"><?php echo $lang === 'es' ? 'Impacto Proyectado' : 'Projected Impact'; ?></p>
            <p class="text-xl font-black text-danger" id="projected-impact">$0</p>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-stroke">
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Código' : 'Cost Code'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Presupuesto Orig.' : 'Original Budget'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Órdenes Cambio' : 'Change Orders'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Presupuesto Rev.' : 'Revised Budget'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Comprometido' : 'Committed'; ?></th>
                    <th class="py-4 px-4 font-semibold text-slate-600"><?php echo $lang === 'es' ? 'Variación' : 'Variance'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cost_codes as $code): 
                    $metrics = calculate_budget_metrics($code);
                ?>
                <tr class="border-b border-stroke hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-4">
                        <p class="text-sm font-bold text-black"><?php echo htmlspecialchars($code['code']); ?></p>
                        <p class="text-xs text-slate-500"><?php echo htmlspecialchars($code['description']); ?></p>
                    </td>
                    <td class="py-4 px-4 text-sm">$<?php echo number_format($code['original_budget']); ?></td>
                    <td class="py-4 px-4 text-sm text-warning font-medium">+$<?php echo number_format($code['change_orders']); ?></td>
                    <td class="py-4 px-4 text-sm font-bold text-black">$<?php echo number_format($metrics['revised']); ?></td>
                    <td class="py-4 px-4 text-sm">$<?php echo number_format($code['committed_costs']); ?></td>
                    <td class="py-4 px-4 text-sm font-medium">
                        <span class="<?php echo $metrics['variance'] < 0 ? 'text-danger font-bold' : 'text-slate-700'; ?>">
                            $<?php echo number_format($metrics['variance']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="bg-slate-50 font-bold text-black">
                    <td class="py-4 px-4"><?php echo $lang === 'es' ? 'TOTALES' : 'TOTALS'; ?></td>
                    <td class="py-4 px-4">$<?php echo number_format($total_original); ?></td>
                    <td class="py-4 px-4 text-warning">+$<?php echo number_format($total_co); ?></td>
                    <td class="py-4 px-4">$<?php echo number_format($total_revised); ?></td>
                    <td class="py-4 px-4">$<?php echo number_format($total_committed); ?></td>
                    <td class="py-4 px-4">$<?php echo number_format($total_revised - $total_committed); ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('variance-slider');
    const impactLabel = document.getElementById('projected-impact');
    const totalSpent = <?php echo $total_spent; ?>;

    slider?.addEventListener('input', (e) => {
        const percent = e.target.value;
        const impact = (totalSpent * (percent / 100)).toLocaleString();
        impactLabel.textContent = `$${impact}`;
    });
});
</script>
