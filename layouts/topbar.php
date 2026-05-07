<!-- layouts/topbar.php -->
<header class="sticky top-0 z-999 flex w-full bg-white drop-shadow-1">
    <div class="flex flex-grow items-center justify-between px-4 py-4 shadow-2 md:px-6 2xl:px-11">
        <div class="flex items-center gap-2 sm:gap-4 lg:hidden">
            <!-- Mobile Toggle -->
            <button id="mobile-toggle" class="z-99999 block rounded-sm border border-slate-200 bg-white p-1.5 shadow-sm lg:hidden">
                <span class="relative block h-5.5 w-5.5 cursor-pointer">
                    <span class="block absolute right-0 h-full w-full">
                        <span class="relative left-0 top-0 my-1 block h-0.5 w-full rounded-sm bg-black"></span>
                        <span class="relative left-0 top-0 my-1 block h-0.5 w-full rounded-sm bg-black"></span>
                        <span class="relative left-0 top-0 my-1 block h-0.5 w-full rounded-sm bg-black"></span>
                    </span>
                </span>
            </button>
            <a class="block flex-shrink-0 lg:hidden" href="index.php">
                <span class="text-xl font-black text-primary">OB</span>
            </a>
        </div>

        <!-- Breadcrumbs -->
        <nav class="hidden sm:flex items-center gap-2 text-xs font-bold uppercase text-slate-500 mr-4">
            <a href="?page=dashboard&lang=<?php echo $lang; ?>" class="hover:text-primary transition-colors">Home</a>
            <?php if ($page !== 'dashboard'): ?>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="9 18 15 12 9 6"></polyline></svg>
                <span class="text-black"><?php echo ucfirst(str_replace('_', ' ', $page)); ?></span>
            <?php endif; ?>
        </nav>

        <div class="hidden sm:flex items-center gap-4">
            <!-- Project Selector -->
            <div class="relative group">
                <button class="flex items-center gap-2 text-sm font-bold text-black border border-stroke rounded-md px-3 py-1.5 hover:bg-slate-50 transition-all">
                    <svg class="fill-primary" width="18" height="18" viewBox="0 0 24 24"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                    <span><?php echo $lang === 'es' ? 'Proyecto: Torre Norte' : 'Project: North Tower'; ?></span>
                    <svg class="fill-slate-400 group-hover:rotate-180 transition-transform" width="12" height="12" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5H7z"/></svg>
                </button>
                <div class="absolute top-full left-0 mt-2 w-60 bg-white border border-stroke shadow-md rounded-sm hidden group-hover:block z-999">
                    <div class="p-2">
                        <a href="#" class="block p-2 hover:bg-slate-50 rounded text-sm font-medium text-black border-l-4 border-primary bg-primary bg-opacity-5">
                            <?php echo $lang === 'es' ? 'Torre Norte' : 'North Tower'; ?>
                        </a>
                        <a href="#" class="block p-2 hover:bg-slate-50 rounded text-sm font-medium text-slate-600 border-l-4 border-transparent">
                            <?php echo $lang === 'es' ? 'Residencias Oceanía' : 'Oceania Residences'; ?>
                        </a>
                        <a href="#" class="block p-2 hover:bg-slate-50 rounded text-sm font-medium text-slate-600 border-l-4 border-transparent">
                            <?php echo $lang === 'es' ? 'Centro Logístico' : 'Logistics Center'; ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Breadcrumbs -->
            <nav class="flex text-sm text-slate-500">
                <ol class="list-none p-0 inline-flex">
                    <li class="flex items-center">
                        <a href="?page=dashboard&lang=<?php echo $lang; ?>" class="hover:text-primary">OpenBuilder</a>
                        <svg class="fill-current w-3 h-3 mx-2" viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                    </li>
                    <li class="flex items-center text-slate-800 font-bold capitalize">
                        <?php echo $page_titles[$page][$lang] ?? str_replace('_', ' ', $page); ?>
                    </li>
                    <?php if (isset($_GET['doc'])): ?>
                    <li class="flex items-center">
                        <svg class="fill-current w-3 h-3 mx-2" viewBox="0 0 24 24"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg>
                        <span class="text-slate-800 font-bold"><?php echo htmlspecialchars($_GET['doc']); ?></span>
                    </li>
                    <?php endif; ?>
                </ol>
            </nav>
            <form action="index.php" method="GET">
                <input type="hidden" name="page" value="rfis">
                <div class="relative">
                    <button class="absolute left-0 top-1/2 -translate-y-1/2">
                        <svg class="fill-slate-500 hover:fill-primary" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.16666 3.33332C5.945 3.33332 3.33332 5.945 3.33332 9.16666C3.33332 12.3883 5.945 15 9.16666 15C12.3883 15 15 12.3883 15 9.16666C15 5.945 12.3883 3.33332 9.16666 3.33332ZM1.66666 9.16666C1.66666 5.02452 5.02452 1.66666 9.16666 1.66666C13.3088 1.66666 16.6667 5.02452 16.6667 9.16666C16.6667 11.0028 15.9922 12.6841 14.8876 13.9875L18.0893 17.1892C18.4147 17.5147 18.4147 18.0423 18.0893 18.3677C17.7638 18.6932 17.2362 18.6932 16.9107 18.3677L13.709 15.166C12.4056 16.2706 10.7243 16.9451 8.8882 16.9451C4.74606 16.9451 1.3882 13.5872 1.3882 9.4451C1.3882 5.30296 4.74606 1.9451 8.8882 1.9451C13.0303 1.9451 16.3882 5.30296 16.3882 9.4451C16.3882 13.5872 13.0303 16.9451 8.8882 16.9451Z"></path>
                        </svg>
                    </button>
                    <input type="text" name="search" placeholder="Search... / Buscar..." class="w-full bg-transparent pl-9 pr-4 font-medium focus:outline-none xl:w-125">
                </div>
            </form>
        </div>

        <div class="flex items-center gap-3 2x:gap-7">
            <!-- Notifications -->
            <div class="relative group">
                <button class="relative flex h-8.5 w-8.5 items-center justify-center rounded-full border border-stroke bg-slate-50 hover:text-primary">
                    <span class="absolute -top-0.5 right-0 z-1 h-3 w-3 rounded-full bg-danger border-2 border-white animate-pulse"></span>
                    <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.1999 14.9343L15.6374 14.0624C15.5249 13.8937 15.4687 13.7249 15.4687 13.528V7.67803C15.4687 4.52803 13.4437 1.88428 10.4624 1.32178V0.759277C10.4624 0.337402 10.1249 0 9.70303 0C9.28115 0 8.94365 0.337402 8.94365 0.759277V1.35C5.9624 1.9125 3.9374 4.55625 3.9374 7.70625V13.5562C3.9374 13.7531 3.88115 13.9218 3.76865 14.0906L3.20615 14.9624C2.98115 15.3 3.20615 15.75 3.5999 15.75H15.8062C16.1999 15.75 16.4249 15.3 16.1999 14.9343Z"></path>
                    </svg>
                </button>
                
                <!-- Dropdown -->
                <div class="absolute right-0 mt-2.5 flex h-90 w-75 flex-col rounded-sm border border-stroke bg-white shadow-default hidden group-hover:block z-999">
                    <div class="px-4.5 py-3">
                        <h5 class="text-sm font-medium text-bodydark2"><?php echo $lang === 'es' ? 'Notificaciones' : 'Notifications'; ?></h5>
                    </div>
                    <ul class="flex h-auto flex-col overflow-y-auto">
                        <li>
                            <a class="flex flex-col gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-slate-50" href="#">
                                <p class="text-sm">
                                    <span class="text-black font-bold"><?php echo $lang === 'es' ? 'Nueva RFI asignada' : 'New RFI assigned'; ?></span>
                                    #102 - Beam Specs
                                </p>
                                <p class="text-xs">12 Oct, 2023</p>
                            </a>
                        </li>
                        <li>
                            <a class="flex flex-col gap-2.5 border-t border-stroke px-4.5 py-3 hover:bg-slate-50" href="#">
                                <p class="text-sm">
                                    <span class="text-black font-bold"><?php echo $lang === 'es' ? 'Informe de IA listo' : 'AI Report Ready'; ?></span>
                                    <?php echo $lang === 'es' ? 'Diario del 24 de Oct' : 'Daily Log Oct 24'; ?>
                                </p>
                                <p class="text-xs">24 Oct, 2023</p>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Language Switcher -->
            <div class="flex gap-2 mr-4">
                <a href="?page=<?php echo $page; ?>&lang=en" class="px-2 py-1 text-xs font-bold rounded <?php echo $lang === 'en' ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600'; ?>">EN</a>
                <a href="?page=<?php echo $page; ?>&lang=es" class="px-2 py-1 text-xs font-bold rounded <?php echo $lang === 'es' ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600'; ?>">ES</a>
            </div>
            
            <div class="relative">
                <a class="flex items-center gap-4" href="#">
                    <span class="hidden text-right lg:block">
                        <span class="block text-sm font-medium text-black">John Doe</span>
                        <span class="block text-xs"><?php echo $lang === 'es' ? 'Gerente de Proyecto' : 'Project Manager'; ?></span>
                    </span>
                    <span class="h-12 w-12 rounded-full overflow-hidden">
                        <img src="https://ui-avatars.com/api/?name=John+Doe&background=3C50E0&color=fff&size=128" alt="User">
                    </span>
                </a>
            </div>
        </div>
    </div>
</header>
