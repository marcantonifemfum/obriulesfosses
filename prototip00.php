<?php
echo '
<link rel="icon" href="https://www.obriulesfosses.cat/Fvermella.png" type="image/x-icon" />
<meta http-equiv="content-language" content="ca" />
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
';

// creem una galeta JS de l'hora del client
//echo "<script>var _quinHoraEs = new Date().toLocaleTimeString();setCookie('quinHoraEs', _quinHoraEs, 1);</script>";

if ($_SERVER['REQUEST_METHOD'] === 'GET')
{  // Si fem GET: passem el valor de les variables dins la mateixa URL
 // exit('GET!');

 // esborrem les anteriors per si les mosques?
 putenv('laX=');
 putenv('laY=');
 putenv('laH=');

 // capturem les variables de la URL
 // minimitzem el trànsit de dades a través de variables d'entorn i així ens estalviem d'escriure les dades a disc
 // de manera que, un cop capturades les variables de la URL, en fem un putenv de cadascuna, per després
 // llegir-les de dins el .ps a través de l'operador getenv del GS

 $width = $_GET['laX'];
 $height = $_GET['laY'];
 $qhe = $_GET['laH'];
}
else
{  // Si fem POST: les variables vindran triades des d'index_##.php o index.html
 exit('no fem POST ara!');
}

echo(" >>>> ".$width . " x " . $height . " <br> " . $qhe);

//amb l'hora del servidor en tenim prou com a nom únic?
$PDFunic = date("d"."B"."H"."i"."s");

// EP! aquí cal veure si al servidor de bTactic podem posar-hi o no les 3 www al davant
$baseURL = "http://localhost/www.obriulesfosses.cat/exhumeu/";  // localhost de Tuxedo
//$baseURL = "https://www.obriulesfosses.cat/exhumeu/";  // servidor bTactic

// adreça absoluta al directori dels PDFs resultants
$somaPDF = "/var/www/html/www.obriulesfosses.cat/exhumeu/pdfs/";  // localhost Tuxedo
//$somaPDF = "/wub/tallerdetipografia/caixista/pdfs/";  // EP! el GS del servidor bTactic no li agrada escriure sobre una adreça absoluta?

// adreça absoluta al directori dels HTMLs resultants
$somaHTML = "/var/www/html/www.obriulesfosses.cat/exhumeu/htmls/";  // localhost Tuxedo
//$somaHTML = "/wub/tallerdetipografia/caixista/pdfs/";  // EP! el GS del servidor bTactic no li agrada escriure sobre una adreça absoluta?

$somaGS = "/usr/bin/";  // path a l'executable de Ghostscript al localhost de Tuxedo
//$somaGS = "/usr/local/bin/";  // path a l'executable de Ghostscript al servidor de la UB

$baseurlPDF = "http://localhost/www.obriulesfosses.cat/exhumeu/pdfs/";  // base url al pdf al localhost de Tuxedo
//$baseurlPDF = "https://www.ub.edu/tallerdetipografia/caixista/pdfs/";  // base url al pdf del servidor de la UB

$PSapplet = "faComarques_faHTMLobriulesfosses_prototip00.ps";

// i la cridarem després de l'execució
$pdfnomes = $PDFunic . "_obriulesfosses_prototip00.pdf";

$pdfFile = $somaPDF . $pdfnomes;  // al localhost del Tuxedo podem treballar amb l'adreça absoluta
//$pdfFile = "pdfs/" . $pdfnomes;  // al servidor de bTactic hem de treballar obligatòriament amb adreces relatives?

//@EP aquí desem $PDFunic com a variable d'entorn per tal de capturar-la, si calgués, p.e. per desar-hi un HTML amb les dades de composició
putenv("MRCT_PDFunic=$PDFunic");  // desem el numèric únic a la variable d'entorn

// variables definides dins la URL
putenv("MRCT_width=$width");  // ample de composició de la imatge ídem a la pantalla del client
putenv("MRCT_height=$height");  // alt de composició de la imatge ídem a la pantalla del client
putenv("MRCT_qhe=$qhe");  // l'hora del client

// crida a partir de la versió GS 9.55 al localhost del MacBookAir
//$command = $somaGS . "gs -q -dNOSAFER -o '" . $pdfFile . "' -dALLOWPSTRANSPARENCY -sDEVICE=pdfwrite -dAutoRotatePages=/None -dNEWPDF=false  -f '" . $PSapplet . "'";
// @EP aquesta pel gs 9.55 localhost de Tuxedo
$command = $somaGS . "gs -q -dNOSAFER -o '" . $pdfFile . "' -dALLOWPSTRANSPARENCY -sDEVICE=pdfwrite -dAutoRotatePages=/None -f '" . $PSapplet . "'";
// el servidor de bTactic té un GPL Ghostscript 10.0.0 (2022-09-21)
// https://obriulesfosses.cat/siPHPcridaGS.php
// exit($command);

// en cas d'error, aquest és un mètode per capturar el prompt i presentar-lo embolicat d'html
ob_start();
$LaDarrera = passthru($command, $ElQtorna);  // $LaDarrera queda buida
$prompt = ob_get_contents();
ob_end_clean();

// demostra com treballa amb la captura de la darrera linia i el parametre de tornada
// echo '</p><hr />La darrera Linia: ' . $LaDarrera . '<hr />El que ens torna: ' . $ElQtorna;
// $LaDarrera és la darrera línia del prompt
// anàlisi d' $ElQtorna
// si torna 127 és que el gs no s'ha executat?
// si torna 1 és que hi ha hagut un ofendingcommand (error a l'execució del ps) o el directori on escriure el PDF no existeix o no té permisos
// o hi hagi un error al nom o el fitxer .ps a interpretar no tingui prous permisos (oju amb el chmod), també genera un valor de 1!
// si torna 0 és que el .ps s'ha executat sense errors

//@SOM aquí
//exit($ElQtorna . " ...que ha fet?");

// com avaluem si s'ha generat el pdf correctament?
if ($ElQtorna == 127)
{  // el gs NO s'ha executat
// exit("...sembla que el GS no s'ha executat");
 echo "<center><span style='color:#ff0000;font-family:monospace;font-size:24px'><br><br>&gt;&gt;&gt; l'int&egrave;rpret Ghostscript no s'ha executat &lt;&lt;&lt;</span>";
//@@OBRIULESFOSSES
 exit("<br><p><br><p><span style='color:#ff0000;font-family:monospace;font-size:24px'><a style='color:#ff0000;font-family:monospace;font-size:24px' href='mailto:onetsoncleguillem@gmail.com'>podeu documentar-nos l'error via email? (copieu i enganxeu el text en gris) gr&agrave;cies!</a><br><br><a style='color:#ff0000;font-family:monospace;font-size:18px' href='https://www.obriulesfosses.cat'>obriulesfosses.cat</a></center>");

echo "</body></html>";
}
{
 //      if (file_exists($pdfFile))
 if ($ElQtorna == 0)
 {
  if ($mapai == 7)  // assagem?
  {
   //echo ($prompt);  // el prompt del .ps llistat pel gs
   //exit("...sembla que ho ha fet bé!");

   //@RIMDAUB localhost
   // aquí llistem el mapa d'imposició com si fos el prompt del Terminal
   echo "<center><span style='color:#ff0000;font-family:monospace;font-size:24px'><br><br>&gt;&gt;&gt; ASSAIG DEL MAPA D'IMPOSICI&Oacute; &lt;&lt;&lt;</span>";
//   echo "<br><br><div w3-include-html='" . $baseurlMAPA . $PDFunic . ".html' style='color:#999999;font-family:monospace;font-size:24px'></div>";
   echo "<br><br><iframe src='" . $baseurlMAPA . $PDFunic . ".html'  height='100%' width='75%' title='' style='border:none' ></iframe>";
   exit("<br><p><br><p><span style='color:#0000ff;font-family:monospace;font-size:24px'><a style='color:#0000ff;font-family:monospace;font-size:18px' href='$baseURL'>Si torneu enrera per aquest vincle perdereu les opcions de men&uacute; triades, si ho feu amb el bot&oacute; del navegador les conservareu</a></span><br><p><br></center>");
  }
  else
  {
   //exit("HTML o PDF?");

// forcem la descàrrega d'un PDF
//header('Content-type: application/pdf, text/html');
// nom del pdf per anomenar i desar
//header('Content-Disposition: attachment; filename=Fatarella18_ca.pdf');
// url
//readfile('http://femfum.com/OnEtsOncleGuillem/Fatarella18_ca.pdf');

  // forcem l'obertura del pdf a la mateixa finestra
  // header("Location:" . $baseurlPDF . $pdfnomes);  // no li agrada a SomNuvol !

// això és com un alert però més controlat per tal de redirigir un resultat cap a on es vulgui
	  //$pr0mpt = rtrim($prompt);
	  //FALS PROMPT
          $pr0mpt = "QUÈ VOLS QUE ET DIGUI?";

// què caracu es desa quan llistem l'stack del ps?
//var_dump($pr0mpt);
//exit('  <<<< COMPINTA?');

         // exit("QUEFA? ".$pr0mpt);
         //$pr1mpt = ltrim($pr0mpt);  // ltrim sembla que no li cal

 // exit($prompt);
 //echo "<script>alert('" . "$pr0mpt" . "');</script>";

          // assagem via recodificat?
          //$pr3mpt = mb_convert_encoding($prompt, "ISO-8859-1", "auto");
          // echo "<script>alert('" . "$pr3mpt" . "');</script>";
          //exit ('mb_internal_encoding($prompt)');

// si cancel·lem ens en anem aquí * i si fem OK anem cap al PDF que s'ha generat amb un nom únic
echo "<script>if (window.confirm('" . $pr0mpt . "')) {window.location.href='$baseurlPDF$pdfnomes';};</script>";

  }

// esborrem tots els fitxers del directori pdfs que tinguin més de 24 hores
  $path="pdfs";
  if (is_dir("$path") )
  {
   $manegal=opendir($path);
   while (false!==($file = readdir($manegal)))
   {
    if ($file != "." && $file != "..")
    {
     $Diff = (time() - filectime("$path/$file"))/60/60/24;
     if ($Diff > 1) unlink("$path/$file");
    }
   }
   closedir($manegal);
  }

// esborrem tots els fitxers del directori htmls que tinguin més de 24 hores
  $path="htmls";
  if (is_dir("$path") )
  {
   $manegal=opendir($path);
   while (false!==($file = readdir($manegal)))
   {
    if ($file != "." && $file != "..")
    {
     $Diff = (time() - filectime("$path/$file"))/60/60/24;
     if ($Diff > 1) unlink("$path/$file");
    }
   }
   closedir($manegal);
  }

//@OBRIULESFOSSES * aquí és on ens en anem si cancel·lem
exit("HO ANEM AQUÍ?????");
 
 }
 else
 { // podem provocar errors executant sense interfície amb només comandes via URL (captura GET)
  // aquí també hi arriben els errors de sintax PostScript
//@OBRIULESFOSSES en el cas que manqui o hi hagi un error al nom del directori de sortida /pdfs o el fitxer .ps a interpretar no tingui prous permisos, genera un valor 1!

   if ($ElQtorna == 0)
   {
    exit("EN QUIN CAS ATERRAREM EN AQUEST PUNT?");
   }
   else
   {
	   //exit("SOM AQUÍ?");
  // aquí llistem l'ERROR del prompt i demanem que s'enviï
  echo "<center><span style='color:#ff0000;font-family:monospace;font-size:24px'><br><br>&gt;&gt;&gt; ERROR d'execuci&oacute; de l'algorisme &lt;&lt;&lt;</span>";
  echo "<br><br><span style='color:#999999;font-family:monospace;font-size:24px'>".$prompt." + ".$ElQtorna."<br></span>";
//@OBRIULESFOSSES localhost
  exit("<br><p><br><p><span style='color:#ff0000;font-family:monospace;font-size:24px'><a style='color:#ff0000;font-family:monospace;font-size:24px' href='mailto:onetsoncleguillem@gmail.com'>podeu documentar-nos l'error via email? (<i>copieu i enganxeu el text en gris</i>) gr&agrave;cies!</a><br><br><br><br></center>");
  }
 
 }

 echo "</body></html>";
}


?>
