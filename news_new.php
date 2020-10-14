<?php
require_once('../assets/inc/db.php'); 
require_once('../assets/inc/functions.php'); 
require('../assets/inc/header.php'); 

if (!isset($_SESSION['auth'])) {
    header("Location: ./403.php");
    exit();
}

if (isset($_POST['news_send'])) {
    $sql ='INSERT INTO news (auteurid, titre, contenu, image) VALUES (%d, "%s", "%s", "%s")';
    $id = $_SESSION['auth'];
    $titre = mysqli_real_escape_string($mysqli, $_POST['news_titre']);
    $contenu = mysqli_real_escape_string($mysqli, $_POST['news_contenu']);
    $image = mysqli_real_escape_string($mysqli, $_FILES["news_img"]["name"]);
    


    $target_dir = "../assets/img/";
    $target_file = $target_dir . basename($_FILES["news_img"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["news_img"]["tmp_name"]);
    if($check == false) {
        $_SESSION['news_add']['erreur']['news_img_not_img'] = "Le fichier n'est pas une image.";
    }

    if (file_exists($target_file)) {
        $_SESSION['news_add']['erreur']['news_img_exist'] = "Le fichier existe déjà.";
    }

    if ($_FILES["news_img"]["size"] > 2000000) {
        $_SESSION['news_add']['erreur']['news_img_size'] = "L'image doit faire moins de 2 Mo.";
    }

    $newsql = sprintf($sql, $id, $titre, $contenu, $image);

    if (isset($_SESSION['news_add']['erreur'])) { ?> 
            
        <br>
    <div class="col-md-12 offset-md-3" style="width: 100%;">
        <div class="alert alert-danger col-md-6 mb-3" role="alert">
            <b>Veuillez vérifier les informations suivants :</b>
            <?php foreach ( $_SESSION['news_add']['erreur'] as $type => $message) { ?>
                <li class="text-left"><?= $message; ?></li>
            <?php } ?>
           
        </div>
    </div>
    <br>

    <?php unset($_SESSION['news_add']['erreur']); 
} else {



    if (!mysqli_query($mysqli, $newsql)) {
        info_error(mysqli_error($mysqli));
    } else {
        $news_id = mysqli_insert_id($mysqli);
        if (move_uploaded_file($_FILES["news_img"]["tmp_name"], $target_file)) {
            header("Location: voirNews.php?id=". $news_id);
        } else {
            info_error("Une erreur est survenu lors du transfert de l'image");
        }


    
    }

}



} else {
    $titre = $contenu = "";
}


?>


<form action="" method="post" enctype="multipart/form-data">

    <div class="form-group">
        <label for="news_titre">Titre</label>
        <input type="text" class="form-control" name="news_titre" id="news_titre" aria-describedby="emailHelp" required>
    </div>
    <div class="form-group">
        <label for="news_contenu">Contenu</label>
        <textarea class="form-control" name="news_contenu" id="news_contenu" rows="18" required></textarea>
    </div>
    <div class="form-group">
        <div class="custom-file">
            <input type="file" class="custom-file-input" name="news_img" id="news_img" accept="image/*" required>
            <label class="custom-file-label" for="customFile">Fichier</label>
        </div>
    </div>

    <button type="submit" id="news_send" name="news_send" class="btn btn-primary">Valider</button>

</form>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.js"></script>

<script>
    bsCustomFileInput.init()
</script>
</body>
</html>
