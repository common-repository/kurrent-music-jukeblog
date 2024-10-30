function OnTogglePlayer() {
    jQuery('.kmv-toggle-songs-list').click(function () {
        if (jQuery(this).find('i').hasClass('kmi-toggle-down')) {
            jQuery(this).find('i').removeClass('kmi-toggle-down').addClass('kmi-toggle-up');
            jQuery(this).closest('.kmv-wp-widget').find('.kmv-songs-list').addClass('kmv-expanded');
        } else {
            jQuery(this).find('i').removeClass('kmi-toggle-up').addClass('kmi-toggle-down');
            jQuery(this).closest('.kmv-wp-widget').find('.kmv-songs-list').removeClass('kmv-expanded');
        }
    });
/*    jQuery(".kmv-toggle-play").click(function () {
        //stops any soundcloud players on the page that might be playing
        jQuery(".kmpc.isplaying .kmv-playbtn").click();

        console.log('clickiti');
        if (jQuery(this).find('i').hasClass('kmv-icn-play')) {
            //Pause any other previews that might be playing    
            if (jQuery(".kmv-icn-pause").length > 0) {
                jQuery(".kmv-icn-pause").removeClass("kmv-icn-pause kmi-km-pause").addClass("kmv-icn-play kmi-km-play").closest(".kmv-preview-player").find("video")[0].pause();
            }

            jQuery(".kmv-toggle-play i.kmv-icn-pause").removeClass("kmv-icn-pause kmi-km-pause").addClass("kmv-icn-play kmi-km-play");
            jQuery(this).closest(".kmv-preview-player").find("video")[0].play();
            jQuery(this).find('i').removeClass("kmv-icn-play kmi-km-play").addClass("kmv-icn-pause kmi-km-pause");
        } else {
            jQuery(this).closest(".kmv-preview-player").find("video")[0].pause();
            jQuery(this).find('i').removeClass("kmv-icn-pause kmi-km-pause").addClass("kmv-icn-play kmi-km-play");
        }
    });
    jQuery(".kmv-preview-player video").on('ended', function () {
        jQuery(this).closest(".kmv-preview-player").find('.kmv-toggle-play i').removeClass('kmv-icn-pause kmi-km-pause').addClass('kmv-icn-play kmi-km-play');
    })
*/
};
/*
function kmvGetVideoResults(spotid) {
    var searchVideos = km_root_host + '/dataentry/search/KurrentMusic/Videos';

    jQuery.ajax({
        url: (searchVideos),
        type: 'GET',
        contentType: 'application/json',
        dataType: 'json',
        data: {
            t: km_api_key,
            action: "lookUp",
            "searchdata": JSON.stringify({ "SpotifyArtistId": spotid }),
            "returnfields": "Id,UserId,Username,Title,MainImage,Artist,ArtistImage,Album,AlbumImage,Song,Genre,SpotifyArtistId,SpotifyAlbumId,YouTubeId,URLId,Views,DefaultPlay,CreatedOn"
        },
        success: function (result) {
            var output = '';
            jQuery('.kmv-videos .loading').remove();
            if (result.length <= 0)
                jQuery('.kmv-videos').remove();
            var Defaultfound = false, titleadded = false;
            if (result.length <= 0)
                jQuery('.kmv-artist-videos').hide();
            var currvideo = 0, indexplayvideo = 0;
            result.forEach(function (videoItem) {
                if (!titleadded) {
                    jQuery('.kmv-artist-videos').prepend('<h4>Latest Videos</h4>');
                    titleadded = true;
                }
                if (videoItem.DefaultPlay)
                    indexplayvideo = currvideo;
                var videohtml;
                videohtml = '<li id="km-video-' + videoItem.Id + '"><div class="kmv-video-title">' + videoItem.Title + '</div><div class="kmv-video-image"><img src="http://img.youtube.com/vi/' + videoItem.YouTubeId + '/0.jpg" alt="" /></div></li>';
                jQuery('.kmv-videos-list').append(videohtml);
                jQuery('#km-video-' + videoItem.Id).data('km-video', videoItem);

            });
            jQuery('.videos-list li').click(function (e) {
                var videoItem = jQuery(this).data('km-video');
                var youtubeid = videoItem.YouTubeId;
                if (!isMobile) {
                    jQuery('#video-container').html('');
                    mpp = jQuery("#video-container").pPlayer({
                        youtubeVideoId: youtubeid,
                        autoplay: 0,
                        share: 0,
                        origin: ""
                    });
                    jQuery('.kmv-videos-list li.active').removeClass('active');
                    jQuery(this).addClass('active');
                } else {
                    window.location = 'https://www.youtube.com/watch?v=' + youtubeid;
                }


            });
            if (!isMobile) {
                jQuery('.kmv-videos-list li').eq(indexplayvideo).click();
            }

        }
    });
}
*/
function kmvInitAlbumUI() {
    jQuery('.kmv-albums-list li').click(function (e) {

        e.preventDefault();
        if (jQuery(this).closest('.kmv-wp-widget').hasClass('small')) {
            window.location = 'http://www.kurrentmusic.com/artistalbum.html#!artist-id=' + jQuery(this).attr('km-artistid') + '&album-id=' + jQuery(this).attr('km-albumid') + '&artist-name=' + jQuery(this).attr('km-artistname') + '&artist-album=' + jQuery(this).attr('km-albumname');
        } else {
            jQuery(this).closest('.kmv-albums-list').find('li.active').removeClass('active');
            jQuery(this).addClass('active');
            jQuery(this).closest('.kmv-discography').find('.kmv-selected-album .kmv-songs-list').html('');
            jQuery(this).closest('.kmv-discography').find('.kmv-selected-album .kmv-album-name').html('<a href="http://kurrentmusic.com/artistalbum.html?artistid=' + jQuery(this).attr('km-artistid') + '&albumid=' + jQuery(this).attr('km-albumid') + '&artistname=' + jQuery(this).attr('km-artistname') + '&artistalbum=' + jQuery(this).attr('km-albumname') + '">' + jQuery(this).attr('km-albumname') + '</a>');

            kmvLoadAlbumInfo(jQuery(this));
        }
    });
    jQuery('.kmv-albums li').eq(0).click();

}
function pad(num, size) {
    var s = num + "";
    while (s.length < size) s = "0" + s;
    return s;
}

function kmvLoadAlbumInfo(elem) {
    albumid = elem.attr('km-albumid');
    albumname = elem.attr('km-albumname');
    var searchURL = km_root_host + '/data/music/albumdetailsspotify/details';
    jQuery.ajax({
        data: {
            t: km_api_key,
            action: "lookUp",
            context: albumid
        },
        url: (searchURL),
        success: function (resultAlbum) {
            resultAlbum.forEach(function (albumItem) {

                var songsURL = km_root_host + '/data/music/albumdetailsspotify/songs';

                jQuery.ajax({
                    data: {
                        t: km_api_key,
                        action: "lookUp",
                        context: albumid
                    },
                    url: (songsURL),
                    success: function (resultSongs) {
                        var outputsongs = "";
                        
                        resultSongs.forEach(function (songItem) {
                            var time = parseInt(songItem.Duration / 1000);
                            var minutes = Math.floor(time / 60);
                            var seconds = time - minutes * 60;
                            var duration = pad(minutes, 2) + ':' + pad(seconds, 2);

                            outputsongs += '<li song-name="' + songItem.Name + '" track-number="' + songItem.TrackNumber + '"><p>' + songItem.TrackNumber + '. ' + songItem.Name + ' <span class="duration">( ' + duration + ' )</span> </p></li>';


                        });
                        console.log(outputsongs);
                        elem.closest('.kmv-discography').find(".kmv-songs-list").html(outputsongs);
                        OnTogglePlayer();
                    }
                });
            });
        },
        error: function (error, msg) {
            console.log('error encountered.');
        }
    });

}
function KMVCheckIfSidebar(){
    jQuery(".kmv-wp-widget").each(function(index){
        if(jQuery(this).closest('.widget-area').hasClass('sidebar')){
            jQuery(this).addClass('widget widget_text');
        }
    });
}

function KMVResizeImages(){
    console.log('hello');
    jQuery('.kmv-artist-image').each(function(index, value){
        jQuery(this).height(jQuery(this).width());
    });
    jQuery('.kmv-album-image').each(function(index, value){
        jQuery(this).height(jQuery(this).width());
    });
     jQuery('li.kmv-albumItem').each(function(index, value){
        jQuery(this).height(jQuery(this).width());
    });
    //jQuery('li.kmv-albumItem').height(jQuery('li.kmv-albumItem').width());
    //jQuery('.kmv-albums-list .album-image')

}
function KMVLoadResponsiveSelector(){
    var els = document.querySelectorAll(".kmv-wp-widget");
   
    SelectorQueries.add(els, "min-width", "460px", "wide");
    SelectorQueries.add(els, "max-width", "350px", "small");
    SelectorQueries.ignoreDataAttributes();
}
jQuery(window).resize(function(){
 //   KMVResizeImages();
});
jQuery(document).ready(function(){
    OnTogglePlayer();
  
    kmvInitAlbumUI();
    KMVCheckIfSidebar();
    var loadrc = window.setTimeout(KMVLoadResponsiveSelector, 500);
    
    jQuery('.kmv-albums-list-container').mCustomScrollbar({'scrollbarPosition': 'outside', 'theme': 'minimal', 'scrollTo':'.songs-list li.active', 'advanced':{ 'updateOnContentResize': true }}); //, 'advanced':{ 'updateOnContentResize': true }
 //   KMVResizeImages();
})


