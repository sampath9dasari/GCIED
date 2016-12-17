var hgCloseNoticeTimeout = false;

function hgGmapsNoticeOptimize(){
    if(jQuery(".hg_gmaps_map_notice_wrapper").length){
        var bigLeft = jQuery(".g_map:not(.hide)").offset().left;
        var computed = getComputedStyle(jQuery(".g_map:not(.hide)")[0]);
        var width = parseInt(computed.width) - parseInt(jQuery(".hg_gmaps_map_notice_wrapper").width()) - 50;
        var left = jQuery("#g_maps").offset().left;
        jQuery(".hg_gmaps_map_notice_wrapper").css( "left", bigLeft-left+width );
        window.requestAnimationFrame(hgGmapsNoticeOptimize);
    }

}

function hgGmapsShowNotice(text){
    jQuery(".hg_gmaps_map_notice").text(text);
    jQuery(".hg_gmaps_map_notice_wrapper").show(0);
    setTimeout(function(){
        if(hgCloseNoticeTimeout){
            clearTimeout(hgCloseNoticeTimeout);
        }
        hgCloseNoticeTimeout = setTimeout(function(){
            hgGmapsCloseNotice();
        },10000);

        hgGmapsNoticeOptimize();
    },500);

}

function hgGmapsCloseNotice(){
    jQuery(".hg_gmaps_map_notice_wrapper").fadeOut();
}


jQuery(document).ready(function () {

    jQuery(window).on("resize",hgGmapsNoticeOptimize);
    jQuery(".hg_gmaps_map_notice_wrapper").on("click", hgGmapsCloseNotice);

    jQuery('.admin_edit_section_container form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    if (jQuery(".hg_gmaps_save_api_key_button").length) {
        jQuery(".hg_gmaps_save_api_key_button").on('click', function () {
            var _this = jQuery(this);
            var key = jQuery(this).closest("form").find(".hg_gmaps_api_key_input").val();
            if (key != undefined && key != "") {
                var data = {
                    action: 'hg_gmaps_save_api_key',
                    hg_gmaps_nonce: ajax_object.hg_gmaps_nonce,
                    api_key: key
                };

                jQuery.ajax({
                    url: ajax_object.ajax_url,
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        jQuery(this).attr("disabled", true);
                        _this.parent().find(".spinner").css("visibility", "visible");
                    },
                    success: function (result) {
                        if (result.success) {
                            setTimeout(function () {
                                var successNotice = "<div id='hg_gmaps_api_key_success' class='notice notice-success is-dismissible'>" +
                                    "<p class='hg_mui_heading'>GOOGLE API KEY SAVED SUCCESSFULLY!</p>" +
                                    "<button type='button' class='notice-dismiss'><span class='screen-reader-text'>Dismiss this notice.</span></button>" +
                                    "</div>";
                                if (jQuery("#hg_gmaps_no_api_key_big_notice").length) {
                                    jQuery("#hg_gmaps_no_api_key_big_notice").replaceWith(successNotice);
                                } else if (jQuery(".free_version_banner").length) {
                                    jQuery(".free_version_banner").after(successNotice);
                                } else if (jQuery("#screen-meta").length) {
                                    jQuery("#screen-meta").after(successNotice);
                                } else {
                                    jQuery("#wpbody-content").prepend(successNotice);
                                }
                                jQuery("#hg_gmaps_api_key_success .notice-dismiss").on("click", function () {
                                    jQuery(this).parent().remove();
                                });
                                var form = _this.closest("form");
                                if (form.hasClass("hg_gmaps_main_api_form")) {
                                    form.find("button").css("visibility", "hidden");
                                    form.find(".spinner").css("visibility", "hidden");
                                }

                                if (jQuery(".hg_gmaps_main_api_form").length && jQuery(".hg_gmaps_main_api_form").hasClass("hide")) {
                                    jQuery(".hg_gmaps_main_api_form").removeClass("hide");
                                    jQuery(".hg_gmaps_main_api_form .hg_gmaps_api_key_input").val(key);
                                }

                            }, 1500);
                            setTimeout(function () {
                                location.reload();
                            },2500);
                        }
                    },
                    error: function () {
                        ecwp.pageLoaded();
                    }
                })
            }
            return false;
        });
    }

    jQuery(".hg_gmaps_main_api_form .hg_gmaps_api_key_input").on("keyup", function () {
        if (jQuery(this).val() != "") {
            jQuery(this).closest("form").find("button").css("visibility", "visible");
        }
    });

     if (jQuery('#g_maps').length) {
	        var el = jQuery('#g_maps');
	        var elpos_original = el.offset().top;
	        jQuery(window).scroll(function () {
	            var elpos = el.offset().top;
	            var windowpos = jQuery(window).scrollTop();
	            var finaldestination = windowpos;
	            if (windowpos+75 < elpos_original) {
	                finaldestination = elpos_original;
	                el.stop().css({'top': 3});
	            } else {
	                el.stop().animate({'top': finaldestination - elpos_original + 100}, 500);
	            }
	        });
	    }







    jQuery('.help').hover(function () {
        jQuery(this).parent().find('.help-block').removeClass('active');
        var width = jQuery(this).parent().find('.help-block').outerWidth();
        jQuery(this).parent().find('.help-block').addClass('active');
    }, function () {
        jQuery(this).parent().find('.help-block').removeClass('active');
    });

    var updated_div = jQuery(".updated");
    var nag_div = jQuery(".update-nag");
    setInterval(function () {
        updated_div.hide(100);
        nag_div.hide(100);
    }, 1000);

    // TAB NAVIGATION <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*********************************************************************************************************
    jQuery(".editing_heading").on('click', function () {
        var active,parent,content;
        if (jQuery(this).parent().hasClass("active_option_tab")) {
            active = jQuery(this).parent().parent().find(".active_option_tab");
            active.find(".tab_options_hidden_section").css({display: "block"});
            active.find(".tab_options_active_section").css({display: "none"});
            //jQuery(this).find(".heading_arrow").html("▼");
            jQuery(this).parent().removeClass("active_option_tab");
            jQuery("#g_map_canvas").trigger("resize");
            parent = jQuery(this).parent();
            content = parent.find(".edit_content");
            content.slideUp(200);
            jQuery("#g_maps > div").addClass("hide");

            jQuery("#g_map_canvas").removeClass("hide");
        } else {
            //jQuery(this).find(".heading_arrow").html("▲");
            active = jQuery(this).parent().parent().find(".active_option_tab");
            active.find(".edit_content").slideUp(200);
            //active.find(".heading_arrow").html("▼");
            active.removeClass("active_option_tab");
            active.find(".tab_options_hidden_section").css({display: "block"});
            active.find(".tab_options_active_section").css({display: "none"});
            jQuery(".marker_image_choose ul li.active").removeClass("active");
            jQuery("#g_map_canvas").trigger("resize");
            jQuery(this).parent().addClass("active_option_tab");
            parent = jQuery(this).parent();
            content = parent.find(".edit_content");
            content.slideDown(200);
            jQuery("#g_maps > div").addClass("hide");
            jQuery("#g_map_canvas").removeClass("hide");
        }
        jQuery('html, body').animate({scrollTop: 0}, 250);
    });


    jQuery("#marker_add_button").on("click", function (e) {
        jQuery(this).hide("fast").addClass("tab_options_hidden_section");
        jQuery("#g_maps > div").not("#g_map_marker").addClass("hide");
        jQuery("#g_map_marker").removeClass("hide");
        jQuery("#markers_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery(".update_marker_list_item").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_marker_options .hidden_edit_content").show(200).addClass("tab_options_active_section");
        //

        return false;
    })

    jQuery("#cancel_marker, #back_marker").on("click", function (e) {
        jQuery("#marker_add_button").show(200);
        jQuery("#g_maps > div").not("#g_map_canvas").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#markers_edit_exist_section").show(200);
        jQuery(".update_marker_list_item").show(200);
        jQuery(".marker_image_choose ul li.active").removeClass("active");
        jQuery("#g_map_marker_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    })

    jQuery("#cancel_edit_marker, #back_edit_marker").on("click", function () {
        jQuery("#marker_add_button").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery(".marker_image_choose ul li.active").removeClass("active");
        jQuery("#markers_edit_exist_section").show(200);
        jQuery(this).parentsUntil(".editing_section").find(".update_list_item").hide(200);
        jQuery("#marker_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    })

    jQuery("#cancel_polygone, #back_polygone").on("click", function (e) {
        jQuery("#polygon_add_button").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#polygone_edit_exist_section").show(200);
        jQuery("#g_map_polygone_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    })

    jQuery("#cancel_edit_polygone, #back_edit_polygone").on("click", function (e) {
        jQuery(".edit_polygone_list_delete a").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#polygone_edit_exist_section").show(200);
        jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").hide(200);
        jQuery("#polygon_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    })
    jQuery("#polygon_add_button").on('click', function (e) {
        jQuery(this).hide(100).addClass("tab_options_hidden_section");
        jQuery("#g_maps > div").not("#g_map_polygon").addClass("hide");
        jQuery("#g_map_polygon").removeClass("hide");
        jQuery("#polygone_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_polygone_options .hidden_edit_content").show(200).addClass("tab_options_active_section");
        var center_lat = jQuery("#map_center_lat").val();
        var center_lng = jQuery("#map_center_lng").val();
        jQuery("#polygone_coords").val("");
        return false;
    })

    jQuery("#cancel_polyline, #back_polyline").on("click", function (e) {
        jQuery("#polyline_add_button").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#polyline_edit_exist_section").show(200);
        jQuery("#g_map_polyline_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    })

    jQuery("#cancel_edit_polyline, #back_edit_polyline").on("click", function (e) {
        jQuery(".edit_polyline_list_delete a").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#polyline_edit_exist_section").show(200);
        jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").hide(200);
        jQuery("#polyline_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    })

    jQuery("#polyline_add_button").on('click', function (e) {
        jQuery(this).hide("fast").addClass("tab_options_hidden_section");
        jQuery("#g_maps > div").not("#g_map_polygon").addClass("hide");
        jQuery("#g_map_polyline").removeClass("hide");
        jQuery("#polyline_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_polyline_options .hidden_edit_content").show(200).addClass("tab_options_active_section");
        jQuery("#polyline_coords").val("");
        return false;
    })

    /** Add Direction Button handling */
    jQuery("#direction_add_button").on('click', function (e) {
        jQuery(this).hide("fast").addClass("tab_options_hidden_section");
        jQuery("#g_maps > div").not("#g_map_direction").addClass("hide");
        jQuery("#g_map_direction").removeClass("hide");
        jQuery("#direction_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_direction_options .hidden_edit_content").show(200).addClass("tab_options_active_section");
        jQuery("#direction_name,#direction_start_addr,#direction_start_lat,#direction_start_lng,#direction_end_addr,#direction_end_lat,#direction_end_lng").val("");
        jQuery("#direction_line_opacity").simpleSlider("setValue", '0.9');
        jQuery("#direction_line_color").val('FF0F0F');
        jQuery("#direction_line_width").val('5');
        jQuery("#hover_direction_line_opacity").simpleSlider("setValue", '0.5');
        jQuery("#hover_direction_line_color").val('FF80B7');

        google.maps.event.trigger(mapdirection, 'resize');
        mapdirection.setCenter(mapcenter);
        if (newDirection) {
            newDirection.setMap(null);
            newDirection = false;
            newDirectionStartMarker.setMap(null);
            newDirectionStartMarker = false;
            newDirectionCoords = [];
            newDirectionsDisplay = false;
        }

        return false;
    });

    /** Cancel creating a direction */
    jQuery("#cancel_direction, #back_direction").on("click", function (e) {
        jQuery("#direction_add_button").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#direction_edit_exist_section").show(200);
        jQuery("#g_map_direction_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        jQuery("#direction_start_addr, #direction_start_lat, #direction_start_lng, #direction_end_addr, #direction_end_lat, #direction_end_lng").val("");
        jQuery("#direction_options_input").removeAttr("checked");

        if(newDirectionStartMarker){
            newDirectionStartMarker.setMap(null);
        }

        newDirection = false;
        newDirectionStartMarker = false;
        newDirectionCoords = [];
        newDirectionsDisplay = false;
        directionsService = new google.maps.DirectionsService();
        stepDisplay = new google.maps.InfoWindow;
        newDirectionMode = 'DRIVING';
        newDirectionShowSteps = false;
        newDirectionMarkers = [];

        return false;
    });

    /** Cancel Editing a direction */
    jQuery("#cancel_edit_direction, #back_edit_direction").on("click", function (e) {
        jQuery(".edit_direction_list_delete a").show(200);
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#direction_edit_exist_section").show(200);
        jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").hide(200);
        jQuery("#direction_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);

        editDirection.setMap(null);
        editDirection = false;
        editDirectionCoords = [];
        editDirectionsDisplay = false;
        editDirectionMode = 'DRIVING';
        editDirectionShowSteps = false;

        return false;
    });

    jQuery("#cancel_circle, #back_circle").on("click", function (e) {
        jQuery("#circle_add_button").show("fast");
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#circle_edit_exist_section").show(200);
        jQuery("#g_map_circle_options .hidden_edit_content").hide(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
        return false;
    });

    jQuery("#cancel_edit_circle, #back_edit_circle").on("click", function (e) {
        jQuery("#g_maps > div").not("#g_map_polygon").addClass("hide");
        jQuery("#g_map_canvas").removeClass("hide");
        jQuery("#circle_edit_exist_section").show(200);
        jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").hide(200);
        jQuery("#circle_add_button").show(200);
        jQuery('html, body').animate({scrollTop: 0}, 250);
    })

    jQuery("#circle_add_button").on("click", function (e) {
        jQuery(this).hide("fast").addClass("tab_options_hidden_section");
        jQuery("#g_maps > div").addClass("hide");
        jQuery("#g_map_circle").removeClass("hide");
        jQuery("#circle_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
        jQuery("#g_map_circle_options .hidden_edit_content").show(200).addClass("tab_options_active_section");
        return false;
    })


    jQuery(".marker_image_choose_button").on("click", function () {
        jQuery(this).parent().parent().find(".active").removeClass("active");
        jQuery(this).parent().addClass("active");
    })

    jQuery(".front_end_input_options").on("keyup change", function () {
        var width = parseInt(jQuery("#map_width").val()) / 2;
        var height = jQuery("#map_height").val();
        var border_radius = jQuery("#map_border_radius").val();
        jQuery(".g_map").css({width: width + "%", height: height + "px", borderRadius: border_radius + "px"})
    })

})
