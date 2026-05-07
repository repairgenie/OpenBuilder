<!-- templates/inspection_schedule.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Calendario de Inspecciones' : 'Inspection Schedule'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Próximas actividades de control de calidad y seguridad.' : 'Upcoming quality and safety control activities.'; ?></p>
</div>

<div class="card p-6">
    <div class="grid grid-cols-7 gap-px bg-slate-200 border border-slate-200 rounded-lg overflow-hidden">
        <!-- Calendar Header -->
        <?php 
        $days = $lang === 'es' ? ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        foreach ($days as $day): ?>
            <div class="bg-slate-50 py-2 text-center text-[10px] font-bold text-slate-500 uppercase"><?php echo $day; ?></div>
        <?php endforeach; ?>
        
        <!-- Calendar Days Simulation -->
        <?php for($i=1; $i<=31; $i++): ?>
            <div class="bg-white h-24 p-2 relative hover:bg-slate-50 transition-colors">
                <span class="text-xs text-slate-400"><?php echo $i; ?></span>
                <?php if($i === 15): ?>
                    <div class="mt-1 p-1 bg-primary bg-opacity-10 text-primary text-[8px] font-bold rounded truncate border-l-2 border-primary">
                        Pre-Pour Check
                    </div>
                <?php endif; ?>
                <?php if($i === 20): ?>
                    <div class="mt-1 p-1 bg-danger bg-opacity-10 text-danger text-[8px] font-bold rounded truncate border-l-2 border-danger">
                        Safety Walk
                    </div>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>
