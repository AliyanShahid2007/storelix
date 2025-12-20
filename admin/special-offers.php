<?php
$page_title = 'Special Offers Management';
require_once '../includes/header.php';
requireAdmin();

$offers = getAllSpecialOffers();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $discount_percentage = floatval($_POST['discount_percentage']);
        $end_time = $_POST['end_time'];

        if (createSpecialOffer($title, $description, $discount_percentage, $end_time)) {
            $success = "Special offer created successfully!";
            $offers = getAllSpecialOffers(); // Refresh the list
        } else {
            $error = "Failed to create special offer.";
        }
    } elseif (isset($_POST['update'])) {
        $id = intval($_POST['id']);
        $title = sanitizeInput($_POST['title']);
        $description = sanitizeInput($_POST['description']);
        $discount_percentage = floatval($_POST['discount_percentage']);
        $end_time = $_POST['end_time'];
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if (updateSpecialOffer($id, $title, $description, $discount_percentage, $end_time, $is_active)) {
            $success = "Special offer updated successfully!";
            $offers = getAllSpecialOffers(); // Refresh the list
        } else {
            $error = "Failed to update special offer.";
        }
    } elseif (isset($_POST['quick_toggle'])) {
        $id = intval($_POST['id']);
        $is_active = intval($_POST['is_active']);

        // Quick toggle only updates the active status
        $offer = getSpecialOfferById($id);
        if ($offer && updateSpecialOffer($id, $offer['title'], $offer['description'], $offer['discount_percentage'], $offer['end_time'], $is_active)) {
            $status_text = $is_active ? 'activated' : 'deactivated';
            $success = "Special offer $status_text successfully!";
            $offers = getAllSpecialOffers(); // Refresh the list
        } else {
            $error = "Failed to update special offer status.";
        }
    } elseif (isset($_POST['delete'])) {
        $id = intval($_POST['id']);
        if (deleteSpecialOffer($id)) {
            $success = "Special offer deleted successfully!";
            $offers = getAllSpecialOffers(); // Refresh the list
        } else {
            $error = "Failed to delete special offer.";
        }
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Special Offers Management</h1>
                <div>
                    <button class="btn btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#previewModal">
                        <i class="fas fa-eye"></i> Preview Timer
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createOfferModal">
                        <i class="fas fa-plus"></i> Create New Offer
                    </button>
                </div>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Special Offers</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Discount</th>
                                    <th>End Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($offers as $offer): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($offer['title']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($offer['description'], 0, 50)) . (strlen($offer['description']) > 50 ? '...' : ''); ?></td>
                                        <td><?php echo $offer['discount_percentage']; ?>%</td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($offer['end_time'])); ?></td>
                                        <td>
                                            <?php if ($offer['is_active'] && strtotime($offer['end_time']) > time()): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php elseif (!$offer['is_active']): ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">Expired</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-outline-success" onclick="toggleActive(<?php echo $offer['id']; ?>, <?php echo $offer['is_active'] ? 0 : 1; ?>)"
                                                        title="<?php echo $offer['is_active'] ? 'Deactivate' : 'Activate'; ?> offer">
                                                    <i class="fas fa-<?php echo $offer['is_active'] ? 'pause' : 'play'; ?>"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-primary" onclick="editOffer(<?php echo $offer['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-info" onclick="previewOffer(<?php echo $offer['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteOffer(<?php echo $offer['id']; ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Offer Modal -->
<div class="modal fade" id="createOfferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Special Offer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="discount_percentage" class="form-label">Discount Percentage</label>
                        <input type="number" class="form-control" id="discount_percentage" name="discount_percentage" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="create" class="btn btn-primary">Create Offer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Offer Modal -->
<div class="modal fade" id="editOfferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Special Offer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_discount_percentage" class="form-label">Discount Percentage</label>
                        <input type="number" class="form-control" id="edit_discount_percentage" name="discount_percentage" min="0" max="100" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_end_time" class="form-label">End Time</label>
                        <input type="datetime-local" class="form-control" id="edit_end_time" name="end_time" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active">
                            <label class="form-check-label" for="edit_is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update" class="btn btn-primary">Update Offer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Preview Timer Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Countdown Timer Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="preview_offer_select" class="form-label">Select Offer to Preview</label>
                    <select class="form-control" id="preview_offer_select">
                        <option value="">Select an offer...</option>
                        <?php foreach ($offers as $offer): ?>
                            <option value="<?php echo $offer['id']; ?>"
                                    data-title="<?php echo htmlspecialchars($offer['title']); ?>"
                                    data-description="<?php echo htmlspecialchars($offer['description']); ?>"
                                    data-discount="<?php echo $offer['discount_percentage']; ?>"
                                    data-end-time="<?php echo $offer['end_time']; ?>">
                                <?php echo htmlspecialchars($offer['title']); ?> (<?php echo $offer['discount_percentage']; ?>%)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="preview-container" style="display: none;">
                    <!-- Special Offer Countdown Timer Preview -->
                    <section class="special-offer-section py-4">
                        <div class="container">
                            <div class="special-offer-banner">
                                <div class="row align-items-center">
                                    <div class="col-lg-8">
                                        <h3 id="preview-title" class="mb-2">Offer Title</h3>
                                        <p id="preview-description" class="mb-0">Offer description will appear here.</p>
                                        <div class="discount-badge">
                                            <span id="preview-discount" class="badge bg-warning text-dark fs-6">50% OFF</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 text-center">
                                        <div class="countdown-timer" id="preview-countdown-timer">
                                            <div class="timer-label">Offer ends in:</div>
                                            <div class="timer-display">
                                                <div class="timer-item">
                                                    <span class="timer-value" id="preview-days">00</span>
                                                    <span class="timer-unit">Days</span>
                                                </div>
                                                <div class="timer-item">
                                                    <span class="timer-value" id="preview-hours">00</span>
                                                    <span class="timer-unit">Hours</span>
                                                </div>
                                                <div class="timer-item">
                                                    <span class="timer-value" id="preview-minutes">00</span>
                                                    <span class="timer-unit">Min</span>
                                                </div>
                                                <div class="timer-item">
                                                    <span class="timer-value" id="preview-seconds">00</span>
                                                    <span class="timer-unit">Sec</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function editOffer(id) {
    // Find the offer data from the table
    const row = document.querySelector(`button[onclick="editOffer(${id})"]`).closest('tr');
    const cells = row.querySelectorAll('td');

    document.getElementById('edit_id').value = id;
    document.getElementById('edit_title').value = cells[0].textContent;
    document.getElementById('edit_description').value = cells[1].textContent;
    document.getElementById('edit_discount_percentage').value = parseFloat(cells[2].textContent);
    document.getElementById('edit_end_time').value = cells[3].textContent.replace(' ', 'T');
    document.getElementById('edit_is_active').checked = cells[4].querySelector('.badge')?.classList.contains('bg-success');

    new bootstrap.Modal(document.getElementById('editOfferModal')).show();
}

function deleteOffer(id) {
    if (confirm('Are you sure you want to delete this special offer?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="id" value="${id}">
            <input type="hidden" name="delete" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleActive(id, is_active) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="id" value="${id}">
        <input type="hidden" name="is_active" value="${is_active}">
        <input type="hidden" name="quick_toggle" value="1">
    `;
    document.body.appendChild(form);
    form.submit();
}

function previewOffer(id) {
    // Find the offer data from the table
    const row = document.querySelector(`button[onclick="previewOffer(${id})"]`).closest('tr');
    const cells = row.querySelectorAll('td');

    // Set the select dropdown to this offer
    document.getElementById('preview_offer_select').value = id;

    // Trigger the preview update
    updatePreview();

    // Show the modal
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

function updatePreview() {
    const select = document.getElementById('preview_offer_select');
    const selectedOption = select.options[select.selectedIndex];
    const container = document.getElementById('preview-container');

    if (!selectedOption.value) {
        container.style.display = 'none';
        return;
    }

    // Update preview content
    document.getElementById('preview-title').textContent = selectedOption.getAttribute('data-title');
    document.getElementById('preview-description').textContent = selectedOption.getAttribute('data-description');
    document.getElementById('preview-discount').textContent = selectedOption.getAttribute('data-discount') + '% OFF';

    // Start countdown timer
    const endTime = new Date(selectedOption.getAttribute('data-end-time')).getTime();
    startPreviewCountdown(endTime);

    container.style.display = 'block';
}

function startPreviewCountdown(endTime) {
    const countdownInterval = setInterval(() => {
        const now = new Date().getTime();
        const distance = endTime - now;

        if (distance < 0) {
            clearInterval(countdownInterval);
            document.getElementById('preview-days').textContent = '00';
            document.getElementById('preview-hours').textContent = '00';
            document.getElementById('preview-minutes').textContent = '00';
            document.getElementById('preview-seconds').textContent = '00';
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('preview-days').textContent = days.toString().padStart(2, '0');
        document.getElementById('preview-hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('preview-minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('preview-seconds').textContent = seconds.toString().padStart(2, '0');
    }, 1000);
}

// Initialize preview modal event listener
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('preview_offer_select').addEventListener('change', updatePreview);
});
</script>

<?php include '../includes/footer.php'; ?>
