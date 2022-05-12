<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */


add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');
function woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="product_custom_field">';
    
    if($custom_time_date =  get_post_meta(get_the_ID(), '_date', true )){
        $datetime_input = $custom_time_date;
    }else{
        $datetime_input = strftime('%Y-%m-%dT%H:%M:%S', strtotime(current_time( 'Y-m-d H:i:s' )));
    }
    
 
    woocommerce_wp_text_input(
        array(
            'id' => '_date',
            'label' => __('Date', 'woocommerce'),
            'type' => 'datetime-local',
            'value' => $datetime_input,
            //'class' => 'short date-picker',
        )
    );

    
    woocommerce_wp_select(
        array(
            'id' => '_custom_product_select',
            'label' => __('Custom Product Select Field', 'woocommerce'),
            'description' => __( 'Podaj stan plyty.', 'woocommerce' ),
            'desc_tip'    => true,
            'options'     => array(
                ''        => __( 'Select', 'woocommerce' ),
                'rare'    => __('rare', 'woocommerce' ),
                'frequent' => __('frequent', 'woocommerce' ),
                'unusual' => __('unusual', 'woocommerce' ),
            )
        )
    );

    echo '</div>';

$image_id =  get_post_meta(get_the_ID(), 'custom_image_id', true );

if( intval( $image_id ) > 0 ) {
   
    $image = wp_get_attachment_image( $image_id, 'medium', false, array( 'id' => 'custom-preview-image' ) );
} else {

    $image = '<img id="custom-preview-image" src="/wp-content/uploads/woocommerce-placeholder-150x150.png" />';
}




//    $post_url = admin_url('post.php');
echo $image; ?>
<input type="hidden" name="custom_image_id" id="custom_image_id" value="<?php echo esc_attr( $image_id ); ?>" class="regular-text" />
    <input type="button" id="remove_img" class="button-info" value="Удалить картинку">
    <br>
    <input type='button' class="button-primary" value="<?php esc_attr_e( 'Select a image', 'mytextdomain' ); ?>" id="custom_media_manager"/>
<br>

    <input type="button" id="clear_custom_fields" value="Clear custom fields">

    <script src="<?= get_template_directory_uri().'/assets/js/custom.js' ?>"></script>

    <?php

}



add_action( 'wp_ajax_custom_get_image', 'custom_get_image'   );
function custom_get_image() {
    if(isset($_GET['id']) ){
        $image = wp_get_attachment_image( filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT ), 'medium', false, array( 'id' => 'custom-preview-image' ) );
        $data = array(
            'image'    => $image,
        );
        wp_send_json_success( $data );
    } else {
        wp_send_json_error();
    }
}

add_action('wp_ajax_delete_image', 'delete_image');
function delete_image(){
    if(isset($_GET['id']) ){

        delete_post_meta( $_GET['postid'], 'custom_image_id', $_GET['id'] );
        $data = array(
            'image'    => $_GET['postid'],
        );
        wp_send_json_success( $data );
    } else {
        wp_send_json_error();
    }
}


function woocommerce_product_custom_fields_save($post_id)
{
    
    // Custom Product Input Field
    $woocommerce_custom_procut_select = $_POST['_custom_product_select'];
    if (!empty($woocommerce_custom_procut_select))
        update_post_meta($post_id, '_custom_product_select', esc_html($woocommerce_custom_procut_select));
    $woocommerce_custom_procut_image_id = $_POST['custom_image_id'];
    if (!empty($woocommerce_custom_procut_image_id))
        update_post_meta($post_id, 'custom_image_id', esc_html($woocommerce_custom_procut_image_id));
    $woocommerce_custom_procut_date = $_POST['_date'];
    if (!empty($woocommerce_custom_procut_date))
        update_post_meta($post_id, '_date', esc_html($woocommerce_custom_procut_date));

//    echo "<pre>"; print_r($_POST); echo "</pre>"; exit();
}


add_filter( 'manage_edit-product_columns', 'add_columns');

function add_columns( $my_columns ) {


    $preview = array( 'thumbnail' => '' );

 
    $my_columns = array_slice( $my_columns, 0, 1, true ) + $preview + array_slice( $my_columns, 1, NULL, true );

    return $my_columns;

}
add_action( 'admin_head', function() {

    echo '<style>
	#thumbnail{
		width: 58px; 
	}
	</style>';

} );


add_action( 'manage_posts_custom_column', 'col_sine_re', 25, 3 );
function col_sine_re($column_name, $post_id) {

    $width = (int) 35;
    $height = (int) 35;

    if ( 'thumbnail' == $column_name ) {

        $thumbnail_id = get_post_meta( $post_id, 'custom_image_id', true );
      
        $attachments = get_children( array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
        if ($thumbnail_id)
            $thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
        elseif ($attachments) {
            foreach ( $attachments as $attachment_id => $attachment ) {
                $thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true );
            }
        }
        if ( isset($thumb) && $thumb ) {
            echo $thumb;
        } else {
            echo __('None');
        }
    }
}

add_filter( 'manage_edit-product_columns', 'remove_columns', 10, 1 );
function remove_columns( $columns ) {
    unset($columns['thumb']);
    return $columns;
}



add_action('save_post', 'save_post_ajax');
function save_post_ajax( $post_id )
{

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;

    if ($_POST['post_type'] == 'product')
    {

        if (isset($_POST['save_post_ajax']) && $_POST['save_post_ajax'] == TRUE)
        {
            header('Content-type: application/json');
            echo json_encode(array('success' => true));

            exit;
        }
    }
}


function post_type_xhr()
{
    global $post;

    if(get_current_screen()->action == 'add'){
        $postaction = 'post-new';
    }else{
        $postaction = 'post';
    }
    # Only for one post type.
    if ($post->post_type == 'product')
    {
        # The url for the js file we created above
        $url = get_template_directory_uri().'/assets/js/customsavepost.js';

        # Register and enqueue the script, dependent on jquery
        wp_register_script( 'custom_script', $url, array('jquery') );
        wp_enqueue_script( 'custom_script' );

        # Localize our variables for use in our js script
        wp_localize_script( 'custom_script', 'ajax_object', array(
            'post_id' => $post_id,
            'post_url' => admin_url($postaction.'.php'),
        ) );
    }
}

add_action('admin_head-post.php', 'post_type_xhr');
add_action('admin_head-post-new.php', 'post_type_xhr');


function register_javascript() {
    wp_enqueue_media();
    wp_localize_script( 'ajax-script', 'ajax_object',
        array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_enqueue_scripts', 'register_javascript', 100);


add_filter( 'woocommerce_product_get_image', 'offsite_product_images', 10, 5 );
function offsite_product_images( $image, $product, $size, $attr, $placeholder ){
    global $post;
    $image_id =  get_post_meta($post->ID, 'custom_image_id', true );
    $image = wp_get_attachment_image( $image_id, 'large', false );
    return $image;
}