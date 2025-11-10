<?php
require_once 'config.php';

if (isAuthenticated()) {
    header('Location: /admin');
    exit;
}

$error = '';
$success = '';

if (isset($_GET['registered']) && $_GET['registered'] == '1') {
    $success = 'Admin account created successfully! Please log in.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($identifier) || empty($password)) {
        $error = 'Please enter both username/email and password.';
    } else {
        $user = authenticateUser($identifier, $password);
        
        if ($user) {
            if (loginUser($user)) {
                if ($remember) {
                    setcookie('remember_token', bin2hex(random_bytes(32)), time() + (30 * 24 * 60 * 60), '/');
                }
                
                $redirect = $_GET['redirect'] ?? '/admin';
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Login failed. Please try again.';
            }
        } else {
            $error = 'Invalid username/email or password.';
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>The Blog | Login</title>
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
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #e1e2ea 0%, #f5f5f7 100%);
            padding: 2rem;
        }
        
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            max-width: 450px;
            width: 100%;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-header .avatar-circle {
            margin: 0 auto 1.5rem;
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
        }
        
        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #6b7280;
            font-size: 0.95rem;
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
        
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .form-check-input {
            margin-right: 0.5rem;
        }
        
        .form-check-label {
            font-size: 0.9rem;
            color: #6b7280;
        }
        
        .btn-login {
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
        
        .btn-login:hover {
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
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="avatar-circle">K</div>
                <h1>Welcome back</h1>
                <p>Sign in to access your dashboard</p>
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
                    <label for="identifier">Username or Email</label>
                    <input 
                        type="text" 
                        id="identifier" 
                        name="identifier" 
                        class="form-control" 
                        placeholder="Enter your username or email"
                        value="<?php echo htmlspecialchars($_POST['identifier'] ?? ''); ?>"
                        required 
                        autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-control" 
                        placeholder="Enter your password"
                        required>
                </div>
                
                <div class="form-check">
                    <input 
                        type="checkbox" 
                        id="remember" 
                        name="remember" 
                        class="form-check-input">
                    <label for="remember" class="form-check-label">
                        Remember me for 30 days
                    </label>
                </div>
                
                <button type="submit" class="btn-login">
                    Sign In
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

