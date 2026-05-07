<!-- templates/pre_qual.php -->
<div class="mx-auto max-w-2xl">
    <div class="card">
        <h2 class="text-xl font-bold text-black mb-6 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Formulario de Precalificación' : 'Pre-qualification Form'; ?></h2>
        
        <form class="space-y-6">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Capacidad de Fianza' : 'Bonding Capacity'; ?></label>
                    <input type="number" class="w-full rounded border-[1.5px] border-stroke py-2 px-4 outline-none focus:border-primary">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-black">EMR (Safety Rating)</label>
                    <input type="text" placeholder="0.00" class="w-full rounded border-[1.5px] border-stroke py-2 px-4 outline-none focus:border-primary">
                </div>
            </div>
            
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Proyectos Similares' : 'Similar Projects'; ?></label>
                <textarea rows="3" class="w-full rounded border-[1.5px] border-stroke py-2 px-4 outline-none focus:border-primary"></textarea>
            </div>
            
            <button type="button" onclick="window.showToast('Pre-qual Submitted!', 'success')" class="rounded bg-black py-3 px-10 font-bold text-white hover:bg-opacity-90">
                <?php echo $lang === 'es' ? 'Enviar para Revisión' : 'Submit for Review'; ?>
            </button>
        </form>
    </div>
</div>
