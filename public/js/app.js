// public/js/app.js - Global UI Logic

class Modal {
    constructor(id) {
        this.modal = document.getElementById(id);
        this.closeButtons = this.modal?.querySelectorAll('[data-modal-close]');
        this.init();
    }

    init() {
        this.closeButtons?.forEach(btn => {
            btn.addEventListener('click', () => this.close());
        });

        this.modal?.addEventListener('click', (e) => {
            if (e.target === this.modal) this.close();
        });
    }

    open() {
        this.modal?.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    close() {
        this.modal?.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

function initMobileMenu() {
    const sidebar = document.querySelector('aside');
    const toggle = document.querySelector('#mobile-toggle');
    const overlay = document.querySelector('#sidebar-overlay');

    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
}

window.addEventListener('DOMContentLoaded', () => {
    initMobileMenu();
    
    // Initialize any modal on the page
    window.modals = {};
    document.querySelectorAll('[id$="-modal"]').forEach(m => {
        window.modals[m.id] = new Modal(m.id);
    });

    // Create Toast Container if it doesn't exist
    if (!document.getElementById('toast-container')) {
        const container = document.createElement('div');
        container.id = 'toast-container';
        document.body.appendChild(container);
    }
});

window.showToast = (message, type = 'success') => {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast ${type === 'error' ? 'border-danger' : 'border-success'}`;
    toast.innerHTML = `<span class="text-sm font-bold text-black">${message}</span>`;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        toast.style.transition = 'all 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
};

// Form Validation
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.hasAttribute('data-validate')) {
        const inputs = form.querySelectorAll('[required]');
        let valid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                valid = false;
                input.classList.add('border-danger');
            } else {
                input.classList.remove('border-danger');
            }
        });

        if (!valid) {
            e.preventDefault();
            const isEs = new URLSearchParams(window.location.search).get('lang') === 'es';
            window.showToast(isEs ? 'Por favor complete todos los campos obligatorios.' : 'Please fill all required fields.', 'error');
        }
    }
});
