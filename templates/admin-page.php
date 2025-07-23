<?php
/**
 * Admin page template for CSV Page Creator.
 *
 * This template displays the CSV upload form and format requirements
 * for creating WordPress pages from CSV data.
 *
 * @category WordPress_Plugin
 * @package  CSVPageCreator
 */

defined('ABSPATH') || exit;
?>
<div class="wrap">
    <h1><?php esc_html_e('CSV Page Creator', 'csv-page-creator'); ?></h1>
    <p><?php esc_html_e('Upload a CSV file with columns: title and description.', 'csv-page-creator'); ?></p>

    <?php if (!empty($success)) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo wp_kses_post(urldecode($success)); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)) : ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo wp_kses_post(urldecode($error)); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
        <?php wp_nonce_field('csv_upload_nonce', 'csv_nonce'); ?>
        <input type="hidden" name="action" value="upload_csv">

        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="csv_file"><?php esc_html_e('CSV File', 'csv-page-creator'); ?></label>
                </th>
                <td>
                    <input type="file" name="csv_file" id="csv_file" accept=".csv" required>
                    <p class="description">
                        <?php esc_html_e("Select a CSV file with 'title' and 'description' columns.", 'csv-page-creator'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <?php submit_button(__('Upload and Create Pages', 'csv-page-creator')); ?>
    </form>

    <div class="csv-format-info">
        <h3><?php esc_html_e('CSV Format Requirements', 'csv-page-creator'); ?></h3>
        <p><?php esc_html_e('Your CSV file should have the following structure:', 'csv-page-creator'); ?></p>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('title', 'csv-page-creator'); ?></th>
                    <th><?php esc_html_e('description', 'csv-page-creator'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>About Us</td>
                    <td>Learn more about our company and mission</td>
                </tr>
                <tr>
                    <td>Services</td>
                    <td>Discover the services we offer to our clients</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
