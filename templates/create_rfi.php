<!-- templates/create_rfi.php -->
<?php $lang = $_GET['lang'] ?? 'en'; ?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-2xl font-bold text-black">
        <?php echo $lang === 'es' ? 'Crear Nueva RFI' : 'Create New RFI'; ?>
    </h2>
</div>
<div class="rounded-sm border border-stroke bg-white shadow-default">
    <form action="index.php?lang=<?php echo $lang; ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="action" value="create_rfi">
        <div class="p-6.5">
            <div class="mb-4.5">
                <label class="mb-2.5 block text-black"><?php echo $lang === 'es' ? 'Número de Referencia' : 'Reference Number'; ?> <span class="text-danger">*</span></label>
                <input type="text" name="ref_number" required placeholder="e.g., RFI-001" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
            </div>
            <div class="mb-4.5">
                <label class="mb-2.5 block text-black"><?php echo $lang === 'es' ? 'Asunto' : 'Subject'; ?> <span class="text-danger">*</span></label>
                <input type="text" name="subject" required placeholder="Description" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
            </div>
            <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black"><?php echo $lang === 'es' ? 'Fecha de Vencimiento' : 'Due Date'; ?> <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 outline-none">
                </div>
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black"><?php echo $lang === 'es' ? 'Prioridad' : 'Priority'; ?></label>
                    <select name="priority" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 outline-none">
                        <option value="Low"><?php echo $lang === 'es' ? 'Baja' : 'Low'; ?></option>
                        <option value="Medium" selected><?php echo $lang === 'es' ? 'Media' : 'Medium'; ?></option>
                        <option value="High"><?php echo $lang === 'es' ? 'Alta' : 'High'; ?></option>
                    </select>
                </div>
            </div>
            <button type="submit" class="flex w-full justify-center rounded bg-primary p-3 font-medium text-white hover:bg-opacity-90">
                <?php echo $lang === 'es' ? 'Enviar RFI' : 'Submit RFI'; ?>
            </button>
        </div>
    </form>
</div>
