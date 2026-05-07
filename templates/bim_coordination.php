<!-- templates/bim_coordination.php -->
<div class="mb-6 flex items-center justify-between">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Coordinación 2D/3D' : '2D/3D Coordination'; ?></h2>
    <div class="flex gap-2">
        <button class="px-4 py-2 bg-slate-100 text-slate-600 rounded text-sm font-bold">Split View</button>
        <button class="px-4 py-2 bg-primary text-white rounded text-sm font-bold">Overlay</button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 h-[500px]">
    <!-- 2D View -->
    <div class="card bg-white p-0 overflow-hidden relative">
        <div class="absolute top-2 left-2 z-10 bg-black bg-opacity-75 text-white text-[10px] px-2 py-1 rounded">2D PLAN: LEVEL 2</div>
        <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&q=80&w=1000" class="w-full h-full object-cover opacity-50 grayscale" alt="Floor Plan">
    </div>
    
    <!-- 3D View -->
    <div class="card bg-slate-900 p-0 overflow-hidden relative">
        <div class="absolute top-2 left-2 z-10 bg-primary text-white text-[10px] px-2 py-1 rounded">3D MODEL: PERSPECTIVE</div>
        <div class="w-full h-full flex items-center justify-center">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="#3C50E0" stroke-width="1" class="animate-pulse"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path></svg>
        </div>
    </div>
</div>
