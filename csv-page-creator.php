<?php

/**
 * Plugin Name: CSV Page Creator
 * Description: Upload a CSV file with titles and descriptions to create WordPress pages in draft mode.
 * Version: 1.0.3
 * Author: Seejan
 * Requires PHP: 7.4
 * Text Domain: csv-page-creator
 *
 * @category WordPress_Plugin
 * @package  CSVPageCreator
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/includes/AdminPage.php';
require_once __DIR__ . '/includes/CSVProcessor.php';

/**
 * Main plugin class for CSV Page Creator.
 *
 * Initializes the plugin functionality and coordinates between components.
 *
 * @category WordPress_Plugin
 * @package  CSVPageCreator
 */
final class CSVPageCreator
{
    /**
     * Constructor - Initialize the plugin.
     *
     * Sets up the admin page functionality when the plugin is instantiated.
     *
     * @return void
     */
    public function __construct()
    {
        // Initialize admin page
        new \CSVPageCreator\AdminPage();
    }
}

new CSVPageCreator();
