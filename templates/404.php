<!-- templates/404.php -->
<div class="flex flex-col items-center justify-center py-20">
    <div class="card max-w-md w-full text-center py-10 px-6">
        <h1 class="text-9xl font-black text-primary opacity-10">404</h1>
        <h2 class="text-2xl font-bold text-black mt-4">
            <?php echo $lang === 'es' ? 'Página no encontrada' : 'Page Not Found'; ?>
        </h2>
        <p class="text-slate-500 mt-2 mb-6">
            <?php echo $lang === 'es' ? 'Lo sentimos, la página que buscas no existe o ha sido movida.' : 'Sorry, the page you are looking for does not exist or has been moved.'; ?>
        </p>
        <a href="?page=dashboard&lang=<?php echo $lang; ?>" class="inline-flex items-center justify-center rounded-md bg-primary py-2 px-6 text-center font-medium text-white hover:bg-opacity-90 shadow-md transition-all active:scale-95">
            <?php echo $lang === 'es' ? 'Volver al Inicio' : 'Back to Home'; ?>
        </a>
    </div>
</div>
