<?php 

    session_start();

	include_once('../connection/connect.php');
    include_once('../connection/post.php');

    if(isset($_SESSION['login'])){

        if(isset($_POST['cuisine'])){
            $cuisine = filter_input(INPUT_POST, 'cuisine', FILTER_SANITIZE_STRING);

            if(empty($cuisine)){
                $error = "Field is required!";
            }
            else{

                $slug = slug($cuisine);

                $query = $db->prepare("INSERT INTO cusine (cuisines, cuisine_slug) VALUES (?, ?);");
                $query->bindValue(1, $cuisine);
                $query->bindValue(1, $slug);

                $query->execute();

                header("Location: categories.php");
            }
        }

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Add Cuisine</title>
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
        <h2>Add Cuisine</h2><br />
        <?php if(isset($error)): ?>
            <small style="color: #aa0000;"><?= $error ?></small>
            <br /><br />
        <?php endif ?>
        
        <form action="add_category.php" method="post" autocomplete="off">
            <input type="text" name="cuisine" placeholder="Cuisine" /><br /><br />
            <input type="submit" value="Add Cuisine" />
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