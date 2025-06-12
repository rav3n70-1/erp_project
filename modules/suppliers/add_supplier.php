<?php
// Set the page title
$page_title = "Add New Supplier";
include('../../includes/header.php');

// This PHP block is only for displaying an error message if the redirect from the handler includes one.
$status_message = '';
if (isset($_GET['status']) && $_GET['status'] == 'error') {
    $status_message = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Error!</strong> There was a problem saving the supplier. Please check your input and try again.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_suppliers.php">Suppliers</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add New Supplier</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<?php echo $status_message; ?>

<div class="card">
    <div class="card-body">
        <form action="handle_add_supplier.php" method="POST">
            <fieldset class="mb-4">
                <legend>Supplier Information</legend>
                <div class="mb-3">
                    <label for="supplier_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="tax_id" class="form-label">Tax ID / VAT No.</label>
                    <input type="text" class="form-control" id="tax_id" name="tax_id">
                </div>
            </fieldset>

            <fieldset>
                <legend>Primary Contact Person</legend>
                <div class="mb-3">
                    <label for="contact_name" class="form-label">Contact Name</label>
                    <input type="text" class="form-control" id="contact_name" name="contact_name">
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number">
                    </div>
                </div>
            </fieldset>
            
            <hr>

            <button type="submit" class="btn btn-primary">Save Supplier</button>
            <a href="view_suppliers.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
include('../../includes/footer.php');
?>