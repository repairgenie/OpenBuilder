<!-- templates/bim_viewer.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Visor BIM' : 'BIM Viewer'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Navegación de modelos 3D y coordinación de campo.' : '3D Model navigation and field coordination.'; ?></p>
</div>

<div class="card relative h-[600px] bg-slate-900 overflow-hidden rounded-xl border-4 border-slate-800">
    <!-- BIM Scene Simulation -->
    <div id="bim-scene" class="w-full h-full flex items-center justify-center">
        <div class="text-center">
            <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="#3C50E0" stroke-width="1" class="animate-pulse mb-4 mx-auto"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
            <p class="text-slate-400 font-mono text-sm">LOADING ARCHITECTURAL_MODEL_V2.IFC ...</p>
        </div>
    </div>

    <!-- Viewer Controls -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-4 bg-black bg-opacity-50 p-3 rounded-full border border-white border-opacity-20 backdrop-blur-md">
        <button title="Orbit" class="p-2 text-white hover:text-primary"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg></button>
        <button title="Walk" class="p-2 text-white hover:text-primary"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg></button>
        <button title="Pan" class="p-2 text-white hover:text-primary"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="5 9 2 12 5 15"></polyline><polyline points="9 5 12 2 15 5"></polyline><polyline points="15 19 12 22 9 19"></polyline><polyline points="19 9 22 12 19 15"></polyline><line x1="2" y1="12" x2="22" y2="12"></line><line x1="12" y1="2" x2="12" y2="22"></line></svg></button>
        <button title="Zoom" class="p-2 text-white hover:text-primary"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg></button>
        <button title="VR/AR Preview" class="p-2 text-white hover:text-primary"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10h16v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8z"></path><path d="M4 10V6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v4"></path><circle cx="9" cy="14" r="1"></circle><circle cx="15" cy="14" r="1"></circle></svg></button>
        <div class="w-px bg-white bg-opacity-20 mx-2"></div>
        <button title="Clash Detection" class="p-2 text-warning hover:text-white"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></button>
    </div>
</div>
