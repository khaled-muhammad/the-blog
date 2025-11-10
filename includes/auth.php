<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function isAuthenticated() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
}


function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'display_name' => $_SESSION['display_name'] ?? null,
        'role' => $_SESSION['role'] ?? null,
        'avatar_url' => $_SESSION['avatar_url'] ?? null
    ];
}

function requireAuth($redirectTo = null) {
    if (!isAuthenticated()) {
        $redirect = $redirectTo ? '?redirect=' . urlencode($redirectTo) : '';
        header('Location: /login.php' . $redirect);
        exit;
    }
}

function requireRole($requiredRole, $redirectTo = '/') {
    if (!isAuthenticated()) {
        requireAuth($redirectTo);
    }
    
    $userRole = $_SESSION['role'] ?? null;
    $roles = is_array($requiredRole) ? $requiredRole : [$requiredRole];
    
    if (!in_array($userRole, $roles)) {
        header('Location: ' . $redirectTo);
        exit;
    }
}

function loginUser($user) {
    if (empty($user) || !isset($user['id'])) {
        return false;
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'] ?? null;
    $_SESSION['email'] = $user['email'] ?? null;
    $_SESSION['display_name'] = $user['display_name'] ?? ($user['first_name'] . ' ' . $user['last_name']);
    $_SESSION['role'] = $user['role'] ?? 'author';
    $_SESSION['avatar_url'] = $user['avatar_url'] ?? null;
    $_SESSION['user_logged_in'] = true;
    
    if (isset($GLOBALS['conn'])) {
        try {
            $stmt = $GLOBALS['conn']->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);
        } catch (PDOException $e) {
            error_log("Failed to update last login: " . $e->getMessage());
        }
    }
    
    return true;
}


function logoutUser() {
    $_SESSION = array();
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}


function hasAdminUsers() {
    if (!isset($GLOBALS['conn'])) {
        return false;
    }
    
    try {
        $stmt = $GLOBALS['conn']->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'admin' AND status = 'active'");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result && $result['count'] > 0);
    } catch (PDOException $e) {
        error_log("Error checking admin users: " . $e->getMessage());
        return false;
    }
}

function createUser($userData) {
    if (!isset($GLOBALS['conn'])) {
        return false;
    }
    
    try {
        $checkStmt = $GLOBALS['conn']->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $checkStmt->execute([$userData['username'], $userData['email']]);
        if ($checkStmt->fetch()) {
            return false;
        }
        
        $passwordHash = hashPassword($userData['password']);
        
        $stmt = $GLOBALS['conn']->prepare("
            INSERT INTO users (
                username, email, password_hash, first_name, last_name,
                display_name, bio, role, status, email_verified_at, created_at, updated_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW(), NOW()
            )
        ");
        
        $stmt->execute([
            $userData['username'],
            $userData['email'],
            $passwordHash,
            $userData['first_name'],
            $userData['last_name'],
            $userData['display_name'] ?? null,
            $userData['bio'] ?? null,
            $userData['role'] ?? 'author'
        ]);
        
        $userId = $GLOBALS['conn']->lastInsertId();
        $userStmt = $GLOBALS['conn']->prepare("
            SELECT id, username, email, first_name, last_name, display_name, role, avatar_url, status 
            FROM users WHERE id = ?
        ");
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        
        return $user;
    } catch (PDOException $e) {
        error_log("Error creating user: " . $e->getMessage());
        return false;
    }
}

function authenticateUser($identifier, $password) {
    if (!isset($GLOBALS['conn'])) {
        return false;
    }
    
    try {
        $stmt = $GLOBALS['conn']->prepare("
            SELECT id, username, email, password_hash, first_name, last_name, 
                   display_name, role, avatar_url, status 
            FROM users 
            WHERE (username = ? OR email = ?) AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return false;
        }
        
        if (!verifyPassword($password, $user['password_hash'])) {
            return false;
        }
        
        unset($user['password_hash']);
        
        return $user;
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

