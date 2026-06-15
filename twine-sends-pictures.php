<?php
/**
 * Plugin Name: Twine Sends Pictures
 * Description: Crea endpoint custom per ricevere le foto dei giocatori
 * Version: 1.0
 * Author: Quota group
 */

if (!defined('ABSPATH')) {
    exit;
}

// gestisce l'upload delle foto scattate dai giocatori della GtA
// aggiunge un nuovo endpoint /custom/v1/upload

add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/upload', [
        'methods'  => 'POST',
        'callback' => 'handle_custom_upload',
        'permission_callback' => function () {
            return current_user_can('upload_files');
        },
    ]);
});

function handle_custom_upload(WP_REST_Request $request) {
    $file = $request->get_file_params()['file'];
    $caption = sanitize_text_field($request->get_param('caption'));
    $description = sanitize_text_field($request->get_param('description'));
    $post_id = intval($request->get_param('post'));

    if (!$file) {
        return new WP_Error('no_file', 'Nessun file fornito.', ['status' => 400]);
    }

    // Carica il file nella libreria Media
    $attachment_id = media_handle_upload('file', $post_id);

    if (is_wp_error($attachment_id)) {
        return $attachment_id;
    }

    // Aggiorna i metadati
    wp_update_post([
        'ID' => $attachment_id,
        'post_excerpt' => $caption, // Aggiorna la caption
        'post_content' => $description, // Aggiorna la description
    ]);

    return [
        'id' => $attachment_id,
        'url' => wp_get_attachment_url($attachment_id),
        'caption' => $caption,
        'description' => $description,
    ];
}
