<?php
$page_title = 'Login';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        $redirect_url = 'admin/dashboard.php';
    } elseif (isEmployee()) {
        $redirect_url = 'employee/dashboard.php';
    } else {
        $redirect_url = 'index.php';
    }
    header('Location: ' . $redirect_url);
    exit();
}

include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        if (login($username, $password)) {
            $success = 'Login successful! Redirecting...';
            if (isAdmin()) {
                $redirect_url = 'admin/dashboard.php';
            } elseif (isEmployee()) {
                $redirect_url = 'employee/dashboard.php';
            } else {
                $redirect_url = 'index.php';
            }
            header('refresh:1;url=' . $redirect_url);
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle fa-4x text-primary"></i>
                        <h2 class="mt-3">Login</h2>
                        <p class="text-muted">Sign in to your account</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">
                                <i class="fas fa-user"></i> Username or Email
                            </label>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                   required autofocus>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock"></i> Password
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                        
                        <div class="text-center">
                            <p class="mb-0">Don't have an account? 
                                <a href="register.php">Register here</a>
                            </p>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                     
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
