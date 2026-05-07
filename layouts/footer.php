<!-- layouts/footer.php -->
<footer class="mt-auto py-6 px-4 md:px-10 text-center border-t border-stroke bg-white">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-xs text-slate-500">
            &copy; 2023 OpenBuilder. <?php echo $lang === 'es' ? 'Todos los derechos reservados.' : 'All rights reserved.'; ?>
        </p>
        
        <!-- Performance Badge -->
        <div class="flex items-center gap-2 px-3 py-1 bg-success bg-opacity-10 rounded-full border border-success border-opacity-20">
            <span class="h-2 w-2 rounded-full bg-success animate-pulse"></span>
            <span class="text-[10px] font-bold text-success uppercase">
                <?php echo $lang === 'es' ? 'Rendimiento: ' : 'Performance: '; ?> 124ms
            </span>
        </div>
        
        <div class="flex gap-4">
            <a href="#" class="text-xs text-slate-500 hover:text-primary transition-colors"><?php echo $lang === 'es' ? 'Privacidad' : 'Privacy'; ?></a>
            <a href="#" class="text-xs text-slate-500 hover:text-primary transition-colors"><?php echo $lang === 'es' ? 'Términos' : 'Terms'; ?></a>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="public/js/app.js?v=<?php echo ASSET_VERSION; ?>"></script>
</body>
</html>
