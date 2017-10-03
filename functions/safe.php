<?php
function safe($txt){
	
	$txt = str_replace("'","\'",$txt);
	return $txt;
}
?>