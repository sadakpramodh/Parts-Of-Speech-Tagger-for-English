<?php
error_reporting(0);
//required functions
require_once("functions/initialize.php");
require_once("functions/tokenizer.php");
require_once("functions/safe.php");
require_once("functions/getHash.php");
require_once("functions/startStateTag.php");
require_once("functions/finalStateTag.php");
//check weather requested / not
if(isset($_POST["phrase"]))
	{
		$st = $_POST["phrase"];
	
function post($txt){
	initialize();
	$txt = tokenizer($txt);
	$txt = startstatetag($txt);
	//echo "Startstatetagged: ".$txt."<br>";
	$txt = finalstatetag($txt);
	//echo "Finalstatetagged: ".$txt."<br>";
	echo "<font color=\"black\" ><b>Input: ".$_POST["phrase"]."</b></font><br>";
	$pieces = explode(" ", $txt);
	echo'<table class="table">';
	echo'<thead>
      <tr>
        <th><font color=\"blue\" >Word</font></th>
        <th><font color=\"green\" >Parts of Speech</green></th>
	  </tr>
	  </thead><tbody>';
		
	foreach ($pieces as $v) {
		echo"<tr>";
  //  for ($x = 0; $x <= sizeof($x); $x++) {
    $v2 = explode("/", $v);
	foreach($v2 as $v3)
	{
		echo "<td>".$v3."</td>";
	}
	
//} 
echo "<tr>";
}
	echo"<tbody></table>";
}
 post($st);
}

?>