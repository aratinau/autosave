<?php
require_once('connection_sql.php');

$sql = "SELECT * FROM textarea";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
foreach ($result as $elem)
{
	$textarea = $elem['content'];
}

?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8" />
		<title>autosave</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.7.0/underscore-min.js"></script>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:300,600' rel='stylesheet' type='text/css'>
		<style>
		html, body {
			font-family: 'Open Sans', sans-serif;
		}
		</style>
	</head>
<body>


<div class="container">
<div>
	etat : <span id="state"></span>
	content : <span id="content"></span>
	<span id="sync">sync</span>
<hr>
</div>

<div id="get_content" style="width: 49%; border: 1px solid red; float: right; display:none; font: 11px BlinkMacSystemFont;"></div>
	<textarea style="width: 50%; height: 1000px; border:0px solid black; outline: none;" data-id="1" data-datetime="<?php echo date('Y-m-d H:i:s'); ?>"><?php echo $textarea; ?></textarea>
</div> <!-- /container -->
<?php
// TODO
// install.php
//
// ajouter :
// un date time + heure
// un id du textarea
//
///!\ la base de donnee a changee
//
// afficher le contenu du serveur a cote pour conaitre les difference ?
// afficher l'heure de la derniere synchro + le temps ecoule depuis celle ci
//
// generer un id unique au chargement de la page pour pas qu'il y ai conflit alors qu'on n'a fait qu'enregistrer sur cette page sans changer
// et je pense que cet id doit etre enregistrer dans la bdd
// comme ca il est generer au chargement de la page
// puis si j'update la note, il est updated
// et si je reupdate la note sur la meme page, il voie qu'il n'y a pas conflit
//
?>

<script>
$(document).ready(function() {
	function js_yyyy_mm_dd_hh_mm_ss() {
		now = new Date();
		year = "" + now.getFullYear();
		month = "" + (now.getMonth() + 1); if (month.length == 1) { month = "0" + month; }
		day = "" + now.getDate(); if (day.length == 1) { day = "0" + day; }
		hour = "" + now.getHours(); if (hour.length == 1) { hour = "0" + hour; }
		minute = "" + now.getMinutes(); if (minute.length == 1) { minute = "0" + minute; }
		second = "" + now.getSeconds(); if (second.length == 1) { second = "0" + second; }
		return year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second;
	}

	var tmp_id = Math.floor((Math.random() * 10) + 4294967295);
	console.log(tmp_id);

	function autosave(data) {
		$.ajax({
			type: "post",
			url: "autosave.php",
			data: data,
			success: function(data) {
				$('#sync').css('font-weight', 'normal');
			}
		});
	}
	function compareDate(date) {
		$.ajax({
			type: "post",
			url: "compare_date.php?id=1",
			data: 'datetime=' + date,
			success: function(data) {
				console.log(data);
				if(data == 1)
				{
					//alert ('ok');
					$("#state").text('datetime ok').css('color', 'green');
			}
				else
				{ //alert ('la date sur le serveur est plus recente, conflit');
					$("#state").text('conflit avec le serveur : sync ecrasera avec cette version').css('color', 'red');
					$.ajax({
						type: "post",
						url: "get_content.php?id=1",
						data: 'datetime=' + date,
						success: function(data) {
							$('#get_content').show(500);
							$('#get_content').html(data.replace(/\n/g, "<br />"));
					}});
				}
			}
		});
	}

	var textarea_value = $('textarea:eq(0)').val();
	function compareContent(content) {
		$.ajax({
			type: "post",
			url: "compare_content.php?id=1",
			data: 'content=' + textarea_value,
			success: function(data) {
				if(data == 1)
				{
					$('#content').text('content identique').css('color', 'green');
				}
				else
				{
					//alert ('la date sur le serveur est plus recente, conflit');
					$('#content').text('risque conflit').css('color', 'red');
				}
			}
		});
	}
	function compare_tmp_id() {
		$.ajax({
			type: "post",
			url: "compare_tmp_id.php?id=1",
			data: 'tmp_id=' + tmp_id,
			success: function(data) {
				if(data == 1)
				{
					console.log('tmp id identique');
				}
				else
				{
					console.log('tmp id PAS identique');
				}
			}
		});
	}


	console.log("total de textarea : " + ($('textarea').length));
	//console.log($('textarea')[0]);

	$('#sync').click(function () {
		//var thisTextarea = $('textarea')[0];
		var dataString = 'textarea=' + $('textarea:eq(0)').val();
		dataString += '&id=' + $('textarea:eq(0)').attr("data-id");
		dataString += '&datetime=' + $('textarea:eq(0)').attr("data-datetime");
		dataString += '&tmp_id=' + parseInt(tmp_id);
		autosave(dataString);
		console.log(dataString);
		var datetime = new Date('Y-m-d H:i:s');
		$('textarea:eq(0)').attr("data-datetime", js_yyyy_mm_dd_hh_mm_ss());
		$('#content').text('content identique').css('color', 'green');
		$("#state").text('datetime ok').css('color', 'green');
	});

	$('textarea:eq(0)').keyup(function () {
		//var textarea = $("#autosavedTextare").val();
		//var dataString = 'textarea=' + textarea;
		//autosave(dataString);
		//
		// on enregistre la val du textarea dans une variable
		// mais il faut verifie en ajax a chaque touche si ca n'a pas change sur le serveur pour pas qu'il y ait de conflit avant de sync
		compareDate($('textarea:eq(0)').attr("data-datetime"));
		compareContent($('textarea:eq(0)').val());
		$('#sync').css('font-weight', 'bold');
	});

	$("#sync").hover(
		function() {
			$(this).css('cursor', 'pointer');
		},
		function() {
			$(this).css('cursor', 'none');
		}
	);

	$(this).mouseenter(function() {
		compareDate($('textarea:eq(0)').attr("data-datetime"));
		compareContent(textarea_value);
		$.ajax({
			type: "post",
			url: "get_content.php?id=1",
			success: function(data) {
				$('textarea:eq(0)').val(data);
		}});
		compare_tmp_id();
	});
	compareDate($('textarea:eq(0)').attr("data-datetime"));
	compareContent(textarea_value);
	$('#get_content').hide();
});
</script>

</body>
</html>
