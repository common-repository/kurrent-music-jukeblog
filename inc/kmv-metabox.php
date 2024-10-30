<?php
/**
* KurrentMusic Jukeblog Custom Meta
**/

/**
* Add The Meta Box
**/
function kurrentmusic_jukeblog_add_meta_box() {
    
    $screens = array('post');
    foreach ( $screens as $screen ) {
        
        add_meta_box(
        'kurrentmusic_jukeblog_meta_sectionid',
        __( 'Kurrent Music Jukeblog Options', 'layerswp' ),
        'kurrentmusic_jukeblog_meta_box_callback',
        /* options for screen: normal, advanced, or side for placement; high, core, default, low for order */
        $screen,
        'normal',
        'high'
        );
    }
}
add_action( 'add_meta_boxes', 'kurrentmusic_jukeblog_add_meta_box' );
//Add The Callback

function kurrentmusic_jukeblog_meta_box_callback(){
    
    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'kurrentmusic_jukeblog_meta_box', 'kurrentmusic_jukeblog_meta_box_nonce' );
    
    /*
    * Use get_post_meta() to retrieve an existing value
    * from the database and use the value for the form.
    */
    
    $km_artistname 	= get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_artist_name', true );
    $km_artistid 	= get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_artist_id', true );
    $km_albumname 	= get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_album_name', true );
    $km_albumid 	= get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_album_id', true );
    $km_viewtype 	= get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_view_type', true );
    $km_layout 		= get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_layout', true );
    
    // form elements go here
    
    echo '<div class="layers-form-item">';
    echo '<label>View Type:</label>';
    echo '<input type="radio" name="kurrentmusic_jukeblog_view_type" value="artist" ';
    if(
    $km_viewtype=='artist'){  echo ' checked';
    }
    echo '> Artist';
    echo '<input type="radio" name="kurrentmusic_jukeblog_view_type" value="album" ';
    if(
    $km_viewtype=='album'){  echo ' checked';
    }
    echo '> Album';
    echo '</div>';
    echo '<div class="layers-form-item">';
    echo '<label>Artist Name: </label>';
    $kurrentmusic_jukeblog_artist_name_val =  isset( $km_artistname ) ? $km_artistname : '';
    $kurrentmusic_jukeblog_artist_id_val = isset( $km_artistid ) ? $km_artistid : '';
    
    echo '<input type="text" name="kurrentmusic_jukeblog_artist_name" id="kurrentmusic_jukeblog_artist_name" value="'. $kurrentmusic_jukeblog_artist_name_val .'">';
    echo '<input type="hidden" name="kurrentmusic_jukeblog_artist_id" id="kurrentmusic_jukeblog_artist_id" value="'. $kurrentmusic_jukeblog_artist_id_val .'">';
    echo '<a class="kmv-lookupbtn"><div class="dashicons dashicons-update" id="kurrentmusic_jukeblog_update_artist"></div> Lookup</a>';
    echo '</div>';
    echo '<div class="layers-form-item kmv_album_settings">';
    echo '<label>Album Name: </label> ';
    
    $kurrentmusic_jukeblog_album_name_val =  isset( $km_albumname ) ? $km_albumname : '';
    $kurrentmusic_jukeblog_album_id_val =  isset( $km_albumid ) ? $km_albumid : '';
    if($kurrentmusic_jukeblog_album_name_val != ''){
        echo '<span id="kurrentmusic_jukeblog_album_name_label">'. $kurrentmusic_jukeblog_album_name_val .'</span>';
    }
    echo '<input type="hidden" name="kurrentmusic_jukeblog_album_name" id="kurrentmusic_jukeblog_album_name" value="' .$kurrentmusic_jukeblog_album_name_val .'">';
    echo '<input type="hidden" name="kurrentmusic_jukeblog_album_id" id="kurrentmusic_jukeblog_album_id" value="' .$kurrentmusic_jukeblog_album_id_val .'">';
    echo '</div>';
    
    
    echo '<div class="layers-form-item kmv_layoutoptions">';
   // echo $km_layout;
    echo '<label>Layout: </label>';
    echo '<span class="kmv_artist_settings">';
    echo '<input type="radio" name="kurrentmusic_jukeblog_layout" id="kurrentmusic_jukeblog_layout" value="top10" ';
    if(
    $km_layout=='top10'){  echo 'checked';
    }
    echo '> Top 10 Songs ';
    echo '</span>';
    echo '<span class="kmv_artist_settings">';
    echo'<input type="radio" name="kurrentmusic_jukeblog_layout" id="kurrentmusic_jukeblog_layout" value="discography" ';
    if(
    $km_layout=='discography'){  echo 'checked';
    }
    echo '> Discography ';
    echo '</span>';
    echo '<span class="kmv_artist_settings">';
    echo'	<input type="radio" name="kurrentmusic_jukeblog_layout" id="kurrentmusic_jukeblog_layout" value="bio" ';
    if(
    $km_layout=='bio'){  echo 'checked';
    }
    echo '> Bio ';
    echo '</span>';
    echo '<span class="kmv_album_settings">';
    echo '<input type="radio" name="kurrentmusic_jukeblog_layout" id="kurrentmusic_jukeblog_layout" value="albumsongs" ';
    if(
    $km_layout=='albumsongs'){  echo 'checked';
    }
    echo '> Album Songs  ';
    echo '</span>';
    echo '<span class="kmv_album_settings">';
    echo '<input type="radio" name="kurrentmusic_jukeblog_layout" id="kurrentmusic_jukeblog_layout" value="albumcover" ';
    if(
    $km_layout=='albumcover'){  echo 'checked';
    }
    echo '> Album Cover Only ';
    echo '</span>';
    echo '<span class="kmv_artist_settings kmv_album_settings">';
    echo '<input type="radio" name="kurrentmusic_jukeblog_layout" id="kurrentmusic_jukeblog_layout" value="blogs" ';
    if(
    $km_layout=='blogs'){  echo 'checked';
    }
    echo '> Blogs ';
    echo '</span>';
    echo '<span class="kmv_artist_settings kmv_album_settings">';
    echo '<input type="radio" name="kurrentmusic_jukeblog_layout" id="kurrentmusic_jukeblog_layout" value="news" ';
    if(
    $km_layout=='news'){  echo 'checked';
    }
    echo '> News ';
    echo '</span>';
    echo '<span class="kmv_disclaimer">Note: Some options of the Kurrent Music Jukeblog Widget include links to kurrentmusic.com and other music industry websites.</span>';
    echo '</div>';
}
//Save The Meta
function kurrentmusic_jukeblog_save_meta_box_data( $post_id ) {
    
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'kurrentmusic_jukeblog_meta_box_nonce' ] ) && wp_verify_nonce( $_POST[ 'kurrentmusic_jukeblog_meta_box' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
    
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
    
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'kurrentmusic_jukeblog_artist_name' ] ) ) {
        update_post_meta( $post_id, 'kurrentmusic_jukeblog_artist_name', sanitize_text_field( $_POST[ 'kurrentmusic_jukeblog_artist_name' ] ) );
    }
    if( isset( $_POST[ 'kurrentmusic_jukeblog_artist_id' ] ) ) {
        update_post_meta( $post_id, 'kurrentmusic_jukeblog_artist_id', sanitize_text_field( $_POST[ 'kurrentmusic_jukeblog_artist_id' ] ) );
    }
    if( isset( $_POST[ 'kurrentmusic_jukeblog_album_name' ] ) ) {
        update_post_meta( $post_id, 'kurrentmusic_jukeblog_album_name', sanitize_text_field( $_POST[ 'kurrentmusic_jukeblog_album_name' ] ) );
    }
    if( isset( $_POST[ 'kurrentmusic_jukeblog_album_id' ] ) ) {
        update_post_meta( $post_id, 'kurrentmusic_jukeblog_album_id', sanitize_text_field( $_POST[ 'kurrentmusic_jukeblog_album_id' ] ) );
    }
    if( isset( $_POST[ 'kurrentmusic_jukeblog_layout' ] ) ) {
        update_post_meta( $post_id, 'kurrentmusic_jukeblog_layout', sanitize_text_field( $_POST[ 'kurrentmusic_jukeblog_layout' ] ) );
    }
    if( isset( $_POST[ 'kurrentmusic_jukeblog_view_type' ] ) ) {
        update_post_meta( $post_id, 'kurrentmusic_jukeblog_view_type', sanitize_text_field( $_POST[ 'kurrentmusic_jukeblog_view_type' ] ) );
    }
}
add_action( 'save_post', 'kurrentmusic_jukeblog_save_meta_box_data' );

function kurrentmusic_jukeblog_backend_files() {
    
    wp_enqueue_style( 'kurrentmusic_jukeblog_backend_css', plugins_url( 'kurrent-music-jukeblog/css/kmv-backend.css' ) );
    wp_enqueue_script( 'kurrentmusic_jukeblog_backend_js', plugins_url( 'kurrent-music-jukeblog/js/kmv-backend.js' ), array('jquery'), '', true );
?>
    <script type="text/javascript">
        var km_root_host = <?=json_encode(__KM_ROOT__)?>;
        var km_api_key = <?=json_encode(get_option(__API_KEY_OPTION_NAME__))?>;
    </script>
<?php
}
add_action( 'admin_head', 'kurrentmusic_jukeblog_backend_files' );

?>