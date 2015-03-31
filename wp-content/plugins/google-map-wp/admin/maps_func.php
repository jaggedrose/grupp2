<?php
function add_map()
{
	global $wpdb;
	$sql = "INSERT INTO ".$wpdb->prefix ."g_maps (name , type, zoom, center_lat, center_lng) VALUES ('New Map', 'ROADMAP', '2', '0', '0')";
	$wpdb->query($sql);
	$rowsldcc = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."g_maps ORDER BY id ASC");
	$last_key = key(array_slice($rowsldcc, -1, 1 , TRUE));
	foreach($rowsldcc as $key=>$rowsldccs)
	{
		if($last_key == $key)
		{
			if (headers_sent()) {
				die("Redirect failed.");
			}
			else{
				exit(header('Location: admin.php?page=hugeitgooglemaps_main&id='.$rowsldccs->id.'&task=edit_cat'));
			}
		}
	}
}
function remove_map($id)
{
	global $wpdb;
	$removeMap = $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."g_maps WHERE id=%d",$id));
	$removeMarkers = $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."g_markers WHERE map=%d",$id));
	$removePolygons = $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."g_polygones WHERE map=%d",$id));
	$removePolylines = $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."g_polylines WHERE map=%d",$id));
	$removeCircles = $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."g_circles WHERE map=%d",$id));
	if($removeMap)
	{
		?>
		<div class="updated"><p><strong><?php _e('Item Deleted.' ); ?></strong></p></div>
		<?php
	}
}

function maps_js($id)
{
	global $wpdb;
	$sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."g_maps WHERE id=%d", $id);
	$map = $wpdb->get_results($sql);
	foreach($map as $map)
	{
		?>
			<script>
				var data;
				var marker = [];
				var infowindow =[];
				var polyline = [];
				var circle = [];
				var newcirclemarker = [];
				var geocoder;
				
				jQuery(document).ready(function(){
					jQuery("#map_name_tab").on("keyup change",function(){
						var name = jQuery(this).val();
						var data = {
							action: 'g_map_options',
							task:"change_name",
							id:<?php echo $map->id; ?>,
							name : name,
						}
						jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", data, function(response){
							if(response.success)
							{
								jQuery("#map_name").val(name);
							}
						},'json')
					})
					jQuery("#map_name").on("keyup change",function(){
						var name = jQuery(this).val();
						var data = {
							action: 'g_map_options',
							task:"change_name",
							id:<?php echo $map->id; ?>,
							name : name,
						}
						jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", data, function(response){
							if(response.success)
							{
								jQuery("#map_name_tab").val(name);
							}
						},'json')
					})

					jQuery("#map_name").on("keyup change",function(){
						var name = jQuery(this).val();
						var data = {
							action : 'g_map_options',
							map_id: <?php echo $map->id; ?>,
							name : name,
						}
						jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", data, function(response){
							if(response.success)
							{
								jQuery("#map_name_tab").val(name);
							}
						},'json')
					})
					
					
					loadMap("<?php echo $map->id; ?>","#<?php echo $map->styling_hue; ?>","<?php echo $map->styling_saturation; ?>","<?php echo $map->styling_lightness; ?>","<?php echo $map->styling_gamma; ?>","<?php echo $map->zoom; ?>","<?php echo $map->type; ?>","<?php echo $map->bike_layer; ?>","<?php echo $map->traffic_layer; ?>","<?php echo $map->transit_layer; ?>");
					
					
					
				})
				function loadMap(id,hue,saturation,lightness,gamma,zoom,type,bike,traffic,transit)
					{
							
						data = {
							action:'g_map_options',
							map_id:id,
							task:"getxml",
						}
						jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>",data,function(response){
							if(response.success)
							{
								var xml = jQuery.parseXML(response.success);
								console.log(xml);
								var maps = xml.documentElement.getElementsByTagName("map");
								for(var i = 0; i < maps.length; i++)
								{
									var pan_controller = maps[i].getAttribute("pan_controller");
									var zoom_controller = maps[i].getAttribute("zoom_controller");
									var type_controller = maps[i].getAttribute("type_controller");
									var scale_controller = maps[i].getAttribute("scale_controller");
									var street_view_controller = maps[i].getAttribute("street_view_controller");
									var overview_map_controller = maps[i].getAttribute("overview_map_controller");
									var mapcenter = new google.maps.LatLng(
										parseFloat(maps[i].getAttribute("center_lat")),
										parseFloat(maps[i].getAttribute("center_lng")));
									
									
									geocoder = new google.maps.Geocoder();
									geocoder.geocode({'latLng':mapcenter},function(results, status){
										if (status == google.maps.GeocoderStatus.OK) {
											address = results[0].formatted_address;
											jQuery("#map_center_addr").val(address);
										}
									})	
									
									
									var mapOptions = {
										zoom:parseInt(zoom),
										center: mapcenter,
										disableDefaultUI: true,
									}
									
									
									
									
									map_admin_view = new google.maps.Map(document.getElementById('g_map_canvas'), mapOptions);
									
									var input = document.getElementById("map_center_addr");
									var autocomplete = new google.maps.places.Autocomplete(input);
									google.maps.event.addListener(autocomplete, 'place_changed', function(){
										
										var addr = jQuery("#map_center_addr").val();
										var geocoder = geocoder = new google.maps.Geocoder();
										//alert(addr);
										geocoder.geocode({'address':addr},function(results, status){
											if (status == google.maps.GeocoderStatus.OK) {
												address = results[0].geometry.location;
												map_admin_view.setCenter(address);
												jQuery("#map_center_lat").val(address.lat());
												jQuery("#map_center_lng").val(address.lng());
											}
										 })
									});
									
									jQuery(".editing_heading").on("click",function(){
										
										google.maps.event.trigger(map_admin_view, 'resize');
										map_admin_view.setCenter(mapcenter);
										map_admin_view.setZoom(parseInt(zoom));
									})
									
									
									if(pan_controller == "true")
									{
										map_admin_view.setOptions({
											panControl: true,
										})
									}
									else
									{
										map_admin_view.setOptions({
											panControl: false,
										})
									}
									if(zoom_controller == "true")
									{
										map_admin_view.setOptions({
											zoomControl: true,
										})
									}
									else
									{
										map_admin_view.setOptions({
											zoomControl: false,
										})
									}
									if(type_controller == "true")
									{
										map_admin_view.setOptions({
											mapTypeControl: true,
										})
									}
									else
									{
										map_admin_view.setOptions({
											mapTypeControl: false,
										})
									}
									if(scale_controller == "true")
									{
										map_admin_view.setOptions({
											scaleControl: true,
										})
									}
									else
									{
										map_admin_view.setOptions({
											scaleControl: false,
										})
									}
									if(street_view_controller == "true")
									{
										map_admin_view.setOptions({
											streetViewControl: true,
										})
									}
									else
									{
										map_admin_view.setOptions({
											streetViewControl: false,
										})
									}
									if(overview_map_controller == "true")
									{
										map_admin_view.setOptions({
											overviewMapControl: true,
										})
									}
									else
									{
										map_admin_view.setOptions({
											overviewMapControl: false,
										})
									}
									
									jQuery(".map_controller_input").on("click",function(){
										if(jQuery('#map_controller_pan').is(':checked'))
										{
											map_admin_view.setOptions({
												panControl: true,
											})
										}
										else
										{
											map_admin_view.setOptions({
												panControl: false,
											})
										}
										if(jQuery('#map_controller_zoom').is(':checked'))
										{
											map_admin_view.setOptions({
												zoomControl: true,
											})
										}
										else
										{
											map_admin_view.setOptions({
												zoomControl: false,
											})
										}
										if(jQuery('#map_controller_type').is(':checked'))
										{
											map_admin_view.setOptions({
												mapTypeControl: true,
											})
										}
										else
										{
											map_admin_view.setOptions({
												mapTypeControl: false,
											})
										}
										if(jQuery('#map_controller_scale').is(':checked'))
										{
											map_admin_view.setOptions({
												scaleControl: true,
											})
										}
										else
										{
											map_admin_view.setOptions({
												scaleControl: false,
											})
										}
										if(jQuery('#map_controller_street_view').is(':checked'))
										{
											map_admin_view.setOptions({
												streetViewControl: true,
											})
										}
										else
										{
											map_admin_view.setOptions({
												streetViewControl: false,
											})
										}
										if(jQuery('#map_controller_overview').is(':checked'))
										{
											map_admin_view.setOptions({
												overviewMapControl: true,
											})
										}
										else
										{
											map_admin_view.setOptions({
												overviewMapControl: false,
											})
										}
									})
									var markers = xml.documentElement.getElementsByTagName("marker");
									for(j = 0; j < markers.length; j++)
									{
										var name = markers[j].getAttribute("name");
										var address = markers[j].getAttribute("address");
										var anim = markers[j].getAttribute("animation");
										var description = markers[j].getAttribute("description");
										var point = new google.maps.LatLng(
											parseFloat(markers[j].getAttribute("lat")),
											parseFloat(markers[j].getAttribute("lng")));
										var html = "<b>" + name + "</b> <br/>" + address;
										if(anim == 'DROP'){
											marker[j] = new google.maps.Marker({
											map: map_admin_view,
											position: point,
											title: name,
											content: description,
											animation: google.maps.Animation.DROP,
											});
										}
										if(anim == 'BOUNCE'){
											marker[j] = new google.maps.Marker({
											map: map_admin_view,
											position: point,
											title: name,
											content: description,
											animation: google.maps.Animation.BOUNCE
											});
										}
										if(anim == 'NONE'){
												marker[j] = new google.maps.Marker({
												map: map_admin_view,
												position: point,
												content: description,
												title: name,
											});
										}
										infowindow[j] = new google.maps.InfoWindow;
										bindInfoWindow(marker[j], map_admin_view, infowindow[j], description);
										jQuery(".edit_list_delete_submit").on("click",function(){
											var parent = jQuery(this).parent();
											var typeelement = parent.find(".edit_list_delete_type");
											var type =typeelement.val();
											var parent = jQuery(this).parent();
											var idelement = parent.find(".edit_list_delete_id");
											var tableelement = parent.find(".edit_list_delete_table");
											var id=idelement.val();
											var table = tableelement.val();
											var li = jQuery(this).parent().parent().parent();
											var x = li.index();
											if(type=="marker")
											{
												marker[x].setMap(null);
												deleteItem(id,table,li,x);
											}
											return false;
										})
									}
								}
								var polygones = xml.documentElement.getElementsByTagName("polygone");
								for(var k = 0; k < polygones.length; k++)
								{
									
									var name = polygones[k].getAttribute("name");
									var new_line_opacity = polygones[k].getAttribute("line_opacity");
									var new_line_color = "#"+polygones[k].getAttribute("line_color");
									var new_fill_opacity = polygones[k].getAttribute("fill_opacity");
									var new_line_width = polygones[k].getAttribute("line_width");
									var new_fill_color = "#"+polygones[k].getAttribute("fill_color");
									var latlngs = polygones[k].getElementsByTagName("latlng");
									var hover_new_line_opacity=polygones[k].getAttribute("hover_line_opacity");
									var hover_new_line_color="#"+polygones[k].getAttribute("hover_line_color");
									var hover_new_fill_opacity=polygones[k].getAttribute("hover_fill_opacity");
									var hover_new_fill_color="#"+polygones[k].getAttribute("hover_fill_color");
									polygoncoords = [];
									for(var g = 0; g < latlngs.length; g++)
									{
										polygonpoints = new google.maps.LatLng(parseFloat(latlngs[g].getAttribute("lat")),
											parseFloat(latlngs[g].getAttribute("lng")))
										polygoncoords.push(polygonpoints)
									}
									//alert(polygoncoords);

									polygone[k] = new google.maps.Polygon({
										paths : polygoncoords,
										map: map_admin_view,
										strokeOpacity: new_line_opacity,
										strokeColor:new_line_color,
										strokeWeight:new_line_width,
										fillOpacity:new_fill_opacity,
										fillColor:new_fill_color,
										draggable:false,
									});
									
									jQuery(".edit_list_delete_submit").on("click",function(){
										var parent = jQuery(this).parent();
										var typeelement = parent.find(".edit_list_delete_type");
										var type =typeelement.val();
										var parent = jQuery(this).parent();
											var idelement = parent.find(".edit_list_delete_id");
											var tableelement = parent.find(".edit_list_delete_table");
											var id=idelement.val();
											var table = tableelement.val();
											var li = jQuery(this).parent().parent().parent();
											var x = li.index();
										if(type=="polygone")
										{
											
											
												polygone[x].setMap(null);
												deleteItem(id,table,li,x);
										}
										return false;
									})
									
									
								}
								var polylines = xml.documentElement.getElementsByTagName("polyline");
								for(var q = 0; q< polylines.length; q++)
								{
									var name = polylines[q].getAttribute("name");
									var line_opacity = polylines[q].getAttribute("line_opacity");
									var line_color = polylines[q].getAttribute("line_color");
									var line_width = polylines[q].getAttribute("line_width");
									var latlngs = polylines[q].getElementsByTagName("latlng");
									var newpolylinecoords =[];
									for(var g = 0; g < latlngs.length; g++)
									{
										polylinepoints = new google.maps.LatLng(parseFloat(latlngs[g].getAttribute("lat")),
											parseFloat(latlngs[g].getAttribute("lng")))
										newpolylinecoords.push(polylinepoints)
									}
									polyline[q] = new google.maps.Polyline({
										path:newpolylinecoords,
										map:map_admin_view,
										strokeColor:"#"+line_color,
										strokeOpacity:line_opacity,
										strokeWeight:line_width,
									})
									jQuery(".edit_list_delete_submit").on("click",function(){
										var parent = jQuery(this).parent();
										var typeelement = parent.find(".edit_list_delete_type");
										var type =typeelement.val();
										var parent = jQuery(this).parent();
										var idelement = parent.find(".edit_list_delete_id");
										var tableelement = parent.find(".edit_list_delete_table");
										var id=idelement.val();
										var table = tableelement.val();
										var li = jQuery(this).parent().parent().parent();
										var x = li.index();
										if(type == "polyline")
										{
												polyline[x].setMap(null);
												deleteItem(id,table,li,x);
											
										}
										return false;
									})
								}
								var circles = xml.documentElement.getElementsByTagName("circle");
								for(var u = 0; u< circles.length; u++)
								{
									var circle_name =circles[u].getAttribute("name");
									var circle_center_lat = circles[u].getAttribute("center_lat");
									var circle_center_lng = circles[u].getAttribute("center_lng");
									var circle_radius = circles[u].getAttribute("radius");
									var circle_line_width = circles[u].getAttribute("line_width");
									var circle_line_color = circles[u].getAttribute("line_color");
									var circle_line_opacity = circles[u].getAttribute("line_opacity");
									var circle_fill_color = circles[u].getAttribute("fill_color");
									var circle_fill_opacity = circles[u].getAttribute("fill_opacity");
									var circle_show_marker = parseInt(circles[u].getAttribute("show_marker"));
									circlepoint = new google.maps.LatLng(parseFloat(circles[u].getAttribute("center_lat")),
									parseFloat(circles[u].getAttribute("center_lng")));
									circle[u] = new google.maps.Circle({
										map:map_admin_view,
										center:circlepoint,
										title:name,
										radius:parseInt(circle_radius),
										strokeColor:"#"+circle_line_color,
										strokeOpacity:circle_line_opacity,
										strokeWeight:circle_line_width,
										fillColor:"#"+circle_fill_color,
										fillOpacity:circle_fill_opacity
									})

									
									jQuery(".edit_list_delete_submit").on("click",function(){
										var parent = jQuery(this).parent();
										var typeelement = parent.find(".edit_list_delete_type");
										var type =typeelement.val();
										var parent = jQuery(this).parent();
										var idelement = parent.find(".edit_list_delete_id");
										var tableelement = parent.find(".edit_list_delete_table");
										var id=idelement.val();
										var table = tableelement.val();
										var li = jQuery(this).parent().parent().parent();
										var x = li.index();
										if(type=="circle")
										{
										
												circle[x].setMap(null);
												deleteItem(id,table,li,x);
										}
										return false;
									})
									if(circle_show_marker == '1')
									{
										newcirclemarker[i] = new google.maps.Marker({
											position:circlepoint,
											map:map_admin_view,
											title:circle_name,
										})
									}
								}
							};
							function deleteItem(id,table,li,x){
								var delete_data = {
									action:'g_map_options',
									id:id,
									table:table,
								}
								
								jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", delete_data, function(response){
									if(response.success)
									{
										li.remove();
									}
								},"json")
							}
						},"json")
					}
				function bindInfoWindow(marker, map, infowindow, description){
					google.maps.event.addListener(marker, 'click', function() {
						infowindow.setContent(description);
						infowindow.open(map, marker);
					});
				}
			</script>
		<?php
	}
}

function ajax_js($id){
	?>
	<script>
		jQuery(document).ready(function(){
			jQuery("#map_submit").on("click",function(){
				var map_name = jQuery("#map_name").val();
				if(jQuery("#map_controller_pan").is(":checked")){
					var map_controller_pan=jQuery("#map_controller_pan").val();
				}
				else{
					var map_controller_pan="false";
				}
				if(jQuery("#map_controller_zoom").is(":checked")){
					var map_controller_zoom=jQuery("#map_controller_zoom").val();
				}
				else{
					var map_controller_zoom="false";
				}
				if(jQuery("#map_controller_type").is(":checked")){
					var map_controller_type=jQuery("#map_controller_type").val();
				}
				else{
					var map_controller_type="false";
				}
				if(jQuery("#map_controller_scale").is(":checked")){
					var map_controller_scale=jQuery("#map_controller_scale").val();
				}
				else{
					var map_controller_scale="false";
				}
				if(jQuery("#map_controller_street_view").is(":checked")){
					var map_controller_street_view=jQuery("#map_controller_street_view").val();
				}
				else{
					var map_controller_street_view="false";
				}
				if(jQuery("#map_controller_overview").is(":checked")){
					var map_controller_overview=jQuery("#map_controller_overview").val();
				}
				else{
					var map_controller_overview="false";
				}
				var map_zoom = jQuery("#map_zoom").val();
				var map_center_lat = jQuery("#map_center_lat").val();
				var map_center_lng = jQuery("#map_center_lng").val();
				var map_width = jQuery("#map_width").val();
				var map_height = jQuery("#map_height").val();
				var map_align = jQuery("#map_align").val();
				var map_border_radius = jQuery("#map_border_radius").val();
				var id=jQuery("#map_id").val();
				var general_data = {
					action:"g_map_options",
					task:"submit_general_options",
					id:id,
					map_name:map_name,
					map_controller_pan:map_controller_pan,
					map_controller_zoom:map_controller_zoom,
					map_controller_type:map_controller_type,
					map_controller_scale:map_controller_scale,
					map_controller_street_view:map_controller_street_view,
					map_controller_overview:map_controller_overview,
					map_zoom:map_zoom,
					map_center_lat:map_center_lat,
					map_center_lng:map_center_lng,
					map_width:map_width,
					map_height:map_height,
					map_align:map_align,
					map_border_radius:map_border_radius,
				}
				//jQuery(this).css({background:"url(../wp-content/plugins/google-map-wp/images/122.gif) #2ea2cc 55px center no-repeat"});
				jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>",general_data,function(response){
					if(response.success){
						jQuery("#map_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						loadMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadMarkerMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolygonMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolylineMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadCircleMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						jQuery("html").parent().on("click",function(){
							jQuery("#map_submit").css({background:"#2ea2cc"});
						})
					}else{
						jQuery("#map_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						jQuery("html").parent().on("click",function(){
							jQuery("#map_submit").css({background:"#2ea2cc"});
						})
					}
				},"json")
				
				return false;
			})
			jQuery("#marker_submit").on("click",function(){
				var marker_location=jQuery("#marker_location").val();
				var marker_location_lat=jQuery("#marker_location_lat").val();
				var marker_location_lng=jQuery("#marker_location_lng").val();
				var marker_animation=jQuery("#marker_animation").val();
				var marker_title=jQuery("#marker_title").val();
				var marker_description=jQuery("#marker_description").val();
				var marker_image_size=jQuery("#marker_image_size").val();
				if(jQuery(this).parent().parent().find(".marker_image_choose ul li.active").html() != undefined){
					var marker_image=jQuery(this).parent().parent().find(".marker_image_choose ul li.active input[type=radio]").val();
					marker_image = "../wp-content/plugins/google-map-wp/images/icons/"+marker_image+""+marker_image_size+".png";
					
				}else{
					var marker_image=jQuery("#marker_pic").val();
				}
				
				var id=jQuery("#map_id").val();
				var marker_data = {
					action:"g_map_options",
					task:"submit_marker",
					id:id,
					marker_location:marker_location,
					marker_location_lat:marker_location_lat,
					marker_location_lng:marker_location_lng,
					marker_animation:marker_animation,
					marker_title:marker_title,
					marker_description:marker_description,
					marker_image:marker_image,
					marker_image_size:marker_image_size,
				}
				//alert(marker_location+marker_location_lat+marker_location_lng+marker_animation+marker_title+marker_description+marker_image+marker_image_size)
				//jQuery(this).css({background:"url(../wp-content/plugins/google-map-wp/images/122.gif) #2ea2cc 55px center no-repeat"});
				jQuery.post("<?php echo admin_url('admin-ajax.php' ); ?>",marker_data,function(response){
					if(response.success){
						jQuery("#marker_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						loadMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadMarkerMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolygonMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolylineMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadCircleMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						jQuery("#cancel_marker").trigger("click");
						if(jQuery(".empty_marker").html() != undefined){
							jQuery(".empty_marker").after("<ul><li class='edit_list has_background' data-list_id='"+response.last_id+"'><div class='list_number' >1</div><div class='edit_list_item'>"+marker_title+"</div><div class='edit_marker_list_delete edit_list_delete'><form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='+id+'><input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' /><input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='marker' /><input type='hidden' class='edit_list_delete_table' value='g_markers' /><input type='hidden' name='delete_marker_id' class='edit_list_delete_id' value='"+response.last_id+"' /></form><a href='#' class='button' class='edit_marker_list_item' ></a><input type='hidden' class='marker_edit_id' name='marker_edit_id' value='"+response.last_id+"' /></div></li></ul>");
							jQuery(".empty_marker").remove();
						}else{
							var last_id = jQuery("#markers_edit_exist_section .edit_list").last().find(".list_number").html();
							var this_id=parseInt(last_id)+1;
							jQuery("#markers_edit_exist_section .edit_list").last().after("<li class='edit_list has_background' data-list_id='"+response.last_id+"'><div class='list_number' >"+this_id+"</div><div class='edit_list_item'>"+marker_title+"</div><div class='edit_marker_list_delete edit_list_delete'><form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='+id+'><input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' /><input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='marker' /><input type='hidden' class='edit_list_delete_table' value='g_markers' /><input type='hidden' name='delete_marker_id' class='edit_list_delete_id' value='"+response.last_id+"' /></form><a href='#' class='button' class='edit_marker_list_item' ></a><input type='hidden' class='marker_edit_id' name='marker_edit_id' value='"+response.last_id+"' /></div></li>");
						}
						
						
						jQuery(document).scrollTop(0)
						jQuery("html").parent().on("click",function(){
							jQuery("#marker_submit").css({background:"#2ea2cc"});
						})
					}else{
						jQuery("#marker_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						alert("no");
						jQuery("html").parent().on("click",function(){
							jQuery("#marker_submit").css({background:"#2ea2cc"});
						})
					}
				},"json");
				return false;
			})
			jQuery("#marker_edit_submmit").on("click",function(){
				var marker_edit_location=jQuery("#marker_edit_location").val();
				var marker_edit_location_lat=jQuery("#marker_edit_location_lat").val();
				var marker_edit_location_lng=jQuery("#marker_edit_location_lng").val();
				var marker_edit_animation=jQuery("#marker_edit_animation").val();
				var marker_edit_title=jQuery("#marker_edit_title").val();
				var marker_edit_description=jQuery("#marker_edit_description").val();
				var marker_edit_image_size=jQuery("#marker_edit_image_size").val();
				if(jQuery(this).parent().parent().find(".marker_image_choose ul li.active").html() != undefined){
					var marker_edit_image=jQuery(this).parent().parent().find(".marker_image_choose ul li.active input[type=radio]").val();
					marker_edit_image = "../wp-content/plugins/google-map-wp/images/icons/"+marker_edit_image+""+marker_edit_image_size+".png";
					
				}else{
					var marker_edit_image=jQuery("#marker_edit_pic").val();
				}
				var id=jQuery("#marker_get_id").val();
				var map_id=jQuery("#map_id").val();
				var marker_edit_data = {
					action:"g_map_options",
					task:"submit_marker_edit",
					id:id,
					map_id:map_id,
					marker_edit_location:marker_edit_location,
					marker_edit_location_lat:marker_edit_location_lat,
					marker_edit_location_lng:marker_edit_location_lng,
					marker_edit_animation:marker_edit_animation,
					marker_edit_title:marker_edit_title,
					marker_edit_description:marker_edit_description,
					marker_edit_image:marker_edit_image,
					marker_edit_image_size:marker_edit_image_size,
				}
				//jQuery(this).css({background:"url(../wp-content/plugins/google-map-wp/images/122.gif) #2ea2cc 55px center no-repeat"});
				jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>",marker_edit_data,function(response){
					if(response.success){
						jQuery("#marker_edit_submmit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						loadMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadMarkerMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolygonMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolylineMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadCircleMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						jQuery("#cancel_edit_marker").trigger("click");
						
						jQuery("#markers_edit_exist_section li").each(function(){
							if(jQuery(this).attr("data-list_id") == id){
								jQuery(this).find(".edit_list_item").html(marker_edit_title)
							}
						})
						jQuery(document).scrollTop(0);
						jQuery("html").parent().on("click",function(){
							jQuery("#marker_edit_submmit").css({background:"#2ea2cc"});
						})
					}else{
						jQuery("#marker_edit_submmit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						jQuery("html").parent().on("click",function(){
							jQuery("#marker_edit_submmit").css({background:"#2ea2cc"});
						})
					}
				},"json");
				return false;
			})
			jQuery("#polygone_submit").on("click",function(){
				var polygone_name=jQuery("#polygone_name").val();
				var polygone_url=jQuery("#polygone_url").val();
				var polygone_coords=jQuery("#polygone_coords").val();
				var polygone_line_opacity=jQuery("#polygone_line_opacity").val();
				var polygone_line_color=jQuery("#polygone_line_color").val();
				var polygone_line_width=jQuery("#polygone_line_width").val();
				var polygone_fill_opacity=jQuery("#polygone_fill_opacity").val();
				var polygone_fill_color=jQuery("#polygone_fill_color").val();
				var hover_polygone_fill_opacity=jQuery("#hover_polygone_fill_opacity").val();
				var hover_polygone_fill_color=jQuery("#hover_polygone_fill_color").val();
				var hover_polygone_line_opacity=jQuery("#hover_polygone_line_opacity").val();
				var hover_polygone_line_color=jQuery("#hover_polygone_line_color").val();
				var id=jQuery("#map_id").val();
				var polygon_data = {
					action:"g_map_options",
					task:"submit_polygon",
					id:id,
					polygone_name:polygone_name,
					polygone_url:polygone_url,
					polygone_coords:polygone_coords,
					polygone_line_opacity:polygone_line_opacity,
					polygone_line_color:polygone_line_color,
					polygone_line_width:polygone_line_width,
					polygone_fill_opacity:polygone_fill_opacity,
					polygone_fill_color:polygone_fill_color,
					hover_polygone_fill_opacity:hover_polygone_fill_opacity,
					hover_polygone_fill_color:hover_polygone_fill_color,
					hover_polygone_line_opacity:hover_polygone_line_opacity,
					hover_polygone_line_color:hover_polygone_line_color
				}
				function dump(attachment){
				   var out = '';
				   for (var i in attachment){
					   out += i + ": " + attachment[i] + "\n";
				   }
				   alert(out);
				}
				//dump(polygon_data);
				//jQuery(this).css({background:"url(../wp-content/plugins/google-map-wp/images/122.gif) #2ea2cc 55px center no-repeat"});
				jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>",polygon_data,function(response){
					if(response.success){
						jQuery("#polygone_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						loadMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadMarkerMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolygonMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolylineMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadCircleMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						jQuery("#cancel_polygone").trigger("click");
						if(jQuery(".empty_polygon").html() != undefined){
							
							jQuery(".empty_polygon").after("<ul><li class='edit_list has_background' data-list_id='"+response.last_id+"'><div class='list_number' >1</div><div class='edit_list_item'>"+polygone_name+"</div><div class='edit_polygone_list_delete edit_list_delete'><form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='+id+'><input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' /><input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='polygone' /><input type='hidden' class='edit_list_delete_table' value='g_polygones' /><input type='hidden' name='delete_polygone_id' class='edit_list_delete_id' value='"+response.last_id+"' /></form><a href='#' class='button' class='edit_polygone_list_item' ></a><input type='hidden' class='polygone_edit_id' name='polygone_edit_id' value='"+response.last_id+"' /></div></li></ul>");
							jQuery(".empty_polygon").remove();
						}else{
							var last_id = jQuery("#polygone_edit_exist_section .edit_list").last().find(".list_number").html();
							var this_id=parseInt(last_id)+1;
							jQuery("#polygone_edit_exist_section .edit_list").last().after("<li class='edit_list has_background' data-list_id='"+response.last_id+"'><div class='list_number' >"+this_id+"</div><div class='edit_list_item'>"+polygone_name+"</div><div class='edit_polygone_list_delete edit_list_delete'><form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='+id+'><input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' /><input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='polygone' /><input type='hidden' class='edit_list_delete_table' value='g_polygones' /><input type='hidden' name='delete_polygone_id' class='edit_list_delete_id' value='"+response.last_id+"' /></form><a href='#' class='button' class='edit_polygone_list_item' ></a><input type='hidden' class='polygone_edit_id' name='polygone_edit_id' value='"+response.last_id+"' /></div></li>");
						}
						jQuery(document).scrollTop(0)
						jQuery("html").parent().on("click",function(){
							jQuery("#polygone_submit").css({background:"#2ea2cc"});
						})
					}else{
						jQuery("#polygone_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						jQuery("html").parent().on("click",function(){
							jQuery("#polygone_submit").css({background:"#2ea2cc"});
						})
					}
				},"json")
				return false;
			})
			jQuery("#polygone_edit_submit").on("click",function(){
				var polygone_edit_name=jQuery("#polygone_edit_name").val();
				var polygone_edit_url=jQuery("#polygone_edit_url").val();
				var polygone_edit_coords=jQuery("#polygone_edit_coords").val();
				var polygone_edit_line_opacity=jQuery("#polygone_edit_line_opacity").val();
				var polygone_edit_line_color=jQuery("#polygone_edit_line_color").val();
				var polygone_edit_line_width=jQuery("#polygone_edit_line_width").val();
				var polygone_edit_fill_opacity=jQuery("#polygone_edit_fill_opacity").val();
				var polygone_edit_fill_color=jQuery("#polygone_edit_fill_color").val();
				var hover_polygone_edit_fill_opacity=jQuery("#hover_polygone_edit_fill_opacity").val();
				var hover_polygone_edit_fill_color=jQuery("#hover_polygone_edit_fill_color").val();
				var hover_polygone_edit_line_opacity=jQuery("#hover_polygone_edit_line_opacity").val();
				var hover_polygone_edit_line_color=jQuery("#hover_polygone_edit_line_color").val();
				var map_id=jQuery("#map_id").val();
				var id=jQuery("#polygone_get_id").val();
				var polygon_edit_data = {
					action:'g_map_options',
					task:"submit_polygon_edit",
					map_id:map_id,
					id:id,
					polygone_edit_name:polygone_edit_name,
					polygone_edit_url:polygone_edit_url,
					polygone_edit_coords:polygone_edit_coords,
					polygone_edit_line_opacity:polygone_edit_line_opacity,
					polygone_edit_line_color:polygone_edit_line_color,
					polygone_edit_line_width:polygone_edit_line_width,
					polygone_edit_fill_opacity:polygone_edit_fill_opacity,
					polygone_edit_fill_color:polygone_edit_fill_color,
					hover_polygone_edit_fill_opacity:hover_polygone_edit_fill_opacity,
					hover_polygone_edit_fill_color:hover_polygone_edit_fill_color,
					hover_polygone_edit_line_opacity:hover_polygone_edit_line_opacity,
					hover_polygone_edit_line_color:hover_polygone_edit_line_color,
				}
				function dump(attachment){
				   var out = '';
				   for (var i in attachment){
					   out += i + ": " + attachment[i] + "\n";
				   }
				   alert(out);
				}
				//dump(polygon_edit_data);
				//jQuery(this).css({background:"url(../wp-content/plugins/google-map-wp/images/122.gif) #2ea2cc 55px center no-repeat"});
				jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>",polygon_edit_data,function(response){
					if(response.success){
						jQuery("#polygone_edit_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						loadMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadMarkerMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolygonMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolylineMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadCircleMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						jQuery("#cancel_edit_polygone").trigger("click");
						jQuery(document).scrollTop(0);
						jQuery("#polygone_edit_exist_section li").each(function(){
							if(jQuery(this).attr("data-list_id") == id){
								jQuery(this).find(".edit_list_item").html(polygone_edit_name)
							}
						})
						jQuery("html").parent().on("click",function(){
							jQuery("#polygone_edit_submit").css({background:"#2ea2cc"});
						})
					}else{
						jQuery("#polygone_edit_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						jQuery("html").parent().on("click",function(){
							jQuery("#polygone_edit_submit").css({background:"#2ea2cc"});
						})
					}
				},"json")
				return false;
			})
			jQuery("#polyline_submit").on("click",function(){
				var polyline_name=jQuery("#polyline_name").val();
				var polyline_coords=jQuery("#polyline_coords").val();
				var polyline_line_opacity=jQuery("#polyline_line_opacity").val();
				var polyline_line_color=jQuery("#polyline_line_color").val();
				var polyline_line_width=jQuery("#polyline_line_width").val();
				var hover_polyline_line_color=jQuery("#hover_polyline_line_color").val();
				var hover_polyline_line_opacity=jQuery("#hover_polyline_line_opacity").val();
				var id=jQuery("#map_id").val();
				var polyline_data = {
					action:"g_map_options",
					task:"submit_polyline",
					id:id,
					polyline_name:polyline_name,
					polyline_coords:polyline_coords,
					polyline_line_opacity:polyline_line_opacity,
					polyline_line_color:polyline_line_color,
					polyline_line_width:polyline_line_width,
					hover_polyline_line_color:hover_polyline_line_color,
					hover_polyline_line_opacity:hover_polyline_line_opacity
				}
				function dump(attachment){
				   var out = '';
				   for (var i in attachment){
					   out += i + ": " + attachment[i] + "\n";
				   }
				   alert(out);
				}
				//dump(polyline_data);
				//jQuery(this).css({background:"url(../wp-content/plugins/google-map-wp/images/122.gif) #2ea2cc 55px center no-repeat"});
				jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>",polyline_data,function(response){
					if(response.success){
						jQuery("#polyline_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						loadMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadMarkerMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolygonMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolylineMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadCircleMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						jQuery("#cancel_polyline").trigger("click");
						jQuery(document).scrollTop(0);
						if(jQuery(".empty_polyline").html() != undefined){
							
							jQuery(".empty_polyline").after("<ul><li class='edit_list has_background' data-list_id='"+response.last_id+"'><div class='list_number' >1</div><div class='edit_list_item'>"+polyline_name+"</div><div class='edit_polyline_list_delete edit_list_delete'><form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='"+id+"'><input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' /><input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='polyline' /><input type='hidden' class='edit_list_delete_table' value='g_polylines' /><input type='hidden' name='delete_polyline_id' class='edit_list_delete_id' value='"+response.last_id+"' /></form><a href='#' class='button' class='edit_polyline_list_item' ></a><input type='hidden' class='polyline_edit_id' name='polyline_edit_id' value='"+response.last_id+"' /></div></li></ul>");
							jQuery(".empty_polyline").remove();
						}else{
							var last_id = jQuery("#polyline_edit_exist_section .edit_list").last().find(".list_number").html();
							var this_id=parseInt(last_id)+1;
							jQuery("#polyline_edit_exist_section .edit_list").last().after("<li class='edit_list has_background' data-list_id='"+response.last_id+"'><div class='list_number' >"+this_id+"</div><div class='edit_list_item'>"+polyline_name+"</div><div class='edit_polyline_list_delete edit_list_delete'><form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='"+id+"'><input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' /><input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='polyline' /><input type='hidden' class='edit_list_delete_table' value='g_polylines' /><input type='hidden' name='delete_polyline_id' class='edit_list_delete_id' value='"+response.last_id+"' /></form><a href='#' class='button' class='edit_polyline_list_item' ></a><input type='hidden' class='polyline_edit_id' name='polyline_edit_id' value='"+response.last_id+"' /></div></li>");
						}
						jQuery("html").parent().on("click",function(){
							jQuery("#polyline_submit").css({background:"#2ea2cc"});
						})
					}else{
						jQuery("#polyline_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						jQuery("html").parent().on("click",function(){
							jQuery("#polyline_submit").css({background:"#2ea2cc"});
						})
					}
				},"json")
				return false;
			})
			jQuery("#polyline_edit_submit").on("click",function(){
				var polyline_edit_name=jQuery("#polyline_edit_name").val();
				var polyline_edit_coords=jQuery("#polyline_edit_coords").val();
				var polyline_edit_line_opacity=jQuery("#polyline_edit_line_opacity").val();
				var polyline_edit_line_color=jQuery("#polyline_edit_line_color").val();
				var polyline_edit_line_width=jQuery("#polyline_edit_line_width").val();
				var hover_polyline_edit_line_color=jQuery("#hover_polyline_edit_line_color").val();
				var hover_polyline_edit_line_opacity=jQuery("#hover_polyline_edit_line_opacity").val();
				var map_id=jQuery("#map_id").val();
				var id=jQuery("#polyline_get_id").val();
				var polyline_edit_data = {
					action:"g_map_options",
					task:"polyline_edit_submit",
					id:id,
					map_id:map_id,
					polyline_edit_name:polyline_edit_name,
					polyline_edit_coords:polyline_edit_coords,
					polyline_edit_line_opacity:polyline_edit_line_opacity,
					polyline_edit_line_color:polyline_edit_line_color,
					polyline_edit_line_width:polyline_edit_line_width,
					hover_polyline_edit_line_color:hover_polyline_edit_line_color,
					hover_polyline_edit_line_opacity:hover_polyline_edit_line_opacity
				}
				//jQuery(this).css({background:"url(../wp-content/plugins/google-map-wp/images/122.gif) #2ea2cc 55px center no-repeat"});
				jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>",polyline_edit_data,function(response){
					if(response.success){
						jQuery("#polyline_edit_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						loadMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadMarkerMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolygonMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolylineMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadCircleMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						jQuery("#cancel_edit_polyline").trigger("click");
						jQuery(document).scrollTop(0);
						jQuery("#polyline_edit_exist_section li").each(function(){
							if(jQuery(this).attr("data-list_id") == id){
								jQuery(this).find(".edit_list_item").html(polyline_edit_name)
							}
						})
						jQuery("html").parent().on("click",function(){
							jQuery("#polyline_edit_submit").css({background:"#2ea2cc"});
						})
					}else{
						jQuery("#polyline_edit_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						jQuery("html").parent().on("click",function(){
							jQuery("#polyline_edit_submit").css({background:"#2ea2cc"});
						})
					}
				},"json")
				return false;
			})
			jQuery("#circle_submit").on("click",function(){
				var circle_name=jQuery("#circle_name").val();
				var circle_center_lat=jQuery("#circle_center_lat").val();
				var circle_center_lng=jQuery("#circle_center_lng").val();
				var circle_marker_show=jQuery(".circle_marker_show:checked").val();
				var circle_radius=jQuery("#circle_radius").val();
				var circle_line_width=jQuery("#circle_line_width").val();
				var circle_line_color=jQuery("#circle_line_color").val();
				var circle_line_opacity=jQuery("#circle_line_opacity").val();
				var circle_fill_color=jQuery("#circle_fill_color").val();
				var circle_fill_opacity=jQuery("#circle_fill_opacity").val();
				var hover_circle_fill_color=jQuery("#hover_circle_fill_color").val();
				var hover_circle_fill_opacity=jQuery("#hover_circle_fill_opacity").val();
				var hover_circle_line_color=jQuery("#hover_circle_line_color").val();
				var hover_circle_line_opacity=jQuery("#hover_circle_line_opacity").val();
				var id=jQuery("#map_id").val();
				var circle_data= {
					action:"g_map_options",
					task:"submit_circle",
					id:id,
					circle_name:circle_name,
					circle_center_lat:circle_center_lat,
					circle_center_lng:circle_center_lng,
					circle_marker_show:circle_marker_show,
					circle_radius:circle_radius,
					circle_line_width:circle_line_width,
					circle_line_color:circle_line_color,
					circle_line_opacity:circle_line_opacity,
					circle_fill_color:circle_fill_color,
					circle_fill_opacity:circle_fill_opacity,
					hover_circle_fill_color:hover_circle_fill_color,
					hover_circle_fill_opacity:hover_circle_fill_opacity,
					hover_circle_line_color:hover_circle_line_color,
					hover_circle_line_opacity:hover_circle_line_opacity
				}
				//jQuery(this).css({background:"url(../wp-content/plugins/google-map-wp/images/122.gif) #2ea2cc 55px center no-repeat"});
				jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>",circle_data,function(response){
					if(response.success){
						jQuery("#circle_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						loadMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadMarkerMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolygonMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolylineMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadCircleMap(id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						jQuery("#cancel_circle").trigger("click");
						jQuery(document).scrollTop(0);
						if(jQuery(".empty_circle").html() != undefined){
							
							jQuery(".empty_circle").after("<ul><li class='edit_list has_background' data-list_id='"+response.last_id+"'><div class='list_number' >1</div><div class='edit_list_item'>"+circle_name+"</div><div class='edit_circle_list_delete edit_list_delete'><form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='"+id+"'><input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' /><input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='circle' /><input type='hidden' class='edit_list_delete_table' value='g_circles' /><input type='hidden' name='delete_circle_id' class='edit_list_delete_id' value='"+response.last_id+"' /></form><a href='#' class='button' class='edit_circle_list_item' ></a><input type='hidden' class='circle_edit_id' name='circle_edit_id' value='"+response.last_id+"' /></div></li></ul>");
							jQuery(".empty_circle").remove();
						}else{
							var last_id = jQuery("#circle_edit_exist_section .edit_list").last().find(".list_number").html();
							var this_id=parseInt(last_id)+1;
							jQuery("#circle_edit_exist_section .edit_list").last().after("<li class='edit_list has_background' data-list_id='"+response.last_id+"' ><div class='list_number' >"+this_id+"</div><div class='edit_list_item'>"+circle_name+"</div><div class='edit_circle_list_delete edit_list_delete'><form class='edit_list_delete_form' method='post' action='admin.php?page=hugeitgooglemaps_main&task=edit_cat&id='"+id+"'><input type='submit' class='button edit_list_delete_submit' name='edit_list_delete_submit' value='x' /><input type='hidden' class='edit_list_delete_type' name='edit_list_delete_type' value='circle' /><input type='hidden' class='edit_list_delete_table' value='g_circles' /><input type='hidden' name='delete_circle_id' class='edit_list_delete_id' value='"+response.last_id+"' /></form><a href='#' class='button' class='edit_circle_list_item' ></a><input type='hidden' class='circle_edit_id' name='circle_edit_id' value='"+response.last_id+"' /></div></li>");
						}
						jQuery("html").parent().on("click",function(){
							jQuery("#circle_submit").css({background:"#2ea2cc"});
						})
					}else{
						jQuery("#circle_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						jQuery("html").parent().on("click",function(){
							jQuery("#circle_submit").css({background:"#2ea2cc"});
						})
					}
				},"json")
				return false;
			})
			jQuery("#circle_edit_submit").on("click",function(){
				var circle_edit_name=jQuery("#circle_edit_name").val();
				var circle_edit_center_lat=jQuery("#circle_edit_center_lat").val();
				var circle_edit_center_lng=jQuery("#circle_edit_center_lng").val();
				var circle_edit_marker_show=jQuery(".circle_edit_marker_show:checked").val();
				var circle_edit_radius=jQuery("#circle_edit_radius").val();
				var circle_edit_line_width=jQuery("#circle_edit_line_width").val();
				var circle_edit_line_color=jQuery("#circle_edit_line_color").val();
				var circle_edit_line_opacity=jQuery("#circle_edit_line_opacity").val();
				var circle_edit_fill_color=jQuery("#circle_edit_fill_color").val();
				var circle_edit_fill_opacity=jQuery("#circle_edit_fill_opacity").val();
				var hover_circle_edit_fill_color=jQuery("#hover_circle_edit_fill_color").val();
				var hover_circle_edit_fill_opacity=jQuery("#hover_circle_edit_fill_opacity").val();
				var hover_circle_edit_line_color=jQuery("#hover_circle_edit_line_color").val();
				var hover_circle_edit_line_opacity=jQuery("#hover_circle_edit_line_opacity").val();
				var map_id=jQuery("#map_id").val();
				var id=jQuery("#circle_get_id").val();
				var circle_edit_data = {
					action:"g_map_options",
					task:"submit_circle_edit",
					map_id:map_id,
					id:id,
					circle_edit_name:circle_edit_name,
					circle_edit_center_lat:circle_edit_center_lat,
					circle_edit_center_lng:circle_edit_center_lng,
					circle_edit_marker_show:circle_edit_marker_show,
					circle_edit_radius:circle_edit_radius,
					circle_edit_line_width:circle_edit_line_width,
					circle_edit_line_color:circle_edit_line_color,
					circle_edit_line_opacity:circle_edit_line_opacity,
					circle_edit_fill_color:circle_edit_fill_color,
					circle_edit_fill_opacity:circle_edit_fill_opacity,
					hover_circle_edit_fill_color:hover_circle_edit_fill_color,
					hover_circle_edit_fill_opacity:hover_circle_edit_fill_opacity,
					hover_circle_edit_line_color:hover_circle_edit_line_color,
					hover_circle_edit_line_opacity:hover_circle_edit_line_opacity,
				}
				//jQuery(this).css({background:"url(../wp-content/plugins/google-map-wp/images/122.gif) #2ea2cc 55px center no-repeat"});
				jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>",circle_edit_data,function(response){
					if(response.success){
						jQuery("#circle_edit_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						loadMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadMarkerMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolygonMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadPolylineMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						loadCircleMap(map_id,"#"+response.hue,response.saturation,response.lightness,response.gamma,response.zoom,response.type,response.bike,response.traffic,response.transit);
						jQuery("#cancel_edit_circle").trigger("click");
						jQuery(document).scrollTop(0);
						jQuery("#circle_edit_exist_section li").each(function(){
							if(jQuery(this).attr("data-list_id") == id){
								jQuery(this).find(".edit_list_item").html(circle_edit_name)
							}
						})
						jQuery("html").parent().on("click",function(){
							jQuery("#circle_edit_submit").css({background:"#2ea2cc"});
						})
					}else{
						jQuery("#circle_edit_submit").css({background:"url(../wp-content/plugins/google-map-wp/images/tick1.png) #2ea2cc 55px center no-repeat"});
						jQuery("html").parent().on("click",function(){
							jQuery("#circle_edit_submit").css({background:"#2ea2cc"});
						})
					}
				},"json")
				return false;
			})

		})
	</script>
	<?php
}
?>