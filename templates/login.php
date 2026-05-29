<?php
// templates/login.php
// Bilingual login page - Biblia §1 requires bilingual support

$lang = $_GET['lang'] ?? 'en';
$error = $_GET['error'] ?? '';

$labels = [
    'en' => [
        'title' => 'Sign In / Iniciar Sesion',
        'heading' => 'Welcome Back',
        'subheading' => 'Sign in to your OpenBuilder account',
        'email' => 'Email Address',
        'password' => 'Password',
        'submit' => 'Sign In / Iniciar Sesion',
        'no_account' => "Don't have an account?",
        'footer' => 'OpenBuilder Construction Management Platform',
    ],
    'es' => [
        'title' => 'Iniciar Sesion / Sign In',
        'heading' => 'Bienvenido',
        'subheading' => 'Inicie sesion en su cuenta de OpenBuilder',
        'email' => 'Correo electronico',
        'password' => 'Contrasena',
        'submit' => 'Iniciar Sesion / Sign In',
        'no_account' => 'No tiene una cuenta?',
        'footer' => 'Plataforma de Gestion de Construccion OpenBuilder',
    ],
];

$l = $labels[$lang] ?? $labels['en'];
?>

<div class="flex items-center justify-center min-h-[70vh]">
    <div class="card w-full max-w-md">
        <div class="text-center mb-8">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary bg-opacity-10 text-primary mb-4">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-black"><?php echo htmlspecialchars($l['heading']); ?></h2>
            <p class="text-sm text-slate-500 mt-1"><?php echo htmlspecialchars($l['subheading']); ?></p>
        </div>

        <?php if ($error): ?>
        <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-700 text-sm">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="login_handler.php?lang=<?php echo $lang; ?>" class="space-y-4">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block text-sm font-medium text-black mb-1"><?php echo htmlspecialchars($l['email']); ?></label>
                <input
                    type="email"
                    name="email"
                    required
                    class="w-full rounded border border-stroke py-2 px-3 text-sm outline-none focus:border-primary"
                    placeholder="admin@openbuilder.com"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-black mb-1"><?php echo htmlspecialchars($l['password']); ?></label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full rounded border border-stroke py-2 px-3 text-sm outline-none focus:border-primary"
                    placeholder="********"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded bg-primary py-3 font-bold text-white hover:bg-opacity-90 transition-all"
            >
                <?php echo htmlspecialchars($l['submit']); ?>
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="?page=dashboard&lang=<?php echo $lang; ?>" class="text-sm text-slate-500 hover:text-primary">
                <?php echo $lang === 'es' ? 'Ver sitio sin cuenta (solo lectura)' : 'View site without account (read-only)'; ?>
            </a>
        </div>

        <div class="mt-4 text-center text-xs text-slate-400">
            <?php echo htmlspecialchars($l['footer']); ?>
        </div>
    </div>
</div>