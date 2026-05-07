<!-- templates/settings.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Configuración' : 'Settings'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Administra tus preferencias del sistema.' : 'Manage your system preferences.'; ?></p>
</div>

<div class="grid grid-cols-1 gap-6">
    <!-- Language Settings -->
    <div class="card">
        <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2">
            <?php echo $lang === 'es' ? 'Idioma del Sistema' : 'System Language'; ?>
        </h3>
        <div class="flex gap-4">
            <a href="?page=settings&lang=en" class="flex-1 flex flex-col items-center p-6 border-2 <?php echo $lang === 'en' ? 'border-primary bg-primary bg-opacity-5' : 'border-stroke hover:border-primary'; ?> rounded-lg transition-all">
                <span class="text-4xl mb-2">🇺🇸</span>
                <span class="font-bold text-black">English</span>
                <?php if ($lang === 'en'): ?>
                <span class="mt-2 text-xs font-bold text-primary uppercase">Active / Activo</span>
                <?php endif; ?>
            </a>
            <a href="?page=settings&lang=es" class="flex-1 flex flex-col items-center p-6 border-2 <?php echo $lang === 'es' ? 'border-primary bg-primary bg-opacity-5' : 'border-stroke hover:border-primary'; ?> rounded-lg transition-all">
                <span class="text-4xl mb-2">🇪🇸</span>
                <span class="font-bold text-black">Español</span>
                <?php if ($lang === 'es'): ?>
                <span class="mt-2 text-xs font-bold text-primary uppercase">Active / Activo</span>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <!-- Theme Settings (Mock) -->
    <div class="card">
        <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2">
            <?php echo $lang === 'es' ? 'Apariencia' : 'Appearance'; ?>
        </h3>
        <div class="flex items-center justify-between">
            <div>
                <p class="font-bold text-black"><?php echo $lang === 'es' ? 'Modo Oscuro' : 'Dark Mode'; ?></p>
                <p class="text-sm text-slate-500"><?php echo $lang === 'es' ? 'Ajusta la apariencia del sistema para condiciones de poca luz.' : 'Adjust the system appearance for low-light conditions.'; ?></p>
            </div>
            <div class="relative inline-block w-12 h-6 transition duration-200 ease-in bg-slate-200 rounded-full cursor-not-allowed">
                 <span class="absolute left-0 inline-block w-6 h-6 transition duration-200 ease-in transform bg-white border border-slate-200 rounded-full shadow"></span>
            </div>
        </div>
    </div>
</div>
