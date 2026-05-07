<!-- templates/bim_versions.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Versiones del Modelo' : 'Model Versions'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Historial de revisiones de modelos BIM.' : 'Revision history of BIM models.'; ?></p>
</div>

<div class="card">
    <div class="flex flex-col gap-4">
        <!-- Version Row -->
        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-stroke">
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 bg-primary bg-opacity-10 text-primary rounded flex items-center justify-center font-bold">V3</div>
                <div>
                    <h4 class="text-sm font-bold text-black">Structural_Model_Final.rvt</h4>
                    <p class="text-xs text-slate-500">Uploaded by Jane Smith • 2 hours ago</p>
                </div>
            </div>
            <button class="px-4 py-1 bg-primary text-white text-xs font-bold rounded">Active</button>
        </div>
        
        <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-stroke">
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 bg-slate-100 text-slate-400 rounded flex items-center justify-center font-bold">V2</div>
                <div>
                    <h4 class="text-sm font-bold text-black">Structural_Model_RevB.rvt</h4>
                    <p class="text-xs text-slate-500">Uploaded by John Doe • Oct 25, 2023</p>
                </div>
            </div>
            <button class="px-4 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded">Restore</button>
        </div>
    </div>
</div>
