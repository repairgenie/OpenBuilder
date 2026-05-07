<!-- layouts/sidebar.php -->
<?php
$nav_json = file_get_contents(__DIR__ . '/../src/navigation.json');
$nav_groups = json_decode($nav_json, true);
?>
<aside id="sidebar" class="absolute left-0 top-0 z-999 flex h-screen w-72 flex-col overflow-y-hidden bg-black duration-300 ease-linear lg:static lg:translate-x-0 -translate-x-full" role="navigation" aria-label="Main Navigation">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between gap-2 px-6 py-5 lg:py-6">
        <a href="index.php" class="flex items-center gap-2">
            <div class="h-8 w-8 bg-primary rounded flex items-center justify-center text-white font-bold">OB</div>
            <span class="text-2xl font-bold text-white tracking-tight">OpenBuilder</span>
        </a>
    </div>

    <!-- Sidebar Menu -->
    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <nav class="mt-5 py-4 px-4 lg:mt-9 lg:px-6">
            <?php foreach ($nav_groups as $group): ?>
                <div class="mb-6">
                    <h3 class="mb-4 ml-4 text-sm font-semibold text-slate-500 uppercase tracking-widest">
                        <?php echo $lang === 'es' ? $group['group_es'] : $group['group_en']; ?>
                    </h3>
                    <ul class="flex flex-col gap-1.5">
                        <?php foreach ($group['items'] as $item): 
                            if (!ModuleRegistry::isEnabled($item['id'])) continue;
                            $isActive = ($page === $item['id']);
                        ?>
                            <li>
                                <a href="?page=<?php echo $item['id']; ?>&lang=<?php echo $lang; ?>" 
                                   class="group relative flex items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-slate-300 duration-300 ease-in-out hover:bg-slate-800 <?php echo $isActive ? 'bg-slate-800 text-white' : ''; ?>">
                                    <span class="text-slate-400 group-hover:text-white">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                                    </span>
                                    <?php echo $lang === 'es' ? $item['name_es'] : $item['name_en']; ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </nav>
    </div>
</aside>
