// This is the best practice for running scripts after the page is ready.
// We will put all our page initializations inside this one listener.
document.addEventListener('DOMContentLoaded', function() {

    // --- 1. Sidebar Toggle Functionality ---
    const sidebarToggle = document.body.querySelector('#menu-toggle');
    if (sidebarToggle) {
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