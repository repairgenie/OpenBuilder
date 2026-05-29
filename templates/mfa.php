<?php
// templates/mfa.php
// Standalone MFA page - renders without index.php output chain

require_once __DIR__ . '/../src/app.php';

$lang = $_GET['lang'] ?? 'en';
$sent = $_GET['sent'] ?? false;

$labels = [
    'en' => [
        'title' => 'Security Verification',
        'desc' => 'Please enter the 6-digit code sent to your device.',
        'sent_desc' => 'A verification code has been sent.',
        'placeholder' => 'Enter 6-digit code',
        'verify' => 'Verify / Verificar',
        'invalid' => 'Invalid code. Please try again.',
    ],
    'es' => [
        'title' => 'Verificacion de Seguridad',
        'desc' => 'Por favor ingrese el codigo de 6 digitos enviado a su dispositivo.',
        'sent_desc' => 'Se ha enviado un codigo de verificacion.',
        'placeholder' => 'Ingrese codigo de 6 digitos',
        'verify' => 'Verificar / Verify',
        'invalid' => 'Codigo invalido. Intente de nuevo.',
    ],
];

$l = $labels[$lang] ?? $labels['en'];
$invalid = $_GET['invalid'] ?? false;
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($l['title']); ?></title>
    <link rel="stylesheet" href="public/css/variables.css">
    <link rel="stylesheet" href="public/css/base.css">
    <style>
        body { background: #f8fafc; font-family: 'Inter', sans-serif; }
        .mfa-container { display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1rem; }
        .mfa-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); padding: 2rem; width: 100%; max-width: 420px; text-align: center; }
        .mfa-icon { margin: 0 auto 1.5rem; width: 64px; height: 64px; background: rgba(99,102,241,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #6366f1; }
        .mfa-title { font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 0.5rem; }
        .mfa-desc { color: #64748b; font-size: 0.875rem; margin-bottom: 2rem; }
        .invalid-box { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; padding: 0.75rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.875rem; }
        .code-inputs { display: flex; justify-content: center; gap: 0.5rem; margin-bottom: 2rem; }
        .code-inputs input { width: 48px; height: 48px; text-align: center; font-size: 1.25rem; font-weight: 700; border: 1.5px solid #e2e8f0; border-radius: 8px; outline: none; }
        .code-inputs input:focus { border-color: #6366f1; }
        .btn-verify { width: 100%; background: #6366f1; color: white; padding: 0.875rem; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; }
        .btn-verify:hover { background: #4f46e5; }
    </style>
</head>
<body>
<div class="mfa-container">
    <div class="mfa-card">
        <div class="mfa-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
        </div>
        <h1 class="mfa-title"><?php echo htmlspecialchars($l['title']); ?></h1>
        <?php if ($invalid): ?>
        <div class="invalid-box"><?php echo htmlspecialchars($l['invalid']); ?></div>
        <?php endif; ?>
        <p class="mfa-desc"><?php echo $sent ? htmlspecialchars($l['sent_desc']) : htmlspecialchars($l['desc']); ?></p>
        <form method="POST" action="mfa_handler.php?lang=<?php echo $lang; ?>" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
            <div class="code-inputs">
                <?php for ($i = 0; $i < 6; $i++): ?>
                <input type="text" name="code[]" maxlength="1" pattern="[0-9]" required class="code-input" inputmode="numeric" autocomplete="off">
                <?php endfor; ?>
            </div>
            <button type="submit" class="btn-verify"><?php echo htmlspecialchars($l['verify']); ?></button>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[name="code[]"]');
    inputs.forEach((input, idx) => {
        input.addEventListener('input', function() {
            if (this.value && idx < inputs.length - 1) inputs[idx + 1].focus();
        });
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && idx > 0) inputs[idx - 1].focus();
        });
    });
});
</script>
</body>
</html>
