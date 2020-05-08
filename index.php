<?php


@session_start();
$rootdir="../";
define("ROOTDIR","../");
//Verbindung zur Datenbank aufnehmen
include("fehlermeldungen.inc.php");
include("../zudb/config.inc.php");
if(!defined("ADMINROOT")){define("ADMINROOT","");}

include_once(ADMINROOT."../files/funktionen.inc.php");
include_once(ADMINROOT."../files/importiere_variablen.inc.php");
include("zugang_okay.inc.php");
include_once(ROOTDIR."shop/shop_einstellungen_und_texte.inc.php");

$dhl_benutzername="";
$meldung_ausgeben="";
$export_text="";
if (isset($_REQUEST['action']))
{

		foreach($_REQUEST['viele_bearbeiten'] as $key_request=>$value_request)
		{
			//echo $key_request."=".$value_request."<BR>";

			$bestellNr=$value_request;

			$was_tun=$_REQUEST['was_tun'];

			$umsatz_SQL="SELECT * FROM ".$db_pfraefix."_warenausgang WHERE bestellNr=$bestellNr ";
			$umsatz_result=mysqli_query($db_mysqli, $umsatz_SQL);
			$gesamtbetrag="0";
			$welche_artikel="";
			$gesamtgewicht="0";

			while($umsatz=mysqli_fetch_array($umsatz_result))
			{
				$gesamtbetrag=$gesamtbetrag+((((($umsatz['netto_preis']*$umsatz['mwst'])/100)+ $umsatz['netto_preis'])*$umsatz['stueck']));
				$welche_artikel.=" , ".$umsatz['stueck']." x ".$umsatz['bezeichnung']." ";

				$gewicht_SQL="SELECT * FROM ".$db_pfraefix."_artikel WHERE artikelNr=$umsatz[artikelNr] ";
				$gewicht_result=mysqli_query($db_mysqli, $gewicht_SQL);
				$gewicht=mysqli_fetch_array($gewicht_result);
				//echo "-".$umsatz['stueck']."-".$umsatz['artikelNr']."-".$gewicht['artikel_gewicht']."-".$gesamtgewicht."-".$bestellNr."<br>";
				$gesamtgewicht=$gesamtgewicht + ($gewicht['artikel_gewicht'] * $umsatz['stueck']);

			}
			//echo $easylog_beleglose_nachnahme_oder_beleghafte_nachnahme."-<br>".$gesamtgewicht."-<br>";
			$gesamtgewicht=$gesamtgewicht/1000;

			$rechnung_SQL2="SELECT * FROM ".$db_pfraefix."_bestellungen WHERE bestellNr='$bestellNr' ";
			$rechnung_result2=mysqli_query($db_mysqli, $rechnung_SQL2);
			$rechnung=mysqli_fetch_array($rechnung_result2);

			$umsatz_SQL="SELECT * FROM ".$db_pfraefix."_warenausgang_bezahlen WHERE bestellNr=$bestellNr ";
			$umsatz_result=mysqli_query($db_mysqli, $umsatz_SQL);
			$umsatz=mysqli_fetch_array($umsatz_result);
			$gesamtbetrag=$gesamtbetrag+$umsatz['bezahlen_preis'];
			$bezahlt_mit_was=$umsatz['bezahlen_name'];
			$umsatz_SQL="SELECT * FROM ".$db_pfraefix."_warenausgang_versand WHERE bestellNr=$bestellNr ";
			$umsatz_result=mysqli_query($db_mysqli, $umsatz_SQL);
			$umsatz=mysqli_fetch_array($umsatz_result);
			$gesamtbetrag=$gesamtbetrag+$umsatz['versandkosten_preis'];
			$gesamtbetrag=round($gesamtbetrag,2);
			$gesamtbetrag=number_format($gesamtbetrag - $rechnung['rabatt_wert'],2);
			$gesamtbetrag=number_format($gesamtbetrag - $rechnung['gutschein_wert'],2);


			$gesammtpreis=$rechnung['gesammtpreis'];
			$gesamtbetrag=number_format($gesammtpreis,2,',', '');



			
				$nachnahme="";
				

				$kdnr=$rechnung['kundenNr'];
				$kunde_sql="SELECT * FROM ".$db_pfraefix."_kunden WHERE kundenNr='$kdnr'";
				$kunde_query=mysqli_query($db_mysqli, $kunde_sql);
				$kunde=mysqli_fetch_array($kunde_query);



				$plz_ort=explode(" ",$kunde['liefer_ort']);
				$plz=$plz_ort[0];
				$ort=str_replace($plz,"",$kunde['liefer_ort']);
				$ort=trim($ort);

				$bemerkungen="";

				$export_text.=$kunde['liefer_firma'].";".$kunde['liefer_name'].";".$kunde['liefer_strasse'].";".$kunde['liefer_adresszusatz_eins']." ".$kunde['liefer_adresszusatz_zwei'].";".$plz.";".$ort.";".$kunde['liefer_land'].";".$kunde['tel'].";".$kunde['email'].";".$kunde['kundenNr'].";;Bestellnr:".$bestellNr.$welche_artikel.";".$gesamtgewicht.";".$nachnahme.";".$kunde['liefer_name']."\n";



		}

}

$farbe="ffffff";
?>
<html>
	<head>
		<meta http-equiv="Content-Language" content="de">
		<title>DHL EasyLog</title>
		<META HTTP-EQUIV="expires" CONTENT="0">
		<META HTTP-EQUIV="pragma" CONTENT="no-cache">
		<link rel="stylesheet" href="style.css" type="text/css">
		<link rel="stylesheet" type="text/css" href="css/sebuttons.css">
	</head>
	<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" topmargin="0">
		<table border="0" cellpadding="0" cellspacing="0" width="700" style="border-collapse: collapse; font-family:Verdana; font-size:11px; color:#000000; font-weight:normal; text-decoration:none;" bordercolor="#111111" height="75">
			<tr>
				<td height="1" width="561" >
					<img height="10" src="../images/space2000.gif" alt="" width="15">
				</td>
			</tr>

			<tr>
				<td height="26" width="561" background="images/blue_mitte.png">
					<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Verdana; font-size:11px; color:#000000; font-weight: normal; text-decoration: none;" bordercolor="#111111"  width="683">
						<tr>
							<td width="399">
								<p class="admin_tab_ueber">DHL EasyLog</p>
							</td>
							<td width="284" valign="baseline"></td>
						</tr>
					</table>
				</td>
				<td height="26" width="25" background="images/blue_rechts.png">
					<img src="../images/space2000.gif" width="24" height="24" alt="">
				</td>
			</tr>

			<tr>
				<td align="left" width="561" height="51" valign="top">



					<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-family:Verdana; font-size:11px; color:#000000; font-weight: normal; text-decoration: none;" bordercolor="#111111" width="680" height="288">
						<tr>
							<td width="20" height="13"></td>
							<td width="660" height="13"></td>
						</tr>
						<tr>
							<td width="20" height="275">&nbsp;</td>
							<td width="660" height="363" valign="top">
								<table border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse" bordercolor="#111111" width="100%" >
									<tr>
										<td width="100%" valign="top">

											<table width="100%" border="1" cellpadding="2" style="border-collapse: collapse" bordercolor="#C0C0C0">
												<?php

													//echo $export_text;
													$zufallscode  = "";
													$moegliche_zeichen  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";

													srand((double) microtime() * 1000000);
													for($i=0;$i<10;$i++)
													{
														$number = rand(0, strlen($moegliche_zeichen));
														$zufallscode .= $moegliche_zeichen[$number];
													}

													$export_text=utf8_decode($export_text);
													$dateiname="temp_bilder/dhl_easylog_".time().$zufallscode.".csv";
													$fp = fopen($dateiname, "w");
													fwrite($fp,$export_text);
													fclose($fp);
													echo "<a target=blank href='$dateiname'><img src='images/buttons/excel-csv.png' border='0' alt='CSV Datei downloaden'></a>";
													
												?>

											</table>

										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>

				</td>
				
				<td align="left" valign="top" background="images/admin_seite_rechts_back.png">
				</td>
			</tr>

			<tr>
				<td height="18" background="images/admin_tabelle_560_unten.png"></td>
				<td height="18" background="images/admin_tabelle_unten_rechts.png"></td>
			</tr>
		
		</table>
	</body>
</html>