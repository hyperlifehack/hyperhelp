<?php
/* funktion welche die im Browser/OS eingestellte Sprache erkennen soll
 * aktuell wird en -> in us umgewandelt
 */
function detectLang($verbose = "")
{
	$setlang = "";
	
	// 1. falls lang mit request oder get kommt, setzten
	if(isset($_REQUEST["lang"]))
	{
		$setlang = $_REQUEST["lang"]; 
	}

	if(isset($_GET["lang"]))
	{
		$setlang = $_GET["lang"];
	}
	
	// 2. falls nicht, use cookie
	if(empty($setlang)){
	
		if (isset($_COOKIE['lang']))
		{
			$lang = $_COOKIE['lang'];
		}
		else
		{
			$acceptedLang = getenv("HTTP_ACCEPT_LANGUAGE");
			$acceptedLang = substr($acceptedLang, 0, 2);
			$set_lang = explode(',', $acceptedLang);
			$lang = $set_lang[0];
			setcookie("lang", $lang);
		}
	}
	else
	{
		$lang = $setlang;
		setcookie("lang", $setlang);
	}
	
	// convert languages
	if($lang == "en")
	{
		$lang = "us";
	}

	if($_COOKIE['lang'] == "en")
	{
		setcookie("lang", "us");
	}

	if(!empty($verbose))
	{
		$acceptedLang = getenv("HTTP_ACCEPT_LANGUAGE");
		echo "ACCEPTED LANG:".$acceptedLang;
		echo '<br>';
		echo "DETECTED LANG:".$lang;
		echo '<br>';
		echo "COOKIE INFO: ".$_COOKIE['lang'];
	}
	
	// if the detected language, is none of the available languages -> set it to english/us default
	if(($lang != "de") && ($lang != "us") && ($lang != "es") && ($lang != "pt") && ($lang != "ru") && ($lang != "cz") && ($lang != "pl")) // && ($lang != "it") && ($lang != "fr")
	{
		$lang = "us";
	}

	return $lang;
}   
?>