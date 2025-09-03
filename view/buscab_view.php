
<!DOCTYPE html>
<html>
<head>
    <title>Net.UPT.edu.pe</title>
    <link rel="stylesheet" type="text/css" href="tree.css" />
    <link rel="stylesheet" type="text/css" href="dhtmlgoodies_calendar.css?random=20051112"/>
    <link rel="stylesheet" href="file_demos/jquery-accordion/demo/demo.css" />
    <script type="text/javascript" src="file_demos/jquery-accordion/lib/jquery.js"></script>
    <script type="text/javascript" src="file_demos/jquery-accordion/lib/chili-1.7.pack.js"></script>
    <script type="text/javascript" src="file_demos/jquery-accordion/lib/jquery.easing.js"></script>
    <script type="text/javascript" src="file_demos/jquery-accordion/lib/jquery.dimensions.js"></script>
    <script type="text/javascript" src="file_demos/jquery-accordion/jquery.accordion.js"></script>
    <script type="text/javascript">
        // Accordion script from original file
        jQuery().ready(function(){
            // simple accordion
            jQuery('#list1a').accordion();
            jQuery('#list1b').accordion({
                autoheight: false
            });
        });
    </script>
</head>
<body>

<?php
    // Lateral menu
    echo ('<link href="site.css" type="text/css" rel="stylesheet">');
    helpx(45801, $data['sex']);
    echo ('<div id="root-row2"><div id="crumbs"></div></div>');
    echo ('<div id="nav"><div id="menu-block">');
    echo ('<a class="menux1" href="inicio.php?sesion='.$data['sex'].'">Inicio</a>');
    
    for ($l = 1; $l <= $data['grupa0']; $l++) {
        // Menu generation logic from original file
    }
    
    echo ('</div></div>');

    echo '<div id="contents">';
    echo '<form method="POST" action="buscab.php?tr=1&sesion='.$data['sex'].'" name="frmbuscab">';
    
    echo '<table border="0" width="600"><tr><td><font size="2">Docente: '.$data['namex'].'</font></td><td><font size="1">';
    if ($_SESSION['codigo'] != 121212 && $_SESSION['codigo'] != 123456) {
        echo '<a href="buscab.php?tr=1&sesion='.$data['sex'].'" >TRABAJO INDIVIDUAL</font>';
    }
 
    
    echo '<br><br>';

    // Display courses table
    echo '<table border="0"><tr><th bgcolor="#DBEAF5" ><font size="1">sel</font></th><th bgcolor="#DBEAF5" ><font size="1">CodCurso</font></th><th bgcolor="#DBEAF5" ><font size="1">Seccion</font></th><th bgcolor="#DBEAF5" ><font size="1">Curso</font></th><th bgcolor="#DBEAF5" ><font size="1">Semestre</font></th><th bgcolor="#DBEAF5" ><font size="1">Escuela</font></th><th bgcolor="#DBEAF5" ><font size="1">Hrs.</font></th></tr>';
    $indice = 0;
    $na = 0;
    $ton = 0;
    while ($row = fetchrow($data['cursos'], -1)) {
        if($indice == 0){
			$sem = $row[7];
		}
		$indice = $indice + 1;
        
        $na++;
        if ($ton==1){$tcol='bgcolor="#F3F9FC"';$ton=0;}else{$tcol='';$ton=1;}
        if (isset($_GET["dc"]) && $_GET["dc"]==$na){$dc='checked';$dcx=$row[5];$idsem=$row[7];$seccion=$row[1];$idcurso=$row[8];}else{$dc='';}
        
        echo ' <tr '.$tcol.'>';
        echo '<td>';
        if( $_SESSION['codigo'] != 121212 && $_SESSION['codigo'] != 123456 ){ 
			echo '<input type="radio" value="'.$na.'" name="R1"  onClick="javascript:pele('.$na.')" '.$dc.' >&nbsp;&nbsp;';
		}
        echo '</td>';
        echo ' <td '.$tcol.'><font size="1">'.$row[0].'</font></a></td>';
        echo ' <td '.$tcol.'><font size="1">'.$row[1].'</font></td>';
        echo ' <td '.$tcol.'><font size="1">'.$row[2].'</font></td>';
        echo ' <td '.$tcol.'><font size="1">'.$row[3].'</font></td>';
        echo ' <td '.$tcol.'><font size="1">'.$row[4].'</font></td>';
        echo ' <td '.$tcol.'><font size="1">'.$row[6].'</font></td></tr>';
    }
    echo '</table>';

    // Display evaluation data if available
    if (isset($data['evaluacion_data'])) {
        // Logic to display evaluation table
    }

    echo '</form>';

    if (isset($_GET['tr']) && $_GET['tr'] == true) {
        require_once "genera.php";
        $file_php = 0;
        individual($data['codigox'], $data['sex'], $_SESSION['codperx'], 1, $file_php, 0);
    }
?>
</div>

<script type="text/javascript" src="dhtmlgoodies_calendar.js?random=20060118"></script>
<script language="JavaScript">
    function pele(op) {
        location.href = "buscab.php?sesion=<?php echo $data['sex']; ?>&dc=" + op;
    }
    // Other JS functions from original file...
</script>
</body>
</html>
