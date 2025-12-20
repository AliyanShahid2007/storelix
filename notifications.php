<?php
$page_title = 'Notifications';
require_once 'includes/header.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$notifications = getNotifications($user_id);
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-bell"></i> My Notifications</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No notifications yet</h5>
                            <p class="text-muted">You'll receive notifications about your orders and account activity here.</p>
                        </div>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($notifications as $notification): ?>
                                <div class="list-group-item <?php echo $notification['is_read'] ? '' : 'list-group-item-warning'; ?>">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 <?php echo $notification['is_read'] ? '' : 'fw-bold'; ?>">
                                            <?php echo htmlspecialchars($notification['title']); ?>
                                        </h6>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y H:i', strtotime($notification['created_at'])); ?>
                                        </small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <?php if (!$notification['is_read']): ?>
                                        <button class="btn btn-sm btn-outline-primary" onclick="markAsRead(<?php echo $notification['id']; ?>)">
                                            <i class="fas fa-check"></i> Mark as Read
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Local safe JSON parser
function parseJsonSafeInline(response) {
    return response.text().then(function(text) {
        try { return JSON.parse(text); } catch (e) { console.error('parseJsonSafeInline:', response.url, text); throw new Error('Invalid JSON'); }
    });
}
    function markAsRead(notificationId) {
        fetch('ajax/mark_notification_read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'notification_id=' + notificationId
        })
        .then(parseJsonSafeInline)
        .then(data => {
            if (data.success) {
                location.reload(); // Reload to update the list
            }
        })
        .catch(error => console.error('Error:', error));
    }
</script>

<?php require_once 'includes/footer.php'; ?>
