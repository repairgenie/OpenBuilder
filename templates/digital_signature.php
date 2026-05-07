<!-- templates/digital_signature.php -->
<div class="mx-auto max-w-lg">
    <div class="card bg-white border-2 border-primary border-dashed">
        <h2 class="text-xl font-bold text-black mb-4"><?php echo $lang === 'es' ? 'Firma Digital Requerida' : 'Digital Signature Required'; ?></h2>
        <p class="text-sm text-slate-500 mb-6"><?php echo $lang === 'es' ? 'Al firmar este documento, usted aprueba el monto de la orden de cambio.' : 'By signing this document, you approve the change order amount.'; ?></p>
        
        <div class="h-32 bg-slate-100 rounded border border-stroke flex items-center justify-center mb-6 cursor-crosshair">
            <p class="text-xs text-slate-400 italic"><?php echo $lang === 'es' ? 'Firmar aquí...' : 'Sign here...'; ?></p>
        </div>
        
        <div class="flex gap-4">
            <button class="flex-1 rounded bg-slate-200 py-2 font-bold text-slate-600 hover:bg-slate-300">
                <?php echo $lang === 'es' ? 'Limpiar' : 'Clear'; ?>
            </button>
            <button onclick="window.showToast('Document Signed & Approved!', 'success')" class="flex-1 rounded bg-primary py-2 font-bold text-white hover:bg-opacity-90">
                <?php echo $lang === 'es' ? 'Aprobar' : 'Approve'; ?>
            </button>
        </div>
    </div>
</div>
