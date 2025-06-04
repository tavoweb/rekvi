<?php

// public/index.php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/classes/Database.php';
require_once __DIR__ . '/../src/classes/Auth.php';
require_once __DIR__ . '/../src/classes/Company.php';
require_once __DIR__ . '/../src/classes/SitemapGenerator.php';

ini_set('display_errors', "1");
ini_set('display_startup_errors', "1");
error_reporting(E_ALL);

define('LOGO_UPLOAD_DIR_PUBLIC', '/uploads/logos/');
define('LOGO_UPLOAD_PATH', __DIR__ . '/uploads/logos/');
define('SITE_BASE_URL', 'http://localhost'); // Base URL for the site

if (!is_dir(LOGO_UPLOAD_PATH)) {
    if (!mkdir(LOGO_UPLOAD_PATH, 0775, true)) {
        die("Klaida: Nepavyko sukurti logotipų katalogo: " . LOGO_UPLOAD_PATH);
    }
}
if (!is_writable(LOGO_UPLOAD_PATH)) {
    die("Klaida: Logotipų katalogas (" . LOGO_UPLOAD_PATH . ") nėra įrašomas (writable). Patikrinkite teises.");
}

try {
    $db = new Database();
} catch (Exception $e) {
    error_log($e->getMessage());
    die("Atsiprašome, įvyko sisteminė klaida. Bandykite vėliau.");
}

$auth = new Auth($db);
$companyManager = new Company($db);
$view_data['total_companies'] = $companyManager->getTotalCompaniesCount();

$view_template = 'home.php';

// Initialize with defaults
$page = 'home';
$action = null;
$id = null;

if (isset($_GET['url'])) {
    $url = trim($_GET['url'], '/');
    $segments = explode('/', $url);

    if (!empty($segments[0])) {
        $page = $segments[0];
    }
    if (isset($segments[1])) {
        $action = $segments[1];
    }
    if (isset($segments[2])) {
        $id = (int)$segments[2];
    }
} else {
    // Fallback to old parameter structure if 'url' is not set
    $page = $_GET['page'] ?? 'home';
    $action = $_GET['action'] ?? null;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
}


switch ($page) {
    case 'home':
        $view_data['isLoggedIn'] = $auth->isLoggedIn();
        $view_data['username'] = $auth->getCurrentUsername();
        $view_template = 'home.php';
        break;

    case 'register':
        if ($auth->isLoggedIn()) {
            redirect('home');
        }
        $view_data['form_values'] = ['username' => '', 'email' => ''];
        $view_data['errors'] = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $view_data['form_values']['username'] = $_POST['username'] ?? '';
            $view_data['form_values']['email'] = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            $result = $auth->registerUser($view_data['form_values']['username'], $view_data['form_values']['email'], $password, $confirmPassword);
            if ($result['success']) {
                set_flash_message('success_message', $result['message']);
                redirect('login');
            } else {
                $view_data['errors'] = $result['errors'];
            }
        }
        $view_template = 'auth/register_form.php';
        break;

    case 'login':
        if ($auth->isLoggedIn()) {
            redirect('home');
        }
        $view_data['form_values'] = ['username_or_email' => ''];
        $view_data['errors'] = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $view_data['form_values']['username_or_email'] = $_POST['username_or_email'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($auth->login($view_data['form_values']['username_or_email'], $password)) {
                set_flash_message('success_message', 'Sėkmingai prisijungėte!');
                if ($auth->isAdmin()) {
                    redirect('companies');
                } else {
                    redirect('home');
                }
            } else {
                $view_data['errors']['general'] = 'Neteisingas vartotojo vardas/el.paštas arba slaptažodis.';
                $view_data['errors']['credentials'] = 'Patikrinkite įvestus duomenis.';
            }
        }
        $view_template = 'auth/login_form.php';
        break;

    case 'logout':
        $auth->logout();
        set_flash_message('success_message', 'Sėkmingai atsijungėte.');
        redirect('login');
        break;

    case 'companies':
        switch ($action) {
            case 'create':
                // Leidžiame visiems kurti įmones, todėl administratoriaus patikrinimas pašalinamas.
                // $auth->requireAdmin('index.php?page=companies', 'index.php?page=login');
                $view_data['errors'] = [];
                $view_data['company'] = null;

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $company_data = $_POST;
                    $company_data['logotipas_filename'] = null; // Numatytasis, jei nieko neįkelta

                    if (empty($company_data['pavadinimas']) || empty($company_data['imones_kodas'])) {
                        $view_data['errors']['general'] = 'Pavadinimas ir įmonės kodas yra privalomi.';
                    } else {
                        // Patikriname, ar įmonė su tokiu kodu jau egzistuoja
                        if ($companyManager->findCompanyByCode($company_data['imones_kodas'])) {
                            $view_data['errors']['imones_kodas'] = 'Įmonė su tokiu įmonės kodu jau egzistuoja.';
                        }
                    }

                    // Jei nėra klaidų dėl įmonės kodo, tvarkome logotipo įkėlimą
                    if (!isset($view_data['errors']['imones_kodas']) && isset($_FILES['logotipas']) && $_FILES['logotipas']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $upload_result = handle_logo_upload($_FILES['logotipas']);
                        if ($upload_result['success']) {
                            $company_data['logotipas_filename'] = $upload_result['filename'];
                        } else {
                            $view_data['errors']['logotipas'] = $upload_result['error'];
                        }
                    }

                    // Tikriname visas klaidas prieš bandant kurti
                    if (empty($view_data['errors'])) { // Patikriname, ar $view_data['errors'] masyvas tuščias
                        if ($companyManager->createCompany($company_data)) {
                            set_flash_message('success_message', 'Įmonė sėkmingai pridėta.');
                            redirect('companies');
                        } else {
                            $view_data['errors']['general'] = 'Klaida pridedant įmonę. Patikrinkite duomenis arba serverio logus.';
                        }
                    }
                    foreach ($company_data as $key => $value) { // Išsaugome formos duomenis po klaidos
                        if (!is_array($value)) {
                            $view_data['company'][$key] = $value;
                        }
                    }
                }
                $view_template = 'companies/form.php';
                break;

            case 'edit':
                $auth->requireAdmin('companies', 'login');
                if (!$id) {
                    redirect('companies');
                }

                $company = $companyManager->getCompanyById($id);
                if (!$company) {
                    set_flash_message('error_message', 'Įmonė nerasta.');
                    redirect('companies');
                }
                $view_data['company'] = $company; // Perduodame esamus duomenis į formą
                $view_data['errors'] = [];

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $company_data = $_POST;
                    $current_logo_db_filename = $company['logotipas']; // Iš DB gautas failo vardas

                    $company_data['logotipas_filename'] = $current_logo_db_filename; // Priskiriame seną pagal nutylėjimą

                    if (isset($_POST['remove_logo']) && $_POST['remove_logo'] == '1') {
                        if ($current_logo_db_filename && file_exists(LOGO_UPLOAD_PATH . $current_logo_db_filename)) {
                            @unlink(LOGO_UPLOAD_PATH . $current_logo_db_filename);
                        }
                        $company_data['logotipas_filename'] = null; // Nustatome null, kad DB būtų atnaujinta
                    } elseif (isset($_FILES['logotipas']) && $_FILES['logotipas']['error'] !== UPLOAD_ERR_NO_FILE) {
                        // Įkeliamas naujas, senas bus ištrintas handle_logo_upload viduje, jei sėkmingai
                        $upload_result = handle_logo_upload($_FILES['logotipas'], $current_logo_db_filename);
                        if (!$upload_result['success']) {
                            $view_data['errors']['logotipas'] = $upload_result['error'];
                            // Jei įkėlimas nepavyko, logotipas_filename lieka senas ($current_logo_db_filename),
                            // nes $company_data['logotipas_filename'] buvo priskirtas $current_logo_db_filename anksčiau.
                        } else {
                            $company_data['logotipas_filename'] = $upload_result['filename']; // Sėkmingai įkeltas naujas arba senas, jei nebuvo įkelta
                        }
                    }

                    if (empty($company_data['pavadinimas']) || empty($company_data['imones_kodas'])) {
                        $view_data['errors']['general'] = 'Pavadinimas ir įmonės kodas yra privalomi.';
                    }

                    if (!isset($view_data['errors']['logotipas']) && empty($view_data['errors']['general'])) {
                        if ($companyManager->updateCompany($id, $company_data)) {
                            set_flash_message('success_message', 'Įmonės duomenys sėkmingai atnaujinti.');
                            redirect('companies', 'view', $id);
                        } else {
                            $view_data['errors']['general'] = 'Klaida atnaujinant įmonės duomenis. Patikrinkite serverio logus.';
                        }
                    }
                    // Atnaujiname $view_data['company'] su POST reikšmėmis, kad forma būtų užpildyta po klaidos
                    foreach ($company_data as $key => $value) {
                        if (!is_array($value) && array_key_exists($key, $view_data['company'])) {
                            $view_data['company'][$key] = $value;
                        }
                    }
                    // Atnaujiname logotipo failo vardą $view_data, jei jis pasikeitė
                    $view_data['company']['logotipas'] = $company_data['logotipas_filename'];
                }
                $view_template = 'companies/form.php';
                break;

            case 'delete':
                $auth->requireAdmin('companies', 'login');
                if (!$id) {
                    redirect('companies');
                }
                $company = $companyManager->getCompanyById($id);
                if (!$company) {
                    set_flash_message('error_message', 'Įmonė nerasta norint ištrinti.');
                    redirect('companies');
                }
                $view_data['company'] = $company;
                $view_template = 'companies/delete_confirm.php';
                break;

            case 'delete_submit':
                $auth->requireAdmin('companies');
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id && isset($_POST['confirm_delete'])) {
                    if ($companyManager->deleteCompany($id)) {
                        set_flash_message('success_message', 'Įmonė sėkmingai ištrinta.');
                    } else {
                        set_flash_message('error_message', 'Nepavyko ištrinti įmonės.');
                    }
                } else {
                    set_flash_message('error_message', 'Neteisinga užklausa trynimui.');
                }
                redirect('companies');
                break;

            case 'view':
                if (!$id) {
                    redirect('companies');
                }
                $company = $companyManager->getCompanyById($id);
                if (!$company) {
                    set_flash_message('error_message', 'Įmonė nerasta.');
                    redirect('companies');
                }
                $view_data['company'] = $company;
                if ($company) {
                    $view_data['meta_title'] = e($company['pavadinimas']) . ' - Rekvizitai'; // Dynamic title

                    // Construct a meta description
                    $description_parts = [];
                    if (!empty($company['pavadinimas'])) {
                        $description_parts[] = "Įmonės " . e($company['pavadinimas']) . " rekvizitai.";
                    }
                    if (!empty($company['adresas_gatve'])) {
                        $description_parts[] = e($company['adresas_gatve']);
                    }
                    if (!empty($company['adresas_miestas'])) {
                        $description_parts[] = e($company['adresas_miestas']);
                    }
                    if (!empty($company['imones_kodas'])) {
                        $description_parts[] = "Įmonės kodas: " . e($company['imones_kodas']);
                    }
                    // Join parts, ensuring not too long. Limit to around 160 chars.
                    $meta_desc_full = implode(', ', $description_parts);
                    if (mb_strlen($meta_desc_full) > 160) {
                        $meta_desc_full = mb_substr($meta_desc_full, 0, 157) . '...';
                    }
                    $view_data['meta_description'] = $meta_desc_full;
                }
                $view_template = 'companies/view.php';
                break;

            case 'import':
                $auth->requireAdmin('companies', 'login');
                $view_data['errors'] = [];
                $view_data['import_results'] = null;

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
                        $file_tmp_path = $_FILES['csv_file']['tmp_name'];
                        $file_name = $_FILES['csv_file']['name'];
                        $file_size = $_FILES['csv_file']['size'];
                        $file_type = $_FILES['csv_file']['type'];
                        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        $allowed_extension = 'csv';
                        $max_file_size = 5 * 1024 * 1024; // 5MB

                        if ($file_extension !== $allowed_extension) {
                            $view_data['errors']['general'] = 'Netinkamas failo formatas. Prašome įkelti .csv failą.';
                        } elseif ($file_size > $max_file_size) {
                            $view_data['errors']['general'] = 'Failas per didelis. Maksimalus dydis 5MB.';
                        } else {
                            if (($handle = fopen($file_tmp_path, "r")) !== false) {
                                $header = fgetcsv($handle, 0, ",");
                                $expected_headers_map = [
                                    'Pavadinimas' => 'pavadinimas', 'ImonesKodas' => 'imones_kodas', 'PVMKodas' => 'pvm_kodas',
                                    'VadovasVardasPavarde' => 'vadovas_vardas_pavarde', 'Tinklalapis' => 'tinklalapis', 'DarboLaikas' => 'darbo_laikas',
                                    'AdresasSalis' => 'adresas_salis', 'AdresasMiestas' => 'adresas_miestas', 'AdresasGatve' => 'adresas_gatve',
                                    'AdresasPastoKodas' => 'adresas_pasto_kodas', 'Telefonas' => 'telefonas', 'ElPastas' => 'el_pastas',
                                    'KontaktinisAsmuo' => 'kontaktinis_asmuo', 'BankoPavadinimas' => 'banko_pavadinimas', 'BankoSaskaita' => 'banko_saskaita',
                                    'Pastabos' => 'pastabos'
                                ];
                                if (!$header || !in_array('Pavadinimas', $header, true) || !in_array('ImonesKodas', $header, true)) {
                                    $view_data['errors']['general'] = 'CSV failo antraštė neteisinga arba trūksta būtinų stulpelių (Pavadinimas, ImonesKodas).';
                                } else {
                                    $import_stats = ['success_count' => 0, 'error_count' => 0, 'error_details' => []];
                                    $row_number = 1;
                                    while (($data_row = fgetcsv($handle, 0, ",")) !== false) {
                                        $row_number++;
                                        $company_data_to_insert = [];
                                        foreach ($header as $index => $col_name) {
                                            if (isset($expected_headers_map[$col_name]) && isset($data_row[$index])) {
                                                $company_data_to_insert[$expected_headers_map[$col_name]] = trim($data_row[$index]);
                                            }
                                        }
                                        if (empty($company_data_to_insert['pavadinimas']) || empty($company_data_to_insert['imones_kodas'])) {
                                            $import_stats['error_count']++;
                                            $import_stats['error_details'][] = ['row' => $row_number, 'message' => 'Trūksta pavadinimo arba įmonės kodo.', 'data' => $data_row];
                                            continue;
                                        }
                                        if ($companyManager->createCompany($company_data_to_insert)) {
                                            $import_stats['success_count']++;
                                        } else {
                                            $import_stats['error_count']++;
                                            $import_stats['error_details'][] = ['row' => $row_number, 'message' => 'Nepavyko įrašyti į DB.', 'data' => $data_row];
                                        }
                                    }
                                    fclose($handle);
                                    $view_data['import_results'] = $import_stats;
                                }
                            } else {
                                $view_data['errors']['general'] = 'Nepavyko nuskaityti CSV failo.';
                            }
                        }
                    } elseif (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                        $view_data['errors']['general'] = 'Klaida įkeliant failą. Klaidos kodas: ' . $_FILES['csv_file']['error'];
                    } else {
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            $view_data['errors']['general'] = 'Prašome pasirinkti CSV failą.';
                        }
                    }
                }
                $view_template = 'companies/import_form.php';
                break;

            case 'load_more_companies':
                // Temporary error reporting for debugging this specific action
                error_reporting(E_ALL);
                ini_set('display_errors', 1);

                // Crucial: Set content type header BEFORE any output
                // If any PHP error occurs before this and display_errors is on, it could break JSON output.
                // For robust production, consider a global error handler that formats errors as JSON for AJAX requests.
                header('Content-Type: application/json');

                if (!isset($_GET['ajax']) || $_GET['ajax'] !== '1') {
                    // error_log("Non-AJAX attempt to access load_more_companies");
                    // Ensure this error message is also JSON
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid request method. This endpoint is for AJAX requests only.']);
                    exit;
                }

                $requested_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                if ($requested_page <= 0) {
                    // It's an AJAX endpoint, client expects JSON.
                    http_response_code(400); // Bad Request
                    echo json_encode(['error' => 'Invalid page number.']);
                    exit;
                }
                
                $search_query_ajax = $_GET['search_query'] ?? null;
                $companies_per_page = 100; 
                
                // All variables for the response
                $response_data = [
                    'companies' => [],
                    'isAdmin' => $auth->isAdmin(), // Get isAdmin status regardless of company results
                    'logos_dir_url' => LOGO_UPLOAD_DIR_PUBLIC,
                    'search_query_active' => $search_query_ajax, // Reflect back the search query used
                    'page' => $requested_page // Reflect back the page number processed
                ];

                try {
                    $companies = $companyManager->getAllCompanies($search_query_ajax, $requested_page, $companies_per_page);
                    $response_data['companies'] = $companies;
                } catch (Exception $e) {
                    // Log the actual error for server admin
                    error_log("Error in load_more_companies: " . $e->getMessage());
                    // Provide a generic error in JSON format to the client
                    http_response_code(500); // Internal Server Error
                    echo json_encode(['error' => 'An error occurred while fetching company data.']);
                    exit;
                }
                
                // No stray echos or HTML output before this.
                echo json_encode($response_data);
                exit;

            case 'search_suggestions':
                header('Content-Type: application/json');
                $query = $_GET['query'] ?? '';
                if (empty($query)) {
                    echo json_encode([]);
                    exit;
                }
                $suggestions = $companyManager->searchSuggestions($query);
                echo json_encode($suggestions);
                exit;

            default:
                $search_query = $_GET['search_query'] ?? null;
                // Fetch page 1, limit 100 for initial company list view
                $view_data['companies'] = $companyManager->getAllCompanies($search_query, 1, 100);
                if ($search_query) {
                    $view_data['search_query_active'] = $search_query;
                }
                $view_template = 'companies/index.php';
                break;
        }
        break;

    case 'admin':
// Note: The original diff had two separate hunks. I'm combining the logic from the second hunk (which was identical to the first in terms of search/replace target) 
// into the first one for clarity, as the tool expects one search/replace per block.
// The original second SEARCH block was identical to the first one.
// The original second REPLACE block was also effectively the same change.
// This means the diff tool might have an issue if the same block appears twice.
// I'm providing a single, consolidated change for `case 'load_more_companies':`
        $auth->requireAdmin();
        $admin_action = $action ?? 'dashboard';

        switch ($admin_action) {
            case 'generate_sitemap':
                $auth->requireAdmin();
                $sitemapGenerator = new SitemapGenerator($db, SITE_BASE_URL);
                if ($sitemapGenerator->generate()) {
                    set_flash_message('success_message', 'Sitemap failai (indeksas ir dalys) sėkmingai sugeneruoti!');
                } else {
                    set_flash_message('error_message', 'Klaida generuojant sitemap failus.');
                }
                redirect('admin', 'users');
                break;
            case 'users':
                $view_data['users'] = $auth->getAllUsers();
                $view_template = 'admin/users_list.php';
                break;
                // case 'dashboard':
            default:
                // For now, if no specific admin action, or it's 'dashboard',
                // redirect to home or show a simple admin dashboard.
                // $view_template = 'admin/dashboard.php';
                // Decided to redirect to home if no specific admin action is matched for now.
                set_flash_message('info_message', 'Pasirinkite veiksmą administratoriaus skydelyje.');
                redirect('home'); // Or 'admin/dashboard' if you create that page.
                break;
        }
        break;

    default:
        http_response_code(404);
        $view_data['error_code'] = 404;
        $view_data['error_message'] = "Atsiprašome, ieškomas puslapis nerastas.";
        redirect('home');
        break;
}

$view_data['auth'] = $auth;

include __DIR__ . '/../templates/layout/header.php';
include __DIR__ . '/../templates/' . $view_template;
include __DIR__ . '/../templates/layout/footer.php';
