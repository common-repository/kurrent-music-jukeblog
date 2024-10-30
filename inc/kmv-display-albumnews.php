<?php 

 $thisalbum = kmvGetAlbumInfo($kmv_album_name, $kmv_album_id);
 $thisalbum->Artist = kmvGetArtistInfo($kmv_artist_name, $kmv_artist_id);
 $thisalbum = kmvGetOtherAlbumNews($thisalbum);
 $channelshtml = kmvGetSocialChannels($thisalbum->Artist);

 ?>
 <div  class="kmv-wp-widget kmv-hidden kmv-wp-widget-albumviewer textwidget" style="display:none;" data-squery="min-width:460px=wide max-width:350px=small">
  <div class="kmv-album-cover">
  	<div class="kmv-image-blur-container"><svg class="kmv-canvas-blur"> <image xlink:href="<?php echo $thisalbum->Image ?>" x="0" y="0" width="100%" height="100%" preserveAspectRatio="none"/></svg></div>
   <!-- <div class="kmv-image-blur"><img src="<?php echo $thisalbum->Image ?>" class="kmv-bg-image"></div> -->
    <div class="kmv-content">
	    <div class="kmv-album-image" style="background-image: url(<?php echo $thisalbum->Image ?>)"><img src="<?php echo $thisalbum->Image ?>" alt="<?php echo $thisalbum->Name ?> Cover Image" /></div>
	      <div class="kmv-album-heading">
	    	  
	          <h3 class="kmv-album-name"><?php echo $thisalbum->Name ?></h3>  
	          <div class="kmv-artist-info">
            	<p>by <a class="kmv-artist-name" href="http://kurrentmusic.com/artistinfo.html#!artist-name=<?php echo $thisalbum->Artist->Name?>&artist-id=<?php echo $thisalbum->Artist->Id ?>"><?php echo $thisalbum->Artist->Name ?></a></p>
            	<div class="kmv-social-channels"><?php echo $channelshtml ?></div>
				<div class="kmv-copyright"><p><?php echo $thisalbum->Copyright ?></p></div>
		      </div>
	      </div>
	       <div class="kmv-releaseinfo"><h6>Released</h6><p><?php echo $thisalbum->ReleaseDate ?></p></div>
	      <?php if(count($thisalbum->OtherNews)>0){
	     ?>
			<div class="kmv-blogs-container">
		        <h6>Album News</h6>
		        <div class="kmv-albums-blogs-container">
		          <div class="kmv-blogs-list">

		            <ul>
		              <?php
		              $kmvmaxblogs=5;
		              for($i=0; $i < count($thisalbum->OtherNews) && $i<$kmvmaxblogs; $i++){
		              	$thisblog = $thisalbum->OtherNews[$i];
		                echo '<li class="kmv-blog-primary"><a class="titlelink" href="' . $thisblog->URL . '">'. $thisblog->Title .'</a><div class="kmv-blogbody">'. $thisblog->Description .'</div><div class="authoringinfo"><p class="date">' . $thisblog->Date . '</p></div></li>';
		               
		               } 
		               ?>
		            </ul>
		          </div>
		        </div>
		      </div>
		      <?php }
		      else{
		      	 echo '<p class="kmv-blog-noblog">No articles found for this album.</p>';
			 } ?>
	    </div>
	</div>
    <div class="kmv-branding">Provided by Kurrent Music</div>
  </div>
    <div class="kmv-blur-assist">
    <svg class="kmv-svg-effects">
        <filter id="kmv-blur-effect-1">
            <feGaussianBlur stdDeviation="5" />
        </filter>

        <filter id="kmv-blur-effect-2">
            <feGaussianBlur stdDeviation="30" />
        </filter>
    </svg> 
  </div>