<?php
/**
 * Plugin Name: Slide Hover TinyMCE
 * Description: Pulsante TinyMCE con dialog, anteprima, ridimensionamento, effetti, caption.
 * Version: 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ---------- SHORTCODE ---------- */
add_shortcode( 'slidehover', 'sh_render' );
function sh_render( $atts, $content = null ) {
    $a = shortcode_atts( [
        'before' => 0,
        'after'  => 0,
        'width'  => 600,
        'height' => 400,
        'effect' => 'fade',
        'align'  => 'none',
        'dur'    => 0.5,
    ], $atts );

    $bid = absint( $a['before'] );
    $aid = absint( $a['after'] );
    $w   = absint( $a['width'] );
    $h   = absint( $a['height'] );
    $fx  = sanitize_html_class( $a['effect'] );
    $al  = sanitize_html_class( $a['align'] );
    $dur = floatval( $a['dur'] );
    $cap = trim( $content );

    if ( ! $bid || ! $aid ) return '';

    $src1 = wp_get_attachment_image_url( $bid, [ $w, $h ] );
    $src2 = wp_get_attachment_image_url( $aid, [ $w, $h ] );
    if ( ! $src1 || ! $src2 ) return '';

    $uid = 'sh_' . uniqid();
    $cls = "wp-caption slidehover-$fx align$al";

   /* shortcode: aggiungiamo le variabili CSS per il trick fluido */
$out  = "<figure id='$uid' class='$cls' style='--w:$w;--h:$h;width:100%;max-width:{$w}px'>";
$out .= "<div class='sh-wrap' style='position:relative;width:100%;height:0;padding-bottom:calc({$h}/{$w}*100%);overflow:hidden'>";
    $out .= "<img src='" . esc_url( $src1 ) . "' class='sh-before' alt=''>";
    $out .= "<img src='" . esc_url( $src2 ) . "' class='sh-after' alt=''>";
    $out .= "</div>";
    if ( $cap ) $out .= "<figcaption class='wp-caption-text'>" . esc_html( $cap ) . "</figcaption>";
    $out .= "</figure>";

    return $out;
}

/* ---------- FRONT CSS ---------- */
add_action( 'wp_enqueue_scripts', function () {
    wp_register_style( 'sh-front', plugins_url( 'front.css', __FILE__ ), [], '1.2.0' );
    wp_enqueue_style( 'sh-front' );
} );

/* ---------- TINYMCE BUTTON ---------- */
add_action( 'admin_init', function () {
    if ( ! current_user_can( 'edit_posts' ) ) return;
    add_filter( 'mce_external_plugins', function ( $plugs ) {
        $plugs['slidehover'] = plugins_url( 'plugin.js', __FILE__ );
        return $plugs;
    } );
    add_filter( 'mce_buttons', function ( $btns ) {
        $btns[] = 'slidehover';
        return $btns;
    } );
} );

/* ---------- DIALOG HTML ---------- */
add_action( 'admin_footer-post.php', 'sh_dialog' );
add_action( 'admin_footer-post-new.php', 'sh_dialog' );
function sh_dialog() {
    wp_enqueue_media();
    ?>
    <div id="sh-dialog" style="display:none">
        <div class="sh-form">
            <p>
                <button id="sh-pick-before" class="button">Scegli “prima”</button>
                <img id="sh-prev-before" style="max-width:60px;vertical-align:middle;margin-left:4px">
                <input type="hidden" id="sh-id-before">
            </p>
            <p>
                <button id="sh-pick-after"  class="button">Scegli “dopo”</button>
                <img id="sh-prev-after"  style="max-width:60px;vertical-align:middle;margin-left:4px">
                <input type="hidden" id="sh-id-after">
            </p>
            <p>
                <label>W <input type="number" id="sh-render[width]"  value="600" min="100" max="2000" style="width:70px"></label>
                <label>H <input type="number" id="sh-render[height]" value="400" min="100" max="2000" style="width:70px"></label>
            </p>
            <p>
                <label>Effetto
                    <select id="sh-effect">
                        <option value="fade">Fade</option>
                        <option value="cycle">Loop</option>
                        <option value="swipe">Swipe</option>
                        <option value="zoom">Zoom</option>
                    </select>
                </label>
            </p>
            <p>
                <label>Durata (s) <input type="number" id="sh-render[ur]" value="0.5" min="0.1" max="5" step="0.1" style="width:60px"></label>
            </p>
            <p>
                <label>Align
                    <select id="align">
                        <option value="none">None</option>
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                </label>
            </p>
            <p>
                <label>Caption<br><textarea id="sh-render[caption" rows="2" style="width:100%"></textarea></label>
            </p>
            <h4>Anteprima:</h4>
            <div id="sh-preview" style="width:300px;height:150px;border:1px solid #ccc;position:relative;overflow:hidden;background:#f7f7f7;">
                <img class="sh-prev-before" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover">
                <img class="sh-prev-after"  style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;opacity:0;transition:opacity .5s">
            </div>
        </div>
    </div>
    <?php
}