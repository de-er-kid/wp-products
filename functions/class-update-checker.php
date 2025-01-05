<?php

class WP_Products_Update_Checker {
    private $plugin_slug;
    private $repo_url;
    private $plugin_file;

    public function __construct($plugin_slug, $repo_url, $plugin_file) {
        $this->plugin_slug = $plugin_slug;
        $this->repo_url = $repo_url;
        $this->plugin_file = $plugin_file;

        // Register the hooks
        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 10, 3);
    }

    // Function to check for updates
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }

        // Fetch the latest release info from GitHub
        $response = wp_remote_get('https://api.github.com/repos/' . $this->repo_url . '/releases/latest');
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return $transient;
        }

        $release = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($release['tag_name'])) {
            return $transient;
        }

        $latest_version = str_replace('v', '', $release['tag_name']);
        $current_version = get_plugin_data($this->plugin_file)['Version'];

        if (version_compare($latest_version, $current_version, '>')) {
            $transient->response[$this->plugin_slug] = (object) [
                'slug'        => dirname($this->plugin_file),
                'plugin'      => $this->plugin_file,
                'new_version' => $latest_version,
                'package'     => $release['zipball_url'],
                'tested'      => '6.3', // Replace with the tested WordPress version.
                'requires'    => '5.2', // Replace with the minimum required WordPress version.
            ];
        }

        return $transient;
    }

    // Function to provide plugin details when clicked on "View Details"
    public function plugin_info($result, $action, $args) {
        if ($action !== 'plugin_information' || $args->slug !== dirname($this->plugin_file)) {
            return $result;
        }

        // Fetch release info from GitHub
        $response = wp_remote_get('https://api.github.com/repos/' . $this->repo_url . '/releases/latest');
        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return $result;
        }

        $release = json_decode(wp_remote_retrieve_body($response), true);
        if (empty($release['tag_name'])) {
            return $result;
        }

        // Populate plugin information
        $result = (object) [
            'name'        => 'WP Products',
            'slug'        => dirname($this->plugin_file),
            'version'     => str_replace('v', '', $release['tag_name']),
            'author'      => '<a href="mailto:sinan.postbox@gmail.com">Sinan</a>',
            'homepage'    => 'https://github.com/de-er-kid/wp-products',
            'download_link' => $release['zipball_url'],
            'requires'    => '5.2',
            'tested'      => '6.7.1',
            'sections'    => [
                'description' => '<p>A minimal plugin for product post types and eCommerce development.</p>',
                'changelog'   => !empty($release['body']) ? nl2br($release['body']) : 'No changelog available.',
            ],
        ];

        return $result;
    }
}
