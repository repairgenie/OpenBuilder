<!-- templates/safety_hazards.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Riesgos de Seguridad' : 'Safety Hazards'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Identificación y mitigación de peligros en la obra.' : 'Identification and mitigation of site hazards.'; ?></p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <!-- Hazard Card -->
    <div class="card bg-white border-l-4 border-danger">
        <div class="flex items-center justify-between mb-4">
            <span class="px-2 py-1 bg-danger bg-opacity-10 text-danger text-[10px] font-bold rounded uppercase">Immediate Danger</span>
            <span class="text-xs text-slate-400">SH-012</span>
        </div>
        <h3 class="text-lg font-bold text-black mb-1">Missing Perimeter Guardrails</h3>
        <p class="text-xs text-slate-500 mb-4">Location: Level 4, North Face</p>
        
        <div class="p-3 bg-slate-50 rounded text-xs text-slate-600 mb-4 italic">
            "Worker observed near edge without fall protection."
        </div>
        
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold text-slate-400">Assigned: Safety Officer</span>
            <button class="px-4 py-1 bg-black text-white text-[10px] font-bold rounded uppercase">Correct Now</button>
        </div>
    </div>
</div>
