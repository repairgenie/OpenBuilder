<!-- templates/drawing_markup.php -->
<div class="fixed inset-0 z-9999 bg-black bg-opacity-90 flex flex-col">
    <!-- Markup Header -->
    <div class="flex items-center justify-between p-4 bg-slate-900 border-b border-white border-opacity-10">
        <div class="flex items-center gap-4">
            <button class="text-white hover:text-primary"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
            <h3 class="text-white font-bold">A-101 Markup</h3>
        </div>
        <div class="flex gap-2">
            <select class="bg-slate-800 text-white text-xs border border-white border-opacity-20 rounded px-2 outline-none">
                <option><?php echo $lang === 'es' ? 'Solo Yo (Privado)' : 'Only Me (Private)'; ?></option>
                <option><?php echo $lang === 'es' ? 'Todos (Público)' : 'Everyone (Public)'; ?></option>
            </select>
            <button class="px-4 py-1 bg-success text-white text-sm font-bold rounded"><?php echo $lang === 'es' ? 'Publicar' : 'Publish'; ?></button>
        </div>
    </div>
    
    <!-- Drawing Canvas Simulation -->
    <div class="flex-1 relative overflow-hidden flex items-center justify-center">
        <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&q=80&w=2000" class="max-w-full max-h-full object-contain" alt="Drawing">
        
        <!-- Mock Markup -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 border-4 border-danger rounded-full flex items-center justify-center">
            <span class="bg-danger text-white text-[10px] px-2 py-1 rounded font-bold uppercase">Cloud</span>
        </div>
    </div>
    
    <!-- Markup Tools -->
    <div class="p-4 bg-slate-900 flex justify-center gap-6 border-t border-white border-opacity-10">
        <button class="text-white hover:text-primary"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path><path d="M2 2l5 5"></path><path d="M9.5 14.5L16 8"></path></svg></button>
        <button class="text-white hover:text-primary"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect></svg></button>
        <button class="text-white hover:text-primary"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg></button>
        <button class="text-white hover:text-primary"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg></button>
        <div class="w-px bg-white bg-opacity-10 h-6"></div>
        <button class="text-danger"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4"></path><path d="M12 16h.01"></path></svg></button>
    </div>
</div>
