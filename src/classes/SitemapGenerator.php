<?php
// src/classes/SitemapGenerator.php

declare(strict_types=1);

class SitemapGenerator
{
    private Database $db;
    private string $site_base_url;

    public function __construct(Database $db, string $site_base_url)
    {
        $this->db = $db;
        $this->site_base_url = rtrim($site_base_url, '/'); // Ensure no trailing slash
    }

    public function generate(): bool
    {
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml_content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Helper function to add URL to sitemap
        $addUrl = function (string $loc, string $lastmod, string $changefreq = 'daily', string $priority = '0.8') use (&$xml_content) {
            $xml_content .= '  <url>' . PHP_EOL;
            $xml_content .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
            $xml_content .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
            $xml_content .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
            $xml_content .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
            $xml_content .= '  </url>' . PHP_EOL;
        };

        $current_date = date('Y-m-d');

        // 1. Homepage
        $addUrl($this->site_base_url . url('home'), $current_date, 'daily', '1.0');

        // 2. Login page
        $addUrl($this->site_base_url . url('login'), $current_date, 'monthly', '0.5');

        // 3. Register page
        $addUrl($this->site_base_url . url('register'), $current_date, 'monthly', '0.5');

        // 4. Company pages
        // Assuming Company class and method to get all company IDs or basic info
        // This part will need adjustment based on actual Company class methods
        try {
            $company_table = defined('TABLE_COMPANIES') ? TABLE_COMPANIES : 'imones'; // Or get from Company class
            $this->db->query("SELECT id FROM " . $company_table . " ORDER BY id ASC");
            $companies = $this->db->resultSet();

            if ($companies) {
                foreach ($companies as $company) {
                    $addUrl($this->site_base_url . url('companies', 'view', (int)$company['id']), $current_date, 'weekly', '0.7');
                }
            }
        } catch (Exception $e) {
            // Log error or handle - for now, we'll just skip company URLs if there's an issue
            error_log("SitemapGenerator: Failed to fetch companies: " . $e->getMessage());
        }

        $xml_content .= '</urlset>' . PHP_EOL;

        $sitemap_path = __DIR__ . '/../../public/sitemap.xml';

        if (file_put_contents($sitemap_path, $xml_content)) {
            return true;
        } else {
            error_log("SitemapGenerator: Failed to write sitemap.xml to " . $sitemap_path);
            return false;
        }
    }
}
