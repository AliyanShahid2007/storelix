<?php 
$page_title = 'Manage Users';
include '../includes/header.php';

requireAdmin();

// Handle user role actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    global $db;

    if ($_POST['action'] == 'promote_employee') {
        $stmt = $db->prepare("UPDATE users SET role='employee' WHERE id=? AND is_admin=0");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    } elseif ($_POST['action'] == 'demote_customer') {
        $stmt = $db->prepare("UPDATE users SET role='customer' WHERE id=? AND is_admin=0");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    } elseif ($_POST['action'] == 'delete_employee') {
        $stmt = $db->prepare("DELETE FROM users WHERE id=? AND role='employee' AND is_admin=0");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    } elseif ($_POST['action'] == 'add_employee') {
        $username = $db->escape($_POST['username']);
        $email = $db->escape($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $full_name = $db->escape($_POST['full_name']);
        $phone = $db->escape($_POST['phone']);
        $address = $db->escape($_POST['address']);

        // Check if username or email already exists
        $check_stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check_stmt->bind_param("ss", $username, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            // User already exists, redirect with error
            header('Location: users.php?error=user_exists');
            exit();
        }

        $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, phone, address, role, is_admin) VALUES (?, ?, ?, ?, ?, ?, 'employee', 0)");
        $stmt->bind_param("ssssss", $username, $email, $password, $full_name, $phone, $address);

        if ($stmt->execute()) {
            header('Location: users.php?success=employee_added');
            exit();
        } else {
            header('Location: users.php?error=add_failed');
            exit();
        }
    }

    // Redirect to refresh the page
    header('Location: users.php');
    exit();
}

global $db;
$result = $db->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="container-fluid my-4 px-4">
    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 'employee_added'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> Employee added successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle"></i>
            <?php if ($_GET['error'] == 'user_exists'): ?>
                Username or email already exists!
            <?php elseif ($_GET['error'] == 'add_failed'): ?>
                Failed to add employee. Please try again.
            <?php endif; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="mb-1"><i class="fas fa-users text-primary me-2"></i>Manage Users</h1>
            <p class="text-muted mb-0">Manage user accounts and permissions</p>
        </div>
        <div>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                <i class="fas fa-user-plus"></i> Add Employee
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Full Name</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Registered</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($user['username']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                <td>
                                    <?php if ($user['is_admin']): ?>
                                        <span class="badge bg-danger">Admin</span>
                                    <?php elseif ($user['role'] == 'employee'): ?>
                                        <span class="badge bg-warning">Employee</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">Customer</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if (!$user['is_admin']): ?>
                                        <?php if ($user['role'] == 'customer'): ?>
                                            <form method="POST" action="" class="d-inline">
                                                <input type="hidden" name="action" value="promote_employee">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Promote this user to employee?')">
                                                    <i class="fas fa-user-plus"></i> Promote
                                                </button>
                                            </form>
                                        <?php elseif ($user['role'] == 'employee'): ?>
                                            <form method="POST" action="" class="d-inline me-1">
                                                <input type="hidden" name="action" value="demote_customer">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Demote this employee to customer?')">
                                                    <i class="fas fa-user-minus"></i> Demote
                                                </button>
                                            </form>
                                            <form method="POST" action="" class="d-inline">
                                                <input type="hidden" name="action" value="delete_employee">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this employee account?')">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
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

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">
                    <i class="fas fa-user-plus text-success me-2"></i>Add New Employee
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_employee">

                    <div class="mb-3">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i> Username *
                        </label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i> Email *
                        </label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> Password *
                        </label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">
                            <i class="fas fa-id-card"></i> Full Name *
                        </label>
                        <input type="text" class="form-control" id="full_name" name="full_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone"></i> Phone
                        </label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Address
                        </label>
                        <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-user-plus"></i> Add Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
