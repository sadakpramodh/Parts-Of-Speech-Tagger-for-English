<?php
function finalstatetag($txt){
	$arraysize = 2;
	$user = "root";
	$pass = "";
	$dbase = "postdata";
	$staart = "STAART";
	$restrictmove = 1;
	
	$wi = 0;
	$ti = 0;
	
	$txt = $staart."/".$staart." ".$staart."/".$staart." ".$txt;

	$wordsinline = explode(" ",$txt);
	
	for($i=0;$i<sizeof($wordsinline);$i++){
		$tempstr = explode("/",$wordsinline[$i]);
		$wordcorpus[$i] = $tempstr[0];
		$tagcorpus[$i] = $tempstr[1];
		//echo $wordcorpus[$i+2]."/".$tagcorpus[$i+2]." ";
	}

	// read in rule from cRuleArray, and process each rule
	$corpussize = sizeof($tagcorpus) - 1;
	//read crules from dbase
	mysql_connect(localhost,$user,$pass);
	mysql_select_db($dbase) or die("Unable to connct to database!");
	$query = "SELECT rule FROM rulebase WHERE type='c';";
	$result = mysql_query($query);
	mysql_close();
	for($cnt=0;$cnt<mysql_num_rows($result);++$cnt)
		$crules[$cnt] = mysql_result($result,$cnt);			
	for($i=0;$i<sizeof($crules);$i++){
		$thiscrule = explode(" ",$crules[$i]);	
		$old = $thiscrule[0];
		$new = $thiscrule[1];
		$when = $thiscrule[2];
		
		if(strcmp($when, "NEXTTAG") == 0 || strcmp($when, "NEXT2TAG") == 0 || strcmp($when, "NEXT1OR2TAG") == 0 || strcmp($when, "NEXT1OR2OR3TAG") == 0 || strcmp($when, "PREVTAG") == 0 || strcmp($when, "PREV2TAG") == 0 || strcmp($when, "PREV1OR2TAG") == 0 || strcmp($when, "PREV1OR2OR3TAG") == 0) $tag = $thiscrule[3];
		elseif(strcmp($when, "NEXTWD") == 0 ||strcmp($when, "CURWD") == 0 ||strcmp($when, "NEXT2WD") == 0 ||strcmp($when, "NEXT1OR2WD") == 0 ||strcmp($when, "NEXT1OR2OR3WD") == 0 ||strcmp($when, "PREVWD") == 0 ||strcmp($when, "PREV2WD") == 0 ||strcmp($when, "PREV1OR2WD") == 0 || strcmp($when, "PREV1OR2OR3WD") == 0) $word = $thiscrule[3];
		elseif(strcmp($when, "SURROUNDTAG") == 0){
			$lft = $thiscrule[3];
			$rght = $thiscrule[4];
		}
		elseif(strcmp($when, "PREVBIGRAM") == 0){
			$prev1 = $thiscrule[3];
			$prev2 = $thiscrule[4];
		}
		elseif(strcmp($when, "NEXTBIGRAM") == 0){
			$next1 = $thiscrule[3];
			$next2 = $thiscrule[4];
		}
		elseif(strcmp($when,"LBIGRAM") == 0|| strcmp($when,"WDPREVTAG") == 0){
			$prev1 = $thiscrule[3];
			$word = $thiscrule[4];
		}
		elseif(strcmp($when,"RBIGRAM") == 0 || strcmp($when,"WDNEXTTAG") == 0){
			$word = $thiscrule[3];
			$next1 = $thiscrule[4];
		}
		elseif(strcmp($when,"WDAND2BFR")== 0 || strcmp($when,"WDAND2TAGBFR")== 0){
			$prev2 = $thiscrule[3];
			$word = $thiscrule[4];
		}
		elseif(strcmp($when,"WDAND2AFT")== 0 || strcmp($when,"WDAND2TAGAFT")== 0){
			$next2 = $thiscrule[4];
			$word = $thiscrule[3];
		}

		for ($cnt = 0; $cnt <= $corpussize; ++$cnt){
			$curtag = $tagcorpus[$cnt];
			if(strcmp($curtag, $old) == 0){
				$curwd = $wordcorpus[$cnt];
				$atempstr2 = $curwd." ".$new;
				if(strcmp($when, "SURROUNDTAG") == 0){
					if($cnt < $corpussize && $cnt > 0){
						if(strcmp($lft, $tagcorpus[$cnt - 1]) == 0 && strcmp($rght, $tagcorpus[$cnt + 1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "NEXTTAG") == 0){
					if($cnt < $corpussize){
						if(strcmp(tag,$tagcorpus[$cnt + 1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "CURWD") == 0){
					if(strcmp($word, $wordcorpus[$cnt]) == 0) $tagcorpus[$cnt] = $new;
				}
				elseif(strcmp($when, "NEXTWD") == 0){
					if($cnt < $corpussize){
						if(strcmp($word, $wordcorpus[$cnt + 1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "RBIGRAM") == 0){
					if($cnt < $corpussize){
						if(strcmp($word, $wordcorpus[$cnt]) == 0 && strcmp($next1, $wordcorpus[$cnt+1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "WDNEXTTAG") == 0){
					if($cnt < $corpussize){
						if(strcmp($word, $wordcorpus[$cnt]) == 0 && strcmp($next1, $tagcorpus[$cnt+1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}

				elseif(strcmp($when, "WDAND2AFT") == 0){
					if($cnt < $corpussize-1){
						if(strcmp($word, $wordcorpus[$cnt]) == 0 && strcmp($next2, $wordcorpus[$cnt+2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "WDAND2TAGAFT") == 0){
					if($cnt < $corpussize-1){
						if(strcmp($word, $wordcorpus[$cnt]) == 0 && strcmp($next2, $tagcorpus[$cnt+2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}

				elseif(strcmp($when, "NEXT2TAG") == 0){
					if($cnt < $corpussize - 1){
						if(strcmp($tag, $tagcorpus[$cnt + 2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "NEXT2WD") == 0){
					if($cnt < $corpussize - 1){
						if(strcmp($word, $wordcorpus[$cnt + 2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "NEXTBIGRAM") == 0){
					if($cnt < $corpussize - 1){
						if(strcmp($next1, $tagcorpus[$cnt + 1]) == 0 && strcmp($next2, $tagcorpus[$cnt + 2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "NEXT1OR2TAG") == 0){
					if($cnt < $corpussize){
						if($cnt < $corpussize-1) $tempcnt1 = $cnt+2;
						else $tempcnt1 = $cnt+1;
						if(strcmp($tag, $tagcorpus[$cnt + 1]) == 0 || strcmp($tag, $tagcorpus[$tempcnt1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "NEXT1OR2WD") == 0){
					if($cnt < $corpussize){
						if($cnt < $corpussize-1) $tempcnt1 = $cnt+2;
						else $tempcnt1 = $cnt+1;
						if (strcmp($word, $wordcorpus[$cnt + 1]) == 0 || strcmp($word, $wordcorpus[$tempcnt1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "NEXT1OR2OR3TAG") == 0){
					if($cnt < $corpussize){
						if($cnt < $corpussize -1) $tempcnt1 = $cnt+2;
						else $tempcnt1 = $cnt+1;
						if($cnt < $corpussize-2) $tempcnt2 = $cnt+3;
						else $tempcnt2 =$cnt+1;
						if(strcmp($tag, $tagcorpus[$cnt + 1]) == 0 || strcmp($tag, $tagcorpus[$tempcnt1]) == 0 || strcmp($tag, $tagcorpus[$tempcnt2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "NEXT1OR2OR3WD") == 0){
					if($cnt < $corpussize){
						if($cnt < $corpussize -1) $tempcnt1 = $cnt+2;
						else $tempcnt1 = $cnt+1;
						if($cnt < $corpussize-2) $tempcnt2 = $cnt+3;
						else $tempcnt2 =$cnt+1;
						if(strcmp($word, $wordcorpus[$cnt + 1]) == 0 || strcmp($word, $wordcorpus[$tempcnt1]) == 0 || strcmp($word, $wordcorpus[$tempcnt2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "PREVTAG") == 0){
					if($cnt > 0){
						if(strcmp($tag, $tagcorpus[$cnt - 1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "PREVWD") == 0){
					if($cnt > 0){
						if(strcmp($word, $wordcorpus[$cnt - 1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "LBIGRAM") == 0){
					if($cnt > 0){
						if(strcmp($word, $wordcorpus[$cnt]) == 0 && strcmp($prev1, $wordcorpus[$cnt-1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "WDPREVTAG") == 0){
					if($cnt > 0){
						if(strcmp($word, $wordcorpus[$cnt]) == 0 && strcmp($prev1, $tagcorpus[$cnt-1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "WDAND2BFR") == 0){
					if($cnt > 1){
						if(strcmp($word, $wordcorpus[$cnt]) == 0 && strcmp($prev2, $wordcorpus[$cnt-2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "WDAND2TAGBFR") == 0){
					if($cnt > 1){
						if(strcmp($word, $wordcorpus[$cnt]) == 0 && strcmp($prev2, $tagcorpus[$cnt-2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}

				elseif(strcmp($when, "PREV2TAG") == 0){
					if($cnt > 1){
						if(strcmp($tag, $tagcorpus[$cnt - 2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "PREV2WD") == 0){
					if($cnt > 1){
						if(strcmp($word, $wordcorpus[$cnt - 2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "PREV1OR2TAG") == 0){
					if($cnt > 0){
						if($cnt > 1) $tempcnt1 = $cnt-2;
						else $tempcnt1 = $cnt-1;
						if(strcmp($tag, $tagcorpus[$cnt - 1]) == 0 || strcmp($tag, $tagcorpus[$tempcnt1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "PREV1OR2WD") == 0){
					if($cnt > 0){
						if($cnt > 1) $tempcnt1 = $cnt-2;
						else $tempcnt1 = $cnt-1;
						if(strcmp($word, $wordcorpus[$cnt - 1]) == 0 || strcmp($word, $wordcorpus[$tempcnt1]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "PREV1OR2OR3TAG") == 0){
					if($cnt > 0){
						if($cnt>1) $tempcnt1 = $cnt-2;
						else $tempcnt1 = $cnt-1;
						if($cnt >2) $tempcnt2 = $cnt-3;
						else $tempcnt2 = $cnt-1;
						if(strcmp($tag, $tagcorpus[$cnt - 1]) == 0 || strcmp($tag, $tagcorpus[$tempcnt1]) == 0 || strcmp($tag, $tagcorpus[$tempcnt2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "PREV1OR2OR3WD") == 0){
					if($cnt > 0){
						if($cnt>1) $tempcnt1 = $cnt-2;
						else $tempcnt1 = $cnt-1;
						if($cnt >2) $tempcnt2 = $cnt-3;
						else $tempcnt2 = $cnt-1;
						if(strcmp($word, $wordcorpus[$cnt - 1]) == 0 || strcmp($word, $wordcorpus[$tempcnt1]) == 0 || strcmp($word, $wordcorpus[$tempcnt2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				elseif(strcmp($when, "PREVBIGRAM") == 0){
					if($cnt > 1){
						if(strcmp($prev2, $tagcorpus[$cnt - 1]) == 0 && strcmp($prev1, $tagcorpus[$cnt - 2]) == 0) $tagcorpus[$cnt] = $new;
					}
				}
				else echo "ERROR: $when is not an allowable transform type<br>";
			}
		}  
	}
	
	$rettxt = $wordcorpus[2]."/".$tagcorpus[2]." ";
	for($i=3;$i<=$corpussize;++$i){
		$rettxt = $rettxt.$wordcorpus[$i]."/".$tagcorpus[$i]." ";
	}
	//echo $rettxt;
	return $rettxt;
/*	i=0;
	bufp = buf[i];
	bufp[0]=0;

	for ($cnt = 0; $cnt <= $corpussize; ++$cnt){
	strcpy(tempstr, $tagcorpus[$cnt]);
	if(strcmp(tempstr,"STAART")==0 &&
	strcmp($tagcorpus[$cnt + 1],"STAART")==0 &&
	$cnt){
	++i;
	bufp = buf[i];
	bufp[0]=0;
	} elseif(strcmp(tempstr,"STAART")){
	//Added by Golam Mortuza Hossain 
	strcpy(my_$word, $wordcorpus[$cnt]);
	strcpy(my_tag, tempstr);
	my_ptr = Registry_get(lemma_hash, my_$word) ;
	if( my_ptr ) strcpy ( my_lemma, my_ptr);
	else
	{
	strcpy(my_$word_lc, my_$word); mylc(my_$word_lc) ;
	my_ptr = Registry_get(lemma_hash, my_$word_lc) ;

	if( my_ptr ) strcpy ( my_lemma, my_ptr);
	else	strcpy ( my_lemma, "<unknown>" ) ;
	}

	if( enhance_penntag )
	{
	my_ptr =
	EnhancePennTag( my_line, my_$word, my_tag, my_lemma) ;
	bufp = strcat(bufp, my_ptr);
	}
	else
	{
	bufp = strcat(bufp, my_$word);
	bufp = strcat(bufp, "/");
	bufp = strcat(bufp, my_tag);
	bufp = strcat(bufp, " ");

	}
	//g.m.h 

	}
	}

	// Benjamin Han: Gee... these puppies need to be freed...
	for (i=0;i<arraySize;i++)
	{
	if($wordcorpus[i]!=staart) free($wordcorpus[i]);
	if($tagcorpus[i]!=staart && $tagcorpus[i]!=$new)
	free($tagcorpus[i]);
	}
	free($wordcorpus);
	free($tagcorpus);

	return buf;
*/
	//mysql_close();
}
?>