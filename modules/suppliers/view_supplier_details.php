<?php
// Set the page title
$page_title = "Supplier Details";
include('../../includes/header.php');
include('../../includes/db.php');

// Helper function to generate star ratings
function generate_stars($rating) {
    $stars_html = '';
    if ($rating === null) {
        return '<span class="text-muted">Not Rated</span>';
    }
    $rating = floatval($rating);
    $full_stars = floor($rating);
    $half_star = $rating - $full_stars >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);

    for ($i = 0; $i < $full_stars; $i++) {
        $stars_html .= '<i class="bi bi-star-fill text-warning"></i>';
    }
    if ($half_star) {
        $stars_html .= '<i class="bi bi-star-half text-warning"></i>';
    }
    for ($i = 0; $i < $empty_stars; $i++) {
        $stars_html .= '<i class="bi bi-star text-warning"></i>';
    }
    return $stars_html . ' <span class="text-muted">(' . number_format($rating, 1) . ')</span>';
}


// 1. Check for a valid Supplier ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invalid supplier ID.</div>";
    include('../../includes/footer.php');
    exit();
}

$supplier_id = $_GET['id'];
$conn = connect_db();

// 2. Fetch the main supplier details
$sql_supplier = "SELECT * FROM suppliers WHERE id = ?";
$stmt_supplier = $conn->prepare($sql_supplier);
$stmt_supplier->bind_param("i", $supplier_id);
$stmt_supplier->execute();
$result_supplier = $stmt_supplier->get_result();
if ($result_supplier->num_rows === 0) {
    echo "<div class='alert alert-danger'>Supplier not found.</div>";
    include('../../includes/footer.php');
    $conn->close();
    exit();
}
$supplier = $result_supplier->fetch_assoc();

// 3. Fetch contacts
$sql_contacts = "SELECT * FROM supplier_contacts WHERE supplier_id = ? ORDER BY contact_name";
$stmt_contacts = $conn->prepare($sql_contacts);
$stmt_contacts->bind_param("i", $supplier_id);
$stmt_contacts->execute();
$result_contacts = $stmt_contacts->get_result();

// 4. Fetch contracts
$sql_contracts_list = "SELECT * FROM supplier_contracts WHERE supplier_id = ? ORDER BY end_date DESC";
$stmt_contracts_list = $conn->prepare($sql_contracts_list);
$stmt_contracts_list->bind_param("i", $supplier_id);
$stmt_contracts_list->execute();
$result_contracts_list = $stmt_contracts_list->get_result();

// 5. Fetch Communication Logs
$sql_logs = "SELECT * FROM supplier_communication_logs WHERE supplier_id = ? ORDER BY log_date DESC";
$stmt_logs = $conn->prepare($sql_logs);
$stmt_logs->bind_param("i", $supplier_id);
$stmt_logs->execute();
$result_logs = $stmt_logs->get_result();

// 6. Fetch Compliance Checklist Data
$sql_compliance = "SELECT 
                        c.id as checklist_id, 
                        c.item_name, 
                        c.item_description,
                        scs.status
                   FROM compliance_checklists c
                   LEFT JOIN supplier_compliance_status scs ON c.id = scs.checklist_id AND scs.supplier_id = ?
                   ORDER BY c.id";
$stmt_compliance = $conn->prepare($sql_compliance);
$stmt_compliance->bind_param("i", $supplier_id);
$stmt_compliance->execute();
$result_compliance = $stmt_compliance->get_result();


// This block handles status messages
$status_message = '';
if (isset($_GET['status'])) {
    $message_map = [
        'updated' => ['type' => 'success', 'text' => 'Supplier details updated successfully!'],
        'contract_success' => ['type' => 'success', 'text' => 'Contract uploaded successfully!'],
        'contract_error' => ['type' => 'danger', 'text' => 'Error uploading contract.'],
        'log_success' => ['type' => 'success', 'text' => 'Communication log added successfully!'],
        'log_error' => ['type' => 'danger', 'text' => 'Error adding communication log.']
    ];
    if (array_key_exists($_GET['status'], $message_map)) {
        $status = $message_map[$_GET['status']];
        $status_message = '<div class="alert alert-'. $status['type'] .' alert-dismissible fade show" role="alert">'. $status['text'] .'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_suppliers.php">Suppliers</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($supplier['supplier_name']); ?></li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($supplier['supplier_name']); ?></h1>
    <div>
        <a href="edit_supplier.php?id=<?php echo $supplier['id']; ?>" class="btn btn-warning"><i class="bi bi-pencil-square me-2"></i>Edit</a>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-id="<?php echo $supplier['id']; ?>" data-name="<?php echo htmlspecialchars($supplier['supplier_name']); ?>"><i class="bi bi-trash me-2"></i>Delete</button>
    </div>
</div>

<?php echo $status_message; ?>

<ul class="nav nav-tabs" id="supplierTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-pane" type="button" role="tab" aria-controls="details-pane" aria-selected="true">Details</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="contracts-tab" data-bs-toggle="tab" data-bs-target="#contracts-pane" type="button" role="tab" aria-controls="contracts-pane" aria-selected="false">Contracts</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="comms-tab" data-bs-toggle="tab" data-bs-target="#comms-pane" type="button" role="tab" aria-controls="comms-pane" aria-selected="false">Communication Log</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="compliance-tab" data-bs-toggle="tab" data-bs-target="#compliance-pane" type="button" role="tab" aria-controls="compliance-pane" aria-selected="false">Compliance</button>
  </li>
</ul>

<div class="tab-content" id="supplierTabContent">
  <div class="tab-pane fade show active" id="details-pane" role="tabpanel" aria-labelledby="details-tab">
    <div class="row mt-4">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header"><h5>Supplier Information</h5></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($supplier['address'])); ?></li>
                        <li class="list-group-item"><strong>Tax ID / VAT No.:</strong> <?php echo htmlspecialchars($supplier['tax_id']); ?></li>
                        <li class="list-group-item"><strong>Member Since:</strong> <?php echo date("F j, Y", strtotime($supplier['created_at'])); ?></li>
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h5>Contact Persons</h5></div>
                <div class="card-body">
                    <?php if ($result_contacts->num_rows > 0): ?>
                        <ul class="list-group list-group-flush">
                        <?php while($contact = $result_contacts->fetch_assoc()): ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($contact['contact_name']); ?></strong><br>
                                <i class="bi bi-envelope-fill text-muted"></i> <?php echo htmlspecialchars($contact['email']); ?><br>
                                <i class="bi bi-telephone-fill text-muted"></i> <?php echo htmlspecialchars($contact['phone_number']); ?>
                            </li>
                        <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">No contact persons listed for this supplier.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h5>Performance Scorecard</h5></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Delivery Time
                            <span><?php echo generate_stars($supplier['rating_delivery_time']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Quality of Goods
                            <span><?php echo generate_stars($supplier['rating_quality']); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Communication
                            <span><?php echo generate_stars($supplier['rating_communication']); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="tab-pane fade" id="contracts-pane" role="tabpanel" aria-labelledby="contracts-tab">
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>Existing Contracts</h5></div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_contracts_list->num_rows > 0): ?>
                                <?php while($contract = $result_contracts_list->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($contract['contract_title']); ?></td>
                                    <td><?php echo date("d M, Y", strtotime($contract['start_date'])); ?></td>
                                    <td><?php echo date("d M, Y", strtotime($contract['end_date'])); ?></td>
                                    <td><a href="/erp_project/<?php echo htmlspecialchars($contract['file_path']); ?>" class="btn btn-sm btn-info" target="_blank"><i class="bi bi-eye"></i> View</a></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted">No contracts found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5>Upload New Contract</h5></div>
                <div class="card-body">
                    <form action="handle_upload_contract.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="supplier_id" value="<?php echo $supplier_id; ?>">
                        <div class="mb-3">
                            <label for="contract_title" class="form-label">Contract Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contract_title" name="contract_title" required>
                        </div>
                        <div class="mb-3">
                            <label for="contract_file" class="form-label">Contract File <span class="text-danger">*</span></label>
                            <input class="form-control" type="file" id="contract_file" name="contract_file" required>
                            <div class="form-text">Allowed types: PDF, DOC, DOCX. Max size: 5MB.</div>
                        </div>
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="tab-pane fade" id="comms-pane" role="tabpanel" aria-labelledby="comms-tab">
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h5>Communication History</h5></div>
                <div class="card-body">
                    <?php if ($result_logs->num_rows > 0): ?>
                        <?php while($log = $result_logs->fetch_assoc()): ?>
                            <div class="p-2 border-bottom">
                                <strong><?php echo htmlspecialchars($log['log_type']); ?></strong> on 
                                <?php echo date("d M, Y, g:i A", strtotime($log['log_date'])); ?><br>
                                <p class="mb-0 mt-1 fst-italic">"<?php echo nl2br(htmlspecialchars($log['notes'])); ?>"</p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">No communication logs found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><h5>Add New Log</h5></div>
                <div class="card-body">
                    <form action="handle_add_comms_log.php" method="POST">
                        <input type="hidden" name="supplier_id" value="<?php echo $supplier_id; ?>">
                        <div class="mb-3">
                            <label for="log_type" class="form-label">Log Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="log_type" name="log_type" required>
                                <option value="Email">Email</option>
                                <option value="Call">Call</option>
                                <option value="Meeting">Meeting</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="log_date" class="form-label">Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="log_date" name="log_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Log</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
  </div>

  <div class="tab-pane fade" id="compliance-pane" role="tabpanel" aria-labelledby="compliance-tab">
      <div class="card mt-4">
          <div class="card-header"><h5>Supplier Compliance Checklist</h5></div>
          <div class="card-body">
              <table class="table table-hover">
                  <thead>
                      <tr>
                          <th>Checklist Item</th>
                          <th>Description</th>
                          <th style="width: 20%;">Status</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php while($item = $result_compliance->fetch_assoc()): ?>
                          <tr>
                              <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                              <td><?php echo htmlspecialchars($item['item_description']); ?></td>
                              <td>
                                  <div class="d-flex align-items-center">
                                      <select class="form-select compliance-status-select" 
                                              data-supplier-id="<?php echo $supplier_id; ?>"
                                              data-checklist-id="<?php echo $item['checklist_id']; ?>">
                                          <option value="Not Set" <?php if($item['status'] == 'Not Set' || $item['status'] == NULL) echo 'selected'; ?>>Not Set</option>
                                          <option value="Compliant" <?php if($item['status'] == 'Compliant') echo 'selected'; ?>>Compliant</option>
                                          <option value="Not Compliant" <?php if($item['status'] == 'Not Compliant') echo 'selected'; ?>>Not Compliant</option>
                                          <option value="In Progress" <?php if($item['status'] == 'In Progress') echo 'selected'; ?>>In Progress</option>
                                      </select>
                                      <span class="ms-2 save-status-indicator" style="display: none;">
                                          <i class="bi bi-check-circle-fill text-success"></i>
                                      </span>
                                  </div>
                              </td>
                          </tr>
                      <?php endwhile; ?>
                  </tbody>
              </table>
          </div>
      </div>
  </div>
</div>

<?php
// Close all database resources
$stmt_supplier->close();
$stmt_contacts->close();
$stmt_contracts_list->close();
$stmt_logs->close();
$stmt_compliance->close();
$conn->close();
include('../../includes/footer.php');
?>