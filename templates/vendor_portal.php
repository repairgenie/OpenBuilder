<!-- templates/vendor_portal.php -->
<div class="mx-auto max-w-lg mt-10">
    <div class="card bg-white shadow-2xl">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Portal de Proveedores' : 'Vendor Portal'; ?></h2>
            <p class="text-slate-500"><?php echo $lang === 'es' ? 'Enviar su oferta para el proyecto.' : 'Submit your bid for the project.'; ?></p>
        </div>
        
        <form class="space-y-4">
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Monto de la Oferta' : 'Bid Amount'; ?></label>
                <input type="number" placeholder="0.00" class="w-full rounded border-[1.5px] border-stroke py-3 px-5 outline-none transition focus:border-primary">
            </div>
            <div>
                <label class="mb-2 block text-sm font-medium text-black"><?php echo $lang === 'es' ? 'Notas / Exclusiones' : 'Notes / Exclusions'; ?></label>
                <textarea rows="4" class="w-full rounded border-[1.5px] border-stroke py-3 px-5 outline-none transition focus:border-primary"></textarea>
            </div>
            <button type="button" onclick="window.showToast('Bid Submitted!', 'success')" class="w-full rounded bg-primary py-3 font-bold text-white hover:bg-opacity-90">
                <?php echo $lang === 'es' ? 'Enviar Oferta' : 'Submit Bid'; ?>
            </button>
        </form>
    </div>
</div>
