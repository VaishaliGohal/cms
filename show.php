<?php
    session_start();

    include_once('connection/connect.php');
    include_once('connection/post.php');
 
    if(isset($_SESSION['login'])){
        $value = 'Logout';
        $value1 = 'Dashboard';
        $action = 'admin/logout.php';
    }
    else if(isset($_SESSION['user_login'])){
        $value = 'Logout';
        $value1 = 'Account Info';
        $action = 'admin/logout.php';
    }
    else{
        $value = 'Login';
        $value1 = 'Favourites';
        $action = 'admin/index.php';
    }

    $post = new Post;

    if(isset($_GET['id'], $_GET['p'])){
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $p = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);

        if (!$id || !$p || empty($id) || empty($p)) {
            header("HTTP/1.0 404 Not Found");
        	exit;
    	}
        else{
            $data = $post->fetch_data($id, $p);
            $num = $post->fetch_count($id, $p);

            if($num == 0){
                header("HTTP/1.0 404 Not Found");
        	    exit;
            }
        }
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Home - Recipe Content Management System</title>
	<link rel="stylesheet" href="styles/style.css" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>

	<div>
	<a href="index.php">Home</a>
	<a href="admin/index.php"><?= $value1 ?></a>

	<?php if((isset($_SESSION['login'])) || (isset($_SESSION['user_login']))): ?>
		<a href="<?= $action ?>" onclick="<?php $click ?>"><?= $value ?></a>
	<?php else: ?>
			<a href="users/add_users.php">Sign Up</a>
			<a href="<?= $action ?>" ><?= $value ?></a>
		<?php endif ?>
	</div>
		<br />	<br />

    <form class="search" action="search.php" method="get" autocomplete="off">
		<input type="text" name="search" placeholder="Search...">
		<button type="submit">Go</button>
	</form>

	<div class="container">
        <h2><?= $data['title'] ?></a></h2>
        <small>
            Post date - 
            <?= date("F d, Y, h:i a", strtotime($data['posted_on']))?>
        </small>
        <br />
        <p>
            <?= $data['description'] ?>
        </p>
        <?php if(empty($data['image']) === false): ?>
            <img src="uploads/<?=$data['image']?>" alt = "<?=$data['image']?>" width="500" height="500" >  
		<?php endif ?>
        

        <?php
            include('comments.php');
        ?>
    </div>
</body>
</html>

<?php
    }
    else{
        header('Location: index.php');
        exit();
    }

?>