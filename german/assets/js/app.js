// ---------------------------------------------------------------------
// Theme (light / dark) toggle — persisted in localStorage
// ---------------------------------------------------------------------
(function () {
  const root = document.documentElement;
  const stored = localStorage.getItem('gurm-theme');
  if (stored) {
    root.setAttribute('data-bs-theme', stored);
  } else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
    root.setAttribute('data-bs-theme', 'dark');
  }

  function updateIcon() {
    const btn = document.getElementById('themeToggleBtn');
    if (!btn) return;
    const isDark = root.getAttribute('data-bs-theme') === 'dark';
    btn.innerHTML = isDark ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon-stars"></i>';
  }

  document.addEventListener('DOMContentLoaded', function () {
    updateIcon();
    const btn = document.getElementById('themeToggleBtn');
    if (btn) {
      btn.addEventListener('click', function () {
        const current = root.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        root.setAttribute('data-bs-theme', next);
        localStorage.setItem('gurm-theme', next);
        updateIcon();
      });
    }

    // Mobile sidebar toggle
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    function closeSidebar() {
      sidebar && sidebar.classList.remove('show');
      backdrop && backdrop.classList.remove('show');
    }
    if (toggleBtn) {
      toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('show');
        backdrop.classList.toggle('show');
      });
    }
    if (backdrop) {
      backdrop.addEventListener('click', closeSidebar);
    }

    // Wire up any [data-confirm-delete] trigger to the shared modal.
    // Expected data attributes: data-delete-action (form action URL),
    // data-delete-id, data-delete-uni-id (optional), data-delete-text.
    document.querySelectorAll('[data-confirm-delete]').forEach(function (el) {
      el.addEventListener('click', function (e) {
        e.preventDefault();
        const modalEl = document.getElementById('confirmDeleteModal');
        const form = document.getElementById('confirmDeleteForm');
        const text = document.getElementById('confirmDeleteText');
        const idField = document.getElementById('confirmDeleteId');
        const uniField = document.getElementById('confirmDeleteUniId');
        form.action = el.getAttribute('data-delete-action');
        idField.value = el.getAttribute('data-delete-id') || '';
        uniField.value = el.getAttribute('data-delete-uni-id') || '';
        text.textContent = el.getAttribute('data-delete-text') || 'Are you sure you want to delete this item? This cannot be undone.';
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
      });
    });

    // Auto-dismiss flash alerts after a while
    document.querySelectorAll('.flash-stack .alert').forEach(function (alertEl) {
      setTimeout(function () {
        const alert = bootstrap.Alert.getOrCreateInstance(alertEl);
        if (alert) alert.close();
      }, 6000);
    });
  });
})();

// ---------------------------------------------------------------------
// Client-side table filter helper — used on explorer pages.
// Filters rows of a table based on multiple <select>/<input> filter
// controls that share a [data-filter-col] attribute matching a
// data-col-* attribute on each <tr>.
// ---------------------------------------------------------------------
function initTableFilter(tableSelector, filterFormSelector, searchInputSelector) {
  const table = document.querySelector(tableSelector);
  const form = document.querySelector(filterFormSelector);
  const searchInput = searchInputSelector ? document.querySelector(searchInputSelector) : null;
  if (!table) return;

  function applyFilters() {
    const rows = table.querySelectorAll('tbody tr');
    const filters = {};
    if (form) {
      form.querySelectorAll('[data-filter-col]').forEach(function (input) {
        const col = input.getAttribute('data-filter-col');
        const val = input.value.trim().toLowerCase();
        if (val) filters[col] = val;
      });
    }
    const searchTerm = searchInput ? searchInput.value.trim().toLowerCase() : '';

    rows.forEach(function (row) {
      let visible = true;
      for (const col in filters) {
        const cellVal = (row.getAttribute('data-col-' + col) || '').toLowerCase();
        if (cellVal !== filters[col]) {
          visible = false;
          break;
        }
      }
      if (visible && searchTerm) {
        const rowText = row.textContent.toLowerCase();
        if (!rowText.includes(searchTerm)) visible = false;
      }
      row.style.display = visible ? '' : 'none';
    });
  }

  if (form) {
    form.querySelectorAll('[data-filter-col]').forEach(function (input) {
      input.addEventListener('input', applyFilters);
      input.addEventListener('change', applyFilters);
    });
  }
  if (searchInput) {
    searchInput.addEventListener('input', applyFilters);
  }
  applyFilters();
}

// ---------------------------------------------------------------------
// Dashboard hero entrance — the app's one orchestrated motion moment.
// Staggers the stat cards in on load and counts each number up from
// zero. Runs only where .stat-card elements exist (the dashboard and
// admin panel), and is skipped entirely if the visitor prefers reduced
// motion — the cards and numbers still render correctly either way,
// just without the animation.
// ---------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  const cards = document.querySelectorAll('.stat-card');
  if (!cards.length) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  cards.forEach(function (card, i) {
    const valueEl = card.querySelector('.stat-value');
    const target = valueEl ? parseInt(valueEl.textContent, 10) : NaN;

    if (prefersReducedMotion) {
      return; // Leave the static, already-correct numbers exactly as rendered.
    }

    card.style.setProperty('--i', i);
    card.classList.add('stagger-fade-in');

    if (!isNaN(target) && valueEl) {
      const duration = 650;
      const start = performance.now() + i * 55; // line up with the card's own fade-in delay
      valueEl.textContent = '0';

      function tick(now) {
        const elapsed = now - start;
        if (elapsed < 0) { requestAnimationFrame(tick); return; }
        const progress = Math.min(elapsed / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3); // ease-out-cubic
        valueEl.textContent = Math.round(eased * target);
        if (progress < 1) requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
    }
  });
});
