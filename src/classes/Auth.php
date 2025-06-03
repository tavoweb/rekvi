<?php

// src/classes/Auth.php

declare(strict_types=1); // Griežtas tipų tikrinimas

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
            $errors['username'] = 'Vartotojo vardas yra privalomas.';
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $errors['username'] = 'Vartotojo vardas turi būti nuo 3 iki 50 simbolių ilgio.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors['username'] = 'Vartotojo vardas gali turėti tik raides (a-z, A-Z), skaičius (0-9) ir apatinį brūkšnį (_).';
        } else {
            $this->db->query("SELECT id FROM " . $this->usersTable . " WHERE vartotojo_vardas = :username");
            $this->db->bind(':username', $username);
            if ($this->db->single()) {
                $errors['username'] = 'Toks vartotojo vardas jau užimtas.';
            }
        }

        if (empty($email)) {
            $errors['email'] = 'El. paštas yra privalomas.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Neteisingas el. pašto formatas.';
        } else {
            $this->db->query("SELECT id FROM " . $this->usersTable . " WHERE el_pastas = :email");
            $this->db->bind(':email', $email);
            if ($this->db->single()) {
                $errors['email'] = 'Šis el. paštas jau registruotas.';
            }
        }

        if (empty($password)) {
            $errors['password'] = 'Slaptažodis yra privalomas.';
        } elseif (strlen($password) < 8) {
            $errors['password'] = 'Slaptažodis turi būti bent 8 simbolių ilgio.';
        }

        if (empty($confirmPassword)) {
            $errors['confirm_password'] = 'Pakartokite slaptažodį.';
        } elseif ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Slaptažodžiai nesutampa.';
        }

        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if ($hashedPassword === false) {
            return ['success' => false, 'errors' => ['general' => 'Nepavyko užkoduoti slaptažodžio.']];
        }

        try {
            $this->db->query("INSERT INTO " . $this->usersTable . " (vartotojo_vardas, el_pastas, slaptazodis_hash, role) VALUES (:username, :email, :password_hash, :role)");
            $this->db->bind(':username', $username);
            $this->db->bind(':email', $email);
            $this->db->bind(':password_hash', $hashedPassword);
            $this->db->bind(':role', 'vartotojas');

            if ($this->db->execute()) {
                return ['success' => true, 'message' => 'Vartotojas sėkmingai užregistruotas. Galite prisijungti.'];
            } else {
                return ['success' => false, 'errors' => ['general' => 'Registracija nepavyko. Bandykite vėliau.']];
            }
        } catch (PDOException $e) {
            error_log("PDOException in registerUser: " . $e->getMessage());
            return ['success' => false, 'errors' => ['general' => 'Sistemos klaida registruojant vartotoją.']];
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
            if (function_exists('set_flash_message')) {
                set_flash_message('error_message', "Norėdami pasiekti šį puslapį, turite prisijungti.");
            } else {
                $_SESSION['error_message'] = "Norėdami pasiekti šį puslapį, turite prisijungti.";
            }

            if (function_exists('redirect')) {
                redirect($redirectPage, $redirectAction, $redirectId);
            } else {
                if ($redirectAction === null) {
                    header('Location: /' . $redirectPage);
                } elseif ($redirectId === null) {
                    header('Location: /' . $redirectPage . '/' . $redirectAction);
                } else {
                    header('Location: /' . $redirectPage . '/' . $redirectAction . '/' . $redirectId);
                }
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
            $errorMessage = "Neturite teisių pasiekti šį resursą. Kreipkitės į administratorių.";
            if (function_exists('set_flash_message')) {
                set_flash_message('error_message', $errorMessage);
            } else {
                $_SESSION['error_message'] = $errorMessage;
            }

            if (function_exists('redirect')) {
                redirect($defaultRedirectPage, $defaultRedirectAction, $defaultRedirectId);
            } else {
                if ($defaultRedirectAction === null) {
                    header('Location: /' . $defaultRedirectPage);
                } elseif ($defaultRedirectId === null) {
                    header('Location: /' . $defaultRedirectPage . '/' . $defaultRedirectAction);
                } else {
                    header('Location: /' . $defaultRedirectPage . '/' . $defaultRedirectAction . '/' . $defaultRedirectId);
                }
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

    /**
     * Gauna visus registruotus vartotojus.
     * Tik administratoriai turėtų kviesti šią funkciją.
     * @return array Vartotojų sąrašas.
     */
    public function getAllUsers(): array
    {
        // Ensure this query selects all necessary fields, including registration_date if available
        // Using 'sukurimo_data' and aliasing as 'registracijos_data' for compatibility.
        // If not, adjust the query. Add other fields if needed.
        $this->db->query("SELECT id, vartotojo_vardas, el_pastas, role, sukurimo_data AS registracijos_data FROM " . $this->usersTable . " ORDER BY id ASC");
        return $this->db->resultSet();
    }
}
