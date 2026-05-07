<!-- templates/distribution_lists.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Listas de Distribución' : 'Distribution Lists'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Gestionar quién recibe notificaciones para RFIs y Submittals.' : 'Manage who receives notifications for RFIs and Submittals.'; ?></p>
</div>

<div class="card bg-white">
    <div class="space-y-4">
        <!-- List Row -->
        <div class="flex items-center justify-between p-4 border border-stroke rounded-lg">
            <div>
                <h4 class="text-sm font-bold text-black">Architectural Review Team</h4>
                <p class="text-xs text-slate-500">3 members • Leads: John Doe, Jane Smith</p>
            </div>
            <button class="text-xs font-bold text-primary hover:underline">Edit Members</button>
        </div>
        
        <div class="flex items-center justify-between p-4 border border-stroke rounded-lg">
            <div>
                <h4 class="text-sm font-bold text-black">Project Stakeholders (Owner)</h4>
                <p class="text-xs text-slate-500">2 members • Leads: Robert Brown</p>
            </div>
            <button class="text-xs font-bold text-primary hover:underline">Edit Members</button>
        </div>
    </div>
</div>
