<!-- layouts/confirm_modal.php -->
<div id="confirm-modal" class="fixed inset-0 z-9999 bg-black bg-opacity-70 flex items-center justify-center hidden">
    <div class="card max-w-sm w-full p-6 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-danger bg-opacity-10 mb-4">
            <svg class="h-6 w-6 text-danger" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
        </div>
        <h3 class="text-lg font-bold text-black" id="confirm-title"><?php echo $lang === 'es' ? '¿Estás seguro?' : 'Are you sure?'; ?></h3>
        <p class="text-sm text-slate-500 mt-2" id="confirm-body">
            <?php echo $lang === 'es' ? 'Esta acción no se puede deshacer.' : 'This action cannot be undone.'; ?>
        </p>
        <div class="mt-6 flex gap-3">
            <button data-modal-close class="flex-1 rounded border border-stroke py-2 text-sm font-medium text-black hover:bg-slate-50 transition-all">
                <?php echo $lang === 'es' ? 'Cancelar' : 'Cancel'; ?>
            </button>
            <button id="confirm-proceed" class="flex-1 rounded bg-danger py-2 text-sm font-medium text-white hover:bg-opacity-90 shadow-md transition-all">
                <?php echo $lang === 'es' ? 'Confirmar' : 'Confirm'; ?>
            </button>
        </div>
    </div>
</div>
