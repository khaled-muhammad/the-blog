<?php
require_once 'config.php';

if (hasAdminUsers()) {
    header('Location: /');
    exit;
}

if (isAuthenticated()) {
    header('Location: /admin');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $display_name = trim($_POST['display_name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    
    if (empty($username)) {
        $error = 'Username is required.';
    } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Valid email is required.';
    } elseif (empty($password)) {
        $error = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } elseif (empty($first_name)) {
        $error = 'First name is required.';
    } elseif (empty($last_name)) {
        $error = 'Last name is required.';
    } else {
        $userData = [
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'display_name' => !empty($display_name) ? $display_name : null,
            'bio' => !empty($bio) ? $bio : null,
            'role' => 'admin'
        ];
        
        $user = createUser($userData);
        
        if ($user) {
            header('Location: /login.php?registered=1');
            exit;
        } else {
            $error = 'Registration failed. Username or email may already be in use.';
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>The Blog | Setup Admin Account</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e1e2ea 0%, #f5f5f7 100%);
            padding: 2rem;
        }
        
        .register-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            max-width: 550px;
            width: 100%;
        }
        
        .register-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .register-header .avatar-circle {
            margin: 0 auto 1.5rem;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
        
        .register-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }
        
        .register-header p {
            color: #6b7280;
            font-size: 0.95rem;
        }
        
        .setup-badge {
            display: inline-block;
            background: linear-gradient(to left, #fe5038, #ff2b87);
            color: #fff;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #ff2b87;
            box-shadow: 0 0 0 3px rgba(255, 43, 135, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        @media (max-width: 576px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        .btn-register {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(to left, #fe5038, #ff2b87);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 43, 135, 0.3);
        }
        
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        
        .form-hint {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="avatar-circle">K</div>
                <span class="setup-badge">Initial Setup</span>
                <h1>Create Admin Account</h1>
                <p>Set up your first administrator account to get started</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="form-control" 
                        placeholder="Choose a username"
                        value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        required 
                        autofocus>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        placeholder="your@email.com"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Min. 6 characters"
                            required>
                        <div class="form-hint">At least 6 characters</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirm Password *</label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            class="form-control" 
                            placeholder="Confirm password"
                            required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input 
                            type="text" 
                            id="first_name" 
                            name="first_name" 
                            class="form-control" 
                            placeholder="First name"
                            value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                            required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input 
                            type="text" 
                            id="last_name" 
                            name="last_name" 
                            class="form-control" 
                            placeholder="Last name"
                            value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                            required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="display_name">Display Name</label>
                    <input 
                        type="text" 
                        id="display_name" 
                        name="display_name" 
                        class="form-control" 
                        placeholder="Optional display name"
                        value="<?php echo htmlspecialchars($_POST['display_name'] ?? ''); ?>">
                    <div class="form-hint">Leave empty to use first and last name</div>
                </div>
                
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea 
                        id="bio" 
                        name="bio" 
                        class="form-control" 
                        rows="3"
                        placeholder="Optional bio"><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" class="btn-register">
                    Create Admin Account
                </button>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JavaScript Libraries -->
    <script
        src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>

