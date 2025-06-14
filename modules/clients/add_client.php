<?php
$page_title = "Add New Client";
include('../../includes/header.php');

if (!has_permission('client_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_clients.php">Clients</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Client</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<div class="card">
    <div class="card-header"><h5>Client Details</h5></div>
    <div class="card-body">
        <form action="handle_add_client.php" method="POST">
            <fieldset class="mb-3">
                <legend>Company Information</legend>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="client_name" class="form-label">Client/Company Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="client_name" name="client_name" required></div>
                    <div class="col-md-6 mb-3"><label for="contact_person" class="form-label">Contact Person</label><input type="text" class="form-control" id="contact_person" name="contact_person"></div>
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-3"><label for="email" class="form-label">Email Address <span class="text-danger">*</span></label><input type="email" class="form-control" id="email" name="email" required></div>
                    <div class="col-md-6 mb-3"><label for="phone_number" class="form-label">Phone Number</label><input type="text" class="form-control" id="phone_number" name="phone_number"></div>
                </div>
            </fieldset>
            <fieldset>
                <legend>Client Portal Access</legend>
                <p class="text-muted small">Create a username and password for this client to log into their portal.</p>
                <div class="row">
                    <div class="col-md-6 mb-3"><label for="username" class="form-label">Username</label><input type="text" class="form-control" id="username" name="username"></div>
                    <div class="col-md-6 mb-3"><label for="password" class="form-label">Password</label><input type="password" class="form-control" id="password" name="password"></div>
                </div>
            </fieldset>
            <hr>
            <button type="submit" class="btn btn-primary">Save Client</button>
            <a href="view_clients.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php include('../../includes/footer.php'); ?>