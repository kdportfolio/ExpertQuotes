<?php

class expert_quotes {

    public function __construct() {
        add_action( 'enqueue_block_editor_assets', array( $this, 'expertquoteblock' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
        add_shortcode( 'expert_quote', array( $this, 'expert_quote' ) );
    }

    function expertquoteblock() {
        wp_enqueue_script(
                'expert-quote', PLUGIN_DIR . '/js/block-expert.js', array( 'wp-blocks', 'wp-editor' ), true
        );
    }

    function enqueue_script() {
        wp_enqueue_style(
                'expert-quote-css', PLUGIN_DIR . 'css/style.css'
        );
    }

    function expert_quote( $atts ) {
        ob_start();

        $output = '';

        if ( isset( $_REQUEST[ 'EXPERT-TEST-IP' ] ) ) {
            $ip = $_REQUEST[ 'EXPERT-TEST-IP' ];
        }
        else {
            $ip = $_SERVER[ "REMOTE_ADDR" ];
        }
        $url = urldecode( "https://geoip.nekoapi.icu/api/" . $ip );
        $json = file_get_contents( $url );
        $data = json_decode( $json, TRUE );
        $location_latitude = $data[ 'location' ][ 'latitude' ];
        $location_longitude = $data[ 'location' ][ 'longitude' ];
        $ip = $data[ 'ip' ];

        $ref = array( $location_latitude, $location_longitude );
        
        $output .= '<div class= "post_content">';

        foreach ( get_the_category() as $category ) {
            $args = array(
                'meta_key' => 'taxonomy_advanced_5',
                'meta_value' => $category->cat_ID,
                'meta_compare' => 'LIKE'
            );

            $experts = get_users( $args );

            foreach ( $experts as $expert ) {

                $experts_meta = get_user_meta( $expert->ID, 'group_8' );

                foreach ( $experts_meta as $key => $value ) {

                    $user_display_status = get_user_meta( $expert->ID, 'group_6' );
                    if ( isset( $user_display_status[ 0 ][ 'checkbox_1' ] ) && $user_display_status[ 0 ][ 'checkbox_1' ] == 1 ) {
                        $all_experts_arrays[] = array( $expert->user_email, get_avatar( $expert->user_email ), $expert->display_name, $expert->user_url, $value[ 'number_6' ], $value[ 'number_5' ] );
                    }
                }
            }
            
            if ( isset( $all_experts_arrays ) && $all_experts_arrays != '' ) {
                
                $distances = array_map( function( $all_experts_array ) use( $ref ) {
                    $a = array_slice( $all_experts_array, -2 );
                    return $this->distance( $a, $ref );
                }, $all_experts_arrays );

                asort( $distances );
                
                /*Debug Options*/
                if( isset ( $_REQUEST [ 'displayDebug' ] ) && $_REQUEST [ 'displayDebug' ] == true ) {
                    $i = 0;
                    foreach ( $distances as $k => $v ) {
                         $arr[$i]['name'] = $all_experts_arrays[ $k ][ 2 ];
                         $arr[$i]['distance'] = $v;
                         $i++;
                    }

                    echo "<pre>";
                    print_r($arr);
                    echo "</pre>";
                }
                /*Debug Options*/
                
                $output .= '<div class="expert-wrap">';
                $output .= '<div class="expert">';
                $output .= $all_experts_arrays[ key( $distances ) ][ 1 ] . $all_experts_arrays[ key( $distances ) ][ 2 ];
                $output .= '</br>';
                $output .= '<a href=' . $all_experts_arrays[ key( $distances ) ][ 3 ] . ' class="expert_url" target="_blank">Jetzt ansprechen</a>';
                $output .= '</div>';
                $output .= '</div>';

                $quote_args = array(
                    'post_type' => 'quote',
                    'orderby' => 'rand',
                    'posts_per_page' => 1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'category',
                            'field' => 'slug',
                            'terms' => $category->slug,
                        )
                    )
                );

                $quotes = new WP_Query( $quote_args );

                while ( $quotes->have_posts() ) : $quotes->the_post();

                    $output .= '<div id="primary" class="primary">';
                    $output .= '<div id="content" role="main" class="main_content">';
                    $output .= '<article id="post">';
                    $output .= '<header class="entry-header">';
                    $output .= get_the_title();
                    $output .= '</header></br>';
                    $output .= '<div class="entry-content"><p>' . get_the_content() . '</p></div></br>';
                    $output .= '</article>';
                    $output .= '</div>';
                    wp_reset_postdata();
                endwhile;
                $output .= '</div>';
            }
            else {
                $output .= '<div align="center" class="no-results"><p>Stay tuned for the expert quote..!!</p></div>';
            }
        }
        $output .= '</div>';
        echo $output;

        return ob_get_clean();
    }

    function distance( $a, $b ) {
        list( $lat1, $lon1 ) = $a;
        list( $lat2, $lon2 ) = $b;

        $theta = $lon1 - $lon2;
        $dist = sin( deg2rad( $lat1 ) ) * sin( deg2rad( $lat2 ) ) + cos( deg2rad( $lat1 ) ) * cos( deg2rad( $lat2 ) ) * cos( deg2rad( $theta ) );
        $dist = acos( $dist );
        $dist = rad2deg( $dist );
        $miles = $dist * 60 * 1.1515;
        return $miles;
    }

}
