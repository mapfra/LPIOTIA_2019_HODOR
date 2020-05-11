<?php

//
//   GenLib.Php Librairie des fonctions générales
//   ____________________________________________

function QuoteOut($String)
{
// échappement des quotes dans une chaine 
	return mysql_real_escape_string(htmlspecialchars($String));
}

function Virgule2Point($Value)
{
	return (str_replace(",", ".", $Value));
}

function ExecPython() {
	$output = shell_exec('ls -lart');
	return "<pre>$output</pre>";
}

function MkTimeStamp($Value) {
	// cree le ts courant à partir d"une plage horaire
	$Temp = explode(':', $Value);
	if (count($Temp) == 3) 	{
		list($H, $i, $s)  = $Temp;
		$ts = mktime($H, $i, $s, date("m"), date("d"), date("Y"));
		return $ts;
	}
}

function SqlIn($Value, $Type="Text", $Format="")
{
// Formatage pour entrée SQL (pour l'instant les dates seulement)
    $SqlIn = "Null";

	switch ($Type) {

	case "Text":
	$SqlIn = "'" . addslashes($Value) . "'";
	break;	

	case "DateFrench":
	$Temp             = explode('/', $Value);
	if (count($Temp) == 3) 	{
		list($d, $m, $Y)  = $Temp;
		$SqlIn            = "'" . $Y . "-" . $m . "-" . $d . "'";
	}
	break;

	case "DateUS":
		$Temp             = explode('-', $Value);
		if (count($Temp) == 3) 	{
			list($Y, $m, $d)  = $Temp;
			$SqlIn            = "'" . $Y . "-" . $m . "-" . $d . "'";
		}
	break;

	case "Time":
	$Temp             = explode(':', $Value);
	if (count($Temp) == 2) 	{
		list($H, $i)  = $Temp;
		$SqlIn            = "'" . $H . ":" . $i . ":" . "00" . "'";
	}
	break;

	case "Time2":
		$Temp             = explode(':', $Value);
		if (count($Temp) == 3) 	{
			list($H, $i, $s)  = $Temp;
			$SqlIn            = "'" . $H . ":" . $i . ":" . $s . "'";
		}
	break;

	case "Numeric":
	$SqlIn = str_replace(",", ".", $Value);
	break;

	case "Box":
	if(!$Value) {
		$SqlIn = 0;
	} else 	{
		$SqlIn = $Value;
	}
	break;
			
	default:
	$SqlIn = $Value;
	}

	return $SqlIn;	
}

function DecimalFormat($Value, $Digits, $Dec=2, $Default=0)
{
# formatage d'une valeur numérique pour champ formulaire-------------------------
# Value : chaine, Digits : nbr de chiffre total , Dec : nbr de décimales
# Default : Valeur par défault
  $DecimalFormat = str_replace(",", ".", $Value);
  if (!isset($DecimalFormat))       $DecimalFormat = $Default;
  if (!is_numeric($DecimalFormat))  $DecimalFormat = $Default;
  $DecimalFormat = round($DecimalFormat, $Dec);
  $DecimalFormat = number_format($DecimalFormat, $Dec, ',', '');
  if($Dec > 0) ++$Digits;
  $DecimalFormat = str_pad($DecimalFormat, $Digits, " ", STR_PAD_LEFT);  
  return($DecimalFormat);
}

function Initiales__($Chaine) {
#	Initiales d'une chaine
	$Initiales__ = "";
	if (strlen($Chaine) == 0)		return("");
	if (is_numeric($Chaine))		return("");
	$Chaine = str_replace("-", " ", $Chaine);
	$Mots	= explode(' ', $Chaine);
	foreach ($Mots as $Mot) {
		$Initiales__ = $Initiales__ . strtoupper(substr($Mot, 0, 1)) . ".";
	}
	return($Initiales__);
}

// Fin _______________________________________________________________________

?>