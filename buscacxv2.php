<?
//ob_start();
require ("funciones.php");
pageheader();
$sex=$_GET["sesion"];
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

				$ver=1;
        $conn=conex();
  
  $dc=$_GET["o"];
	$taex = false;
	if(isset($_GET["taex"])){
		$taex = $_GET["taex"];
	}else{
		$taex = $_SESSION['estaex'];
	}
  if($dc > 0)
  {
	//$sqldel="TRUNCATE TABLE AprobadosUnidades"; delAprobadosUnidades
	$sqldel="exec delAprobadosUnidades";
	$resultdel=luis($conn, $sqldel);	  
  }
  //echo $dc;
  //echo '<br>';
  //$sqla="declare @k int select top 1 @k=1 from eval where idcarga=".$dc." and idarbol=1000000 select lower(descurso), carga.idcurso, carga.idsem, carga.idcarga, carga.iddepe, seccion, @k as ka, case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, semestre, codcurso, codcurso, rtrim(apepper)+' '+rtrim(apemper)+', '+rtrim(nomper) as nombre from carga, curso, depe, semestre, persona where persona.codper=carga.codper and curso.idcurso=carga.idcurso and  depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem and  carga.activo=1 and idcarga=".$dc;
  //ESVC-I-111010
  //*iwin $sqla="declare @k int select top 1 @k=1 from eval where idcarga=".$dc." and idarbol=1000000 select lower(descurso), carga.idcurso, carga.idsem, carga.idcarga, carga.iddepe, seccion, @k as ka, case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, semestre, codcurso, codcurso, rtrim(apepper)+' '+rtrim(apemper)+', '+rtrim(nomper) as nombre from carga, curso, depe, semestre, persona where persona.codper=carga.codper and curso.idcurso=carga.idcurso and  depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem and (semestre.activo>0 and carga.activo>0) and idcarga=".$dc;
  //ESVC-F-111010
  
  // LISTA DATOS DEL DOCENTE
  	//$sqla="declare @k int select top 1 @k=1 from eval where idcarga=".$dc." and idarbol=1000000 select lower(descurso), carga.idcurso, carga.idsem, carga.idcarga, carga.iddepe, seccion, @k as ka, case when patindex('Escuela profesional de%', depe.descrip)>0 then right(rtrim(depe.descrip),len(rtrim(depe.descrip))-23) else depe.descrip end  as descri, semestre, codcurso, codcurso, rtrim(apepper)+' '+rtrim(apemper)+', '+rtrim(nomper) as nombre from carga, curso, depe, semestre, persona where persona.codper=carga.codper and curso.idcurso=carga.idcurso and  depe.iddepe=carga.iddepe and semestre.idsem=carga.idsem and (semestre.activo>0 OR carga.activo>0) and idcarga=".$dc;
		if(!$taex){		
			$sqla="exec sp_int_ListarDatosDocente ".$_SESSION['codigo'].",".$dc;
		}else{
			$sqla="exec sp_int_ListarDatosDocenteTaex ".$_SESSION['codigo'].",".$dc;
		}	
  	// $sqla="exec sp_int_ListarDatosDocente ".$_SESSION['codigo'].",".$dc;
;
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
        // LISTA LA CANTIDAD DE UNIDADES Y  EVALUACIONES 
				if(!$taex){		
					$sqlx="select eval.ideval, eval.idarbol, desarbol ,peso, feval, lo.ideval, nivel from eval inner join arbol on arbol.idarbol=eval.idarbol left join (select distinct deval.ideval from deval, eval, arbol where eval.idarbol=arbol.idarbol and deval.ideval=eval.ideval and nivel=4 and idcarga=".$dc.") as lo on eval.ideval=lo.ideval where nivel in (1,2,4) and idcarga=".$dc." order by eval.idarbol ";
				}else{
					$sqlx="select eval_taex.idevaltaex, eval_taex.idarboltaex, desarbol ,peso, feval, lo.idevaltaex, nivel from eval_taex inner join arbol_taex on arbol_taex.idarboltaex=eval_taex.idarboltaex left join (select distinct deval_taex.idevaltaex from deval_taex, eval_taex, arbol_taex where eval_taex.idarboltaex=arbol_taex.idarboltaex and deval_taex.idevaltaex=eval_taex.idevaltaex and nivel=4 and idcarga=".$dc.") as lo on eval_taex.idevaltaex=lo.idevaltaex where nivel in (1,2,4) and idcarga=".$dc." order by eval_taex.idarboltaex ";
				}	
        // $sqlx="select eval.ideval, eval.idarbol, desarbol ,peso, feval, lo.ideval, nivel from eval inner join arbol on arbol.idarbol=eval.idarbol left join (select distinct deval.ideval from deval, eval, arbol where eval.idarbol=arbol.idarbol and deval.ideval=eval.ideval and nivel=4 and idcarga=".$dc.") as lo on eval.ideval=lo.ideval where nivel in (1,2,4) and idcarga=".$dc." order by eval.idarbol ";

        //echo $sqlx;
        $result=luis($conn, $sqlx);
      	$crea=1;
      	$fiel=0;
      	$fuel=0;
      	$feel=0;
      	$fine[0][0]=0;
        while ($row =fetchrow($result,-1))
        {   
			// CUANDO SEA Consolidado de Unidades    
        	if($row[1]==1000000)
					{
        		$crea=0;
        	}
			//$row[1] = idarbol, 
        	// $row[5] = ideval, 
        	// $row[6] = nivel
			// CUANDO SEA UNA EVALUACION
        	if($row[5]==null and $row[1]>1000000 and $row[6]==2)
        	{
        		if($fuel>0)
        		{
        			$arb[$fuel][0]=$fiel;
        			$fiel=0;
        		}	
        		$fuel++;
				// $row[3] = peso,
				// $row[1] = idarbol,
        		$uar[$fuel]=$row[3];
        		$uan[$fuel]=$row[1];
        		$feel++;
        	}
			// $row[5] = ideval, 		
        	if($row[5]>0)
        	{
        		$feel++;
        		$fiel++;
				// $row[1] = idarbol, 
				// $row[3] = peso,
        		$arb[$fuel][$fiel]=$row[1];
        		$arp[$fuel][$fiel]=$row[3];
        		if ($fiel==1){
        	 	$sele[$fuel]='p'.$fiel.'.peso as p'.$fiel.', p'.$fiel.'.nota as n'.$fiel;	
        	 	$selg[$fuel]='u'.$fuel.'.p'.$fiel.', u'.$fuel.'.n'.$fiel;	
        	 	//$sela[$fuel]='(case when p'.$fiel.'.nota is null then 0 else p'.$fiel.'.nota end)*(p'.$fiel.'.peso/100)';

						if(!$taex){		
							$from[$fuel]="(select coduniv, peso, nota from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dc."  and eval.idarbol=".$row[1].") as p".$fiel."  ";	
						}else{
							$from[$fuel]="(select coduniv, peso, nota from deval_taex, eval_taex, arbol_taex where deval_taex.idevaltaex=eval_taex.idevaltaex and arbol_taex.idarboltaex=eval_taex.idarboltaex and idcarga=".$dc."  and eval_taex.idarboltaex=".$row[1].") as p".$fiel."  ";	
						}
        	 	// $from[$fuel]="(select coduniv, peso, nota from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dc."  and eval.idarbol=".$row[1].") as p".$fiel."  ";	

        		}else{
        	 	$sele[$fuel]=$sele[$fuel].', p'.$fiel.'.peso as p'.$fiel.', p'.$fiel.'.nota as n'.$fiel;	
        	 	$selg[$fuel]=$selg[$fuel].', u'.$fuel.'.p'.$fiel.', u'.$fuel.'.n'.$fiel;	
        	 	//$sela[$fuel]=$sela[$fuel].'+(case when p'.$fiel.'.nota is null then 0 else p'.$fiel.'.nota end)*(p'.$fiel.'.peso/100)';

						if(!$taex){		
							$from[$fuel]=$from[$fuel]."left join (select coduniv, peso, nota from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dc."  and eval.idarbol=".$row[1].") as p".$fiel." on p1.coduniv=p".$fiel.".coduniv ";
						}else{
							$from[$fuel]=$from[$fuel]."left join (select coduniv, peso, nota from deval_taex, eval_taex, arbol_taex where deval_taex.idevaltaex=eval_taex.idevaltaex and arbol_taex.idarboltaex=eval_taex.idarboltaex and idcarga=".$dc."  and eval_taex.idarboltaex=".$row[1].") as p".$fiel." on p1.coduniv=p".$fiel.".coduniv ";
						}
        	 	// $from[$fuel]=$from[$fuel]."left join (select coduniv, peso, nota from deval, eval, arbol where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dc."  and eval.idarbol=".$row[1].") as p".$fiel." on p1.coduniv=p".$fiel.".coduniv ";}
					}
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
	$conn=conex();
	if ($ver==1)
	{
		echo '<br>';
		// echo $selx;
		// echo $frox;
		
		/*$sqlq="select nombre, u0.coduniv, estado, activo ".$selx." from (select deval.coduniv, nombre, codestado as estado, notafinal as activo from deval, eval, arbol, cursomatricula where deval.ideval=eval.ideval and arbol.idarbol=eval.idarbol and idcarga=".$dc."  and eval.idarbol=1000000 and cursomatricula.coduniv=deval.coduniv and cursomatricula.itemest=deval.itemest and codestado not in (6,8,13,16,14,15,16,0)  and idsem=".$idsem." and idcurso=".$idcurso." and seccion='".$seccion."'  ) as u0 ".$frox." Order By nombre COLLATE Traditional_Spanish_CS_AI_KS_WS ";*/

		if(!$taex){		
			$sqlq="SELECT nombre,
				       u0.coduniv,
				       estado,
				       activo ".$selx."
				FROM   (
				           SELECT deval.coduniv,
				                  nombre,
				                  mc.IdSacEstadoEvaluacion AS estado,
				                  mc.NotaFinalActa  AS activo
				           FROM   deval, eval, arbol, mat_cursomatricula mc
				           WHERE  deval.ideval = eval.ideval
				                  AND arbol.idarbol = eval.idarbol
				                  AND idcarga = ".$dc."
				                  AND eval.idarbol = 1000000
				                  AND mc.coduniv = deval.coduniv
				                  AND mc.itemest = deval.itemest
				                  AND mc.IdSacEstadoEvaluacion NOT IN (6, 8, 13, 16, 14, 15, 16, 0)
				                  AND mc.idsem = ".$idsem."
				                  AND mc.IdPesPlanEstudioCurso = ".$idcurso."
				                  AND mc.seccion = '".$seccion."'
				                  and mc.Estado = 1
				       ) AS u0 ".$frox."
				ORDER BY nombre COLLATE Traditional_Spanish_CS_AI_KS_WS "; 
		}else{
			$sqlq="SELECT nombre,
				       u0.coduniv,
				       estado,
				       activo ".$selx."
				FROM   (
				           SELECT deval_taex.coduniv,
									 				deval_taex.nombre,
				                  mc.IdSacEstadoEvaluacion AS estado,
				                  mc.NotaFinalActa  AS activo
				           FROM   deval_taex, eval_taex, arbol_taex, mat_cursomatricula mc
									 INNER JOIN Pes_PlanEstudioTaexGeneralDetalle AS ppetgd ON ppetgd.IdPesPlanEstudioCurso = mc.IdPesPlanEstudioCurso AND ppetgd.Activo = 1 AND ppetgd.Eliminado = 0
									 INNER JOIN Tax_SemestreConsolidadoDetalle AS tscd ON tscd.Idsem = mc.Idsem AND tscd.Activo = 1 AND tscd.Eliminado = 0
									 INNER JOIN Tax_SemestreConsolidado AS tsc ON tsc.IdTaxSemestreConsolidado = tscd.IdTaxSemestreConsolidado
				           WHERE  deval_taex.idevaltaex = eval_taex.idevaltaex
				                  AND arbol_taex.idarboltaex = eval_taex.idarboltaex
				                  AND idcarga = ".$dc."
				                  AND eval_taex.idarboltaex = 1000000
				                  AND mc.coduniv = deval_taex.coduniv
				                  AND mc.itemest = deval_taex.itemest
				                  AND mc.IdSacEstadoEvaluacion NOT IN (6, 8, 13, 16, 14, 15, 16, 0)
				                  AND tsc.IdSem = ".$idsem."
				                  AND ppetgd.IdPesPlanEstudioTaexGeneral = ".$idcurso."
				                  AND mc.seccion = '".$seccion."'
				                  and mc.Estado = 1

									UNION ALL

									SELECT deval_taex.coduniv,
									 				deval_taex.nombre,
				                  mc.IdSacEstadoEvaluacion AS estado,
				                  mc.NotaFinalActa  AS activo
				           FROM   deval_taex, eval_taex, arbol_taex, mat_cursomatricula mc
									 INNER JOIN Pes_PlanEstudioTaexGeneralDetalle AS ppetgd ON ppetgd.IdPesPlanEstudioCurso = mc.IdPesPlanEstudioCurso AND ppetgd.Activo = 1 AND ppetgd.Eliminado = 0
									 --INNER JOIN Tax_SemestreConsolidadoDetalle AS tscd ON tscd.Idsem = mc.Idsem AND tscd.Activo = 1 AND tscd.Eliminado = 0
									 INNER JOIN Mat_CursoSemestreTaex AS mcst ON mcst.IdMatCursoMatricula = mc.IdMatCursoMatricula AND mcst.Estado = 1
									 INNER JOIN Tax_SemestreConsolidado AS tsc ON tsc.IdTaxSemestreConsolidado = mcst.IdTaxSemestreConsolidado									 
				           WHERE  deval_taex.idevaltaex = eval_taex.idevaltaex
				                  AND arbol_taex.idarboltaex = eval_taex.idarboltaex
				                  AND idcarga = ".$dc."
				                  AND eval_taex.idarboltaex = 1000000
				                  AND mc.coduniv = deval_taex.coduniv
				                  AND mc.itemest = deval_taex.itemest
				                  AND mc.IdSacEstadoEvaluacion NOT IN (6, 8, 13, 16, 14, 15, 16, 0)
				                  AND tsc.IdSem = ".$idsem."
				                  AND ppetgd.IdPesPlanEstudioTaexGeneral = ".$idcurso."
				                  AND mc.seccion = '".$seccion."'
				                  and mc.Estado = 1

				       ) AS u0 ".$frox."
				ORDER BY nombre COLLATE Traditional_Spanish_CS_AI_KS_WS "; 
		}
		// $sqlq="SELECT nombre,
		// 		       u0.coduniv,
		// 		       estado,
		// 		       activo ".$selx."
		// 		FROM   (
		// 		           SELECT deval.coduniv,
		// 		                  nombre,
		// 		                  mc.IdSacEstadoEvaluacion AS estado,
		// 		                  mc.NotaFinalActa  AS activo
		// 		           FROM   deval, eval, arbol, mat_cursomatricula mc
		// 		           WHERE  deval.ideval = eval.ideval
		// 		                  AND arbol.idarbol = eval.idarbol
		// 		                  AND idcarga = ".$dc."
		// 		                  AND eval.idarbol = 1000000
		// 		                  AND mc.coduniv = deval.coduniv
		// 		                  AND mc.itemest = deval.itemest
		// 		                  AND mc.IdSacEstadoEvaluacion NOT IN (6, 8, 13, 16, 14, 15, 16, 0)
		// 		                  AND mc.idsem = ".$idsem."
		// 		                  AND mc.IdPesPlanEstudioCurso = ".$idcurso."
		// 		                  AND mc.seccion = '".$seccion."'
		// 		                  and mc.Estado = 1
		// 		       ) AS u0 ".$frox."
		// 		ORDER BY nombre COLLATE Traditional_Spanish_CS_AI_KS_WS "; 
		//echo $sqlq;
	}
	else
	{
		$sqlq="select l from (select 1 as l) as a where l>2";
	}
	$result=luis($conn, $sqlq);
?>
<table border="0" >
	<tr>
	  <td>&nbsp;</td>
		<td ><font size="1">Docente:</font></td>
		<td><font size="1"><? echo $nombre; ?></font></td>
	</tr>
	<?php if(!$taex){	?>
		<tr>
			<td>&nbsp;</td>
			<td ><font size="1">Escuela:</font></td>
			<td><font size="1"><? echo strtoupper($escuela); ?></font></td>
		</tr>
	<?php }	?>
	<tr>
	  <td>&nbsp;</td>
		<td ><font size="1">Curso:</font></td>
		<td><font size="1"><? echo strtoupper($curso); ?></font></td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
		<td ><font size="1">Codigo:</font></td>
		<td><font size="1"><? echo $codcurso; ?></font></td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
		<td ><font size="1">Seccion:</font></td>
		<td><font size="1"><? echo $seccion; ?></font></td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
		<td ><font size="1">Generado:</font></td>
		<td><font size="1"><? fecha(); ?></font></td>
	</tr>
</table>
<?
	$fula = $feel + $fuel;
	echo '<table border="0" cellspacing="2">
			<tr>
				<th bgcolor="#DBEAF5" rowspan="3"><font size="1">Nº&nbsp;&nbsp;</font></th>
				<th bgcolor="#DBEAF5" rowspan="3"><font size="1">Codigo</font></th>
				<th bgcolor="#DBEAF5" rowspan="3"><font size="1">Nombre</font></th>';
				echo'<th bgcolor="#DBEAF5" colspan="'.$feel.'"><font size="1">Consolidado</font></th>	';
				//echo'<th bgcolor="#DBEAF5" colspan="'.$fula.'"><font size="1">Consolidado</font></th>	';
				echo'<th bgcolor="#DBEAF5" rowspan="3"><font size="1">Promedio Final</font></th>
				<th bgcolor="#DBEAF5" rowspan="3"><font size="1">&nbsp;&nbsp;Estado Final</font></th>	
			</tr>';	
	echo '<tr>';
	$k=4;
	For ($i=1;$i<=$fuel;$i++)
	{
		$iup=$uar[$i];
		$tpu=$tpu+$uar[$i];
		echo '<td bgcolor="#DBEAF5" colspan="'.($arb[$i][0]+1).'" align="center"><font size="1">'.$iup.'%</font></td>'; // original
		// PORCENTAJE DE UNA UNIDAD
		//echo '<td bgcolor="#DBEAF5" colspan="'.($arb[$i][0]+2).'" align="center"><font size="1">'.$iup.'%</font></td>';		
		$k=$k+3;
	}
	echo '</tr>';
	echo '<tr>';
	For ($i=1;$i<=$fuel;$i++)
 	{	
 		For ($j=1;$j<=$arb[$i][0];$j++)
 		{
			// PORCENTAJE DE CADA EVALUACION DE UNA UNIDAD
 			echo '<td bgcolor="#DBEAF5" ><font size="1">'.$arp[$i][$j].'</font></td>';
 		}
 		// IMPRIME EL PROMEDIO DE UNA UNIDAD
		echo '<th bgcolor="#DBEAF5" ><font size="1">U'.$i.'</font></td>';
		//echo '<th bgcolor="#DBEAF5" ><font size="1">Estado</font></td>';
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
				// LAS NOTAS DE LAS EVALUACIONES
 				echo ' <td '.$tcol.'><font '.$apr.' size="1">&nbsp;&nbsp;'.$row[$k].'</font></td>';
 			}
 			$apr='color="#FF0000"';			
			if ($pru>=11) // original ok 
			//if ($pru>=10.5)
			{
					$apr='color="#0000FF"';
			}
			// PROMEDIOS FINALES DE CADA UNIDAD POR ESTUDIANTE
			echo ' <td '.$tcol.'>';
				//ok
				$notas =number_format($pru, 2, '.', ',');
				$sqlas="exec sp_add_unidades_aprobados ".$i.", ".$notas;

				//$sqlas="exec sp_add_unidades_aprobados ".number_format($pru, 2, '.', ',');
				$results=luis($conn, $sqlas);
				echo '<font '.$apr.' size="1">&nbsp;&nbsp;&nbsp;'.number_format($pru, 2, '.', ',').'</font>';
			echo'</td>';
			// *************
			// if ($pro>=10.5)
			//if (number_format($pru, 2, '.', ',')>=11)
			if ($notas>=10.5)
			{
				$estados="Aprobado";
			}
			else
			{
				$estados="Desaprobado";	
			}			
			//echo ' <td '.$tcol.'><font '.$apr.' size="1">'.$estados.'</font></td>';
			//$pro=round($pro+($pru*($pre/100)),2);
			$pro=$pro+($pru*($pre/100));
			$pru=0;
		}
			//$pro=round($pro,2);
			//$pro=round($pro,0);
			//**saco
			$pro=round(round(round($pro,8),2),0);
			$apr='color="#FF0000"';			
			if ($pro>=11)
			{
				$apr='color="#0000FF"';
			}
		echo ' <td '.$tcol.'><font '.$apr.' size="1"><center>'.$pro.'</center></font></td>';
		$estado="";
		// *****		
		if ($pro>=10.5)
		{
			$estado="Aprobado";	
		}
		else
		{
			$estado="Desaprobado";	
		}		
		// *****	
		if ($row[2]==11)
		{
			$estado="Abandono";	
			$count_abandono++;
		}
		if ($row[2]==12)
		{
			$estado="Retirado";
			$count_retirado++;	
		}
//		if (($row[2]<>11) or ($row[2]<>12))
//		{
//		  if ($pro>=10.5)
//		  {
//			  $estado="Aprobado";	
//		  }
//		  else
//		  {
//			  $estado="Desaprobado";	
//		  }	
//		}
		echo '<td '.$tcol.'><font size="1">&nbsp;&nbsp;'.$estado.'</td>';
		echo '</tr>';
  }
	echo '</table>';
			$abandono = $count_abandono;
			$retirado = $count_retirado;
// *********************
		while ($rov =fetchrow($results,-1))
		{

			$unidad[0] = $rov[0];	
			$unidad[1] = $rov[1];
			$unidad[2] = $rov[2];
			$unidad[3] = $rov[3];
			$unidad[4] = $rov[4];
			$unidad[5] = $rov[5];			
			$unidad[6] = $rov[6];				
		}
//***************	PORCENTAJES POR UNIDADES	**************	
	$total_matriculados = $na;

	if (isset($abandono)==false)
	{
		$abandono=0;
	}
	if (isset($retirado)==false)
	{
		$retirado=0;
	}	
	For ($i=1;$i<=$fuel;$i++)
	{
	$count_aprobado	=$unidad[$i-1];
	$count_desaprobado= $total_matriculados - $count_aprobado - $abandono - $retirado ;

	$porcentaje_aprobado = 0;
	$porcentaje_desaprobado = 0;
	$porcentaje_abandono = 0;
	$porcentaje_retirado = 0;
	// CALCULO DE LOS PORCENTAJES
	if($total_matriculados > 0){
		$porcentaje_aprobado = ($count_aprobado * 100) / $total_matriculados;
		$porcentaje_desaprobado = ($count_desaprobado * 100) / $total_matriculados;
		$porcentaje_abandono = ($abandono * 100) / $total_matriculados;
		$porcentaje_retirado = ($retirado * 100) / $total_matriculados;
	}
echo'
<br /><br />
<table border="1" cellspacing="0" cellpadding="0">
  <tr>
    <th bgcolor="#DBEAF5" width="576" colspan="10" valign="top"><p align="center"><font size="2">RESUMEN ACADEMICO UNIDAD '.$i.'</font></p></th>
  </tr>
  <tr>
    <th bgcolor="#DBEAF5" width="115" colspan="2" valign="top"><p align="center"><font size="1">TOTAL MATRICULADOS</font></p></th>
    <th bgcolor="#DBEAF5" width="115" colspan="2" valign="top"><p align="center"><font size="1">APROBADOS</font></p></th>
    <th bgcolor="#DBEAF5" width="115" colspan="2" valign="top"><p align="center"><font size="1">DESAPROBADOS</font></p></th>
    <th bgcolor="#DBEAF5" width="115" colspan="2" valign="top"><p align="center"><font size="1">RETIRADOS</font></p></th>
    <th bgcolor="#DBEAF5" width="115" colspan="2" valign="top"><p align="center"><font size="1">ABANDONARON</font></p></th>
  </tr>
  <tr>
    <th bgcolor="#DBEAF5" width="58" valign="top"><p align="center"><font size="2">Nº</font></p></th>
    <th bgcolor="#DBEAF5" width="58" valign="top"><p align="center"><font size="2">%</font></p></th>
    <th bgcolor="#DBEAF5" width="58" valign="top"><p align="center"><font size="2">Nº</font></p></th>
    <th bgcolor="#DBEAF5" width="58" valign="top"><p align="center"><font size="2">%</font></p></th>
    <th bgcolor="#DBEAF5" width="58" valign="top"><p align="center"><font size="2">Nº</font></p></th>
    <th bgcolor="#DBEAF5" width="58" valign="top"><p align="center"><font size="2">%</font></p></th>
    <th bgcolor="#DBEAF5" width="58" valign="top"><p align="center"><font size="2">Nº</font></p></th>
    <th bgcolor="#DBEAF5" width="58" valign="top"><p align="center"><font size="2">%</font></p></th>
    <th bgcolor="#DBEAF5" width="58" valign="top"><p align="center"><font size="2">Nº</font></p></th>
    <th bgcolor="#DBEAF5" width="58" valign="top"><p align="center"><font size="2">%</font></p></th>
  </tr>
  <tr>
    <td width="58" valign="top"><p align="center"><font size="2">'.$total_matriculados.'</font></p></td>
    <td width="58" valign="top"><p align="center"><font size="2">100</font></p></td>
	
    <td width="58" valign="top"><p align="center"><font size="2">'.$count_aprobado.'</font></p></td>
    <td width="58" valign="top"><p align="center"><font size="2">'.number_format($porcentaje_aprobado, 2, '.', ',').'</font></p></td>
	
    <td width="58" valign="top"><p align="center"><font size="2">'.$count_desaprobado.'</font></p></td>
    <td width="58" valign="top"><p align="center"><font size="2">'.number_format($porcentaje_desaprobado, 2, '.', ',').'</font></p></td>
	
    <td width="58" valign="top"><p align="center"><font size="2">'.$retirado.'</font></p></td>
    <td width="58" valign="top"><p align="center"><font size="2">'.number_format($porcentaje_retirado, 2, '.', ',').'</font></p></td>
	
    <td width="58" valign="top"><p align="center"><font size="2">'.$abandono.'</font></p></td>
    <td width="58" valign="top"><p align="center"><font size="2">'.number_format($porcentaje_abandono, 2, '.', ',').'</font></p></td>
  </tr>
</table>
<br />
	';
		}
//****************************	
	
/*
$fecha = date('m-d-Y');
pageheadercache();
header('Content-type: application/vnd.ms-excel');
header("Content-Disposition: attachment; filename=consolidado_de_acta.xls");
header("Expires: 0");
ob_end_flush();
*/
?>