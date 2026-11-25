function initSidebarToggle() {
  const sidebar = document.getElementById('sidebar');
  if (!sidebar) return;

  const overlay = document.createElement('div');
  overlay.className = 'sidebar-overlay';
  document.body.appendChild(overlay);

  const isDesktop = () => window.innerWidth >= 992;

  const closeMobile = () => {
    sidebar.classList.remove('sidebar-open');
    overlay.classList.remove('show');
  };

  document.querySelectorAll('[data-sidebar-toggle]').forEach(btn => {
    btn.addEventListener('click', () => {
      if (isDesktop()) {
        sidebar.classList.toggle('sidebar-collapsed');
      } else {
        if (sidebar.classList.contains('sidebar-open')) {
          closeMobile();
        } else {
          sidebar.classList.add('sidebar-open');
          overlay.classList.add('show');
        }
      }
    });
  });

  document.getElementById('sidebarClose')?.addEventListener('click', closeMobile);
  overlay.addEventListener('click', closeMobile);
}

function initUserDropdown() {
  const toggle = document.getElementById('userMenuToggle');
  if (!toggle) return;

  new bootstrap.Dropdown(toggle, {
    popperConfig(defaultConfig) {
      return { ...defaultConfig, strategy: 'fixed' };
    }
  });

  const chevron = toggle.querySelector('.user-menu-chevron');
  if (chevron) {
    toggle.addEventListener('show.bs.dropdown', () => chevron.classList.add('rotated'));
    toggle.addEventListener('hide.bs.dropdown', () => chevron.classList.remove('rotated'));
  }
}

<<<<<<< Updated upstream
function initLogoutConfirm() {
  document.getElementById('confirmLogoutBtn')?.addEventListener('click', () => {
    window.location.href = '#';
=======
// Confirmación de eliminación/anulación: <form class="js-confirm-delete" data-message="...">
function initDeleteConfirm() {
  const modalEl = document.getElementById('deleteModal');
  if (!modalEl) return;

  const modal = new bootstrap.Modal(modalEl);
  const title = document.getElementById('deleteModalLabel');
  const text = document.getElementById('deleteModalText');
  let pending = null;

  document.addEventListener('submit', (e) => {
    const form = e.target.closest('form.js-confirm-delete');
    if (!form || form.dataset.confirmed === '1') return;

    e.preventDefault();
    pending = form;
    title.textContent = form.dataset.title || '¿Eliminar registro?';
    text.textContent = form.dataset.message || 'Esta acción no se puede deshacer.';
    modal.show();
  });

  document.getElementById('confirmDeleteBtn')?.addEventListener('click', () => {
    if (!pending) return;
    pending.dataset.confirmed = '1';
    pending.submit();
>>>>>>> Stashed changes
  });
}

function initTooltips() {
  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
    new bootstrap.Tooltip(el);
  });
}

function initLoginForm() {
  const form = document.getElementById('loginForm');
  if (!form) return;

  const password = document.getElementById('loginPassword');
  const toggle = document.getElementById('togglePassword');
  toggle?.addEventListener('click', () => {
    const visible = password.type === 'text';
    password.type = visible ? 'password' : 'text';
    toggle.setAttribute('aria-label', visible ? 'Mostrar contraseña' : 'Ocultar contraseña');
    toggle.querySelector('i').className = `bi ${visible ? 'bi-eye' : 'bi-eye-slash'} text-secondary`;
  });

  form.addEventListener('submit', (e) => {
<<<<<<< Updated upstream
    e.preventDefault();
    document.getElementById('loginAlert')?.classList.add('d-none');

    if (!form.checkValidity()) {
=======
    if (!form.checkValidity()) {
      e.preventDefault();
>>>>>>> Stashed changes
      form.classList.add('was-validated');
      return;
    }

<<<<<<< Updated upstream
=======
    // El formulario se envía al servidor (Laravel valida las credenciales)
>>>>>>> Stashed changes
    const submit = document.getElementById('loginSubmit');
    submit.disabled = true;
    submit.querySelector('.spinner-border').classList.remove('d-none');
    submit.querySelector('.btn-label').textContent = 'Ingresando...';
<<<<<<< Updated upstream

    // Prototipo: simula la autenticación y redirige al dashboard
    setTimeout(() => { window.location.href = 'dashboard.html'; }, 800);
=======
>>>>>>> Stashed changes
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initSidebarToggle();
  initUserDropdown();
<<<<<<< Updated upstream
  initLogoutConfirm();
=======
  initDeleteConfirm();
>>>>>>> Stashed changes
  initTooltips();
  initLoginForm();
});
