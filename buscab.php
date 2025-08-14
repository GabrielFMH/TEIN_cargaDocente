<?
$sex=$_GET["sesion"];
require ("funciones.php");
require ("funciones_fichamatricula.php"); // PARA FICHA MATRICULA
pageheader();
callse($sex);
session_name($sex);
session_start();
$tiempo=timeup($_SESSION['timer']);
if ($tiempo)
{
	$_SESSION['timer']=time();
}
else
{
	header("Location: logout.php?sesion=".$sex);
}
if ($_SESSION['tipo']!=3){header("Location: cambio.php?sesion=".$sex);}

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

<!--HASTA AQUI ES EL ACORDEON -->


<title>Net.UPT.edu.pe</title>
</head>
<body >

<?
/*recibe los campos enviados de las cajas de texto, despues de haber realizado clic en el boton AGREGAR*/
if ($_POST["vagregar"]=="Agregar"){
	$vacti=$_POST["vacti"];
	$vdacti=$_POST["vdacti"];
	$vimporta=$_POST["vimporta"];
	$vmedida=$_POST["vmedida"];
	$vcant=$_POST["vcant"];
	$vhoras=$_POST["vhoras"];
	$vcalif=$_POST["vcalif"];
	$vmeta=$_POST["vmeta"];

	/*NUEVAS VARIABLES  - FECHA INICIO , FECHA FINAL y EL IDDEPE*/
	$vdatebox=$_POST["datebox"];
	$vdatebox2=$_POST["datebox2"];

	$viddepe=$_POST["viddepe"];

	$vcanthoras=$_POST["vcanthoras"];


	/*HASTA AQUI*/

	if (isset($vacti)==false){$vacti="";}
	if (isset($vdacti)==false){$vdacti="";}
	if (isset($vimporta)==false){$importa="";}
	if (isset($vmedida)==false){$vmedida="";}
	if (!$vcant>0){$vcant=0;}
	if (!$vhoras>0){$vhoras=0;}
	if (isset($vcalif)==false){$vcalif=0;}
	if (isset($vmeta)==false){$vmeta="";}
	$conn=conex();
	//$sql="insert into trab select ".$_SESSION['codigox'].", (select min(idsem) from semestre where tipo=1 and activo=1), '".$vacti."', '".$vdacti."', '".$vimporta."', '".$vmedida."',".$vcant.", ".$vhoras.", ".$vcalif.", '".$vmeta."',''";
	//$sql="insert into trab select ".$_SESSION['codigox'].", (select min(idsem) from semestre where tipo=1 and activo=1 and idsem not in (20080105,20080111)), '".$vacti."', '".$vdacti."', '".$vimporta."', '".$vmedida."',".$vcant.", ".$vhoras.", ".$vcalif.", '".$vmeta."',''";
	/*+++++++++++++++++++*/
	/*CON LAS VARIALES CAPTURADAS EN LAS CAJAS DE TEXTO ENVIA ESTOS VALORES AL PROCEDIMIENTO ALMACENADO */
	/*$sql="exec trabiagregar_v2 ".$_SESSION['codigox'].", '".$vacti."', '".$vdacti."', '".$vimporta."', '".$vmedida."',".$vcant.", ".$vhoras.", ".$vcalif.", '".$vmeta."', '".$vdatebox."', '".$vdatebox2."', '".$viddepe."'";	*/
	$sql="exec trabiagregar_v2 ".$_SESSION['codigox'].", '".$vacti."', '".$vdacti."', '".$vimporta."', '".$vmedida."',".$vcant.", ".$vhoras.", ".$vcalif.", '".$vmeta."', '".$vdatebox."', '".$vdatebox2."', '".$viddepe."', '".$vcanthoras."'";
	//echo $sql;
	$result=luis($conn, $sql);
	cierra($result);
	noconex($conn);
}
/*AUMENTE ESTO PARA EDITAR EL ESTADO DE TODAS LAS ACTIVIDADES*/

	/*EDITO EL  ESTADO DE TODAS LA ACTIVIDADES QUE TENGAN EL MISMO codigo Y idsem*/
		if ($_POST["medit"]=="Modificar")
		{
			$msemestre=$_POST["msemestre"];
			$mcodigo=$_POST["mcodigo"];
			$mestado_editar=$_POST["mestado_editar"];
			/*HASTA AQUI*/
			$conn=conex();
			/*+++++++++++++++++++*/
			/*CON LAS VARIALES CAPTURADAS ENVIA ESTOS VALORES AL PROCEDIMIENTO ALMACENADO */
			$sql="exec sp_edit_estado_trabindiv ".$mcodigo.", ".$msemestre.", ".$mestado_editar;
			//echo $sql;
			$result=luis($conn, $sql);
			cierra($result);
			noconex($conn);
		}

	/*HASTA AQUI EDITO LAS ACTIVIDADES*/
	/*++++++++++++*/
	/*AUMENTO ESTO PARA PODER HABILITAR O DESHABILITAR ESTADO DE LA TABLA trab*/
	if ($_POST["modi_estado".$i]=="Modificar_Estado")
	{
		$idtrab=$_POST["vi".$i];
		$vestado_editar=$_POST["vestado_editar".$i];
		$conn=conex();

		$sql_editar="exec sp_editar_trabindiv_estado ".$_SESSION['codigox'].", '".$idtrab."', '".$vestado_editar."'";
		$result=luis($conn, $sql_editar);
		cierra($result);
		noconex($conn);
	}

/*HASTA AQUI INGRESA Y EDITA LOS DATOS A LA TABLA trab_registro*/


	/*+++++++++*/
	/*AGREGO LOS VALOES A LA TABLA trab_historial*/
	if ($_POST["addhistorial"]=="Registrar")
	{
	$vdactividad_historial=$_POST["vdactividad_historial"];
	$vnominfo_historial=$_POST["vnominfo_historial"];
	$vdirigido_historial=$_POST["vdirigido_historial"];
	$vcargo_historial=$_POST["vcargo_historial"];
	$vremitente_historial=$_POST["vremitente_historial"];
	$vdetalle_historial=$_POST["vdetalle_historial"];
	$vporcentaje_historial=$_POST["vporcentaje_historial"];
	/*$vdetalle2_historial=$_POST["vdetalle2_historial"];*/

	if (isset($vnominfo_historial)==false){$vnominfo_historial="";}
	if (isset($vdirigido_historial)==false){$vdirigido_historial="";}
	if (isset($vdirigido_historial)==false){$vdirigido_historial="";}
	if (isset($vcargo_historial)==false){$vcargo_historial="";}
	if (isset($vremitente_historial)==false){$vremitente_historial="";}
	if (isset($vdetalle_historial)==false){$vdetalle_historial="";}
	/*if (isset($vdetalle2_historial)==false){$vdetalle2_historial="";}	*/


	$idtrab=$vdactividad_historial;
	date_default_timezone_set('America/Lima');
	$dia=date("d/m/Y");

	$conn=conex();
	/*CON LAS VARIALES CAPTURADAS ENVIA ESTOS VALORES AL PROCEDIMIENTO ALMACENADO */

	$sql="exec sp_add_trab_historial ".$idtrab.", '".$vnominfo_historial."', '".$vdirigido_historial."', '".$vcargo_historial."', '".$vdetalle_historial."', '".$dia."'";
	//echo $sql;
	$result=luis($conn, $sql);

	/*ESTE IF REGISTRARA LAS ACTIVIDADES SIEMPRE Y CUANDO EL ESTADO DEL HISTORIAL DE LA ACTIVIDAD 	SEA DIFERENTE DE CERO */

/*	$sql_estado_historial=' select TOP 1 id_historial, idtrab, porcentaje, estado from trab_historial where idtrab="'.$idtrab.'" order by id_historial desc';
	$resultado=luis($conn, $sql_estado_historial);

	while ($row=fetchrow($result,-1))
	{
		$estado=$row[3];
	}

	cierra($resultado);

	$estado_historial=$estado;
	if(isset($estado_historial)==true)
	if(isset($estado_historial))
	if(($estado_historial=1)or($estado_historial=2))
	{
		$sql="exec sp_add_trab_historial ".$idtrab.", '".$vnominfo_historial."', '".$vdirigido_historial."', '".$vcargo_historial."', '".$vremitente_historial."', '".$vdetalle_historial."', '".$vporcentaje_historial."', '".$dia."'";
		$result=luis($conn, $sql);

		cierra($result);

	}
	else
	{

		if($estado_historial>0)
		{
			$sql="exec sp_add_trab_historial ".$idtrab.", '".$vnominfo_historial."', '".$vdirigido_historial."', '".$vcargo_historial."', '".$vremitente_historial."', '".$vdetalle_historial."', '".$vporcentaje_historial."', '".$dia."'";
			$result=luis($conn, $sql);

			cierra($result);

		}
	}*/


			/*$sql="exec sp_add_trab_historial ".$idtrab.", '".$vnominfo_historial."', '".$vdirigido_historial."', '".$vcargo_historial."', '".$vremitente_historial."', '".$vdetalle_historial."', '".$vporcentaje_historial."', '".$dia."'";
			$result=luis($conn, $sql);*/

			$sql="exec sp_add_trab_historial ".$_SESSION['codigox'].", '".$idtrab."', '".$vnominfo_historial."', '".$vdirigido_historial."', '".$vcargo_historial."', '".$vremitente_historial."', '".$vdetalle_historial."', '".$vporcentaje_historial."', '".$dia."'";
			//echo $sql;
			$result=luis($conn, $sql);


			cierra($result);
			noconex($conn);

	//ESTE SP ACTULIZA EL porcentaje DE LA TABLA trab Y LA TABLA trab_historial

	/*
	$sql_editar="exec sp_editar_trabindiv_porcent_v2 ".$_SESSION['codigox'].", '".$idtrab."', '".$vporcentaje_historial."'";

	$result=luis($conn, $sql_editar);
	cierra($result);
	*/

	}
	noconex($conn);
	/**HASTA AQUI AGREGA A LA TABLA trab_historial/
	/*++++++++*/

	/*HASTA AQUI EDITA EL ESTADO DE TODAS LAS ACTIVIDADES QUE TENGAN EL MISMO idsem Y codigo*/

/*ELIMINA el TRABAJO INDIVIDUAL*/
if ($_POST["vn"]>0){
	For ($i=1;$i<=$_POST["vn"];$i++)
	{
		if ($_POST["de".$i]=="Eliminar")
		{

			/*VARIABLES PARA ELIMINAR LOS CAMPOS DE LA TABLA detalle_trab*/
			$msemestre=$_POST["msemestre"];
			$mcodigo=$_POST["mcodigo"];
			/*HASTA VARIABLES PARA ELIMINAR LOS CAMPOS DE LA TABLA detalle_trab*/

			$conn=conex();
			$sql="delete from trab where codigo=".$_SESSION['codigox']." and idtrab=".$_POST["vi".$i];
			$result=luis($conn, $sql);
			cierra($result);

			/*ELIMINA LOS DATOS DE LA LA TABLA trab_historial cuando le elimina una actividad*/
			$sql_historial="delete from trab_historial where idtrab=".$_POST["vi".$i];
			$result=luis($conn, $sql_historial);
			cierra($result);
			/*HASTA AQUI ELIMINA*/

			$sql_historial="delete from detalle_trab where codigo=".$_SESSION['codigox']." and idsem=".$_POST["msemestre"];
			$result=luis($conn, $sql_historial);
			cierra($result);
			/*$sql_semestre="exec trabisem ".$_SESSION['codigox'];
			$result_semestre=luis($conn, $sql_semestre);
			while ($row=fetchrow($result_semestre,-1))
			{
				$semestre=$row[1];
				$fecha=$row[2];
				$codigoo=$row[3];

			}*/


			/*+++++++++*/
			noconex($conn);
		}

		/*AGREGO CODIGO PARA EDITAR EL TRABAJO INDIVIDUAL*/
		else {
			if ($_POST["dedit".$i]=="Editar"){

										$idtrab=$_POST["vi".$i];

										$vacti_editar=$_POST["vacti_editar".$i];
										$vdacti_editar=$_POST["vdacti_editar".$i];
										$vimporta_editar=$_POST["vimporta_editar".$i];
										$vmedida_editar=$_POST["vmedida_editar".$i];
										$vcant_editar=$_POST["vcant_editar".$i];
										$vhoras_editar=$_POST["vhoras_editar".$i];
										$vcalif_editar=$_POST["vcalif_editar".$i];
										$vmeta_editar=$_POST["vmeta_editar".$i];

										/*NUEVAS VARIABLES  - FECHA INICIO y FECHA FINAL*/
										$vdatebox_editar=$_POST["dateboxx".$i];
										$vdatebox2_editar=$_POST["dateboxx2".$i];
										$vporcentaje_editar=$_POST["vporcentaje_editar".$i];
										/*++*/
										$vdocumento_editar=$_POST["vdocumento_editar".$i];
										/*++*/
										$vestado_editar=$_POST["vestado_editar".$i];
										/*HASTA AQUI*/

										if (isset($vacti_editar)==false){$vacti_editar="";}
										if (isset($vdacti_editar)==false){$vdacti_editar="";}
										if (isset($vimporta_editar)==false){$vimporta_editar="";}
										if (isset($vmedida_editar)==false){$vmedida_editar="";}
										if (!$vcant_editar>0){$vcant_editar=0;}
										if (!$vhoras_editar>0){$vhoras_editar=0;}
										if (isset($vcalif_editar)==false){$vcalif_editar=0;}
										if (isset($vmeta_editar)==false){$vmeta_editar="";}

										/*++*/
										if (isset($vdocumento_editar)==false){$vdocumento_editar="";}
										/*++*/
										$conn=conex();

	/*++*/
	date_default_timezone_set('America/Lima');
	$fecha_registro=date("d/m/Y");
	/*$sql_modificar="exec sp_add_trabregistro ".$idtrab.", '".$vporcentaje_editar."', '".$vdocumento_editar."', '".$fecha_registro."'";
	$result2=luis($conn, $sql_modificar);
	cierra($result2);*/
	/*++*/

	/*$sql_editar="exec sp_editar_trabindiv ".$_SESSION['codigox'].", '".$_POST["vi".$i]."', '".$vacti_editar."', '".$vdacti_editar."', '".$vimporta_editar."', '".$vmedida_editar."',".$vcant_editar.", ".$vhoras_editar.", ".$vcalif_editar.", '".$vmeta_editar."', '".$vdatebox_editar."', '".$vdatebox2_editar."', '".$vporcentaje_editar."', '".$vestado_editar."'";*/

	// consulta ok
	// $sql_editar="exec sp_editar_trabindiv ".$_SESSION['codigox'].", '".$_POST["vi".$i]."', '".$vacti_editar."', '".$vdacti_editar."', '".$vimporta_editar."', '".$vmedida_editar."',".$vcant_editar.", ".$vhoras_editar.", ".$vcalif_editar.", '".$vmeta_editar."', '".$vdatebox_editar."', '".$vdatebox2_editar."', '".$vporcentaje_editar."'";

	$sql_editar="exec sp_editar_trabindiv ".$_SESSION['codigox'].", '".$_POST["vi".$i]."', '".$vacti_editar."', '".$vdacti_editar."', '".$vimporta_editar."', '".$vmedida_editar."',".$vcant_editar.", ".$vhoras_editar.", ".$vcalif_editar.", '".$vmeta_editar."', '".$vdatebox_editar."', '".$vdatebox2_editar."', '".$vporcentaje_editar."'";
	//echo $sql_editar;

	$result=luis($conn, $sql_editar);
	cierra($result);



	noconex($conn);
										}

/*AUMENTO ESTO PARA PODER INGRESAR Y EDITAR LOS DATOS A LA TABLA trab_registro*/
				else{ /*abre el else*/
					if ($_POST["modi".$i]=="Modificar Estado")
										{
										$idtrab=$_POST["vi".$i];


										/*$vporcentaje_editar=$_POST["vporcentaje_editar".$i];*/
										/*$vdocumento_editar=$_POST["vdocumento_editar".$i];*/
										$vestado_editar=$_POST["vestado_editar".$i];
										/*HASTA AQUI*/

										if (isset($vdocumento_editar)==false){$vdocumento_editar="";}
										$conn=conex();

	/*++*/

	if($vestado_editar>0)
	{
		$sql_editar="exec sp_editar_trabindiv_estado ".$_SESSION['codigox'].", '".$idtrab."', '".$vestado_editar."'";
		$result=luis($conn, $sql_editar);
		cierra($result);

	/*$sql_editar="exec sp_editar_trabindiv_porcent ".$_SESSION['codigox'].", '".$idtrab."', '".$vporcentaje_editar."', '".$vestado_editar."'";

	$result=luis($conn, $sql_editar);
	cierra($result);*/
	}
	else
	{

		$sql_editar="exec sp_editar_trabindiv_estado ".$_SESSION['codigox'].", '".$idtrab."', '".$vestado_editar."'";
		$result=luis($conn, $sql_editar);
		cierra($result);
		noconex($conn);
	/*$sql_editar="exec sp_editar_trabindiv_porcent ".$_SESSION['codigox'].", '".$idtrab."', '".$vporcentaje_editar."', '".$vestado_editar."'";
	$result=luis($conn, $sql_editar);
	cierra($result);*/

	}


	noconex($conn);
										}

	date_default_timezone_set('America/Lima');
	$fecha_registro=date("d/m/Y");
	/*$sql_modificar="exec sp_add_trabregistro ".$idtrab.", ".$vporcentaje_editar.", ".$vdocumento_editar.", ".$fecha_registro;
	$result=luis($conn, $sql_modificar);*/
	/*cierra($result);*/
	/*noconex($conn);*/

					}/*cierera el else*/

/*HASTA AQUI INGRESA Y EDITA LOS DATOS A LA TABLA trab_registro*/
			}

		/*else {

				if ($_POST["dedit".$i]=="Editar")
				{
				$conn=conex();

				$sql = 'select idtrab, actividad, dactividad, importancia, medida, cant, horas, calif, meta from trab t inner join semestre s on s.idsem=t.idsem and s.activo>0  where codigo="'.$_SESSION['codigox'].'" and idtrab="'.$_POST["vi".$i];
				$result=luis($conn, $sql);

				while ($row=fetchrow($result,-1))
				{


				}

				$vacti =  ;
				$vdacti = ;
				$vimporta = ;
				$vmedida= ;
				$vcant= ;
				$vhoras= ;
				$vcalif= ;
				$vmeta= ;


				$sql='update trab set
				actividad ="'.$vacti.'",
				dactividad = "'.$vdacti.'",
				importancia = "'.$vimporta.'",
				medida = "'.$vmedida.'",
				cant = "'.$vcant.'",
				horas = "'.$vhoras.'",
				calif = "'. $vcalif.'",
				meta  = "'.$vmeta.'"
				where codigo="'.$_SESSION['codigox'].'" and idtrab="'.$_POST["vi".$i];
				$result=luis($conn, $sql);
				cierra($result);
				noconex($conn);

			}

			}*/


		/*HASTA AQUI ES EL CODIGO PARA EDITAR*/


	}
					}


/*MENU LATERAL IZQUIERDO*/
   echo ('<link href="site.css" type="text/css" rel="stylesheet">');
   helpx(45801,$sex);
   echo ('<div id="root-row2">');
   	echo ('<div id="crumbs"> ');
	echo ('</div>');
   echo ('</div>');

     echo ('<div id="nav">');
     echo ('<div id="menu-block">');
      echo ('<a class="menux1" href="inicio.php?sesion='.$sex.'">Inicio</a>');
      $recod=0;
	 /* echo $_SESSION['grupa0'];*/
	  /*echo 10;*/
      For ($l=1;$l<=$_SESSION['grupa0'];$l++)
	{
        	switch ($_SESSION['grupa'.$l]) {
    		case 100:
        		echo '<div id="side-block"></div>';
      			echo '<a class="menux1" href="alumno.php?sesion='.$sex.'">Alumno</a>';
        		break;
    		case 200:
        		echo '<div id="side-block"></div>';
      			echo '<a class="menux1" href="carga.php?sesion='.$sex.'">Carga</a>';
        		break;
    		case 300:
      			echo '<div id="side-block"></div>';
      			echo '<a class="menux1" href="estadistica.php?sesion='.$sex.'">Estadistica</a>';
        		break;
    		case 400:
      			echo '<div id="side-block"></div>';
		      	echo '<a class="menuy1" href="busca.php?sesion='.$sex.'">Busqueda</a>';
        		break;
        	case 421:
      			$recod=1;
        		break;
		}
   	}
     echo ('</div>');
    echo ('</div>');

/* FIN DE MENU LATERAL DERECHO*/

echo '<div id="contents">';
//echo '<form method="POST" action="carga.php?sesion='.$sex.'" name="frmbuscab">';

/*AGREGUE ESTA LINEA DE CODIGO PARA EL CALENDARIO */
/*echo '<script language="JavaScript" type="text/javascript" src="calendar.js"></script>';*/
/*HASTA AQUI CODIGO DEL CALENDARIO*/

/*CREA FORMULARIO PARA LA TRABAJO INDIVIDUAL DE LOS DOCENTES*/

echo '<form method="POST" action="buscab.php?tr=1&sesion='.$sex.'" name="frmbuscab">';

//echo '<INPUT TYPE="hidden" NAME="consul" value="'.$cons.'" >';
//echo  'Docente: '.$_SESSION['namex'].'&nbsp;&nbsp;';
/*++++++++++*/
/*INDICA EL NOMBRE DEL DOCENTE Y PROPORCIONA EL LINK PARA VISUALIZAR EL TRABAJO INDIVIDUAL */
// echo '<table border="0" width="600"><tr><td><font size="2">Docente: '.$_SESSION['namex'].'</font></td><td><font size="1"><a href="buscab.php?tr=1&sesion='.$sex.'" >TRABAJO INDIVIDUAL</font></td>';
echo '<table border="0" width="600"><tr><td><font size="2">Docente: '.$_SESSION['namex'].'</font></td><td><font size="1">';
if( $_SESSION['codigo'] != 121212 && $_SESSION['codigo'] != 123456 ){ 
	echo '<a href="buscab.php?tr=1&sesion='.$sex.'" >TRABAJO INDIVIDUAL</font>';
}
echo '</td>';

if ($recod==1)
{
	echo  '<td><font size="1"><a href="buscab.php?sesion='.$sex.'&re=1">reset pass</a></font></td>';
}
// VISUALIZA OPCION PARA ASIGNAR PERMISOS DENTRO DE LA INTRANET

	// OPCION PARA PERMISOS
		$conn=conex();
		//$sql_permiso="select * from gcodigo where idfac = 999  and codigo =  ".$_SESSION['codigo'];
		$sql_permiso="select * from gcodigo where idfac = 999  and codigo =".$_SESSION['codigo'];
		$result_permiso=luis($conn, $sql_permiso);
		while ($rowp =fetchrow($result_permiso,-1))
		{
			//$cod_usuario = $rowp[0];
			$permiso_activo = $rowp[1];
			//$permiso_alcance = $rowp[2];
		}
		//echo $permiso_activo;
		if($permiso_activo > 0)  // SI EL USUARIO POSEE EL PERMISO 999 PUEDE ASIGNAR PERMISOS
		{
			//LLAMA AL ARCHIVO PHP encripta_pdf.php QUE CONTIENE LAS FUNCIONES PARA DESENCRIPTAR
			require_once('encripta_pdf.php');
			echo '<td ><font size="1"><a href=permisos.php?id='.fn_encriptar($_SESSION['codigox']).'&sesion='.$sex.' title="Modificar Permisos de Usuario" target="_blank">PERMISOS</a></font></td>';
		}
		cierra($result_permiso);
		// noconex($conn);


		$sql_permiso="select * from gcodigo where idfac = 998  and codigo =".$_SESSION['codigo'];
		$result_permiso=luis($conn, $sql_permiso);
		while ($rowp = fetchrow($result_permiso,-1)){ $permiso_token = $rowp[1]; }

		if($permiso_token > 0) {
			$_SESSION['codigo_busqueda'] = 0;
			// ======================================================================
			// BOTON TOKEN DOCENTE
			// ======================================================================
			$conn_cargahoraria 	= conex();
			$sql_cargahoraria		=	"SELECT TOP 1 ISNULL(adde.IdDocenteDatoEvaluacion, 0)
															FROM Aud_DocenteDatoEvaluacion AS adde
															WHERE adde.bitEliminado = 0
															AND adde.CodUniv = ".$_SESSION['codigox']."
															AND adde.ItemEst = 1";
													// echo $sql_cargahoraria;

			$resul_cargahoraria	=	luis($conn_cargahoraria, $sql_cargahoraria);
			$row_cargahoraria 	=	fetchrow($resul_cargahoraria,-1);

			$_SESSION['codigo_busqueda'] = $_SESSION['codigox'];

			if( $row_cargahoraria[0] > 0 ){
				require_once('encripta_pdf.php');
	      echo '<td ><font size="1"><a href=token.php?id='.fn_encriptar($_SESSION['codigox']).'&sesion='.$sex.' title="Gestionar Token del Docente" target="_blank">Token Docente</a></font></td>';
			}
			// ======================================================================

		}
		cierra($result_permiso);

// ***

echo '</tr></table>';
echo  '<br><br>';
//echo 'Docente: Perez Garcia, Juan <br><br>';*/
$na=0;
$dcx=0;
	$conn=conex();
	$sql="";

      	$ga="";
		/*++++++++*/
		/*echo $_SESSION['grupa0'];
		echo '<br>';*/
		/*++++++++*/
		/*
		LA VARIABLE $_SESSION['grupa0'] TIENE UN VALOR DE 11

		*/
		/*++++++++*/
		/*echo $_SESSION['grupab'];
		echo'<br>';
		echo'++++';*/
	For ($l=1;$l<=$_SESSION['grupa0'];$l++)
	{
		if ($_SESSION['grupa'.$l]==420)
		{
			switch (strlen($_SESSION['grupb'.$l]))
			{
			case 1:
				$ga=$ga." or left(carga.iddepe,1)=".$_SESSION['grupb'.$l];
				break;
			case 3:
				$ga=$ga." or left(carga.iddepe,3)=".$_SESSION['grupb'.$l];
				break;
			case 6:
				$ga=$ga." or left(carga.iddepe,6)=".$_SESSION['grupb'.$l];
				break;
			case 9:
				$ga=$ga." or carga.iddepe=".$_SESSION['grupb'.$l];
				break;
			}
		/*echo $ga;*/
		/*El valor de la variable $ga es =
		$ga = or left(carga.iddepe,3)=314
		*/
		}
	}
	if ($ga!="")
	{
	      	//$ga=" and ( ".substr($ga,3)." )";
	      	//$sql="select codcurso, seccion, descurso, semestre, case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, carga.idcarga, carga.idcurso, carga.idsem,carga.hora from carga, curso, depe, semestre where depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem and carga.idcurso=curso.idcurso and (semestre.activo>0 or carga.activo>0) and carga.codper=".$_SESSION['codperx'].$ga." order by codcurso, seccion";
	      	$sql = "exec sp_int_ListarCargaDocente_TA ".$_SESSION['codperx'];	//Lista los cursos del docente pero para la busqueda con permisos (Tecnico A.)
			// echo $sql;
	}

	$result=luis($conn, $sql);
echo '<table border="0"><tr><th bgcolor="#DBEAF5" ><font size="1">sel</font></th><th bgcolor="#DBEAF5" ><font size="1">CodCurso</font></th><th bgcolor="#DBEAF5" ><font size="1">Seccion</font></th><th bgcolor="#DBEAF5" ><font size="1">Curso</font></th><th bgcolor="#DBEAF5" ><font size="1">Semestre</font></th><th bgcolor="#DBEAF5" ><font size="1">Escuela</font></th><th bgcolor="#DBEAF5" ><font size="1">Hrs.</font></th></tr>';
$indice = 0;
$sem = 0;
while ($row =fetchrow($result,-1))
	{
		if($indice == 0){
			$sem = $row[7];
		}
		$indice = $indice + 1;

		$na++;
                if ($ton==1){$tcol='bgcolor="#F3F9FC"';$ton=0;}else{$tcol='';$ton=1;}
                if ($_GET["dc"]==$na){$dc='checked';$dcx=$row[5];$idsem=$row[7];$seccion=$row[1];$idcurso=$row[8];}else{$dc='';}
		/*se encarga de incrementear de generar otro radio button con ayuda del javascript PELE*/
		// echo ' <tr '.$tcol.'><td><input type="radio" value="'.$na.'" name="R1"  onClick="javascript:pele('.$na.')" '.$dc.' >&nbsp;&nbsp;</td>';
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
	echo '<table border="0" cellpadding="10" ><tr><td width="132" ></td></tr></table>';
	cierra($result);
//	noconex($conn);

echo '</table>';
/*cierra la tabla con los cursos de los docentes */
/*
if (isset($_GET['tr'])==true){
	require ("genera.php");
	individual($_SESSION['codigox'], $sex, $_SESSION['codperx'],1);
}*/
/*echo $dcx;
en este caso la variable $dcx; e = 0
echo'<br> ++++';
*//////////////////
/*Este codigo genera las tablas cuando se realiza clic izquierdo sobre uno de los radio button*/
if ($_GET["dc"]>0)
{
	//$conn=conex();
	/*La variable $dcx es = idcarga*/
        $sql="select eval.ideval, eval.idarbol/10000 as nivel, case when nivel=2 then desarbol else null end as unidad, case when nivel=4 then desarbol else null end as Criterio, case when nivel=2 then peso else null end as pesou, case when nivel=4 then peso else null end as pesoc, deval, convert(char(10),feval,103) as feval, eval.idarbol, nivel, lo.ideval from eval inner join arbol on arbol.idarbol=eval.idarbol left join (select distinct eval.ideval from eval, deval where eval.ideval=deval.ideval and idcarga=".$dcx.") as lo on lo.ideval=eval.ideval where nivel in (2, 4) and left(tarbol,2)<>'TM' and idcarga=".$dcx." order by eval.idarbol ";
       // echo $sql;
        $result=luis($conn, $sql);
        echo '<table border="0" cellspacing="0"><tr><th bgcolor="#DBEAF5"><font size="2"><a href="buscab.php?sesion='.$sex.'&dc='.$_GET["dc"].'&ar=1000000 ">Unidad</a>&nbsp;&nbsp;</font></th><th bgcolor="#DBEAF5"><font size="1">Criterio</font></th><th bgcolor="#DBEAF5"><font size="1">Peso C.</font></th><th bgcolor="#DBEAF5"><font size="1">Peso U.</font></th><th bgcolor="#DBEAF5"><font size="1">Fecha</font></th><th bgcolor="#DBEAF5"><font size="1">Descripcion</font></th></tr><tr><td colspan  = 6 align = "Center" bgcolor="#DBEAF5"><font size="1px"><b><a href=# onclick="javascript:window.open(\'https://net.upt.edu.pe/buscabx.php?sesion='.$sex.'&o='.$dcx.'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false">Ver Consolidado</a></b></font></td> </tr>';
	$tu=0;
	$tc=0;
	$lv=0;
	$tcl=0;
	$cn=0;
	$nu=0;
	$nc=0;
	$in=1;
	//fetchrow($result,$in-1);
        while ($row =fetchrow($result,-1))
        {
        if ($row[1]<>$lv)
        {
        	if ($tcl>0)
        	{
        		$apr="";
        		if (number_format($tc,  2, '.', ',')!=100)
			{
				$apr='color="#FF0000"';
			}
        		echo '<tr><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"><font size="1">&nbsp;</font></td><td bgcolor="#F3F9FC"><font '.$apr.' size="1">'.number_format($tc,  2, '.', ',').' %</font></td><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"></td></tr>';
        	}
        	$lv=$row[1];
        }
        	$cn++;
        	$don='<font color="#FFFFFF" size="1">.&nbsp;</font>';
        	if ($row[9]==2){$nu++; $fx='u'.$nu;
                	//echo ' <tr>';
          }else {
           $nc++; $fx='c'.$nc;
           if($row[10]>0){
              $don='<font color="#0000FF" size="1">&nbsp;&nbsp;</font>'; $dx=10;
           }else{
           	   $dx=1;
           }
                //echo ' <tr>';
				  }
                if (strlen($row[2])>0){$v='Res.';}else{$v='';}
                //$v='';
                echo ' <td><font size="1"><a href="buscab.php?sesion='.$sex.'&dc='.$_GET["dc"].'&ar='.$row[8].'&v=0 ">'.$row[2].'</a>&nbsp;&nbsp;<a href="buscab.php?sesion='.$sex.'&dc='.$_GET["dc"].'&ar='.$row[8].'&v=1 ">'.$v.'</a></font></td>';
                echo ' <td>'.$don.'<font color="#006600" size="1">'.$row[3].'&nbsp;&nbsp;</font></td>';
				/* La variable $row[3] corresponde al campo CRITERIO que es =  a EVALUACION*/

                if ($row[9]==2){
			$tu=$tu+$row[4];
			$tc=0;
			$tcl=0;
			/* La variable $row[4] corresponde al campo PESO U. que es =  es el asignado al de la unidad*/
			echo '<td></td><td><font size="1">'.$row[4].'</font></td>';
                }else {
			$tc=$tc+$row[5];
			$tcl++;
			/* La variable $row[5] corresponde al campo PESO C. que es =  es el asignado al peso por cada evaluacion*/
			echo '<td><font size="1">'.$row[5].'</font></td><td></td>';}
						/* La variable $row[7] corresponde al campo FECHA que es el asignado al cada fecha por cada evaluacion*/
                echo ' <td><font size="1">'.$row[7].'&nbsp;&nbsp;</font></td>';
						/* La variable $row[6] corresponde al campo DESCRIPCION  que es el asignado al cada Evaluacion*/
                echo ' <td><font size="1">'.$row[6].'</font></td></tr>';

        }
        if ($tcl>0)
        	{
        		$apr="";
        		if (number_format($tc,  2, '.', ',')!=100)
			{
				$apr='color="#FF0000"';
			}
        		echo '<tr><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"><font size="1">&nbsp;</font></td><td bgcolor="#F3F9FC"><font '.$apr.' size="1">'.number_format($tc,  2, '.', ',').' %</font></td><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"></td></tr>';
        	}
        $apr="";
        if (number_format($tu,  2, '.', ',')!=100)
	{
		$apr='color="#FF0000"';
	}
        echo '<tr><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5"><font '.$apr.' size="1">'.number_format($tu,  2, '.', ',').' %</font></td><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5">&nbsp;</td></tr>';
	echo '</table>';
}
/*genera nuevo pass para los usuarios*/
if (isset($_GET['re'])==true)
{
require ("genera.php");
$pass=recoddoc($_SESSION['codigo'], $_SESSION['codigox']);
echo $pass;
}
///////////////////////////////////

if ($_GET["dc"]>0 and $_GET["ar"]==1000000)
{
echo '<br><br>';
	$ver=1;
  //$conn=conex();

  //$dc=$_GET["o"];
  $dc=$dcx;
  /*
  $idsem=20070205;
  $seccion='A';
  $idcurso=140433;

  $sqla="declare @k int select top 1 @k=1 from eval where idcarga=".$dc." and idarbol=1000000 select lower(descurso), carga.idcurso, carga.idsem, carga.idcarga, carga.iddepe, seccion, @k as ka, case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, semestre, codcurso, codcurso, rtrim(apepper)+' '+rtrim(apemper)+', '+rtrim(nomper) as nombre from carga, curso, depe, semestre, persona where persona.codper=carga.codper and curso.idcurso=carga.idcurso and  depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem and  carga.activo=1 and idcarga=".$dc;
	$resulta=luis($conn, $sqla);
	while ($row =fetchrow($resulta,-1))
	{
		$curso=$row[0];
		$idcurso=$row[1];
		$idsem=$row[2];
		$idcarga=$dc;
		$seccion=$row[5];
		$escuela=$row[7];
		$codcurso=$row[9];
		$nombre=$row[11];
	}
	cierra($resulta);
  */
  /*La variable $dc es  = idcarga*/
        $sqlx="select eval.ideval, eval.idarbol, desarbol ,peso, feval, lo.ideval, nivel from eval inner join arbol on arbol.idarbol=eval.idarbol left join (select distinct deval.ideval from deval, eval, arbol where eval.idarbol=arbol.idarbol and deval.ideval=eval.ideval and nivel=4 and idcarga=".$dc.") as lo on eval.ideval=lo.ideval where nivel in (1,2,4) and left(tarbol,2)<>'TM' and idcarga=".$dc." order by eval.idarbol ";
        //echo $sqlx;
        $result=luis($conn, $sqlx);
      	$crea=1;
      	$fiel=0;
      	$fuel=0;
      	$feel=0;
      	$fine[0][0]=0;
        while ($row =fetchrow($result,-1))
        {
        	if($row[1]==1000000)
					{
        		$crea=0;
        	}

        	if($row[5]==null and $row[1]>1000000 and $row[6]==2)
        	{
        		if($fuel>0)
        		{
        			$arb[$fuel][0]=$fiel;
        			$fiel=0;
        		}
        		$fuel++;
        		$uar[$fuel]=$row[3];
        		$uan[$fuel]=$row[1];
        		$feel++;
        	}

        	if($row[5]>0)
        	{
        		$feel++;
        		$fiel++;
        		$arb[$fuel][$fiel]=$row[1];
        		$arp[$fuel][$fiel]=$row[3];
        		if ($fiel==1){
        	 	$sele[$fuel]='p'.$fiel.'.peso as p'.$fiel.', p'.$fiel.'.nota as n'.$fiel;
        	 	$selg[$fuel]='u'.$fuel.'.p'.$fiel.', u'.$fuel.'.n'.$fiel;
        	 	//$sela[$fuel]='(case when p'.$fiel.'.nota is null then 0 else p'.$fiel.'.nota end)*(p'.$fiel.'.peso/100)';
        	 	$from[$fuel]="(select coduniv, peso, nota from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dc."  and eval.idarbol=".$row[1].") as p".$fiel."  ";
        		}else{
        	 	$sele[$fuel]=$sele[$fuel].', p'.$fiel.'.peso as p'.$fiel.', p'.$fiel.'.nota as n'.$fiel;
        	 	$selg[$fuel]=$selg[$fuel].', u'.$fuel.'.p'.$fiel.', u'.$fuel.'.n'.$fiel;
        	 	//$sela[$fuel]=$sela[$fuel].'+(case when p'.$fiel.'.nota is null then 0 else p'.$fiel.'.nota end)*(p'.$fiel.'.peso/100)';
        	 	$from[$fuel]=$from[$fuel]."left join (select coduniv, peso, nota from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dc."  and eval.idarbol=".$row[1].") as p".$fiel." on p1.coduniv=p".$fiel.".coduniv ";}
        	}
        }
        if($fuel>0)
        {
        	$arb[$fuel][0]=$fiel;
        	$fiel=0;
        }
      //fin menu
        $hay=0;
    	$selx='';
	$frox='';
	For ($i=1;$i<=$fuel;$i++)
	{

		if ($arb[$i][0]>0)
		{
			//$sqlx="select p1.coduniv, ".$sele[$i].", round(".$sela[$i].",2) as nota from ".$from[$i]." ";
			$sqlx="select p1.coduniv, ".$sele[$i]." from ".$from[$i]." ";
			//$selx=$selx.', '.$selg[$i].', u'.$i.'.nota, '.$uar[$i].' as peu';
			$selx=$selx.', '.$uar[$i].' as pu'.$i.', '.$selg[$i];
        		$frox=$frox."left join (".$sqlx.") as u".$i." on u0.coduniv=u".$i.".coduniv ";
        		$hay=1;

		}else{
			//$selx=$selx.', null as peso, null as nota, '.$uar[$i].' as peu';
			$selx=$selx.', '.$uar[$i].' as pu'.$i;
		}
	}
	//$conn=conex();
	if ($ver==1)
	{
		/*$sqlq="select nombre, u0.coduniv, estado, activo ".$selx." from (select deval.coduniv, nombre, codestado as estado, notafinal as activo from deval, eval, arbol, cursomatricula where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dc."  and eval.idarbol=1000000 and cursomatricula.coduniv=deval.coduniv and cursomatricula.itemest=deval.itemest and codestado not in (6,8,13,16,14,15,16)  and idsem=".$idsem." and idcurso=".$idcurso." and seccion='".$seccion."'  ) as u0 ".$frox." Order By nombre COLLATE Traditional_Spanish_CS_AI_KS_WS ";*/
		/*$sqlq="select nombre, u0.coduniv, estado, activo ".$selx." from (select deval.coduniv, nombre, codestado as estado, notafinal as activo from deval, eval, arbol, cursomatricula where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dc."  and eval.idarbol=1000000 and cursomatricula.coduniv=deval.coduniv and cursomatricula.itemest=deval.itemest and codestado not in (6,8,13,16,14,15,16)  and idsem=".$idsem." and idcurso=".$idcurso." and seccion='".$seccion."'  ) as u0 ".$frox." Order By nombre COLLATE Traditional_Spanish_CS_AI_KS_WS ";*/
		$sqlq="select nombre, u0.coduniv, estado, activo ".$selx." from (select deval.coduniv, nombre, IdSacEstadoEvaluacion as Estado, notafinalActa as activo from deval, eval, arbol, mat_cursomatricula where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dc."  and eval.idarbol=1000000  and mat_cursomatricula.estado=1  and mat_cursomatricula.coduniv=deval.coduniv and mat_cursomatricula.itemest=deval.itemest and mat_cursomatricula.IdSacEstadoEvaluacion in (1,2,3,4,5,6,7,10,11,12) and idsem=".$idsem." and mat_cursomatricula.IdPesPlanEstudioCurso=".$idcurso." and seccion='".$seccion."'  ) as u0 ".$frox." Order By nombre COLLATE Traditional_Spanish_CS_AI_KS_WS ";
		//echo $sqlq;
	}
	else
	{
		$sqlq="select l from (select 1 as l) as a where l>2";
	}
	$result=luis($conn, $sqlq);

//echo '<table border="0" cellpadding="2" ><tr></tr></table>';
//echo $sqlq; //esvc120710
	echo '<table border="0" cellspacing="2"><tr><th bgcolor="#DBEAF5" rowspan="3"><font size="1">N�&nbsp;&nbsp;</font></th><th bgcolor="#DBEAF5" rowspan="3"><font size="1">Codigo</font></th><th bgcolor="#DBEAF5" rowspan="3"><font size="1">Nombre</font></th><th bgcolor="#DBEAF5" colspan="'.$feel.'"><font size="1">Consolidado</font></th><th bgcolor="#DBEAF5" rowspan="3"><font size="1">Prom</font></th><th bgcolor="#DBEAF5" rowspan="3"><font size="1">&nbsp;&nbsp;Estado</font></th></tr>';
	echo '<tr>';
	$k=4;
	For ($i=1;$i<=$fuel;$i++)
	{
		$iup=$uar[$i];
		$tpu=$tpu+$uar[$i];
		echo '<td bgcolor="#DBEAF5" colspan="'.($arb[$i][0]+1).'" align="center"><font size="1">'.$iup.'%</font></td>';
		$k=$k+3;
	}
	echo '</tr>';
	echo '<tr>';
	For ($i=1;$i<=$fuel;$i++)
 	{
 		For ($j=1;$j<=$arb[$i][0];$j++)
 		{
 			echo '<td bgcolor="#DBEAF5" ><font size="1">'.$arp[$i][$j].'</font></td>';
 		}
 		echo '<th bgcolor="#DBEAF5" ><font size="1">U'.$i.'</font></td>';
 	}
	echo '</tr>';

	$na=0;
	$tcol='';
	$ton=0;
  while ($row =fetchrow($result,-1))
  {
                $na++;
                if ($ton==1){$tcol='bgcolor="#F3F9FC"';$ton=0;}else{$tcol='';$ton=1;}
                echo ' <tr '.$tcol.'><td><font size="1">'.$na.'</font></td>';
                echo ' <td '.$tcol.'><font size="1">'.$row[1].'&nbsp;</font></td>';
                echo ' <td '.$tcol.'><font size="1">'.$row[0].'</font></td>';
                $k=3;
                $pro=0;
		For ($i=1;$i<=$fuel;$i++)
		{
			$k=$k+1;
			$pre=$row[$k];
			For ($j=1;$j<=$arb[$i][0];$j++)
 			{
				$pru=$pru+($row[$k+2]*($row[$k+1]/100));
 				$k=$k+2;
 				$apr='color="#FF0000"';
				if ($row[$k]>=11)
				{
					$apr='color="#0000FF"';
				}
				if ($row[2]==11){$notaxz=0; $apr='color="#FF0000"';} else {$notaxz=$row[$k];}
				echo ' <td '.$tcol.'><font '.$apr.' size="1">&nbsp;&nbsp;'.$notaxz.'</font></td>';

				if ($row[2]==11){$pru=0;}

 				//echo ' <td '.$tcol.'><font '.$apr.' size="1">&nbsp;&nbsp;'.$row[$k].'</font></td>';
 			}
 			$apr='color="#FF0000"';
			if ($pru>=11)
			{
					$apr='color="#0000FF"';
			}

			echo ' <td '.$tcol.'><font '.$apr.' size="1">&nbsp;&nbsp;&nbsp;'.number_format($pru,2).'</font></td>';
			//$pro=$pro+round(($pru*($pre/100)),2);
			$pro=$pro+($pru*($pre/100));
			$pru=0;
		}
			//echo $pro.'x';
			//$pro=round($pro,2);
			//echo $pro.'y';
			//echo $iwin.'w';
			//$pro=round($pro,0);
			//echo $pro.'z'.'<br>';
			$pro=round(round(round($pro,8),2),0);
			$apr='color="#FF0000"';
			if ($pro>=11)
			{
				$apr='color="#0000FF"';
			}
		echo ' <td '.$tcol.'><font '.$apr.' size="1">&nbsp;&nbsp;&nbsp;'.$pro.'</font></td>';
		$estado="";
		if ($row[2]==11)
		{
			$estado="Abandono";
		}
		if ($row[2]==12)
		{
			$estado="Retirado";
		}
		echo '<td '.$tcol.'><font size="1">&nbsp;&nbsp;'.$estado.'</td>';
		echo '</tr>';
  }
	echo '</table>';
}
///////////////////////////////////
if ($_GET["dc"]>0 and $_GET["ar"]==2000000)
{
 echo '<br><br>';
	//$conn=conex();
        $sqlx="select eval.ideval, eval.idarbol, desarbol ,peso, feval, lo.ideval, nivel from eval inner join arbol on arbol.idarbol=eval.idarbol left join (select distinct deval.ideval from deval, eval, arbol where eval.idarbol=arbol.idarbol and deval.ideval=eval.ideval and nivel=4 and idcarga=".$dcx.") as lo on eval.ideval=lo.ideval where nivel in (1,2,4) and left(tarbol,2)<>'TM' and idcarga=".$dcx." order by eval.idarbol ";
        //echo $sqlx;
        $result=luis($conn, $sqlx);

	$crea=1;
      	//$sele='';
        //$from='';
      	$fiel=0;
      	$fuel=0;
        while ($row =fetchrow($result,-1))
        {
        	if($row[1]==1000000)
		{
        		$crea=0;
        	}

        	if($row[5]==null and $row[1]>1000000 and $row[6]==2)
        	{
        		//echo '<a href="notab.php?sesion='.$sex.'&ar='.$row[1].'" class="menua1">'.$row[2].'</a>';
        		if($fuel>0)
        		{
        			$arb[$fuel][0]=$fiel;
        			$fiel=0;
        		}
        		$fuel++;
        		$uar[$fuel]=$row[3];
        		$uan[$fuel]=$row[1];
        	}

        	if($row[5]>0)
        	{
        		$fiel++;
        		$arb[$fuel][$fiel]=$row[1];
        		if ($fiel==1){
        	 	$sele[$fuel]='p'.$fiel.'.peso';
        	 	$sela[$fuel]='(case when p'.$fiel.'.nota is null then 0 else p'.$fiel.'.nota end)*(p'.$fiel.'.peso/100)';
        	 	$from[$fuel]="(select coduniv, peso, nota from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dcx."  and eval.idarbol=".$row[1].") as p".$fiel."  ";
        		}else{
        	 	$sele[$fuel]=$sele[$fuel].'+p'.$fiel.'.peso';
        	 	$sela[$fuel]=$sela[$fuel].'+(case when p'.$fiel.'.nota is null then 0 else p'.$fiel.'.nota end)*(p'.$fiel.'.peso/100)';
        	 	$from[$fuel]=$from[$fuel]."left join (select coduniv, peso, nota from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dcx."  and eval.idarbol=".$row[1].") as p".$fiel." on p1.coduniv=p".$fiel.".coduniv ";}
        	}
        }
        if($fuel>0)
        {
        	$arb[$fuel][0]=$fiel;
        	$fiel=0;
        }

        $hay=0;
    	$selx='';
	$frox='';
	For ($i=1;$i<=$fuel;$i++)
	{

		if ($arb[$i][0]>0)
		{
			$sqlx="select p1.coduniv, ".$sele[$i]." as peso, round(".$sela[$i].",2) as nota from ".$from[$i]." ";
			$selx=$selx.', u'.$i.'.peso, u'.$i.'.nota, '.$uar[$i].' as peu';
        		$frox=$frox."left join (".$sqlx.") as u".$i." on u0.coduniv=u".$i.".coduniv ";
        		$hay=1;

		}else{
			$selx=$selx.', null as peso, null as nota, '.$uar[$i].' as peu';
		}
	}

	//$conn=conex();
	$sqlq="select nombre, u0.coduniv, estado, iddeval, activo ".$selx." from (select coduniv, nombre, estado, iddeval, deval.activo from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dcx."  and eval.idarbol=1000000 and deval.activo in (0,1,2,3,4,12) ) as u0 ".$frox." Order By nombre COLLATE Traditional_Spanish_CS_AI_KS_WS ";
	$result=luis($conn, $sqlq);
//echo $sqlq;


	echo '<table border="0" cellspacing="0"><tr><th bgcolor="#DBEAF5" rowspan="3"><font size="1">N�&nbsp;&nbsp;</font></th><th bgcolor="#DBEAF5" rowspan="3"><font size="1">Codigo</font></th><th bgcolor="#DBEAF5" rowspan="3"><font size="1">Nombre</font></th><th bgcolor="#DBEAF5" colspan="'.$fuel.'"><font size="1">Consolidado</font></th><th bgcolor="#DBEAF5" colspan="1"><font size="1">Prom</font></th><th bgcolor="#DBEAF5" rowspan="3"><font size="1">&nbsp;&nbsp;abandono</font></th></tr>';
	echo '<tr>';

	$k=5;
	$tpu=0;
	$caja=0;
	$vcaja=0;
	For ($i=1;$i<=$fuel;$i++)
	{
		$arp='color="#000000"';
		$iup=$uar[$i];
		$tpu=$tpu+$uar[$i];
		//$iup=resulta($result,1,($k+2));
		//$tpu=$tpu+resulta($result,1,($k+2));
		if ($iup!=100)
		{
			if ($caja==0)
			{
				$caja==1;
				$vcaja=$i;
			}
			//$arp='color="#FF0000"';
		}
		echo '<td bgcolor="#DBEAF5" ><font '.$arp.' size="1">U'.$i.'&nbsp;'.$iup.'%|</font></td>';
		$k=$k+3;
	}
	$arp='color="#0000FF"';
	$tpu=round($tpu,2);
	if ($tpu!=100)
	{
		if ($caja==0)
		{
			$caja==1;
			$vcaja=$tpu;
		}
		$arp='color="#FF0000"';
	}
	echo '<td bgcolor="#DBEAF5" rowspan="2"><font '.$arp.' size="1">&nbsp;&nbsp;'.$tpu.'%</font></td>';
	echo '</tr>';

	$k=5;
	$tpu=0;
	$caja=0;
	$vcaja=0;

	echo '<tr>';
	For ($i=1;$i<=$fuel;$i++)
	{
		$arp='color="#000000"';
		$iup=resulta($result,0,($k));
		$tpu=$tpu+resulta($result,0,($k+2));
		if ($iup!=100)
		{
			if ($caja==0)
			{
				$caja==1;
				$vcaja=$i;
			}
			$arp='color="#FF0000"';
		}
		echo '<td bgcolor="#DBEAF5" ><font '.$arp.' size="1">&nbsp;'.$iup.'%</font></td>';
		$k=$k+3;
	}
	echo '</tr>';


	echo '<input type="hidden" name="t4" maxlength="32" value="'.$vcaja.'" >';
	$na=0;
	$tcol='';
	$ton=0;
        while ($row =fetchrow($result,-1))
        {
                $na++;
                if ($ton==1){$tcol='bgcolor="#F3F9FC"';$ton=0;}else{$tcol='';$ton=1;}
                echo ' <tr '.$tcol.'><td><font size="1">'.$na.'</font></td>';
                echo ' <td '.$tcol.'><font size="1">'.$row[1].'&nbsp;</font></td>';
                echo ' <td '.$tcol.'><font size="1">'.$row[0].'</font></td>';
                $k=6;
                $pro=0;
		For ($i=1;$i<=$fuel;$i++)
		{
			$apr='color="#FF0000"';
			if ($row[$k]>=11)
			{
				$apr='color="#0000FF"';
			}
			echo ' <td '.$tcol.'><font '.$apr.' size="1">&nbsp;&nbsp;'.$row[$k].'</font></td>';
			$pro=$pro+($row[$k]*($row[$k+1]/100));
			$k=$k+3;
		}
			$apr='color="#FF0000"';
			if (number_format($pro,  0, '.', ',')>=11)
			{
				$apr='color="#0000FF"';
			}
		echo ' <td '.$tcol.'><font '.$apr.' size="1">&nbsp;&nbsp;'.number_format($pro, 0, '.', ',').'</font></td>';
		$estado="RT";
		if ($row[4]!=12)
		{
			$estado='';
			if ($row[2]>0)
			{
				$estado='AB';
			}
		}
		echo '<td '.$tcol.'><font size="1">&nbsp;&nbsp;'.$estado.'</td>';
		echo '</tr>';
        }

	echo '</table>';
}

/////////////
if ($_GET["dc"]>0 and $_GET["ar"]>1000000 )
{
 echo '<br><br>';

				if ($_GET["v"]>0)
				{
					$ve='is null';
				}
				else
				{
					$ve='in (1,2,3,4,12)';
				}

				//$conn=conex();
        $sql="declare @ok int select top 1 @ok=1 from deval, eval where eval.ideval=deval.ideval and idcarga=".$dcx." and eval.idarbol/1000=".$_GET["ar"]."/1000 select eval.ideval, eval.idarbol, desarbol ,peso, feval, @ok as ok, nivel, deval from eval inner join arbol on arbol.idarbol=eval.idarbol where (nivel=2 or (nivel=4 and eval.idarbol/1000=".$_GET["ar"]."/1000)) and left(tarbol,2)<>'TM' and idcarga=".$dcx." order by eval.idarbol ";

        $result=luis($conn, $sql);
				$sele='';
        $from='';
      	$fiel=0;
      	$hay=0;
        while ($row =fetchrow($result,-1))
        {
        	if ($row[5]>0)
        	{
			$hay=1;
        	}
        	if ($row[1]==$_GET["ar"] and $row[6]==2){
        	$sely='notab.php?sesion='.$sex.'&ar='.$row[1];
        	$selyx='notabx.php?sesion='.$sex.'&ar='.$row[1];
        	$seln=$row[2];
        	//echo '<a href="'.$sely.'" class="menub1">'.$row[2].'</a>';
        	}elseif($row[1]<>$_GET["ar"] and $row[6]==2){
        	//echo '<a href="notab.php?sesion='.$sex.'&ar='.$row[1].'" class="menua1">'.$row[2].'</a>';
        	}elseif( $row[6]==4){
        	$fiel++;
        	$sele=$sele.', p'.$fiel.'.peso, p'.$fiel.'.nota ,p'.$fiel.'.tarbol ';
        	$from=$from."left join (select coduniv, peso, nota, tarbol from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dcx."  and eval.idarbol=".$row[1].") as p".$fiel." on p0.coduniv=p".$fiel.".coduniv ";
        	//echo '<a href="notac.php?sesion='.$sex.'&ar='.$_GET["ar"].'&er='.$row[1].'" class="menux2"><font size="1">'.$row[2].'</font></a>';
        	$cpe[$fiel]=$row[3];
        	}
        }
        //
        $sql="select nombre, p0.coduniv ".$sele." from (select coduniv, nombre from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dcx."  and eval.idarbol=1000000  ) as p0 ".$from." order by nombre COLLATE Traditional_Spanish_CS_AI_KS_WS ";
        //echo $sql;
        $result=luis($conn, $sql);

	echo '<table border="0" cellspacing="0"><tr><th bgcolor="#DBEAF5" rowspan="2"><font size="1">N�&nbsp;&nbsp;</font></th><th bgcolor="#DBEAF5" rowspan="2"><font size="1">Nombre</font></th><th bgcolor="#DBEAF5" rowspan="2"><font size="1">Codigo</font></th><th bgcolor="#DBEAF5" colspan="'.$fiel.'"><font size="1">'.$seln.'</font></th><th bgcolor="#DBEAF5" rowspan="2"><font size="1">Prom</font></th></tr>';
	echo '<tr>';
	$k=2;
	For ($i=1;$i<=$fiel;$i++)
	{
		echo '<td bgcolor="#DBEAF5" ><font size="1">'.resulta($result,0,($k+2)).'&nbsp;'.$cpe[$i].'%|</font></td>';
		$k=$k+3;
	}
	echo '</tr>';


	$na=0;
	$tcol='';
	$ton=0;
        while ($row =fetchrow($result,-1))
        {
                $na++;
                if ($ton==1){$tcol='bgcolor="#F3F9FC"';$ton=0;}else{$tcol='';$ton=1;}
                echo ' <tr '.$tcol.'><td><font size="1">'.$na.'</font></td>';
                echo ' <td '.$tcol.'><font size="1">'.$row[0].'&nbsp;</font></td>';
                echo ' <td '.$tcol.'><font size="1">'.$row[1].'</font></td>';
                $k=3;
                $pro=0;
		For ($i=1;$i<=$fiel;$i++)
		{
			$apr='color="#FF0000"';
			if ($row[$k]>=11)
			{
				$apr='color="#0000FF"';
			}
			echo ' <td '.$tcol.'><font '.$apr.' size="1">&nbsp;&nbsp;'.$row[$k].'</font></td>';
			$pro=$pro+($row[$k]*($row[$k-1]/100));

			/*if ($row[2]==11){$notaxz=0; $apr='color="#FF0000"';} else {$notaxz=$row[$k];}
			echo ' <td '.$tcol.'><font '.$apr.' size="1">&nbsp;&nbsp;'.$notaxz.'</font></td>';
			if ($row[2]==1){$pro=0;} else {$pro=$pro+round(($row[$k]*($row[$k+1]/100)),2);}						*/

			$k=$k+3;
		}
		$apr='color="#FF0000"';
		if (number_format($pro, 2, '.', ',')>=11)
		{
			$apr='color="#0000FF"';
		}
		echo ' <td '.$tcol.'><font '.$apr.' size="1">&nbsp;&nbsp;'.number_format($pro, 2, '.', ',').'</font></td>';
		echo '</tr>';
        }
	echo '</table>';

}
noconex($conn);
?>
</form>
<?
/*AGREGUE LA VARIALBE $file_php PARA DETERMINAR SI ESTOY EN EL ARCHIVO estadistica.php O buscab.php*/

$file_php=0;

if (isset($_GET['tr'])==true){

	require ("genera.php");
	/*individual($_SESSION['codigox'], $sex, $_SESSION['codperx'],1);*/
	individual($_SESSION['codigox'], $sex, $_SESSION['codperx'],1,$file_php,$sem);

							}

	/*AGREGUE ESTA FUNCION PARA PODER EDITAR EL TRABAJO INDIVIDUAL*/
/*if (isset($_GET['tr'])==true){
	require ("genera.php");

	individual_editar($_SESSION['codigox'], $sex, $_SESSION['codperx'],1);
							}*/
	/*HASTA AQUI*/

?>
</div>

</body>
<!--AGREGUE ESTA LINEA DE CODIGO PARA EL CALENDARIO-->
<script type="text/javascript" src="dhtmlgoodies_calendar.js?random=20060118"></script>
<!--HASTA AQUI CALENDARIO-->

<script language="JavaScript" >
function pele(op)
{
	//document.frmbuscab.consul.value=op;
	location.href="buscab.php?sesion=<? echo $sex ?>&dc="+op;
}
function msj()
{
	//var d = document.forms[1].elements[2];
	var d = document.forms[1].vdacti;
	//var h = document.forms[1].elements[7];
	var h = document.forms[1].vhoras;

	if (d.value=="" || (h.value)=="")
	{
		alert ('Debe ingresar el Detalle de Actividad y las Horas. ');
		//alert (d.name);
		//alert (h.name);
	}
	else
	{
		document.frmindiv.vagregar.value="Agregar";
		document.frmindiv.submit();
	}
}

</script>
<!--AGREGO ESTO PARA VALIDAR LOS BOTONES DE EDITAR , ELIMINAR-->
	<script LANGUAGE="JavaScript">

    function confirmSubmit()
    {
    var agree=confirm("�Esta seguro que desea editar la actividad?");
    if (agree)
        return true ;
    else
        return false ;
    }

	function confirmdelete()
    {
    var agree=confirm("�Esta seguro que desea eliminar la actividad?");
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
</html>
