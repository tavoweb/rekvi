<?php
// src/classes/Auth.php

declare(strict_types=1);

class Auth
{
    private Database $db;
    private string $usersTable;

    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->usersTable = defined('TABLE_USERS') ? TABLE_USERS : 'vartotojai';
    }

    public function registerUser(string $username, string $email, string $password, string $confirmPassword): array
    {
        $errors = [];
        $username = trim($username);
        $email = trim($email);

        if (empty($username)) {
            $errors['username'] = ['key' => 'auth_username_required'];
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors['username'] = ['key' => 'auth_username_length', 'params' => ['min' => 3, 'max' => 50]];
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['username'] = ['key' => 'auth_username_format'];
        } else {
            $this->db->query("SELECT id FROM " . $this->usersTable . " WHERE vartotojo_vardas = :username");
            $this->db->bind(':username', $username);
            if ($this->db->single()) {
                $errors['username'] = ['key' => 'auth_username_taken'];
            }
        }

        if (empty($email)) {
            $errors['email'] = ['key' => 'auth_email_required'];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ['key' => 'auth_email_invalid'];
        } else {
            $this->db->query("SELECT id FROM " . $this->usersTable . " WHERE el_pastas = :email");
            $this->db->bind(':email', $email);
            if ($this->db->single()) {
                $errors['email'] = ['key' => 'auth_email_taken'];
            }
        }

        if (empty($password)) {
            $errors['password'] = ['key' => 'auth_password_required'];
        } elseif (strlen($password) < 8) {
            $errors['password'] = ['key' => 'auth_password_min_length', 'params' => ['length' => 8]];
        }

        if (empty($confirmPassword)) {
            $errors['confirm_password'] = ['key' => 'auth_confirm_password_required'];
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = ['key' => 'auth_passwords_do_not_match'];
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if ($hashedPassword === false) {
            return ['success' => false, 'errors' => ['general' => ['key' => 'auth_password_hash_failed']]];
        }

        try {
            $this->db->query("INSERT INTO " . $this->usersTable . " (vartotojo_vardas, el_pastas, slaptazodis_hash, role) VALUES (:username, :email, :password_hash, :role)");
            $this->db->bind(':username', $username);
            $this->db->bind(':email', $email);
            $this->db->bind(':password_hash', $hashedPassword);
            $this->db->bind(':role', 'vartotojas');

            if ($this->db->execute()) {
                // 'user_registered_successfully' was already in lang files, used by index.php directly
                // Now Auth.php provides the key for consistency.
                return ['success' => true, 'message_key' => 'auth_registration_success_message_key'];
            } else {
                return ['success' => false, 'errors' => ['general' => ['key' => 'auth_registration_failed_generic']]];
            }
        } catch (PDOException $e) {
            error_log("PDOException in registerUser: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => ['key' => 'auth_registration_system_error']]];
        }
    }

    public function login(string $usernameOrEmail, string $password): bool
    {
        $this->db->query("SELECT id, vartotojo_vardas, el_pastas, slaptazodis_hash, role FROM " . $this->usersTable . " WHERE vartotojo_vardas = :username_identifier OR el_pastas = :email_identifier");
        $this->db->bind(':username_identifier', $usernameOrEmail);
        $this->db->bind(':email_identifier', $usernameOrEmail);
        $user = $this->db->single();

        if ($user && password_verify($password, $user['slaptazodis_hash'])) {
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['username'] = $user['vartotojo_vardas'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
        return false;
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    public function requireLogin(string $redirectPage = 'login', ?string $redirectAction = null, ?int $redirectId = null): void
    {
        if (!$this->isLoggedIn()) {
            // Assuming trans() is available globally after index.php loads helpers.
            // If Auth is instantiated before helpers, this could be an issue.
            // However, set_flash_message is a helper, implying trans() should also be fine.
            set_flash_message('error_message', trans('auth_login_required'));

            if (function_exists('redirect')) {
                redirect($redirectPage, $redirectAction, $redirectId);
            } else { // Fallback if redirect helper not available (should not happen in this app)
                $location = '/' . $redirectPage;
                if ($redirectAction) $location .= '/' . $redirectAction;
                if ($redirectId) $location .= '/' . $redirectId;
                header('Location: ' . $location);
                exit;
            }
        }
    }

    public function requireAdmin(string $defaultRedirectPage = 'home', ?string $defaultRedirectAction = null, ?int $defaultRedirectId = null, string $loginRedirectPage = 'login'): void
    {
        if (!$this->isLoggedIn()) {
            $this->requireLogin($loginRedirectPage);
            return;
        }

        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'administratorius') {
             set_flash_message('error_message', trans('auth_admin_required'));

            if (function_exists('redirect')) {
                redirect($defaultRedirectPage, $defaultRedirectAction, $defaultRedirectId);
            } else { // Fallback
                $location = '/' . $defaultRedirectPage;
                if ($defaultRedirectAction) $location .= '/' . $defaultRedirectAction;
                if ($defaultRedirectId) $location .= '/' . $defaultRedirectId;
                header('Location: ' . $location);
                exit;
            }
        }
    }

    public function getCurrentUserId(): ?int
    {
        return $this->isLoggedIn() ? (int)$_SESSION['user_id'] : null;
    }

    public function getCurrentUserRole(): ?string
    {
        return $this->isLoggedIn() && isset($_SESSION['user_role']) ? (string)$_SESSION['user_role'] : null;
    }

    public function getCurrentUsername(): ?string
    {
        return $this->isLoggedIn() && isset($_SESSION['username']) ? (string)$_SESSION['username'] : null;
    }

    public function isAdmin(): bool
    {
        return $this->isLoggedIn() && $this->getCurrentUserRole() === 'administratorius';
    }

    public function getAllUsers(): array
    {
        $this->db->query("SELECT id, vartotojo_vardas AS username, el_pastas AS email, role, sukurimo_data AS created_at FROM " . $this->usersTable . " ORDER BY id ASC");
        return $this->db->resultSet();
    }
}
