<?php 

    session_start();

	include_once('../connection/connect.php');
    include_once('../connection/post.php');

    $post = new Post;

    if(isset($_SESSION['login']) && $_SESSION['login'] == true){
        if(isset($_GET['id'])){
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id || empty($id)) {
                echo '<script>alert("Incorrect has been ID passed!!!")</script>';
                exit;
            }

            $query = $db->prepare("DELETE FROM post WHERE post_id = ?");

            $query->bindValue(1, $id);
            $query->execute();

            header("Location: delete.php");
        }


        $posts = $post->fetch_all();

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Delete Recipe</title>
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
        <h2>Delete a Recipe </h2><br />
        <?php if(isset($error)): ?>
            <small style="color: #aa0000;"><?= $error ?></small>
            <br /><br />
        <?php endif ?>
        <form action="delete.php" method="get">
            <select name="id" lass="w3-select">
                    <option value=0>Select a Recipe</option>
                <?php foreach($posts as $post): ?>
                    <option value="<?=$post['post_id']?>">
                        <?= $post['title'] ?>
                    </option>
                <?php endforeach ?>
            </select><br /> <br />
            <input type="submit" name="delete" value="Delete" onclick="this.form.submit();" />
        </form>
    </div>
</body>
</html>

<?php

    }
    else{
        header("Location: index.php");
    }

?>