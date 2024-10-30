<?php
$km_token_status = checkValidApiKey();
if($km_token_status->valid && $kmv_artist_name !=''){
?>

  <script type="text/javascript">
    var km_root_host = <?=json_encode(__KM_ROOT__)?>;
    var km_api_key = <?=json_encode(get_option(__API_KEY_OPTION_NAME__))?>;
  </script>
 <!-- <div class="kmv-blurassist">
    <svg class="kmv-svg-effects">
        <filter id="kmv-blur-effect-1">
            <feGaussianBlur stdDeviation="5" />
        </filter>

        <filter id="kmv-blur-effect-2">
            <feGaussianBlur stdDeviation="10" />
        </filter>
    </svg> 
</div> -->
  <!--<div class="kmv-blurassist"><img src="<?php echo plugins_url( 'kurrent-music-jukeblog/img/blur.svg'); ?>" alt=""></div>-->
  <?php
    switch ($kmv_view_type){
        case "album": {
            
            switch ($kmv_layout){
                case "albumsongs":
                    require( 'kmv-display-songs.php' );
                    break;
                case "albumcover":
                    require( 'kmv-display-cover.php' );
                    break;
                case "blogs":
                    require( 'kmv-display-albumblogs.php' );
                    break;
                case "news":
                    require( 'kmv-display-albumnews.php' );
                    break;
                default:
                    require( 'kmv-display-cover.php' );
                    break;
        }
        break;
}
default: {
  //  echo  "Artist Details";
    switch ($kmv_layout){
        case "top10":
            require( 'kmv-display-top10.php' );
            break;
        case "discography":
            require( 'kmv-display-discography.php' );
            break;
        case "bio":
            require( 'kmv-display-bio.php' );
            break;
        case "blogs":
            require( 'kmv-display-blogs.php' );
            break;
        case "news":
            require( 'kmv-display-news.php' );
            break;
        default:
            require( 'kmv-display-top10.php' );
            break;
}
break;
}
}

?>
    <?php
}else {
    if($km_token_status->error != ''){
        require('km-invalid-api-key.php');
    }
}
/*
echo '<p>artist name: ' . $kmv_artist_name . '</p>';
echo '<p>artist id: ' . $kmv_artist_id . '</p>';
echo '<p>album name: ' . $kmv_album_name . '</p>';
echo '<p>album id: ' . $kmv_album_id . '</p>';
echo '<p>layout: ' . $kmv_layout . '</p>';
echo '<p>view type: ' . $kmv_view_type . '</p>';
*/
?>