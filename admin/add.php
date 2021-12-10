<?php 

    session_start();

	include_once('../connection/connect.php');
    include_once('../connection/post.php');

    $query = $db->prepare("SELECT * FROM cusine");
    $query->execute();
    $cuisines = $query->fetchAll();

    if(isset($_SESSION['login']) && $_SESSION['login'] == true){
        if(isset($_POST['title'], $_POST['description'])){
            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $description = $_POST['description']; // Can not validate because of the use of WYSIWYG
            $cuisine = filter_input(INPUT_POST, 'cuisine', FILTER_VALIDATE_INT);

            if(isset($_FILES['image'])){
                $file_name = $_FILES['image']['name'];
                $file_size = $_FILES['image']['size'];
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_type = $_FILES['image']['type'];
                $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
                $extensions = array("jpeg", "jpg", "png", "");
    
                if(in_array($file_ext, $extensions) === true){
                    move_uploaded_file($file_tmp, "../uploads/".$file_name);
                }
            }
            

            if(empty($title) || empty($description) || empty($cuisine)){
                $error = "*All fields are required!";
            }
            else if(in_array($file_ext, $extensions) === false){
                $error = "File extension is not allowed. Allowed extension: JPG or PNG.";
            }
            else{

                $slug = slug($title);

                $query = $db->prepare(" INSERT INTO post (cuisine_id, title, description, image, slug) VALUES (?, ?, ?, ?, ?)");

                $query->bindValue(1, $cuisine);
                $query->bindValue(2, $title);
                $query->bindValue(3, $description);
                $query->bindValue(4, $file_name);
                $query->bindValue(5, $slug);

                $query->execute();

                $query1 = $db->prepare("UPDATE cusine SET post = post + 1 WHERE cuisine_id = $cuisine");

                $query1->execute();

                header("Location: index.php");
            }
        }
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Add Recipe</title>
	<link rel="stylesheet" href="../styles/style.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

	<div>
	<a href="../index.php">Home</a>
    <a href="index.php">Dashboard</a>
    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a>
	</div>
    
    <div class="container">
        <h2>Add a Recipe </h2><br />
        <?php if(isset($error)): ?>
            <small style="color: #aa0000;"><?= $error ?></small>
            <br /><br />
        <?php endif ?>
        <form action="add.php" method="post" enctype="multipart/form-data" autocomplete="off">
            <input type="text" name="title" placeholder="Recipe Title" /><br /><br />
            <select name="cuisine">
                <option value="0">Select Cuisine</option>
                <?php foreach($cuisines as $cuisine): ?>
                    <option value="<?= $cuisine['cuisine_id'] ?>"><?= $cuisine['cuisines'] ?></option>
                <?php endforeach ?>
            </select><br /><br />
            <textarea id="description" name="description"></textarea><br /><br /><br /><br />
            <input type="file" name="image" /><br /><br />
            <input type="submit" value="Add Recipe" />
        </form>
    </div>

    <script src="ckeditor/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('description');
    </script>

</body>
</html>

<?php
    }
    else{
        header("Location: index.php");
    }

?>