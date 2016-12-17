function hgGmapsInitializeAllMaps(id, response){
	loadMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit, response.animation);
	loadMarkerMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
	loadPolygonMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
	loadPolylineMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
	loadCircleMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
	loadDirectionsMap(id, "#" + response.hue, response.saturation, response.lightness, response.gamma, response.zoom, response.type, response.bike, response.traffic, response.transit);
}

jQuery(document).ready(function(){

	jQuery("#direction_edit_submit").on("click",function(){
		if(!editDirection){
			alert("Oops, looks like somethin is wrong, We can't find the direction to save");
		}

		var _this = jQuery(this),
			name = jQuery("#direction_edit_name").val(),
			startLat = jQuery("#direction_edit_start_lat").val(),
			startLng = jQuery("#direction_edit_start_lng").val(),
			endLat = jQuery("#direction_edit_end_lat").val(),
			endLng = jQuery("#direction_edit_end_lng").val(),
			travelMode = jQuery("#direction_edit_travelmode").val(),
			showSteps = jQuery("#direction_edit_show_steps").is(":checked") ? 'yes' : 'no',
			lineOpacity = jQuery( "#direction_edit_line_opacity" ).val(),
			lineColor = jQuery( "#direction_edit_line_color" ).val(),
			lineWidth = jQuery( "#direction_edit_line_width" ).val(),
			id = jQuery("#direction_get_id").val(),
			map_id = jQuery("#map_id").val();

			var data = {
				action: "g_map_options",
				task: "submit_edit_direction",
				nonce : map_ajax_l10n.hg_gmaps_nonce,
				id: id,
				map_id : map_id,
				name: name,
				startLat: startLat,
				startLng: startLng,
				endLat: endLat,
				endLng: endLng,
				travelMode: travelMode,
				showSteps: showSteps,
				lineOpacity: lineOpacity,
				lineColor: lineColor,
				lineWidth: lineWidth
			};

		jQuery.ajax({
			url:map_ajax_l10n.ajax_url,
			type: 'post',
			data: data,
			dataType: 'json',
			beforeSend: function(xhr) {
				_this.parent().find(".spinner").css("visibility","visible");
			}
		}).done(function (response) {
			_this.parent().find(".spinner").css("visibility","hidden");
			if(response.success){

				hgGmapsInitializeAllMaps(map_id, response);
				jQuery("#cancel_edit_direction").trigger("click");
				jQuery(document).scrollTop(0);
				jQuery("#direction_edit_exist_section li").each(function () {
					if (jQuery(this).attr("data-list_id") == id) {
						jQuery(this).find(".edit_list_item").html(name)
					}
				})
			}

		}).fail(function(){
			console.log("Failed to save the direction");
		});

		return false;
	});

	jQuery("#direction_submit").on("click",function(){

		if(!newDirection){
			alert("First create a directino please");
			return false;
		}

		var _this = jQuery(this),
			name = jQuery("#direction_name").val(),
			startLat = jQuery("#direction_start_lat").val(),
			startLng = jQuery("#direction_start_lng").val(),
			endLat = jQuery("#direction_end_lat").val(),
			endLng = jQuery("#direction_end_lng").val(),
			travelMode = jQuery("#direction_travelmode").val(),
			showSteps = jQuery("#direction_show_steps").is(":checked") ? 'yes' : 'no',
			lineOpacity = jQuery( "#direction_line_opacity" ).val(),
			lineColor = jQuery( "#direction_line_color" ).val(),
			lineWidth = jQuery( "#direction_line_width" ).val(),
			id = jQuery("#map_id").val();

		var data = {
			action: "g_map_options",
			task: "submit_direction",
			nonce : map_ajax_l10n.hg_gmaps_nonce,
			id: id,
			name: name,
			startLat: startLat,
			startLng: startLng,
			endLat: endLat,
			endLng: endLng,
			travelMode: travelMode,
			showSteps: showSteps,
			lineOpacity: lineOpacity,
			lineColor: lineColor,
			lineWidth: lineWidth
		};

		jQuery.ajax({
			url:map_ajax_l10n.ajax_url,
			type: 'post',
			data: data,
			dataType: 'json',
			beforeSend: function(xhr) {
				_this.parent().find(".spinner").css("visibility","visible");
			}
		}).done(function (response) {
			_this.parent().find(".spinner").css("visibility","hidden");
			if (response.success) {
				hgGmapsInitializeAllMaps(id, response);
				jQuery("#cancel_direction").trigger("click");
				jQuery(document).scrollTop(0);
				if (jQuery(".empty_direction").html() != undefined) {
					jQuery(".empty_direction").after("<ul>" +
							"<li class='edit_list has_background' data-list_id='" + response.last_id + "'>" +
								"<div class='list_number' >1</div><div class='edit_list_item'>" + name + "</div>" +
								"<div class='edit_direction_list_delete edit_list_delete'>" +
									"<form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='" + id + "'>" +
										"<input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' />" +
										"<input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='direction' />" +
										"<input type='hidden' class='edit_list_delete_table' value='g_directions' />" +
										"<input type='hidden' name='delete_direction_id' class='edit_list_delete_id' value='" + response.last_id + "' />" +
									"</form>" +
									"<a href='#' class='button' class='edit_direction_list_item' ></a>" +
									"<input type='hidden' class='direction_edit_id' name='direction_edit_id' value='" + response.last_id + "' />" +
								"</div>" +
							"</li>" +
						"</ul>");
					jQuery(".empty_direction").remove();
				} else {
					var last_id = jQuery("#direction_edit_exist_section .edit_list").last().find(".list_number").html();
					var this_id = parseInt(last_id) + 1;
					jQuery("#direction_edit_exist_section .edit_list").last().after("<li class='edit_list has_background' data-list_id='" + response.last_id + "'>" +
							"<div class='list_number' >" + this_id + "</div><div class='edit_list_item'>" + name + "</div>" +
							"<div class='edit_direction_list_delete edit_list_delete'>" +
								"<form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='" + id + "'>" +
									"<input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' />" +
									"<input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='direction' />" +
									"<input type='hidden' class='edit_list_delete_table' value='g_directions' />" +
									"<input type='hidden' name='delete_direction_id' class='edit_list_delete_id' value='" + response.last_id + "' />" +
								"</form>" +
								"<a href='#' class='button' class='edit_direction_list_item' ></a>" +
								"<input type='hidden' class='direction_edit_id' name='direction_edit_id' value='" + response.last_id + "' />" +
							"</div>" +
							"</li>");
				}
			} else {
				console.log("Oops, something went wrong");
			}
		}).fail(function () {
			console.log('Failed to save the direction');
		});
		return false;
	});

	jQuery(".copy_map_button").on("click",function(){
		var map_id=jQuery(this).data("map-id");
		var data={
			action:"g_map_options",
			task:"copy_map",
			map_id:map_id
		};
		jQuery.post(map_ajax_l10n.ajax_url,data,function(response){
			if(response.success){
				window.location.href = window.location.href + "?page=hugeitgooglemaps_main&task=edit_cat&id=" + response.new_map_id ;
			}else{
				
			}
		},"json");
	});
	
	jQuery(".extract_to_csv_button").on("click",function(){
		var map_id=jQuery(this).data("map-id");
		var data = {
			action:"g_map_options",
			task:"export_to_csv",
			map_id:map_id
		};
		
		function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
			/*If JSONData is not an object then JSON.parse will parse the JSON string in an Object*/
			var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
			var CSV = '';    
			/*Set Report title in first row or line*/
			CSV += ReportTitle + '\r\n\n';
			/*This condition will generate the Label/Header*/
			if (ShowLabel) {
				var row = "";
				/*This loop will extract the label from 1st index of on array*/
				for (var index in arrData[0]) {
					/*Now convert each value to string and comma-seprated*/
					row += index + ',';
				}
				row = row.slice(0, -1);
				/*append Label row with line break*/
				CSV += row + '\r\n';
			}
			/*1st loop is to extract each row*/
			for (var i = 0; i < arrData.length; i++) {
				var row = "";
				row=arrData[i];
				/*2nd loop will extract each column and convert it in string comma-seprated*/
				row.slice(0, row.length - 1);
				/*add a line break after each row*/
				CSV += row + '\r\n';
			}
			if (CSV == '') {        
				alert("Invalid data");
				return;
			}   
			var fileName = "";
			fileName += ReportTitle.replace(/ /g,"_");   
			var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
			// Now the little tricky part.
			// you can use either>> window.open(uri);
			// but this will not work in some browsers
			// or you will not get the correct file extension    
			//this trick will generate a temp <a /> tag
			var link = document.createElement("a");    
			link.href = uri;
			//set the visibility hidden so it will not effect on your web-layout
			link.style = "visibility:hidden";
			link.download = fileName + ".csv";
			//this part will append the anchor tag and remove it after automatic click
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		}
		
		jQuery.post(map_ajax_l10n.ajax_url,data,function(response){
			if(response.success){
				var name="";
				if(response.map_name!=""){
					name=response.map_name;
				}else{
					name=map_id
				}
				JSONToCSVConvertor(response.string, "Map Info_"+name, false);
			}
			
		},'json');
	});
});