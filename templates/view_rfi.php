<!-- templates/view_rfi.php -->
<?php
$rfi = null;
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM rfis WHERE id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    $rfi = $stmt->fetch(PDO::FETCH_ASSOC);
}

function get_priority_color($p) {
    switch ($p) {
        case 'High': return 'text-danger bg-danger bg-opacity-10';
        case 'Medium': return 'text-warning bg-warning bg-opacity-10';
        default: return 'text-success bg-success bg-opacity-10';
    }
}
?>
<?php if ($rfi): ?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black">RFI <?php echo htmlspecialchars($rfi['ref_number']); ?></h2>
        <p class="text-slate-500"><?php echo htmlspecialchars($rfi['subject']); ?></p>
    </div>
    <div class="flex gap-3">
        <a href="?page=rfis&lang=<?php echo $lang; ?>" class="inline-flex items-center justify-center rounded-md border border-stroke bg-white py-2 px-6 text-center font-medium text-black hover:shadow-md transition-all">
            <?php echo $lang === 'es' ? 'Volver' : 'Back'; ?>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-6">
        <!-- RFI Details -->
        <div class="card">
            <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Información de la Solicitud' : 'Request Information'; ?></h3>
            <div class="space-y-4">
                <p class="text-slate-600">
                    <span class="font-bold text-black"><?php echo $lang === 'es' ? 'Fecha de Creación:' : 'Created Date:'; ?></span>
                    <?php echo ($rfi['created_at'] ?? "N/A"); ?>
                </p>
                <div class="p-4 bg-slate-50 rounded border border-stroke italic text-slate-700">
                    <?php echo $lang === 'es' ? 'Por favor proporcione detalles sobre el espaciado de las varillas en el Sector A.' : 'Please provide details regarding the rebar spacing in Sector A.'; ?>
                </div>
            </div>
        </div>

        <!-- Activity Thread -->
        <div class="card">
            <h3 class="text-lg font-bold text-black mb-6 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Actividad y Comentarios' : 'Activity & Comments'; ?></h3>
            <div class="space-y-6">
                <div class="flex gap-4">
                    <span class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name=PM&background=3C50E0&color=fff" alt="User">
                    </span>
                    <div class="flex-1 bg-slate-50 p-4 rounded-lg relative">
                        <p class="text-sm font-bold text-black mb-1">John Doe (PM)</p>
                        <p class="text-sm text-slate-700"><?php echo $lang === 'es' ? '¿Podemos confirmar si esto afecta la ruta crítica?' : 'Can we confirm if this affects the critical path?'; ?></p>
                        <span class="text-xs text-slate-400 mt-2 block">2 hours ago</span>
                    </div>
                </div>
                <div class="flex gap-4">
                    <span class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name=Eng&background=10B981&color=fff" alt="User">
                    </span>
                    <div class="flex-1 bg-slate-50 p-4 rounded-lg">
                        <p class="text-sm font-bold text-black mb-1">Eng. Martinez</p>
                        <p class="text-sm text-slate-700"><?php echo $lang === 'es' ? 'No, el espaciado está dentro de las tolerancias de diseño.' : 'No, the spacing is within design tolerances.'; ?></p>
                        <span class="text-xs text-slate-400 mt-2 block">1 hour ago</span>
                    </div>
                </div>
            </div>
            
            <!-- Comment Input -->
            <div class="mt-8 pt-6 border-t border-stroke">
                <textarea rows="3" placeholder="<?php echo $lang === 'es' ? 'Escribe un comentario...' : 'Write a comment...'; ?>" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary"></textarea>
                <div class="mt-2 flex justify-end">
                    <button class="rounded bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90 shadow-md transition-all">
                        <?php echo $lang === 'es' ? 'Enviar' : 'Post Comment'; ?>
                    </button>
                </div>
            </div>
        </div>

        <!-- Attachments -->
        <div class="card">
            <h3 class="text-lg font-bold text-black mb-4"><?php echo $lang === 'es' ? 'Archivos Adjuntos' : 'Attachments'; ?></h3>
            <div class="grid grid-cols-2 gap-4">
                <div onclick="window.modals['preview-modal'].open()" class="flex items-center gap-3 p-3 border border-stroke rounded hover:bg-slate-50 cursor-pointer transition-all hover:scale-[1.02]">
                    <svg class="fill-danger" width="24" height="24" viewBox="0 0 24 24"><path d="M20 2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-8.5 7.5c0 .83-.67 1.5-1.5 1.5H9v2H7.5V7H10c.83 0 1.5.67 1.5 1.5v1zm5 2c0 .83-.67 1.5-1.5 1.5h-2.5V7H15c.83 0 1.5.67 1.5 1.5v3zm4-3.5h-1.5V11H19V12h-1.5V7h3v1.5zM9 9.5h1v-1H9v1zM14 11h1V8h-1v3z"/></svg>
                    <div class="overflow-hidden">
                        <p class="text-sm font-bold text-black truncate">Revised_S102.pdf</p>
                        <p class="text-xs text-slate-500">1.2 MB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="preview-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden mx-4">
            <div class="p-4 border-b border-stroke flex items-center justify-between">
                <h4 class="font-bold text-black">Revised_S102.pdf</h4>
                <button data-modal-close class="text-slate-500 hover:text-black">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 bg-slate-100 overflow-auto p-10 flex items-center justify-center">
                <div class="bg-white shadow-lg p-20 text-slate-300 italic border border-dashed border-slate-300 rounded">
                    [ PDF Preview Simulation / Simulación de Vista Previa PDF ]
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar Info -->
    <div class="space-y-6">
        <div class="card">
            <h3 class="text-lg font-bold text-black mb-4 border-b border-stroke pb-2"><?php echo $lang === 'es' ? 'Estado y Prioridad' : 'Status & Priority'; ?></h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium"><?php echo $lang === 'es' ? 'Estado' : 'Status'; ?></span>
                    <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold bg-success text-white">
                        <?php echo htmlspecialchars($rfi['status']); ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-medium"><?php echo $lang === 'es' ? 'Prioridad' : 'Priority'; ?></span>
                    <span class="inline-flex rounded-full py-1 px-3 text-xs font-bold <?php echo get_priority_color($rfi['priority']); ?>">
                        <?php echo htmlspecialchars($rfi['priority']); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="text-sm font-bold text-slate-500 uppercase mb-4"><?php echo $lang === 'es' ? 'Viendo Ahora' : 'Currently Viewing'; ?></h3>
            <div class="flex -space-x-2">
                <img src="https://ui-avatars.com/api/?name=JD&background=3C50E0&color=fff" class="h-8 w-8 rounded-full border-2 border-white" data-tooltip="John Doe (PM)">
                <img src="https://ui-avatars.com/api/?name=JS&background=10B981&color=fff" class="h-8 w-8 rounded-full border-2 border-white" data-tooltip="Jane Smith (Arch)">
                <div class="h-8 w-8 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-slate-500">+1</div>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card py-20 text-center">
    <p class="text-xl font-bold text-danger">RFI not found / No se encontró la RFI</p>
    <a href="?page=rfis&lang=<?php echo $lang; ?>" class="text-primary hover:underline mt-4 block">Return to RFIs / Volver a RFIs</a>
</div>
<?php endif; ?>
