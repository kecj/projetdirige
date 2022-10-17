<?php session_start(); ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Ajout d'item</title>
	<style>
        <?php require 'css/style.css'; ?>
    </style>
	<?php require_once("sql.php"); ?>
	<?php require_once("fonctions.php"); ?>
</head>

<?php

if(empty($_SESSION) || CheckIfAdmin($_SESSION["idJoueur"]) != 1){
	ChangePage("index.php");
}

if (isset($_POST['envoyer']))
{
	$rep = 'images/';
	$fich = $rep . basename($_FILES['fichier']['name']);
	$imageFileType = strtolower(pathinfo($fich,PATHINFO_EXTENSION));
	if (file_exists($fich)) 
	{
		$temp = explode(".", $_FILES["file"]["name"]);
		$newfilename = round(microtime(true)) . '.' . end($temp);
		if (move_uploaded_file($_FILES['fichier']['tmp_name'], $rep . $newfilename . end($temp)))
		{
			ajouterItem($_POST['nom_item'], $_POST['desc_item'],$_POST['type_item'],$_POST['poids_item'],$_POST['qte_item'],$_POST['prix_item'],$_POST['fichier']);
			ChangePage('index.php'); 
		} 
	}
	else{
		if ($_FILES["fileToUpload"]["size"] > 500000) {
			echo "<script>console.log('Désolé, le fichier est trop volumineux.')</script>";
		}
		else
		{
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" )
			{
				echo "<script>console.log('Désolé, seulement jpg, jpeg, gif, png acceptés.')</script>";
			}
			else
			{
				if (move_uploaded_file($_FILES['fichier']['tmp_name'], $fich))
				{
					ajouterItem($_POST['nom_item'], $_POST['desc_item'],$_POST['type_item'],$_POST['poids_item'],$_POST['qte_item'],$_POST['prix_item'],basename($_FILES['fichier']['name']));
					ChangePage('adminconnect.php'); 
				} 
				else 
				{
					echo "<script>console.log('Problème lors du déplacement')</script>";
				}	
			}
		}
	}
}
?>

<body>
	<?php CreateHeaderV3("Capital","Menu Admin", "Déconnexion","modifiercapitaladmin.php","adminconnect.php", "index.php",$_SESSION["username"]); ?>
	<div id="ajout_conteneur">
		<form action="ajout.php" method="post" enctype="multipart/form-data" class="ajout_item_conteneur">
			<div class="ajout_top_lane">
				<div class="ajout_image_preview_container">
					<img id="uploadPreview" class="ajout_image_preview">
				</div>
				<div class="ajout_top_lane_infos">
					<div class="ajout_nom_container">
						<label for="nom_img" class="ajout_item_nom_label">Nom</label>
						<input type="text" name="nom_item" class="ajout_item_nom" required>
					</div>
					<div class="ajout_image_uploader_container">
						<input type="hidden" name="MAX_FILE_SIZE" value="5000000">
						<label for="fichier">Image</label>
						<input type="file" accept=".gif,.GIF,.jpg,.JPG,.jpeg,.JPEG,.png,.PNG" name="fichier" id="uploadImage" onchange="PreviewImage()" class="ajout_image_uploader" required>
					</div>

				</div>
			</div>

			<div class="ajout_mid_lane">
				<div class="ajout_desc_container">
					<label for="desc_img">Description</label>
					<input type="text" name="desc_item" class="ajout_item_desc" required>
				</div>
			</div>
			<div class="ajout_bot_lane">
				<div class="ajout_qte_container">
					<label for="desc_img">Quantite</label>
					<input type="number" name="qte_item" required>
				</div>
				<div class="ajout_prix_container">
					<label for="desc_img">Prix unitaire</label>
					<input type="text" name="prix_item" required>
				</div>
				<div class="ajout_poids_container">
					<label for="desc_img">Poids</label>
					<input type="text" name="poids_item" required>
				</div>
				<div class="ajout_type_container">
					<label for="desc_img">Type</label>
					<select name="type_item" id="color">
						<option value = 1>Armure</option>
						<option value = 2>Arme</option>
						<option value = 3>Nourriture</option>
						<option value = 4>Drogue</option>
						<option value = 5>Munition</option>
						<option value = 6>Autre</option>
					</select>
				</div>
			</div>
			<input type="submit" name="envoyer" value="Ajouter" class="ajout_submit">
		</form>
	</div>
	<?php CreateFooter() ?>
</body>

</html>

<script type="text/javascript">

    function PreviewImage() {
        var oFReader = new FileReader();
        oFReader.readAsDataURL(document.getElementById("uploadImage").files[0]);

        oFReader.onload = function (oFREvent) {
            document.getElementById("uploadPreview").src = oFREvent.target.result;
        };
    };

</script>