<html>
<head><title>Articles page</title></head>
<body>
<?php foreach($data as $article) { ?>
<h1><?php echo $article['body']; ?></h1>
<p><?php echo $article['author']; ?></p>
<?php } ?>
</body>
</html>
