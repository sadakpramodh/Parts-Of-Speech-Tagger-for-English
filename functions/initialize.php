<?php
function initialize(){

	global $lexrulesright, $lexrulesleft, $bigramarray;
	
	$lexrulesright = array("$" => "","would" => "","be" => "","it" => "","n't" => "",
	"the" => "","he" => "","he" => "","are","The" => "","he" => "","Mr." => "","so" => "",
	"which" => "","been" => "","a" => "","can" => "","the" => "","S-T-A-R-T" => "","very",
	"It" => "","but" => "","costs" => "","negative" => "","Engelken" => "");
	
	$lexrulesleft = array("was" => "","is" => "","Co." => "","million" => "","their" => "",
	"of" => "","economic" => "","'s" => "","Inc." => "","be" => "","people" => "","have" => "",
	"may" => "","them" => "","were" => "","but" => "","ways" => "");
	
	$bigramarray = array("NOOTHING NOOTHING");
	//lexicon and rules are loaded into databases during installation.

}
?>