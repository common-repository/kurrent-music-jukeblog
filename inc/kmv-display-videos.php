<?="Album Artist Videos here";
 $thisartist = kmvGetArtistInfo($kmv_artist_name, $kmv_artist_id);
$videos = kmvGetArtistVideos($thisartist);
 ?>
 <section class="kmv-videos kmv-hidden" style="display:none;">
	<div class="content-container"><div class="loading"><i class="fa fa-spin fa-circle-o-notch"></i> Loading...</div><div class="content"><div class="kmv-videos-list-container"><ul class="kmv-videos-list"></ul></div><div class="kmv-selected-video"> <div id="video-container" class="kmv-video-container"></div></div></div></div>
</section>
