<!-- templates/create_rfi.php -->
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <h2 class="text-2xl font-bold text-black">Create New RFI / Crear Nueva RFI</h2>
</div>
<div class="rounded-sm border border-stroke bg-white shadow-default">
    <form action="index.php" method="POST">
        <input type="hidden" name="action" value="create_rfi">
        <div class="p-6.5">
            <div class="mb-4.5">
                <label class="mb-2.5 block text-black">Reference Number / Número de Referencia <span class="text-danger">*</span></label>
                <input type="text" name="ref_number" required placeholder="e.g., RFI-001" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
            </div>
            <div class="mb-4.5">
                <label class="mb-2.5 block text-black">Subject / Asunto <span class="text-danger">*</span></label>
                <input type="text" name="subject" required placeholder="Description" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
            </div>
            <div class="mb-4.5 flex flex-col gap-6 xl:flex-row">
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black">Due Date / Fecha de Vencimiento <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" required class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary">
                </div>
                <div class="w-full xl:w-1/2">
                    <label class="mb-2.5 block text-black">Priority / Prioridad</label>
                    <select name="priority" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 outline-none">
                        <option value="Low">Low / Baja</option>
                        <option value="Medium" selected>Medium / Media</option>
                        <option value="High">High / Alta</option>
                    </select>
                </div>
            </div>
            <button class="flex w-full justify-center rounded bg-primary p-3 font-medium text-white hover:bg-opacity-90">
                Submit RFI / Enviar RFI
            </button>
        </div>
    </form>
</div>
