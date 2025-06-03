<?php
// tests/test_company_pagination.php

declare(strict_types=1);

echo "Starting Company Pagination and Search Tests...\n";
echo "NOTE: These tests assume a populated database. Some tests might provide more meaningful results with specific test data.\n";
echo "For Test Case 2 (Search), specific company names are used; ensure they exist or adapt the test.\n\n";

// Adjust paths as needed if your directory structure is different
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/classes/Database.php';
require_once __DIR__ . '/../src/classes/Company.php'; // Make sure Company class doesn't use session-based Auth or other web-specific features directly in getAllCompanies

// Mockup or minimal version of TABLE_COMPANIES if not defined in a CLI context
if (!defined('TABLE_COMPANIES')) {
    define('TABLE_COMPANIES', 'imones_rekvizitai'); // Default table name used in Company class
}

// Error handling for database connection
try {
    $db = new Database();
} catch (Exception $e) {
    echo "CRITICAL ERROR: Could not connect to database. " . $e->getMessage() . "\n";
    exit(1);
}

$companyManager = new Company($db);

// Helper function for assertions
function assertCondition(bool $condition, string $testName, string $successMessage, string $failureMessage): void
{
    if ($condition) {
        echo "$testName: PASS - $successMessage\n";
    } else {
        echo "$testName: FAIL - $failureMessage\n";
    }
}

// --- Test Case 1: Basic Pagination ---
echo "\n--- Test Case 1: Basic Pagination ---\n";

// 1.1: Fetch page 1, limit 5
$page1_limit5 = $companyManager->getAllCompanies(null, 1, 5);
assertCondition(
    count($page1_limit5) <= 5,
    "Test 1.1 (Page 1, Limit 5 - Count)",
    "Returned " . count($page1_limit5) . " companies (expected <= 5).",
    "Expected <= 5 companies, Got " . count($page1_limit5) . "."
);

// 1.2: Fetch page 2, limit 5
$page2_limit5 = $companyManager->getAllCompanies(null, 2, 5);
assertCondition(
    count($page2_limit5) <= 5,
    "Test 1.2 (Page 2, Limit 5 - Count)",
    "Returned " . count($page2_limit5) . " companies (expected <= 5).",
    "Expected <= 5 companies, Got " . count($page2_limit5) . "."
);

// 1.3: Check for overlap between page 1 and page 2 (basic check)
if (count($page1_limit5) > 0 && count($page2_limit5) > 0) {
    $ids_page1 = array_column($page1_limit5, 'id');
    $ids_page2 = array_column($page2_limit5, 'id');
    $overlap = array_intersect($ids_page1, $ids_page2);
    assertCondition(
        empty($overlap),
        "Test 1.3 (Page 1 & 2 - No Overlap)",
        "No overlapping company IDs found between page 1 and page 2.",
        "Overlapping company IDs found: " . implode(', ', $overlap) . "."
    );
} else {
    echo "Test 1.3 (Page 1 & 2 - No Overlap): SKIPPED - Not enough data on one or both pages to check for overlap.\n";
}


// --- Test Case 2: Pagination with Search ---
echo "\n--- Test Case 2: Pagination with Search ---\n";
// NOTE: This test case is highly dependent on the actual data in your database.
// For robust testing, you would typically insert known test data before these tests
// and clean it up afterwards.
// Let's assume we're searching for a term that might exist.
// If you have specific companies, use their names. Example: "Maxima" or a test company.
$searchTerm = "TestCompany"; // <<<<<<< ADAPT THIS SEARCH TERM if needed
echo "INFO: Using search term '$searchTerm'. Adapt if this term yields no results in your DB.\n";

// 2.1: Search term, page 1, limit 3
$search_p1_limit3 = $companyManager->getAllCompanies($searchTerm, 1, 3);
$count_search_p1_limit3 = count($search_p1_limit3);
assertCondition(
    $count_search_p1_limit3 <= 3,
    "Test 2.1 (Search '$searchTerm', Page 1, Limit 3 - Count)",
    "Returned $count_search_p1_limit3 companies (expected <= 3).",
    "Expected <= 3 companies, Got $count_search_p1_limit3."
);
// Optional: Check if results actually contain the search term (simplified check)
if ($count_search_p1_limit3 > 0) {
    $firstCompany = $search_p1_limit3[0];
    assertCondition(
        stripos($firstCompany['pavadinimas'], $searchTerm) !== false || stripos($firstCompany['imones_kodas'], $searchTerm) !== false,
        "Test 2.1.1 (Search '$searchTerm', Page 1, Limit 3 - Content Check)",
        "First result '{$firstCompany['pavadinimas']}' seems to match.",
        "First result '{$firstCompany['pavadinimas']}' might not match '$searchTerm'."
    );
}


// 2.2: Search term, page 2, limit 3
$search_p2_limit3 = $companyManager->getAllCompanies($searchTerm, 2, 3);
$count_search_p2_limit3 = count($search_p2_limit3);
assertCondition(
    $count_search_p2_limit3 <= 3,
    "Test 2.2 (Search '$searchTerm', Page 2, Limit 3 - Count)",
    "Returned $count_search_p2_limit3 companies (expected <= 3).",
    "Expected <= 3 companies, Got $count_search_p2_limit3."
);

// 2.3: Check for overlap between search page 1 and page 2
if ($count_search_p1_limit3 > 0 && $count_search_p2_limit3 > 0) {
    $s_ids_p1 = array_column($search_p1_limit3, 'id');
    $s_ids_p2 = array_column($search_p2_limit3, 'id');
    $s_overlap = array_intersect($s_ids_p1, $s_ids_p2);
    assertCondition(
        empty($s_overlap),
        "Test 2.3 (Search '$searchTerm', Page 1 & 2 - No Overlap)",
        "No overlapping company IDs found between search page 1 and page 2.",
        "Overlapping company IDs found for search '$searchTerm': " . implode(', ', $s_overlap) . "."
    );
} else {
    echo "Test 2.3 (Search '$searchTerm', Page 1 & 2 - No Overlap): SKIPPED - Not enough data on one or both search pages to check for overlap.\n";
}

// --- Test Case 3: Edge Cases ---
echo "\n--- Test Case 3: Edge Cases ---\n";

// 3.1: Fetch a page that should have no results
$page_far_limit100 = $companyManager->getAllCompanies(null, 99999, 100); // Assuming 99999 is an out-of-bounds page
assertCondition(
    empty($page_far_limit100),
    "Test 3.1 (Page 99999, Limit 100 - Empty Result)",
    "Returned an empty array as expected.",
    "Expected an empty array, Got " . count($page_far_limit100) . " companies."
);

// 3.2: Fetch with limit 0 (or 1 if 0 is not supported/intended)
// The Company class's getAllCompanies method uses PDO::PARAM_INT for limit,
// and SQL LIMIT 0 usually returns an empty set. Let's test with limit 1 as a practical minimum.
$page1_limit1 = $companyManager->getAllCompanies(null, 1, 1);
assertCondition(
    count($page1_limit1) <= 1,
    "Test 3.2 (Page 1, Limit 1 - Count)",
    "Returned " . count($page1_limit1) . " company (expected <= 1).",
    "Expected <= 1 company, Got " . count($page1_limit1) . "."
);

// Test with limit 0 if it's meant to be handled as "no results"
$page1_limit0 = $companyManager->getAllCompanies(null, 1, 0);
assertCondition(
    empty($page1_limit0),
    "Test 3.3 (Page 1, Limit 0 - Empty Result)",
    "Returned an empty array as expected for limit 0.",
    "Expected an empty array for limit 0, Got " . count($page1_limit0) . " companies."
);


echo "\n\nAll tests completed.\n";
echo "Review the output above for PASS/FAIL status of each test.\n";
echo "Remember that meaningful results depend on the database content and the chosen search terms.\n";

?>
