<?php
function tokenizer($txt){
	$txt = $txt." ";
	$token_list = array("\"", " \" ",",", " , ",";", " ; ",":", " : ","? ", " ? ","! ", " ! ",
		". ", " .","[", " [ ","]", " ] "," (", " ( ",") ", " ) ",").", " ).","<", " < ",
		">", " > ","--", " -- ","'s ", " 's ","'S ", " 'S ","'m ", " 'm ","'M ", " 'M ",
		"'d ", " 'd ","'D ", " 'D ","'ll ", " 'll ","'re ", " 're ","'ve ", " 've ",
		" can't ", " can n't "," Can't ", " Can n't ","n't ", " n't ","'LL ", " 'LL ",
		"'RE ", " 'RE ","'VE ", " 'VE ","N'T ", " N'T "," Cannot ", " Can not ",
		" cannot ", " can not "," D'ye ", " D' ye "," d'ye ", " d' ye ",
		" Gimme ", " Gim me "," gimme ", " gim me "," Gonna ", " Gon na ",
		" gonna ", " gon na "," Gotta ", " Got ta "," gotta ", " got ta ",
		" Lemme ", " Lem me "," lemme ", " lem me "," More'n ", " More 'n ",
		" more'n ", " more 'n ","'Tis ", " 'T is ","'tis ", " 't is ","'Twas ", " 'T was ",
		"'twas ", " 't was "," Wanna ", " Wan na "," wanna ", " wanna ",

		"Let 's ", "Let's ",".. .", " ...","Adm .", "Adm.","Aug .", "Aug.","Ave .", "Ave.",
		"Brig .", "Brig.","Bros .", "Bros.","CO .", "CO.", "CORP .", "CORP.","COS .", "COS.", 
		"Capt .", "Capt.","Co .", "Co.","Col .", "Col.","Colo .", "Colo.","Corp .", "Corp.",
		"Cos .", "Cos.","Dec .", "Dec.","Del .", "Del.","Dept .", "Dept.","Dr .", "Dr.",
		"Drs .", "Drs.","Etc .", "Etc.","Feb .", "Feb.","Ft .", "Ft.","Ga .", "Ga.",
		"Gen .", "Gen.","Gov .", "Gov.","Hon .", "Hon.","INC .", "INC.","Inc .", "Inc.",
		"Ind .", "Ind.","Jan .", "Jan.","Jr .", "Jr.","Kan .", "Kan.","Ky .", "Ky.",
		"La .", "La.","Lt .", "Lt.","Ltd .", "Ltd.","Maj .", "Maj.","Md .", "Md.",
		"Messrs .", "Messrs.","Mfg .", "Mfg.","Miss .", "Miss.","Mo .", "Mo.",
		"Mr .", "Mr.","Mrs .", "Mrs.","Ms .", "Ms.","Nev .", "Nev.","No .", "No.",
		"Nos .", "Nos.","Nov .", "Nov.","Oct .", "Oct.","Ph .", "Ph.","Prof .", "Prof.",
		"Prop .", "Prop.","Pty .", "Pty.","Rep .", "Rep.","Reps .", "Reps.","Rev .", "Rev.",
		"S.p.A .", "S.p.A.","Sen .", "Sen.","Sens .", "Sens.","Sept .", "Sept.","Sgt .", "Sgt.",
		"Sr .", "Sr.","St .", "St.","Va .", "Va.","Vt .", "Vt.","U.S .", "U.S.","Wyo .", "Wyo.",
		"a.k.a .", "a.k.a.","a.m .", "a.m.","cap .", "cap.","e.g .", "e.g.","eg .", "eg.",
		"etc .", "etc.","ft .", "ft.","i.e .", "i.e.","p.m .", "p.m.","v .", "v.",
		"v.B .", "v.B.","v.w .", "v.w.","vs .", "vs.","__END__", "__END__");
	$i = 0;
	for($i = 0; $i < 150; $i++){
		$txt = str_replace($token_list[(2*$i)], $token_list[(2*$i)+1], $txt);
	}
	//echo $txt;
	return $txt;
}
?>