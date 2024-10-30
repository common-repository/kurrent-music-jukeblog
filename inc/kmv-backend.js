
jQuery(document).ready(function(){
	
	KurrentMusicJukeblog_UpdateLayout();
	jQuery("input[type=radio][name=kurrentmusic_jukeblog_view_type]").change(function(e){
		
		KurrentMusicJukeblog_UpdateLayout();
	});
	var loadartisttimer;
	jQuery("#kurrentmusic_jukeblog_artist_name").keypress(function(){
		window.clearTimeout(loadartisttimer);
		loadartistimer = window.setTimeout(KurrentMusicJukeblog_LoadArtistList,1500);
		
	});
	KurrentMusicJukeblog_LoadArtistList();
	function KurrentMusicJukeblog_LoadArtistList(){
		//Gets value from artist input box and loads the list of artists found based on that term

		var kmv_sel_viewtype = jQuery("input[type=radio][name=kurrentmusic_jukeblog_view_type]:checked").val();
		var kmv_sel_artist = jQuery('#kurrentmusic_jukeblog_artist_name').val();
		var searchArtists = km_root_host +  "/data/music/artistsearchspotifyplus/searchspotifyplus";
		var myparent = jQuery("#kurrentmusic_jukeblog_artist_name").closest('.layers-form-item');
		console.log(myparent);
		if(kmv_sel_artist!='' && kmv_sel_artist!=null && kmv_sel_artist!=undefined){
			jQuery.ajax({
				data: {
					action: 'lookUp',
					t: km_api_key,
					context: kmv_sel_artist, 
				},
				url: (searchArtists),
				success: function (result) {
					var artists = [];
					var htmlartistselect = '';
					result.forEach(function (thisItem) {
						if(thisItem.Image != null){
							artists.push(thisItem);
							htmlartistselect += '<option value="'+thisItem.Id+'">' + thisItem.Name +'</option>';
						}
					});
					if(jQuery('#kurrentmusic_jukeblog_artist_options').length==0){
						//Load Artist Select box
						myparent.append('<div class="dashicons dashicons-update" id="kurrentmusic_jukeblog_update_artist"></div><select name="kurrentmusic_jukeblog_artist_options" id="kurrentmusic_jukeblog_artist_options"></select>');
					}
					jQuery('#kurrentmusic_jukeblog_artist_options').html(htmlartistselect);
					

					
					
					jQuery("#kurrentmusic_jukeblog_artist_options").unbind();
					jQuery("#kurrentmusic_jukeblog_artist_options").change(function(e){
						e.preventDefault();
						KurrentMusicJukeblog_UpdateSelectedArtist();

					});
					jQuery("#kurrentmusic_jukeblog_artist_options option").eq(0).attr('selected', true);
					jQuery('#kurrentmusic_jukeblog_update_artist').click(function(e){
						e.preventDefault();
						KurrentMusicJukeblog_LoadArtistList();
					});
					
					KurrentMusicJukeblog_UpdateSelectedArtist();
					console.log(artists);
				},
		         error: function (error) {
		         	console.log(error);
		         }
			});
		}
	}
	function KurrentMusicJukeblog_UpdateSelectedArtist(){

		jQuery("#kurrentmusic_jukeblog_artist_name").val(jQuery('#kurrentmusic_jukeblog_artist_options option:selected').text());
		jQuery("#kurrentmusic_jukeblog_artist_id").val(jQuery('#kurrentmusic_jukeblog_artist_options').val());
		KurrentMusicJukeblog_LoadArtistAlbums();
	}
	function KurrentMusicJukeblog_LoadArtistAlbums(){
		var artistid = jQuery('#kurrentmusic_jukeblog_artist_options').val();
		if(artistid!=null && artistid!=undefined && artistid !=''){
			var albumsURL = km_root_host + "/data/music/artistalbumsspotify/albums";
            jQuery.ajax({
				data: {
					action: "lookUp",
					t: km_api_key,
					context: artistid
				},
                url: (albumsURL),
                success: function (result) {
                	var albums = [];
					var htmlalbumselect = '';
					result.forEach(function (thisItem) {
						if(thisItem.Image != null){
							albums.push(thisItem);
							htmlalbumselect += '<option value="'+thisItem.Id+'">' + thisItem.Name +'</option>';
						}
					});
                	if(jQuery('#kurrentmusic_jukeblog_album_options').length==0){
                		jQuery('#kurrentmusic_jukeblog_album_name').closest('.layers-form-item').append('<select name="kurrentmusic_jukeblog_album_options" id="kurrentmusic_jukeblog_album_options"></select>');
                	}
                	jQuery('#kurrentmusic_jukeblog_album_options').html(htmlalbumselect);
                	
                	jQuery('#kurrentmusic_jukeblog_album_options').change(function(e){

                		e.preventDefault();
                		KurrentMusicJukeblog_UpdateSelectedAlbum();
                	});
                	if(jQuery('#kurrentmusic_jukeblog_album_id').val()!='' && jQuery('#kurrentmusic_jukeblog_album_options option[value=' + jQuery('#kurrentmusic_jukeblog_album_id').val() + ']').length>0){
                		jQuery('#kurrentmusic_jukeblog_album_options option[value=' + jQuery('#kurrentmusic_jukeblog_album_id').val() + ']').attr('selected', true);
            		}else{
                		jQuery("#kurrentmusic_jukeblog_album_options option").eq(0).attr('selected', true);
                	}
                	KurrentMusicJukeblog_UpdateSelectedAlbum();
                },
                error: function(error){
                	console.log(error);
                }
            });
		}

		
	}
	function KurrentMusicJukeblog_UpdateSelectedAlbum(){
		jQuery('#kurrentmusic_jukeblog_album_name_label').remove();
		jQuery("#kurrentmusic_jukeblog_album_name").val(jQuery("#kurrentmusic_jukeblog_album_options option:selected").text());
        jQuery("#kurrentmusic_jukeblog_album_id").val(jQuery("#kurrentmusic_jukeblog_album_options").val());
	}
	function KurrentMusicJukeblog_UpdateLayout(){
		
		var kmv_sel_viewtype = jQuery("input[type=radio][name=kurrentmusic_jukeblog_view_type]:checked").val();
		console.log(kmv_sel_viewtype);
		if(kmv_sel_viewtype=='album'){
			jQuery(".kmv_artist_settings").hide();
			jQuery(".kmv_album_settings").show();
		}else{
			jQuery(".kmv_album_settings").hide();
			jQuery(".kmv_artist_settings").show();
		}
	}
});