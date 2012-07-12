<?php

include_once("gfx3/lib.php");

$main = new EMain();
$template = new EStructure();

$template->code();

echo "Hello world! Here you can download your latest copy of the GFX framework -> ";
$template->insert("content");

?>
