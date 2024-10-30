<?php 
 
 $thisartist = kmvGetArtistInfo($kmv_artist_name, $kmv_artist_id);
 $topsongs = kmvGetArtistTop10Songs($thisartist);
 $channelshtml = kmvGetSocialChannels($thisartist);
 ?>
 <div class="kmv-wp-widget kmv-hidden kmv-wp-widget-artistviewer textwidget" style="display:none;" data-squery="min-width:460px=wide max-width:350px=small">
  <div class="kmv-top10">
    <div class="kmv-image-blur-container"><svg class="kmv-canvas-blur"> <image xlink:href="<?php echo $thisartist->Image ?>" x="0" y="0" width="100%" height="100%" preserveAspectRatio="none"/></svg></div>
      <!--<div class="kmv-image-blur"><img src="<?php echo $thisartist->Image ?>" class="kmv-bg-image"></div>-->
      <div class="kmv-content">
        <div class="kmv-artist-details">
            <div class="kmv-artist-image" style="background-image: url(<?php echo $thisartist->Image ?>)"><img src="<?php echo $thisartist->Image ?>" alt="<?php echo $thisartist->Name ?> Image" /></div>
            <h3 class="kmv-artist-name"><a class="kmv-artist-name" href="http://kurrentmusic.com/artistinfo.html?artist-name=<?php echo $thisartist->Name?>&artist-id=<?php echo $thisartist->Id ?>"><?php echo $thisartist->Name ?></a></h3>  
            <div class="kmv-social-channels"><?php echo $channelshtml ?></div>
        </div>
        <div class="kmv-top10">
          <h6>Top Songs</h6>
          <div class="kmv-top10-list-container">
            <div class="kmv-top10-list">

              <ol class="kmv-songs-list">
                <?php
                foreach ($topsongs as &$thissong){
                  echo '<li><p class="kmv-top10-songname">' . $thissong->SongName . ' </p><p class="kmv-top10-albumname">' . $thissong->AlbumName . '</p></li>';
                 } 
               
                 ?>
              </ol>
            </div>
          </div>
          
        </div>
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