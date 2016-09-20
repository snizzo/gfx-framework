<?php

include_once("../gfx3/lib.php");

if(isset($_GET['l']) and $_GET['l']=='y'){
	EProtect::logout();
	EUtility::redirect("index.php");
}


EStructure::view("header");
?>
		<form action="logout.php" method="get"><input type="hidden" name="l" value="y"><input style="width:100px;height:40px;" type="submit" value="Logout"></form>
<?php
EStructure::view("footer");
?>
