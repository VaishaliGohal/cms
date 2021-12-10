<?php 
	session_start();

	include_once('connection/connect.php');
	include_once('connection/post.php');

	$post = new Post;
	$posts = $post->fetch_all();

	// if(isset($_GET['cid'])){
	// 	$cid = $_GET['cid'];
	// }
	
	$query = $db->prepare("SELECT * FROM cusine WHERE post > 0");

	$query->execute();
	$cuisines = $query->fetchAll();

	$name = "Sort By";

	if((isset($_SESSION['login']) || isset($_SESSION['user_login'])) && isset($_GET['sid'], $_GET['slug'])){
		$sid = filter_input(INPUT_GET, 'sid', FILTER_VALIDATE_INT);
		$p = filter_input(INPUT_GET, 'p', FILTER_SANITIZE_STRING);

    	if (!$sid || !$p || empty($sid) || empty($p)) {
        	header("HTTP/1.0 404 Not Found");
        	exit;
    	}

		if($sid == '1' && $p == 'title'){
			$name = "Sort by: Title";
			$query1 = $db->prepare("SELECT * FROM post ORDER BY title ASC");

			$query1->execute();
			$posts = $query1->fetchAll();
		}
		else if($sid == '2' && $p == 'date-posted'){
			$name = "Sort by: Date Posted";
			$query1 = $db->prepare("SELECT * FROM post ORDER BY posted_on ASC");

			$query1->execute();
			$posts = $query1->fetchAll();
		}
		else if($sid == '3' && $p == 'new-old'){
			$name = "Sort by: Old to New";
			$query1 = $db->prepare("SELECT * FROM post ORDER BY post_id DESC");

			$query1->execute();
			$posts = $query1->fetchAll();
		}
	}

	$value = '';
	$action = '';
	$value1 = '';
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

	<?php if(isset($_SESSION['login']) || isset($_SESSION['user_login'])): ?>
		<div class="w3-dropdown-hover w3-mobile">
			<button class="w3-button"><?= $name ?><em class="fa fa-caret-down"></em></button>
			<div class="w3-dropdown-content w3-bar-block w3-black">
				<a href="index.php?sid=1&p=title" class="w3-bar-item w3-button w3-mobile" >Title</a>
				<a href="index.php?sid=2&p=date-posted" class="w3-bar-item w3-button w3-mobile" >Date Posted (old to new)</a>
				<a href="index.php?sid=3&p=new-old" class="w3-bar-item w3-button w3-mobile" >Release (new to old)</a>
			</div>
		</div>
	<?php endif ?>

	<form class="search" action="search.php" method="get" autocomplete="off">
		<input type="text" name="search" placeholder="Search...">
		<button type="submit">Go</button>
	</form>

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