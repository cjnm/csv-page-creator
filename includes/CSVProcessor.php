<?php

/**
 * CSV processing functionality for CSV Page Creator plugin.
 *
 * This file contains the CSVProcessor class which handles parsing CSV files
 * and creating WordPress pages from the CSV data.
 *
 * @category WordPress_Plugin
 * @package  CSVPageCreator
 */

namespace CSVPageCreator;

defined('ABSPATH') || exit;

/**
 * Processes CSV files to create WordPress pages.
 *
 * This class handles reading CSV files, validating the data structure,
 * and creating WordPress pages from the CSV content.
 *
 * @category WordPress_Plugin
 * @package  CSVPageCreator
 */
class CSVProcessor
{
    /**
     * Process the CSV file to create pages.
     *
     * @param  string $filePath Path to the CSV file.
     * @return int|false Number of pages created or false on failure.
     */
    public function process(string $filePath)
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return false;
        }

        $createdCount = 0;
        $header = null;
        $titleIndex = null;
        $descriptionIndex = null;

        while (($data = fgetcsv($handle, 0, ',')) !== false) {
            if ($header === null) {
                $header = array_map('strtolower', array_map('trim', $data));
                $titleIndex = array_search('title', $header);
                $descriptionIndex = array_search('description', $header);

                if ($titleIndex === false || $descriptionIndex === false) {
                    fclose($handle);
                    return 'missing_columns';
                }
                continue;
            }

            // Skip empty rows
            if (empty(array_filter($data))) {
                continue;
            }

            $title = $data[$titleIndex] ?? '';
            $description = $data[$descriptionIndex] ?? '';

            if (empty(trim($title))) {
                continue;
            }

            $postData = [
                'post_title'   => sanitize_text_field($title),
                'post_content' => wp_kses_post($description),
                'post_status'  => 'draft',
                'post_type'    => 'page',
                'post_author'  => get_current_user_id(),
            ];

            $postId = wp_insert_post($postData);
            if ($postId && !is_wp_error($postId)) {
                $createdCount++;
            }
        }

        fclose($handle);
        return $createdCount;
    }
}
