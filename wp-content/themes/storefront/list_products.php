<?php
/*
Template Name: List products
Template Post Type: post, page, product
*/

//if(!is_user_logged_in()) {
//    auth_redirect();
//}

get_header();

?>

    <p><a href="/?page_id=14" class="button product_type_simple ">Add product</a></p>
    <table>
    <tr>
        <th>img</th>
        <th>name</th>
        <th>price</th>
        <th>date</th>
    </tr>
<?php


$params = array(
    'post_type' => 'product',
    'numberposts' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
    '_regular_price' => 'DESC',
    'suppress_filters' => true
);
$recent_posts_array = get_posts($params);
foreach( $recent_posts_array as $recent_post_single ) :
//    echo "<pre>" . print_r($recent_post_single) . "</pre>";
    $image_id =  get_post_meta($recent_post_single->ID, 'custom_image_id', true );
    $image = wp_get_attachment_image( $image_id, 'thumbnail', false, array( 'id' => 'custom-preview-image' ) );
?>


        <tr>
            <td><?= $image ?></td>
            <td><?php echo '<a href="' . get_permalink( $recent_post_single ) . '">' . $recent_post_single->post_title . '</a>'; ?></td>
            <td><?= get_price($recent_post_single->ID) ?></td>
            <td><?= $recent_post_single->post_date ?></td>
        </tr>


<?php
endforeach;
?>
    </table>
<?php
get_Footer();

function get_price($id)
{
    $product = wc_get_product($id);
    $thePrice = $product->get_price(); //will give raw price
    echo $thePrice;
}