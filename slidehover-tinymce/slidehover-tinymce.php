<?php
/**
 * Plugin Name: Slide Hover TinyMCE
 * Description: Pulsante TinyMCE per scegliere due immagini e generare lo shortcode [slidehover].
 * Version:     1.0.0
 * Author:      UMBY
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ---------- SHORTCODE ---------- */
add_shortcode( 'slidehover', 'sh_render' );
function sh_render( $atts ) {
    $atts = shortcode_atts( [
        'before' => 0,
        'after'  => 0,
        'width'  => 600,
        'height' => 400,
        'loop'   => 'no',
    ], $atts );

    $before = absint( $atts['before'] );
    $after  = absint( $atts['after'] );
    $w      = absint( $atts['width'] );
    $h      = absint( $atts['height'] );
    $loop   = ( strtolower( $atts['loop'] ) === 'yes' ) ? 'loop' : '';

    if ( ! $before || ! $after ) {
        return '';
    }

    $src_before = wp_get_attachment_image_url( $before, [ $w, $h ] );
    $src_after  = wp_get_attachment_image_url( $after,  [ $w, $h ] );

    if ( ! $src_before || ! $src_after ) {
        return '';
    }

    $uid = 'sh_' . uniqid();
    ob_start();
    ?>
    <figure class="slidehover <?php echo $loop; ?>" id="<?php echo esc_attr( $uid ); ?>" style="position:relative;width:<?php echo $w; ?>px;height:<?php echo $h; ?>px;overflow:hidden;margin:0;">
        <img src="<?php echo esc_url( $src_before ); ?>" alt="before">
        <img src="<?php echo esc_url( $src_after ); ?>"  alt="after">
    </figure>
    <?php
    return ob_get_clean();
}

/* ---------- FRONT-END CSS ---------- */
add_action( 'wp_enqueue_scripts', function () {
    wp_register_style( 'slidehover-css', plugins_url( 'css/slidehover-front.css', __FILE__ ), [], '1.0.0' );
    wp_enqueue_style( 'slidehover-css' );
} );

/* ---------- TINYMCE BUTTON ---------- */
add_action( 'admin_init', function () {
    if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
        return;
    }
    if ( get_user_option( 'rich_editing' ) === 'true' ) {
        add_filter( 'mce_external_plugins', 'sh_mce_plugin' );
        add_filter( 'mce_buttons', 'sh_mce_button' );
    }
} );

function sh_mce_plugin( $plugins ) {
    $plugins['slidehover'] = plugins_url( 'js/slidehover-tinymce.js', __FILE__ );
    return $plugins;
}
function sh_mce_button( $buttons ) {
    array_push( $buttons, 'slidehover' );
    return $buttons;

}
