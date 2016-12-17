<?php $path_site2 = plugins_url("./images", __FILE__); ?>
	<div class="free_version_banner" <?php if( isset($_COOKIE['hgSliderFreeBannerShow']) && isset($_COOKIE['hgSliderFreeBannerShow']) == "no" ){ echo 'style="display:none"'; } ?> >
		<a class="close_free_banner">+</a>
		<img class="manual_icon" src="<?php echo $path_site2; ?>/icon-user-manual.png" alt="user manual" />
		<p class="usermanual_text">If you have any difficulties in using the options, Follow the link to <a href="http://huge-it.com/wordpress-google-map-user-manual/" target="_blank">User Manual</a></p>
		<a class="get_full_version" href="http://huge-it.com/google-map/" target="_blank">GET THE FULL VERSION</a>
		<a href="http://huge-it.com" target="_blank"><img class="huge_it_logo" src="<?php echo $path_site2; ?>/Huge-It-logo.png"/></a>
		<div style="clear: both;"></div>
		<div class="hg_social_link_buttons">
			<a target="_blank" class="fb" href="https://twitter.com/HugeITcom"></a>
			<a target="_blank" class="twitter"  href="https://www.youtube.com/channel/UCueCH_ulkgQZhSuc0L5rS5Q"></a>
			<a target="_blank" class="gplus" href="https://www.facebook.com/hugeit/"></a>
			<a target="_blank" class="yt"  href="https://plus.google.com/111845940220835549549"></a>
		</div>
		<div class="hg_view_plugins_block">
			<a target="_blank"  href="https://wordpress.org/support/view/plugin-reviews/google-maps">Rate Us</a>
			<a target="_blank"  href="http://huge-it.com/wordpress-plugins-google-map-layers/">Full Demo</a>
			<a target="_blank"  href="http://huge-it.com/wordpress-google-map-faq/">FAQ</a>
			<a target="_blank"  href="http://huge-it.com/contact-us/">Contact Us</a>
		</div>
		<div  class="description_text"><p>This is the LITE version of the plugin. Click "GET THE FULL VERSION" for more advanced options. We appreciate every customer.</p></div>
		<div style="clear: both;"></div>
	</div>
<script>
	jQuery(".close_free_banner").click(function(){
		jQuery(".free_version_banner").fadeOut();
		hgSliderSetCookie( 'hgSliderFreeBannerShow', 'no', {expires:3600} );
	});
	function hgSliderSetCookie(name, value, options) {
		options = options || {};
		var expires = options.expires;
		if (typeof expires == "number" && expires) {
			var d = new Date();
			d.setTime(d.getTime() + expires * 1000);
			expires = options.expires = d;
		}
		if (expires && expires.toUTCString) {
			options.expires = expires.toUTCString();
		}
		if(typeof value == "object"){
			value = JSON.stringify(value);
		}
		value = encodeURIComponent(value);
		var updatedCookie = name + "=" + value;
		for (var propName in options) {
			updatedCookie += "; " + propName;
			var propValue = options[propName];
			if (propValue !== true) {
				updatedCookie += "=" + propValue;
			}
		}
		document.cookie = updatedCookie;
	}
</script>
