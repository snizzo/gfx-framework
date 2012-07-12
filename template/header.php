<span style="font-size:60px">GFX3</span><span>the framework for <?php random_names(); ?> people!</span>

<?php

function random_names(){
	$random = rand(1,3);
	switch($random){
		case 1:
			echo "<span style=\"color:red\">lovely</span>";
			break;
		case 2:
			echo "<span style=\"color:green\">great</span>";
			break;
		case 3:
			echo "<span style=\"color:grey\">cool</span>";
			break;
	}
}

?>
