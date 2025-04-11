<?php
/*
Plugin Name: Ticket Tailor Upcoming Events Feed
Description: Fetches and displays a simplified array of Ticket Tailor event data with API key settings.
Version: 1.1
Author: AY Studio
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if (!function_exists('format_pretty_date')) {
    function format_pretty_date($iso) {
        $date = new DateTime($iso);
        $day = (int)$date->format('j');
        $suffix = 'th';
        if (!in_array(($day % 100), [11,12,13])) {
            switch ($day % 10) {
                case 1: $suffix = 'st'; break;
                case 2: $suffix = 'nd'; break;
                case 3: $suffix = 'rd'; break;
            }
        }
        return $day . $suffix . ' ' .
            $date->format("F") . " '" .
            $date->format("y") . ' - ' .
            $date->format("H:i");
    }
}

// Add settings link on plugin page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $settings_link = '<a href="options-general.php?page=ay_ticket_tailor_settings">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
});

// Enqueue plugin styles
add_action('wp_enqueue_scripts', function () {
    wp_register_style('ticket-tailor-style', plugins_url('style.css', __FILE__));
    wp_enqueue_style('ticket-tailor-style');
});

// Register settings menu
add_action('admin_menu', function() {
    add_options_page('Ticket Tailor Settings', 'Ticket Tailor', 'manage_options', 'ay_ticket_tailor_settings', function() {
        ?>
        <div class="wrap">
            <h1>Ticket Tailor API Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('ay_ticket_tailor_settings_group');
                do_settings_sections('ay_ticket_tailor_settings_group');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Ticket Tailor API Key</th>
                        <td>
                            <input type="text" name="ay_ticket_tailor_api_key" value="<?php echo esc_attr(get_option('ay_ticket_tailor_api_key')); ?>" size="60"/>
                            <br /><small><em>Note: You must have Events read access when creating the API Key.</em></small>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    });
});

add_action('admin_init', 'ay_ticket_tailor_register_settings');
function ay_ticket_tailor_register_settings() {
    $setting_args = array(
        'type'              => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default'           => '',
    );

    register_setting(
        'ay_ticket_tailor_settings_group',
        'ay_ticket_tailor_api_key',
        $setting_args
    );
}

add_shortcode('ticket_tailor_events', function() {
    $apiKey = get_option('ay_ticket_tailor_api_key');
    if (!$apiKey) return '<p>Please set your Ticket Tailor API key in the plugin settings.</p>';

    $encodedKey = base64_encode($apiKey);
    $url = 'https://api.tickettailor.com/v1/events?status=published&start_at.gte=' . time();
    $args = array(
        'headers' => array(
            'Accept' => 'application/json',
            'Authorization' => 'Basic ' . $encodedKey,
        ),
        'timeout' => 15,
        'redirection' => 5,
        'httpversion' => '1.1',
    );

    $response = wp_remote_get($url, $args);

    if (is_wp_error($response)) {
        return '<p>Error fetching events: ' . esc_html($response->get_error_message()) . '</p>';
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (!is_array($data) || empty($data['data'])) {
        return '<p>No events found.</p>';
    }
    $events = [];
    ?>
    <div class="tt-events-grid">
        <?php
        $events = [];

        // STEP 1: Collect all events
        foreach ($data['data'] as $event) {
            $events[] = [
                'id' => $event['id'] ?? null,
                'name' => $event['name'] ?? null,
                'call_to_action' => $event['call_to_action'] ?? null,
                'checkout_url' => $event['checkout_url'] ?? null,
                'start' => $event['start']['iso'] ?? null,
                'end' => $event['end']['iso'] ?? null,
                'thumbnail' => $event['images']['thumbnail'] ?? null,
                'header' => $event['images']['header'] ?? null,
                'status' => $event['status'] ?? null,
                'tickets_available' => $event['tickets_available'] ?? null,
                'url' => $event['url'] ?? null,
                'venue' => $event['venue'] ?? null,
            ];
        }

        // STEP 2: Sort all events by start date (ascending)
        usort($events, function ($a, $b) {
            return strtotime($a['start']) <=> strtotime($b['start']);
        });

        // STEP 3: Output HTML for each sorted event
        foreach ($events as $event): ?>
            <div class="tt-event-card">
                <a href="<?php echo esc_url($event['url']); ?>" target="_blank">
                    <figure class="latest__thumb card__img__hover">
                        <img src="<?php echo esc_url($event['header']); ?>" alt="<?php echo esc_attr($event['name']); ?>">
                    </figure>
                </a>
                <div class="latest__cont">
                    <h3 class="latest__ttl">
                        <a href="<?php echo esc_url($event['url']); ?>" target="_blank"><?php echo esc_html($event['name']); ?></a>
                    </h3>
                    <div class="date__row">
                    <span class="lbl">
                        From <?php echo esc_html(format_pretty_date($event['start'])); ?> to <?php echo esc_html(format_pretty_date($event['end'])); ?>
                    </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php
    return;
});