/**
 * Sistema de Becas Escolares - JavaScript Principal
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Sidebar toggle (admin) ─────────────────────────────────
    const sidebar   = document.getElementById('sidebar');
    const hamburger = document.getElementById('sidebarHamburger');
    const toggle    = document.getElementById('sidebarToggle');

    if (hamburger && sidebar) {
        hamburger.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.remove('open');
        });
    }

    // Cerrar sidebar al hacer click fuera (móvil)
    document.addEventListener('click', function (e) {
        if (!sidebar) return;
        if (sidebar.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            hamburger && !hamburger.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });

    // ── Auto-cerrar alertas ───────────────────────────────────
    document.querySelectorAll('.alert.fade.show').forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 6000);
    });

    // ── CURP: auto-mayúsculas ─────────────────────────────────
    document.querySelectorAll('input[name="curp"]').forEach(function (input) {
        input.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    });

    // ── Confirmación antes de eliminar ────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const msg = this.dataset.confirm || '¿Estás seguro?';
            if (!confirm(msg)) e.preventDefault();
        });
    });

    // ── Validación básica de formularios ──────────────────────
    document.querySelectorAll('form[novalidate]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // ── Preview de imagen/archivo seleccionado ────────────────
    document.querySelectorAll('input[type="file"]').forEach(function (input) {
        input.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const sibling = this.nextElementSibling;
            if (sibling && sibling.classList.contains('file-preview-name')) {
                sibling.textContent = file.name;
            }
        });
    });

    // ── Tooltips Bootstrap ────────────────────────────────────
    document.querySelectorAll('[title]').forEach(function (el) {
        new bootstrap.Tooltip(el, { trigger: 'hover' });
    });

    // ── Animación de KPI números ──────────────────────────────
    document.querySelectorAll('.kpi-value').forEach(function (el) {
        const raw = el.textContent.replace(/[^0-9]/g, '');
        if (!raw || isNaN(raw)) return;
        const target = parseInt(raw);
        const prefix = el.textContent.startsWith('$') ? '$' : '';
        const suffix = el.textContent.endsWith('%') ? '%' : '';
        let current  = 0;
        const step   = Math.ceil(target / 40);
        const timer  = setInterval(function () {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = prefix + current.toLocaleString('es-MX') + suffix;
        }, 30);
    });

    // ── Scroll suave a mensajes ───────────────────────────────
    const mensajesContainer = document.querySelector('.mensajes-container');
    if (mensajesContainer) {
        mensajesContainer.scrollTop = mensajesContainer.scrollHeight;
    }
});

/**
 * Toggle visibilidad de password
 * @param {string} inputId
 * @param {HTMLElement} btn
 */
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon  = btn.querySelector('i');
    if (!input || !icon) return;
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

/**
 * Muestra un toast de notificación
 * @param {string} message
 * @param {string} type  success|danger|warning|info
 */
function showToast(message, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = 9999;
        document.body.appendChild(container);
    }
    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${type} border-0`;
    toastEl.setAttribute('role', 'alert');
    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>`;
    container.appendChild(toastEl);
    const bsToast = new bootstrap.Toast(toastEl, { delay: 4000 });
    bsToast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
}
