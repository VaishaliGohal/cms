<?php 
	session_start();

	include_once('connection/connect.php');
	include_once('connection/post.php');

	$limit = 2;

    if(isset($_GET['search']) && isset($_GET['page'])){
        $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

		if (!$search || empty($search)) {
        	header($header);
        	exit;
    	}

    	if (!$page || empty($page)) {
        	header($header);
        	exit;
    	}
    }
    else{
		$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_STRING);
        $page = 1;
    }

    $offset = ($page - 1) * $limit;

    $post = new Post;
    $posts = $post->fetch_search_term($search, $offset, $limit);
	
	if($posts == null){
        $error = "No page found with a title <i>'$search'</i>";
    }
    else if($search == null || $search == ' '){
        $error = "No characters entered!";
    }

	$query = $db->prepare("SELECT * FROM cusine WHERE post > 0");

	$query->execute();
	$cuisines = $query->fetchAll();

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

	<ul>
		<?php foreach ($cuisines as $cuisine): ?>
			<li>
				<a href="cuisine_categories.php?cid=<?= $cuisine['cuisine_id'] ?>" ><?= $cuisine['cuisines'] ?></a>
			</li>
		<?php endforeach ?>
	</ul>

    <form class="search" action="search.php?" method="get" autocomplete="off">
		<input type="text" name="search" placeholder="Search...">
		<button type="submit">Go</button>
	</form>

	<div class="container">
        <?php if(isset($error)): ?>
            <small style="color: #aa0000;"><?= $error ?></small>
            <br /><br />
        <?php else: ?>
		    <h1><b>Search Term: <?= $search ?></b></h1>

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
        <?php endif ?>
	</div>

	<?php

		$search = $_GET['search'];
		$query1 = $db->prepare("SELECT * FROM post
								WHERE title LIKE '%{$search}%'");
		$query1->execute();
		$query1->fetchAll();

		$post_count = $query1->rowCount();


		if($post_count > 0){

			$total_posts = $post_count;
			$total_page = ceil($total_posts / $limit);

		?>

		

			<ul class="pagination">
				<?php if($page > 1): ?>
					<li><a href="search.php?search=<?= $search ?>&page=<?= $page - 1 ?>">&larr;</a></li>
				<?php endif ?>

				<?php for($i = 1; $i <= $total_page; $i++): ?>
					<?php 
					
						if($i == $page){
							$active = "active";
						}
						else{
							$active = "";
					}
					?>
					
					<li class="<?= $active ?>"><a href="search.php?search=<?= $search ?>&page=<?= $i ?>"><?= $i ?></a></li>
				<?php endfor ?>
				
				<?php if($page < $total_page): ?>
					<li><a href="search.php?search=<?= $search ?>&page=<?= $page + 1 ?>">&rarr;</a></li>
				<?php endif ?>
			</ul>

	<?php

		}

	?>

</body>
</html>