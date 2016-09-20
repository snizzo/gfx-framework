	<?php EStructure::view("header"); ?>
		<h3>Database configuration editor</h3>
            Current driver: <?=$data[0]['driver']?><br/>
            <form action="database.php" method="post">
                <input type="hidden" name="reset" value="yes">
                <input type="submit" value="Reset entire database">
            </form>
            
            <hr>
        <form action="database.php" method="post">
            <input type="radio" name="changedrv" value="EMysql" <?php if($data[0]['driver']=='EMysql'){echo "checked";} ?>> MySQL (requires configured server)<br>
            <input type="radio" name="changedrv" value="ESQLite" <?php if($data[0]['driver']=='ESQLite'){echo "checked";} ?>> SQLite (doesn't require server)<br>
            <input type="submit" value="Change driver">
        </form>
        <hr>
        <?php if($data[0]['driver']=="EMysql"){ ?>
        Please define your database configuration (host, username, password and database name) to connect at:<br><br>
        <?=$data[0]['notification']?>
			<table border="0">
				<form action="database.php" method="post">
				<tr><td>Name:</td><td><input type="text" name="name" value="<?=$data[0]['name']?>" placeholder="database name"></td></tr>
				<tr><td>Host:</td><td><input type="text" name="host" value="<?=$data[0]['host']?>" placeholder="host"></td></tr>
				<tr><td>User:</td><td><input type="text" name="user" value="<?=$data[0]['user']?>" placeholder="user"></td></tr>
				<tr><td>Password:</td><td><input type="password" name="password" value="<?=$data[0]['pass']?>" placeholder="password"></td></tr>
				<tr><td>Repeat password:</td><td><input type="password" name="password2" value="<?=$data[0]['pass2']?>"  placeholder="repeat password"></td></tr>
				
				<tr><td colspan="2"><input style="float:right" type="submit" value="Try configuration"></td></tr>
				</form>
			</table>
        <?php } ?>
        
        <?php if($data[0]['driver']=="ESQLite"){ ?>
        Attempting connection to database:<br><br>
        <?=$data[0]['notification']?>
        <?php } ?>
		
	<?php EStructure::view("footer"); ?>
