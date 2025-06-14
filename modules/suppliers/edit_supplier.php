<?php
$page_title = "Edit Supplier";
include('../../includes/header.php');
if (!has_permission('po_edit')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }
include('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid supplier ID."); }
$supplier_id = $_GET['id'];
$conn = connect_db();

$sql = "SELECT s.*, sc.id as contact_id, sc.contact_name, sc.email, sc.phone_number
        FROM suppliers s
        LEFT JOIN supplier_contacts sc ON s.id = sc.supplier_id
        WHERE s.id = ?
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) { die("Supplier not found."); }
$supplier = $result->fetch_assoc();
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_suppliers.php">Suppliers</a></li>
    <li class="breadcrumb-item"><a href="view_supplier_details.php?id=<?php echo $supplier_id; ?>"><?php echo htmlspecialchars($supplier['supplier_name']); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
  </ol>
</nav>

<h1 class="mt-4">Edit Supplier: <?php echo htmlspecialchars($supplier['supplier_name']); ?></h1>

<div class="card">
    <div class="card-body">
        <form action="handle_edit_supplier.php" method="POST">
            <input type="hidden" name="supplier_id" value="<?php echo $supplier['id']; ?>">
            <input type="hidden" name="contact_id" value="<?php echo $supplier['contact_id']; ?>">

            <fieldset class="mb-4">
                <legend>Supplier Information</legend>
                <div class="mb-3"><label for="supplier_name" class="form-label">Supplier Name</label><input type="text" class="form-control" id="supplier_name" name="supplier_name" value="<?php echo htmlspecialchars($supplier['supplier_name']); ?>" required></div>
                <div class="mb-3"><label for="address" class="form-label">Address</label><textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($supplier['address']); ?></textarea></div>
                <div class="mb-3"><label for="tax_id" class="form-label">Tax ID</label><input type="text" class="form-control" id="tax_id" name="tax_id" value="<?php echo htmlspecialchars($supplier['tax_id']); ?>"></div>
            </fieldset>

            <fieldset class="mb-4">
                <legend>Primary Contact Person</legend>
                <div class="mb-3"><label for="contact_name" class="form-label">Contact Name</label><input type="text" class="form-control" id="contact_name" name="contact_name" value="<?php echo htmlspecialchars($supplier['contact_name']); ?>"></div>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Email</label><input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($supplier['email']); ?>"></div>
                    <div class="col-md-6 mb-3"><label for="phone_number" class="form-label">Phone</label><input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($supplier['phone_number']); ?>"></div>
                </div>
            </fieldset>
            
            <?php if(has_permission('supplier_rate')): ?>
            <fieldset class="mb-4">
                <legend>Performance Ratings (1-5)</legend>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="rating_delivery_time" class="form-label">Delivery Time</label>
                        <input type="number" class="form-control" id="rating_delivery_time" name="rating_delivery_time" value="<?php echo htmlspecialchars($supplier['rating_delivery_time']); ?>" min="1" max="5" step="0.5">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rating_quality" class="form-label">Quality of Goods</label>
                        <input type="number" class="form-control" id="rating_quality" name="rating_quality" value="<?php echo htmlspecialchars($supplier['rating_quality']); ?>" min="1" max="5" step="0.5">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="rating_communication" class="form-label">Communication</label>
                        <input type="number" class="form-control" id="rating_communication" name="rating_communication" value="<?php echo htmlspecialchars($supplier['rating_communication']); ?>" min="1" max="5" step="0.5">
                    </div>
                </div>
            </fieldset>
            <?php endif; ?>

            <fieldset class="mb-4">
                <legend>Supplier Portal Access</legend>
                 <div class="row">
                    <div class="col-md-6 mb-3"><label for="username" class="form-label">Portal Username</label><input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($supplier['username']); ?>"></div>
                    <div class="col-md-6 mb-3"><label for="password" class="form-label">New Password</label><input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep unchanged"></div>
                </div>
            </fieldset>
            
            <hr>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="view_supplier_details.php?id=<?php echo $supplier['id']; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$stmt->close();
$conn->close();
include('../../includes/footer.php');
?>