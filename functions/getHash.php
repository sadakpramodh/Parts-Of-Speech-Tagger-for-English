<?php
function hash_get($hash, $ky){
	if($hash[$ky]) return $hash[$ky];
	else return FALSE;
}
?>