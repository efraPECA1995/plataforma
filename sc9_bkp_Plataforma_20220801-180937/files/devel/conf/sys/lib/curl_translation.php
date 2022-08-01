<?php
//__NM__Translation cURL from google__NM__FUNCTION__NM__//
 
function translate($word,$from = "en",$to = "pt_br")
{
$word = urlencode($word);


$url = 'http://translate.google.com/translate_a/t?client=t&text='.$word.'&hl='.$to.'&sl='.$from.'&tl='.$to.'&ie=UTF-8&oe=UTF-8&multires=1&otf=1&pc=1&trs=1&ssel=3&tsel=6&sc=1';
$name = file_get_contents($url);
$name = explode(']]',$name);
$name = substr($name[0], 4);
$name = explode('"],["',$name);
$return = '';
foreach ($name as $text)
{
	$text = explode('","',$text);
$return .= $text[0];
	$c = 0;
	/*while ($c < count($text)-1)
	{
		$return .= $text[$c];
		$return .= "\"";
		$c++;
	}*/
}

$return = str_replace("\\\\", "___#SLASH_SEPARATOR#___", $return);
$return = str_replace("\\\"", "\"", $return);
$return = str_replace("___#SLASH_SEPARATOR#___" , "\\", $return);
$return = str_replace("\ \"", "\\\"", $return);

$return = str_replace("'" , "\\'", $return);
	
return $return;

}
?>