<!-- templates/inspection_execution.php -->
<div class="mb-6">
    <div class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase mb-2">
        <span class="px-2 py-0.5 bg-slate-100 rounded">Level 2</span>
        <span>•</span>
        <span>ID: INS-102</span>
    </div>
    <h2 class="text-2xl font-bold text-black">Pre-Concrete Pour Checklist</h2>
</div>

<div class="card space-y-6">
    <!-- Checkpoint Item -->
    <div class="p-4 border border-stroke rounded-lg">
        <div class="flex items-start justify-between mb-4">
            <h4 class="text-sm font-bold text-black"><?php echo $lang === 'es' ? '1. Limpieza de encofrado' : '1. Formwork Cleanliness'; ?></h4>
            <div class="flex gap-1">
                <button class="px-3 py-1 bg-success text-white text-[10px] font-bold rounded">Pass</button>
                <button class="px-3 py-1 bg-slate-100 text-slate-400 text-[10px] font-bold rounded">Fail</button>
            </div>
        </div>
        
        <div class="flex gap-2">
            <div class="h-16 w-16 bg-slate-100 rounded border-2 border-dashed border-stroke flex items-center justify-center text-slate-400 cursor-pointer hover:border-primary hover:text-primary">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
            </div>
            <div class="flex-1">
                <textarea rows="1" class="w-full text-xs border-b border-stroke py-1 outline-none focus:border-primary" placeholder="<?php echo $lang === 'es' ? 'Añadir comentario...' : 'Add comment...'; ?>"></textarea>
            </div>
        </div>
    </div>

    <!-- Final Sign-off -->
    <div class="mt-10 border-t border-stroke pt-8">
        <h4 class="text-sm font-bold text-black mb-4"><?php echo $lang === 'es' ? 'Firma del Inspector' : 'Inspector Signature'; ?></h4>
        <div class="h-24 bg-slate-50 border border-stroke rounded mb-4 flex items-center justify-center">
            <p class="text-[10px] text-slate-400 italic"><?php echo $lang === 'es' ? 'Firmar para completar' : 'Sign to complete'; ?></p>
        </div>
        <button onclick="window.showToast('Inspection Completed!', 'success')" class="w-full bg-primary text-white py-3 font-bold rounded">
            <?php echo $lang === 'es' ? 'Enviar Inspección' : 'Submit Inspection'; ?>
        </button>
    </div>
</div>
