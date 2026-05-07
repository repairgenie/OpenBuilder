<!-- templates/drawings_v2.php -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Planos y Documentación' : 'Drawings & Documentation'; ?></h2>
        <p class="text-slate-500"><?php echo $lang === 'es' ? 'Visor de alta resolución con control de versiones.' : 'High-resolution viewer with version control.'; ?></p>
    </div>
    <div class="flex gap-2">
        <button class="rounded-md bg-white border border-stroke py-2 px-4 font-medium text-black hover:bg-slate-50">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline mr-1"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            <?php echo $lang === 'es' ? 'Descargar Todo' : 'Download All'; ?>
        </button>
        <button class="rounded-md bg-primary py-2 px-6 font-medium text-white hover:bg-opacity-90">
            <?php echo $lang === 'es' ? 'Subir Planos' : 'Upload Drawings'; ?>
        </button>
    </div>
</div>

<div class="mb-6 flex gap-2 overflow-x-auto pb-2">
    <button class="px-4 py-1 bg-black text-white rounded-full text-xs font-bold"><?php echo $lang === 'es' ? 'Todos' : 'All'; ?></button>
    <button class="px-4 py-1 bg-white border border-stroke text-slate-600 rounded-full text-xs font-bold"><?php echo $lang === 'es' ? 'Arquitectura' : 'Architectural'; ?></button>
    <button class="px-4 py-1 bg-white border border-stroke text-slate-600 rounded-full text-xs font-bold"><?php echo $lang === 'es' ? 'Estructural' : 'Structural'; ?></button>
    <button class="px-4 py-1 bg-white border border-stroke text-slate-600 rounded-full text-xs font-bold"><?php echo $lang === 'es' ? 'Mecánico' : 'Mechanical'; ?></button>
    <button class="px-4 py-1 bg-white border border-stroke text-slate-600 rounded-full text-xs font-bold"><?php echo $lang === 'es' ? 'Eléctrico' : 'Electrical'; ?></button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- Drawing Card -->
    <div class="card p-0 overflow-hidden hover:border-primary transition-colors cursor-pointer group">
        <div class="h-40 bg-slate-100 overflow-hidden relative">
            <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover group-hover:scale-110 duration-500" alt="Drawing Thumbnail">
            <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white text-[10px] px-2 py-1 rounded font-bold">V4</div>
        </div>
        <div class="p-4">
            <h4 class="font-bold text-black text-sm mb-1 truncate">A-101 Floor Plan - Level 1</h4>
            <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold"><?php echo $lang === 'es' ? 'Arquitectura' : 'Architectural'; ?></p>
        </div>
    </div>
</div>
