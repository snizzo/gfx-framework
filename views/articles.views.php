<html>
<head><title>Articles page</title></head>
<body>
<?php foreach($data as $article) { ?>
<h1><?php echo $article['id']; ?></h1>
<p><?php echo $article['text']; ?></p>
<?php } ?>
</body>
</html>
