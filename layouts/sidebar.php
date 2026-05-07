<?php
$nav_items = json_decode(file_get_contents(__DIR__ . '/../src/navigation.json'), true);
?>
<aside aria-label="Main Navigation" role="navigation" class="absolute left-0 top-0 z-9999 flex h-screen w-72.5 flex-col overflow-y-hidden bg-black duration-300 ease-linear lg:static lg:translate-x-0 -translate-x-full">
    <!-- Sidebar Header -->
    <div class="flex items-center justify-between gap-2 px-6 py-5.5 lg:py-6.5">
        <a href="index.php">
            <span class="text-2xl font-black text-white">OpenBuilder</span>
        </a>
    </div>

    <div class="no-scrollbar flex flex-col overflow-y-auto duration-300 ease-linear">
        <nav class="mt-5 py-4 px-4 lg:mt-9 lg:px-6">
            <div>
                <h3 class="mb-4 ml-4 text-sm font-semibold text-bodydark2 uppercase"><?php echo $lang === 'es' ? 'MENÚ' : 'MENU'; ?></h3>
                <ul class="mb-6 flex flex-col gap-1.5">
                    <?php foreach ($nav_items as $item): ?>
                    <li>
                        <a href="?page=<?php echo $item['path']; ?>&lang=<?php echo $lang; ?>" class="group relative flex items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-graydark <?php echo $page === $item['path'] ? 'bg-graydark' : ''; ?>">
                            <!-- Simplified Icons -->
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18">
                                <path d="M15.75 3.375H2.25C1.62891 3.375 1.125 3.87891 1.125 4.5V13.5C1.125 14.1211 1.62891 14.625 2.25 14.625H15.75C16.3711 14.625 16.875 14.1211 16.875 13.5V4.5C16.875 3.87891 16.3711 3.375 15.75 3.375Z"></path>
                            </svg>
                            <?php echo $item['title'][$lang]; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Admin Section -->
            <div class="mt-10">
                <h3 class="mb-4 ml-4 text-sm font-semibold text-bodydark2 uppercase"><?php echo $lang === 'es' ? 'ADMINISTRACIÓN' : 'ADMIN'; ?></h3>
                <ul class="flex flex-col gap-1.5">
                    <li>
                        <a href="?page=users&lang=<?php echo $lang; ?>" class="group relative flex items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-graydark <?php echo $page === 'users' ? 'bg-graydark' : ''; ?>">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            <?php echo $lang === 'es' ? 'Usuarios' : 'Users'; ?>
                        </a>
                    </li>
                    <li>
                        <a href="?page=project_settings&lang=<?php echo $lang; ?>" class="group relative flex items-center gap-2.5 rounded-sm py-2 px-4 font-medium text-bodydark1 duration-300 ease-in-out hover:bg-graydark <?php echo $page === 'project_settings' ? 'bg-graydark' : ''; ?>">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24"><path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                            <?php echo $lang === 'es' ? 'Ajustes' : 'Settings'; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</aside>
