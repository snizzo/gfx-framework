<?php

include_once("gfx3/lib.php");

//loading template method
EStructure::load();

EStructure::code();

echo "Hello world! Here you can download your latest copy of the GFX framework by simply cloning this mercurial repo:<br>
<pre> <i>hg clone https://code.google.com/p/gfx-framework/</i></pre>";
echo "Here you can find also the manual: <a href=\"http://code.google.com/p/gfx-framework/downloads/list\">http://code.google.com/p/gfx-framework/downloads/list</a></pre>";
EStructure::insert("content");

EStructure::unload();
?>
