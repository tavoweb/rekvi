<?php
// src/classes/SitemapGenerator.php

declare(strict_types=1);

class SitemapGenerator
{
    private Database $db;
    private string $site_base_url;
    private string $public_path;

    private const MAX_URLS_PER_SITEMAP = 45000;
    private const SITEMAP_INDEX_FILENAME = 'sitemap.xml';
    private const SITEMAP_PART_FILENAME_PREFIX = 'sitemap-pages-';

    public function __construct(Database $db, string $site_base_url)
    {
        $this->db = $db;
        $this->site_base_url = rtrim($site_base_url, '/');
        $this->public_path = __DIR__ . '/../../public/'; // Path to the public directory
    }

    private function _addUrlToXml(string &$xml_content, string $loc, string $lastmod, string $changefreq = 'daily', string $priority = '0.8'): void
    {
        $xml_content .= '  <url>' . PHP_EOL;
        $xml_content .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
        $xml_content .= '    <lastmod>' . $lastmod . '</lastmod>' . PHP_EOL;
        $xml_content .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $xml_content .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
        $xml_content .= '  </url>' . PHP_EOL;
    }

    private function _startSitemapPart(string &$xml_content): void
    {
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml_content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
    }

    private function _endSitemapPart(string &$xml_content): void
    {
        $xml_content .= '</urlset>' . PHP_EOL;
    }

    private function _writeSitemapFile(string $filename, string $content): bool
    {
        $filepath = $this->public_path . $filename;
        if (file_put_contents($filepath, $content)) {
            return true;
        } else {
            error_log("SitemapGenerator: Failed to write sitemap file to " . $filepath);
            return false;
        }
    }

    private function _generateSitemapIndex(array $sitemapPartFiles): bool
    {
        $xml_content = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml_content .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $current_date = date('Y-m-d');

        foreach ($sitemapPartFiles as $filename) {
            $xml_content .= '  <sitemap>' . PHP_EOL;
            $xml_content .= '    <loc>' . htmlspecialchars($this->site_base_url . '/' . $filename) . '</loc>' . PHP_EOL;
            $xml_content .= '    <lastmod>' . $current_date . '</lastmod>' . PHP_EOL;
            $xml_content .= '  </sitemap>' . PHP_EOL;
        }

        $xml_content .= '</sitemapindex>' . PHP_EOL;
        return $this->_writeSitemapFile(self::SITEMAP_INDEX_FILENAME, $xml_content);
    }

    // Method to delete old sitemap files
    private function _deleteOldSitemapFiles(): void
    {
        // Delete main sitemap index
        $index_file = $this->public_path . self::SITEMAP_INDEX_FILENAME;
        if (file_exists($index_file)) {
            @unlink($index_file);
        }

        // Delete sitemap parts
        $part_files_pattern = $this->public_path . self::SITEMAP_PART_FILENAME_PREFIX . '*.xml';
        $existing_parts = glob($part_files_pattern);
        if ($existing_parts) {
            foreach ($existing_parts as $file) {
                @unlink($file);
            }
        }
    }

    public function generate(): bool
    {
        $this->_deleteOldSitemapFiles(); // Delete old files first

        $all_urls = [];
        $current_date = date('Y-m-d');

        // 1. Homepage
        $all_urls[] = ['loc' => $this->site_base_url . url('home'), 'lastmod' => $current_date, 'changefreq' => 'daily', 'priority' => '1.0'];
        // 2. Login page
        $all_urls[] = ['loc' => $this->site_base_url . url('login'), 'lastmod' => $current_date, 'changefreq' => 'monthly', 'priority' => '0.3'];
        // 3. Register page
        $all_urls[] = ['loc' => $this->site_base_url . url('register'), 'lastmod' => $current_date, 'changefreq' => 'monthly', 'priority' => '0.3'];

        // 4. Company pages
        try {
            $company_table = defined('TABLE_COMPANIES') ? TABLE_COMPANIES : 'imones_rekvizitai';
            $this->db->query("SELECT id FROM " . $company_table . " ORDER BY id ASC");
            $companies = $this->db->resultSet();

            if ($companies) {
                foreach ($companies as $company) {
                    $all_urls[] = ['loc' => $this->site_base_url . url('companies', 'view', (int)$company['id']), 'lastmod' => $current_date, 'changefreq' => 'weekly', 'priority' => '0.7'];
                }
            }
        } catch (Exception $e) {
            error_log("SitemapGenerator: Failed to fetch companies: " . $e->getMessage());
            // Continue without company URLs if DB fails
        }

        if (empty($all_urls)) {
            error_log("SitemapGenerator: No URLs to add to sitemap.");
            // Potentially generate an empty index or just return false
            return $this->_generateSitemapIndex([]); // Generate an empty index
        }

        $sitemapPartFiles = [];
        $sitemapPartCounter = 1;
        $urlCounterInCurrentPart = 0;
        $currentSitemapContent = '';

        $this->_startSitemapPart($currentSitemapContent);

        foreach ($all_urls as $url_data) {
            if ($urlCounterInCurrentPart >= self::MAX_URLS_PER_SITEMAP) {
                $this->_endSitemapPart($currentSitemapContent);
                $partFilename = self::SITEMAP_PART_FILENAME_PREFIX . $sitemapPartCounter . '.xml';
                if (!$this->_writeSitemapFile($partFilename, $currentSitemapContent)) {
                    return false; // Failed to write a part file
                }
                $sitemapPartFiles[] = $partFilename;

                $sitemapPartCounter++;
                $urlCounterInCurrentPart = 0;
                $this->_startSitemapPart($currentSitemapContent);
            }

            $this->_addUrlToXml($currentSitemapContent, $url_data['loc'], $url_data['lastmod'], $url_data['changefreq'] ?? 'daily', $url_data['priority'] ?? '0.8');
            $urlCounterInCurrentPart++;
        }

        // Write the last sitemap part file if it has content
        if ($urlCounterInCurrentPart > 0) {
            $this->_endSitemapPart($currentSitemapContent);
            $partFilename = self::SITEMAP_PART_FILENAME_PREFIX . $sitemapPartCounter . '.xml';
            if (!$this->_writeSitemapFile($partFilename, $currentSitemapContent)) {
                return false; // Failed to write the last part file
            }
            $sitemapPartFiles[] = $partFilename;
        }

        // If no part files were generated (e.g. very few URLs, less than MAX_URLS_PER_SITEMAP, but still some URLs)
        // This case is handled by the logic above, as the first part file would have been created.
        // If $sitemapPartFiles is empty and $all_urls was not, it implies an error or very few URLs.
        // The check for $urlCounterInCurrentPart > 0 handles this.

        return $this->_generateSitemapIndex($sitemapPartFiles);
    }
}
