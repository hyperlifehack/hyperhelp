<?php
/* Empfänger fix eingetragen -> kann eigentlich nicht als spam missbraucht werden, welcher spammer will nur an eine e-mail spamen? */
$WebsiteName = "WerMachtMitBei.de"; // hier ist das Mail-Script der Website... 

//gib php Fehler und Warnungen aus
error_reporting(E_ALL);
// gib mail-script fehler aus
$result = "";

header('Content-type: text/html; charset=utf-8');
//set Deutschsprachige Umgebung, damit die regul&auml;ren Ausdr&uuml;cke auch Umlaute etc. erkennen
// setlocale(LC_ALL, 'de_DE');
setlocale(LC_ALL, 'us_US');
//Erzeuge einen zuf&auml;llig aussehenden Zugangscode aus dem Datum ...
// $code = chr((date("y") + 7) % 10 + date("m") + 68 + (date("m") % 2) * 32) . chr((date("d") + (date("d") %2)) / 2 + 66 + (37 * (date("d") %2)));

function sendMail($to,$from,$subject,$text)
{
	/* assemble Header */
	$header = "";
	$header .= ('MIME-Version: 1.0'."\r\n");
	$header .= ('Content-type: text/html; charset=utf-8'."\r\n");
	$header .= ('From: <'.$from.'>'."\r\n"); // ."\r\n",<'.$return_path.'>'
	// $header .= ('Bcc: <'.$return_path.'>'."\r\n");
	$header .= ('Message-ID: <'.sha1(microtime()) . '@' . gethostbyaddr($_SERVER['REMOTE_ADDR']) . '>'."\r\n");
	$header .= ('Date: '.date('r')."\r\n");
	$header .= ('X-Mailer: PHP/'.phpversion()."\r\n");
	$header .= ('X-Sender-IP: '.$_SERVER['REMOTE_ADDR']."\r\n");
	// $header_empfaenger = '<'.$empfaenger.'>'."\r\n";

	// debug echo htmlentities("header:".$header);

	/* Verschicken der Mail */
	if(mail($to, $subject, $text, $header)) {
		// Es hat geklappt: Best&auml;tigung ausgeben
		$result = "Mail send successfully!";
	} else {
	// Irgendwas ist schiefgelaufen :-(
		$result = "Failed to send Mail!";
	}

	return $result;
}
?>