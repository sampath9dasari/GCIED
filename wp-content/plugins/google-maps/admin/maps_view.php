<?php
function show_map() {
	global $wpdb;
	?>
	<div class="maps_list_container">
		<h2>
			Huge-IT Google maps
			<a class="new_map_create" href="admin.php?page=hugeitgooglemaps_main&task=add_cat">Add New Map</a>
		</h2>

		<table class="maps_table">
			<thead>
			<tr>
				<th scope="col" id="id" style="width:30px"><span>ID</span><span class="sorting-indicator"></span></th>
				<th scope="col" id="name" style="width:85px"><span>Name</span><span class="sorting-indicator"></span></th>
				<th scope="col" id="name" style="width:85px"><span>Action</span><span class="sorting-indicator"></span></th>
				<th scope="col" id="shortcode" style="width:85px"><span>Shortcode</span><span class="sorting-indicator"></span></th>
				<th style="width:40px">Delete</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$sql     = "SELECT * FROM " . $wpdb->prefix . "g_maps ORDER BY id ASC";
			$getMaps = $wpdb->get_results( $sql );
			if ( count( $getMaps ) > 0 ) {
				$c = 1;
				foreach ( $getMaps as $map ) {

					$i = $c % 2;
					?>
					<tr class="<?php if ( $i == 1 ) {
						echo "has_background";
					} ?>">
						<td><?php echo $map->id; ?></td>
						<td>
							<a href="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo $map->id; ?>"><?php echo esc_html( stripslashes( $map->name ) ); ?></a>
						<td><a href="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo $map->id; ?>">Edit</a></td>
						<td>[huge_it_maps id="<?php echo $map->id ?>"]</td>
						<td><a href="admin.php?page=hugeitgooglemaps_main&task=remove_cat&id=<?php echo $map->id; ?>">Delete</a></td>
					</tr>
					<?php ;
					$c ++;
				}
			}
			?>
			</tbody>
		</table>
	</div>
	<?php ;
}


function edit_map() {
	require_once( "marker_func.php" );
	require_once( "polygone_func.php" );
	require_once( "polyline_func.php" );
	require_once( "circle_func.php" );

	if( !isset( $_GET['id'] ) ){
		wp_die( __( 'Something went wrong','hg_gmaps' ) );
	}

	$map_id = intval( $_GET['id'] );

	if( !$map_id ){
		wp_die( __( 'Something went wrong','hg_gmaps' ) );
	}

	maps_js( $map_id );
	marker_js( $map_id );
	polygone_js( $map_id );
	polyline_js( $map_id );
	circle_js( $map_id );
	ajax_js( $map_id );
	global $wpdb;
	$sql    = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "g_maps WHERE id=%s", $map_id );
	$getMap = $wpdb->get_results( $sql );
	if ( count( $getMap ) > 0 ) {

		?>
		<script>
			jQuery(document).ready(function () {
				jQuery('input[data-slider="true"]').bind("slider:changed", function (event, data) {
					jQuery(this).parent().find('span').html(data.value);
					jQuery(this).val(data.value);
				});
				jQuery('#map_zoom').bind("slider:changed", function (event, data) {
					jQuery(this).parent().find('span').html(parseInt(data.value));
					jQuery(this).val(parseInt(data.value));
					map_admin_view.setZoom(parseInt(jQuery(this).val()))
				});
				jQuery('#map_width').bind("slider:changed", function (event, data) {
					jQuery(this).parent().find('span').html(parseInt(data.value) + "%");
					jQuery(this).val(parseInt(data.value));
				});
			})
		</script>
		<?php
		if ( isset( $_GET['cat_edited'] ) ) {
			if ( $_GET['cat_edited'] == "true" ) {
				?>
				<div class="updated">
					Map saved succefully
				</div>
				<?php
			}
		}
		$api_key = get_option( "hg_gmaps_api_key", "" );

		if ( $api_key != "" ) {
			$api_key_value = 'value="' . esc_attr( $api_key ) . '"';
		} else {
			$api_key_value = '';
		}
		?>
		<form class="hg_gmaps_main_api_form <?php if ( $api_key == '' ) {
			echo 'hide';
		} ?>" action="" method="post">
			<label class="hg_mui_text">
				<span class="hg_mui_label mui_label_mt11">API KEY</span>
				<div class="hg_mui_input_block">
					<input name="hg_gmaps_api_key_input" class="hg_gmaps_api_key_input" <?php echo $api_key_value; ?>
					       required="required" type="text"><span class="control_title">Input the api key here</span>
					<div class="hg_mui_bar"></div>
				</div>
			</label>
				<span class="hg_gmaps_apply_action"><button
						class="hg_gmaps_save_api_key_button hg_mui_btn hg_mui_btn_raised_green">Save
					</button><span class="spinner"></span></span>
		</form>
		<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post" name="adminform" id="adminform">
			<input type="hidden" name="map_id" id="map_id" value="<?php echo esc_attr(intval($_GET['id'])); ?>"/>
			<div class="map_heading">
				<ul class="maps_list">
					<?php
					$sql    = "SELECT * FROM " . $wpdb->prefix . "g_maps ORDER BY id ASC";
					$getAll = $wpdb->get_results( $sql );
					if ( count( $getAll ) > 0 ) {
						foreach ( $getAll as $mapname ) {
							if ( $mapname->id == intval($_GET['id']) ) {
								?>
								<li class="active">
									<input type="text" name="map_name_tab" maxlength="250" id="map_name_tab" value="<?php echo $mapname->name; ?>"/>

								</li>
								<style>
									#adminform .map_heading ul .active input {
										background: url(<?php echo plugins_url("../images/edit1.png",__FILE__); ?>) right center no-repeat #fff !important;
									}
								</style>
								<?php ;
							} else {
								?>
								<li>
									<a href="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo $mapname->id; ?>"><?php echo $mapname->name ?></a>
								</li>
								<?php ;
							}
						}
					}
					?>
					<li>
						<a class="new_map_button" href="admin.php?page=hugeitgooglemaps_main&task=add_cat">+</a>
					</li>
				</ul>
			</div>
		</form>

		<?php
		$id      = intval($_GET['id']);
		$sql     = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "g_maps WHERE id=%s", $id );
		$thisMap = $wpdb->get_row( $sql );
		$type    = $thisMap->type;
		?>
		<div class="admin_edit_section_container">
			<input type="hidden" id="map_id" name="map_id" value="<?php echo $id; ?>"/>
			<ul class="admin_edit_section">
				<li class="editing_section ">
					<div class="editing_heading">
						<span class="heading_icon"><img src="<?php echo untrailingslashit(HG_GMAP_IMAGES_URL).'/images/menu-icons/dashboard.svg'; ?>" width="20" /></span>
						General Options
						<div class="help">?
							<div class="help-block">
								<span class="pnt"></span>
								<p>General Options of current map</p>
							</div>
						</div>
						<span class="heading_arrow"></span>
					</div>
					<div class="edit_content map_options hide">
						<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
							<ul>
								<li class="has_background">
									<label for="map_name">Map Name</label>
									<input type="text" name="map_name" id="map_name" value="<?php echo $thisMap->name; ?>"/>
								</li>
								<li>
									<label for="map_controller_pan">Enable Pan-Controller</label>
									<input type="checkbox" class="map_controller_input" id="map_controller_pan" name="map_controller_pan"
									       value="true" <?php if ( $thisMap->pan_controller == "true" ) {
										echo "checked='checked'";
									} ?> />
								</li>
								<li class="has_background">
									<label for="map_controller_zoom">Enable Zoom-Controller</label>
									<input type="checkbox" class="map_controller_input" id="map_controller_zoom" name="map_controller_zoom"
									       value="true" <?php if ( $thisMap->zoom_controller == "true" ) {
										echo "checked='checked'";
									} ?> />
								</li>
								<li>
									<label for="map_controller_type">Enable Map-Type-Controller</label>
									<input type="checkbox" class="map_controller_input" id="map_controller_type" name="map_controller_type"
									       value="true" <?php if ( $thisMap->type_controller == "true" ) {
										echo "checked='checked'";
									} ?> />
								</li>
								<li class="has_background">
									<label for="map_controller_scale">Enable Scale-Controller</label>
									<input type="checkbox" class="map_controller_input" id="map_controller_scale" name="map_controller_scale"
									       value="true" <?php if ( $thisMap->scale_controller == "true" ) {
										echo "checked='checked'";
									} ?> />
								</li>
								<li>
									<label for="map_controller_street_view">Enable Street-View-Controller</label>
									<input type="checkbox" class="map_controller_input" id="map_controller_street_view"
									       name="map_controller_street_view" value="true" <?php if ( $thisMap->street_view_controller == "true" ) {
										echo "checked='checked'";
									} ?> />
								</li>
								<li class="has_background">
									<label for="map_controller_overview">Enable Overview-Map-Controller</label>
									<input type="checkbox" class="map_controller_input" id="map_controller_overview" name="map_controller_overview"
									       value="true" <?php if ( $thisMap->overview_map_controller == "true" ) {
										echo "checked='checked'";
									} ?> />
								</li>
								<li>
									<label for="map_zoom">Default Zoom</label>
									<div class="slider-container" style="float:left; width:55%; height:25px; ">
										<input name="map_zoom" id="map_zoom" data-slider-highlight="true"
										       data-slider-values="0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21" type="text"
										       data-slider="true" value="<?php echo $thisMap->zoom; ?>"/>
										<span style="position:absolute; top: 4px;left: 160px;"><?php echo $thisMap->zoom; ?></span>
									</div>
								</li>
								<li class="has_background">
									<label for="min_zoom">Minimum Zoom</label>
									<div class="slider-container" style="float:left; width:55%; height:25px; ">
										<input name="min_zoom" id="min_zoom" data-slider-highlight="true"
										       data-slider-values="0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21" type="text"
										       data-slider="true" value="<?php echo $thisMap->min_zoom; ?>"/>
										<span style="position:absolute; top: 4px;left: 160px;"><?php echo $thisMap->min_zoom; ?></span>
									</div>
								</li>
								<li>
									<label for="max_zoom">Maximum Zoom</label>
									<div class="slider-container" style="float:left; width:55%; height:25px; ">
										<input name="max_zoom" id="max_zoom" data-slider-highlight="true"
										       data-slider-values="0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21" type="text"
										       data-slider="true" value="<?php echo $thisMap->max_zoom; ?>"/>
										<span style="position:absolute; top: 4px;left: 160px;"><?php echo $thisMap->max_zoom; ?></span>
									</div>
								</li>
								<li class="has_background">
									<label for="map_center_addr">Center Address</label>
									<input type="text" name="map_center_addr" id="map_center_addr"/>
								</li>
								<li>
									<label for="map_center_lat">Center Latitude</label>
									<input type="text" name="map_center_lat" id="map_center_lat" value="<?php echo $thisMap->center_lat; ?>"/>
								</li>
								<li class="has_background">
									<label for="map_center_lng">Center Longitude</label>
									<input type="text" name="map_center_lng" id="map_center_lng" value="<?php echo $thisMap->center_lng; ?>"/>
								</li>
								<li>
									<label for="map_width">Map Width</label>
									<div class="slider-container" style="float:left; width:55%; height:25px; ">
										<input class="front_end_input_options" name="map_width" id="map_width" data-slider-highlight="true"
										       data-slider-values="0,10,20,30,40,50,60,70,80,90,100" type="text" data-slider="true"
										       value="<?php echo $thisMap->width; ?>"/>
										<span style="position:absolute; top: 4px;left: 160px;"><?php echo $thisMap->width; ?>%</span>
									</div>
								</li>
								<li class="has_background">
									<label for="map_height">Map Height</label>
									<input class="front_end_input_options" type="number" name="map_height" id="map_height"
									       value="<?php echo $thisMap->height; ?>"/>
								</li>
								<li>
									<label for="map_align">Map Align</label>
									<select class="front_end_input_options" name="map_align" id="map_align">
										<option value="left" <?php if ( $thisMap->align == 'left' ) {
											echo 'selected';
										}; ?>>left
										</option>
										<option value="center" <?php if ( $thisMap->align == 'center' ) {
											echo 'selected';
										}; ?>>center
										</option>
										<option value="right" <?php if ( $thisMap->align == 'right' ) {
											echo 'selected';
										}; ?>>right
										</option>
									</select>
								</li>
								<li>
									<label for="map_border_radius">Border Radius</label>
									<input class="front_end_input_options" type="number" name="map_border_radius" id="map_border_radius"
									       value="<?php echo $thisMap->border_radius; ?>"/>
								</li>
								<li class="pro">
									<label for="wheel_scroll">Wheel Scrolling</label>
									<select class="front_end_input_options" name="wheel_scroll" id="">
										<option value="true" <?php if ( $thisMap->wheel_scroll == 'true' ) {
											echo 'selected';
										}; ?>>On
										</option>
										<option value="false" <?php if ( $thisMap->wheel_scroll == 'false' ) {
											echo 'selected';
										}; ?>>Off
										</option>
									</select>
								</li>
								<li class="pro" >
									<label for="map_draggable">Map Draggable</label>
									<select class="front_end_input_options" name="map_draggable" id="">
										<option  value="true" <?php if ( $thisMap->draggable == 'true' ) {
											echo 'selected';
										}; ?>>On
										</option>
										<option  value="false" <?php if ( $thisMap->draggable == 'false' ) {
											echo 'selected';
										}; ?>>Off
										</option>
									</select>
								</li>
								<li class="pro">
									<label for="map_language">Map Language</label>
									<select class="front_end_input_options" id="" name="map_language">
										<option value="location based" <?php if ( $thisMap->language == 'location based' ) {
											echo 'selected';
										}; ?>>Location Based
										</option>
										<option value="ar" <?php if ( $thisMap->language == 'ar' ) {
											echo 'selected="selected"';
										}; ?>>Arabic
										</option>
										<option value="bg" <?php if ( $thisMap->language == 'bg' ) {
											echo 'selected="selected"';
										}; ?>>Bulgarian
										</option>
										<option value="bn" <?php if ( $thisMap->language == 'bn' ) {
											echo 'selected="selected"';
										}; ?>>Bengali
										</option>
										<option value="ca" <?php if ( $thisMap->language == 'ca' ) {
											echo 'selected="selected"';
										}; ?>>Catalan
										</option>
										<option value="cs" <?php if ( $thisMap->language == 'cs' ) {
											echo 'selected="selected"';
										}; ?>>Czech
										</option>
										<option value="da" <?php if ( $thisMap->language == 'da' ) {
											echo 'selected="selected"';
										}; ?>>Danish
										</option>
										<option value="de" <?php if ( $thisMap->language == 'de' ) {
											echo 'selected="selected"';
										}; ?>>German
										</option>
										<option value="el" <?php if ( $thisMap->language == 'el' ) {
											echo 'selected="selected"';
										}; ?>>Greek
										</option>
										<option value="en" <?php if ( $thisMap->language == 'en' ) {
											echo 'selected="selected"';
										}; ?>>English
										</option>
										<option value="en-AU" <?php if ( $thisMap->language == 'en-AU' ) {
											echo 'selected="selected"';
										}; ?>>English (Australian)
										</option>
										<option value="en-GB" <?php if ( $thisMap->language == 'en-GB' ) {
											echo 'selected="selected"';
										}; ?>>English (Great Britain)
										</option>
										<option value="es" <?php if ( $thisMap->language == 'es' ) {
											echo 'selected="selected"';
										}; ?>>Spanish
										</option>
										<option value="eu" <?php if ( $thisMap->language == 'eu' ) {
											echo 'selected="selected"';
										}; ?>>Basque
										</option>
										<option value="fa" <?php if ( $thisMap->language == 'fa' ) {
											echo 'selected="selected"';
										}; ?>>Farsi
										</option>
										<option value="fi" <?php if ( $thisMap->language == 'fi' ) {
											echo 'selected="selected"';
										}; ?>>Finnish
										</option>
										<option value="fil" <?php if ( $thisMap->language == 'fil' ) {
											echo 'selected="selected"';
										}; ?>>Finnish
										</option>
										<option value="fr" <?php if ( $thisMap->language == 'fr' ) {
											echo 'selected="selected"';
										}; ?>>French
										</option>
										<option value="gl" <?php if ( $thisMap->language == 'gl' ) {
											echo 'selected="selected"';
										}; ?>>Galician
										</option>
										<option value="gu" <?php if ( $thisMap->language == 'gu' ) {
											echo 'selected="selected"';
										}; ?>>Gujarati
										</option>
										<option value="hi" <?php if ( $thisMap->language == 'hi' ) {
											echo 'selected="selected"';
										}; ?>>Hindi
										</option>
										<option value="hr" <?php if ( $thisMap->language == 'hr' ) {
											echo 'selected="selected"';
										}; ?>>Croatian
										</option>
										<option value="hu" <?php if ( $thisMap->language == 'hu' ) {
											echo 'selected="selected"';
										}; ?>>Hungarian
										</option>
										<option value="id" <?php if ( $thisMap->language == 'id' ) {
											echo 'selected="selected"';
										}; ?>>Indonesian
										</option>
										<option value="it" <?php if ( $thisMap->language == 'it' ) {
											echo 'selected="selected"';
										}; ?>>Italian
										</option>
										<option value="iw" <?php if ( $thisMap->language == 'iw' ) {
											echo 'selected="selected"';
										}; ?>>Hebrew
										</option>
										<option value="ja" <?php if ( $thisMap->language == 'ja' ) {
											echo 'selected="selected"';
										}; ?>>Japanese
										</option>
										<option value="kn" <?php if ( $thisMap->language == 'kn' ) {
											echo 'selected="selected"';
										}; ?>>Kannada
										</option>
										<option value="ko" <?php if ( $thisMap->language == 'ko' ) {
											echo 'selected="selected"';
										}; ?>>Korean
										</option>
										<option value="lt" <?php if ( $thisMap->language == 'lt' ) {
											echo 'selected="selected"';
										}; ?>>Lithuanian
										</option>
										<option value="lv" <?php if ( $thisMap->language == 'lv' ) {
											echo 'selected="selected"';
										}; ?>>Latvian
										</option>
										<option value="ml" <?php if ( $thisMap->language == 'ml' ) {
											echo 'selected="selected"';
										}; ?>>Malayalam
										</option>
										<option value="mr" <?php if ( $thisMap->language == 'mr' ) {
											echo 'selected="selected"';
										}; ?>>Marathi
										</option>
										<option value="nl" <?php if ( $thisMap->language == 'nl' ) {
											echo 'selected="selected"';
										}; ?>>Dutch
										</option>
										<option value="no" <?php if ( $thisMap->language == 'no' ) {
											echo 'selected="selected"';
										}; ?>>Norwegian
										</option>
										<option value="pl" <?php if ( $thisMap->language == 'pl' ) {
											echo 'selected="selected"';
										}; ?>>Polish
										</option>
										<option value="pt" <?php if ( $thisMap->language == 'pt' ) {
											echo 'selected="selected"';
										}; ?>>Portuguese
										</option>
										<option value="pt-BR" <?php if ( $thisMap->language == 'pt-BR' ) {
											echo 'selected="selected"';
										}; ?>>Portuguese (Brazil)
										</option>
										<option value="pt-PT" <?php if ( $thisMap->language == 'pt-PT' ) {
											echo 'selected="selected"';
										}; ?>>Portuguese (Portugal)
										</option>
										<option value="ro" <?php if ( $thisMap->language == 'ro' ) {
											echo 'selected="selected"';
										}; ?>>Romanian
										</option>
										<option value="ru" <?php if ( $thisMap->language == 'ru' ) {
											echo 'selected="selected"';
										}; ?>>Russian
										</option>
										<option value="sk" <?php if ( $thisMap->language == 'sk' ) {
											echo 'selected="selected"';
										}; ?>>Slovak
										</option>
										<option value="sl" <?php if ( $thisMap->language == 'sl' ) {
											echo 'selected="selected"';
										}; ?>>Slovenian
										</option>
										<option value="sr" <?php if ( $thisMap->language == 'sr' ) {
											echo 'selected="selected"';
										}; ?>>Serbian
										</option>
										<option value="sv" <?php if ( $thisMap->language == 'sv' ) {
											echo 'selected="selected"';
										}; ?>>Swedish
										</option>
										<option value="ta" <?php if ( $thisMap->language == 'ta' ) {
											echo 'selected="selected"';
										}; ?>>Tamil
										</option>
										<option value="te" <?php if ( $thisMap->language == 'te' ) {
											echo 'selected="selected"';
										}; ?>>Telugu
										</option>
										<option value="th" <?php if ( $thisMap->language == 'th' ) {
											echo 'selected="selected"';
										}; ?>>Thai
										</option>
										<option value="tl" <?php if ( $thisMap->language == 'tl' ) {
											echo 'selected="selected"';
										}; ?>>Tagalog
										</option>
										<option value="tr" <?php if ( $thisMap->language == 'tr' ) {
											echo 'selected="selected"';
										}; ?>>Turkish
										</option>
										<option value="uk" <?php if ( $thisMap->language == 'uk' ) {
											echo 'selected="selected"';
										}; ?>>Ukrainian
										</option>
										<option value="vi" <?php if ( $thisMap->language == 'vi' ) {
											echo 'selected="selected"';
										}; ?>>Vietnamese
										</option>
										<option value="zh-CN" <?php if ( $thisMap->language == 'zh-CN' ) {
											echo 'selected="selected"';
										}; ?>>Chinese (Simplified)
										</option>
										<option value="zh-TW" <?php if ( $thisMap->language == 'zh-TW' ) {
											echo 'selected="selected"';
										}; ?>>Chinese (Traditional)
										</option>
									</select>
								</li>
								<li class="pro">
									<label for="map_animation">Map Animation</label>
									<select id="" name="map_animation">
										<option value="none" <?php if ( $thisMap->animation == "none" ) {
											echo 'selected="selected"';
										} ?>>None
										</option>
										<optgroup label="Attention Seekers">
											<option value="bounce" <?php if ( $thisMap->animation == "bounce" ) {
												echo 'selected="selected"';
											} ?>>bounce
											</option>
											<option value="flash" <?php if ( $thisMap->animation == "flash" ) {
												echo 'selected="selected"';
											} ?>>flash
											</option>
											<option value="pulse" <?php if ( $thisMap->animation == "pulse" ) {
												echo 'selected="selected"';
											} ?>>pulse
											</option>
											<option value="rubberBand" <?php if ( $thisMap->animation == "rubberBand" ) {
												echo 'selected="selected"';
											} ?>>rubberBand
											</option>
											<option value="shake" <?php if ( $thisMap->animation == "shake" ) {
												echo 'selected="selected"';
											} ?>>shake
											</option>
											<option value="swing" <?php if ( $thisMap->animation == "swing" ) {
												echo 'selected="selected"';
											} ?>>swing
											</option>
											<option value="tada" <?php if ( $thisMap->animation == "tada" ) {
												echo 'selected="selected"';
											} ?>>tada
											</option>
											<option value="wobble" <?php if ( $thisMap->animation == "wobble" ) {
												echo 'selected="selected"';
											} ?>>wobble
											</option>
											<option value="jello" <?php if ( $thisMap->animation == "jello" ) {
												echo 'selected="selected"';
											} ?>>jello
											</option>
											<option value="rollIn" <?php if ( $thisMap->animation == "rollIn" ) {
												echo 'selected="selected"';
											} ?>>rollIn
											</option>
										</optgroup>
										<optgroup label="Bouncing Entrances">
											<option value="bounceIn" <?php if ( $thisMap->animation == "bounceIn" ) {
												echo 'selected="selected"';
											} ?>>bounceIn
											</option>
											<option value="bounceInDown" <?php if ( $thisMap->animation == "bounceInDown" ) {
												echo 'selected="selected"';
											} ?>>bounceInDown
											</option>
											<option value="bounceInLeft" <?php if ( $thisMap->animation == "bounceInLeft" ) {
												echo 'selected="selected"';
											} ?>>bounceInLeft
											</option>
											<option value="bounceInRight" <?php if ( $thisMap->animation == "bounceInRight" ) {
												echo 'selected="selected"';
											} ?>>bounceInRight
											</option>
											<option value="bounceInUp" <?php if ( $thisMap->animation == "bounceInUp" ) {
												echo 'selected="selected"';
											} ?>>bounceInUp
											</option>
										</optgroup>
										<optgroup label="Fading Entrances">
											<option value="fadeIn" <?php if ( $thisMap->animation == "fadeIn" ) {
												echo 'selected="selected"';
											} ?>>fadeIn
											</option>
											<option value="fadeInDown" <?php if ( $thisMap->animation == "fadeInDown" ) {
												echo 'selected="selected"';
											} ?>>fadeInDown
											</option>
											<option value="fadeInDownBig" <?php if ( $thisMap->animation == "fadeInDownBig" ) {
												echo 'selected="selected"';
											} ?>>fadeInDownBig
											</option>
											<option value="fadeInLeft" <?php if ( $thisMap->animation == "fadeInLeft" ) {
												echo 'selected="selected"';
											} ?>>fadeInLeft
											</option>
											<option value="fadeInLeftBig" <?php if ( $thisMap->animation == "fadeInLeftBig" ) {
												echo 'selected="selected"';
											} ?>>fadeInLeftBig
											</option>
											<option value="fadeInRight" <?php if ( $thisMap->animation == "fadeInRight" ) {
												echo 'selected="selected"';
											} ?>>fadeInRight
											</option>
											<option value="fadeInRightBig" <?php if ( $thisMap->animation == "fadeInRightBig" ) {
												echo 'selected="selected"';
											} ?>>fadeInRightBig
											</option>
											<option value="fadeInUp" <?php if ( $thisMap->animation == "fadeInUp" ) {
												echo 'selected="selected"';
											} ?>>fadeInUp
											</option>
											<option value="fadeInUpBig" <?php if ( $thisMap->animation == "fadeInUpBig" ) {
												echo 'selected="selected"';
											} ?>>fadeInUpBig
											</option>
										</optgroup>
										<optgroup label="Flippers">
											<option value="flip" <?php if ( $thisMap->animation == "flip" ) {
												echo 'selected="selected"';
											} ?>>flip
											</option>
											<option value="flipInX" <?php if ( $thisMap->animation == "flipInX" ) {
												echo 'selected="selected"';
											} ?>>flipInX
											</option>
											<option value="flipInY" <?php if ( $thisMap->animation == "flipInY" ) {
												echo 'selected="selected"';
											} ?>>flipInY
											</option>
										</optgroup>
										<optgroup label="Rotating Entrances">
											<option value="lightSpeedIn" <?php if ( $thisMap->animation == "lightSpeedIn" ) {
												echo 'selected="selected"';
											} ?>>lightSpeedIn
											</option>
											<option value="rotateIn" <?php if ( $thisMap->animation == "rotateIn" ) {
												echo 'selected="selected"';
											} ?>>rotateIn
											</option>
											<option value="rotateInDownLeft" <?php if ( $thisMap->animation == "rotateInDownLeft" ) {
												echo 'selected="selected"';
											} ?>>rotateInDownLeft
											</option>
											<option value="rotateInDownRight" <?php if ( $thisMap->animation == "rotateInDownRight" ) {
												echo 'selected="selected"';
											} ?>>rotateInDownRight
											</option>
											<option value="rotateInUpLeft" <?php if ( $thisMap->animation == "rotateInUpLeft" ) {
												echo 'selected="selected"';
											} ?>>rotateInUpLeft
											</option>
											<option value="rotateInUpRight" <?php if ( $thisMap->animation == "rotateInUpRight" ) {
												echo 'selected="selected"';
											} ?>>rotateInUpRight
											</option>
										</optgroup>
										<optgroup label="Sliding Entrances">
											<option value="slideInUp" <?php if ( $thisMap->animation == "slideInUp" ) {
												echo 'selected="selected"';
											} ?>>slideInUp
											</option>
											<option value="slideInDown" <?php if ( $thisMap->animation == "slideInDown" ) {
												echo 'selected="selected"';
											} ?>>slideInDown
											</option>
											<option value="slideInLeft" <?php if ( $thisMap->animation == "slideInLeft" ) {
												echo 'selected="selected"';
											} ?>>slideInLeft
											</option>
											<option value="slideInRight" <?php if ( $thisMap->animation == "slideInRight" ) {
												echo 'selected="selected"';
											} ?>>slideInRight
											</option>
										</optgroup>
										<optgroup label="Zooming Entrances">
											<option value="zoomIn" <?php if ( $thisMap->animation == "zoomIn" ) {
												echo 'selected="selected"';
											} ?>>zoomIn
											</option>
											<option value="zoomInDown" <?php if ( $thisMap->animation == "zoomInDown" ) {
												echo 'selected="selected"';
											} ?>>zoomInDown
											</option>
											<option value="zoomInLeft" <?php if ( $thisMap->animation == "zoomInLeft" ) {
												echo 'selected="selected"';
											} ?>>zoomInLeft
											</option>
											<option value="zoomInRight" <?php if ( $thisMap->animation == "zoomInRight" ) {
												echo 'selected="selected"';
											} ?>>zoomInRight
											</option>
											<option value="zoomInUp" <?php if ( $thisMap->animation == "zoomInUp" ) {
												echo 'selected="selected"';
											} ?>>zoomInUp
											</option>
										</optgroup>
									</select>
								</li>
								<li class="pro">
									<label for="map_type">Map Type</label>
									<select id="" name="map_type">
										<option value="ROADMAP" <?php if ( $type == "ROADMAP" ) {
											echo "selected";
										} ?> >Roadmap
										</option>
										<option value="SATELLITE" <?php if ( $type == "SATELLITE" ) {
											echo "selected";
										} ?> >Satellite
										</option>
										<option value="HYBRID" <?php if ( $type == "HYBRID" ) {
											echo "selected";
										} ?> >Hybrid
										</option>
										<option value="TERRAIN" <?php if ( $type == "TERRAIN" ) {
											echo "selected";
										} ?> >Terrain
										</option>
									</select>
								</li>
								<li class="pro">
									<label for="map_infowindow_type">Marker Infowindow Open On</label>
									<?php $info_type = $thisMap->info_type; ?>
									<select id="" name="map_infowindow_type">
										<option value="click" <?php if ( $info_type == "click" ) {
											echo "selected";
										} ?> >Click
										</option>
										<option value="hover" <?php if ( $info_type == "hover" ) {
											echo "selected";
										} ?> >Hover
										</option>
									</select>
								</li>
							</ul>
							<input type="submit" class="button-primary" name="map_submit" id="map_submit" value="Save"/>
						</form>
					</div>
				</li>
				<li class="markers_editor editing_section">
					<div class="editing_heading">
						<span class="heading_icon"><img src="<?php echo untrailingslashit(HG_GMAP_IMAGES_URL).'/images/menu-icons/marker.svg'; ?>" width="20" /></span>
						Markers
						<div class="help">?
							<div class="help-block">
								<span class="pnt"></span>
								<p>A marker identifies a location on a map. Right-Click on the map to add a Marker. Hold pressed and drag to move
									it</p>
							</div>
						</div>
						<span class="heading_arrow"></span>
					</div>
					<div class="edit_content hide" id="g_map_marker_options">
						<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
							<a class="add_button" id="marker_add_button" href="#">+Add New Marker</a>
							<div class="hidden_edit_content hide">
								<a href="#" id="back_marker" class="cancel left">◄ Back</a>
								<ul>
									<li class="has_background">
										<label for="marker_location">Address</label>
										<input type="text" id="marker_location" name="marker_location"/>
									</li>
									<li>
										<label for="marker_location_lat">Latitude</label>
										<input type="text" id="marker_location_lat" name="marker_location_lat"/>
									</li>
									<li class="has_background">
										<label for="marker_location_lng">Longitude </label>
										<input type="text" id="marker_location_lng" name="marker_location_lng"/>
									</li>
									<li>
										<label for="marker_animation">Animation</label>
										<select id="marker_animation" name="marker_animation">
											<option checked value="NONE">None</option>
											<option value="BOUNCE">Bounce</option>
											<option value="DROP">Drop</option>
										</select>
									</li>
									<li class="has_background">
										<label for="marker_title">Title</label>
										<input type="text" id="marker_title" name="marker_title"/>
									</li>
									<li class="description_container">
										<label for="marker_description">Description<span class="pro_desc"><a href="http://huge-it.com/google-map" target="_blank">Go Pro</a>    to enable HTML in description</span></label>
										<textarea class="description" id="marker_description" name="marker_description"></textarea>
									</li>
									<li class="has_background">
										Choose Marker Icon
									</li>
									<li class="marker_image_choose">
										<ul>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/default-icon.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/marker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/azuremarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/redmarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/bluemarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/greenmarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/starmarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/blackmarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinleft48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinpink48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinright48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/toyflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/greenflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/flagleft48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinkflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/blueflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/flagright48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/paperflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pointleft48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/redflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>

											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/baseflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>


											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pointright48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinkleft48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>

											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinkgreen48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>

											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinkright48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pointcenter48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/star48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>

											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pointer48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/shopmarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
										</ul>
									</li>
									<li class="pro has_background">
										<label for="marker_image_size">Size of Icon</label>
										<select>
											<option value="16">16x16</option>
											<option value="24">24x24</option>
											<option value="48" selected>48x48</option>
											<option value="64">64x64</option>
											<option value="256">256x256</option>
										</select>
									</li>
									<li class="pro">
										<label for="marker_pic">Custom Marker Icon</label>
										<input type="text" name="marker_pic" placeholder="http://" style="width:29%"/>
										<input type="button" class="button upload_marker_pic" value="upload image"/>
									</li>
									<div>
										<input type="submit" class="button-primary" id="marker_submit" name="marker_submit" value="Save"
										       style="width:23%"/>
										<a href="#" id="cancel_marker" class="cancel">cancel</a>
									</div>
								</ul>
							</div>
						</form>
						<div id="markers_edit_exist_section">
							<div class="edit_list_heading">
								<div class="list_number">
									ID
								</div>
								<div class="edit_list_item">
									Title
								</div>
								<div class="edit_list_delete">
									Action
								</div>
							</div>


							<?php
							$id            = intval($_GET['id']);
							$i             = 1;
							$sql           = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "g_markers WHERE map=%s", $id );
							$getMarkerList = $wpdb->get_results( $sql );
							if ( $getMarkerList ) {
								?>

								<ul class="list_exist" id="marker_list_exist">
									<?php
									foreach ( $getMarkerList as $marker ) {
										$j = $i % 2;
										?>
										<li class="edit_list <?php if ( $j == 1 ) {
											echo "has_background";
										} ?>" data-list_id="<?php echo $marker->id ?>">
											<div class="list_number">
												<?php
												echo $i;
												?>
											</div>
											<div class="edit_list_item">
												<?php if ( ! empty( $marker->title ) ) {
													echo $marker->title;
												} else {
													echo "-";
												} ?>
											</div>
											<div class="edit_marker_list_delete edit_list_delete">
												<form class="edit_list_delete_form" method="post"
												      action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>">
													<input type="submit" class="button edit_list_delete_submit" name="edit_list_delete_submit"
													       value="x"/>
													<input type="hidden" class="edit_list_delete_type" name="edit_list_delete_type" value="marker"/>
													<input type="hidden" class="edit_list_delete_table" value="g_markers"/>
													<input type="hidden" name="delete_marker_id" class="edit_list_delete_id"
													       value="<?php echo $marker->id ?>"/>
												</form>
												<a href="#" class="button" class="edit_marker_list_item"></a>
												<input type="hidden" class="marker_edit_id" name="marker_edit_id" value="<?php echo $marker->id ?>"/>

											</div>
										</li>
										<?php
										$i ++;
									}
									?>
								</ul>

								<?php
							} else {
								echo "<p class='empty_marker'>You have 0 markers</p>";
							}
							?>

						</div>
						<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
							<input type="hidden" id="marker_get_id" name="marker_get_id"/>

							<div class="update_list_item hide">
								<a href="#" id="back_edit_marker" class="cancel  left">◄ Back</a>
								<ul>
									<li class="has_background">
										<label for="marker_edit_location">Address</label>
										<input type="text" id="marker_edit_location" name="marker_edit_location"/>
									</li>
									<li>
										<label for="marker_edit_location_lat">Latitude</label>
										<input type="text" id="marker_edit_location_lat" name="marker_edit_location_lat"/>
									</li>
									<li class="has_background">
										<label for="marker_edit_location_lng">Longitude</label>
										<input type="text" id="marker_edit_location_lng" name="marker_edit_location_lng"/>
									</li>
									<li>
										<label for="marker_edit_animation">Animation</label>
										<select id="marker_edit_animation" name="marker_edit_animation">
											<option checked="checked" value="NONE">None</option>
											<option value="BOUNCE">Bounce</option>
											<option value="DROP">Drop</option>
										</select>
									</li>
									<li class="has_background">
										<label for="marker_edit_title">Title</label>
										<input type="text" id="marker_edit_title" name="marker_edit_title"/>
									</li>
									<li class="description_container">
										<label for="marker_edit_description"><span class="pro_desc"><a href="http://huge-it.com/google-map" target="_blank">Go Pro</a>    to enable HTML in description</span>Description</label>
										<textarea class="description" id="marker_edit_description" name="marker_edit_description"></textarea>
									</li>
									<li class="marker_image_choose">

										<ul>
											<li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/default-icon.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/marker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/azuremarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/redmarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/bluemarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/greenmarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/starmarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/blackmarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinleft48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinpink48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinright48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/toyflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/greenflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/flagleft48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinkflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/blueflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/flagright48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/paperflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pointleft48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/redflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>

											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/baseflag48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>


											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pointright48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinkleft48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>

											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinkgreen48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>

											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pinkright48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pointcenter48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/star48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>

											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/pointer48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
											<li>
												<div class="imag_block">
													<img class="marker_icon" src="<?php echo plugins_url( "../images/icons/shopmarker48.png", __FILE__ ); ?>" alt=""/>
												</div>
											</li>
										</ul>
									</li>
									<li class="pro has_background">
										<label for="marker_image_size">Size of Icon</label>
										<select>
											<option value="16">16x16</option>
											<option value="24">24x24</option>
											<option value="48" selected>48x48</option>
											<option value="64">64x64</option>
											<option value="256">256x256</option>
										</select>
									</li>
									<li class="pro">
										<label for="marker_pic">Custom Marker Icon</label>
										<input type="text" name="marker_pic" placeholder="http://" style="width:29%"/>
										<input type="button" class="button upload_marker_pic" value="upload image"/>
									</li>
								</ul>
								<div>
									<input type="submit" class="button-primary" name="marker_edit_submmit" id="marker_edit_submmit" value="Save"/>
									<a href="#" class="cancel" id="cancel_edit_marker">Cancel</a>
								</div>
							</div>
						</form>
					</div>
				</li>
				<li class="editing_section">
					<div class="editing_heading">
						<span class="heading_icon"><img src="<?php echo untrailingslashit(HG_GMAP_IMAGES_URL).'/images/menu-icons/polygon.svg'; ?>" width="20" /></span>
						Polygons
						<div class="help">?
							<div class="help-block">
								<span class="pnt"></span>
								<p>Bounded highlighted area on the map, showing the specific range, limited with geometric figure. Right click on the
									map to add point. Hold pressed and drag to move it. Left click to remove it.</p>
							</div>
						</div>
						<span class="heading_arrow"></span>
					</div>
					<div class="edit_content hide">
						<div id="g_map_polygone_options">
							<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
								<a id="polygon_add_button" class="add_button clear" href="#">+Add New Polygone</a>
								<div class="hidden_edit_content hide">
									<a href="#" id="back_polygone" class="cancel left">◄ Back</a>
									<ul>
										<li class="has_background">
											<label for="polygone_name">Name</label>
											<input type="text" name="polygone_name" id="polygone_name"/>
										</li>
										<li class="description_container">
											<label for="polygone_coords">data</label>
											<textarea id="polygone_coords" class="polycoords" name="polygone_coords" readonly="readonly"
											          placeholder="Right click on the map to add point. Hold pressed and drag to move it. Left click to remove it."></textarea>
										</li>
										<li>
											<label for="polygone_line_opacity">Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" name="polygone_line_opacity" id="polygone_line_opacity"
												       class="polygone_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"
												       value="0.9"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.9</span>
											</div>
										</li>

										<li class="has_background">
											<label for="polygone_line_color">Line Color</label>
											<input type="text" class="color polygone_options_input" name="polygone_line_color"
											       id="polygone_line_color" value="FF0F0F"/>
										</li>
										<li>
											<label for="polygone_line_width">Line Width</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" name="polygone_line_width" class="polygone_options_input" id="polygone_line_width"
												       data-slider-highlight="true" data-slider-values="<?php for ( $i = 0; $i <= 50; $i ++ ) {
													if ( $i == 50 ) {
														echo $i;
													} else {
														echo $i . ",";
													}
												} ?>" type="text" data-slider="true" value="5"/>
												<span style="position:absolute; top: 4px;left: 160px;">5</span>
											</div>
										</li>
										<li class="has_background">
											<label for="polygone_fill_opacity">Fill Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" name="polygone_fill_opacity" id="polygone_fill_opacity"
												       class="polygone_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"
												       value="0.5"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.5</span>
											</div>
										</li>
										<li>
											<label for="polygone_fill_color">Fill Color</label>
											<input type="text" name="polygone_fill_color" id="polygone_fill_color"
											       class="color polygone_options_input" value="5DFF0D"/>
										</li>
										<li class="pro has_background">
											<label for="hover_polygone_fill_opacity">On-Hover Fill Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="polygone_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"
												       value="0.5"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.5</span>
											</div>
										</li>
										<li class="pro">
											<label for="hover_polygone_fill_color">On-Hover Fill Color</label>
											<input type="text" class="color polygone_options_input" />
										</li>
										<li class="pro has_background">
											<label for="hover_polygone_line_opacity">On-Hover Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="polygone_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"
												       value="0.5"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.5</span>
											</div>
										</li>
										<li class="pro">
											<label for="hover_polygone_line_color">On-Hover Line Color</label>
											<input type="text" class="color polygone_options_input"/>
										</li>
										<li class="pro">
											<label for="polygone_url">Link</label>
											<input type="text" placeholder="http://"/>
										</li>
									</ul>
									<div>
										<input type="submit" class="button-primary" name="polygone_submit" id="polygone_submit" value="Save"/>
										<a href="#" id="cancel_polygone" class="cancel">Cancel</a>
									</div>
								</div>
							</form>
							<div id="polygone_edit_exist_section">
								<div class="edit_list_heading">
									<div class="list_number">
										ID
									</div>
									<div class="edit_list_item">
										Title
									</div>
									<div class="edit_list_delete">
										Action
									</div>
								</div>

								<?php
								$id              = intval($_GET['id']);
								$i               = 1;
								$sql             = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "g_polygones WHERE map=%s", $id );
								$getPolygoneList = $wpdb->get_results( $sql );
								if ( $getPolygoneList ) {
									?>

									<ul class="list_exist" id="polygone_list_exist">
										<?php
										foreach ( $getPolygoneList as $polygone ) {
											$j = $i % 2;
											?>
											<li class="edit_list <?php if ( $j == 1 ) {
												echo "has_background";
											} ?>" data-list_id="<?php echo $polygone->id; ?>">
												<div class="list_number">
													<?php
													echo $i;
													?>
												</div>
												<div class="edit_list_item">
													<?php if ( ! empty( $polygone->name ) ) {
														echo $polygone->name;
													} else {
														echo "-";
													} ?>
												</div>
												<div class="edit_polygone_list_delete edit_list_delete">
													<form class="edit_list_delete_form" method="post"
													      action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>">
														<input type="submit" class="button edit_list_delete_submit" name="edit_list_delete_submit"
														       value="x"/>
														<input type="hidden" class="edit_list_delete_type" name="edit_list_delete_type"
														       value="polygone"/>
														<input type="hidden" class="edit_list_delete_table" value="g_polygones"/>
														<input type="hidden" name="delete_polygone_id" class="edit_list_delete_id"
														       value="<?php echo $polygone->id ?>"/>
													</form>
													<a href="#" class="button" class="edit_polygone_list_item"></a>
													<input type="hidden" class="polygone_edit_id" name="polygone_edit_id"
													       value="<?php echo $polygone->id ?>"/>
												</div>
											</li>
											<?php
											$i ++;
										}
										?>
									</ul>

									<?php
								} else {
									echo "<p class='empty_polygon'>You have 0 polygones on this map</p>";
								}
								?>
							</div>
							<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
								<input type="hidden" id="polygone_get_id" name="polygone_get_id"/>
								<div class="update_list_item hide">
									<a href="#" id="back_edit_polygone" class="cancel left">◄ Back</a>
									<ul>
										<li class="has_background">
											<label for="polygone_edit_name">Name</label>
											<input type="text" name="polygone_edit_name" id="polygone_edit_name"/>
										</li>
										<li class="description_container">
											<label for="polygone_edit_coords">data</label>
											<textarea id="polygone_edit_coords" class="polycoords" name="polygone_edit_coords" readonly="readonly"
											          placeholder="Right click on the map to add point. Hold pressed and drag to move it. Left click to remove it."></textarea>
										</li>
										<li>
											<label for="polygone_edit_line_opacity">Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" name="polygone_edit_line_opacity" id="polygone_edit_line_opacity"
												       class="polygone_edit_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>
										<li class="has_background">
											<label for="polygone_edit_line_color">Line Color</label>
											<input type="text" class="color polygone_edit_options_input" name="polygone_edit_line_color"
											       id="polygone_edit_line_color" value=""/>
										</li>
										<li>
											<label for="polygone_edit_line_width">Line Width</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" name="polygone_edit_line_width" class="polygone_edit_options_input"
												       id="polygone_edit_line_width" data-slider-highlight="true"
												       data-slider-values="<?php for ( $i = 0; $i <= 50; $i ++ ) {
													       if ( $i == 50 ) {
														       echo $i;
													       } else {
														       echo $i . ",";
													       }
												       } ?>" type="text" data-slider="true" value=""/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>
										<li class="has_background">
											<label for="polygone_edit_fill_opacity">Fill Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" name="polygone_edit_fill_opacity" id="polygone_edit_fill_opacity"
												       class="polygone_edit_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>
										<li>
											<label for="polygone_edit_fill_color">Fill Color</label>
											<input type="text" name="polygone_edit_fill_color" id="polygone_edit_fill_color"
											       class="color polygone_edit_options_input" value=""/>
										</li>
										<li class="pro has_background">
											<label for="hover_polygone_edit_fill_opacity">On-Hover Fill Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="polygone_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>
										<li class="pro">
											<label for="hover_polygone_edit_fill_color">On-Hover Fill Color</label>
											<input type="text"  class="color polygone_options_input"/>
										</li>
										<li class="pro has_background">
											<label for="hover_polygone_edit_line_opacity">On-Hover Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="polygone_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>
										<li class="pro">
											<label for="hover_polygone_edit_line_color">On-Hover Line Color</label>
											<input type="text" class="color polygone_options_input"/>
										</li>
										<li class="pro">
											<label for="polygone_edit_url">Link</label>
											<input type="text"  placeholder="http://"/>
										</li>

									</ul>
									<div>
										<input type="submit" class="button-primary" name="polygone_edit_submit" id="polygone_edit_submit"
										       value="Save"/>
										<a href="#" id="cancel_edit_polygone" class="cancel">Cancel</a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</li>
				<li class="editing_section">
					<div class="editing_heading">
						<span class="heading_icon"><img src="<?php echo untrailingslashit(HG_GMAP_IMAGES_URL).'/images/menu-icons/polyline.svg'; ?>" width="20" /></span>
						Polylines
						<div class="help">?
							<div class="help-block">
								<span class="pnt"></span>
								<p>Continuous line composed of one or more line segments, which creates specific track. Right click on the map to add
									point. Hold pressed and drag to move it. Left click to remove it.</p>
							</div>
						</div>
						<span class="heading_arrow"></span>
					</div>
					<div class="edit_content hide">
						<div id="g_map_polyline_options">
							<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
								<a id="polyline_add_button" class="add_button" href="#">+Add New Polyline</a>
								<div class="hidden_edit_content hide">
									<a href="#" id="back_polyline" class="cancel left">◄ Back</a>
									<ul>
										<li class="has_background">
											<label for="polyline_name">Name</label>
											<input type="text" id="polyline_name" name="polyline_name"/>
										</li>
										<li class="description_container">
											<label for="polyline_coords">data</label>
											<textarea id="polyline_coords" class="polycoords" name="polyline_coords" readonly="readonly"
											          placeholder="Right click on the map to add point. Hold pressed and drag to move it. Left click to remove it."></textarea>
										</li>
										<li>
											<label for="polyline_line_opacity">Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" name="polyline_line_opacity" id="polyline_line_opacity"
												       class="polyline_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"
												       value="0.9"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.9</span>
											</div>
										</li>
										<li class="has_background">
											<label for="polyline_line_color">Line Color</label>
											<input type="text" class="color polyline_options_input" name="polyline_line_color"
											       id="polyline_line_color" value="18A326"/>
										</li>
										<li>
											<label for="polyline_line_width">Line Width</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" name="polyline_line_width" class="polyline_options_input " id="polyline_line_width"
												       data-slider-highlight="true" data-slider-values="<?php for ( $i = 0; $i <= 50; $i ++ ) {
													if ( $i == 50 ) {
														echo $i;
													} else {
														echo $i . ",";
													}
												} ?>" type="text" data-slider="true" value="5"/>
												<span style="position:absolute; top: 4px;left: 160px;">5</span>
											</div>
										</li>
										<li class="pro has_background">
											<label for="hover_polyline_line_color">On-Hover Line Color</label>
											<input type="text" class="color polyline_options_input" value="11A000"/>
										</li>
										<li class="pro">
											<label for="hover_polyline_line_opacity">On-Hover Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="polyline_options_input " data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"
												       value="0.5"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.5</span>
											</div>
										</li>

									</ul>
									<div>
										<input type="submit" class="button-primary" id="polyline_submit" name="polyline_submit" value="Save"/>
										<a href="#" id="cancel_polyline" class="cancel">Cancel</a>
									</div>
								</div>
							</form>
							<div id="polyline_edit_exist_section">
								<div class="edit_list_heading">
									<div class="list_number">
										ID
									</div>
									<div class="edit_list_item">
										Title
									</div>
									<div class="edit_list_delete">
										Action
									</div>
								</div>

								<?php
								$id              = intval($_GET['id']);
								$i               = 1;
								$sql             = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "g_polylines WHERE map=%s", $id );
								$getPolylineList = $wpdb->get_results( $sql );
								if ( $getPolylineList ) {
									?>

									<ul class="list_exist" id="polyline_list_exist">
										<?php
										foreach ( $getPolylineList as $polyline ) {
											$j = $i % 2;
											?>
											<li class="edit_list <?php if ( $j == 1 ) {
												echo "has_background";
											} ?>" data-list_id="<?php echo $polyline->id; ?>">
												<div class="list_number">
													<?php
													echo $i;
													?>
												</div>
												<div class="edit_list_item">
													<?php if ( ! empty( $polyline->name ) ) {
														echo $polyline->name;
													} else {
														echo "-";
													} ?>
												</div>
												<div class="edit_polyline_list_delete edit_list_delete">
													<form class="edit_list_delete_form" method="post"
													      action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>">
														<input type="submit" class="button edit_list_delete_submit" name="edit_list_delete_submit"
														       value="x"/>
														<input type="hidden" class="edit_list_delete_type" name="edit_list_delete_type"
														       value="polyline"/>
														<input type="hidden" class="edit_list_delete_table" value="g_polylines"/>
														<input type="hidden" name="delete_polyline_id" class="edit_list_delete_id"
														       value="<?php echo $polyline->id ?>"/>
													</form>
													<a href="#" class="button" class="edit_polyline_list_item"></a>
													<input type="hidden" class="polyline_edit_id" name="polyline_edit_id"
													       value="<?php echo $polyline->id ?>"/>

												</div>
											</li>

											<?php
											$i ++;
										}
										?>
									</ul>

									<?php
								} else {
									echo "<p class='empty_polyline'>You have 0 polylines on this map</p>";
								}
								?>
							</div>
							<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
								<input type="hidden" id="polyline_get_id" name="polyline_get_id"/>
								<div class="update_list_item hide">
									<a href="#" id="back_edit_polyline" class="cancel left">◄ Back</a>
									<ul>
										<li class="has_background">
											<label for="polyline_edit_name">Name</label>
											<input type="text" id="polyline_edit_name" name="polyline_edit_name"/>
										</li>
										<li class="description_container">
											<label for="polyline_edit_coords">data</label>
											<textarea id="polyline_edit_coords" class="polycoords" name="polyline_edit_coords" readonly="readonly"
											          placeholder="Right click on the map to add point. Hold pressed and drag to move it. Left click to remove it."></textarea>
										</li>
										<li>
											<label for="polyline_edit_line_opacity">Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" name="polyline_edit_line_opacity" id="polyline_edit_line_opacity"
												       class="polyline_edit_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"
												       value="0.9"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.9</span>
											</div>
										</li>
										<li class="has_background">
											<label for="polyline_edit_line_color">Line Color</label>
											<input type="text" class="color polyline_edit_options_input" name="polyline_edit_line_color"
											       id="polyline_edit_line_color" value="FF0F0F"/>
										</li>
										<li>
											<label for="polyline_edit_line_width">Line Width</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" name="polyline_edit_line_width" class="polyline_edit_options_input "
												       id="polyline_edit_line_width" data-slider-highlight="true"
												       data-slider-values="<?php for ( $i = 0; $i <= 50; $i ++ ) {
													       if ( $i == 50 ) {
														       echo $i;
													       } else {
														       echo $i . ",";
													       }
												       } ?>" type="text" data-slider="true" value="5"/>
												<span style="position:absolute; top: 4px;left: 160px;">5</span>
											</div>
										</li>
										<li class="pro has_background">
											<label for="hover_polyline_edit_line_color">On-Hover Line Color</label>
											<input type="text" class="color polyline_options_input" />
										</li>
										<li class="pro">
											<label for="hover_polyline_edit_line_opacity">On-Hover Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="polyline_options_input " data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>

									</ul>
									<div>
										<input type="submit" class="button-primary" id="polyline_edit_submit" name="polyline_edit_submit"
										       value="Save"/>
										<a href="#" id="cancel_edit_polyline" class="cancel">Cancel</a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</li>
				<li class="editing_section">
					<div class="hg_gmaps_white editing_heading">
						<span class="heading_icon"><img src="<?php echo untrailingslashit(HG_GMAP_IMAGES_URL).'/images/menu-icons/direction.svg'; ?>" width="20" /></span>
						<?php _e('Directions','hg_gmaps'); ?><i class="hg_gmaps_pro_icon"></i>
						<div class="help">?
							<div class="help-block">
								<span class="pnt"></span>
								<p><?php _e('You can calculate directions by using the DirectionsService/feature. Directions are displayed as a polyline drawing the route on a map. The right click adds and varies  the strating and finishing point. Hold pressed the left click and drug the marker.','hg_gmaps'); ?></p>
							</div>
						</div>
						<span class="heading_arrow_directions"></span>
					</div>
					
				</li>
				<li class="editing_section">
					<div class="editing_heading">
						<span class="heading_icon"><img src="<?php echo untrailingslashit(HG_GMAP_IMAGES_URL).'/images/menu-icons/circle.svg'; ?>" width="20" /></span>
						Circles
						<div class="help">?
							<div class="help-block">
								<span class="pnt"></span>
								<p>Round area, showing the specific range. Right click on map wherever you need to place the circle’s center. Hold
									pressed and drag to move it</p>
							</div>
						</div>
						<span class="heading_arrow"></span>
					</div>
					<div class="edit_content hide">
						<div id="g_map_circle_options">
							<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
								<a id="circle_add_button" class="add_button" href="#">+Add New Circles</a>
								<div class="hidden_edit_content hide">
									<a href="#" id="back_circle" class="cancel left">◄ Back</a>
									<ul>
										<li class="has_background">
											<label for="circle_name">Name</label>
											<input type="text" id="circle_name" name="circle_name" class="circle_options_input"/>
										</li>
										<li>
											<label for="circle_center_addr">center Address</label>
											<input type="text" class="circle_options_input" id="circle_center_addr" name="circle_center_addr"/>
										</li>
										<li class="has_background">
											<label for="circle_center_lat">center Latitude</label>
											<input type="text" class="circle_options_input" id="circle_center_lat" name="circle_center_lat"/>
										</li>
										<li>
											<label for="circle_center_lng">center Longitude</label>
											<input type="text" class="circle_options_input" id="circle_center_lng" name="circle_center_lng"/>
										</li>

										<li class="has_background">
											<label>Show Marker?</label>
											<label class="radio_label" for="circle_marker_show">YES</label>
											<input type="radio" class="radio circle_marker_show" id="circle_marker_show" name="circle_marker_show"
											       value="1"/>
											<label class="radio_label" for="circle_marker_show">NO</label>
											<input type="radio" class="radio circle_marker_show" id="circle_marker_show" name="circle_marker_show"
											       value="0" checked/>
										</li>
										<li>
											<label for="circle_radius">Radius(meter)</label>
											<input type="number" class="circle_options_input" id="circle_radius" name="circle_radius" value="50000"/>
										</li>
										<li class="has_background">
											<label for="circle_line_width">Line Width</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="circle_options_input" id="circle_line_width" name="circle_line_width"
												       data-slider-highlight="true" data-slider-values="<?php for ( $i = 0; $i <= 50; $i ++ ) {
													if ( $i == 50 ) {
														echo $i;
													} else {
														echo $i . ",";
													}
												} ?>" type="text" data-slider="true" value="5"/>
												<span style="position:absolute; top: 4px;left: 160px;">5</span>
											</div>
										</li>
										<li>
											<label for="circle_line_color">Line Color</label>
											<input type="text" class="color circle_options_input" id="circle_line_color" name="circle_line_color"
											       value="FF2B39"/>
										</li>
										<li class="has_background">
											<label for="circle_line_opacity">Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="circle_options_input" id="circle_line_opacity" name="circle_line_opacity"
												       data-slider-highlight="true" data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1"
												       type="text" data-slider="true" value="0.8"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.8</span>
											</div>
										</li>
										<li>
											<label for="circle_fill_color">Fill Color</label>
											<input type="text" class="color circle_options_input" id="circle_fill_color" name="circle_fill_color"
											       value="4FFF72"/>
										</li>
										<li class="has_background">
											<label for="circle_fill_opacity">Fill Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px;">
												<input class="circle_options_input" id="circle_fill_opacity" name="circle_fill_opacity"
												       data-slider-highlight="true" data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1"
												       type="text" data-slider="true" value="0.4"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.4</span>
											</div>
										</li>
										<li class="pro">
											<label for="hover_circle_fill_color">On-Hover Fill Color</label>
											<input type="text" class="color circle_options_input" value="96FFA1"/>
										</li>
										<li class="pro has_background">
											<label for="hover_circle_fill_opacity">On-Hover Fill Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px;">
												<input class="circle_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1"
												       type="text" data-slider="true" value="0.3"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.3</span>
											</div>
										</li>
										<li class="pro">
											<label for="hover_circle_line_color">On-Hover Line Color</label>
											<input type="text" class="color circle_options_input" value="FF5C5C"/>
										</li>
										<li class="pro has_background">
											<label for="hover_circle_line_opacity">On-Hover Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="circle_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"
												       value="0.6"/>
												<span style="position:absolute; top: 4px;left: 160px;">0.6</span>
											</div>
										</li>
									</ul>
									<div>
										<input type="submit" class="button-primary" id="circle_submit" name="circle_submit" value="Save"/>
										<a href="#" id="cancel_circle" class="cancel">Cancel</a>
									</div>
								</div>
							</form>
							<div id="circle_edit_exist_section">
								<div class="edit_list_heading">
									<div class="list_number">
										ID
									</div>
									<div class="edit_list_item">
										Title
									</div>
									<div class="edit_list_delete">
										Action
									</div>
								</div>

								<?php
								$id            = intval($_GET['id']);
								$i             = 1;
								$sql           = $wpdb->prepare( "SELECT * FROM " . $wpdb->prefix . "g_circles WHERE map=%s", $id );
								$getCircleList = $wpdb->get_results( $sql );
								if ( $getCircleList ) {
									?>

									<ul class="list_exist" id="circle_list_exist">
										<?php
										foreach ( $getCircleList as $circle ) {
											$j = $i % 2;
											?>
											<li class="edit_list <?php if ( $j == 1 ) {
												echo "has_background";
											} ?>" data-list_id="<?php echo $circle->id; ?>">
												<div class="list_number">
													<?php
													echo $i;
													?>
												</div>
												<div class="edit_list_item">
													<?php if ( ! empty( $circle->name ) ) {
														echo $circle->name;
													} else {
														echo "-";
													} ?>
												</div>
												<div class="edit_circle_list_delete edit_list_delete">
													<form class="edit_list_delete_form" method="post"
													      action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>">
														<input type="submit" class="button edit_list_delete_submit" name="edit_list_delete_submit"
														       value="x"/>
														<input type="hidden" class="edit_list_delete_type" name="edit_list_delete_type"
														       value="circle"/>
														<input type="hidden" class="edit_list_delete_table" value="g_circles"/>
														<input type="hidden" name="delete_circle_id" class="edit_list_delete_id"
														       value="<?php echo $circle->id ?>"/>
													</form>
													<a href="#" class="button edit_circle_list_item"></a>
													<input type="hidden" class="circle_edit_id" name="circle_edit_id"
													       value="<?php echo $circle->id ?>"/>

												</div>
											</li>
											<?php
											$i ++;
										}
										?>
									</ul>

									<?php
								} else {
									echo "<p class='empty_circle'>you have 0 circles on this map</p>";
								}
								?>
							</div>
							<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
								<input type="hidden" id="circle_get_id" name="circle_get_id"/>
								<div class="update_list_item hide">
									<a href="#" id="back_edit_circle" class="cancel left">◄ Back</a>
									<ul>
										<li class="has_background">
											<label for="circle_edit_name">Name</label>
											<input type="text" id="circle_edit_name" name="circle_edit_name" class="circle_edit_options_input"/>
										</li>
										<li>
											<label for="circle_edit_center_addr">Center Address</label>
											<input type="text" class="circle_edit_options_input" id="circle_edit_center_addr"
											       name="circle_edit_center_addr"/>
										</li>
										<li class="has_background">
											<label for="circle_edit_center_lat">Center Latitude</label>
											<input type="text" class="circle_edit_options_input" id="circle_edit_center_lat"
											       name="circle_edit_center_lat"/>
										</li>
										<li>
											<label for="circle_edit_center_lng">Center Longitude</label>
											<input type="text" class="circle_edit_options_input" id="circle_edit_center_lng"
											       name="circle_edit_center_lng"/>
										</li>

										<li class="has_background">
											<label>Show Marker?</label>
											<label class="radio_label" for="circle_edit_marker_show">YES</label>
											<input type="radio" class="radio circle_edit_marker_show" name="circle_edit_marker_show"
											       id="circle_edit_marker_show_true" value="1">
											<label class="radio_label" for="circle_edit_marker_show">NO</label>
											<input type="radio" class="radio circle_edit_marker_show" name="circle_edit_marker_show"
											       id="circle_edit_marker_show_false" value="0">
										</li>
										<li>
											<label for="circle_radius">Radius(meter)</label>
											<input type="number" class="circle_edit_options_input" id="circle_edit_radius" name="circle_edit_radius"
											       value="1000000"/>
										</li>
										<li class="has_background">
											<label for="circle_edit_line_width">Line Width</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="circle_edit_options_input" id="circle_edit_line_width"
												       name="circle_edit_line_width" data-slider-highlight="true"
												       data-slider-values="<?php for ( $i = 0; $i <= 50; $i ++ ) {
													       if ( $i == 50 ) {
														       echo $i;
													       } else {
														       echo $i . ",";
													       }
												       } ?>" type="text" data-slider="true" value=""/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>
										<li>
											<label for="circle_edit_line_color">Line Color</label>
											<input type="text" class="color circle_edit_options_input" id="circle_edit_line_color"
											       name="circle_edit_line_color" value="FF0000"/>
										</li>
										<li class="has_background">
											<label for="circle_edit_line_opacity">Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="circle_edit_options_input" id="circle_edit_line_opacity"
												       name="circle_edit_line_opacity" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"
												       value=""/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>
										<li>
											<label for="circle_edit_fill_color">Fill Color</label>
											<input type="text" class="color circle_edit_options_input" id="circle_edit_fill_color"
											       name="circle_edit_fill_color" value="00FF00"/>
										</li>
										<li class="has_background">
											<label for="circle_edit_fill_opacity">Fill Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="circle_edit_options_input" id="circle_edit_fill_opacity"
												       name="circle_edit_fill_opacity" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"
												       value=""/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>
										<li class="pro">
											<label for="hover_circle_edit_fill_color">On-Hover Fill Color</label>
											<input type="text" class="color circle_options_input" />
										</li>
										<li class="pro has_background">
											<label for="hover_circle_edit_fill_opacity">On-Hover Fill Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px;">
												<input class="circle_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>
										<li class="pro">
											<label for="hover_circle_edit_line_color">On-Hover Line Color</label>
											<input type="text" class="color circle_options_input" />
										</li>
										<li class="pro has_background">
											<label for="hover_circle_edit_line_opacity">On-Hover Line Transparency</label>
											<div class="slider-container" style="float:left; width:55%; height:25px; ">
												<input type="text" class="circle_options_input" data-slider-highlight="true"
												       data-slider-values="0,0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9,1" type="text" data-slider="true"/>
												<span style="position:absolute; top: 4px;left: 160px;"></span>
											</div>
										</li>
									</ul>
									<div>
										<input type="submit" class="button-primary" id="circle_edit_submit" name="circle_edit_submit" value="Save"/>
										<a href="#" id="cancel_edit_circle" class="cancel">Cancel</a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</li>
				<li class="editing_section">
					<div class="editing_heading">
						<span class="heading_icon"><img src="<?php echo untrailingslashit(HG_GMAP_IMAGES_URL).'/images/menu-icons/layers.svg'; ?>" width="20" /></span>
						Layers
						<div class="help">?
							<div class="help-block">
								<span class="pnt"></span>
								<p>Highlighted road areas on map, highlight the roads and real-time traffic on them, make bicycle road highlighted on
									your map, transit roads, which connects cities etc...</p>
							</div>
						</div>
						<span class="heading_arrow"></span>
					</div>
					<div class="edit_content map_options hide">
						<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
							<ul>
								<li class="pro has_background">
									<label for="traffic_layer_enable">Enable Traffic Layer

									</label>
									<input  type="checkbox" class="map_layers_inputs" value="true"/>
								</li>
								<li class="pro">
									<label for="bicycling_layer_enable">Enable Bicycling Layer</label>
									<input  type="checkbox" class="map_layers_inputs" value="true"/>
								</li>
								<li class="pro has_background">
									<label for="transit_layer_enable">Enable Transit layer</label>
									<input  type="checkbox" class="map_layers_inputs"  value="true"/>
								</li>
							</ul>
						</form>
					</div>
				</li>
				<li class="editing_section">
					<div class="editing_heading">
						<span class="heading_icon"><img src="<?php echo untrailingslashit(HG_GMAP_IMAGES_URL).'/images/menu-icons/styles.svg'; ?>" width="20" /></span>
						Map Styling
						<div class="help">?
							<div class="help-block">
								<span class="pnt"></span>
								<p>Choose some color/tone for the current map</p>
							</div>
						</div>
						<span class="heading_arrow"></span>
					</div>
					<div class="edit_content map_options hide">
						<form action="admin.php?page=hugeitgooglemaps_main&task=edit_cat&id=<?php echo esc_attr(intval($_GET['id'])); ?>" method="post">
							<ul>
								<li class="pro has_background">
									<label for="g_map_styling_hue">Hue(color)</label>
									<input type="text" class="color map_styling_options_inputs" value="<?php echo $thisMap->styling_hue; ?>"/>
								</li>
								<li class="pro">
									<label for="g_map_styling_lightness">Lightness</label>
									<div class="slider-container" style="float:left; width:55%; height:25px; ">
										<input class="map_styling_options_inputs"
										       data-slider-highlight="true" data-slider-values="<?php for ( $i = - 100; $i < 101; $i ++ ) {
											if ( $i != 100 ) {
												echo $i . ",";
											} else {
												echo $i;
											}
										} ?>" type="text" data-slider="true" value="<?php echo $thisMap->styling_lightness; ?>"/>
										<span style="position:absolute; top: 4px;left: 160px;"><?php echo $thisMap->styling_lightness; ?></span>
									</div>
								</li>
								<li class="pro has_background">
									<label for="g_map_styling_saturation">Saturation</label>
									<div class="slider-container" style="float:left; width:55%; height:25px; ">
										<input class="map_styling_options_inputs"
										       data-slider-highlight="true" data-slider-values="<?php for ( $i = - 100; $i < 101; $i ++ ) {
											if ( $i != 100 ) {
												echo $i . ",";
											} else {
												echo $i;
											}
										} ?>" type="text" data-slider="true" value="<?php echo $thisMap->styling_saturation; ?>"/>
										<span style="position:absolute; top: 4px;left: 160px;"><?php echo $thisMap->styling_saturation; ?></span>
									</div>
								</li>
								<li class="pro">
									<label for="g_map_styling_gamma">Gamma</label>
									<div class="slider-container" style="float:left; width:55%; height:25px; ">
										<input class="map_styling_options_inputs"
										       data-slider-highlight="true" data-slider-values="<?php for ( $i = 1; $i < 11; $i ++ ) {
											if ( $i != 10 ) {
												echo $i . ",";
											} else {
												echo $i;
											}
										} ?>" type="text" data-slider="true" value="<?php echo $thisMap->styling_gamma; ?>"/>
										<span style="position:absolute; top: 4px;left: 160px;"><?php echo $thisMap->styling_gamma; ?></span>
									</div>
								</li>
							</ul>
						</form>
					</div>
				</li>

			</ul>
			<div id="g_maps" >
				<div id="g_map_canvas" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<div id="g_map_marker" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<div id="g_map_marker_edit" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<div id="g_map_polygon" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<div id="g_map_polyline" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<div id="g_map_circle" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<div id="g_map_direction" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<div id="g_map_polygone_edit" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<div id="g_map_polyline_edit" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<div id="g_map_circle_edit" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<div id="g_map_direction_edit" class="g_map hide"
				     style="height:<?php echo $thisMap->height ?>px;width:<?php echo $thisMap->width / 2 ?>%;border-radius:<?php echo $thisMap->border_radius ?>px;position:relative !important"></div>
				<span class="hg_gmaps_map_notice_wrapper"><span class="hg_gmaps_map_notice"></span></span>
			</div>

			<div class="map_database_actions_section">
				<div class="button copy_map_button" data-map-id="<?= $thisMap->id; ?>">Create Copy Of This Map</div>
				<div class="button extract_to_csv_button" data-map-id="<?= $thisMap->id; ?>">Export This Map To CSV</div>
			</div>
			<div class="shortcode_containers">
				<div class="shortcode_container">
					<div class="shortcode_heading">Shortcode</div>
					<p class="shortcode_description">Copy & paste the shortcode directly into any WordPress post or page.</p>
					<div class="shortcode_view">[huge_it_maps id="<?php echo $thisMap->id; ?>"]</div>
				</div>
				<div class="shortcode_container">
					<div class="shortcode_heading">Template Include</div>
					<p class="shortcode_description">Copy & paste this code into a template file to include the map within your theme.</p>
					<div class="shortcode_view">&lt;?php echo do_shortcode("[huge_it_maps id='<?php echo $thisMap->id; ?>']"); ?&gt;</div>
				</div>
			</div>
		</div>
		<style>
			.edit_list_delete a {
				background: url(<?php echo plugins_url("../images/edit1.png",__FILE__); ?>) center center no-repeat !important;
			}
			.pro {
				background: url(<?php echo plugins_url("../images/pro01.png",__FILE__); ?>) 39% center no-repeat;
			}
			.pro * {
				opacity: 0.6;
			}
			.marker_icon{
				cursor: pointer;
				opacity: 0.6;
			}
			.hg_gmaps_pro_icon{
				background: url(<?php echo plugins_url("../images/pro01.png",__FILE__); ?>) 0% center no-repeat;
			}
		</style>
		<?php ;
	}

}
?>