<?php

    include_once('connection/connect.php');

    if(isset($_GET['id'], $_GET['p'])){
        $post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $p = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);

        $query = $db->prepare("SELECT * FROM comment WHERE post_id = ? ORDER BY comment_id DESC");
        $query->bindValue(1, $post_id);
        $query->execute();

        $comments = $query->fetchAll();

        if(isset($_POST['title'], $_POST['comment'], $_POST['name'], $_POST['captcha'])){

            $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
            $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $post_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $captcha = filter_input(INPUT_POST, 'captcha', FILTER_VALIDATE_INT);

            if(empty($title) || empty($comment) || empty($name) || empty($captcha)){
                $error = "All fields are required!";
            }
            else if($captcha == $_SESSION['captcha']){
                $query = $db->prepare(" INSERT INTO comment (post_id, name, title, comments) VALUES (?, ?, ?, ?)");

                $query->bindValue(1, $post_id);
                $query->bindValue(2, $name);
                $query->bindValue(3, $title);
                $query->bindValue(4, $comment);

                $query->execute();
                header("Location: show.php?id=$post_id&$p");
                
            }
            else{
                $error = "Captcha verification failed!";
            }
        }
    }

    if(isset($_GET['did'], $_GET['id'])){
        $did = filter_input(INPUT_GET, 'did', FILTER_VALIDATE_INT);
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $query3 = $db->prepare("SELECT * FROM post WHERE post_id = ?");
        $query3->bindValue(1, $id);
        $query3->execute();
        $post = $query3->fetch();

        $p = $post['slug'];

        $query1 = $db->prepare("DELETE FROM comment WHERE comment_id=?");
        $query1->bindValue(1, $did);

        $query1->execute();

        header("Location: show.php?id=$id&slug=$p");
        exit();
    }
?>

<!DOCTYPE html>
<html lang=en>
<head>
	<meta charset="utf-8">
	<title>Comments</title>
	<link rel="stylesheet" href="styles/style.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
    
    <div class="container">
        <h3><b>Comments</b></h3><br>
        <?php if(isset($error)): ?>
            <small style="color: #aa0000;"><?= $error ?></small>
            <br /><br />
        <?php endif ?>
        <form action="show.php?id=<?= $post_id ?>&p=<?= $p ?>" method="post" autocomplete="off">
            <?php if(isset($_SESSION['user_login'])): ?>
                <input type="hidden" name="name" value="<?= $_SESSION['user_login'] ?>" />
            <?php elseif(isset($_SESSION['login'])): ?>
                <input type="hidden" name="name" value="<?= $_SESSION['login'] ?>" />
            <?php else: ?>
                <input class="w3-input w3-animate-input" type="text" name="name" placeholder="Name" style="width:40%" value= "<?php
                    if(isset($_POST['name']) && $captcha != $_SESSION['captcha']){
                        echo $_POST['name']; 
                    } ?>" /><br/>
            <?php endif ?>
            <input class="w3-input w3-animate-input" type="text" name="title" placeholder="Title" style="width:40%" value= "<?php
                if(isset($_POST['title']) && $captcha != $_SESSION['captcha']){
                    echo $_POST['title'];
                } ?>" /><br/>
            <input class="w3-input w3-border" name="comment" placeholder="Comment" value="<?php
                if(isset($_POST['comment']) && $captcha != $_SESSION['captcha']){
                    echo $_POST['comment']; 
                } ?>" /><br />
            <div>
            <div>
                <input type="text" name="captcha" placeholder="Verify 6-digits number"/>
                <label><?= $_SESSION['captcha'] ?></label>

                <!-- <img src="captcha.php" alt="captcha"></img> -->
            </div>
            <br /><br />
            <input type="submit" name="submit" value="Post Comment" />
        </form>
    </div>
    <div class="container">
        <h1><b>Comments</b></h1>

        <ul style="list-style-type:none;">
            <?php foreach ($comments as $comment): ?>
                <li>
                    <h2><?= $comment['title'] ?></h2>
                </li>
                <li>
                    <small>
                        by -
                        <?= $comment['name'] ?> on 
                        <?= date("F d, Y, h:i a", strtotime($comment['comment_on']))?>

                        <?php if(isset($_SESSION['login'])): ?>
                            <a style="color:red" href="comments.php?id=<?=$post_id?>&p<?=$p?>&did=<?= $comment['comment_id']?>">(delete)</a>
                        <?php endif ?>
                    </small>
                </li>
                <li><?= $comment['comments'] ?></li>
            <?php endforeach ?>
        </ul>
    </div>
</body>
</html>