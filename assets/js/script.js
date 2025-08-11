// This is the best practice for running scripts after the page is ready.
// We will put all our page initializations inside this one listener.
document.addEventListener('DOMContentLoaded', function() {

    // Apply persisted theme early
    const storedTheme = localStorage.getItem('erp|theme');
    if (storedTheme === 'dark' || storedTheme === 'light') {
        document.documentElement.setAttribute('data-bs-theme', storedTheme);
    }

    // --- 1. Sidebar Toggle Functionality ---
    const sidebarToggle = document.body.querySelector('#menu-toggle');
    if (sidebarToggle) {
        // restore persisted toggle state
        const persisted = localStorage.getItem('sb|sidebar-toggle');
        if (persisted === 'true') { document.body.classList.add('toggled'); }

        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('toggled'));
        });
    }
    
    // --- 2. Initialize DataTables ---
    // Note: We use jQuery here because DataTables requires it.
    if (typeof $ === 'function' && $.fn.DataTable) {
        $('.data-table:not(.dataTable)').DataTable();
    }

    // --- 3. Initialize all Bootstrap Tooltips on the page ---
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // --- 4. Active link highlighting in sidebar based on location ---
    try {
        const currentPath = window.location.pathname.replace(/\/?$/, '');
        const sidebarLinks = document.querySelectorAll('#sidebar-wrapper .list-group a');
        sidebarLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (!href) return;
            const normalized = href.replace(/\/?$/, '');
            if (normalized === currentPath) {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
                // expand parent collapse if inside submenu
                const collapseParent = link.closest('.collapse');
                if (collapseParent && typeof bootstrap !== 'undefined') {
                    const bsCollapse = new bootstrap.Collapse(collapseParent, { toggle: false });
                    bsCollapse.show();
                }
            }
        });
    } catch (e) { /* no-op */ }

    // --- 5. Theme toggle ---
    const themeToggleBtn = document.getElementById('theme-toggle');
    const setTheme = (theme) => {
        document.documentElement.setAttribute('data-bs-theme', theme);
        localStorage.setItem('erp|theme', theme);
        if (themeToggleBtn) {
            themeToggleBtn.innerHTML = theme === 'dark' ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon-stars"></i>';
            themeToggleBtn.classList.toggle('btn-warning', theme === 'dark');
            themeToggleBtn.classList.toggle('btn-outline-secondary', theme !== 'dark');
        }
    };
    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
            setTheme(current === 'dark' ? 'light' : 'dark');
        });
        // initialize icon/state
        const initial = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'dark' : 'light';
        setTheme(initial);
    }

    // --- 6. Page fade-in once ready ---
    const pageWrapper = document.getElementById('page-content-wrapper');
    if (pageWrapper) {
        requestAnimationFrame(() => { pageWrapper.classList.add('page-ready'); });
    }

    // --- TOP PROGRESS BAR ---
    let topBar = document.getElementById('top-progress');
    if (!topBar) {
        topBar = document.createElement('div');
        topBar.id = 'top-progress';
        document.body.appendChild(topBar);
    }
    const TopProgress = {
        start() { topBar.style.width = '15%'; requestAnimationFrame(() => topBar.style.width = '55%'); },
        end() { topBar.style.width = '100%'; setTimeout(() => { topBar.style.width = '0'; }, 300); }
    };

    // --- BACK TO TOP BUTTON ---
    let backTop = document.getElementById('back-to-top');
    if (!backTop) {
        backTop = document.createElement('button');
        backTop.type = 'button';
        backTop.id = 'back-to-top';
        backTop.className = 'btn';
        backTop.innerHTML = '<i class="bi bi-arrow-up"></i>';
        document.body.appendChild(backTop);
    }
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) backTop.classList.add('show'); else backTop.classList.remove('show');
    });
    backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

    // --- Sidebar link tooltips (collapsed state aid) ---
    try {
        document.querySelectorAll('#sidebar-wrapper .list-group a').forEach(a => {
            if (!a.getAttribute('title')) a.setAttribute('title', a.textContent.trim());
            new bootstrap.Tooltip(a, { placement: 'right' });
        });
    } catch (e) {}

    // --- Keyboard shortcuts ---
    // Alt+K: focus search (if present), Alt+M: toggle menu, Alt+T: theme toggle
    document.addEventListener('keydown', (e) => {
        if (!e.altKey) return;
        if (e.key.toLowerCase() === 'm') { e.preventDefault(); const btn = document.getElementById('menu-toggle'); if (btn) btn.click(); }
        if (e.key.toLowerCase() === 't') { e.preventDefault(); const btn = document.getElementById('theme-toggle'); if (btn) btn.click(); }
        if (e.key.toLowerCase() === 'k') {
            const search = document.querySelector('input[type="search"], .dataTables_filter input');
            if (search) { e.preventDefault(); search.focus(); search.select(); }
        }
        if (e.key.toLowerCase() === 'd') { e.preventDefault(); const btn = document.getElementById('density-toggle'); if (btn) btn.click(); }
    });

    // Density toggle
    const densityToggleBtn = document.getElementById('density-toggle');
    const setDensity = (mode) => {
        document.body.classList.toggle('density-compact', mode === 'compact');
        localStorage.setItem('erp|density', mode);
    };
    if (densityToggleBtn) {
        const storedDensity = localStorage.getItem('erp|density');
        setDensity(storedDensity === 'compact' ? 'compact' : 'comfortable');
        densityToggleBtn.addEventListener('click', () => {
            const current = document.body.classList.contains('density-compact') ? 'compact' : 'comfortable';
            setDensity(current === 'compact' ? 'comfortable' : 'compact');
        });
    }

    // --- Enhance fetchNotifications with mark-all UI ---
    const notifList = document.getElementById('notification-list');
    if (notifList) {
        const header = document.createElement('li');
        header.innerHTML = '<div class="d-flex align-items-center justify-content-between px-3 py-2"><strong>Notifications</strong><button class="btn btn-sm btn-link p-0" id="mark-all-read">Mark all</button></div>';
        notifList.prepend(header);
        notifList.addEventListener('click', (e) => {
            const markAll = e.target.closest('#mark-all-read');
            if (!markAll) return;
            e.preventDefault();
            TopProgress.start();
            fetch('/erp_project/includes/mark_notifications_read.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({}) })
                .finally(() => { TopProgress.end(); });
        });
    }

    // --- Intercept anchor navigations to show top progress (same-origin only) ---
    document.body.addEventListener('click', (e) => {
        const link = e.target.closest('a[href]');
        if (!link) return;
        const href = link.getAttribute('href');
        // Ignore hashes, JS links, or external
        if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
        const isExternal = /^https?:\/\//i.test(href) && !href.includes(window.location.host);
        if (isExternal) return;
        TopProgress.start();
    }, true);

    window.addEventListener('pageshow', () => TopProgress.end());

    // --- 7. Intersection-based reveal for cards/rows ---
    try {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });
        document.querySelectorAll('.card, .table, .list-group-item').forEach(el => observer.observe(el));
    } catch (e) {}

    // --- 8. Button ripple effect ---
    document.body.addEventListener('click', function(e){
        const button = e.target.closest('.btn');
        if(!button) return;
        const rect = button.getBoundingClientRect();
        const circle = document.createElement('span');
        circle.className = 'ripple-circle';
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        circle.style.width = circle.style.height = size + 'px';
        circle.style.left = x + 'px';
        circle.style.top = y + 'px';
        button.appendChild(circle);
        circle.addEventListener('animationend', () => circle.remove());
    });

    // --- 9. Count-up numbers for dashboard stats (optional) ---
    try {
        const counters = document.querySelectorAll('[data-count-up]');
        counters.forEach((el) => {
            const end = parseFloat(el.getAttribute('data-count-up'));
            if (isNaN(end)) return;
            const duration = 900; // ms
            const startTime = performance.now();
            const start = 0;
            const formatter = (value) => {
                if (el.hasAttribute('data-currency')) return '$' + value.toLocaleString();
                return value.toLocaleString();
            };
            const tick = (now) => {
                const progress = Math.min(1, (now - startTime) / duration);
                const value = Math.floor(start + (end - start) * progress);
                el.textContent = formatter(value);
                if (progress < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        });
    } catch (e) {}
});


// --- MODAL HANDLERS ---
// This single, robust function handles populating all our delete modals.
function setupModalListener(modalId, inputId, isDelete = true) {
    const modalElement = document.getElementById(modalId);
    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            const dataId = button.getAttribute('data-id');
            const dataName = button.getAttribute('data-name'); // For modals that might show a name
            
            const modalInput = modalElement.querySelector(inputId);
            if (modalInput) modalInput.value = dataId;

            if (isDelete && dataName) {
                 const nameElement = modalElement.querySelector('.modal-data-name');
                 if (nameElement) nameElement.textContent = dataName;
            } else if (!isDelete && dataName) {
                 const nameInput = modalElement.querySelector('.modal-data-name-input');
                 if (nameInput) nameInput.value = dataName;
            }
        });
    }
}

// Initialize all our modals
setupModalListener('deleteConfirmationModal', '#supplierIdToDelete');
setupModalListener('editCategoryModal', '#edit_category_id', false);
setupModalListener('deleteCategoryModal', '#delete_category_id');
setupModalListener('deleteProductModal', '#delete_product_id');
setupModalListener('deleteBudgetModal', '#delete_budget_id');
setupModalListener('deleteAssetModal', '#delete_asset_id');
setupModalListener('deleteUserModal', '#delete_user_id');
setupModalListener('deleteProjectModal', '#delete_project_id');
setupModalListener('deleteInvoiceModal', '#delete_invoice_id');


// --- AJAX EVENT LISTENERS ---
// This 'change' listener handles all dropdown changes.
document.addEventListener('change', function(event) {
    // Handle Compliance Status Update
    if (event.target.classList.contains('compliance-status-select')) {
        // ... (compliance status logic)
    }
    
    // Handle Project Task Status Update
    if (event.target.classList.contains('task-status-select')) {
        // ... (task status logic)
    }

    // UPDATED: Handle Delivery Status CHANGE
    // This part now ONLY shows the save button.
    if (event.target.classList.contains('delivery-status-select')) {
        const selectElement = event.target;
        const saveButton = selectElement.parentElement.querySelector('.save-delivery-status');
        if (saveButton) {
            saveButton.style.display = 'inline-block';
        }
    }
});

// This 'click' listener handles all button clicks.
document.addEventListener('click', function(event) {
    // NEW: Handle Delivery Status SAVE button click
    const saveButton = event.target.closest('.save-delivery-status');
    if (saveButton) {
        const wrapper = saveButton.parentElement;
        const selectElement = wrapper.querySelector('.delivery-status-select');
        const deliveryId = selectElement.dataset.deliveryId;
        const newStatus = selectElement.value;

        saveButton.disabled = true;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('/erp_project/modules/deliveries/handle_update_delivery_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ delivery_id: deliveryId, new_status: newStatus })
        })
        .then(response => {
            if (!response.ok) { throw new Error('Server responded with an error.'); }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                saveButton.classList.remove('btn-success');
                saveButton.classList.add('btn-outline-success');
                saveButton.innerHTML = '<i class="bi bi-check-lg"></i>';
            } else {
                saveButton.classList.remove('btn-success');
                saveButton.classList.add('btn-danger');
                saveButton.innerHTML = '<i class="bi bi-x-lg"></i>';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            saveButton.classList.remove('btn-success');
            saveButton.classList.add('btn-danger');
            saveButton.innerHTML = '<i class="bi bi-x-lg"></i>';
        })
        .finally(() => {
            setTimeout(() => {
                saveButton.style.display = 'none';
                saveButton.disabled = false;
                saveButton.classList.remove('btn-outline-success', 'btn-danger');
                saveButton.classList.add('btn-success');
                saveButton.innerHTML = '<i class="bi bi-check-lg"></i>';
            }, 1500);
        });
    }
});


// --- IN-APP NOTIFICATION SYSTEM ---
const notificationCountElement = document.getElementById('notification-count');
const notificationListElement = document.getElementById('notification-list');
const notificationDropdown = document.getElementById('notificationDropdown');

function fetchNotifications() {
    if(!notificationCountElement) return;
    fetch('/erp_project/includes/fetch_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.unread_count > 0) {
                notificationCountElement.textContent = data.unread_count;
                notificationCountElement.style.display = 'block';
            } else {
                notificationCountElement.style.display = 'none';
            }
            notificationListElement.innerHTML = '';
            if (data.notifications.length > 0) {
                data.notifications.forEach(notif => {
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `<a class="dropdown-item notification-link" href="${notif.link}" data-id="${notif.id}"><div class="small">${notif.message}</div><div class="small text-muted">${new Date(notif.created_at).toLocaleString()}</div></a>`;
                    notificationListElement.appendChild(listItem);
                });
            } else {
                notificationListElement.innerHTML = '<li><span class="dropdown-item text-muted text-center">No new notifications</span></li>';
            }
        }).catch(error => console.error('Error fetching notifications:', error));
}

if (notificationDropdown) {
    notificationDropdown.addEventListener('show.bs.dropdown', function() {
        setTimeout(() => {
            if (notificationListElement.children.length > 0 && notificationListElement.children[0].textContent !== 'No new notifications') {
                fetch('/erp_project/includes/mark_notifications_read.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({}) }).catch(error => console.error('Error marking all as read:', error));
            }
        }, 3000);
    });
}
if (notificationListElement) {
    notificationListElement.addEventListener('click', function(e) {
        const link = e.target.closest('a.notification-link');
        if (!link) return;
        
        e.preventDefault();
        const notificationId = link.dataset.id;
        const destinationUrl = link.href;

        if (notificationId) {
            fetch('/erp_project/includes/mark_notifications_read.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ notification_id: notificationId }) })
            .finally(() => { window.location.href = destinationUrl; });
        } else {
            window.location.href = destinationUrl;
        }
    });
}

if (notificationCountElement) {
    fetchNotifications();
    setInterval(fetchNotifications, 20000);
}

// Global UI utilities
(function(){
  // Toast API
  window.erpToast = function(message, type = 'info'){
    const container = document.getElementById('toast-container');
    if(!container) return;
    const toastEl = document.createElement('div');
    toastEl.className = 'toast align-items-center show glass border-0';
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.innerHTML = `<div class="d-flex"><div class="toast-body"><span class="badge me-2 bg-${type === 'success' ? 'success' : type === 'danger' ? 'danger' : type === 'warning' ? 'warning' : 'primary'}"></span>${message}</div><button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
    container.appendChild(toastEl);
    setTimeout(() => toastEl.remove(), 4000);
  };

  // Generic confirm via [data-confirm]
  document.addEventListener('click', function(e){
    const activator = e.target.closest('[data-confirm]');
    if(!activator) return;
    e.preventDefault();
    const modalEl = document.getElementById('genericConfirmModal');
    const bsModal = new bootstrap.Modal(modalEl);
    document.getElementById('genericConfirmTitle').textContent = activator.getAttribute('data-confirm-title') || 'Please Confirm';
    document.getElementById('genericConfirmBody').textContent = activator.getAttribute('data-confirm') || 'Are you sure?';
    const okBtn = document.getElementById('genericConfirmOk');
    const href = activator.getAttribute('href');
    const form = activator.closest('form');
    const callback = activator.getAttribute('data-confirm-callback');
    const submitSelector = activator.getAttribute('data-confirm-submit');
    const cleanup = () => okBtn.replaceWith(okBtn.cloneNode(true));
    okBtn.addEventListener('click', function handler(){
      bsModal.hide();
      // navigate, submit or callback
      if (callback && window[callback]) { window[callback](); }
      else if (submitSelector) { const toSubmit = document.querySelector(submitSelector); if (toSubmit) toSubmit.submit(); }
      else if (form && activator.type === 'submit') { form.submit(); }
      else if (href) { window.location.href = href; }
      cleanup();
    }, { once: true });
    bsModal.show();
  });

  // DataTables defaults
  if (typeof $ === 'function' && $.fn.DataTable) {
    $.extend(true, $.fn.dataTable.defaults, {
      language: { search: '', searchPlaceholder: 'Search…' },
      pageLength: 10,
      lengthMenu: [ [10, 25, 50, 100], [10, 25, 50, 100] ],
      responsive: true,
      dom: '<"row align-items-center"<"col-sm-6"l><"col-sm-6"f>>t<"row align-items-center"<"col-sm-6"i><"col-sm-6"p>>'
    });
  }

  // Simple HTML5 validation helper
  document.addEventListener('submit', function(e){
    const form = e.target.closest('form');
    if (!form) return;
    if (!form.checkValidity()) {
      e.preventDefault(); e.stopPropagation();
      form.classList.add('was-validated');
      erpToast('Please fill in the required fields', 'warning');
    }
  }, true);

  // Loading overlay controls
  window.erpLoading = {
    show(){ const el = document.getElementById('loading-overlay'); if(el) el.classList.add('show'); },
    hide(){ const el = document.getElementById('loading-overlay'); if(el) el.classList.remove('show'); }
  };
})();