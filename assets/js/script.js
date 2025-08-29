// This is the best practice for running scripts after the page is ready.
// We will put all our page initializations inside this one listener.
document.addEventListener('DOMContentLoaded', function() {

    // --- MODERN LOADING SCREEN MANAGEMENT ---
    function initModernLoadingScreen() {
        const loadingScreen = document.getElementById('modernLoadingScreen');
        if (loadingScreen) {
            // Hide loading screen after page is fully loaded
            window.addEventListener('load', () => {
                setTimeout(() => {
                    loadingScreen.classList.add('hidden');
                    setTimeout(() => {
                        loadingScreen.remove();
                    }, 500);
                }, 1200); // Show for at least 1.2 seconds for effect
            });
        }
    }

    // Initialize loading screen management if on dashboard
    if (window.location.pathname.includes('dashboard.php') || window.location.pathname.endsWith('/')) {
        initModernLoadingScreen();
    }

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

    // --- 7. Intersection-based reveal for cards/rows (ALL PAGES) ---
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

    // --- 8. Button ripple effect (ALL PAGES) ---
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
                if (el.hasAttribute('data-currency')) return '৳' + value.toLocaleString();
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

    // --- MODERN ENHANCEMENTS FOR ALL PAGES ---
    
    // Initialize page backgrounds for non-dashboard pages
    function initPageBackgrounds() {
        const pageBackground = document.querySelector('.page-background');
        const pageOverlay = document.querySelector('.modern-page-overlay');
        
        if (pageBackground || pageOverlay) {
            // Make sure they're visible
            if (pageBackground) {
                pageBackground.style.opacity = '1';
                pageBackground.style.visibility = 'visible';
            }
            if (pageOverlay) {
                pageOverlay.style.opacity = '1';
                pageOverlay.style.visibility = 'visible';
            }
        }
    }
    
    // Apply subtle reveal animations to all cards on any page
    function initUniversalAnimations() {
        const revealElements = document.querySelectorAll('.card:not(.modern-dashboard-card)');
        revealElements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            
            setTimeout(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 100 + 200);
        });

        // Also animate other page elements
        const otherElements = document.querySelectorAll('h1, .alert, .breadcrumb');
        otherElements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(15px)';
            el.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 50 + 100);
        });
    }

    // Enhanced form interactions for all pages
    function initModernFormInteractions() {
        const formControls = document.querySelectorAll('.form-control, .form-select');
        formControls.forEach(input => {
            // Add focus glow effect
            input.addEventListener('focus', (e) => {
                e.target.style.transform = 'scale(1.02)';
                e.target.style.boxShadow = '0 0 0 0.25rem rgba(13, 110, 253, 0.15)';
            });
            
            input.addEventListener('blur', (e) => {
                e.target.style.transform = 'scale(1)';
                e.target.style.boxShadow = 'none';
            });

            // Add typing animation
            input.addEventListener('input', (e) => {
                e.target.style.borderColor = 'var(--erp-brand-primary)';
                setTimeout(() => {
                    e.target.style.borderColor = '';
                }, 300);
            });
        });

        // Enhanced button interactions
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(btn => {
            btn.addEventListener('mouseenter', (e) => {
                e.target.style.transform = 'translateY(-2px)';
            });
            
            btn.addEventListener('mouseleave', (e) => {
                e.target.style.transform = 'translateY(0)';
            });
        });
    }

    // Modern hover effects for tables
    function initModernTableEffects() {
        const tables = document.querySelectorAll('.table');
        tables.forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', (e) => {
                    e.target.style.transform = 'translateX(3px)';
                    e.target.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
                    e.target.style.background = 'rgba(13, 110, 253, 0.08)';
                });
                
                row.addEventListener('mouseleave', (e) => {
                    e.target.style.transform = 'translateX(0)';
                    e.target.style.boxShadow = 'none';
                    e.target.style.background = '';
                });
            });
        });
    }

    // Add smooth transitions to DataTables
    function initDataTableEnhancements() {
        // Wait for DataTables to initialize
        setTimeout(() => {
            const datatableElements = document.querySelectorAll('.dataTables_wrapper input, .dataTables_wrapper select');
            datatableElements.forEach(el => {
                el.style.transition = 'all 0.3s ease';
                el.addEventListener('focus', (e) => {
                    e.target.style.transform = 'scale(1.02)';
                });
                el.addEventListener('blur', (e) => {
                    e.target.style.transform = 'scale(1)';
                });
            });

            // Enhance pagination
            const paginationLinks = document.querySelectorAll('.dataTables_paginate .paginate_button');
            paginationLinks.forEach(link => {
                link.style.transition = 'all 0.3s ease';
                link.addEventListener('mouseenter', (e) => {
                    e.target.style.transform = 'translateY(-1px)';
                });
                link.addEventListener('mouseleave', (e) => {
                    e.target.style.transform = 'translateY(0)';
                });
            });
        }, 500);
    }

    // Modern card hover enhancements
    function initModernCardEffects() {
        const cards = document.querySelectorAll('.card:not(.modern-dashboard-card)');
        cards.forEach(card => {
            card.addEventListener('mouseenter', (e) => {
                e.target.style.transform = 'translateY(-4px)';
                e.target.style.boxShadow = '0 12px 40px rgba(31, 38, 135, 0.25)';
            });
            
            card.addEventListener('mouseleave', (e) => {
                e.target.style.transform = 'translateY(0)';
                e.target.style.boxShadow = '';
            });
        });
    }

    // Enhanced alert animations
    function initAlertAnimations() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach((alert, index) => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(-20px)';
            alert.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                alert.style.opacity = '1';
                alert.style.transform = 'translateX(0)';
            }, index * 100 + 300);
        });
    }

    // Apply modern styling to dynamically loaded content
    function observeNewContent() {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                mutation.addedNodes.forEach((node) => {
                    if (node.nodeType === 1) { // Element node
                        // Re-apply modern effects to new content
                        if (node.classList.contains('card')) {
                            initModernCardEffects();
                        }
                        if (node.querySelector('.form-control, .form-select')) {
                            initModernFormInteractions();
                        }
                        if (node.querySelector('.table')) {
                            initModernTableEffects();
                        }
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Initialize all modern features with proper timing
    function initAllModernFeatures() {
        // Initialize page backgrounds first
        initPageBackgrounds();
        
        // Then animations with staggered timing
        setTimeout(() => {
            initUniversalAnimations();
        }, 100);
        
        setTimeout(() => {
            initModernFormInteractions();
            initModernTableEffects();
            initModernCardEffects();
            initAlertAnimations();
        }, 300);
        
        setTimeout(() => {
            initDataTableEnhancements();
        }, 800);
        
        // Start observing for new content
        observeNewContent();
    }

    // Initialize modern features based on page type
    if (document.body.classList.contains('dashboard-page')) {
        // Dashboard-specific initialization is handled by the dashboard script
        setTimeout(initAllModernFeatures, 200);
    } else {
        // For all other pages, apply modern enhancements immediately
        initAllModernFeatures();
    }
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

// --- MODERN DASHBOARD ENHANCEMENTS ---

// Particles.js Configuration and Initialization
function initParticles() {
    // Create particles container if it doesn't exist
    if (!document.getElementById('particles-js')) {
        const particlesContainer = document.createElement('div');
        particlesContainer.id = 'particles-js';
        document.body.insertBefore(particlesContainer, document.body.firstChild);
    }

    // Simple custom particle system (lightweight alternative to particles.js library)
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.style.position = 'fixed';
    canvas.style.top = '0';
    canvas.style.left = '0';
    canvas.style.width = '100%';
    canvas.style.height = '100%';
    canvas.style.zIndex = '-1';
    canvas.style.pointerEvents = 'none';
    
    document.getElementById('particles-js').appendChild(canvas);

    function resizeCanvas() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    
    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    const particles = [];
    const particleCount = 80;

    class Particle {
        constructor() {
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.vx = (Math.random() - 0.5) * 0.5;
            this.vy = (Math.random() - 0.5) * 0.5;
            this.radius = Math.random() * 2 + 1;
            this.opacity = Math.random() * 0.5 + 0.2;
        }

        update() {
            this.x += this.vx;
            this.y += this.vy;

            if (this.x < 0 || this.x > canvas.width) this.vx *= -1;
            if (this.y < 0 || this.y > canvas.height) this.vy *= -1;
        }

        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
            ctx.fill();
        }
    }

    // Initialize particles
    for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }

    function animate() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        particles.forEach(particle => {
            particle.update();
            particle.draw();
        });

        // Draw connections between nearby particles
        particles.forEach((particle, i) => {
            particles.slice(i + 1).forEach(otherParticle => {
                const dx = particle.x - otherParticle.x;
                const dy = particle.y - otherParticle.y;
                const distance = Math.sqrt(dx * dx + dy * dy);

                if (distance < 100) {
                    ctx.beginPath();
                    ctx.moveTo(particle.x, particle.y);
                    ctx.lineTo(otherParticle.x, otherParticle.y);
                    ctx.strokeStyle = `rgba(255, 255, 255, ${0.1 * (1 - distance / 100)})`;
                    ctx.lineWidth = 1;
                    ctx.stroke();
                }
            });
        });

        requestAnimationFrame(animate);
    }

    animate();
}

// Scroll Reveal Animation
function initScrollReveal() {
    const revealElements = document.querySelectorAll('.reveal');
    
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(element => {
        revealObserver.observe(element);
    });
}

// Modern Counter Animation Enhancement
function initModernCounters() {
    const counters = document.querySelectorAll('.modern-counter[data-count-up]');
    
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                counterObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => {
        counterObserver.observe(counter);
    });
}

function animateCounter(element) {
    const target = parseFloat(element.getAttribute('data-count-up'));
    const duration = 2000;
    const startTime = performance.now();
    const isCurrency = element.hasAttribute('data-currency');

    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function for smooth animation
        const easeOutExpo = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
        const current = target * easeOutExpo;
        
        if (isCurrency) {
            element.textContent = '৳' + Math.floor(current).toLocaleString();
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }

        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        }
    }

    requestAnimationFrame(updateCounter);
}

// Floating Action Button Functionality
function initFloatingActionButton() {
    // Check if FAB already exists
    if (document.querySelector('.floating-action-btn')) return;

    const fab = document.createElement('button');
    fab.className = 'floating-action-btn';
    fab.innerHTML = '<i class="bi bi-plus"></i>';
    fab.setAttribute('title', 'Quick Actions');
    
    // Create dropdown menu
    const fabMenu = document.createElement('div');
    fabMenu.className = 'fab-menu';
    fabMenu.style.cssText = `
        position: fixed;
        bottom: 150px;
        right: 30px;
        display: none;
        flex-direction: column;
        gap: 10px;
        z-index: 999;
    `;

    const quickActions = [
        { icon: 'receipt-cutoff', text: 'New PO', href: '/erp_project/modules/purchase_orders/create_po.php' },
        { icon: 'person-plus-fill', text: 'Add Employee', href: '/erp_project/modules/hr/add_employee.php' },
        { icon: 'journal-plus', text: 'Log Invoice', href: '/erp_project/modules/finance/log_invoice.php' },
        { icon: 'folder-plus', text: 'New Project', href: '/erp_project/modules/projects/add_project.php' }
    ];

    quickActions.forEach(action => {
        const actionBtn = document.createElement('a');
        actionBtn.href = action.href;
        actionBtn.className = 'btn btn-light btn-sm rounded-circle fab-action';
        actionBtn.style.cssText = `
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transform: scale(0);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        `;
        actionBtn.innerHTML = `<i class="bi bi-${action.icon}"></i>`;
        actionBtn.setAttribute('title', action.text);
        fabMenu.appendChild(actionBtn);
    });

    let fabOpen = false;
    fab.addEventListener('click', () => {
        fabOpen = !fabOpen;
        fabMenu.style.display = fabOpen ? 'flex' : 'none';
        fab.innerHTML = fabOpen ? '<i class="bi bi-x"></i>' : '<i class="bi bi-plus"></i>';
        
        // Animate menu items
        const actions = fabMenu.querySelectorAll('.fab-action');
        actions.forEach((action, index) => {
            setTimeout(() => {
                action.style.transform = fabOpen ? 'scale(1)' : 'scale(0)';
            }, index * 50);
        });
    });

    document.body.appendChild(fab);
    document.body.appendChild(fabMenu);
}

// Modern Card Hover Effects
function initModernCardEffects() {
    const cards = document.querySelectorAll('.modern-dashboard-card');
    
    cards.forEach(card => {
        card.addEventListener('mouseenter', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            card.style.setProperty('--mouse-x', x + 'px');
            card.style.setProperty('--mouse-y', y + 'px');
        });
    });
}

// Progress Ring Animation
function initProgressRings() {
    const rings = document.querySelectorAll('.progress-ring-path');
    
    rings.forEach(ring => {
        const progress = ring.getAttribute('data-progress') || 0;
        const circumference = 2 * Math.PI * 45; // 45 is the radius
        const offset = circumference - (progress / 100) * circumference;
        
        ring.style.strokeDasharray = circumference;
        ring.style.strokeDashoffset = circumference;
        
        // Animate on scroll into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        ring.style.strokeDashoffset = offset;
                    }, 500);
                    observer.unobserve(entry.target);
                }
            });
        });
        
        observer.observe(ring);
    });
}

// Smooth Page Transitions
function initPageTransitions() {
    // Add page transition overlay
    const overlay = document.createElement('div');
    overlay.className = 'page-transition-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        z-index: 9999;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    `;
    overlay.innerHTML = '<div class="modern-loading"></div>';
    document.body.appendChild(overlay);

    // Intercept navigation clicks
    document.addEventListener('click', (e) => {
        const link = e.target.closest('a[href]');
        if (!link || link.target === '_blank') return;
        
        const href = link.getAttribute('href');
        if (href.startsWith('#') || href.startsWith('javascript:') || href.includes('mailto:')) return;
        
        e.preventDefault();
        
        overlay.style.opacity = '1';
        overlay.style.visibility = 'visible';
        
        setTimeout(() => {
            window.location.href = href;
        }, 300);
    });
}

// Staggered Animation for Grid Items
function initStaggeredAnimations() {
    const gridItems = document.querySelectorAll('.col-lg-3, .col-md-6, .modern-action-item');
    
    gridItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        item.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Mouse Parallax Effect
function initMouseParallax() {
    let mouseX = 0, mouseY = 0;
    let currentX = 0, currentY = 0;

    document.addEventListener('mousemove', (e) => {
        mouseX = (e.clientX / window.innerWidth) * 2 - 1;
        mouseY = (e.clientY / window.innerHeight) * 2 - 1;
    });

    function updateParallax() {
        currentX += (mouseX - currentX) * 0.1;
        currentY += (mouseY - currentY) * 0.1;

        const parallaxElements = document.querySelectorAll('.floating-element');
        parallaxElements.forEach((element, index) => {
            const speed = (index + 1) * 0.02;
            const x = currentX * speed * 50;
            const y = currentY * speed * 50;
            element.style.transform = `translate(${x}px, ${y}px)`;
        });

        requestAnimationFrame(updateParallax);
    }

    updateParallax();
}

// Enhanced Notification System
function initModernNotifications() {
    // Override the existing erpToast function with modern styling
    window.erpToast = function(message, type = 'info', duration = 4000) {
        const container = document.getElementById('toast-container') || createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `modern-toast modern-toast-${type}`;
        toast.style.cssText = `
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 15px 20px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        `;

        const icon = getToastIcon(type);
        toast.innerHTML = `
            <i class="bi bi-${icon}" style="font-size: 1.2rem; color: ${getToastColor(type)};"></i>
            <span style="color: #333; font-weight: 500;">${message}</span>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #666; font-size: 1.2rem; cursor: pointer; margin-left: auto;">×</button>
        `;

        container.appendChild(toast);

        // Trigger animation
        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(0)';
        });

        // Auto remove
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    };

    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1080;
            max-width: 350px;
        `;
        document.body.appendChild(container);
        return container;
    }

    function getToastIcon(type) {
        const icons = {
            success: 'check-circle-fill',
            error: 'exclamation-triangle-fill',
            warning: 'exclamation-circle-fill',
            info: 'info-circle-fill'
        };
        return icons[type] || icons.info;
    }

    function getToastColor(type) {
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        return colors[type] || colors.info;
    }
}

// Make functions globally available for dashboard initialization
window.initParticles = initParticles;
window.initScrollReveal = initScrollReveal;
window.initModernCounters = initModernCounters;
window.initFloatingActionButton = initFloatingActionButton;
window.initModernCardEffects = initModernCardEffects;
window.initProgressRings = initProgressRings;
window.initPageTransitions = initPageTransitions;
window.initStaggeredAnimations = initStaggeredAnimations;
window.initMouseParallax = initMouseParallax;
window.initModernNotifications = initModernNotifications;

// Auto-initialize modern enhancements for dashboard pages
if (window.location.pathname.includes('dashboard.php') || window.location.pathname.endsWith('/') || window.location.pathname.includes('modern-dashboard-demo.html')) {
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize particles system
        initParticles();
        
        // Initialize scroll reveal
        initScrollReveal();
        
        // Initialize modern counters
        initModernCounters();
        
        // Initialize floating action button
        initFloatingActionButton();
        
        // Initialize modern card effects
        initModernCardEffects();
        
        // Initialize progress rings
        initProgressRings();
        
        // Initialize page transitions
        initPageTransitions();
        
        // Initialize staggered animations with delay
        setTimeout(initStaggeredAnimations, 500);
        
        // Initialize mouse parallax
        initMouseParallax();
        
        // Initialize modern notifications
        initModernNotifications();
        
        // Add reveal class to elements that should animate on scroll
        document.querySelectorAll('.card, .modern-dashboard-card, .modern-chart-container').forEach(el => {
            el.classList.add('reveal');
        });
    });
}