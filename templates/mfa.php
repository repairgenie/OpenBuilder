<!-- templates/mfa.php -->
<div class="flex items-center justify-center min-h-[60vh]">
    <div class="card w-full max-w-md text-center">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-primary bg-opacity-10 text-primary mb-6">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
        </div>
        <h2 class="text-2xl font-bold text-black mb-2">
            <?php echo $lang === 'es' ? 'Verificación de Seguridad' : 'Security Verification'; ?>
        </h2>
        <p class="text-sm text-slate-500 mb-8">
            <?php echo $lang === 'es' ? 'Por favor ingrese el código de 6 dígitos enviado a su dispositivo.' : 'Please enter the 6-digit code sent to your device.'; ?>
        </p>

        <div class="flex justify-center gap-3 mb-8">
            <?php for ($i = 0; $i < 6; $i++): ?>
            <input type="text" maxlength="1" class="h-12 w-12 rounded border border-stroke text-center text-xl font-bold focus:border-primary outline-none">
            <?php endfor; ?>
        </div>

        <button onclick="window.showToast('<?php echo $lang === 'es' ? 'Verificado con éxito' : 'Verified successfully'; ?>', 'success'); window.location.href='?page=dashboard&lang=<?php echo $lang; ?>'" class="w-full rounded bg-primary py-3 font-bold text-white hover:bg-opacity-90 transition-all">
            <?php echo $lang === 'es' ? 'Verificar' : 'Verify'; ?>
        </button>

        <p class="mt-6 text-xs text-slate-500">
            <?php echo $lang === 'es' ? '¿No recibiste el código?' : "Didn't receive the code?"; ?>
            <a href="#" class="text-primary font-bold hover:underline"><?php echo $lang === 'es' ? 'Reenviar' : 'Resend'; ?></a>
        </p>
    </div>
</div>
