<?php
// index.php - Main Entry Point and Router

error_reporting(E_ALL);
ini_set('display_errors', '0');

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile on line $errline");
});

set_exception_handler(function($e) {
    error_log("Uncaught Exception: " . $e->getMessage());
    echo "<h1>Internal Server Error / Error Interno del Servidor</h1>";
});

require_once __DIR__ . '/src/app.php';

$page = $_GET['page'] ?? 'dashboard';
$lang = $_GET['lang'] ?? 'en';

// Include Header
include_once __DIR__ . '/layouts/header.php';
?>

<div class="flex h-screen overflow-hidden">
    <!-- Include Sidebar -->
    <?php include_once __DIR__ . '/layouts/sidebar.php'; ?>

    <!-- Content Area -->
    <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
        <?php include_once __DIR__ . '/layouts/topbar.php'; ?>
        
        <!-- Main Content -->
        <main class="flex-1">
            <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                <?php 
                $template_file = __DIR__ . "/templates/{$page}.php";
                if (!file_exists($template_file)) {
                    $template_file = __DIR__ . "/templates/404.php";
                }
                include $template_file;
                ?>
            </div>
        </main>

        <?php include_once __DIR__ . '/layouts/footer.php'; ?>
    </div>
</div>

<!-- Search Modal -->
<div id="search-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-start justify-center pt-20 hidden">
    <div class="w-full max-w-2xl bg-white rounded-lg shadow-2xl overflow-hidden mx-4">
        <div class="p-4 border-b border-stroke flex items-center gap-3">
            <svg class="fill-slate-400" width="20" height="20" viewBox="0 0 24 24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
            <input type="text" id="modal-search-input" placeholder="Search documentation... / Buscar..." class="w-full text-lg focus:outline-none">
            <kbd class="px-2 py-1 bg-slate-100 border border-stroke rounded text-xs text-slate-500">ESC</kbd>
        </div>
        <div id="modal-search-results" class="max-h-96 overflow-y-auto">
            <div class="p-10 text-center text-slate-400 italic"><?php echo $lang === 'es' ? 'Comienza a escribir para buscar...' : 'Start typing to search...'; ?></div>
        </div>
    </div>
</div>

<!-- Mobile Bottom Nav -->
<div class="fixed bottom-0 left-0 z-9999 w-full bg-white border-t border-stroke lg:hidden flex justify-around py-3">
    <a href="?page=dashboard&lang=<?php echo $lang; ?>" class="flex flex-col items-center gap-1 <?php echo $page === 'dashboard' ? 'text-primary' : 'text-slate-500'; ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
        <span class="text-[10px] font-bold"><?php echo $lang === 'es' ? 'Panel' : 'Home'; ?></span>
    </a>
    <a href="?page=rfis&lang=<?php echo $lang; ?>" class="flex flex-col items-center gap-1 <?php echo $page === 'rfis' ? 'text-primary' : 'text-slate-500'; ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
        <span class="text-[10px] font-bold">RFIs</span>
    </a>
    <a href="?page=tasks&lang=<?php echo $lang; ?>" class="flex flex-col items-center gap-1 <?php echo $page === 'tasks' ? 'text-primary' : 'text-slate-500'; ?>">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"></path></svg>
        <span class="text-[10px] font-bold"><?php echo $lang === 'es' ? 'Tareas' : 'Tasks'; ?></span>
    </a>
    <button onclick="window.modals['search-modal'].open()" class="flex flex-col items-center gap-1 text-slate-500">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <span class="text-[10px] font-bold"><?php echo $lang === 'es' ? 'Buscar' : 'Search'; ?></span>
    </button>
</div>

<!-- AI Chatbot -->
<div id="ai-chat" class="fixed bottom-24 right-6 z-9999 hidden">
    <div class="card w-80 shadow-2xl p-0 overflow-hidden">
        <div class="bg-primary p-4 text-white flex items-center justify-between">
            <span class="font-bold"><?php echo $lang === 'es' ? 'Asistente IA' : 'AI Assistant'; ?></span>
            <button onclick="document.getElementById('ai-chat').classList.add('hidden')" class="hover:text-slate-200">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="h-64 p-4 overflow-y-auto bg-slate-50 text-xs text-slate-600 italic">
            <?php echo $lang === 'es' ? '¿Cómo puedo ayudarte con el proyecto hoy?' : 'How can I help you with the project today?'; ?>
        </div>
        <div class="p-4 border-t border-stroke">
            <input type="text" placeholder="<?php echo $lang === 'es' ? 'Escribe tu pregunta...' : 'Type your question...'; ?>" class="w-full rounded border border-stroke py-2 px-3 text-xs outline-none focus:border-primary">
        </div>
    </div>
</div>

<button onclick="document.getElementById('ai-chat').classList.toggle('hidden')" class="fixed bottom-24 right-6 z-9999 h-14 w-14 bg-primary text-white rounded-full shadow-2xl flex items-center justify-center hover:scale-110 transition-all active:scale-95">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
</button>

<?php include_once __DIR__ . '/layouts/confirm_modal.php'; ?>
<div id="sidebar-overlay" class="fixed inset-0 z-999 bg-black bg-opacity-50 hidden lg:hidden"></div>
