<!-- templates/final_walkthrough.php -->
<div class="fixed inset-0 z-9999 bg-white flex flex-col">
    <!-- Walkthrough Header -->
    <div class="p-4 bg-primary text-white flex items-center justify-between">
        <h3 class="font-bold"><?php echo $lang === 'es' ? 'Recorrido Final' : 'Final Walkthrough'; ?></h3>
        <button class="text-white hover:opacity-75"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
    </div>
    
    <!-- Progress Tracking -->
    <div class="p-4 border-b border-stroke flex items-center justify-between">
        <div class="flex-1 mr-4">
            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-success w-[65%]"></div>
            </div>
        </div>
        <span class="text-xs font-bold text-slate-500">65% Complete</span>
    </div>
    
    <!-- Active Item -->
    <div class="flex-1 flex flex-col items-center justify-center p-6 text-center">
        <div class="h-48 w-48 bg-slate-100 rounded-xl mb-6 flex items-center justify-center text-slate-300">
            <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
        </div>
        <h4 class="text-xl font-bold text-black mb-2">Punch Item #PL-015</h4>
        <p class="text-slate-500 mb-8"><?php echo $lang === 'es' ? 'Verificar pintura en marco de puerta.' : 'Verify paint touch-up on door frame.'; ?></p>
        
        <div class="flex gap-4 w-full max-w-xs">
            <button class="flex-1 py-4 bg-danger text-white font-bold rounded-lg shadow-lg">Reject</button>
            <button class="flex-1 py-4 bg-success text-white font-bold rounded-lg shadow-lg">Accept</button>
        </div>
    </div>
</div>
