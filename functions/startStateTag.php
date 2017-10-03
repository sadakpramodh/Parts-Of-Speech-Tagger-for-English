<?php
function startstatetag($txt){
	global $lexrulesright, $lexrulesleft, $bigramarray;
	
	$user = "root";
	$pass = "";
	$dbase = "postdata";
	
	$words = explode(" ",$txt);
	
	mysql_connect(localhost,$user,$pass);
	@mysql_select_db($dbase) or die( "Error connecting to Lexicon database!");		
	$max = sizeof($words);
	for($i=0;$i<$max;$i++){
		//$words[$i] = mysql_real_escape_string($words[$i]);
		$query = "SELECT pos FROM lexicon WHERE word='".mysql_real_escape_string($words[$i])."' COLLATE latin1_general_cs;";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		if(!$row[0]){
			if(!$ntothash[$words[$i]]){
				$ntothash[$words[$i]] = "";
				$ntotkeys[$i] = $words[$i];
			}
			if($i != 0 && $i!= $max-1){
				$bigramspace = $words[$i]." ".$words[$i+1];
				$bigramhash[$bigramspace] = "";
				$bigramspace = $words[$i-1]." ".$words[$i];
				$bigramhash[$bigramspace] = "";
			}
			elseif($i != $max-1){
				$bigramspace = $words[$i]." ".$words[$i+1];
				$bigramhash[$bigramspace] = "";
			}
			elseif($i != 0){
				$bigramspace = $words[$i-1]." ".$words[$i];
				$bigramhash[$bigramspace] = "";
			}
		}
	}
	mysql_close();
	
	if($ntotkeys) $ntotkeys = array_values($ntotkeys);

/*	for($j=0;$j<sizeof($bigramarray);$j++){
		$bigram = explode(" ",$bigramarray[$j]);
		echo $bigram[0]."|".$bigram[1];
		if($lexrulesright[$bigram[0]] && $ntothash[$bigram[1]]){
			$bigramspace = $bigram[0]." ".$bigram[1];
			$bigramhash[$bigramspace] = "";
		}	
		if($lexrulesleft[$bigram[1]] && $ntothash[$bigram[0]]){
			$bigramspace = $bigram[0]." ".$bigram[1];
			$bigramhash[$bigramspace] = "";
		}
	}
*/	
	// start of actual startstate algorithm
	
	$noun = "NN";
	$proper = "NNP";
	$number = "CD";
	
	for($cnt=0;$cnt<sizeof($ntothash);++$cnt){
		if(ord($ntotkeys[$cnt]) > 47  && ord($ntotkeys[$cnt]) < 59)
			$ntothash[$ntotkeys[$cnt]] = $number;
		elseif(ord($ntotkeys[$cnt]) > 64 && ord($ntotkeys[$cnt]) < 91)
			$ntothash[$ntotkeys[$cnt]] = $proper;
		else
			$ntothash[$ntotkeys[$cnt]] = $noun;
	}
	
	// startstate algorithm ends

	//load lex rulebase
	mysql_connect(localhost,$user,$pass);
	mysql_select_db($dbase) or die("Unable to connct to database!");
	$query = "SELECT rule FROM rulebase WHERE type='l';";
	$result = mysql_query($query);
	for($cnt=0;$cnt<mysql_num_rows($result);++$cnt)
		$lrules[$cnt] = mysql_result($result,$cnt);			
	for($cnt=0;$cnt<mysql_num_rows($result);++$cnt){
		$therule = explode(" ",$lrules[$cnt]);
		$rulesize = sizeof($therule) - 1;
		$ntotsize = sizeof($ntotkeys);
		
		if(strcmp($therule[1],"char") == 0){
			for($cnt2=0;$cnt<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]], $therule[$rulesize-1]) != 0){
					if(strpbrk($ntotkeys[$cnt2],$therule[0])){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}			
		}
		elseif(strcmp($therule[2],"fchar") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]], $therule[0]) == 0){
					if(strpbrk($ntotkeys[$cnt2],$therule[1])){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[1],"deletepref") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]], $therule[$rulesize-1]) != 0){
					$tempstr = $ntotkeys[$cnt2];
					for($cnt3=0;$cnt3<(int)$therule[2];++$cnt3){
						if(substr($tempstr,$cnt3,1) != substr($therule[0],$cnt3,1)) break;
					}
					if($cnt3 == (int)$therule[2]){
						$tempstr += (int)$therule[2];
						//$tempstr = mysql_real_escape_string($tempstr);
						$query = "SELECT pos FROM lexicon WHERE word='".mysql_real_escape_string($tempstr)."' COLLATE latin1_general_cs;";
						$result = mysql_query($query);
						$row = mysql_fetch_row($result);
						if($row[0]){
							$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
						}
					}
				}
			}
		}
		elseif(strcmp($therule[2],"fdeletepref") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt]], $therule[0]) == 0){
					$tempstr = $ntotkeys[$cnt2];
					for($cnt3=0;$cnt3<(int)$therule[3];++$cnt){
						if(substr($tempstr,$cnt3,1) != substr($therule[1],$cnt3,1)) break;
					}
					if($cnt3 == (int)$therule[3]){
						$tempstr += (int)$therule[3];
						//$tempstr = mysql_real_escape_string($tempstr);
						$query = "SELECT pos FROM lexicon WHERE word='".mysql_real_escape_string($tempstr)."' COLLATE latin1_general_cs;";
						$result = mysql_query($query);
						$row = mysql_fetch_row($result);
						if($row[0]){
							$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
						}				
					}
				}
			}
		}
		elseif(strcmp($therule[1],"haspref") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]], $therule[$rulesize-1]) != 0){
					$tempstr = $ntotkeys[$cnt2];
					for($cnt3=0;$cnt3<(int)$therule[2];++$cnt3){
						if(substr($tempstr,$cnt3,1) != substr($therule[0],$cnt3,1)) break;
					}
					if($cnt3 == (int)$therule[2]){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[2],"fhaspref") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]], $therule[0]) == 0){
					$tempstr = $ntotkeys[$cnt2];
					for($cnt3=0;$cnt3<(int)$therule[3];++$cnt3){
						if(substr($tempstr,$cnt3,1) != substr($therule[1],$cnt3,1)) break;						
					}
					if($cnt3 == (int)$therule[3]){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[1],"deletesuf") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt]],$therule[$rulesize-1]) != 0){
					$tempstr = $ntotkeys[$cnt2];
					$tempcount = strlen($tempstr) - (int)$therule[2];
					for($cnt3=$tempcount;$cnt3<strlen($tempstr);++$cnt3){
  						if(substr($tempstr,$cnt3,1) != substr($therule[0],$cnt3-$tempcount,1)) break;
  					}
					if($cnt3 == strlen($tempstr)){
						//$tempstr = mysql_real_escape_string($tempstr);
						$query = "SELECT pos FROM lexicon WHERE word='".mysql_real_escape_string($tempstr)."' COLLATE latin1_general_cs;";
						$result = mysql_query($query);
						$row = mysql_fetch_row($result);
						if($row[0]){
							$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
  						}
					}
				}
			}
		}
		elseif(strcmp($therule[2],"fdeletesuf") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]],$therule[0]) == 0){
					$tempstr = $ntotkeys[$cnt2];
					$tempcount = strlen($tempstr) - (int)$therule[3];
					for($cnt3=$tempcount;$cnt3<strlen($tempstr);++$cnt3){
						if(substr($tempstr,$cnt3,1) != substr($therule[1],$cnt3-$tempcount,1)) break;
					}
					if($cnt3 == strlen($tempstr)){
						//$tempstr = mysql_real_escape_string($tempstr);
						$query = "SELECT pos FROM lexicon WHERE word='".mysql_real_escape_string($tempstr)."' COLLATE latin1_general_cs;";
						$result = mysql_query($query);
						$row = mysql_fetch_row($result);
						if($row[0]){
							$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
						}
					}
				}
			}
		}
		elseif(strcmp($therule[1],"hassuf") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]],$therule[$rulesize-1]) != 0){
					$tempstr = $ntotkeys[$cnt2];
					$tempcount = strlen($tempstr) - (int)$therule[2];
					for($cnt3=$tempcount;$cnt3<strlen($tempstr);++$cnt3){
						if(substr($tempstr,$cnt3,1) != substr($therule[0],$cnt3-$tempcount,1)) break;
					}
					if($cnt3 == strlen($tempstr)) {
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[2],"fhassuf") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]],$therule[0]) == 0){
					$tempstr = $ntotkeys[$cnt2];
					$tempcount = strlen($tempstr) - (int)$therule[3];
					for($cnt3=$tempcount;$cnt3<strlen($tempstr);++$cnt3){
						if(substr($tempstr,$cnt3,1) != substr($therule[1],$cnt3-$tempcount,1)) break;
					}
					if($cnt3 == strlen($tempstr)){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[1],"addpref") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]],$therule[$rulesize-1]) == 0){
					$tempstr_space = $therule[0].$ntotkeys[$cnt2];
					//$tempstr_space = mysql_real_escape_string($tempstr_space);
					$query = "SELECT pos FROM lexicon WHERE word='".mysql_real_escape_string($tempstr_space)."' COLLATE latin1_general_cs;";
					$result = mysql_query($query);
					$row = mysql_fetch_row($result);
					if($row[0]){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[2],"faddpref") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]],$therule[$rulesize-1]) == 0){
					$tempstr_space = $therule[1].$ntotkeys[$cnt2];
					//$tempstr_space = mysql_real_escape_string($tempstr_space);
					$query = "SELECT pos FROM lexicon WHERE word='".mysql_real_escape_string($tempstr_space)."' COLLATE latin1_general_cs;";
					$result = mysql_query($query);
					$row = mysql_fetch_row($result);
					if($row[0]){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[1],"addsuf") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]],$therule[$rulesize-1]) != 0){
					$tempstr_space = $ntotkeys[$cnt2].$therule[0];
					//$tempstr_space = mysql_real_escape_string($tempstr_space);
					$query = "SELECT pos FROM lexicon WHERE word='".mysql_real_escape_string($tempstr_space)."' COLLATE latin1_general_cs;";
					$result = mysql_query($query);
					$row = mysql_fetch_row($result);
					if($row[0]){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[2],"faddsuf") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]],$therule[0]) == 0){
					$tempstr_space = $ntotkeys[$cnt2].$therule[1];
					//$tempstr_space = mysql_real_escape_string($tempstr_space);
					$query = "SELECT pos FROM lexicon WHERE word='".mysql_real_escape_string($tempstr_space)."' COLLATE latin1_general_cs;";
					$result = mysql_query($query);
					$row = mysql_fetch_row($result);
					if($row[0]){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[1],"goodleft") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt]],$therule[$rulesize-1]) != 0){
					$bigram_space = $ntotkeys[$cnt2]." ".$therule[0];
					if(hash_get($bigramhash,$bigram_space)){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[2],"fgoodleft") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntotkeys[$cnt2],$therule[0]) == 0){
					$bigram_space = $ntotkeys[$cnt2]." ".$therule[1];
					if(hash_get($bigramhash,$bigram_space)){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[1],"goodright") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]],$therule[$rulesize-1]) != 0){
					$bigram_space = $therule[0]." ".$ntotkeys[$cnt2];
					if(hash_get($bigramhash,$bigram_space)){
						$ntothash[$ntotkeys[$cnt2]] = $therule[$rulesize-1];
					}
				}
			}
		}
		elseif(strcmp($therule[2],"fgoodright") == 0){
			for($cnt2=0;$cnt2<$ntotsize;++$cnt2){
				if(strcmp($ntothash[$ntotkeys[$cnt2]],$therule[0]) == 0){
					$bigram_space = $therule[1]." ".$ntotkeys[$cnt2];
					if(hash_get($bigramhash,$bigram_space)){
						$ntothash[$ntotkeys[$cnt]] = $therule[$rulesize-1];
					}
				}
			}
		}

	}
	$sent = explode(" ",$txt);
	for($i=0;$i<sizeof($sent);$i++){
		//$sent[$i] = mysql_real_escape_string($sent[$i]);
		$query = "SELECT pos FROM lexicon WHERE word='".mysql_real_escape_string($sent[$i])."' COLLATE latin1_general_cs;";
		$result = mysql_query($query);
		$row = mysql_fetch_row($result);
		if($row[0]){
			$tags = explode(" ",$row[0],2);
			$sent[$i] = $sent[$i]."/".$tags[0];
		}
		else{
			$sent[$i] = $sent[$i]."/".hash_get($ntothash,$sent[$i]);
		}
	}		
	mysql_close();
	$rettxt = $sent[0];
	
	for($i=1;$i<sizeof($sent);$i++) $rettxt = $rettxt." ".$sent[$i];

	return $rettxt;
}
?>