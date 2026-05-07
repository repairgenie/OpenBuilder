<!-- templates/drawing_compare.php -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-black"><?php echo $lang === 'es' ? 'Comparación de Planos' : 'Drawing Comparison'; ?></h2>
    <p class="text-slate-500"><?php echo $lang === 'es' ? 'Comparar V4 (Actual) vs V3 (Anterior).' : 'Compare V4 (Current) vs V3 (Previous).'; ?></p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 h-[600px]">
    <div class="card p-0 overflow-hidden relative border-r-2 border-primary">
        <div class="absolute top-2 left-2 z-10 bg-slate-900 text-white text-[10px] px-2 py-1 rounded font-bold uppercase">Version 4 (Green)</div>
        <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&q=80&w=1000" class="w-full h-full object-cover mix-blend-multiply filter sepia hue-rotate-[90deg] opacity-60" alt="V4">
    </div>
    
    <div class="card p-0 overflow-hidden relative">
        <div class="absolute top-2 left-2 z-10 bg-danger text-white text-[10px] px-2 py-1 rounded font-bold uppercase">Version 3 (Red)</div>
        <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&q=80&w=1000" class="w-full h-full object-cover mix-blend-multiply filter sepia hue-rotate-[-30deg] opacity-60" alt="V3">
    </div>
</div>

<div class="mt-4 flex justify-center gap-4">
    <button class="px-6 py-2 bg-black text-white rounded font-bold text-sm"><?php echo $lang === 'es' ? 'Sincronizar Zoom' : 'Sync Zoom'; ?></button>
    <button class="px-6 py-2 bg-slate-100 text-slate-600 rounded font-bold text-sm"><?php echo $lang === 'es' ? 'Modo Superpuesto' : 'Overlay Mode'; ?></button>
</div>
