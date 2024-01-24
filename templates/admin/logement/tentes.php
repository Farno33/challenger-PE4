<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/admin/logement/recensement.php ****************/
/* Template des bâtiments pour le recensement **************/
/* *********************************************************/
/* Dernière modification : le 19/01/15 *********************/
/* *********************************************************/

$cql = 'T|tentes
TL|ecoles
G|zones';


$zone = empty($_GET['zone']) ? null : (int) $_GET['zone'];
$ecole = empty($_GET['ecole']) ? null : (int) $_GET['ecole'];

//Inclusion de l'entête de page
require DIR.'templates/admin/_header_admin.php';

?>		
	<style>
	.menus nav { margin-bottom:0px;}
	.container { height:100%;}
	.main { height:calc(100% - 175px); padding:0px;}

	input[type=radio] {
		display: none;
	}

	input[type=radio] + label {
		display:inline-block;
		height:30px;
		width:30px;
		border-top:5px solid #BBB;
		background:#BBB;
	}

	input[type=radio]:checked + label {
		border-top-width: 30px;
		height:0px;
	}

	div.radio {
		vertical-align: top;
	}
	</style>

	<div id="options" style="float:right; height:100%; width:300px; padding:20px 10px; overflow:auto">
				<form method="post" action="<?php url('admin/module/competition/extract'); ?>">
					<h2>
						Liste des <?php echo $zone ? 'tentes' : 'zones'; ?>
						<?php if (!$zone) { ?><input type="submit" value="CQL" /><?php } ?>
					</h2>

					<input type="hidden" name="cql" value='<?php echo $cql; ?>' />
				</form>


		<form method="get">
		<center>
			<fieldset>
				<?php if ($zone) { ?>
				<input type="button" value="Revenir aux zones" onclick="window.location.href='<?php url('admin/module/logement/tentes'); ?>';" />
				
				<?php if (!empty($ecole)) { ?>
				<input type="button" value="Revenir à l'édition" onclick="window.location.href='<?php url('admin/module/logement/tentes'); ?>?zone=<?php echo $zone; ?>';" />
				<?php } ?>

				<br />
				<br />
				<h3>Zone : <b><?php echo stripslashes($zones[$zone]['zone']); ?></b></h3>
				<input type="hidden" name="zone" value="<?php echo $zone; ?>" />		


				
				<?php if (empty($ecole)) { ?>

				<h4>Edition</h4>
				<input type="button" class="success" value="Activer l'ajout" onclick="edit = !edit; this.value = edit ? 'Désactiver l\'ajout' : 'Activer l\'ajout';" />
				<input type="button" value="Enlever dernière tente" onclick="window.location.href='<?php url('admin/module/logement/tentes'); ?>';" />
				<input type="button" class="delete" value="Tout supprimer" onclick="window.location.href='<?php url('admin/module/logement/tentes'); ?>';" />
				
				<?php } } else { 

					if (empty($ecole)) { ?>
				
				<input type="button" class="success" value="Ajouter une zone" onclick="addZone();" />
				
				<table>
					<thead>
						<tr>
							<th style="width:50px">Zone</th>
							<th><small>Attrib./Tentes</small></th>
							<th class="actions">Actions</th>
						</tr>
					</thead>

					<tbody>

						<?php foreach ($zones as $zid => $z) { ?>
						
						<tr onclick="window.location.href='<?php url('admin/module/logement/tentes?zone='.$zid); ?>';" class="form clickme">
							<td style="background-color:<?php echo stripslashes($z['color']); ?>" class="content"><center><?php echo stripslashes($z['zone']); ?></center></td>
							<td class="content"><center><?php echo $z['nb_attrib'].'/'.$z['nb_tentes']; ?></center></td>
							<td class="content">
								<button type="submit" name="listing" value="<?php echo $zid; ?>">
									<img src="<?php url('assets/images/actions/list.png'); ?>" alt="Listing" />
								</button>

								<?php if (empty($z['nb_tentes'])) { ?>

								<button type="submit" name="delete" value="<?php echo $zid; ?>">
									<img src="<?php url('assets/images/actions/delete.png'); ?>" alt="Delete" />
								</button>

								<?php } ?>

							</td>
						</tr>

						<?php } if (!count($zones)) { ?>

						<tr class="vide">
							<td colspan="3">Aucune zone</td>
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php } else { ?>

				<input type="button" value="Revenir aux zones" onclick="window.location.href='<?php url('admin/module/logement/tentes'); ?>';" />

				<?php } ?>

				<?php } ?>


				<h4>Attribution</h4>
				<select name="ecole" style="width:150px" onchange="$(this).parent().parent().parent().submit();">
					<option value="" disabled <?php if (!isset($ecole))
						echo 'selected'; ?>>Choisissez une école</option>

					<?php foreach ($ecoles as $id => $ec) { ?>

					<option value="<?php echo $id; ?>" <?php if (!empty($ecole) && $ecole == $id) 
						echo ' selected'; ?>><?php echo stripslashes($ec['nom']); ?></option>

					<?php } ?>

				</select>
				<input type="submit" class="success" value="<?php echo $zone ? 'Attribuer' : 'Voir'; ?>" style="width:90px; margin: 0px !important; width:100px; padding:0px" />
			

				<?php if ($ecole) { ?>

				<br /><br />
				<table>
					<thead>
						<tr>
							<td colspan="3"><center>Garçons à loger : <b><?php echo $ecoles[$ecole]['nb_need']; ?></b></center></td>
						</tr>
						<tr>
							<th style="width:50px">Zone</th>
							<th><small>Attrib./Tentes</small></th>
							<th><small>Attrib. Ecole</small></th>
						</tr>
					</thead>

					<tbody>

						<?php 

						foreach ($zones as $zid => $z) {
						?>
						
						<tr onclick="window.location.href='<?php url('admin/module/logement/tentes?ecole='.$ecole.'&zone='.$zid); ?>';" class="form clickme">
							<td style="background-color:<?php echo stripslashes($z['color']); ?>" class="content"><center><?php echo stripslashes($z['zone']); ?></center></td>
							<td class="content"><center><?php echo ($zone && $zid == $zone ? '' : $z['nb_attrib'].'/').$z['nb_tentes']; ?></center></td>
							<td class="content"><center><?php echo $zone && $zid == $zone ? '' : $z['nb_attrib_ecole']; ?></center></td>
						</tr>

						<?php } if (!count($zones)) { ?>

						<tr class="vide">
							<td colspan="3">Aucune zone</td>
						</tr>

						<?php } if (!empty($zone)) { ?>

						<tr>
							<td colspan="3"><center><a href="<?php url('admin/module/logement/tentes?ecole='.$ecole); ?>">Plan général</a></center></td>
						</tr>

						<?php } ?>

					</tbody>
				</table>

				<?php } ?>



			</fieldset>
		<center>
	</form>
	</div>

	<div id="map" style="height:100%; width:calc(100% - 300px)"></div>
	<div class="clearfix"></div>

	<div id="modal-ajout-zone" class="modal">
		<form method="get">
			<fieldset>
				<legend>Ajout d'une zone</legend>

				<label for="form-chambre" class="needed">
					<span>Label</span>
					<input id="form-label" type="text" value="" name="label" />
				</label>

				<label for="form-color" class="needed">
					<span>Couleur</span>
					<div class="radio">
						<?php foreach ($colorsZone as $k => $color) { ?>
						<input type="radio" name="color" data-color="<?php echo $color; ?>" id="color-<?php echo $k; ?>" <?php if (!$k) echo ' '; ?>/>
						<label for="color-<?php echo $k; ?>" style="border-color:<?php echo $color; ?>"></label>
						<?php } ?>
					</div>
				</label>

				<center>
					<input type="button" class="success" value="Dessiner la zone" onclick="drawZone()" />
				</center>
			</fieldset>
		</form>
	</div>


    <script type="text/javascript">

var map;
var drawingManager;
var selectedShape;
var bounds;
var zone = <?php echo !empty($_GET['zone']) ? $_GET['zone'] : 'null'; ?>;
var zoneLabel = '<?php echo !empty($_GET['zone']) ? $zones[$_GET['zone']]['zone'] : ''; ?>';
var zoneColor = '<?php echo !empty($_GET['zone']) ? $zones[$_GET['zone']]['color'] : ''; ?>';
var count = <?php echo !empty($_GET['zone']) ? (int) $zones[$_GET['zone']]['max'] : 0; ?>;
var ecole = <?php echo !empty($_GET['ecole']) ? $_GET['ecole'] : 'null'; ?>;
var edit = false;
var label;
var color;


function clearSelection() {
        if (selectedShape) {
        	var path = [];
				
	            for (var i = 0; i < selectedShape.getPath().getLength(); i++) {
	            	path.push({
	            		lat:parseFloat(selectedShape.getPath().getAt(i).lat()), 
	            		lng:parseFloat(selectedShape.getPath().getAt(i).lng())});
	            }

	            $.ajax({
           			url: '<?php url('admin/module/logement/tentes?edit'); ?>',
				  	method: "POST",
				  	cache: false,
					dataType: "json",
					data:{zone: selectedShape.id, path:path}
				});
          selectedShape.setEditable(false);
          selectedShape = null;
        }
      }

      function pinSymbol(color) {
    return {
        path: 'M 0,0 C -2,-20 -10,-22 -10,-30 A 10,10 0 1,1 10,-30 C 10,-22 2,-20 0,0 z',
        fillColor: color,
        labelOrigin: new google.maps.Point(0, -30),
        fillOpacity: 0.8,
        strokeColor: "#000",
        strokeWeight: 1,
        scale: 1,
   };
}


function iconTente(color, numero, active) {
    var strokeColor = active ? 'white' : 'black';
    var fillColor = active ? 'black' : color;
    var color = active ? 'white' : 'black';

    return {
	    anchor: new google.maps.Point(11, 11),
	    url: 'data:image/svg+xml;utf-8,<svg width="22" height="22" viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg"><g><circle fill="' +
	    	fillColor + '" stroke="' + strokeColor + '" stroke-width="1" cx="11" cy="11" r="10"></circle><text fill="' + color + '" x="50%" y="50%" text-anchor="middle" dy=".3em" style="font-weight:bold; font-size:11px">' + 
	    	('000' + numero).slice(-3) + '</text></g></svg>'
	  };
}

      function setSelection(shape) {
        clearSelection();
        if (ecole) 
        	return; 

        selectedShape = shape;
        shape.setEditable(true);
      }

      function deleteSelectedShape() {
        if (selectedShape) {
          selectedShape.setMap(null);
        }
      }

      function getCentroid(points){
    var f;
    var x = 0;
    var y = 0;
    var nPts = points.length;
    var area = 0;

    for (var i = 0; i < nPts; i++) {   
        var pt1 = points[i % nPts];
        var pt2 = points[(i+1) % nPts];
        f = pt1.lat * pt2.lng - pt2.lat * pt1.lng;
        x += (pt1.lat + pt2.lat) * f;
        y += (pt1.lng + pt2.lng) * f;

        area += pt1.lat * pt2.lng;
        area -= pt1.lng * pt2.lat;        
    }
    area /= 2;
    f = area * 6;
    return new google.maps.LatLng(x/f, y/f);
}


function editTente(marker, latLng) {
	if (marker.id) {
		$.ajax({
			url: '<?php url('admin/module/logement/tentes?editTente'); ?>',
		  	method: "POST",
		  	cache: false,
			dataType: "json",
			data: { tente:marker.id, lat: latLng.lat(), lng: latLng.lng() }
		});
	}
}

function initMap() {
	bounds = new google.maps.LatLngBounds();
  map = new google.maps.Map(document.getElementById('map'), {
    center: {lat: 45.783443, lng: 4.767115},
    zoom: 17,
    streetViewControl: false,
    panControl: true,
    mapTypeControlOptions: {
 	 mapTypeIds: [google.maps.MapTypeId.TERRAIN, google.maps.MapTypeId.HYBRID]
    },
    mapTypeId: google.maps.MapTypeId.TERRAIN,
    zoomControl: true,
  });

  var styles = [{
    featureType: "poi",
  elementType: 'labels',
  stylers: [
    { visibility: 'off' }
  ]},
  {
    featureType: "poi.school",
  elementType: 'labels',
  stylers: [
    { visibility: 'on' }
  ]}];

  map.setTilt(0);
map.setOptions({styles: styles});


  drawingManager = new google.maps.drawing.DrawingManager({
    drawingMode: null,
    drawingControl: false,
    polygonOptions: {
    	strokeWeight: 3,
        fillOpacity: 0.5,
        clickable: true,
        draggable: false,
        editable: false
    },
    map: map
  });

  if (zone) {

  	$.ajax({
		url: '<?php url('admin/module/logement/tentes?getTentes'); ?>',
	  	method: "POST",
	  	cache: false,
		dataType: "json",
		data: { zone:zone },
		success: function(data) {
			var addMarker = false;
			for (var i in data) {
				addMarker = true;

				var bound = new google.maps.LatLng(data[i].latitude, data[i].longitude);
				bounds.extend(bound);
				var attributed = !!data[i].ecole;
				var own = attributed && !!ecole && ecole == data[i].ecole;

				var marker = new google.maps.Marker({
				    position: bound,
				  	icon: iconTente(own ? zoneColor : 'white', data[i].numero, !!ecole && attributed && !own || !ecole && attributed),
				    draggable: !ecole,
				    map: map
				  });

				marker.id = data[i].id;
				marker.numero = data[i].numero;
				marker.active = attributed;
				
				if (!ecole) {
					marker.addListener('dragend', function(e) {
			  			editTente(this, e.latLng);
			  		});
			  	} else if (!attributed || own) {
			  		marker.addListener('click', function(e) {
			  			this.setIcon(iconTente(this.active ? 'white' : zoneColor, this.numero));
			  			this.active = !this.active;

			  			$.ajax({
			  				url: '<?php url('admin/module/logement/tentes?set'); ?>',
						  	method: "POST",
						  	cache: false,
							dataType: "json",
							data: { tente:this.id, ecole:ecole },
			  			});
			  		});
			  	}
			}

			if (addMarker)
				map.fitBounds(bounds);
		}
	});


  	if (!ecole) {
  		map.addListener('click', function(event) {
	  		if (edit) {
	  			var marker = new google.maps.Marker({
				    position: event.latLng,
				  	icon: iconTente('white', ++count),
				    draggable: true,
				    map: map
				  });

		  		marker.addListener('dragend', function(e) { 
		  			editTente(this, e.latLng);
		  		});


		  		$.ajax({
					url: '<?php url('admin/module/logement/tentes?addTente'); ?>',
				  	method: "POST",
				  	cache: false,
					dataType: "json",
					data: { zone:zone, numero:count, lat: event.latLng.lat(), lng: event.latLng.lng() },
					success: function(data) {
						if (data) {
							count = parseInt(data.numero);
							marker.setIcon(iconTente('white', count));
							marker.id = data.id;
						}
					}
				});
			}
		});
	}

	
  }


  $.ajax({
		url: '<?php url('admin/module/logement/tentes?get'); ?>',
	  	method: "POST",
	  	cache: false,
		dataType: "json",
		data: { zone:zone, ecole:ecole },
		success: function(data) {
			var addPolygon = false;
			for (var i in data) {
				var points = [];
				addPolygon = true;

				for (var j in data[i].path) {
					var loc = new google.maps.LatLng(data[i].path[j].lat, data[i].path[j].lng);
					points.push({lat: loc.lat(), lng: loc.lng()});
					bounds.extend(loc);
				}



				var polygon = new google.maps.Polygon({
				    paths: points,
				    strokeOpacity: zone ? 0.5 : 1,
				    strokeColor: data[i].color,
				    strokeWeight: 2,
				    fillOpacity: zone ? 0.25 : 0.5,
				    fillColor: data[i].color,
				    map: map
				});
				polygon.id = data[i].id;
				

				if (!zone) {
					var marker = new google.maps.Marker({
					    position: getCentroid(points),
					    label: data[i].zone,
						icon: pinSymbol(data[i].color),
					    map: map
					  }); 
					marker.id = data[i].id;
					polygon.marker = marker;

					if (ecole) {
						for (var j in data[i].tentes) {
							var bound = new google.maps.LatLng(data[i].tentes[j].latitude, data[i].tentes[j].longitude);
							bounds.extend(bound);

							var marker = new google.maps.Marker({
							    position: bound,
							  	icon: iconTente(data[i].color, data[i].tentes[j].numero, data[i].tentes[j].ecole != ecole),
							    draggable: false,
							    map: map
							  });
						}
					}
				} 

				if (!zone) {
					polygon.addListener('click', function(mev) {
						var item = this;
						var func = function() {
			              	var points = [];
							for (var i = 0; i < this.getLength(); i++)
								points.push({lat: this.getAt(i).lat(), lng: this.getAt(i).lng()});
							item.marker.setPosition(getCentroid(points));
			            };

						item.getPath().addListener('set_at', func);
						item.getPath().addListener('insert_at', func);
						item.getPath().addListener('remove_at', func);

						setSelection(this);
						if (mev.vertex != null && this.getPath().getLength() > 3) {
						    this.getPath().removeAt(mev.vertex);
						}
					});

		            if (!ecole) {
		            	marker.addListener('click', function() {
				            window.location.href = '<?php url('admin/module/logement/tentes?zone='); ?>' + this.id;
			            });
			        }
		        } else {
		        	polygon.addListener('click', function(mev) {
						google.maps.event.trigger(map, 'click', mev);
					});
		        }
			}

			if (addPolygon)
				map.fitBounds(bounds);
		}
	});

google.maps.event.addListener(drawingManager, 'polygoncomplete', function(e) {
            drawingManager.setDrawingMode(null);
            // Add an event listener that selects the newly-drawn shape when the user
            // mouses down on it.
            var newShape = e;

            if (newShape.getPath().getLength() < 3) {
            	alert('Au moins 3 points');
            	newShape.setMap(null);
            	newShape = null;
            }
            else {
	            var path = [];
				
	            for (var i = 0; i < newShape.getPath().getLength(); i++) {
	            	path.push({
	            		lat:parseFloat(newShape.getPath().getAt(i).lat()), 
	            		lng:parseFloat(newShape.getPath().getAt(i).lng())});
	            }

           		$.ajax({
           			url: '<?php url('admin/module/logement/tentes?add'); ?>',
				  	method: "POST",
				  	cache: false,
					dataType: "json",
					data:{zone: label, path:path, color:color}
				});

				window.location.href="";
           	}
});

map.addListener('click', clearSelection);



}


function addZone() {
	$('#modal-ajout-zone').modal();
	$('#form-label').val('');
	$('#color-0').prop('checked', true);
}

function drawZone() {
	color = $('div.radio input[type=radio]:checked').data('color');
	label = $('#form-label').val();

	if (!color)
		$('div.radio input[type=radio]').addClass('form-error').removeClass('form-error', 1000);

	if (!label)
		$('#form-label').addClass('form-error').removeClass('form-error', 1000);
	
	if (label && color) {
		drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
		$.modal.close();
	}
}

    </script>
    <script async defer
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWPMhM7djiArwBgWyUuNY6rJXJb1UrnyQ&callback=initMap&libraries=drawing">
    </script>


<?php

//Inclusion du pied de page
require DIR.'templates/_footer.php';
