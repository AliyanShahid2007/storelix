<?php
$page_title = 'Live Chat (Disabled)';
require_once '../includes/header.php';
requireAdmin();
?>

<div class="container my-5">
    <div class="card shadow-sm border-0">
        <div class="card-body text-center p-5">
            <h3 class="mb-3">Live Chat Disabled</h3>
            <p class="text-muted mb-4">The live chat feature has been removed from this site. Chat sessions and management have been disabled.</p>
            <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
