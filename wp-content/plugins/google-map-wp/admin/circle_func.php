<?php
function circle_js($id)
{
	global $wpdb;
	$sql = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix."g_maps WHERE id=%d",$id);
	$map = $wpdb->get_results($sql);
	foreach($map as $map)
	{
		?>
			<script>
				var data;
				var circlemarker;
				var newcircle;
				var editcircle;
				var circlemarkeredit;
				jQuery(document).ready(function(){
					loadCircleMap("<?php echo $map->id; ?>","#<?php echo $map->styling_hue; ?>","<?php echo $map->styling_saturation; ?>","<?php echo $map->styling_lightness; ?>","<?php echo $map->styling_gamma; ?>","<?php echo $map->zoom; ?>","<?php echo $map->type; ?>","<?php echo $map->bike_layer; ?>","<?php echo $map->traffic_layer; ?>","<?php echo $map->transit_layer; ?>");
				})
				function loadCircleMap(id,hue,saturation,lightness,gamma,zoom,type,bike,traffic,transit){
					data = {
						action:'g_map_options',
						map_id:id,
						task:"getxml",
					}
					
					jQuery.post("<?php echo admin_url( 'admin-ajax.php' ); ?>",data,function(response){
						if(response.success)
						{
							var xml = jQuery.parseXML(response.success);
							var maps = xml.documentElement.getElementsByTagName("map");
							for(var i = 0; i < maps.length; i++)
							{
								var mapcenter = new google.maps.LatLng(
									parseFloat(maps[i].getAttribute("center_lat")),
									parseFloat(maps[i].getAttribute("center_lng")));

								var mapOptions = {
									zoom: parseInt(zoom),
									center: mapcenter,
								}
								mapcircle = new google.maps.Map(document.getElementById('g_map_circle'), mapOptions);
								map_circle_edit = new google.maps.Map(document.getElementById('g_map_circle_edit'),mapOptions);
								
								var input_circle = document.getElementById("circle_center_addr");
								var autocomplete_circle = new google.maps.places.Autocomplete(input_circle);
								google.maps.event.addListener(autocomplete_circle, 'place_changed', function(){
									
									var addr = jQuery("#circle_center_addr").val();
									geocoder = new google.maps.Geocoder();
									geocoder.geocode({ 'address': addr}, function (results, status) {
										if(newcircle)
										{
											newcircle.setCenter(results[0].geometry.location);
											circlemarker.setPosition(results[0].geometry.location);
										}
										else
										{
											placeCircle(results[0].geometry.location)
										}
										mapcircle.setCenter(results[0].geometry.location);
										updateCircleInputs(results[0].geometry.location);
									})
								})
								
								jQuery("#circle_add_button").on("click",function(){
									google.maps.event.trigger(mapcircle, 'resize');
									mapcircle.setCenter(mapcenter);
									if(newcircle){
										newcircle.setMap(null);
										circlemarker.setMap(null);
										circlemarker ="";
										newcircle = "";
									}
								})
								
								google.maps.event.addListener(mapcircle, 'rightclick', function(event){
									placeCircle(event.latLng);
									updateCircleInputs(event.latLng);
								});
								
								
								
								jQuery(".edit_circle_list_delete a").on("click",function(){
									if(editcircle){
										editcircle.setMap(null);
										circlemarkeredit.setMap(null);
									}
									var parent = jQuery(this).parent();
									var idelement = parent.find(".circle_edit_id");
									var circleid = idelement.val();
									jQuery("#g_maps > div").not("#g_map_polygon").addClass("hide");
									jQuery("#g_map_circle_edit").removeClass("hide");
									jQuery("#circle_edit_exist_section").hide(200).addClass("tab_options_hidden_section");
									jQuery(this).parent().parent().parent().parent().parent().find(".update_list_item").show(200).addClass("tab_options_active_section");
									jQuery("#circle_add_button").hide(200).addClass("tab_options_hidden_section");
									google.maps.event.trigger(map_circle_edit, 'resize');
									map_circle_edit.setCenter(mapcenter);
									jQuery("#circle_get_id").val(circleid);
									var circles = xml.documentElement.getElementsByTagName("circle");
									for(var j = 0; j < circles.length; j++)
									{
										var id = circles[j].getAttribute("id");
										if(circleid == id)
										{
											var name = circles[j].getAttribute("name");
											var center_lat = circles[j].getAttribute("center_lat");
											var center_lng = circles[j].getAttribute("center_lng");
											var radius = circles[j].getAttribute("radius");
											var line_width = circles[j].getAttribute("line_width");
											var line_color = circles[j].getAttribute("line_color");
											var line_opacity = circles[j].getAttribute("line_opacity");
											var fill_color = circles[j].getAttribute("fill_color");
											var fill_opacity = circles[j].getAttribute("fill_opacity");
											var show_marker = circles[j].getAttribute("show_marker");
											jQuery("#circle_edit_name").val(name);
											jQuery("#circle_edit_center_lat").val(center_lat);
											jQuery("#circle_edit_center_lng").val(center_lng);
											
											
											//jQuery("#circle_edit_center_addr").val(name);
											if(show_marker == '1'){
												jQuery("#circle_edit_marker_show_true").attr("checked","checked");
											}
											else{
												jQuery("#circle_edit_marker_show_false").attr("checked","checked");
											}
											jQuery("#circle_edit_radius").val(radius);
											jQuery("#circle_edit_line_width").simpleSlider("setValue", line_width);
											jQuery("#circle_edit_line_color").val(line_color);
											jQuery("#circle_edit_line_opacity").simpleSlider("setValue", line_opacity);
											jQuery("#circle_edit_fill_color").val(fill_color);
											jQuery("#circle_edit_fill_opacity").simpleSlider("setValue", fill_opacity);
											
											editcircleposition = new google.maps.LatLng(parseFloat(circles[j].getAttribute("center_lat")),
												parseFloat(circles[j].getAttribute("center_lng")));
											var geocoder= new google.maps.Geocoder();
											geocoder.geocode({'latLng':editcircleposition},function(results, status){
												if (status == google.maps.GeocoderStatus.OK) {
													address = results[0].formatted_address;
													jQuery("#circle_edit_center_addr").val(address);
												}
											})
											map_circle_edit.setCenter(editcircleposition);
											
											circlemarkeredit = new google.maps.Marker({
												position:editcircleposition,
												map:map_circle_edit,
												title:name,
												draggable:true,
											})
											
											google.maps.event.addListener(circlemarkeredit, "drag", function(event){
												if(editcircle)
												{
													editcircle.setCenter(event.latLng);
													updateCircleEditInputs(event.latLng);
												}
											})
											
											editcircle = new google.maps.Circle({
												map:map_circle_edit,
												center:editcircleposition,
												title:name,
												radius:parseInt(radius),
												strokeColor:"#"+line_color,
												strokeOpacity:line_opacity,
												strokeWeight:line_width,
												fillColor:"#"+fill_color,
												fillOpacity:fill_opacity
											})
											
											jQuery(".circle_edit_options_input").on("keyup change",function(){
												var radius = jQuery("#circle_edit_radius").val();
												var line_width = jQuery("#circle_edit_line_width").val();
												var line_color = jQuery("#circle_edit_line_color").val();
												var line_opacity = jQuery("#circle_edit_line_opacity").val();
												var fill_color = jQuery("#circle_edit_fill_color").val();
												var fill_opacity = jQuery("#circle_edit_fill_opacity").val();
												editcircle.setOptions({
													radius:parseInt(radius),
													strokeColor:"#"+line_color,
													strokeOpacity:line_opacity,
													strokeWeight:line_width,
													fillColor:"#"+fill_color,
													fillOpacity:fill_opacity
												})
											})
											var input_edit_circle = document.getElementById("circle_edit_center_addr");
											var autocomplete_edit_circle = new google.maps.places.Autocomplete(input_edit_circle);
											google.maps.event.addListener(autocomplete_edit_circle, 'place_changed', function(){
												
												var addr = jQuery("#circle_edit_center_addr").val();
												geocoder = new google.maps.Geocoder();
												geocoder.geocode({ 'address': addr}, function (results, status) {
													if(editcircle)
													{
														editcircle.setCenter(results[0].geometry.location);
														circlemarkeredit.setPosition(results[0].geometry.location);
													}
													map_circle_edit.setCenter(results[0].geometry.location);
													updateCircleEditInputs(results[0].geometry.location);
												})
											})
											
											updateCircleEditInputs(circlemarkeredit.getPosition());
											google.maps.event.addListener(map_circle_edit, "rightclick", function(event){
												if(circlemarkeredit)
												{
													//alert(event.latLng)
													circlemarkeredit.setPosition(event.latLng);
													editcircle.setCenter(event.latLng);
													updateCircleEditInputs(event.latLng);
												}
												
											})

											
										}
									}
									return false;
								})
							}
						}
					},"json")
				}
				function placeCircle(location)
				{
					if(circlemarker)
					{
						circlemarker.setPosition(location)
					}
					else
					{
						circlemarker = new google.maps.Marker({
							position:location,
							map:mapcircle,
							draggable:true,
						})
					}
					google.maps.event.addListener(circlemarker, "drag" , function(event){
						if(newcircle)
						{
							newcircle.setCenter(event.latLng)
						}
						else
						{
							plaaceCircle(event.LatLng)
						}
						updateCircleInputs(circlemarker.getPosition());
					})
					var circle_radius = jQuery("#circle_radius").val();
					var circle_line_width = jQuery("#circle_line_width").val();
					var circle_line_color = jQuery("#circle_line_color").val();
					var circle_line_opacity = jQuery("#circle_line_opacity").val();
					var circle_fill_color = jQuery("#circle_fill_color").val();
					var circle_fill_opacity = jQuery("#circle_fill_opacity").val();
					if(newcircle)
					{
						newcircle.setCenter(location)
					}
					else
					{
						newcircle = new google.maps.Circle({
							map:mapcircle,
							center:location,
							title:"circle",
							radius:parseInt(circle_radius),
							strokeColor:"#"+circle_line_color,
							strokeOpacity:circle_line_opacity,
							strokeWeight:circle_line_width,
							fillColor:"#"+circle_fill_color,
							fillOpacity:circle_fill_opacity
						})
					}
					jQuery(".circle_options_input").on("keyup change",function(){
						var circle_radius = jQuery("#circle_radius").val();
						var circle_line_width = jQuery("#circle_line_width").val();
						var circle_line_color = jQuery("#circle_line_color").val();
						var circle_line_opacity = jQuery("#circle_line_opacity").val();
						var circle_fill_color = jQuery("#circle_fill_color").val();
						var circle_fill_opacity = jQuery("#circle_fill_opacity").val();
						newcircle.setOptions({ 
							radius:parseInt(circle_radius),
							strokeColor:"#"+circle_line_color,
							strokeWeight:circle_line_width,
							strokeOpacity:circle_line_opacity,
							fillOpacity:circle_fill_opacity,
							fillColor:"#"+circle_fill_color,
						}); 
					})
				}
				function updateCircleInputs(location)
				{
					jQuery("#circle_center_lat").val(location.lat());
					jQuery("#circle_center_lng").val(location.lng());
					geocoder = new google.maps.Geocoder();
					geocoder.geocode({'latLng':location},function(results, status){
						if (status == google.maps.GeocoderStatus.OK) {
							address = results[0].formatted_address;
							jQuery("#circle_center_addr").val(address);
						}
					})
				}
				
				function updateCircleEditInputs(location)
				{
					jQuery("#circle_edit_center_lat").val(location.lat());
					jQuery("#circle_edit_center_lng").val(location.lng());
					geocoder = new google.maps.Geocoder();
					geocoder.geocode({'latLng':location},function(results, status){
						if (status == google.maps.GeocoderStatus.OK) {
							address = results[0].formatted_address;
							jQuery("#circle_edit_center_addr").val(address);
						}
					})
					
				}
			</script>
		<?php
	}
}
?>