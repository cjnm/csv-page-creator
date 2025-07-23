<?php

/**
 * Admin page functionality for CSV Page Creator plugin.
 *
 * This file contains the AdminPage class which handles all WordPress admin
 * interface functionality including menu registration, file uploads, and
 * CSV processing coordination.
 *
 * @category WordPress_Plugin
 * @package  CSVPageCreator
 */

namespace CSVPageCreator;

defined('ABSPATH') || exit;

/**
 * Handles the admin page functionality for CSV Page Creator.
 *
 * This class manages the WordPress admin interface, including menu registration,
 * file upload handling, and CSV processing coordination.
 *
 * @category WordPress_Plugin
 * @package  CSVPageCreator
 */
class AdminPage
{
    /**
     * Constructor - Initialize admin page hooks.
     *
     * Sets up WordPress action hooks for menu registration, CSV upload handling,
     * and admin asset enqueuing.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('admin_menu', [$this, 'registerMenu']);
        add_action('admin_post_upload_csv', [$this, 'handleCsvUpload']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    /**
     * Register the admin menu page.
     *
     * Adds the CSV Page Creator menu item to the WordPress Tools menu.
     *
     * @return void
     */
    public function registerMenu(): void
    {
        add_menu_page(
            __('CSV Page Creator', 'csv-page-creator'),
            __('CSV Page Creator', 'csv-page-creator'),
            'manage_options',
            'csv-page-creator',
            [$this, 'renderAdminPage']
        );
    }

    /**
     * Enqueue admin assets for the CSV Page Creator page.
     *
     * @param  string $hook The current admin page hook.
     * @return void
     */
    public function enqueueAssets(string $hook): void
    {
        if ('tools_page_csv-page-creator' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'csv-page-creator-admin-style',
            plugin_dir_url(__FILE__) . 'assets/css/admin-style.css',
            [],
            '1.0.0'
        );
    }

    /**
     * Render the admin page content.
     *
     * Displays the CSV upload form and handles success/error messages.
     *
     * @return void
     */
    public function renderAdminPage(): void
    {
        $success = filter_input(INPUT_GET, 'success', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $error = filter_input(INPUT_GET, 'error', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        include __DIR__ . '/../templates/admin-page.php';
    }

    /**
     * Handle CSV file upload and processing.
     *
     * Validates the uploaded file, processes the CSV data, and creates
     * WordPress pages from the CSV content.
     *
     * @return void
     */
    public function handleCsvUpload(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to access this page.', 'csv-page-creator'));
        }

        check_admin_referer('csv_upload_nonce', 'csv_nonce');

        if (empty($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->redirectWithMessage('error', __('File upload failed.', 'csv-page-creator'));
        }

        $file = $_FILES['csv_file'];

        // Check file extension
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $mime = $file['type'] ?? '';
        if (strtolower($ext) !== 'csv' || ($mime !== 'text/csv' && $mime !== 'application/vnd.ms-excel')) {
            $this->redirectWithMessage('error', __('Please upload a valid CSV file.', 'csv-page-creator'));
        }

        // Process CSV
        $processor = new CSVProcessor();
        $createdCount = $processor->process($file['tmp_name']);

        if ($createdCount === 'missing_columns') {
            $this->redirectWithMessage('error', __('CSV must have "title" and "description" columns.', 'csv-page-creator'));
        } elseif ($createdCount === false) {
            $this->redirectWithMessage('error', __('Error processing CSV file.', 'csv-page-creator'));
        } elseif ($createdCount === 0) {
            $this->redirectWithMessage('error', __('No valid rows found in CSV.', 'csv-page-creator'));
        } else {
            $this->redirectWithMessage('success', sprintf(_n('%d page created.', '%d pages created.', $createdCount, 'csv-page-creator'), $createdCount));
        }
    }

    /**
     * Redirect with success or error message.
     *
     * Redirects the user back to the admin page with a success or error
     * message displayed as a WordPress admin notice.
     *
     * @param  string $type    The message type ('success' or 'error').
     * @param  string $message The message to display.
     * @return void
     */
    private function redirectWithMessage(string $type, string $message): void
    {
        $redirect_url = add_query_arg(
            [$type => rawurlencode($message)],
            admin_url('tools.php?page=csv-page-creator')
        );
        wp_safe_redirect($redirect_url);
        exit;
    }
}
