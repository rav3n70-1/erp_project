<?php
$page_title = "Edit Client";
include('../../includes/header.php');

if (!has_permission('client_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid Client ID."); }
$client_id = $_GET['id'];
$conn = connect_db();

// Fetch existing client data
$sql_client = "SELECT * FROM clients WHERE id = ?";
$stmt_client = $conn->prepare($sql_client);
$stmt_client->bind_param("i", $client_id);
$stmt_client->execute();
$result_client = $stmt_client->get_result();
if ($result_client->num_rows === 0) { die("Client not found."); }
$client = $result_client->fetch_assoc();
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_clients.php">Clients</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Client</li>
  </ol>
</nav>

<h1 class="mt-4">Edit Client: <?php echo htmlspecialchars($client['client_name']); ?></h1>

<div class="card">
    <div class="card-header"><h5>Client Details</h5></div>
    <div class="card-body">
        <form action="handle_edit_client.php" method="POST">
            <input type="hidden" name="client_id" value="<?php echo $client['id']; ?>">
            <fieldset class="mb-3">
                <legend>Company Information</legend>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="client_name" class="form-label">Client/Company Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="client_name" name="client_name" value="<?php echo htmlspecialchars($client['client_name']); ?>" required></div>
                    <div class="col-md-6 mb-3"><label for="contact_person" class="form-label">Contact Person</label><input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($client['contact_person']); ?>"></div>
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Email Address <span class="text-danger">*</span></label><input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($client['email']); ?>" required></div>
                    <div class="col-md-6 mb-3"><label for="phone_number" class="form-label">Phone Number</label><input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($client['phone_number']); ?>"></div>
                </div>
            </fieldset>
            <fieldset>
                <legend>Client Portal Access</legend>
                <p class="text-muted small">Update the username or set a new password for this client's portal access.</p>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="username" class="form-label">Username</label><input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($client['username']); ?>"></div>
                    <div class="col-md-6 mb-3"><label for="password" class="form-label">New Password</label><input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep unchanged"></div>
                </div>
            </fieldset>
            <hr>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="view_clients.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>