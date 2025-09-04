<?php
// Este es el archivo de la vista: view/carga_view.php

// Verificar si se está solicitando una exportación y evitar cargar la vista en ese caso
if (isset($_GET['exportar'])) {
    // No cargar la vista si se está exportando
    return;
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="tree.css" />

<!--AGREGUE ESTA LINEA DE CODIGO PARA LLAMAR A LA LIBRERIA CALENDARIO-->
<link rel="stylesheet" type="text/css"  href="dhtmlgoodies_calendar.css?random=20051112"/>
<!--HASTA AQUI -->
<!--AGREGUE ESTO PARA EL ACORDEON -->
	<link rel="stylesheet" href="file_demos/jquery-accordion/demo/demo.css" />

	<script type="text/javascript" src="file_demos/jquery-accordion/lib/jquery.js"></script>
	<script type="text/javascript" src="file_demos/jquery-accordion/lib/chili-1.7.pack.js"></script>

	<script type="text/javascript" src="file_demos/jquery-accordion/lib/jquery.easing.js"></script>
	<script type="text/javascript" src="file_demos/jquery-accordion/lib/jquery.dimensions.js"></script>
	<script type="text/javascript" src="file_demos/jquery-accordion/jquery.accordion.js"></script>

	<script type="text/javascript" src="uptlogin/scripts/jquery.rightclick.js"></script>
	<style>
		.tooltip {
			position: relative;
			display: inline-block;
			border-bottom: 1px dotted black;
		}
		.tooltip .tooltiptext {
			visibility: hidden;
			background-color: #555;
			width: 300px;
			color: #fff;
			text-align: center;
			border-radius: 6px;
			padding: 5px 0;
			position: absolute;
			z-index: 1;
			left: 200px;
			margin-left: -60px;
			opacity: 0;
			transition: opacity 0.3s;
		}
		.tooltip:hover .tooltiptext {
			visibility: visible;
			opacity: 1;
		}
	</style>
	<script type="text/javascript">
	jQuery().ready(function(){
		// simple accordion
		jQuery('#list1a').accordion();
		jQuery('#list1b').accordion({
			autoheight: false
		});

		// second simple accordion with special markup
		jQuery('#navigation').accordion({
			active: false,
			header: '.head',
			navigation: true,
			event: 'mouseover',
			fillSpace: true,
			animated: 'easeslide'
		});

		// highly customized accordion
		jQuery('#list2').accordion({
			event: 'mouseover',
			active: '.selected',
			selectedClass: 'active',
			animated: "bounceslide",
			header: "dt"
		}).bind("change.ui-accordion", function(event, ui) {
			jQuery('<div>' + ui.oldHeader.text() + ' hidden, ' + ui.newHeader.text() + ' shown</div>').appendTo('#log');
		});

		// first simple accordion with special markup
		jQuery('#list3').accordion({
			header: 'div.title',
			active: false,
			alwaysOpen: false,
			animated: false,
			autoheight: false
		});

		var wizard = $("#wizard").accordion({
			header: '.title',
			event: false
		});

		var wizardButtons = $([]);
		$("div.title", wizard).each(function(index) {
			wizardButtons = wizardButtons.add($(this)
			.next()
			.children(":button")
			.filter(".next, .previous")
			.click(function() {
				wizard.accordion("activate", index + ($(this).is(".next") ? 1 : -1))
			}));
		});

		// bind to change event of select to control first and seconds accordion
		// similar to tab's plugin triggerTab(), without an extra method
		var accordions = jQuery('#list1a, #list1b, #list2, #list3, #navigation, #wizard');

		jQuery('#switch select').change(function() {
			accordions.accordion("activate", this.selectedIndex-1 );
		});
		jQuery('#close').click(function() {
			accordions.accordion("activate", -1);
		});
		jQuery('#switch2').change(function() {
			accordions.accordion("activate", this.value);
		});
		jQuery('#enable').click(function() {
			accordions.accordion("enable");
		});
		jQuery('#disable').click(function() {
			accordions.accordion("disable");
		});
		jQuery('#remove').click(function() {
			accordions.accordion("destroy");
			wizardButtons.unbind("click");
		});
	});
	</script>
<script type="text/javascript">
 $(document).keydown(function(e){
  var code = (e.keyCode ? e.keyCode : e.which);
  if(code == 116) {
   e.preventDefault();
   jConfirm('¿Deseas recargar la página?', 'Confirmación', function(r) {
       if(r)
        location.reload();
   });
  }
 });
</script>
<script type="text/javascript">
$(function(){
	$('body').noContext();
});
</script>

<!--HASTA AQUI ES EL ACORDEON -->

<title>Net.UPT.edu.pe</title>
<!-- html2pdf.js v0.10.1 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<!-- SheetJS (xlsx.js) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body >

<?php
/*MENU LATERAL IZQUIERDO*/
   echo ('<link href="site.css" type="text/css" rel="stylesheet">');
   helpx(10701,$data['sex']);

   echo ('<div id="root-row2">');
   	echo ('<div id="crumbs"> ');
	echo ('</div>');
   echo ('</div>');

     echo ('<div id="nav">');
     echo ('<div id="menu-block">');
      echo ('<a class="menux1" href="inicio.php?sesion='.$data['sex'].'">Inicio</a>');
      // if( $_SESSION['codigo'] == 298907 ){
	      echo ('<div id="side-block"></div>');
				echo ('<a class="menux1" href="dusuario.php?sesion='.$data['sex'].'">Datos Docente</a>');
			// }
			// echo $_SESSION['grupa0'];
      For ($l=1;$l<=$data['grupa0'];$l++)
	{
        	switch ($data['grupa'.$l]) {
        	case 100:
        		echo '<div id="side-block"></div>';
      			echo '<a class="menux1" href="alumno.php?sesion='.$data['sex'].'">Alumno</a>';
        		break;
        	case 200:
        		echo '<div id="side-block"></div>';
				echo '<div id="side-block"></div>';
      			echo '<a class="menuy1" href="carga.php?sesion='.$data['sex'].'">Carga</a>';
				echo '<div id="side-block"></div>';
                
                while ($row = fetchrow($data['semestres'],-1))
				{
 // 
					echo '<i><a class="menux1 tooltip" style="font-size:11px; text-align:left; background:#a6a6ac; color:white; padding-top:5px;" href="carga.php?sesion='.$data['sex'].'&x='.$row[0].'&tx='.$row[3].'"> '.$row[1].'  <span style="font-size:10px; padding-left:2px;">('.$row[0].')</span><span class="tooltiptext">'.$row[2].'</span></a></i>';
					echo '<div id="side-block"></div>';
				}

				echo '<div id="side-block"></div>';
      			echo '<a class="menux1" href="Elearning.php?sesion='.$data['sex'].'" target="_blank">Aula Virtual</a>';


			//if ($_SESSION['padl']==1)
			//{
      		 echo '<div id="side-block"></div>';
      		 echo '<a class="menux1" href="asistenciap.php?sesion='.$data['sex'].'">Parte Asistencia</a>';
      		//}

        		break;
    		case 300:
      			echo '<div id="side-block"></div>';
      			echo '<a class="menux1" href="estadistica.php?sesion='.$data['sex'].'">Estadistica</a>';
        		break;
    		case 400:
      			echo '<div id="side-block"></div>';
		      	echo '<a class="menux1" href="busca.php?sesion='.$data['sex'].'">Busqueda</a>';
        		break;
    		case 700:
			echo '<div id="side-block"></div>';
		      	echo '<a class="menux1" href="biblioteca.php?sesion='.$data['sex'].'" target="_blank">Biblioteca</a>';
		      	break;

		}
   	}

// ************* 	LOGO 	******************
			echo '<div id="side-block"></div>';
		      	echo '<br>';
		      	echo '<br>';
				// logo WIFI
	//echo '<br><center><a href="alumno.php?sesion='.$data['sex'].'&wf=1" ><img width="74" height="50"  alt="Clic para generar su clave Wifi" src="imagenes/logo_wifi.png" border=0><br><font size =2px> GENERAR CLAVE WIFI</font></a></center><b>';

	if ($data['wf']==1){
		echo '<br>';
		echo '<center>';
		echo $data['wifi'].'</b><br>';
		echo '</center>';
	}

// ************* 	HASTA AQUI LOGO 	******************
     echo ('</div>');
    echo ('</div>');

echo '<div id="contents">';






echo '<form method="POST" action="carga.php?sesion='.$data['sex'].'" name="frminicio">';
echo '<INPUT TYPE="hidden" NAME="op" value="0" >';
echo '<table border="0" width="100%">';
echo '<tr>';

if($data['semestre_info']){
    $rowdir = fetchrow($data['semestre_info'], -1);
    $semestre=$rowdir[0];
    $obssemestre=$rowdir[1];
}

echo '<td width="550">';
if($data['idsem'] > 0){
	echo '<font size="2"><strong>Semestre:</strong> ('. $data['idsem'] .') - '. $obssemestre . '</font><br>';
}
echo '<font size="2"><strong>Docente:</strong> '.$data['name'].'</font>';
echo '</td>';

if ($data['idsem'] == '')
{
	echo "<font size='4' style='color: 102368;'>Hacer clic en el SEMESTRE del menu izquierdo.</font>";
	exit;
}

if(!$data['esSemestreTaex']){
	echo '<td><font size="1"><a style="font-size:12px;" href="carga.php?tr=1&sesion='.$data['sex'].'&x='.$data['idsem'].'" >TRABAJO INDIVIDUAL '.$obssemestre.' </a></font>  <font size="1" face="Arial">
									<blink>
										<a href="documentos/PIT_Docente/ActividadesPITGA_V2.pdf" target="_blank">
											<center style="color: red;">( Guía PIT )</center>
										</a>
									</blink>
									</font></td>';
}

if($data['director_depe']){
    $rowdir = fetchrow($data['director_depe'], -1);
    $iddepedirec=$rowdir[0];
    require_once('encripta_pdf.php');
    if (($iddepedirec>0) or ($data['codigo']==117584) or ($data['codigo']==	202848))
    {
        //echo '<td><font size="2"><a target="_blank" href="http://www.upt.edu.pe/epic2/resultado.php" >Reporte de resultados de encuesta</a></font></td>';
    }

    if (($data['codigo']==117584) or ($data['codigo']==	202848)or ($data['codigo']==141414)or ($data['codigo']==109684)or ($data['codigo']==124717))
    {
        echo '<td><font size="2"><a target="_blank" href="http://www.upt.edu.pe/epic2/resultadovi.php" >Reporte de resultados de encuesta VICERRECTOR y RECTOR(A)</a></font></td>';
    }
}

echo '</tr>';
echo '</table>';

// Botones para imprimir y exportar
echo '<div style="text-align: right; margin: 10px 0;">';
echo '<button type="button" id="btnGenerarPDF" style="padding: 8px 15px; background-color: #1E88E5; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">Descargar Carga (PDF)</button>';
echo '<button type="button" id="btnGenerarExcel" style="padding: 8px 15px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">Descargar Carga (Excel)</button>';
echo '<button type="button" id="btnGenerarExcelWebScraping" style="padding: 8px 15px; background-color: #FF9800; color: white; border: none; border-radius: 4px; cursor: pointer;">Descargar con Excel usando WebScraping</button>';
echo '</div>';

$na=0;
$mj=0;

echo '<table border="0" ><tr><th bgcolor="#DBEAF5" ><font size="1">sel</font></th><th bgcolor="#DBEAF5" ><font size="1">CodCurso</font></th><th bgcolor="#DBEAF5" ><font size="1">Seccion</font></th><th bgcolor="#DBEAF5" ><font size="1">Curso</font></th><th bgcolor="#DBEAF5" ><font size="1">Semestre</font></th><th bgcolor="#DBEAF5" ><font size="1">Escuela</font></th><th bgcolor="#DBEAF5" ><font size="1">Hrs.</font></th>';
echo'<th bgcolor="#DBEAF5" ><font size="1">Consolidado</font></th>';
echo'</tr>';

if($data['cursos']){
    while ($row =fetchrow($data['cursos'],-1))
	{
		$na++;
        if ($ton==1){$tcol='bgcolor="#F3F9FC"';$ton=0;}else{$tcol='';$ton=1;}
		echo ' <tr '.$tcol.'><td><input type="radio" value="'.$na.'" name="R1" onClick="javascript:pele('.$na.')" >&nbsp;&nbsp;</td>';
		echo ' <td '.$tcol.'><font size="1">'.$row[0].'</font></td>';
		echo ' <td '.$tcol.'><font size="1">'.$row[1].'</font></td>';
		echo ' <td '.$tcol.'><font size="1">'.$row[2].'</font></td>';
		echo ' <td '.$tcol.'><font size="1">'.$row[3].'</font></td>';
		echo ' <td '.$tcol.'><font size="1">'.$row[4].'</font></td>';
		echo ' <td '.$tcol.'><font size="1">'.$row[6].'</font></td>';
		echo '<input name="codcur" type="hidden" value="'.$row[0].'">';
		echo'<input name="codp" type="hidden" value="'.$data['codper'].'">';
		// CORRECCIÓN: Usar buscacxv2.php en lugar de carga.php
		echo'<td '.$tcol.'><font size="1"><a href=# onclick="javascript:window.open(\'buscacxv2.php?sesion='.$data['sex'].'&o='.$row[5].'&taex='.$row[9].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=850,height=650,top=40,left=50\');return false"><center><img src="imagenes/view.gif" width=18 height=20 alt="Consolidado" border=0></center> </a></font></td>';
		echo'</tr>';
		if ($row[4]=='Ingeniería Civil'){$mj=1;}

	}
}

echo '<table border="0" cellpadding="10" ><tr><td width="132" ></td></tr></table>';
echo '</table>';
echo '</form>';

if(isset($data['ultimos_accesos'])){
    if ( numrow($data['ultimos_accesos']) > 0 ){
?>

    <h3>Últimos Accesos</h3>
    <table border="0" cellspacing="2">
        <tr>
            <th bgcolor="#DBEAF5"><font size="1">&nbsp;&nbsp;N&#176;&nbsp;&nbsp;</font></th>
            <th bgcolor="#DBEAF5"><font size="1">CodCurso</font></th>
            <th bgcolor="#DBEAF5"><font size="1">Curso</font></th>
            <th bgcolor="#DBEAF5"><font size="1">Seccion</font></th>
            <th bgcolor="#DBEAF5"><font size="1">Usuario Ingreso</font></th>
            <th bgcolor="#DBEAF5"><font size="1">Fecha Ingreso</font></th>
        </tr>
        <?php

            $nc=0;
            $tcol='';
            $ton=0;

            while ($row = fetchrow($data['ultimos_accesos'],-1))
            {
                $nc++;

                if ($ton==1){$tcol='class="lin1"';$ton=0;}else{$tcol='class="lin0"';$ton=1;}
        ?>
            <tr <?php echo $tcol; ?> >
                <td align="center"><font size="1"><?php echo $nc; ?></font></td>
                <td align="center"><font size="1"><?php echo $row[0]; ?></font></td>
                <td><font size="1"><?php echo $row[1]; ?></font></td>
                <td align="center"><font size="1"><?php echo $row[2]; ?></font></td>
                <td align="center"><font size="1"><?php echo $row[3]; ?></font></td>
                <td><font size="1"><?php echo $row[4]; ?></font></td>
            </tr>
        <?php
            }
        ?>
    </table>
<?php
    }
}
echo '</br>';
echo '</br>';

$file_php=0;
if (isset($data['tr'])==true ){
	require ("genera.php");
	$sem = $data['idsem'];
	individual($data['codigo'], $data['sex'], $data['codper'], 0, $file_php,$sem);
}
echo '</br>';
if(isset($data['tr'])==true)
{
	echo  '<br><br><form method="post" action="carga.php?tr=1&sesion='.$data['sex'].'&x='.$data['idsem'].'" name="registro" id="registro" enctype="multipart/form-data">
	<table border="0" widtd="100%" >
	<tr><td style="color:111351; font-size:15px;"><b>REGISTRO DE HORARIO DE TRABAJO</b></td></tr>
	<tr>
	<td><input type="hidden" name="variable2" value="registro" />- Descargar <b>Anexo</b> de horario de trabajo<a href="trabajoindividualex/Horario_Docente.xls" target="_blank"><span style="color: red;"><font size="1" face="Arial"><blink> ( Haga clic aquí )</blink></font></span> </td></tr>
	</tr>
	<tr>
	<tr>
	<td></td>
	</tr>
	<td>Seleccionar el archivo <font size="1px">(xls)</font> <input type="file" name="archivo[]" accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" multiple onchange="validarArchivo(this)"></td>
	<td><input type="submit" value="Subir Archivo" name="registrar" id="registrar"></td>
	</tr>
	</table>
	</form>';

    if($data['trabajo_individual_doc']){
        $rowdir = fetchrow($data['trabajo_individual_doc'],-1);
        $doc=$rowdir[0];
        echo "- Visualizar Horario de Trabajo ".$semestre.": <a href=trabajoindividualex/".$doc.">Hacer Clic para Ver el Documento</a><br><br><br>";
    }
}

echo '<br><br>En la columna <strong>Consolidado</strong> podrá visualizar la nota promedio de la unidad y el resumen de la unidad <br>que incluye: aprobados, desaprobados, retirados y abandonos.';

?>
</div>
</body>

<!--AGREGUE ESTA LINEA DE CODIGO PARA EL CALENDARIO-->
<script type="text/javascript" src="dhtmlgoodies_calendar.js?random=20060118"></script>
<!--HASTA AQUI CALENDARIO-->

<script language="JavaScript" >
function pele(op)
{
	document.frminicio.submit();
}
function msj()
{
	//var d = document.forms[1].elements[2];
	var d = document.forms[1].vdacti;
	//var h = document.forms[1].elements[7];
	var h = document.forms[1].vhoras;

	if (d.value=="" || (h.value)=="")
	{
		alert ('Debe ingresar el Detalle de Actividad y las Horas.  ¡No se puedo guardar!');
		//alert(d.name);
		//alert(h.name);

	}
	else
	{
		//alert(d.name);
		//alert(h.name);
		document.frmindiv.vagregar.value="Agregar";
		document.frmindiv.submit();
	}
}
</script>
<!--AGREGO ESTO PARA VALIDAR LOS BOTONES DE EDITAR , ELIMINAR-->
	<script LANGUAGE="JavaScript">

    function confirmSubmit()
    {
    var agree=confirm("¿Esta seguro que desea editar la actividad?");
    if (agree)
        return true ;
    else
        return false ;
    }

    function confirmFinalizar(x)
    {

		if (x==0 || x==2)
		{
			alert("Para finalizar la actividad, tiene quer ser aprobada por su jefe inmediato.");
			return false ;
		}
		else
		{
			var agree=confirm("¿Esta seguro que desea finalizar la actividad?");
			if (agree)
				return true ;
			else
				return false ;
		}

    }

    function confirmRevertir()
    {
    var agree=confirm("¿Esta seguro que desea revertir la actividad?");
    if (agree)
        return true ;
    else
        return false ;
    }

	function confirmdelete()
    {
    var agree=confirm("¿Esta seguro que desea eliminar la actividad?");
    if (agree)
        return true ;
    else
        return false ;
    }

	function msjregistrar()
	{

		var nominfo_historial = document.forms[1].vnominfo_historial;
		var dirigido_historial = document.forms[1].vdirigido_historial;
		var cargo_historial = document.forms[1].vcargo_historial;
		var remitente_historial = document.forms[1].vremitente_historial;
		var detalle_historial = document.forms[1].vdetalle_historial;

		if (nominfo_historial.value=="" || (dirigido_historial.value)=="" || (cargo_historial.value)=="" || (remitente_historial.value)=="" || (detalle_historial.value)=="")
		{
			alert ('Debe ingresar datos en cada campo. ');

		}
		else
		{
			document.frmindiv.addhistorial.value="Registrar";
			document.frmindiv.submit();
		}
	}

    </script>
<!--HASTA AQUI ES EL SCRIPT PARA LOS BOTONES-->
<script type="text/javascript">
/* === Abrir diálogo de impresión del navegador (Ctrl+P) === */
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('btnGenerarPDF');
    if (!btn) return;

    btn.addEventListener('click', function (ev) {
        ev.preventDefault();   // evita que el botón envíe el formulario
        /* Opcional: desplazarse al inicio para asegurar que todo se pinte */
        window.scrollTo(0, 0);

        /* Llamamos a la herramienta de impresión nativa del navegador */
        window.print();
    });

    /* === Descargar Excel === */
    var btnExcel = document.getElementById('btnGenerarExcel');
    if (btnExcel) {
        btnExcel.addEventListener('click', function (ev) {
            ev.preventDefault();
            // Crear un enlace temporal para descargar el archivo Excel
            var url = 'carga.php?sesion=<?php echo $data['sex']; ?>&x=<?php echo $data['idsem']; ?>&exportar=1&formato=excel';
            var link = document.createElement('a');
            link.href = url;
            link.download = 'carga_docente_<?php echo $data['idsem']; ?>.xlsx';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    }
    /* === Descargar Excel usando WebScraping === */
    var btnExcelWebScraping = document.getElementById('btnGenerarExcelWebScraping');
    if (btnExcelWebScraping) {
        btnExcelWebScraping.addEventListener('click', function (ev) {
            ev.preventDefault();
            
            // Crear un nuevo libro de trabajo
            var wb = XLSX.utils.book_new();
            
            // Encontrar el contenedor principal de contenidos
            var contentsDiv = document.getElementById('contents');
            if (!contentsDiv) return;
            
            // Crear un contenedor temporal para el contenido que queremos exportar
            var tempDiv = document.createElement('div');
            
            // Encontrar el elemento "Últimos Accesos" y el elemento "REGISTRO DE HORARIO DE TRABAJO"
            var ultimosAccesos = null;
            var registroHorario = null;
            
            // Buscar "Últimos Accesos" por su encabezado h3
            var headers = contentsDiv.querySelectorAll('h3');
            for (var i = 0; i < headers.length; i++) {
                if (headers[i].textContent.includes('Últimos Accesos')) {
                    ultimosAccesos = headers[i];
                    break;
                }
            }
            
            // Buscar "REGISTRO DE HORARIO DE TRABAJO" por su id
            registroHorario = contentsDiv.querySelector('#registro');
            
            // Si encontramos ambos elementos, extraer el contenido entre los dos <br> especificados
            if (ultimosAccesos && registroHorario) {
                // Encontrar el segundo <br> después de ultimosAccesos
                var startBr = null;
                var brCount = 0;
                var currentNode = ultimosAccesos.nextSibling;
                while (currentNode) {
                    if (currentNode.tagName === 'BR') {
                        brCount++;
                        if (brCount === 2) {
                            startBr = currentNode;
                            break;
                        }
                    }
                    currentNode = currentNode.nextSibling;
                }
            
                // Encontrar el último <br> antes de registroHorario
                var endBr = null;
                currentNode = registroHorario.previousSibling;
                while (currentNode) {
                    if (currentNode.tagName === 'BR') {
                        endBr = currentNode;
                        break;
                    }
                    currentNode = currentNode.previousSibling;
                }
            
                // Extraer el contenido entre startBr y endBr
                if (startBr && endBr) {
                    currentNode = startBr.nextSibling;
                    while (currentNode && currentNode !== endBr) {
                        if (currentNode.nodeType === 1) { // Solo elementos HTML
                            tempDiv.appendChild(currentNode.cloneNode(true));
                        }
                        currentNode = currentNode.nextSibling;
                    }
                }
            } else {
                // Si no encontramos los marcadores, usar un enfoque alternativo
                // Extraer las tablas principales de cursos y últimos accesos
                var tablas = contentsDiv.querySelectorAll('table');
                for (var i = 0; i < tablas.length; i++) {
                    var tabla = tablas[i];
                    // Incluir tablas de cursos (las que tienen encabezados como CodCurso, Seccion, etc.)
                    var thElements = tabla.querySelectorAll('th');
                    if (thElements.length > 0) {
                        var firstTh = thElements[0];
                        if (firstTh.textContent.includes('sel') || firstTh.textContent.includes('CodCurso')) {
                            tempDiv.appendChild(tabla.cloneNode(true));
                            // Agregar un salto después de la tabla de cursos
                            tempDiv.appendChild(document.createElement('br'));
                        }
                        // Incluir tabla de "Últimos Accesos"
                        else if (firstTh.textContent.includes('Nº') || firstTh.textContent.includes('CodCurso')) {
                            // Verificar si es la tabla de últimos accesos por su encabezado
                            var parent = tabla.closest('div');
                            if (parent && parent.previousElementSibling &&
                                parent.previousElementSibling.tagName === 'H3' &&
                                parent.previousElementSibling.textContent.includes('Últimos Accesos')) {
                                tempDiv.appendChild(tabla.cloneNode(true));
                            }
                        }
                    }
                }
            }
            
            // Convertir el contenido a una hoja de Excel
            if (tempDiv.children.length > 0) {
                // Crear una tabla temporal para organizar mejor los datos
                var tempTable = document.createElement('table');
                tempTable.appendChild(tempDiv);
                var ws = XLSX.utils.table_to_sheet(tempTable, {sheet:"Carga Docente"});
            
                // Remover filas 5 a 12 (0-based: 4 a 11)
                var originalRange = XLSX.utils.decode_range(ws['!ref']);
                var newWs = {};
                var newRowIndex = 0;
                // Copiar filas 0-3
                for (var r = 0; r < 4; r++) {
                    for (var c = originalRange.s.c; c <= originalRange.e.c; c++) {
                        var cellAddr = XLSX.utils.encode_cell({r: r, c: c});
                        if (ws[cellAddr]) {
                            var newCellAddr = XLSX.utils.encode_cell({r: newRowIndex, c: c});
                            newWs[newCellAddr] = ws[cellAddr];
                        }
                    }
                    newRowIndex++;
                }
                // Saltar filas 4-11
                // Copiar filas desde 12 en adelante
                for (var r = 12; r <= originalRange.e.r; r++) {
                    for (var c = originalRange.s.c; c <= originalRange.e.c; c++) {
                        var cellAddr = XLSX.utils.encode_cell({r: r, c: c});
                        if (ws[cellAddr]) {
                            var newCellAddr = XLSX.utils.encode_cell({r: newRowIndex, c: c});
                            newWs[newCellAddr] = ws[cellAddr];
                        }
                    }
                    newRowIndex++;
                }
                // Actualizar el rango
                var newRange = {
                    s: {r: 0, c: originalRange.s.c},
                    e: {r: newRowIndex - 1, c: originalRange.e.c}
                };
                newWs['!ref'] = XLSX.utils.encode_range(newRange);
                // Reemplazar ws
                ws = newWs;
            
                // Remover las últimas 12 filas antes de generar el Excel
                var range = XLSX.utils.decode_range(ws['!ref']);
                var totalRows = range.e.r - range.s.r + 1;
                if (totalRows > 12) {
                    var newEndRow = range.e.r - 12;
                    // Eliminar las celdas de las últimas 12 filas
                    for (var row = newEndRow + 1; row <= range.e.r; row++) {
                        for (var col = range.s.c; col <= range.e.c; col++) {
                            var cellAddress = XLSX.utils.encode_cell({r: row, c: col});
                            delete ws[cellAddress];
                        }
                    }
                    // Actualizar el rango
                    range.e.r = newEndRow;
                    ws['!ref'] = XLSX.utils.encode_range(range);
                }
            
                XLSX.utils.book_append_sheet(wb, ws, "Carga Docente");
            }
            
            // Generar el archivo Excel y descargarlo
            var filename = 'carga_docente_webscraping_<?php echo $data['idsem']; ?>.xlsx';
            XLSX.writeFile(wb, filename);
        });
    }
});
</script>
</html>