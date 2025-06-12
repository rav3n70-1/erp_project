<?php
// This file contains helper functions related to user roles and permissions.

/**
 * Checks if the current logged-in user has a specific permission key.
 * The 'System Admin' role always returns true.
 *
 * @param string $permission_key The specific action to check (e.g., 'po_approve', 'supplier_edit').
 * @return bool True if the user has the permission, false otherwise.
 */
function has_permission($permission_key) {
    if (!isset($_SESSION['role_name']) || !isset($_SESSION['permissions'])) {
        return false;
    }

    if ($_SESSION['role_name'] == 'System Admin' || $_SESSION['role_name'] == 'Admin') {
        return true;
    }

    return in_array($permission_key, $_SESSION['permissions']);
}


/**
 * Logs a user action to the audit trail.
 *
 * @param mysqli $conn The database connection object.
 * @param string $action A description of the action performed.
 * @param string $target_type The type of entity being acted upon (e.g., 'PO', 'Supplier').
 * @param int $target_id The ID of the entity.
 */
function log_audit_trail($conn, $action, $target_type, $target_id) {
    if (!isset($_SESSION['user_id'])) {
        return; // Don't log if no user is in session
    }
    
    $user_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO audit_log (user_id, action, target_type, target_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $user_id, $action, $target_type, $target_id);
    $stmt->execute();
    $stmt->close();
}
?>