<?php
/**
 * This file contains helper functions related to user roles and permissions.
 */

/**
 * Checks if the current logged-in user has the required permission(s).
 * The 'System Admin' role always returns true.
 *
 * @param string|array $required_permissions A single permission key (string) or an array of keys.
 * @return bool True if the user has the required permission, false otherwise.
 */
function has_permission($required_permissions) {
    // 1. Ensure the user is logged in and their permissions are loaded into the session.
    if (!isset($_SESSION['role_name']) || !isset($_SESSION['permissions'])) {
        return false;
    }

    // 2. The 'System Admin' role is a superuser and bypasses all checks.
    if ($_SESSION['role_name'] == 'System Admin') {
        return true;
    }

    // 3. Get the permissions the user actually has from their session.
    $user_permissions = $_SESSION['permissions'];

    // 4. Handle the logic based on whether we're checking for a single permission or multiple.
    if (is_string($required_permissions)) {
        // If we are checking for just one permission key (e.g., 'po_approve').
        return in_array($required_permissions, $user_permissions);
    } 
    
    if (is_array($required_permissions)) {
        // If we are checking for any one of a list of permissions (e.g., ['hr_view', 'hr_manage']).
        // This checks for any intersection between the required array and the user's array.
        return !empty(array_intersect($required_permissions, $user_permissions));
    }

    // If the input is invalid, deny permission.
    return false;
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
    // Only proceed if a user is actually logged in.
    if (!isset($_SESSION['user_id'])) {
        return; 
    }
    
    $user_id = $_SESSION['user_id'];
    
    $sql = "INSERT INTO audit_log (user_id, action, target_type, target_id) VALUES (?, ?, ?, ?)";
    
    // Prepare the statement and check for errors to make it more robust.
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("issi", $user_id, $action, $target_type, $target_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // In a real application, you would log this database error.
        error_log("Failed to prepare statement for audit log: " . $conn->error);
    }
}
?>