<?
// require ("admins.php");
$sex=$_GET["sesion"];
require ("token_admin.php");
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

$idsem = $_GET["x"];
if (isset($_GET["tx"])){
	$esSemestreTaex = $_GET["tx"];
}
else {
	$esSemestreTaex = 0;
}
// $esSemestreTaex = $_GET["tx"];
//echo 'asdf'.$_GET["x"];

$_SESSION['token_validado'] = 0; // VARIABLE PARA LA VALIDACION DEL TOKEN DEL DOCENTE

/*+++++++++++++++++++++++++++++++++++++++++*/
//echo $idsem;

if ($_POST["variable2"]=="registro")
{
	$achivos_adjuntos='';
	$i=0;
	do
	{
		if($_FILES['archivo']['tmp_name'][$i] !="")
		{

			$aleatorio = rand();
			$nuevonombre = $_SESSION['codigo'].$idsem.".xls";
			//TODO: posible error en individualex
			move_uploaded_file($_FILES['archivo']['tmp_name'][$i],'trabajoindividualex/'.$nuevonombre);

			$conn=conex();
			$sql="exec trabiagregardoc_v2 ".$idsem.",".$_SESSION['codigo'].", '".$nuevonombre."'";
			// echo $sql;
			$result=luis($conn, $sql);
			cierra($result);
			noconex($conn);
			?>
			<script language='javascript'>
            alert("Registrado con Exito");
            window.location="<?php $_SERVER['HTTP_REFERER']; ?>";
            </script>
			<?php
		}
		$i++;
	} while ($i < 10);
}


/*recibe los campos enviados de las cajas de texto, despues de haber realizado clic en el boton AGREGAR*/
if ($_POST["vagregar"]=="Agregar")
{

	session_start();
	$vacti=$_POST["vacti"];
	$vdacti=$_POST["vdacti"];
	$vimporta=$_POST["vimporta"];
	$vmedida=$_POST["vmedida"];
	$vcant=$_POST["vcant"];
	$vhoras=$_POST["vhoras"];
	$vcalif=$_POST["vcalif"];
	$vmeta=$_POST["vmeta"];
	$_SESSION['codigox']=$_POST["coduni"];
	/*NUEVAS VARIABLES  - FECHA INICIO , FECHA FINAL y EL IDDEPE*/
	$vdatebox=$_POST["datebox"];
	$vdatebox2=$_POST["datebox2"];

	$viddepe=$_POST["viddepe"];

	$vcanthoras=$_POST["vcanthoras"];
	$vidsemestre=$idsem;
	//echo $vidsemestre;
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


	if ($vacti == "Administrativa")
	{
		if ($vcalif==7 or $vcalif==8)
		// if ($vcalif==7 or $vcalif==8)
		{

			$sql="exec trabiagregar_v2 ".$_SESSION['codigox'].", '".$vacti."', '".$vdacti."', '".$vimporta."', '".$vmedida."',".$vcant.", ".$vhoras.", ".$vcalif.", '".$vmeta."', '".$vdatebox."', '".$vdatebox2."', '".$viddepe."', '".$vcanthoras."', ".$idsem."";

		}
		else
		{
			?>
			<script language='javascript'>
				alert("La actividad Administrativa solo puede tener calificacion [Administración] o [Jefatura]");
				window.location="<?php $_SERVER['HTTP_REFERER']; ?>";
			</script>
			<?php
		}
	}
	else
	{

		$sql="exec trabiagregar_v2 ".$_SESSION['codigox'].", '".$vacti."', '".$vdacti."', '".$vimporta."', '".$vmedida."',".$vcant.", ".$vhoras.", ".$vcalif.", '".$vmeta."', '".$vdatebox."', '".$vdatebox2."', '".$viddepe."', '".$vcanthoras."', ".$idsem."";
	}

	//echo $sql;
	//echo "asdfsdafasdfsdafsadfasdfasdfasdf";
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
		$_SESSION['codigox']=$_POST["coduni"];
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
		$_SESSION['codigox']=$_POST["coduni"];


		$idtrab=$vdactividad_historial;

		date_default_timezone_set('America/Lima');
		$dia=date("d/m/Y");

		$conn=conex();

		$sql="exec sp_add_trab_historial ".$_SESSION['codigox'].", '".$idtrab."', '".$vnominfo_historial."', '".$vdirigido_historial."', '".$vcargo_historial."', '".$vremitente_historial."', '".$vdetalle_historial."', '".$vporcentaje_historial."', '".$dia."'";
		// var_dump($sql);
		// exit;
		$result=luis($conn, $sql);

		cierra($result);
		noconex($conn);

		//ESTE SP ACTULIZA EL porcentaje DE LA TABLA trab Y LA TABLA trab_historial

	}
	// noconex($conn);
	/**HASTA AQUI AGREGA A LA TABLA trab_historial/
	/*++++++++*/

/*HASTA AQUI EDITA EL ESTADO DE TODAS LAS ACTIVIDADES QUE TENGAN EL MISMO idsem Y codigo*/
/*ELIMINA el TRABAJO INDIVIDUAL*/

if ($_POST["vn"]>0)
{
	For ($i=1;$i<=$_POST["vn"];$i++)
	{
		if ($_POST["de".$i]=="Eliminar")
		{
			$_SESSION['codigox']=$_POST["coduni"];
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
			noconex($conn);
		}
		else if  ($_POST["dedit".$i]=="Editar")
		{
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
			$vdocumento_editar=$_POST["vdocumento_editar".$i];
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

			if (isset($vdocumento_editar)==false){$vdocumento_editar="";}
			$_SESSION['codigox']=$_POST["coduni"];
			$conn=conex();
			date_default_timezone_set('America/Lima');
			$fecha_registro=date("d/m/Y");
			$sql_editar="exec sp_editar_trabindiv ".$_SESSION['codigox'].", '".$_POST["vi".$i]."', '".$vacti_editar."', '".$vdacti_editar."', '".$vimporta_editar."', '".$vmedida_editar."',".$vcant_editar.", ".$vhoras_editar.", ".$vcalif_editar.", '".$vmeta_editar."', '".$vdatebox_editar."', '".$vdatebox2_editar."', '".$vporcentaje_editar."'";

			$result=luis($conn, $sql_editar);
			cierra($result);
			noconex($conn);

			if ($_POST["modi".$i]=="Modificar Estado")
			{
				$idtrab=$_POST["vi".$i];
				$_SESSION['codigox']=$_POST["coduni"];
				$vestado_editar=$_POST["vestado_editar".$i];

				if (isset($vdocumento_editar)==false){$vdocumento_editar="";}
				$conn=conex();
				if($vestado_editar>0)
				{
					$sql_editar="exec sp_editar_trabindiv_estado ".$_SESSION['codigox'].", '".$idtrab."', '".$vestado_editar."'";
					$result=luis($conn, $sql_editar);
					cierra($result);
				}
				else
				{
					$sql_editar="exec sp_editar_trabindiv_estado ".$_SESSION['codigox'].", '".$idtrab."', '".$vestado_editar."'";
					$result=luis($conn, $sql_editar);
					cierra($result);
				}
				noconex($conn);
				date_default_timezone_set('America/Lima');
				$fecha_registro=date("d/m/Y");
			}
		}
		else if  ($_POST["delim".$i]=="Finalizar")
		{
			$idtrab=$_POST["vi".$i];
			$capidsem = $idsem;
			$capcodigo = $_POST["coduni"];
			$estado = 1;

			$conn=conex();
			$sql_finalizar="exec sp_registrar_trabindiv_finalizacion ".$idtrab.",".$capidsem.",".$capcodigo.",".$estado;

			$result=luis($conn, $sql_finalizar);
			cierra($result);
			noconex($conn);
		}
		else if  ($_POST["rever".$i]=="Revertir")
		{
			$idtrab=$_POST["vi".$i];
			$capidsem = $idsem;
			$capcodigo = $_POST["coduni"];
			$estado = 0;

			$conn=conex();
			$sql_revert="exec sp_update_trabindiv_reversion ".$idtrab.",".$capidsem.",".$capcodigo.",".$estado;

			$result=luis($conn, $sql_revert);
			cierra($result);
			noconex($conn);
		}
	}
}

$gok=0;

For ($l=1;$l<=$_SESSION['grupa0'];$l++)
{
	if ($_SESSION['grupa'.$l]==200){$gok=1;}
}

if ($gok==0){header("Location: logout.php?sesion=".$sex);}
if ($_SESSION['tipo']!=3){header("Location: cambio.php?sesion=".$sex);}

// echo $_SESSION['check'];
if ($_SESSION['check']>0)
{
	$conn=conex();
 	/*$sql="select carga.idcarga, idsem, idcurso, seccion, idarbol, iddepe from carga left join eval on eval.idcarga=carga.idcarga and idarbol=1000000 where ((carga.idsem in (select idsem from semestre where activo>0)) and carga.activo=1) and carga.codigo=".$_SESSION['codigo'];*/
 	$sql="SELECT carga.idcarga, idsem, idcurso, seccion, idarbol, iddepe
		FROM   carga
		       LEFT JOIN eval ON  eval.idcarga = carga.idcarga
		            AND idarbol = 1000000
		WHERE  (
		           (
		               carga.idsem IN (SELECT idsem FROM   semestre WHERE  activo > 0)
		           )
		           AND carga.activo = 1
		       )
		       AND carga.codigo =  ".$_SESSION['codigo']."
		UNION ALL
		SELECT cch.IdCargaCROM idCarga, cs.IdSem,cch.IdPesPlanEstudioCurso IdCurso,cch.Seccion,e.idarbol,cch.IdEscuela
		FROM Cho_CargaHoraria cch
		LEFT JOIN eval e ON cch.IdCargaCROM = e.idcarga AND e.idarbol = 1000000
		INNER JOIN Cho_PeriodoPersona cpp ON cpp.IdPeriodoPersona = cch.IdPeriodoPersona
		INNER JOIN Cho_Persona cp ON cp.IdChoPersona = cpp.IdChoPersona
		INNER JOIN ucodigo u ON u.CodPer = cp.CodPer
		INNER JOIN Cho_Semestre cs ON cs.IdChoSemestre = cpp.IdChoSemestre
		INNER JOIN SEMESTRE s ON s.IdSem = cs.IdSem
		WHERE u.coduniv = ".$_SESSION['codigo']." AND cch.Estado = 1 AND s.Activo = 1";
 	//echo $sql;
	$result=luis($conn, $sql);
	while ($row =fetchrow($result,-1))
	{
		if ($row[4]>0)
		{

			$sqla="exec uretiro ".$row[0].", ".$row[1].", ".$row[2].", '".$row[3]."', ".$row[5];
			$sqlb="exec inuevo ".$row[0].", ".$row[1].", ".$row[2].", '".$row[3]."', ".$_SESSION['codigo'].", ".$row[5];
			$vacioa=luis($conn, $sqla);
			$vaciob=luis($conn, $sqlb);
		}

	}
	$_SESSION['check']=0;

}

// INICIO - ESTE POST "R2" YA NO SE USA
if ($_POST["R2"]>0)
{
  $dc=$_SESSION['car'.$_POST["R2"]];
	$conn=conex();
 	//$sql="declare @k int select top 1 @k=1 from eval where idcarga=".$dc." and idarbol=1000000 select lower(descurso)as nom, carga.idcurso, carga.idsem, carga.idcarga, carga.iddepe, seccion, @k as ka, case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, semestre, codcurso from carga, curso, depe, semestre where curso.idcurso=carga.idcurso and  depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem and ((carga.idsem in (select idsem from semestre where activo>0)) or carga.activo=1) and carga.codigo=".$_SESSION['codigo']." and idcarga=".$dc;
	/*$sql="declare @k int select top 1 @k=1 from eval where idcarga=".$dc." and idarbol=1000000

		select lower(descurso)as nom, carga.idcurso, carga.idsem, carga.idcarga, carga.iddepe, seccion, @k as ka,
		case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, semestre, codcurso
		from carga, curso, depe, semestre
		where curso.idcurso=carga.idcurso and  depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem
		and ((carga.idsem in (select idsem from semestre where activo>0)) or carga.activo=1) and carga.codigo=".$_SESSION['codigo']." and idcarga=".$dc."

		UNION ALL

		SELECT LOWER(c.DesCurso) AS nom,
	       cch.IdCurso,
	       s.IdSem,
	       cch.IdCargaCROM   AS 'idcarga',
	       cch.IdEscuela     AS 'iddepe',
	       cch.SeccionCurso  AS 'seccion',
	       @k                AS ka,
	       CASE
	            WHEN PATINDEX('Escuela profesional de%', d.descrip) > 0 THEN
	                 RIGHT(RTRIM(d.descrip), LEN(RTRIM(d.descrip)) -23)
	            ELSE d.descrip
	       END               AS descri,
	       s.Semestre,
	       c.CodCurso
		FROM   Cho_CargaHoraria cch
	       INNER JOIN Cho_PeriodoPersona cp
	            ON  cp.IdPeriodoPersona = cch.IdPeriodoPersona
	       INNER JOIN Cho_Semestre cs
			   LEFT JOIN SEMESTRE s
					ON  s.IdSem = cs.IdSem
	            ON  cs.IdChoSemestre = cp.IdChoSemestre
	       INNER JOIN CURSO c
	            ON  c.idcurso = cch.IdCurso
	       INNER JOIN depe d
	            ON  d.iddepe = cch.IdEscuela
	       INNER JOIN Cho_Persona cpp
	            ON  cpp.IdPersona = cp.IdPersona
	       LEFT JOIN ucodigo u
	            ON  cpp.CodPer = u.codper
		WHERE  u.coduniv = ".$_SESSION['codigo']."
	       AND cch.IdCargaCROM = ".$dc."
	       AND (cch.Estado = 1 AND s.Activo > 0)";*/
		//echo $dc;
	       $sql="declare @k int select top 1 @k=1 from eval where idcarga=".$dc." and idarbol=1000000

		select lower(descurso)as nom, carga.idcurso, carga.idsem, carga.idcarga, carga.iddepe, seccion, @k as ka,
		case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, semestre, codcurso
		from carga, curso, depe, semestre
		where curso.idcurso=carga.idcurso and  depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem
		and ((carga.idsem in (select idsem from semestre where activo>0)) or carga.activo=1) and carga.codigo=".$_SESSION['codigo']." and idcarga=".$dc."

		UNION ALL

		SELECT LOWER(c.Asignatura) AS nom,
	       cch.IdPesPlanEstudioCurso IdCurso,
	       s.IdSem,
	       cch.IdCargaCROM   AS 'idcarga',
	       cch.IdEscuela     AS 'iddepe',
	       cch.Seccion,
	       @k                AS ka,
	       CASE
	            WHEN PATINDEX('Escuela profesional de%', d.descrip) > 0 THEN
	                 RIGHT(RTRIM(d.descrip), LEN(RTRIM(d.descrip)) -23)
	            ELSE d.descrip
	       END               AS descri,
	       s.Semestre,
	       c.CodigoCurso CodCurso
		FROM   Cho_CargaHoraria cch
	       INNER JOIN Cho_PeriodoPersona cp ON  cp.IdChoPeriodoPersona = cch.IdChoPeriodoPersona
	       INNER JOIN Cho_Semestre cs  ON  cs.IdChoSemestre = cp.IdChoSemestre
	       LEFT JOIN SEMESTRE s ON  s.IdSem = cs.IdSem
	       INNER JOIN Pes_PlanEstudioCurso c ON c.IdPesPlanEstudioCurso = cch.IdPesPlanEstudioCurso
	       INNER JOIN depe d ON  d.iddepe = cch.IdEscuela
	       INNER JOIN Cho_Persona cpp ON  cpp.IdChoPersona = cp.IdChoPersona
	       LEFT JOIN ucodigo u ON  cpp.CodPer = u.codper
		WHERE  u.coduniv = ".$_SESSION['codigo']."
	       AND cch.IdCargaCROM = ".$dc."
	       AND (cch.Estado > 0 AND s.Activo > 0)";

// echo $sql;
	$result=luis($conn, $sql);
	
	while ($row =fetchrow($result,-1))
	{
		$_SESSION['curso']=$row[0];
		$_SESSION['idcurso']=$row[1];
		$_SESSION['idsem']=$row[2];
		$_SESSION['idcarga']=$dc;
		$_SESSION['iddepe']=$row[4];
		$_SESSION['seccion']=$row[5];
		$_SESSION['escuela']=$row[7];
		$_SESSION['semestre']=$row[8];
		$_SESSION['codcurso']=$row[9];
		$_SESSION['conso']=0;
		$cod_cur=$row[1];
		$cod_doc=$_SESSION['codigo'];
		$cod_p=$_SESSION['codper'];
		if ($row[6]>0)
		{
			$_SESSION['conso']=1;
		}
	}
	$semestre_ac = $_SESSION['semestre'];

	//echo 'header("Location: admin_intranet/carga/index.php?sesion="'.$sex.'"&cod="'.$cod_cur.'"&codd="'.$_POST['coddoc'].')';
	echo "
	<script type='text/javascript'>

           window.location='admin_intranet/carga/index.php?sesion=$sex&cod=$cod_cur&codd=$cod_doc&codp=$cod_p&seme=$semestre_ac'

	</script>
  ";

}
// FIN - ESTE POST "R2" YA NO SE USA


// var_dump($_POST["R1"]);
if ($_POST["R1"]>0)
{
	$dc = $_SESSION['car'.$_POST["R1"]];
	$dcEsTaex = $_SESSION['taex'.$_POST["R1"]];
	$conn=conex();

 	//$sql="declare @k int select top 1 @k=1 from eval where idcarga=".$dc." and idarbol=1000000 select lower(descurso), carga.idcurso, carga.idsem, carga.idcarga, carga.iddepe, seccion, @k as ka, case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, semestre, codcurso from carga, curso, depe, semestre where curso.idcurso=carga.idcurso and  depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem and ((carga.idsem in (select idsem from semestre where activo>0)) or carga.activo=1) and carga.codigo=".$_SESSION['codigo']." and idcarga=".$dc;

	if(!$dcEsTaex){
		$sql = "sp_int_ListarIdCargaDocente ".$_SESSION['codigo'].", ".$dc;
	}else{
		$sql = "sp_int_ListarIdCargaDocenteTaex ".$_SESSION['codigo'].", ".$dc;
	}
	// echo $sql;
	// exit;
	///Modificado por Gary
	$result=luis($conn, $sql);
	while ($row =fetchrow($result,-1))
	{
		$_SESSION['curso']=$row[0];
		$_SESSION['idcurso']=$row[1];
		$_SESSION['idsem']=$row[2];
		$_SESSION['idcarga']=$dc;
		$_SESSION['iddepe']=$row[4];
		$_SESSION['seccion']=$row[5];
		$_SESSION['escuela']=$row[7];
		$_SESSION['semestre']=$row[8];
		$_SESSION['codcurso']=$row[9];
		$_SESSION['validarb']=$row[10];
		$_SESSION['conso']=0;
		if ($row[6]>0)
		{
			$_SESSION['conso']=1;
		}
		$_SESSION['estaex']=$dcEsTaex;
	}
	header("Location: unidad.php?sesion=".$sex);
}
else
{
	$conn=conex();

	//$sql="select codcurso, seccion, descurso, semestre, case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, carga.idcarga,carga.hora from carga, curso, depe, semestre where depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem and carga.idcurso=curso.idcurso and (semestre.activo>0 and carga.activo>0) and carga.codigo=".$_SESSION['codigo']."  order by codcurso, carga.seccion";

	$sql = "exec sp_int_ListarCargaDocenteNuevo ".$_SESSION['codper'].", ".$idsem.", ".$esSemestreTaex;
	MostrarTextoAdmin("Listado de Cursos de Carga", $sql);
	// exit;
	// if (in_array($_SESSION['codigo'], $admins)) {
	// 	// echo $sql;
	// }
	//Modificado por gary
	$result=luis($conn, $sql);
	$ji=0;
	while ($row =fetchrow($result,-1))
	{
		$ji++;
		$_SESSION['car'.$ji]=$row[5];
		$_SESSION['taex'.$ji]=$row[9];
		$_SESSION['idsemindiv']=$row[7];
	}
	$_SESSION['car0']=$ji;
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
</head>
<body >

<?


/*MENU LATERAL IZQUIERDO*/
   echo ('<link href="site.css" type="text/css" rel="stylesheet">');
   helpx(10701,$sex);

   echo ('<div id="root-row2">');
   	echo ('<div id="crumbs"> ');
	echo ('</div>');
   echo ('</div>');

     echo ('<div id="nav">');
     echo ('<div id="menu-block">');
      echo ('<a class="menux1" href="inicio.php?sesion='.$sex.'">Inicio</a>');
      // if( $_SESSION['codigo'] == 298907 ){
	      echo ('<div id="side-block"></div>');
				echo ('<a class="menux1" href="dusuario.php?sesion='.$sex.'">Datos Docente</a>');
			// }
			// echo $_SESSION['grupa0'];
      For ($l=1;$l<=$_SESSION['grupa0'];$l++)
	{
        	switch ($_SESSION['grupa'.$l]) {
        	case 100:
        		echo '<div id="side-block"></div>';
      			echo '<a class="menux1" href="alumno.php?sesion='.$sex.'">Alumno</a>';
        		break;
        	case 200:
        		echo '<div id="side-block"></div>';
				echo '<div id="side-block"></div>';
      			echo '<a class="menuy1" href="carga.php?sesion='.$sex.'">Carga</a>';
				echo '<div id="side-block"></div>';
				$sql="sp_LISTADO_SEMESTRE_CARGA_NUEVO ".$_SESSION["codper"];
				MostrarTextoAdmin("Listado de Semestres", $sql);
				// echo $sql;
				// if (in_array($_SESSION['codigo'], $admins)) {
				// 	var_dump ($sql);
				// }
				$resulta=luis($conn, $sql);
				// var_dump ($resulta);
				//toritotito
				while ($row=fetchrow($resulta,-1))
				{
// 
					echo '<i><a class="menux1 tooltip" style="font-size:11px; text-align:left; background:#a6a6ac; color:white; padding-top:5px;" href="carga.php?sesion='.$sex.'&x='.$row[0].'&tx='.$row[3].'">'.$row[1].'  <span style="font-size:10px; padding-left:2px;">('.$row[0].')</span><span class="tooltiptext">'.$row[2].'</span></a></i>';
					echo '<div id="side-block"></div>';
				}
				cierra($resulta);
				echo '<div id="side-block"></div>';
      			echo '<a class="menux1" href="Elearning.php?sesion='.$sex.'" target="_blank">Aula Virtual</a>';


			//if ($_SESSION['padl']==1)
			//{
      			 echo '<div id="side-block"></div>';
      			 echo '<a class="menux1" href="asistenciap.php?sesion='.$sex.'">Parte Asistencia</a>';
      			//}

        		break;
    		case 300:
      			echo '<div id="side-block"></div>';
      			echo '<a class="menux1" href="estadistica.php?sesion='.$sex.'">Estadistica</a>';
        		break;
    		case 400:
      			echo '<div id="side-block"></div>';
		      	echo '<a class="menux1" href="busca.php?sesion='.$sex.'">Busqueda</a>';
        		break;
    		case 700:
			echo '<div id="side-block"></div>';
		      	echo '<a class="menux1" href="biblioteca.php?sesion='.$sex.'" target="_blank">Biblioteca</a>';
		      	break;

		}
   	}

// ************* 	LOGO 	******************
			echo '<div id="side-block"></div>';
		      	echo '<br>';
		      	echo '<br>';
				// logo WIFI
	//echo '<br><center><a href="alumno.php?sesion='.$sex.'&wf=1" ><img width="74" height="50"  alt="Clic para generar su clave Wifi" src="imagenes/logo_wifi.png" border=0><br><font size =2px> GENERAR CLAVE WIFI</font></a></center><b>';

	if ($_GET['wf']==1){
		echo '<br>';
		echo '<center>';
		echo $_SESSION['wifi'].'</b><br>';
		echo '</center>';
	}

// ************* 	HASTA AQUI LOGO 	******************
     echo ('</div>');
    echo ('</div>');

echo '<div id="contents">';
echo '<form method="POST" action="carga.php?sesion='.$sex.'" name="frminicio">';
echo '<INPUT TYPE="hidden" NAME="op" value="0" >';
echo '<table border="0" width="100%">';
echo '<tr>';
			
$conn=conex();
if(!$esSemestreTaex){
	$sql="SELECT TOP 1 s.Semestre, Observ FROM SEMESTRE s WHERE s.IdSem=".$idsem;
}else{
	$sql="SELECT TOP 1 s.Semestre, Nombre FROM Tax_SemestreConsolidado s WHERE s.IdSem=".$idsem;
}

$resultse=luis($conn, $sql);
while ($rowdir =fetchrow($resultse,-1))
{
	$semestre=$rowdir[0];
	$obssemestre=$rowdir[1];
	// echo '<br>Iddepe del director: ';
	// echo $iddepedirec;
}

echo '<td width="550">';
if($idsem > 0){
	echo '<font size="2"><strong>Semestre:</strong> ('. $idsem .') - '. $obssemestre .'</font><br>';
}
echo '<font size="2"><strong>Docente:</strong> '.$_SESSION['name'].'</font>';
echo '</td>';
//echo '<td><font size="2"><a target="_blank" href="cargaa.php?sesion='.$sex.'" >Resultado encuesta</a></font></td>';


if ($_GET["x"]=='')
{
	echo "<font size='4' style='color: 102368;'>Hacer clic en el SEMESTRE del menu izquierdo.</font>";
	exit;
}

if(!$esSemestreTaex){
	echo '<td><font size="1"><a style="font-size:12px;" href="carga.php?tr=1&sesion='.$sex.'&x='.$idsem.'" >TRABAJO INDIVIDUAL '.$obssemestre.' </a></font>  <font size="1" face="Arial">
									<blink>
										<a href="documentos/PIT_Docente/ActividadesPITGA_V2.pdf" target="_blank">
											<center style="color: red;">( Guía PIT )</center>
										</a>
									</blink>
							</font></td>';
}

// *** LINK DE REPORTE DE ENCUESTA DE ODESAR ***
//$conn=conex();
	//$sqldir="select top 1 carga.idcarga, idsem, idcurso, seccion, idarbol, iddepe from carga left join eval on eval.idcarga=carga.idcarga and idarbol=1000000 where ((carga.idsem in (select idsem from semestre where activo>0)) and carga.activo=1) and carga.codigo=".$_SESSION['codigo'];
$sqldir="SELECT idesc AS iddepe FROM gcodigo WHERE  idfac = 421 AND codigo =".$_SESSION['codigo'];
// var_dump($sqldir);
	$resultdir=luis($conn, $sqldir);
	while ($rowdir =fetchrow($resultdir,-1))
	{
		$iddepedirec=$rowdir[0];
		// echo '<br>Iddepe del director: ';
		// echo $iddepedirec;
	}
			cierra($resultdir);
			require_once('encripta_pdf.php');
			//echo '<td><font size="2"><a target="_blank" href="reportencu_director.php?sesion='.$sex.'&id='.fn_encriptar($iddepedirec).'" >Reporte de resultados de encuesta Director</a></font></td>';
			if (($iddepedirec>0) or ($_SESSION['codigo']==117584) or ($_SESSION['codigo']==	202848))
			//if (($iddepedirec>0) or ($_SESSION['codigo']==117584))
			{
			//echo '<td><font size="2"><a target="_blank" href="http://www.upt.edu.pe/epic2/resultado.php" >Reporte de resultados de encuesta</a></font></td>';
			//echo '<td><font size="2"><a target="_blank" href="http://www.upt.edu.pe/epic2/resultado.php" >Reporte de resultados de encuesta</a></font></td>';
			}

			if (($_SESSION['codigo']==117584) or ($_SESSION['codigo']==	202848)or ($_SESSION['codigo']==141414)or ($_SESSION['codigo']==109684)or ($_SESSION['codigo']==124717))
			{
			echo '<td><font size="2"><a target="_blank" href="http://www.upt.edu.pe/epic2/resultadovi.php" >Reporte de resultados de encuesta VICERRECTOR y RECTOR(A)</a></font></td>';
			}

			// *** FIN DE LINK DE REPORTE DE ENCUESTA DE ODESAR ***


		echo '</tr>';
echo '</table>';
//echo 'Docente: '.$_SESSION['name'].'<br><br>';
//echo 'Docente: Perez Garcia, Juan <br><br>';
$na=0;
$mj=0;

	//$conn=conex();
        //$sql="select codcurso, seccion, descurso, semestre, case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, carga.idcarga from carga, curso, depe, semestre where depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem and carga.idcurso=curso.idcurso and carga.activo=1 and carga.codigo=".$_SESSION['codigo'];
	//$result=luis($conn, $sql);
	$in=1;
	fetchrow($result,$in-1);
	//echo '<table border="0" ><tr><th bgcolor="#DBEAF5" ><font size="1">sel</font></th><th bgcolor="#DBEAF5" ><font size="1">CodCurso</font></th><th bgcolor="#DBEAF5" ><font size="1">Seccion</font></th><th bgcolor="#DBEAF5" ><font size="1">Curso</font></th><th bgcolor="#DBEAF5" ><font size="1">Semestre</font></th><th bgcolor="#DBEAF5" ><font size="1">Escuela</font></th></tr>';
	//esto era antes Gary
	/* echo '<table border="0" ><tr><th bgcolor="#DBEAF5" ><font size="1">sel</font></th><th bgcolor="#DBEAF5" ><font size="1">CodCurso</font></th><th bgcolor="#DBEAF5" ><font size="1">Seccion</font></th><th bgcolor="#DBEAF5" ><font size="1">Curso</font></th><th bgcolor="#DBEAF5" ><font size="1">Semestre</font></th><th bgcolor="#DBEAF5" ><font size="1">Escuela</font></th><th bgcolor="#DBEAF5" ><font size="1">Hrs.</font></th><th bgcolor="#DBEAF5" ><font size="1">Sílabo</font></th>';*/
	echo '<table border="0" ><tr><th bgcolor="#DBEAF5" ><font size="1">sel</font></th><th bgcolor="#DBEAF5" ><font size="1">CodCurso</font></th><th bgcolor="#DBEAF5" ><font size="1">Seccion</font></th><th bgcolor="#DBEAF5" ><font size="1">Curso</font></th><th bgcolor="#DBEAF5" ><font size="1">Semestre</font></th><th bgcolor="#DBEAF5" ><font size="1">Escuela</font></th><th bgcolor="#DBEAF5" ><font size="1">Hrs.</font></th>';
	echo'<th bgcolor="#DBEAF5" ><font size="1">Consolidado</font></th>';
	echo'</tr>';
	while ($row =fetchrow($result,-1))
	{
		// var_dump($row);
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
		echo'<input name="codp" type="hidden" value="'.$_SESSION['codper'].'">';
		//echo ' <td '.$tcol.'><input type="radio" value="'.$na.'" onClick="javascript:pele('.$na.')" name="R2">&nbsp;&nbsp;</td>'; //onClick="javascript:pele('.$na.')"
		//echo'<td '.$tcol.'>			<font size="1">					<a href=# onclick="javascript:window.open(\'https://'.$_SERVER['HTTP_HOST'].'/buscacxv2.php?sesion='.$sex.'&o='.$row[5].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=nos,width=850,height=650,top=40,left=50\');return false"><center><img src="imagenes/view.gif" width=18 height=20 alt="Edit file" border=0></center> </a></font></td>';
		echo'<td '.$tcol.'><font size="1"><a href=# onclick="javascript:window.open(\'buscacxv2.php?sesion='.$sex.'&o='.$row[5].'&taex='.$row[9].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=nos,width=850,height=650,top=40,left=50\');return false"><center><img src="imagenes/view.gif" width=18 height=20 alt="Edit file" border=0></center> </a></font></td>';
		echo'</tr>';
		if ($row[4]=='Ingeniería Civil'){$mj=1;}

	}

	//<input type=button value="Ingresar"  onClick="javascript:sele(2)">
	echo '<table border="0" cellpadding="10" ><tr><td width="132" ></td></tr></table>';
	cierra($result);
	noconex($conn);
echo '</table>';
echo '</form>';

/*AGREGUE LA VARIALBE $file_php PARA DETERMINAR SI ESTOY EN EL ARCHIVO estadistica.php O buscab.php*/

$file_php=0;
if (isset($_GET['tr'])==true ){
	require ("genera.php");
	$sem = $_GET["x"];
	individual($_SESSION['codigo'], $sex, $_SESSION['codper'], 0, $file_php,$sem);
}
/*if ($mj==1){echo '<a target="_blank" href="http://epic.upt.edu.pe/encuesta.php?id='.$_SESSION['codigo'].'" >Ingrese aqui por favor y llene su encuesta para el proceso de autoevaluacion de la EPIC</a>';}*/ // funciona



//ECHO $_GET['tr'];
if(isset($_GET['tr'])==true)
{
	echo  '<br><br><form method="post" action="carga.php?tr=1&sesion='.$sex.'&x='.$idsem.'" name="registro" id="registro" enctype="multipart/form-data">
	<table border="0" widtd="100%" >
	<tr><td style="color:111351; font-size:15px;"><b>REGISTRO DE HORARIO DE TRABAJO</b></td></tr>
	<tr>
	<td><input type="hidden" name="variable2" value="registro" />- Descargar <b>Anexo</b> de horario de trabajo<a href="trabajoindividualex/Horario_Docente.xls" target="_blank"><span style="color: red;"><font size="1" face="Arial"><blink> ( Haga clic aquí )</span> </font></td></tr>
	</tr>
	<tr>
	<tr>
	<td></td>
	</tr>
	<td>Seleccionar el archivo <font size=1px>(xls)</font> <input type="file" name="archivo[]" multiple></td>
	<td><input type="submit" value="Subir Archivo" name="registrar" id="registrar"></td>
	</tr>
	</table>
	</form>';


	$conn=conex();
	$sql="SELECT TOP 1 s.archivo FROM trabdoc s WHERE s.codigo=".$_SESSION['codigo']." and s.idsem = ".$idsem;
	$resultse=luis($conn, $sql);
	$cantidad = numrow($resultse);

	if ($cantidad>0)
	{
		while ($rowdir =fetchrow($resultse,-1))
		{
			$doc=$rowdir[0];
		}

		echo "- Visualizar Horario de Trabajo ".$semestre.": <a href=trabajoindividualex/".$doc.">Hacer Clic para Ver el Documento</a><br><br><br>";
	}

}


echo '<br><br>En la columna <strong>Consolidado</strong> podrá visualizar la nota promedio de la unidad y el resumen de la unidad <br>que incluye: aprobados, desaprobados, retirados y abandonos.';
?>

	<?php

		// =============================================================================================
		// VERIFICACION DE EMAIL DEL DOCENTE
		// =============================================================================================
	// if( $_SESSION["codigo"] == 298907 ){
		$conn_email = conex();
		$sql_email	=	"SELECT
											dde.IdDocenteDatoEvaluacion
			   					 FROM Aud_DocenteDatoEvaluacion AS dde WHERE dde.CodUniv = ".$_SESSION['codigo']." AND dde.ItemEst = 1 and dde.bitEliminado = 0";
		// echo $sql_email;
		$resul_email=	luis($conn_email, $sql_email);
		$row_email 	=	fetchrow($resul_email,-1);

		if( isset($row_email[0]) ){
			$pIdDocenteDatoEvaluacion = $row_email[0];

			$sql_accesos	=	"SELECT TOP 10
							tb.CodigoCurso
						,tb.Asignatura
						,tb.Seccion
						,UPPER(tb.Docente) Docente
						,FORMAT(tb.Fecha ,'dd/MM/yyyy hh:mm:ss' ,'es') Fecha
			FROM  (
				SELECT TOP 10
					adtcd.IdDocenteTokenCursoDetalle Id 
					, ppec.CodigoCurso
					,ppec.Asignatura
					,cch.Seccion
					,CONCAT(
							cp.ApellidoPaterno
							,' '
							,cp.ApellidoMaterno
							,', '
							,cp.Nombres
					) AS Docente
					,adtcd.datCreado Fecha
				FROM Aud_DocenteTokenCursoDetalle AS adtcd
				INNER JOIN Aud_DocenteTokenCurso AS adtc ON adtc.IdDocenteTokenCurso = adtcd.IdDocenteTokenCurso
				INNER JOIN Cho_CargaHoraria AS cch ON cch.IdCargaCROM = adtcd.IdCarga AND cch.Estado = 1
				INNER JOIN Cho_PeriodoPersona AS cpp ON cpp.IdChoPeriodoPersona = cch.IdChoPeriodoPersona AND cpp.Estado = 1
				INNER JOIN Cho_Persona AS cp ON cp.IdChoPersona = cpp.IdChoPersona AND cp.Estado = 1
				INNER JOIN Cho_Semestre AS cs ON cs.IdChoSemestre = cpp.IdChoSemestre AND cs.Estado = 1
				INNER JOIN Pes_PlanEstudioCurso AS ppec ON ppec.IdPesPlanEstudioCurso = cch.IdPesPlanEstudioCurso AND ppec.ExtraCurricular = 0
				INNER JOIN SEMESTRE AS s ON s.IdSem = cs.IdSem
				WHERE adtc.IdDocenteDatoEvaluacion = ".$pIdDocenteDatoEvaluacion."
				AND adtc.Idsem = ".$idsem."
				AND adtc.bitEliminado = 0
				AND adtcd.esTaex = 0

				UNION ALL
	
				SELECT TOP 10	
					adtcd.IdDocenteTokenCursoDetalle
					,ppetg.CodigoCursoGeneral CodigoCurso
					,ppetg.AsignaturaGeneral Asignatura
					,tch.Seccion
					,CONCAT(
							p.ApepPer
							,' '
							,p.ApemPer
							,', '
							,p.NomPer
					) AS Docente
					,adtcd.datCreado Fecha
				FROM Aud_DocenteTokenCursoDetalle AS adtcd
				INNER JOIN Aud_DocenteTokenCurso AS adtc ON adtc.IdDocenteTokenCurso = adtcd.IdDocenteTokenCurso
				INNER JOIN Tax_CargaHoraria AS tch ON tch.IdTaxCargaHoraria = adtcd.IdCarga
				INNER JOIN persona AS p ON p.CodPer = tch.CodPer
				INNER JOIN Pes_PlanEstudioTaexGeneral AS ppetg ON ppetg.IdPesPlanEstudioTaexGeneral = tch.IdPesPlanEstudioTaexGeneral
				INNER JOIN Tax_SemestreConsolidado AS tsc ON tsc.IdTaxSemestreConsolidado = tch.IdTaxSemestreConsolidado AND tsc.IdSem = adtc.IdSem
				WHERE adtc.IdDocenteDatoEvaluacion = ".$pIdDocenteDatoEvaluacion."
				AND adtc.IdSem = ".$idsem."
				AND adtc.bitEliminado = 0
				AND adtcd.esTaex = 1

			) AS tb 
			ORDER BY tb.Id DESC
			";
			// echo $sql_accesos;
			$resul_accesos =	luis($conn_email, $sql_accesos);

			if ( numrow($resul_accesos) > 0 ){
	?>

			<br><br>
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

					while ($row = fetchrow($resul_accesos,-1))
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

		cierra($resul_email);
		cierra($resul_accesos);
		noconex($conn_email);

	// }
		// =============================================================================================
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
</html>
