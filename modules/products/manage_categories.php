<?php
$page_title = "Product Categories";
include('../../includes/header.php');
include('../../includes/db.php');

$conn = connect_db();

// Fetch all existing categories to display in the list
$sql = "SELECT * FROM product_categories ORDER BY category_name ASC";
$result = $conn->query($sql);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Product Categories</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<?php
if (isset($_GET['status'])) {
    $message = '';
    $alert_type = 'info';
    $status_map = [
        'success'      => ['msg' => 'Category added successfully!', 'type' => 'success'],
        'updated'      => ['msg' => 'Category updated successfully!', 'type' => 'success'],
        'deleted'      => ['msg' => 'Category has been deleted.', 'type' => 'warning'],
        'delete_error' => ['msg' => 'Error: Cannot delete category because it is currently in use by one or more products.', 'type' => 'danger'],
        'error'        => ['msg' => 'An error occurred. Please try again.', 'type' => 'danger'],
    ];

    if (array_key_exists($_GET['status'], $status_map)) {
        $message = $status_map[$_GET['status']]['msg'];
        $alert_type = $status_map[$_GET['status']]['type'];
    }

    if ($message) {
        echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">
            '. htmlspecialchars($message) .'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
}
?>

<div class="row">
    <?php if (has_permission(['Manager', 'Procurement Officer'])): ?>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Add New Category</h5>
                </div>
                <div class="card-body">
                    <form action="handle_add_category.php" method="POST">
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="<?php echo has_permission(['Manager', 'Procurement Officer']) ? 'col-md-8' : 'col-md-12'; ?>">
        <div class="card">
            <div class="card-header">
                <h5>Existing Categories</h5>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th>Date Added</th>
                            <?php if (has_permission('Manager')): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                    <td><?php echo date("d M, Y", strtotime($row['created_at'])); ?></td>
                                    <?php // The entire Actions column is now hidden from users without permission ?>
                                    <?php if (has_permission('Manager')): ?>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-warning" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editCategoryModal"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['category_name']); ?>">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                            
                                            <?php if (has_permission('Admin')): // Only Admins see the delete button ?>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteCategoryModal"
                                                    data-id="<?php echo $row['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($row['category_name']); ?>">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No categories found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if (has_permission('Manager')): ?>
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="editForm" action="handle_edit_category.php" method="POST">
              <div class="modal-body">
                  <input type="hidden" name="category_id" id="edit_category_id">
                  <div class="mb-3">
                      <label for="edit_category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                  </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>
          </form>
        </div>
      </div>
    </div>

    <div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="deleteCategoryModalLabel">Confirm Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete this category?
            <p class="text-danger small">This action is blocked if the category is in use.</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <form action="handle_delete_category.php" method="POST">
                <input type="hidden" name="id" id="delete_category_id">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
          </div>
        </div>
      </div>
    </div>
<?php endif; ?>

<?php
$conn->close();
include('../../includes/footer.php');
?>