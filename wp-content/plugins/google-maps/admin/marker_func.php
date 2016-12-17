<?php
function marker_js( $id ) {
	global $wpdb;
	$id = intval($id );
	$sql = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "g_maps WHERE id=%s", $id );
	$map = $wpdb->get_results( $sql );
	foreach ( $map as $map ) {
		?>
		<script>

			var data;
			var marker = [];
			var infowindow = [];
			var newmarker;
			var geocoder;
			var markeredit;

			jQuery(document).ready(function () {
				loadMarkerMap("<?php echo $map->id; ?>", "#<?php echo $map->styling_hue; ?>", "<?php echo $map->styling_saturation; ?>", "<?php echo $map->styling_lightness; ?>", "<?php echo $map->styling_gamma; ?>", "<?php echo $map->zoom; ?>", "<?php echo $map->type; ?>", "<?php echo $map->bike_layer; ?>", "<?php echo $map->traffic_layer; ?>", "<?php echo $map->transit_layer; ?>");

			})
			function loadMarkerMap(id, hue, saturation, lightness, gamma, zoom, type, bike, traffic, transit) {
				data = {
					action: 'g_map_options',
					map_id: id,
					task: "ajax",
				}
				jQuery.ajax({
					url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
					dataType: 'json',
					method: 'post',
					data: data,
					beforeSend: function () {
					}
				}).done(function (response) {
					HGinitializeMarkerMap(response);
				}).fail(function () {
					console.log('Failed to load response from database');
				});
				function HGinitializeMarkerMap(response) {
					if (response.success) {
						var mapInfo = response.success;
						var maps = mapInfo.maps;
						for (var i = 0; i < maps.length; i++) {
							var trafficLayer = new google.maps.TrafficLayer();
							var trafficLayer1 = new google.maps.TrafficLayer();
							var bikeLayer = new google.maps.BicyclingLayer();
							var bikeLayer1 = new google.maps.BicyclingLayer();
							var transitLayer = new google.maps.TransitLayer();
							var transitLayer1 = new google.maps.TransitLayer();
							var mapcenter = new google.maps.LatLng(
								parseFloat(maps[i].center_lat),
								parseFloat(maps[i].center_lng));
							var styles = [
								{
									stylers: [
										{hue: hue},
										{saturation: saturation},
										{lightness: lightness},
										{gamma: gamma},
									]
								}
							]
							var mapOptions = {
								zoom: parseInt(zoom),
								center: mapcenter,
								styles: styles,
							}
							map = new google.maps.Map(document.getElementById('g_map_marker'), mapOptions);
							map_marker_edit = new google.maps.Map(document.getElementById('g_map_marker_edit'), mapOptions);
							jQuery("#marker_pic").on("change", function () {
								jQuery("#marker_pic_name").val(jQuery(this).val());
							})

							var input_marker = document.getElementById("marker_location");
							var autocomplete_marker = new google.maps.places.Autocomplete(input_marker);
							google.maps.event.addListener(autocomplete_marker, 'place_changed', function () {

								var addr = jQuery("#marker_location").val();
								geocoder = new google.maps.Geocoder();
								geocoder.geocode({'address': addr}, function (results, status) {
									if (newmarker) {
										newmarker.setPosition(results[0].geometry.location)
									}
									else {
										placeMarker(results[0].geometry.location)
									}
									map.setCenter(results[0].geometry.location);
									updateMarkerInputs(results[0].geometry.location);
								});
							});
							//jQuery("#marker_add_button").off("click");
							jQuery("#marker_add_button").on("click", function () {
								jQuery(".marker_image_choose").find(".active").removeClass("active");
								jQuery("#marker_location").val("");
								jQuery("#marker_location_lat").val("");
								jQuery("#marker_location_lng").val("");
								google.maps.event.trigger(map, 'resize');
								map.setCenter(mapcenter);
								if (newmarker) {
									newmarker.setMap(null);
								}
							})
							if (type == "ROADMAP") {
								map.setOptions({mapTypeId: google.maps.MapTypeId.ROADMAP})
								map_marker_edit.setOptions({mapTypeId: google.maps.MapTypeId.ROADMAP})
							}
							if (type == "SATELLITE") {
								map.setOptions({mapTypeId: google.maps.MapTypeId.SATELLITE});
								map_marker_edit.setOptions({mapTypeId: google.maps.MapTypeId.SATELLITE});
							}
							if (type == "HYBRID") {
								map.setOptions({mapTypeId: google.maps.MapTypeId.HYBRID});
								map_marker_edit.setOptions({mapTypeId: google.maps.MapTypeId.HYBRID});
							}
							if (type == "TERRAIN") {
								map.setOptions({mapTypeId: google.maps.MapTypeId.TERRAIN});
								map_marker_edit.setOptions({mapTypeId: google.maps.MapTypeId.TERRAIN});
							}

							if (bike == "true") {
								bikeLayer.setMap(map);
								bikeLayer1.setMap(map_marker_edit);
							}
							if (traffic == "true") {
								trafficLayer.setMap(map);
								trafficLayer1.setMap(map_marker_edit);
							}
							if (transit == "true") {
								transitLayer.setMap(map);
								transitLayer1.setMap(map_marker_edit);
							}

							google.maps.event.addListener(map, 'rightclick', function (event) {
								placeMarker(event.latLng);
								updateMarkerInputs(event.latLng);
							});

							jQuery("#marker_animation").off("change");
							jQuery("#marker_animation").on("change", function () {
								var animation = jQuery(this).val();
								if (newmarker) {
									if (animation == "BOUNCE") {
										newmarker.setAnimation(google.maps.Animation.BOUNCE)
									}
									if (animation == "DROP") {
										newmarker.setAnimation(google.maps.Animation.DROP)
									}
									if (animation == "NONE") {
										newmarker.setAnimation(null);
									}
								}
							})
							jQuery("#marker_pic").off("change");
							jQuery("#marker_pic").on("change", function () {
								var name = jQuery(this).val();
								jQuery(this).parent().parent().find("#marker_image_choose").find(".active").removeClass("active");
								if (name != "") {
									newmarker.setIcon(name);
								}
							})
							jQuery(".marker_image_choose_button").on("click", function () {
								var name = jQuery(this).val();
								var size = jQuery("#marker_image_size").val();
								jQuery("#marker_edit_pic").val("");
								if (newmarker) {
									if (name == 'default') {
										newmarker.setIcon(null);
									} else {
										newmarker.setIcon("<?php echo plugins_url( "google-map-wp/images/icons/" ); ?>" + name + "" + size + ".png");
									}
								}
							})
							jQuery("#marker_image_size").off("change");
							jQuery("#marker_image_size").on("change", function () {

								var size = jQuery(this).val();
								var name = jQuery(".marker_image_choose_button").parent().parent().find(".active").find(".marker_image_choose_button").val();
								if (name != undefined) {
									if (newmarker) {
										newmarker.setIcon("<?php echo plugins_url( "google-map-wp/images/icons/" ); ?>" + name + "" + size + ".png");
									}
								}
								else {
									var imageurl = jQuery("#marker_pic").val();
									if (imageurl != "") {
										var imagename = imageurl.match(/.*\/([^/]+)\.([^?]+)/i)[1];
										setIconImage(imagename, imageurl);
									}
								}
							})


							jQuery("#marker_location_lat #marker_location_lng").off("change");
							jQuery("#marker_location_lat #marker_location_lng").on("change", function () {
								var lat = parseFloat(jQuery("#marker_location_lat").val());
								var lng = parseFloat(jQuery("#marker_location_lng").val());
								var position = new google.maps.LatLng(lat, lng);
								placeMarker(position);

							})
							var markers = mapInfo.markers;

							var custom_uploader;
							jQuery('#upload_marker_pic').off("click");
							jQuery('#upload_marker_pic').click(function (e) {

								e.preventDefault();

								//If the uploader object has already been created, reopen the dialog
								if (custom_uploader) {
									custom_uploader.open();
									return;
								}

								//Extend the wp.media object
								custom_uploader = wp.media.frames.file_frame = wp.media({
									title: 'Choose file',
									button: {
										text: 'Choose file'
									},
									multiple: false
								});

								//When a file is selected, grab the URL and set it as the text field's value
								custom_uploader.on('select', function () {
									attachment = custom_uploader.state().get('selection').first().toJSON();
									/*function dump(attachment) {
									 var out = '';
									 for (var i in attachment) {
									 out += i + ": " + attachment[i] + "\n";
									 }
									 alert(out);
									 }
									 dump(attachment);*/

									jQuery('#marker_pic').val(attachment.url);
									jQuery('#marker_pic_url').val(attachment.url);
									jQuery(".marker_image_choose").find(".active").removeClass("active");
									setIconImage(attachment.filename.split(",")[0], attachment.url);
								});


								custom_uploader.open();
							});

							var custom_uploader1;
							jQuery('#upload_edit_marker_pic').off("click");
							jQuery('#upload_edit_marker_pic').click(function (e) {

								e.preventDefault();

								//If the uploader object has already been created, reopen the dialog
								if (custom_uploader1) {
									custom_uploader1.open();
									return;
								}

								//Extend the wp.media object
								custom_uploader1 = wp.media.frames.file_frame = wp.media({
									title: 'Choose file',
									button: {
										text: 'Choose file'
									},
									multiple: false
								});

								//When a file is selected, grab the URL and set it as the text field's value
								custom_uploader1.on('select', function () {
									attachment = custom_uploader1.state().get('selection').first().toJSON();
									/*function dump(attachment) {
									 var out = '';
									 for (var i in attachment) {
									 out += i + ": " + attachment[i] + "\n";
									 }
									 alert(out);
									 }
									 dump(attachment);*/

									jQuery('#marker_edit_pic').val(attachment.url);
									jQuery('#marker_edit_pic_url').val(attachment.url);
									jQuery(".marker_image_choose").find(".active").removeClass("active");
									setIconImage(attachment.filename.split(".")[0], attachment.url);
								});
								custom_uploader1.open();

							});
							jQuery(".edit_marker_list_delete a").off("click");
							jQuery(".edit_marker_list_delete a").on("click", function () {
								jQuery("#marker_edit_pic").on("change", function () {
									jQuery("#marker_edit_pic_name").val(jQuery(this).val());
								})
								var parent = jQuery(this).parent();
								var idelement = parent.find(".marker_edit_id");
								var markerid = idelement.val();
								jQuery("#g_maps > div").not("#g_map_polygon").addClass("hide");
								jQuery("#g_map_marker_edit").removeClass("hide");
								jQuery("#markers_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
								jQuery(this).parentsUntil(".editing_section").find(".update_list_item").show(200).addClass("tab_options_active_section");
								jQuery("#marker_add_button").hide(200).addClass("tab_options_hidden_section");
								jQuery("#marker_get_id").val(markerid);
								var markers = mapInfo.markers;
								for (var y = 0; y < markers.length; y++) {
									var id = markers[y].id;
									if (markerid == id) {
										var name = markers[y].name;
										var address = markers[y].address;
										var anim = markers[y].animation;
										var description = markers[y].description;
										var markimg = markers[y].img;

										jQuery("#marker_edit_pic_name").val(markimg);
										jQuery(".marker_image_choose").find(".active").removeClass("active");
										var filename = markimg.substring(markimg.lastIndexOf("/") + 1, markimg.lastIndexOf("."));
										filename = filename.replace(/[0-9]/g, '');
										var activeIcon = jQuery(".marker_image_choose_button").parent().parent().find(".marker_image_change_" + filename);
										activeIcon.attr("checked", "checked");
										activeIcon.parent().addClass("active");


										var lat = markers[y].lat;
										var lng = markers[y].lng;
										var img_size = markers[y].size;
										if (img_size == "16") {
											jQuery("#image_edit_size_16").attr("selected", "selected")
										}
										if (img_size == "24") {
											jQuery("#image_edit_size_24").attr("selected", "selected")
										}
										if (img_size == "48") {
											jQuery("#image_edit_size_48").attr("selected", "selected")
										}
										if (img_size == "64") {
											jQuery("#image_edit_size_64").attr("selected", "selected")
										}
										if (img_size == "256") {
											jQuery("#image_edit_size_256").attr("selected", "selected")
										}
										var point = new google.maps.LatLng(
											parseFloat(markers[y].lat),
											parseFloat(markers[y].lng));


										map_marker_edit.setCenter(point);


										google.maps.event.trigger(map_marker_edit, 'resize');
										map_marker_edit.setCenter(point);
										jQuery("#marker_edit_location_lat").val(lat);
										jQuery("#marker_edit_location_lng").val(lng);
										jQuery("#marker_edit_animation").val(anim);
										jQuery("#marker_edit_title").val(name);
										jQuery("#marker_edit_description").val(description);
										if (markeredit) {
											markeredit.setMap(null);
										}
										if (anim == 'DROP') {
											markeredit = new google.maps.Marker({
												map: map_marker_edit,
												position: point,
												title: name,
												icon: markimg,
												content: description,
												animation: google.maps.Animation.DROP,
												draggable: true
											});
										}
										if (anim == 'BOUNCE') {
											markeredit = new google.maps.Marker({
												map: map_marker_edit,
												position: point,
												title: name,
												content: description,
												icon: markimg,
												animation: google.maps.Animation.BOUNCE,
												draggable: true
											});
										}
										if (anim == 'NONE') {
											markeredit = new google.maps.Marker({
												map: map_marker_edit,
												position: point,
												icon: markimg,
												content: description,
												title: name,
												draggable: true
											});
										}
										google.maps.event.addListener(map_marker_edit, 'rightclick', function (event) {
											if (markeredit) {
												markeredit.setPosition(event.latLng);
											}
											updateMarkerEditInputs(event.latLng);
										});

										google.maps.event.addListener(markeredit, 'drag', function (event) {

											updateMarkerEditInputs(event.latLng);
										});

										var input_edit_marker = document.getElementById("marker_edit_location");
										var autocomplete_edit_marker = new google.maps.places.Autocomplete(input_edit_marker);
										google.maps.event.addListener(autocomplete_edit_marker, 'place_changed', function () {

											var addr = jQuery("#marker_edit_location").val();
											geocoder = new google.maps.Geocoder();
											geocoder.geocode({'address': addr}, function (results, status) {
												if (markeredit) {
													markeredit.setPosition(results[0].geometry.location)
												}
												map_marker_edit.setCenter(results[0].geometry.location);
												updateMarkerEditInputs(results[0].geometry.location);
											})
										})

										updateMarkerEditInputs(markeredit.getPosition());
									}
								}
								google.maps.event.trigger(map_marker_edit, 'resize');
								jQuery("#marker_edit_animation").off("change");
								jQuery("#marker_edit_animation").on("change", function () {
									var animation = jQuery(this).val();
									if (markeredit) {
										if (animation == "BOUNCE") {
											markeredit.setAnimation(google.maps.Animation.BOUNCE)
										}
										if (animation == "DROP") {
											markeredit.setAnimation(google.maps.Animation.DROP)
										}
										if (animation == "NONE") {
											markeredit.setAnimation(null);
										}
									}
								})
								jQuery("#marker_edit_pic").off("change");
								jQuery("#marker_edit_pic").on("change", function () {
									var name = jQuery(this).val();
									jQuery(this).parent().parent().find("#marker_image_choose").find(".active").removeClass("active");
									if (name != "") {
										markeredit.setIcon(name);
									}
								})


								jQuery(".marker_image_choose_button").on("click", function () {
									var name = jQuery(this).val();
									var size = jQuery("#marker_edit_image_size").val();
									jQuery("#marker_pic").val("");
									if (markeredit) {
										if (name == 'default') {
											markeredit.setIcon(null);
										} else {
											markeredit.setIcon("<?php echo plugins_url( "google-map-wp/images/icons/" ); ?>" + name + "" + size + ".png");
										}
									}
								})
								jQuery("#marker_edit_image_size").off("change");
								jQuery("#marker_edit_image_size").on("change", function () {

									var size = jQuery(this).val();
									var name = jQuery(this).parent().parent().find(".marker_image_choose").find(".active").find(".marker_image_choose_button").val();
									if (name != undefined) {
										if (markeredit) {
											markeredit.setIcon("<?php echo plugins_url( "google-map-wp/images/icons/" ); ?>" + name + "" + size + ".png");
										}
									}
									else {

										var imageurl = jQuery("#marker_edit_pic").val();

										if (imageurl != "") {
											var imagename = imageurl.match(/.*\/([^/]+)\.([^?]+)/i)[1];
											setIconImage(imagename, imageurl);
										}
									}
								})


								return false;
							})
						}

					}
				}
			}
			function placeMarker(location) {
				if (newmarker) {
					newmarker.setMap(map);
					newmarker.setPosition(location);
				}
				else {
					newmarker = new google.maps.Marker({
						map: map,
						position: location,
						title: "new point",
						draggable: true,
					})
				}
				google.maps.event.addListener(newmarker, 'drag', function (event) {
					updateMarkerInputs(event.latLng);
				});
			}
			function updateMarkerInputs(location) {
				jQuery("#marker_location_lat").val(location.lat());
				jQuery("#marker_location_lng").val(location.lng());
				geocoder = new google.maps.Geocoder();
				geocoder.geocode({'latLng': location}, function (results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						address = results[0].formatted_address;
						jQuery("#marker_location").val(address);
					}
				})
			}

			function updateMarkerEditInputs(location) {
				jQuery("#marker_edit_location_lat").val(location.lat());
				jQuery("#marker_edit_location_lng").val(location.lng());
				geocoder = new google.maps.Geocoder();
				geocoder.geocode({'latLng': location}, function (results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						address = results[0].formatted_address;
						jQuery("#marker_edit_location").val(address);
					}
				})
			}


			function setIconImage(filename, url) {
				var size;
				if (newmarker) {
					size = jQuery("#marker_image_size").val();
					if (jQuery("#marker_pic").val() == jQuery("#marker_pic_url").val()) {
						var data_file = {
							action: "g_map_options",
							type: "upload",
							filename: filename,
							url: url,
							size: size,
						}
						jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", data_file, function (response) {
							if (response.success) {
								jQuery("#marker_pic_name").val("<?php echo admin_url(); ?>" + response.success);
								newmarker.setIcon("<?php echo admin_url(); ?>" + response.success);
							}
							;
						}, "json");
					} else {
						newmarker.setIcon(url);
					}
				} else if (markeredit) {
					size = jQuery("#marker_edit_image_size").val();
					if (jQuery("#marker_edit_pic").val() == jQuery("#marker_edit_pic_url").val()) {
						var data_file = {
							action: "g_map_options",
							type: "upload",
							filename: filename,
							url: url,
							size: size,
						}
						jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", data_file, function (response) {
							if (response.success) {
								jQuery("#marker_edit_pic_name").val("<?php echo admin_url(); ?>" + response.success);
								markeredit.setIcon("<?php echo admin_url(); ?>" + response.success);
							}
							;
						}, "json");
					} else {
						markeredit.setIcon(url);
					}
				}


			}
		</script>
		<?php ;
	}
}

?>
