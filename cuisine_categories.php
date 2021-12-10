<?php 
	/* Cuisine Catagories*/
	session_start();

	include_once('connection/connect.php');
	include_once('connection/post.php');

	if(isset($_GET['cid'])){
		$cid = filter_input(INPUT_GET, 'cid', FILTER_VALIDATE_INT);
		$p = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);
		if (!$cid || !$p || empty($cid) || empty($p)) {
        	header("HTTP/1.0 404 Not Found");
        	exit;
    	}
	}

    $query = $db->prepare("SELECT * FROM post JOIN cusine ON post.cuisine_id = cusine.cuisine_id WHERE post.cuisine_id = ? AND cusine.cuisine_slug = ? ORDER BY post_id DESC");
    $query->bindValue(1, $cid);
	$query->bindValue(2, $p);
    $query->execute();

    $posts = $query->fetchAll();

    $query1 = $db->prepare("SELECT * FROM cusine WHERE post > 0");

    $query1->execute();
    $cuisines = $query1->fetchAll();

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
    <div>
        <ul>
            <?php foreach ($cuisines as $cuisine): ?>
                <li>
                    <a href="cuisine_categories.php?cid=<?= $cuisine['cuisine_id'] ?>&p=<?= $cuisine['cuisine_slug'] ?>" ><?= $cuisine['cuisines'] ?></a>
                </li>
            <?php endforeach ?>
        </ul>
    </div>

	<form class="search" action="search.php" method="get" autocomplete="off">
		<input type="text" name="search" placeholder="Search...">
		<button type="submit">Go</button>
	</form>

    <?php
        $query1 = $db->prepare("SELECT * FROM cusine WHERE cuisine_id = $cid");

        $query1->execute();
        $cuisines1 = $query1->fetch();
    ?>

	<div class="container">
		<ul style="list-style-type:none;">
			<?php foreach ($posts as $post): ?>
				<li>
					<h2><a href="show.php?id=<?=$post['post_id']?>&p=<?=$post['slug']?>"><?= $post['title'] ?></a></h2>
				</li>
				<li>
					<small>Post date -<?= date("F d, Y, h:i a", strtotime($post['posted_on']))?></small>
				</li>
				<?php if(strlen($post['description']) > 200): ?>
					<li>
						<?= substr($post['description'], 0, 200)?>...<a href="show.php?id=<?=$post['post_id']?>&p=<?=$post['slug']?>">Click here for Read more</a>
					</li>
				<?php else: ?>
					<li><?= $post['description'] ?></li>
				<?php endif ?>
				<?php if(empty($post['image']) === false): ?>
					<li>
						<img src="uploads/<?=$post['image']?>" alt = "<?=$post['image']?>" width="500" height="500" >
					</li>
				<?php endif ?>
			<?php endforeach ?>
			
		</ul>
	</div>
</body>
</html>
