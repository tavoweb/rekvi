<?php
// src/helpers.php

/**
 * Saugaus HTML atvaizdavimo pagalbinė funkcija.
 * Apsaugo nuo XSS atakų.
 *
 * @param string|null $string Tekstas, kurį reikia apdoroti.
 * @return string Apdorotas tekstas.
 */
function e(?string $string): string {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}

/**
 * Nukreipia vartotoją į nurodytą URL.
 *
 * @param string $page Puslapis (pvz., 'home', 'companies')
 * @param string|null $action Veiksmas (pvz., 'view', 'edit')
 * @param int|null $id Įrašo ID (jei reikia)
 * @param array $params Papildomi parametrai
 */
function redirect(string $page, ?string $action = null, ?int $id = null, array $params = []): void {
    $url = url($page, $action, $id, $params);
    header("Location: " . $url);
    exit;
}

/**
 * Išsaugo pranešimą sesijoje.
 *
 * @param string $name Pranešimo pavadinimas (pvz., 'success_message', 'error_message').
 * @param string $message Pranešimo tekstas.
 */
function set_flash_message(string $name, string $message): void {
    $_SESSION[$name] = $message;
}

/**
 * Gauna ir išvalo pranešimą iš sesijos.
 *
 * @param string $name Pranešimo pavadinimas.
 * @return string|null Pranešimo tekstas arba null, jei nėra.
 */
function get_flash_message(string $name): ?string {
    if (isset($_SESSION[$name])) {
        $message = $_SESSION[$name];
        unset($_SESSION[$name]);
        return $message;
    }
    return null;
}

/**
 * Generuoja švarią URL nuorodą be "index.php?"
 *
 * @param string $page Puslapis (pvz., 'home', 'companies')
 * @param string|null $action Veiksmas (pvz., 'view', 'edit')
 * @param int|null $id Įrašo ID (jei reikia)
 * @param array $params Papildomi parametrai (pvz., ['search_query' => 'test'])
 * @return string Sugeneruota URL nuoroda
 */
function url(string $page, ?string $action = null, ?int $id = null, array $params = []): string {
    $base_url = '';
    
    if ($action === null) {
        // Nuoroda į pagrindinį puslapį: /home
        $url = $base_url . '/' . $page;
    } elseif ($id === null) {
        // Nuoroda į veiksmą: /companies/create
        $url = $base_url . '/' . $page . '/' . $action;
    } else {
        // Nuoroda į veiksmą su ID: /companies/view/123
        $url = $base_url . '/' . $page . '/' . $action . '/' . $id;
    }
    
    // Pridedame papildomus parametrus jei yra
    if (!empty($params)) {
        $query_string = http_build_query($params);
        $url .= '?' . $query_string;
    }
    
    return $url;
}

/**
 * Tvarko logotipo failo įkėlimą.
 *
 * @param array $file_input Duomenys iš $_FILES superglobalaus masyvo.
 * @param string|null $current_logo_filename Esamo logotipo failo vardas (jei yra, bus ištrintas sėkmingo naujo įkėlimo atveju).
 * @return array Rezultatas: ['success' => bool, 'filename' => ?string, 'error' => ?string]
 *               'filename' yra naujo failo vardas sėkmės atveju, arba $current_logo_filename jei nieko neįkelta/klaida.
 */
function handle_logo_upload(array $file_input, ?string $current_logo_filename = null): array {
    if (isset($file_input['name']) && $file_input['error'] === UPLOAD_ERR_OK) {
        $filename = $file_input['name'];
        $temp_path = $file_input['tmp_name'];
        $filesize = $file_input['size'];
        $file_type = $file_input['type']; // Gautas iš naršyklės, gali būti nepatikimas

        $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif'];
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $max_filesize = 2 * 1024 * 1024; // 2MB

        $actual_mime_type = mime_content_type($temp_path);
        if (!in_array(strtolower($actual_mime_type), $allowed_mime_types)) {
            return ['success' => false, 'filename' => $current_logo_filename, 'error' => 'Netinkamas failo tipas. Leidžiama JPG, PNG, GIF.'];
        }
        if ($filesize > $max_filesize) {
            return ['success' => false, 'filename' => $current_logo_filename, 'error' => 'Failas per didelis. Maksimalus dydis 2MB.'];
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(!in_array($extension, $allowed_extensions)){
            return ['success' => false, 'filename' => $current_logo_filename, 'error' => 'Netinkamas failo plėtinys. Leidžiama JPG, JPEG, PNG, GIF.'];
        }
        
        $new_filename = uniqid('logo_', true) . '.' . $extension;
        $destination = LOGO_UPLOAD_PATH . $new_filename;

        if (move_uploaded_file($temp_path, $destination)) {
            if ($current_logo_filename && file_exists(LOGO_UPLOAD_PATH . $current_logo_filename)) {
                @unlink(LOGO_UPLOAD_PATH . $current_logo_filename);
            }
            return ['success' => true, 'filename' => $new_filename, 'error' => null];
        } else {
            return ['success' => false, 'filename' => $current_logo_filename, 'error' => 'Klaida įkeliant failą į serverį.'];
        }
    } elseif (isset($file_input['error']) && $file_input['error'] !== UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'filename' => $current_logo_filename, 'error' => 'Failo įkėlimo klaida. Kodas: ' . $file_input['error']];
    }
    return ['success' => true, 'filename' => $current_logo_filename, 'error' => null]; // Nebuvo bandoma įkelti naujo failo
}