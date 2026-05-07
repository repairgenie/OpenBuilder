<!-- layouts/pagination.php -->
<?php if ($pagination['total_pages'] > 1): ?>
<div class="mt-4 flex items-center justify-between border-t border-stroke pt-4">
    <div class="text-sm text-slate-500">
        <?php echo $lang === 'es' ? 'Mostrando' : 'Showing'; ?>
        <span class="font-bold text-black"><?php echo count($pagination['items']); ?></span>
        <?php echo $lang === 'es' ? 'de' : 'of'; ?>
        <span class="font-bold text-black"><?php echo $pagination['total_items']; ?></span>
    </div>
    <div class="flex gap-2">
        <?php if ($pagination['current_page'] > 1): ?>
        <a href="?page=<?php echo $page; ?>&p=<?php echo $pagination['current_page'] - 1; ?>&lang=<?php echo $lang; ?>" class="px-3 py-1 border border-stroke rounded bg-white hover:bg-slate-50 text-sm font-medium">
            <?php echo $lang === 'es' ? 'Anterior' : 'Previous'; ?>
        </a>
        <?php endif; ?>
        
        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
        <a href="?page=<?php echo $page; ?>&p=<?php echo $pagination['current_page'] + 1; ?>&lang=<?php echo $lang; ?>" class="px-3 py-1 border border-stroke rounded bg-white hover:bg-slate-50 text-sm font-medium">
            <?php echo $lang === 'es' ? 'Siguiente' : 'Next'; ?>
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>
