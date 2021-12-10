<?php 

    session_start();

	include_once('../connection/connect.php');
	include_once('../connection/post.php');

    $post = new Post;

    $query = $db->prepare("SELECT * FROM cusine");
    $query->execute();
    $cuisines = $query->fetchAll();

    if(isset($_SESSION['login']) && $_SESSION['login'] == true){
        if(isset($_GET['id'])){
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id || empty($id)) {
                echo '<script>alert("Incorrect has been ID passed!!!")</script>';
                exit;
            }

            $query = $db->prepare("SELECT * FROM cusine WHERE cuisine_id = ?");
            $query->bindValue(1, $id);
            $query->execute();
            $cuisines = $query->fetch();
        }

        if(isset($_POST['cuisine'])){

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $cuisine = filter_input(INPUT_POST, 'cuisine', FILTER_SANITIZE_STRING);

            if(empty($cuisine)){
                $error = "*All fields are required!";
                $query = $db->prepare("SELECT * FROM cusine WHERE cuisine_id = ?");
                $query->bindValue(1, $id);
                $query->execute();
                $cuisines = $query->fetch();
            }
            else{
                $slug = slug($cuisine);

                $query1 = $db->prepare("UPDATE cusine SET cuisines = ?, cuisine_slug = ? WHERE cuisine_id = ?;");
                $query1->bindValue(1, $cuisine);
                $query1->bindValue(2, $slug);
                $query1->bindValue(3, $id);

                $query1->execute();

                header("Location: categories.php");
            }
        }
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Update Cuisine</title>
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
        <h2>Edit Cuisine</h2><br />
        <?php if(isset($error)): ?>
            <small style="color: #aa0000;"><?= $error ?></small>
            <br /><br />
        <?php endif ?>
        
        <form action="edit_category.php" method="post" autocomplete="off">
            <input type="hidden" name="id" value="<?= $cuisines['cuisine_id'] ?>" />
            <input type="text" name="cuisine" value="<?= $cuisines['cuisines'] ?>" /><br /><br />
            <input type="submit" value="Edit Cuisine" />
        </form><br /> <br />
        
        <a href="categories.php">Back</a>
    </div>
</body>
</html>

<?php
    }
    else{
        header("Location: index.php");
    }

?>