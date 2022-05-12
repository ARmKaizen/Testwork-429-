<?php
/*
Template Name: Add product
Template Post Type: post, page, product
*/

if(!is_user_logged_in()) {
    auth_redirect();
}

get_header();
?>
    <style>
        body{
            background: -webkit-linear-gradient(left, #0072ff, #00c6ff);
        }
        .contact-form{
            background: #fff;
            margin-top: 10%;
            margin-bottom: 5%;
            width: 70%;
        }
        .contact-form .form-control{
            border-radius:1rem;
        }
        .contact-image{
            text-align: center;
        }
        .contact-image img{
            border-radius: 6rem;
            width: 11%;
            margin-top: -3%;
            transform: rotate(29deg);
        }
        .contact-form form{
            padding: 14%;
        }
        .contact-form form .row{
            margin-bottom: -7%;
        }
        .contact-form h3{
            margin-bottom: 8%;
            margin-top: -10%;
            text-align: center;
            color: #0062cc;
        }
        .contact-form .btnContact {
            width: 50%;
            border: none;
            border-radius: 1rem;
            padding: 1.5%;
            background: #dc3545;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
        }
        .btnContactSubmit
        {
            width: 50%;
            border-radius: 1rem;
            padding: 1.5%;
            color: #fff;
            background-color: #0062cc;
            border: none;
            cursor: pointer;
        }
    </style>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<?php
if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "new_post") {

//    echo "<pre>"; print_r($_POST); echo "</pre>"; exit();

    if (isset ($_POST['title'])) {
        $title =  $_POST['title'];
    } else {
        echo 'Please enter a title';
    }
    if (isset ($_POST['description'])) {
        $description = $_POST['description'];
    } else {
        echo 'Please enter the content';
    }
    $tags = $_POST['post_tags'];
    $_regular_price = $_POST['_regular_price'];
    $_sale_price = $_POST['_sale_price'];
    $_date = $_POST['_date'];
    $_custom_product_select = $_POST['_custom_product_select'];
    $custom_image_id = $_POST['custom_image_id'];

    $new_post = array(
        'post_title'    => $title,
        'post_content'  => $description,
        'tags_input'    => array($tags),
        'post_status'   => 'publish',
        'post_type' => 'product'
    );
    $new_post_meta = array(
        '_regular_price'   => $_regular_price,
        '_sale_price'   => $_sale_price,
        '_price'   => $_sale_price,
        '_date'   => $_date,
        '_custom_product_select'   => $_custom_product_select,
        'custom_image_id'   => $custom_image_id,
    );
    //save the new post and return its ID
    if($pid = wp_insert_post($new_post)){
        foreach ($new_post_meta as $item => $value){
            add_post_meta($pid, $item, $value);
        }

        echo '<div class="woocommerce-info">
		Товар добавлен	</div>';
    }
}
//echo "<pre>";
//print_r( $wp_filter );
//echo "</pre>";

?><p><a href="/" class="button product_type_simple ">Product list</a></p>
    <div class="container contact-form">
        <div class="contact-image">
            <img src="https://image.ibb.co/kUagtU/rocket_contact.png" alt="rocket_contact"/>
        </div>
        <form id="new_post" name="new_post" method="post" action="">
            <h3>Create a product</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" name="title" placeholder="title" id="title" class="form-control" required="required" />
                    </div>
                    <div class="form-group">
                        <input type="text" name="_regular_price" placeholder="regular price" id="_regular_price" class="form-control" required="required" />
                    </div>
                    <div class="form-group">
                        <input type="text" name="_sale_price" placeholder="sale price" id="_sale_price" class="form-control" required="required" />
                    </div>
                    <div class="form-group">
                        <select name="_custom_product_select" id="_custom_product_select" class="form-control" required="required">
                            <option></option>
                            <option value="rare">rare</option>
                            <option value="frequent">frequent</option>
                            <option value="unusual">unusual</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="datetime-local" name="_date" value="<?= strftime('%Y-%m-%dT%H:%M:%S', strtotime(current_time( 'Y-m-d H:i:s' ))) ?>" id="_date" class="form-control"  />
                    </div>


                    <div class="form-group">
                        <?php
                        echo $image_id =  get_post_meta(get_the_ID(), 'custom_image_id', true );

                        if( intval( $image_id ) > 0 ) {
                            // Change with the image size you want to use
                            $image = wp_get_attachment_image( $image_id, 'medium', false, array( 'id' => 'custom-preview-image' ) );
                        } else {
                            // Some default image
                            $image = '<img id="custom-preview-image" src="/wp-content/uploads/woocommerce-placeholder-150x150.png" />';
                        }
                        echo $image;
                        ?>
                        <input type="hidden" name="custom_image_id" id="custom_image_id" value="<?php echo esc_attr( $image_id ); ?>" class="regular-text" />
                        <input type="button" id="remove_img" class="button-info" value="Удалить картинку" class="btn btn-success">
                        <br>
                        <input type='button' class="button-primary" value="<?php esc_attr_e( 'Select a image', 'mytextdomain' ); ?>" id="custom_media_manager"/>
                        <input type="button" id="clear_custom_fields" value="Clear custom fields">
                    </div><div class="form-group">
                        <input type="submit" name="submit" id="submit" class="btnContact" value="Publish" />
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <textarea name="description" class="form-control" placeholder="Content" style="width: 100%; height: 280px;"></textarea>
                    </div>
                </div>
            </div>
            <input type="hidden" name="action" value="new_post" />
            <?php wp_nonce_field( 'new-post' ); ?>
        </form>
    </div>

    <script>
        var ajaxurl = "<?= admin_url('admin-ajax.php') ?>";
    </script>
    <script src="<?= get_template_directory_uri().'/assets/js/custom.js' ?>"></script>
<?php

get_Footer();