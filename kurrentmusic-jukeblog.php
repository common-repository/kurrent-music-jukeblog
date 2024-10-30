<?php
/*
Plugin Name: KurrentMusic Jukeblog
Plugin URI: http://kurrentmusic.com/wp/jukeblog
Description: Displays panel with supplemental artist or album information on your blogs.
Version: 1.0
Author: KurrentMusic.com
Author URI: http://kurrentmusic.com
*/
/*
Assign global variables
*/
class KMVArtist{
    public $Id = '';
    public $Name = '';
    public $Image = '';
    public $Popularity = '';
    public $Biography = '';
    public $Albums = '';
    public $Blogs = '';
    public $OtherNews = '';
    
}
class KMVAlbum{
    public $Id = '';
    public $Name = '';
    public $Image = '';
    public $Artist = '';
    public $Popularity = '';
    public $ReleaseDate = '';
    public $Copyright = '';
    public $Songs = '';
    public $Blogs = '';
    public $OtherNews = '';
}
class KMVSong{
    public $Name = '';
    public $TrackNumber = '';
    public $Duration = '';
    public $PreviewURL = '';
}
class KMVBlog{
    public $Title = '';
    public $Description = '';
    public $URL = '';
    public $Date = '';
    public $SourceAvatar = '';
    
}

define('__KM_ROOT__','http://visualizer.kurrentmusic.com');
define('__API_KEY_OPTION_NAME__', "KMV_API_KEY");
$plugin_url = WP_PLUGIN_URL . '/kurrentmusic_jukeblog';
$options = array();
$display_json = false;
register_activation_hook(__FILE__, 'plugin_activate');


function GUID()    {
    if (function_exists('com_create_guid') === true)         {
        return trim(com_create_guid(), '{}');
    }
    
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

function getRequestIP(){
    $clientIP = false;
    if (array_key_exists('HTTP_CLIENT_IP', $_SERVER))
        $clientIP = getRequestIP();
    else
        $clientIP = getenv('REMOTE_ADDR');
    
    return $clientIP;
}

function buildDataRetrievalUrl($path, $params){
    if (array_key_exists('action', $params) === FALSE){
        $params['action'] = 'retrieveData';
    }
    
    return buildUrl($path, $params);
}

function buildUrl($path, $params){
    if (array_key_exists('t', $params) === FALSE){
        $params['t'] = get_option(__API_KEY_OPTION_NAME__);
    }
    
    $params['ip'] = getRequestIP();
    $params['rt'] = $GLOBALS['kmv-request-token'];
    
    foreach ($params as $key => $value) {
        $params[$key] = rawurlencode($value);
    }
    
    $url = add_query_arg($params, __KM_ROOT__ . $path);
    
    return $url;
}

function checkValidApiKey(){
    $artist_name =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_artist_name', true);
    $artist_id =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_artist_id', true);
    $album_name =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_album_name', true);
    $album_id =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_album_id', true);
    $view_layout =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_layout', true);
    $view_type =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_view_type', true);

    $validKey = (object) array( 'valid'=> false, 'error'=> '', token => '' );
    $url= buildUrl('',array( 'action'=> 'validateApiKey', 
                             'userAgent' => $_SERVER['HTTP_USER_AGENT'],
                             'referrerUrl'=> $_SERVER['HTTP_HOST'],
                             'artistName' => $artist_name,
                             'artistId' => $artist_id,
                             'albumName' => $album_name,
                             'albumId' => $album_id,
                             'viewLayout' => $view_layout,
                             'viewType' => $view_type));
    
    $contents = @file_get_contents($url);

    if ($contents === FALSE){
        $validKey->error = 'Error retrieving data';
    }else {
        $validKey = json_decode($contents);
        $GLOBALS['kmv-request-token'] = $validKey->token;
    }
    
    return $validKey;
}

function plugin_activate() {
    
    $api_key = get_option(__API_KEY_OPTION_NAME__);
    if ($api_key === FALSE || $api_key === ''){
        $api_key = GUID();
        
        $url = buildUrl('',array('action' => 'registerApiKey', 't' => $api_key));
        $response = json_decode(file_get_contents($url));
        update_option(__API_KEY_OPTION_NAME__, $api_key);
    }
}

function kmvGetAlbumBlogs($album){
    $path = '/dataentry/search/KurrentMusic/Blogs';
    $params = array('searchdata' => json_encode(array('Artist' => $album->Artist->Name, 'album' => $album->Name, 'Language' => 'English')),
                    'returnfields' => "Id,MainImage, Genre, Language, BodyText, Title, URLId, Artist, PublishedDate, Published");    
    $albumblogsURL = buildDataRetrievalUrl($path,$params);
    
    
    $albumblogs = json_decode(file_get_contents($albumblogsURL));

    $album->Blogs = array();
    for($i=0;$i<count($albumblogs); $i++){
        $thisBlog = new KMVBlog();
        $thisBlog->Title = $albumblogs[$i]->Title;
        $albumblogs[$i]->BodyText = preg_replace('/<p><\/p>/','',$albumblogs[$i]->BodyText);
        $albumblogs[$i]->BodyText = preg_replace('/<p>&nbsp;<\/p>/','',$albumblogs[$i]->BodyText);
        $thisBlog->Description = substr($albumblogs[$i]->BodyText, 0 , 200) .'...';
        $thisBlog->URL = 'http://www.kurrentmusic.com/blogviewer.html#!blog-guid='. $albumblogs[$i]->UrlId;
        $thisBlog->Date = date_format(date_create($albumblogs[$i]->PublishedDate), "m/d/Y");
        array_push($album->Blogs, $thisBlog);
    }
    
    $album = kmvGetOtherAlbumNews($album);
    return $album;
}
function kmvGetOtherAlbumNews($album){
    $path = '/data/music/albumid/id';
    $params = array('artist' => ($album->Artist->Name), 'album' => ($album->Name));
    $url = buildDataRetrievalUrl($path,$params);
    $album->OtherNews = array();
    $altid = json_decode(file_get_contents($url));
    if($altid[0]->Id){
        $path = '/data/music/albumreviews/reviews';
        $params = array('context'=> $altid[0]->Id);
        $url = buildDataRetrievalUrl($path,$params);
        
        $altnews = json_decode(file_get_contents($url));
        
        for($i=0;$i<count($altnews); $i++){
            $thisBlog = new KMVBlog();
            $thisBlog->Title = $altnews[$i]->Title;
            $altnews[$i]->Summary = preg_replace('/<p><\/p>/','',$altnews[$i]->Summary);
            $altnews[$i]->Summary = preg_replace('/<p>&nbsp;<\/p>/','',$altnews[$i]->Summary);
            $thisBlog->Description = substr($altnews[$i]->Summary, 0 , 200).'...';
            $thisBlog->URL = $altnews[$i]->Url;
            $thisBlog->Date = $altnews[$i]->Month . '/' . $altnews[$i]->Day . '/' .$altnews[$i]->Year;
            $thisBlog->SourceAvatar = $altnews[$i]->SourceAvatar;
            array_push($album->OtherNews, $thisBlog);
        }
    }
    return $album;
}

function kmvGetArtistBlogs($artist){
    $path = '/dataentry/search/KurrentMusic/Blogs';
    $params = array('searchdata' => json_encode(array('Artist' => $artist->Name, 'Language' => 'English')),
                    'returnfields' => "Id,MainImage, Genre, Language, BodyText, Title, URLId, Artist, PublishedDate, Published");
    $artistblogsURL = buildDataRetrievalUrl($path,$params);
    
    $artistblogs = json_decode(file_get_contents($artistblogsURL));
    $artist->Blogs = array();
    
    for($i=count($artistblogs)-1;$i>=0; $i--){
        $thisBlog = new KMVBlog();
        $thisBlog->Title = $artistblogs[$i]->Title;
        $artistblogs[$i]->BodyText = preg_replace('/<p><\/p>/','',$artistblogs[$i]->BodyText);
        $artistblogs[$i]->BodyText = preg_replace('/<p>&nbsp;<\/p>/','',$artistblogs[$i]->BodyText);
        $thisBlog->Description = substr($artistblogs[$i]->BodyText, 0,200).'...';
        $thisBlog->URL = 'http://www.kurrentmusic.com/blogviewer.html#!blog-guid='. $artistblogs[$i]->URLId;
        $thisBlog->Date = date_format(date_create($artistblogs[$i]->PublishedDate), "m/d/Y");
        array_push($artist->Blogs, $thisBlog);
    }
    
    $artist = kmvGetOtherArtistNews($artist);
    return $artist;
}
function kmvGetOtherArtistNews($artist){
    $path = '/data/music/artistss/search';
    $params = array('artist' => ($artist->Name));
    $altidURL = buildDataRetrievalUrl($path,$params);
    
    $altid = json_decode(file_get_contents($altidURL));
    $artist->OtherNews = array();
    if($altid[0]->Id){
        $path = '/data/music/artistnews/news';
        $params = array('contex' => $altid[0]->Id);
        $altnewsURL = buildDataRetrievalUrl($path,$params);
        
        $altnews = json_decode(file_get_contents($altnewsURL));
        
        for($i=0;$i<count($altnews); $i++){
            $thisBlog = new KMVBlog();
            $thisBlog->Title = $altnews[$i]->Title;
            $altnews[$i]->Summary = preg_replace('/<p><\/p>/','',$altnews[$i]->Summary);
            $altnews[$i]->Summary = preg_replace('/<p>&nbsp;<\/p>/','',$altnews[$i]->Summary);
            
            $thisBlog->Description = substr($altnews[$i]->Summary, 0,200).'...';
            $thisBlog->URL = $altnews[$i]->Url;
            $thisBlog->Date = $altnews[$i]->Month . '/' . $altnews[$i]->Day . '/' .$altnews[$i]->Year;
            $thisBlog->SourceAvatar = $altnews[$i]->SourceAvatar;
            array_push($artist->OtherNews, $thisBlog);
        }
    }
    return $artist;
}
function kmvGetArtistInfo($name, $id){
    
    if($id!=''){
        //Get general artist information
        $path = '/data/music/artistdetailsspotify/details';
        $params = array('context' => $id);
        $artistdetailsURL = buildDataRetrievalUrl($path,$params);
        
        $artistdetails = json_decode(file_get_contents($artistdetailsURL));
        $artist = new KMVArtist();
        $artist->Id = $id;
        $artist->Name = $artistdetails[0]->Name;
        $artist->Image = $artistdetails[0]->Image;
        $artist->Popularity = $artistdetails[0]->Popularity;
        //Get Artist Albums
        $path = '/data/music/artistalbumsspotify/albums';
        $params = array('context' => $id);
        $artistalbumsURL = buildDataRetrievalUrl($path,$params);
        
        $artistalbums = json_decode(file_get_contents($artistalbumsURL));
        
        $artist->Albums = array();
        for($i=0; $i<count($artistalbums); $i++){
            $foundalbum =false;
            for($j=0; $j < count($artist->Albums); $j++){
                if($artist->Albums[$j]->Name == $artistalbums[$i]->Name){
                    $foundalbum= true;
                }
            }
            if($foundalbum==false){
                $thisalbum = new KMVALbum();
                $thisalbum->Id = $artistalbums[$i]->Id;
                $thisalbum->Name = $artistalbums[$i]->Name;
                $thisalbum->Image = $artistalbums[$i]->Image;
                array_push($artist->Albums, $thisalbum);
            }
        }
        
        return $artist;
    }
}
function kmvGetArtistBio($artist){
    
    if($artist->Id!=''){
        $path = '/data/music/artistss/search';
        $params = array('context' => $artist->Name);
        $altartistidURL = buildDataRetrievalUrl($path,$params);
        $args = array('timeout' => 120);
        $altartistid = wp_remote_get($altartistidURL);
        $altartistid = json_decode($altartistid['body']);
        if(count($altartistid)<1){
            $artistbio = 'No currently biography available.';
        }else{
            foreach($altartistid as &$thisaltid){
                if($thisaltid->Name==$artist->Name){

                    $path = '/data/music/artistbio/bio';
                    $params = array('context' => $thisaltid->Id);
                    $altartistBioURL = buildDataRetrievalUrl($path,$params);
                    
                    $altartistbio = wp_remote_get($altartistBioURL, $args);
                    $altartistbio = json_decode($altartistbio['body']);
                    if(count($altartistbio->error)>0){
                        $artistbio = $altartistbio->error;
                    }else{
                        $artistbio = $altartistbio[0]->Text;
                    }
                    break;
            }
        }
    }
   
        $artist->Biography = substr($artistbio, 0 , 250) . '... <a href="http://www.kurrentmusic.com/artistinfo.html?artist-name='. $artist ->Name .'&artist-id='. $artist ->Id .'" target="_blank">Read More <i class="kmi-external-link"></i></a>';
    
    return $artist;
}

}
function kmvGetArtistVideos($artist){
    $searchInfo = array("SpotifyArtistId" => $artist->Id, "returnfields" => "Id,UserId,Username,Title,MainImage,Artist,ArtistImage,Album,AlbumImage,Song,Genre,SpotifyArtistId,SpotifyAlbumId,YouTubeId,URLId,Views,DefaultPlay,CreatedOn");
    $path = '/dataentry/search/KurrentMusic/Videos';
    $params = array('searchdata' => json_encode($searchInfo));
    $videosURL = buildDataRetrievalUrl($path,$params);
    
    $videos = json_decode(file_get_contents($videosURL));
}
function kmvGetAlbumInfo($name, $id){
    
    if($id!=''){
        $album = new KMVAlbum();
        //get album info by id
        $path = '/data/music/albumdetailsspotify/details';
        $params = array('context' => $id);
        $albumdetailsURL = buildDataRetrievalUrl($path,$params);
        
        $albumdetails = json_decode(file_get_contents($albumdetailsURL));
        $album->Id = $id;
        $album->Name = $name;
        $album->Popularity = $albumdetails[0]->Popularity;
        $album->ReleaseDate = $albumdetails[0]->ReleaseDate;
        $album->Copyright = $albumdetails[0]->Copyrights;
        $album->Image = $albumdetails[0]->Image;
        
        $path = '/data/music/albumdetailsspotify/songs';
        $params = array('context' => $id);
        $albumsongsURL = buildDataRetrievalUrl($path,$params);
        
        $albumsongs = json_decode(file_get_contents($albumsongsURL));
        $album->Songs = array();
        for($i=0; $i<count($albumsongs); $i++){
            $thissong = new KMVSong();
            $thissong->Name = $albumsongs[$i]->Name ;
            $thissong->TrackNumber = $albumsongs[$i]->TrackNumber ;
            $thissong->Duration = $albumsongs[$i]->Duration ;
            $thissong->PreviewURL = $albumsongs[$i]->PreviewUrl ;
            array_push($album->Songs, $thissong);
        }
        return $album;
    }
}
function kmvGetArtistTop10Songs($artist){
    $path = '/data/music/top10artistsongs/songs';
    $params = array('context' => $artist->Id);
    $top10URL = buildDataRetrievalUrl($path,$params);
    
    $top10 = json_decode(file_get_contents($top10URL));
    
    return $top10;
}
function kmvGetSocialChannels($artist){
    $path = '/data/music/artistss/search';
    $params = array('context' => ($artist->Name));
    $altidURL = buildDataRetrievalUrl($path,$params);
    
    $altid = json_decode(file_get_contents($altidURL));
    if($altid[0]->Id){
        $path = '/data/music/artistchannels/channels';
        $params = array('context' => $altid[0]->Id);
        $channelsURL = buildDataRetrievalUrl($path,$params);
        
        $channels = json_decode(file_get_contents($channelsURL));
        
        return kmvRenderSocialChannels($channels);
    }
}
function kmvRenderSocialChannels($channels){
    $channelshtml = '';
    foreach($channels as &$thischannel){
        
        switch($thischannel->Name){
            case "twitter":
                if (strpos($channelshtml, 'twitter') == false){
                    $channelshtml = $channelshtml . '<a href="' . $thischannel->Url . '" title="' . $thischannel->Name . '" target="_blank"><i class="kmi-twitter-square"></i></a>';
            }
            break;
        case "facebook":
            if (strpos($channelshtml, 'facebook') == false){
                $channelshtml = $channelshtml . '<a href="' . $thischannel->Url . '" title="' . $thischannel->Name . '" target="_blank"><i class="kmi-facebook-square"></i></a>';
        }
        break;
    case "official":
        if (strpos($channelshtml, 'globe') == false){
            $channelshtml = $channelshtml . '<a href="' . $thischannel->Url . '" title="' . $thischannel->Name . '" target="_blank"><i class="kmi-globe"></i></a>';
    }
    break;
case "youtube":
    if (strpos($channelshtml, 'youtube') == false){
        $channelshtml = $channelshtml . '<a href="' . $thischannel->Url . '" title="' . $thischannel->Name . '" target="_blank"><i class="kmi-youtube"></i></a>';
}
break;
case "instagram":
    if (strpos($channelshtml, 'instagram') == false){
        $channelshtml = $channelshtml . '<a href="' . $thischannel->Url . '" title="' . $thischannel->Name . '" target="_blank"><i class="kmi-instagram"></i></a>';
}
break;

}

}

return $channelshtml;
}
/*
Add options for plugin in post creation page
*/
require_once('inc/kmv-metabox.php');
/*
options include: artist viewer / album viewer
artist viewer details: top 10, bio, articles, discography
album details: articles, songs, cover
Create widget
*/
class KurrentMusic_Jukeblog_Widget extends WP_Widget {
    
    function kurrentmusic_jukeblog_widget() {
        // Instantiate the parent object
        parent::__construct( false, 'KurrentMusic Jukeblog Widget' );
    }
    
    function widget( $args, $instance ) {
        // Widget output
        extract( $args );
        $kmv_artist_name =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_artist_name', true);
        $kmv_artist_id =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_artist_id', true);
        $kmv_album_name =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_album_name', true);
        $kmv_album_id =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_album_id', true);
        $kmv_layout =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_layout', true);
        $kmv_view_type =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_view_type', true);
        
        require( 'inc/kmv-display.php' );
        
    }
    
    function update( $new_instance, $old_instance ) {
        // Save widget options
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        
        return $instance;
    }
}
function kurrentmusic_jukeblog_frontend_files() {

    wp_enqueue_style( 'kurrentmusic_jukeblog_frontend_css', plugins_url( 'kurrent-music-jukeblog/css/kmvwp.css' ) );
    wp_enqueue_style( 'kurrentmusic_jukeblog_icons_css', plugins_url( 'kurrent-music-jukeblog/icon/kmicons.css' ));
    wp_enqueue_script( 'kurrentmusic_jukeblog_rc_js', plugins_url( 'kurrent-music-jukeblog/js/kmv-rc.js' ), array('jquery'), '', true);
    wp_enqueue_style( 'kurrentmusic_jukeblog_scrollbar_css', plugins_url( 'kurrent-music-jukeblog/inc/custom-scrollbar/jquery.mCustomScrollbar.min.css' ));
    wp_enqueue_script( 'kurrentmusic_jukeblog_scrollbar_js', plugins_url( 'kurrent-music-jukeblog/inc/custom-scrollbar/jquery.mCustomScrollbar.min.js' ), array('jquery'), '', true);
    wp_enqueue_script( 'kurrentmusic_jukeblog_frontend_js', plugins_url( 'kurrent-music-jukeblog/js/kmv-frontend.js' ), array('jquery'), '', true );


}
add_action( 'wp_head', 'kurrentmusic_jukeblog_frontend_files' );

function kurrentmusic_jukeblog_register_widgets() {
    register_widget( 'KurrentMusic_Jukeblog_Widget' );
}
add_action( 'widgets_init', 'kurrentmusic_jukeblog_register_widgets' );

/*
Create shortcode
*/
function kurrentmusic_jukeblog_shortcode( $atts, $content = null ) {
    
    global $post;
    
    extract( shortcode_atts( array(
    'artist_name' => '',
    'artist_id' => '',
    'album_name' =>'',
    'album_id' =>'',
    'view_type' =>'',
    'layout' =>''
    ), $atts ) );
    
    $options = get_option( 'kurrentmusic_jukeblog' );
    
    $kmv_artist_name =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_artist_name', true);
    $kmv_artist_id =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_artist_id', true);
    $kmv_album_name =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_album_name', true);
    $kmv_album_id =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_album_id', true);
    $kmv_layout =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_layout', true);
    $kmv_view_type =  get_post_meta( get_the_ID(), 'kurrentmusic_jukeblog_view_type', true);
    
    //artist was set on shortcode, get all artist and album values from shortcode element
    if($artist_name!=''){
        $kmv_artist_name = $artist_name;
        $kmv_artist_id = $artist_id;
        $kmv_album_name = $album_name;
        $kmv_album_id = $album_id;
    }
    if($view_type!='')
    $kmv_view_type = $view_type;
    if($layout!='')
    $kmv_layout = $layout;
    ob_start();
    
    require( 'inc/kmv-display.php' );
    
    $content = ob_get_clean();
    
    return $content;
    
}
add_shortcode( 'kurrentmusic_jukeblog', 'kurrentmusic_jukeblog_shortcode' );
?>