// public/js/search.js

let searchIndex = [];

async function loadSearchIndex() {
    try {
        const response = await fetch('public/search.json');
        searchIndex = await response.json();
    } catch (e) {
        console.error('Failed to load search index:', e);
    }
}

function initSearch() {
    const modal = document.querySelector('#search-modal');
    const modalInput = document.querySelector('#modal-search-input');
    const modalResults = document.querySelector('#modal-search-results');

    if (modalInput) {
        modalInput.addEventListener('input', (e) => handleSearch(e.target.value, modalResults));
    }

    // Global Shortcuts
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            modal.classList.remove('hidden');
            modalInput.focus();
        }
        if (e.key === 'Escape') {
            modal.classList.add('hidden');
        }
    });

    modal?.addEventListener('click', (e) => {
        if (e.target === modal) modal.classList.add('hidden');
    });
}

function handleSearch(query, container) {
    query = query.toLowerCase().trim();
    const isEs = new URLSearchParams(window.location.search).get('lang') === 'es';

    if (query.length < 1) {
        container.innerHTML = `
            <div class="p-6">
                <p class="text-xs font-bold text-slate-500 uppercase mb-4">${isEs ? 'Categorías' : 'Categories'}</p>
                <div class="grid grid-cols-2 gap-2">
                    <a href="?page=rfis&lang=${isEs ? 'es' : 'en'}" class="p-3 border border-stroke rounded hover:bg-slate-50 text-sm font-medium text-black flex items-center gap-2">
                        <svg class="fill-primary" width="16" height="16" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                        RFIs
                    </a>
                    <a href="?page=budget&lang=${isEs ? 'es' : 'en'}" class="p-3 border border-stroke rounded hover:bg-slate-50 text-sm font-medium text-black flex items-center gap-2">
                        <svg class="fill-success" width="16" height="16" viewBox="0 0 24 24"><path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 1.9 1.55 3.28 3.68 3.75V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/></svg>
                        Budget
                    </a>
                </div>
            </div>
        `;
        return;
    }

    const currentLang = isEs ? 'es' : 'en';
    const results = searchIndex.filter(item => 
        item.lang === currentLang && 
        (item.title.toLowerCase().includes(query) || item.path.toLowerCase().includes(query))
    );

    if (results.length > 0) {
        container.innerHTML = results.map(item => `
            <a href="${item.url}" class="block px-4 py-3 hover:bg-slate-50 text-sm border-b border-stroke last:border-0 flex items-center gap-3">
                <div class="flex flex-col">
                    <span class="font-bold text-black">${item.title}</span>
                    <span class="text-xs text-slate-500">${item.path}</span>
                </div>
            </a>
        `).join('');
    } else {
        container.innerHTML = `<div class="px-4 py-4 text-sm text-slate-500 text-center">${isEs ? 'No se encontraron resultados' : 'No results found'}</div>`;
    }
}

window.addEventListener('DOMContentLoaded', async () => {
    await loadSearchIndex();
    initSearch();
});
