<?php
require_once 'error_logger.php';
include('genera_formularios_carga.php');
function huella($codper)
{
	$conn=conex();
	$sqlv="exec validarHuella ".$codper;
	$resultv=luis($conn, $sqlv);
	$estado=resulta($resultv,0,0);
  	cierra($resultv);
	return $estado;
}
function recodest($coda,$codb)
{
	$conn=conex();
	$sqlv="exec gclavea";
  $resultv=luis($conn, $sqlv);
  $t2=resulta($resultv,0,0);
  cierra($resultv);
	$t=semix($codb,"",$t2);
	$sql="exec upcodest ".$coda.", ".$codb.", '".$t[1]."', '".$t[2]."', '".$t[3]."'";
	// echo $sql;
	$result=luis($conn, $sql);
	$row=fetchrow($result,-1);
	if ($row[0]<>1)
	{
		$t2="";
		$newpass=gecodest($codb); // LLAMA A LA FUNCION gecodest Y GENERA UNA NUEVA CONTRASEÑA EN CASO NO EXISTA
		echo $newpass;
	}
	cierra($result);
  noconex($conn);
  return $t2;
}

function recodest_gpsalumni($codb)
{
	$conn=conex();
	$sqlv="exec gclavea";
  $resultv=luis($conn, $sqlv);
  $t2=resulta($resultv,0,0);
  cierra($resultv);
	$t=semix($codb,"",$t2);
	$sql="exec upcodest_gpsalumni ".$codb.", '".$t[1]."', '".$t[2]."', '".$t[3]."'";
	//echo $sql;
	$result=luis($conn, $sql);
	$row=fetchrow($result,-1);
	// if ($row[0]<>1)
	// {
	// 	$t2="";
	// 	$newpass=gecodest($codb); // LLAMA A LA FUNCION gecodest Y GENERA UNA NUEVA CONTRASEÑA EN CASO NO EXISTA
	// 	echo $newpass;
	// }
	cierra($result);
  noconex($conn);
  return $t2;
}


//***************** GENERA UN  NUEVO PASS **********************
function gecodest($coduniv)
{

	//	SP PARA  EL MATRIX
	require ("funciones_fichamatricula.php"); // PARA CONEXION AL MATRIX
	$conn_2=conex_fichamatricula(); // PARA CONEXION AL MATRIX
	$sql="exec sp_GenerarClaveEstudiantematrix ".$coduniv;
	//echo $sql;
	$resultado=luis_fichamatricula($conn_2, $sql);
	while ($row =fetchrow_fichamatricula($resultado,-1))
	{
		$var=$row[0];
		// echo $var;
	}
	cierra_fichamatricula($resultado);
  	noconex_fichamatricula($conn_2);

	//	SP PARA  EL UPT
	$conn=conex();
	$sql="exec sp_GenerarClaveEstudianteUPT ".$coduniv;
	$resultado=luis($conn, $sql);
	while ($row =fetchrow($resultado,-1))
	{
		$var=$row[0];
	}
	if ($var=='Ya tiene Codigo Generado')
	{
		echo $var;
	}
	else
	{
		echo 'Su nuevo codigo es: ';
		//echo'<br>';
		$pass=recodest($_SESSION['codigo'], $_SESSION['codunivx']);
		echo $pass;
	}
	cierra($resultado);
  	noconex($conn);
}
//**************************************
function recoddoc($coda,$codb)
{
	$conn=conex();
	$sqlv="exec gclavea";
  $resultv=luis($conn, $sqlv);
  $t2=resulta($resultv,0,0);
  cierra($resultv);
	$t=semix($codb,"",$t2);
	$sql="exec upcoddoc ".$coda.", ".$codb.", '".$t[1]."', '".$t[2]."', '".$t[3]."'";
	$result=luis($conn, $sql);
	$row=fetchrow($result,-1);
	if ($row[0]<>1)
	{
		$t2="";
		//$newpass=gecodest($codb); // LLAMA A LA FUNCION gecodest Y GENERA UNA NUEVA CONTRASEÑA EN CASO NO EXISTA
		//echo $newpass;
	}
	cierra($result);
  noconex($conn);
  return $t2;
}
function genera_carga_pw($coda,$codb)
{
	$conn=conex();
	$new_pw= generar_clave(6);
/*	$sqlv="exec gclavea";
  $resultv=luis($conn, $sqlv);
  $t2=resulta($resultv,0,0);
  cierra($resultv);
	$t=semix($codb,"",$t2);
	*/

	$sql="exec SP_Cambio_PW ".$coda.", ".$codb.", '".md5($new_pw)."'";
	$result=luis($conn, $sql);
	$row=fetchrow($result,-1);
	if ($row[0]<>1)
	{
		$new_pw="";
	}
	cierra($result);
  noconex($conn);

  return $new_pw;
}
function Lista_Doc_SinCodPas($op)
{
	$conn=conex();
	$sql="exec Sp_SinCodPaswd_Carga ";
	//$result=luis($conn, $sql);e

	$query = mssql_query($sql,$conn);
		if (!$query) {
		    die('MSSQL error: ' . mssql_get_last_message());
			}
	while($row = fetchrow($query,-1))
      	{
			if($op==1)
			{
				//$conn1=conex();
				$sql_1="exec Sp_GenerarPaswd_Carga " .$row[0].",".$row[1] .",'".md5(substr($row[2], 0, 3).substr($row[4], 0, 3))."'";
				//echo $sql_1;

				$query2 = mssql_query($sql_1,$conn);
			if (!$query2) {
				die('MSSQL error: ' . mssql_get_last_message());
				}


			}else{
			echo "<tr><td>".$row[0] ."</td><td>".$row[2] .$row[3] ."</td><td>".$row[4] ."</td><td></tr>";
			}
			//echo  "holas";
		}
		echo "<tr><td>Hay  que Generar Codigo</td></tr>";
	cierra($query);
  noconex($conn);

//  return $new_pw;
}

function taex($ite)
{
	$sesc="c".$ite;
	$sesi="i".$ite;
	$sesd="d".$ite;
     	$conn=conex();

     	// echo '<th><a href="javascript:imprecord()"><img border="0" src="imagenes/printer.gif" height="37" width="38"></a></th><td>&nbsp;Record Academico&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>';

	//echo '<td>Record Academico&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>';
	// echo $_SESSION[$sesd]. ' (TAEX)';
	echo '<table align="center"><tr><td align="center" colspan="5"><div style="padding: 10px;">'.$_SESSION[$sesd]. ' (TAEX)'.'</div></td></tr><tr><th>Codigo</th><th>Asignatura</th><th>Credito</th><th>Nota</th><th>Estado</th></tr>';
	//$sql="exec swrecord ".$_SESSION[$sesc].", ".$_SESSION[$sesi];
	$sql="exec swtaex ".$_SESSION[$sesc].", ".$_SESSION[$sesi];
	//echo $sql;
	$result=luis($conn, $sql);
	$nx=numrow($result);
	$ix=0;
      	$iy=0;
      	$xa="";
      	$to=0;
      	$m=0;

	//while ($ix<$nx)
      	while($row = fetchrow($result,-1))
      	{
		//$row=pg_fetch_row($result, $ix);
      		if ($row[5]>10)
		{
			$to=$to+$rov[4];
		}

      		if ($row[0]<>$xa)
		{
			$xa=$row[0];
			$iy=$iy+1;
			$sem[$iy]=$row[0];
			$semc[$iy]=$row[1];
		}

		$kim[$ix]=$ix."-";
		$ix++;
      	}
      	//mostra



      	$tm=0;
	$pa=0;
      	$in=1;
      	$ta=0;

	for ($m = 1; $m<=$iy; ++$m)
	{
		echo '<tr><td></td><td><b>'.$semc[$m].'</b></td></tr>';
		$ti=0;
		$po=0;
		$tn=0;


		//mssql_data_seek ( $result,$in-1 );
		fetchrow($result,$in-1);

		$in=0;

		while ($rov = fetchrow($result,-1) )
      		{

			if ($rov[0]==$sem[$m] and $rov[5]>10)
			{
				$ti=$ti+$rov[4];
			}
			if ($rov[0]==$sem[$m] /*and $rov[7]<>11 and $rov[7]<>12 and $rov[7]<>15*/)
			{
              if ($rov[7]<>11 and $rov[7]<>12 and $rov[7]<>15)
              {
				$tn=$tn+$rov[4];
				$po=$po+$rov[4]*$rov[5];
			  }
				echo '<tr><td>'.$rov[2].'</td>';
				echo '<td>'.$rov[3].'</td>';
				echo '<td>'.round($rov[4],0).'</td>';
				echo '<td>'.round($rov[5],0).'</td>';
				echo '<td>'.$rov[6].'</td></tr>';
			}
			if ($rov[0]<=$sem[$m])
			{
				if ($rov[0]==$sem[$m] and $rov[7]<>11 and $rov[7]<>12 and $rov[7]<>15 )
				{
					$tm=$tm+$rov[4];
					$pa=$pa+$rov[4]*$rov[5];
					if($rov[5]>10)
					{
						$ta=$ta+$rov[4];
					}
				}
			}
			else
			{
				break;
			}
			$in++;
		}
/*       echo '<tr><td></td><td><b>Prom. Pond. Ciclo: '.round(($po/$tn),2).'</b></td><td><b>Cre: '.$ti.'</b></td></td><td></tr>';*/
        echo '<tr><td></td><td><b></b></td><td><b>Cre: '.$ti.'</b></td></td><td></tr>';
		//echo '<tr><td></td><td><b>Prom. Pond. Acum.: '.round(($pa/$tm),2).'</b></td><td><b>Cre: '.$ta.'</b></td></td><td></tr>'; // LINEA DE CODIGO ORIGINAL 09/03/2012
		// ESTE IF EVITA QUE SE DE EL ERROR POR DIVISION x CERO PARA EL CASO DEL PROMEDIO DE LOS ESTUDIANTES EN  ABANDONO DE PRIMER CICLO
		if ($tm==0)
		{
		echo '<tr><td></td><td>
					<b></b></td><td><b>Cre. Ac.: '.$ta.'</b></td></td><td></tr>';
		}
		else
		{
		echo '<tr><td></td><td>
					<b></b></td><td><b>Cre. Ac.: '.$ta.'</b></td></td><td></tr>';
		}
		// ***
		echo '<tr><td> </td></tr>';
	}
      	cierra($result);
      	noconex($conn);

}

function record($ite)
{
	$sesc="c".$ite;
	$sesi="i".$ite;
	$sesd="d".$ite;
     	$conn=conex();

     	echo '<th><a href="javascript:imprecord()"><img border="0" src="imagenes/printer.gif" height="37" width="38"></a></th><td>&nbsp;Record Academico&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>';

	//echo '<td>Record Academico&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>';
	echo $_SESSION[$sesd];
	echo '<table align="center"><tr><th>Codigo</th><th>Asignatura</th><th>Credito</th><th>Nota</th><th>Estado</th></tr>';
	//$sql="exec swrecord ".$_SESSION[$sesc].", ".$_SESSION[$sesi];
	$sql="exec swrecord ".$_SESSION[$sesc].", ".$_SESSION[$sesi];
	// echo $sql;
	$result=luis($conn, $sql);
	$nx=numrow($result);
	$ix=0;
      	$iy=0;
      	$xa="";
      	$to=0;
      	$m=0;

	//while ($ix<$nx)
      	while($row = fetchrow($result,-1))
      	{
		//$row=pg_fetch_row($result, $ix);
      		if ($row[5]>10)
		{
			$to=$to+$rov[4];
		}

      		if ($row[0]<>$xa)
		{
			$xa=$row[0];
			$iy=$iy+1;
			$sem[$iy]=$row[0];
			$semc[$iy]=$row[1];
		}

		$kim[$ix]=$ix."-";
		$ix++;
      	}
      	//mostra



      	$tm=0;
	$pa=0;
      	$in=1;
      	$ta=0;

	for ($m = 1; $m<=$iy; ++$m)
	{
		echo '<tr><td></td><td><b>'.$semc[$m].'</b></td></tr>';
		$ti=0;
		$po=0;
		$tn=0;


		//mssql_data_seek ( $result,$in-1 );
		fetchrow($result,$in-1);

		$in=0;

		while ($rov = fetchrow($result,-1) )
      		{

			if ($rov[0]==$sem[$m] and $rov[5]>10)
			{
				$ti=$ti+$rov[4];
			}
			if ($rov[0]==$sem[$m] /*and $rov[7]<>11 and $rov[7]<>12 and $rov[7]<>15*/)
			{
              if ($rov[7]<>11 and $rov[7]<>12 and $rov[7]<>15)
              {
				$tn=$tn+$rov[4];
				$po=$po+$rov[4]*$rov[5];
			  }
				echo '<tr><td>'.$rov[2].'</td>';
				echo '<td>'.$rov[3].'</td>';
				echo '<td>'.round($rov[4],0).'</td>';
				echo '<td>'.round($rov[5],0).'</td>';
				echo '<td>'.$rov[6].'</td></tr>';
			}
			if ($rov[0]<=$sem[$m])
			{
				if ($rov[0]==$sem[$m] and $rov[7]<>11 and $rov[7]<>12 and $rov[7]<>15 )
				{
					$tm=$tm+$rov[4];
					$pa=$pa+$rov[4]*$rov[5];
					if($rov[5]>10)
					{
						$ta=$ta+$rov[4];
					}
				}
			}
			else
			{
				break;
			}
			$in++;
		}
/*       echo '<tr><td></td><td><b>Prom. Pond. Ciclo: '.round(($po/$tn),2).'</b></td><td><b>Cre: '.$ti.'</b></td></td><td></tr>';*/
        echo '<tr><td></td><td><b>Prom. Pond. Ciclo: ';if($po<>0){print round(($po/$tn),2);} else {print $po;} echo'</b></td><td><b>Cre: '.$ti.'</b></td></td><td></tr>';
		//echo '<tr><td></td><td><b>Prom. Pond. Acum.: '.round(($pa/$tm),2).'</b></td><td><b>Cre: '.$ta.'</b></td></td><td></tr>'; // LINEA DE CODIGO ORIGINAL 09/03/2012
		// ESTE IF EVITA QUE SE DE EL ERROR POR DIVISION x CERO PARA EL CASO DEL PROMEDIO DE LOS ESTUDIANTES EN  ABANDONO DE PRIMER CICLO
		if ($tm==0)
		{
		echo '<tr><td></td><td>
					<b>Prom. Pond. Acum.: '.$tm.'</b></td><td><b>Cre: '.$ta.'</b></td></td><td></tr>';
		}
		else
		{
		echo '<tr><td></td><td>
					<b>Prom. Pond. Acum.: '.round(($pa/$tm),2).'</b></td><td><b>Cre: '.$ta.'</b></td></td><td></tr>';
		}
		// ***
		echo '<tr><td> </td></tr>';
	}
      	cierra($result);
      	noconex($conn);

}

/**/
function recordpostgrado($ite)
{
	$sesc="c".$ite;
	$sesi="i".$ite;
	$sesd="d".$ite;
     	$conn=conex();

     	echo '<th><a href="javascript:imprecord()"><img border="0" src="imagenes/printer.gif" height="37" width="38"></a></th><td>&nbsp;Record Academico&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>';
	
	//echo '<td>Record Academico&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>';
	echo $_SESSION[$sesd];
	echo '<table align="center"><tr><th>Codigo</th><th>Asignatura</th><th>Credito</th><th>Nota</th><th>Estado</th></tr>';
	//$sql="exec swrecord ".$_SESSION[$sesc].", ".$_SESSION[$sesi];
	//$sql="exec swrecord ".$_SESSION[$sesc].", ".$_SESSION[$sesi];
	//echo $sql;
	//$result=luis($conn, $sql);
	//$nx=numrow($result);

		require_once ("funciones_fichamatricula.php"); // PARA CONEXION AL MATRIX
		$connas=conex_fichamatricula();
		$sql="exec swrecordpostgrado ".$_SESSION[$sesc].", ".$_SESSION[$sesi];
		// echo $sql;
		$result=luis_fichamatricula($connas, $sql);
		$nx=numrow($result);
		//echo '<td>Semestre:&nbsp;&nbsp;'.$_SESSION[$sesm].'</a></td></tr>';



	$ix=0;
      	$iy=0;
      	$xa="";
      	$to=0;
      	$m=0;

	//while ($ix<$nx)
      	while($row = fetchrow($result,-1))
      	{
		//$row=pg_fetch_row($result, $ix);
      		if ($row[5]>10)
		{
			$to=$to+$rov[4];
		}

      		if ($row[0]<>$xa)
		{
			$xa=$row[0];
			$iy=$iy+1;
			$sem[$iy]=$row[0];
			$semc[$iy]=$row[1];
		}

		$kim[$ix]=$ix."-";
		$ix++;
      	}
      	//mostra



      	$tm=0;
	$pa=0;
      	$in=1;
      	$ta=0;

	for ($m = 1; $m<=$iy; ++$m)
	{
		echo '<tr><td></td><td><b>'.$semc[$m].'</b></td></tr>';
		$ti=0;
		$po=0;
		$tn=0;


		//mssql_data_seek ( $result,$in-1 );
		fetchrow($result,$in-1);

		$in=0;

		while ($rov = fetchrow($result,-1) )
      		{

			if ($rov[0]==$sem[$m] and $rov[5]>10)
			{
				$ti=$ti+$rov[4];
			}
			if ($rov[0]==$sem[$m] /*and $rov[7]<>11 and $rov[7]<>12 and $rov[7]<>15*/)
			{
              if ($rov[7]<>11 and $rov[7]<>12 and $rov[7]<>15)
              {
				$tn=$tn+$rov[4];
				$po=$po+$rov[4]*$rov[5];
			  }
				echo '<tr><td>'.$rov[2].'</td>';
				echo '<td>'.$rov[3].'</td>';
				echo '<td>'.round($rov[4],0).'</td>';
				echo '<td>'.round($rov[5],0).'</td>';
				echo '<td>'.$rov[6].'</td></tr>';
			}
			if ($rov[0]<=$sem[$m])
			{
				if ($rov[0]==$sem[$m] and $rov[7]<>11 and $rov[7]<>12 and $rov[7]<>15 )
				{
					$tm=$tm+$rov[4];
					$pa=$pa+$rov[4]*$rov[5];
					if($rov[5]>10)
					{
						$ta=$ta+$rov[4];
					}
				}
			}
			else
			{
				break;
			}
			$in++;
		}
/*       echo '<tr><td></td><td><b>Prom. Pond. Ciclo: '.round(($po/$tn),2).'</b></td><td><b>Cre: '.$ti.'</b></td></td><td></tr>';*/
        echo '<tr><td></td><td><b>Prom. Pond. Ciclo: ';if($po<>0){print round(($po/$tn),2);} else {print $po;} echo'</b></td><td><b>Cre: '.$ti.'</b></td></td><td></tr>';
		//echo '<tr><td></td><td><b>Prom. Pond. Acum.: '.round(($pa/$tm),2).'</b></td><td><b>Cre: '.$ta.'</b></td></td><td></tr>'; // LINEA DE CODIGO ORIGINAL 09/03/2012
		// ESTE IF EVITA QUE SE DE EL ERROR POR DIVISION x CERO PARA EL CASO DEL PROMEDIO DE LOS ESTUDIANTES EN  ABANDONO DE PRIMER CICLO
		if ($tm==0)
		{
		echo '<tr><td></td><td>
					<b>Prom. Pond. Acum.: '.$tm.'</b></td><td><b>Cre: '.$ta.'</b></td></td><td></tr>';
		}
		else
		{
		echo '<tr><td></td><td>
					<b>Prom. Pond. Acum.: '.round(($pa/$tm),2).'</b></td><td><b>Cre: '.$ta.'</b></td></td><td></tr>';
		}
		// ***
		echo '<tr><td> </td></tr>';
	}
      	cierra($result);
      	noconex($conn);

}
/**/
function matri($ite, $url, $bolTieneEncuestaActiva = false)
{
	$sesc="c".$ite;
	$sesi="i".$ite;
	$sesd="d".$ite;
	$sess="s".$ite;
	$sesm="m".$ite;

	$sesk="k".$ite; /*nuevo 24-09-2019 postgrado*/

	$bolMostrarSilabo = true;
	// if($_SESSION[$sesc] == 2019063765){ $bolMostrarSilabo = true; }

	$sex=substr($url,18,31);

	//validación para estudiantes de postgrado
	//*******INICIO*********//

	$connlp=conex();
	$sqlvlp="select top 1 left(iddepe,3) from estudiante where activo>0 and coduniv=".$_SESSION[$sesc];
	$resultulp=luis($connlp, $sqlvlp);
	$rovlp=fetchrow($resultulp,-1);
	cierra($resultulp);
	noconex($connlp);

	if ($rovlp[0]==311 or empty($rovlp[0]))
	{

		//echo $url;
		//$conn=conex();
		//comentado el 24-09-2019 postgrado
		// require_once ("funciones_fichamatricula.php"); // PARA CONEXION AL MATRIX
		// $connas=conex_fichamatricula();
		// $sql="exec swmatrimat ".$_SESSION[$sesc].", ".$_SESSION[$sesi].", ".$_SESSION[$sess];


		$conn=conex();
		$sql="exec swmatrimatpostgrado ".$_SESSION[$sesc].", ".$_SESSION[$sesi].", ".$_SESSION[$sess];


		//echo $sql;
		// $result=luis_fichamatricula($connas, $sql);
		$result=luis($conn, $sql);
		$nx=numrow($result);
		echo '<td>Semestre2:&nbsp;&nbsp;'.$_SESSION[$sesm].'</a></td></tr>';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_SESSION[$sesd];
		if ($nx>0)
		{
			$h=0;
			$j=0;
			//$rov = mssql_fetch_row($result);
			echo '<br></br>';
			//echo $_SESSION[$sesd];
			echo '<br></br>';
			//echo '<table border="1" align="center"><tr><th></th><th>Codigo</th><th>Asignatura</th><th>Seccion</th><th>Credito</th><th>Estado</th><th>Modificado</th></tr>';
			// echo '<table border="1" align="center"><tr><th>Codigo</th><th>Asignatura</th><th>Seccion</th><th>Credito</th><th>Estado</th><th>Modificado</th></tr>'; //ULITUNO VALIDO
			echo '<table border="1" align="center">';
			echo '<tr>';
			echo '<th>Codigo</th>';
			echo '<th>Asignatura</th>';
			echo '<th>Seccion</th>';
			echo '<th>Credito</th>';
			echo '<th>Estado</th>';
			echo '<th>Modificado</th>';
			echo '</tr>';
			//*saco-echo '<table border="1" align="center"><tr><th>Codigo</th><th>Asignatura</th><th>Seccion</th><th>Credito</th><th>Estado</th><th>Modificado</th></tr>';
			// echo '<tr><td></td><td></td><td><b>'.$_SESSION[$sesm].'</b></td></tr>'; //ULITUNO VALIDO
			echo '<tr>';
			echo '<td></td>';
			echo '<td></td>';
			echo '<td><b>'.$_SESSION[$sesm].'</b></td>';
			echo '<td></td>';
			echo '<td></td>';
			echo '<td></td>';
			echo '</tr>';
			//*saco-echo '<tr><td></td><td><b>'.$_SESSION[$sesm].'</b></td></tr>';
			$ver = $_SESSION['codigo'];
			// while ($rov =fetchrow_fichamatricula($result,-1))
			while ($rov =fetchrow($result,-1))
			{
				//comentar el url2 para activar la encuesta

				//$url2 = str_replace("alumno.php", "http://www.upt.edu.pe/epic2/encuesta.php", $url);  //17/08/15 desactivar encuesta
				//***saco encuesta
				//esvc-2010 (i)al comentarlo se activa la encust por curso, al desactivarlo se desactiva la encuesta --> JR
				$url2 = $url; //---- JR   naty 17-04-2018

				//esvc-2010 (i
				//$rov=pg_fetch_row($result, $j);
				//$h=$h+$rov[3];
				$cuto= str_replace (" ", ".",rtrim($rov[1]));
				$cuto= str_replace ('"', '',$cuto);
				$cuto= str_replace ("'", "",$cuto);

				//echo $cuto;
				//*saco-if ($rov[5]>0)	{	echo '<tr><td><a href='.$url.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'  >'.$rov[0].'</a></td>';


				$iddepe = $rov[8]; //activra primero
				//  encuesta EPIS 2014 - por el ING. LANCHIPA

				//descomentar para encuestaEPIS 01-07-2016
				/*if($iddepe==314048000)
				{
					//if($_SESSION['codigo']<>2010036403)				{
					//2010036403  -  149939


					echo '<tr>
					<td>';
					$conexion = mysql_connect("172.30.101.252:3306", "root", "f14ced209ea280399c267c85073e94c4");
					mysql_select_db("encuestaEPIS", $conexion);
					$sql="SELECT * FROM encuesta e INNER JOIN user_encuesta ue ON ue.id_encuesta = e.id_encuesta
					INNER JOIN usuario u ON u.id_usuario = ue.id_usuario WHERE u.codigo_usuario = ".$_SESSION['codigo']."
					AND ue.estado_user_encuesta = 1 AND e.estado_encuesta = 1";
					//ECHO $sql;
					$resEmp = mysql_query ($sql, $conexion) or die (mysql_error ());
					$total = mysql_num_rows ($resEmp);

					if($total>0)
					{
						$va = '<a target="_blank" href="encuestaEpis/mod_respuesta/index.php?sesion='.$sex.'">'.$rov[0].'</a>';

					}
					else
					{
						$va = '<a href="'.$url2.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].'">'.$rov[0].'</a>';
					}

					echo $va;


					echo '</td>';

				}
				else
				{ //*/	//descomentar para encuestaEPIS 01-07-2016
					$conexion_enc	=	conex();
					$sql_ba = "";
					$sql_enc			=	"
						SELECT
							COUNT(*) AS 'NumeroEncuestas'
						FROM ".$sql_ba."Enc_EncuestaUsuario AS eeu
						INNER JOIN ".$sql_ba."Enc_Usuario AS eu ON eu.IdEncUsuario = eeu.IdEncUsuario
						INNER JOIN ".$sql_ba."Enc_Encuesta AS ee ON ee.IdEncEncuesta = eeu.IdEncEncuesta
						WHERE ee.Idsem = ".$_SESSION[$sess]."
						AND eeu.Eliminado = 0
						AND eu.Eliminado = 0
						AND eeu.Estado = 2
						AND ee.Estado = 1
						AND ee.IdPtaDependenciaFijo = 123
						AND CAST(GETDATE() AS DATETIME) BETWEEN ( CAST ( CONVERT(Varchar(10), ee.FechaInicio, 112) + ' ' + CONVERT(Varchar(8), ee.HoraInicio) AS DateTime) ) AND ( CAST ( CONVERT(Varchar(10), ee.FechaFin, 112) + ' ' + CONVERT(Varchar(8), ee.HoraFin) AS DateTime) )
						AND eu.Acceso = ".$_SESSION['codigo']."
					";
					// echo $sql_enc;
					$result_enc		=	luis($conexion_enc, $sql_enc);
					$row_enc			=	fetchrow($result_enc,-1);
					$bolTieneEncuestaActiva = false;
					if ($row_enc[0] > 0)
					{
						// echo "aquiii2";
						$bolTieneEncuestaActiva = true;
					}

					if($bolTieneEncuestaActiva == true /*&& $rov[9] == 1*/){ // SI TIENE ENCUESTA - comentado para que no verifique la fecha del curso 14/06/2021
						// echo '<tr><td><a href="AcaEncuesta/complements/local.php?sesion='.$sex.'" target="_blank">'.$rov[0].'</a></td>';
						if ($rov[5]>0 )
						{
							echo '<tr><td><a href="encuesta_nuevo/index.php?sesion='.$_GET["sesion"].'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'&cu='.$rov[0].'&s='.$rov[2].'&id='.$_SESSION['codigo'].'" target="_blank">'.$rov[0].'</a></td>';
						}else{
							echo '<tr><td><a href="encuesta_nuevo/index.php?sesion='.$_GET["sesion"].'&ic=0&c=0&d='.$cuto.'&cu='.$rov[0].'&s='.$rov[2].'&id='.$_SESSION['codigo'].'" target="_blank">'.$rov[0].'</a></td>';
						}
						// echo '<tr><td><a href='.$url2.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].'  >'.$rov[0].'</a></td>';
					}else{
						/*ORIGINAL*/
						if ($rov[5]>0)
						{
							echo '<tr>
									<td><a href='.$url2.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].'  >'.$rov[0].'</a></td>';
						}
						else
						{
							echo '<tr>
									<td><a href='.$url2.'&ic=0&c=0&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].' >'.$rov[0].'</td>';
						}
						/*FIN ORIGINAL*/
					}

					/*ORIGINAL*/
					// if ($rov[5]>0)
					// {
					// 	echo '<tr>
					// 			<td><a href='.$url2.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].'  >'.$rov[0].'</a></td>';
					// }
					// else
					// {
					// 	echo '<tr>
					// 			<td><a href='.$url2.'&ic=0&c=0&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].' >'.$rov[0].'</td>';
					// }
					/*FIN ORIGINAL*/
				//}	//descomentar para encuestaEPIS 01-07-2016


				//*saco-}	else 	{	echo '<tr><td>'.$rov[0].'</td>';	}
				/*}*/
				echo '<td>'.$rov[1].'</td>';
				echo '<td>'.$rov[2].'</td>';
				echo '<td>'.$rov[3].'</td>';
				// LINK MATRICULADO PARA VER LA ASISTENCIA - OK
				//echo '<td><a onclick="javascript:window.open(\'asistenciac.php?sesion='.$sex.'&id='.$rov[7].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false" href="asistenciac.php"><font size=2>'.$rov[4].'</font></a></td>';

	// **********************

				// echo '<td><a onclick="javascript:window.open(\'asistenciac.php?sesion='.$sex.'&id='.$rov[7].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false" href="asistenciac.php"><font size=2>'.substr($rov[4],0,10).'</font></a><a onclick="javascript:window.open(\'asistenciac2.php?sesion='.$sex.'&id='.$rov[7].'&cur='.$rov[1].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false" href="asistenciac2.php"><font size=2>o</font></a></td>';

	echo '<td>
	<a onclick="javascript:window.open(\'asistenciac2.php?sesion='.$sex.'&id='.$rov[7].'&cur='.$rov[1].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false" href="asistenciac2.php"><font size=2>'.$rov[4].'</font></a></td>';

	// **********************

				echo '<td>'.$rov[6].'</td></tr>';
				$j++;


				//if ($rov[5]>0)	{	echo '<tr><td><a href='.$rov[7].'><h6>Encuesta</h6></a></td><td><a href='.$url.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'  >'.$rov[0].'</a></td>';
				//}	else 	{	echo '<tr><td><a href='.$rov[7].'><h6>Encuesta</h6></a></td><td>'.$rov[0].'</td>';	}

			}
			//echo '<tr><td></td><td><b>'.$h.'</b></td></tr>';
		}
		// cierra_fichamatricula($result); comentado 24-09-2019 postgrado
		// noconex_fichamatricula($conn);
		cierra($result);
		noconex($conn);
	}
	else
	{ //*************FIN***********//

		//echo $url;
		$conn=conex();
		$sql="exec swmatri ".$_SESSION[$sesc].", ".$_SESSION[$sesi].", ".$_SESSION[$sess];
		MostrarTextoAdmin("Listado de Cursos: ", $sql);
		$result=luis($conn, $sql);
		$nx=numrow($result);
		echo '<td>Semestre3:&nbsp;&nbsp;'.$_SESSION[$sesm].'</a></td></tr>';
		echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_SESSION[$sesd];
		if ($nx>0)
		{
			$h=0;
			$j=0;
			//$rov = mssql_fetch_row($result);
			echo '<br></br>';
			//echo $_SESSION[$sesd];
			echo '<br></br>';
			//echo '<table border="1" align="center"><tr><th></th><th>Codigo</th><th>Asignatura</th><th>Seccion</th><th>Credito</th><th>Estado</th><th>Modificado</th></tr>';
			// echo '<table border="1" align="center"><tr><th>Codigo</th><th>Asignatura</th><th>Seccion</th><th>Credito</th><th>Estado</th><th>Modificado</th></tr>'; //ULITUNO VALIDO
			echo '<table border="1" align="center">';
			echo '<tr>';
			echo '<th>Codigo</th>';
			echo '<th>Asignatura</th>';
			echo '<th>Seccion</th>';
			echo '<th>Credito</th>';
			echo '<th>Estado</th>';
			echo '<th>Modificado</th>';
			if($bolMostrarSilabo == true){ echo '<th colspan="2">Sílabo</th>'; }
			echo '</tr>';
			//*saco-echo '<table border="1" align="center"><tr><th>Codigo</th><th>Asignatura</th><th>Seccion</th><th>Credito</th><th>Estado</th><th>Modificado</th></tr>';
			// echo '<tr><td></td><td></td><td><b>'.$_SESSION[$sesm].'</b></td></tr>'; //ULITUNO VALIDO

			$colspan_semestre = 6;
			if($bolMostrarSilabo == true){
				$colspan_semestre = 8;
			}
			echo '<tr>';
			// echo '<td></td>';
			// echo '<td></td>';
			echo '<td colspan="'.$colspan_semestre.'" align="center"><b>'.$_SESSION[$sesm].'</b></td>';
			// echo '<td></td>';
			// echo '<td></td>';
			// echo '<td></td>';
			echo '</tr>';
			//*saco-echo '<tr><td></td><td><b>'.$_SESSION[$sesm].'</b></td></tr>';
			$ver = $_SESSION['codigo'];


			// /* ================================================================================= */
			// /* VERIFICACION DE ENCUESTAS - ACADEMICO - Roberto M.*/  //modificado naty 28-11-2017
			// /* ================================================================================= */
			// if( $rovlp[0] == 314 ){
			// 	$conexion_enc	=	conex();
			// 	// $sql_ba = "UPT20180321_2023.dbo.";
			// 	$sql_ba = "";
			// 	//$sql_ba = "UPT.dbo.";
			// 	$sql_enc			=	"
			// 		SELECT
			// 			COUNT(*) AS 'NumeroEncuestas'
			// 		FROM ".$sql_ba."Enc_EncuestaUsuario AS eeu
			// 		INNER JOIN ".$sql_ba."Enc_Usuario AS eu ON eu.IdEncUsuario = eeu.IdEncUsuario
			// 		INNER JOIN ".$sql_ba."Enc_Encuesta AS ee ON ee.IdEncEncuesta = eeu.IdEncEncuesta
			// 		WHERE ee.Idsem = ".$_SESSION[$sess]."
			// 		AND eeu.Eliminado = 0
			// 		AND eu.Eliminado = 0
			// 		AND eeu.Estado = 2
			// 		AND ee.Estado = 1
			// 		AND eu.Acceso = ".$_SESSION['codigo']."
			// 		AND CAST(GETDATE() AS DATETIME) BETWEEN ( CAST ( CONVERT(Varchar(10), ee.FechaInicio, 112) + ' ' + CONVERT(Varchar(8), ee.HoraInicio) AS DateTime) ) AND ( CAST ( CONVERT(Varchar(10), ee.FechaFin, 112) + ' ' + CONVERT(Varchar(8), ee.HoraFin) AS DateTime) )
			// 	";
			// 	$result_enc		=	luis($conexion_enc, $sql_enc);
			// 	$row_enc			=	fetchrow($result_enc,-1);
			// 	//echo $sql_enc;
			// 	$bolTieneEncuestaActiva = false;
			// 	if ($row_enc[0] > 0)
			// 	{
			// 		// echo "aquiii2";
			// 		$bolTieneEncuestaActiva = true;
			// 	}

			// 	// if( $_SESSION['codigo'] != 2012043676 ){
			// 	// 	$bolTieneEncuestaActiva = false; //COMENTAR
			// 	// }else{
			// 	// 	$bolTieneEncuestaActiva = true;
			// 	// }

			// 	 $_SESSION['EncIdSemestre']  = $_SESSION[$sess];
			// }
			// /* ================================================================================= */


			while ($rov =fetchrow($result,-1))
			{
				//comentar para activar la encuesta DE LOS ALUMNOS

				// $url2 = str_replace("alumno.php", "http://www.upt.edu.pe/epic2/encuesta.php", $url);  //17/08/15 desactivar encuesta
				//***saco encuesta
				//esvc-2010 (i)al comentarlo se activa la encust por curso, al desactivarlo se desactiva la encuesta --> JR
				 $url2 = $url; //---- JR naty 17-04-2018
				  // if($_SESSION['codigo']==2006027626){
						// $url2 = str_replace("alumno.php", "http://www.upt.edu.pe/epic2/encuesta.php", $url);  //17/08/15 desactivar encuesta
					 // }
				//esvc-2010 (i
				//$rov=pg_fetch_row($result, $j);
				//$h=$h+$rov[3];
				$cuto= str_replace (" ", ".",rtrim($rov[1]));
				$cuto= str_replace ('"', '',$cuto);
				$cuto= str_replace ("'", "",$cuto);

				//echo $cuto;
				//*saco-if ($rov[5]>0)	{	echo '<tr><td><a href='.$url.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'  >'.$rov[0].'</a></td>';


				$iddepe = $rov[8]; //activra primero
				//  encuesta EPIS 2014 - por el ING. LANCHIPA

				//descomentar para encuestaEPIS //01-07-2016
				/*if($iddepe==314048000)
				{
					//if($_SESSION['codigo']<>2010036403)				{
					//2010036403  -  149939


					echo '<tr>
					<td>';
					//$conexion = mysql_connect("172.30.101.252:3306", "root", "f14ced209ea280399c267c85073e94c4");   28-11-2016
					//mysql_select_db("encuestaEPIS", $conexion);	   28-11-2016

					$conexion = mysqli_connect("172.30.101.252:3306", "root", "f14ced209ea280399c267c85073e94c4");
					mysqli_select_db("encuestaEPIS", $conexion);

					$sql="SELECT * FROM encuesta e INNER JOIN user_encuesta ue ON ue.id_encuesta = e.id_encuesta
					INNER JOIN usuario u ON u.id_usuario = ue.id_usuario WHERE u.codigo_usuario = ".$_SESSION['codigo']."
					AND ue.estado_user_encuesta = 1 AND e.estado_encuesta = 1";
					//ECHO $sql;

					//$resEmp = mysql_query ($sql, $conexion) or die (mysql_error ());  28-11-2016
					//$total = mysql_num_rows ($resEmp);   28-11-2016


					$resEmp = mysqli_query ($sql, $conexion) or die (mysqli_error ());
					$total = mysqli_num_rows ($resEmp);
					if($total>0)
					{
						$va = '<a target="_blank" href="encuestaEpis/mod_respuesta/index.php?sesion='.$sex.'">'.$rov[0].'</a>';

					}
					else
					{
						$va = '<a href="'.$url2.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].'">'.$rov[0].'</a>';
					}

					echo $va;


					echo '</td>';

				}
				else
				{ //*/	//descomentar para encuestaEPIS
					if($bolTieneEncuestaActiva == true){ // SI TIENE ENCUESTA
						// echo '<tr><td><a href="AcaEncuesta/complements/local.php?sesion='.$sex.'" target="_blank">'.$rov[0].'</a></td>';
						if ($rov[5]>0)
						{
							echo '<tr><td><a href="encuesta_nuevo/index.php?sesion='.$_GET["sesion"].'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'&cu='.$rov[0].'&s='.$rov[2].'&id='.$_SESSION['codigo'].'" target="_blank">'.$rov[0].'</a></td>';
						}else{
							echo '<tr><td><a href="encuesta_nuevo/index.php?sesion='.$_GET["sesion"].'&ic=0&c=0&d='.$cuto.'&cu='.$rov[0].'&s='.$rov[2].'&id='.$_SESSION['codigo'].'" target="_blank">'.$rov[0].'</a></td>';
						}
						// echo '<tr><td><a href='.$url2.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].'  >'.$rov[0].'</a></td>';
					}else{
						/*ORIGINAL*/
						if ($rov[5]>0)
						{
							echo '<tr>
									<td><a href='.$url2.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].'  >'.$rov[0].'</a></td>';
						}
						else
						{
							echo '<tr>
									<td><a href='.$url2.'&ic=0&c=0&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].' >'.$rov[0].'</td>';
						}
						/*FIN ORIGINAL*/
					}
				//}	//descomentar para encuestaEPIS


				//*saco-}	else 	{	echo '<tr><td>'.$rov[0].'</td>';	}
				/*}*/
				echo '<td>'.$rov[1].'</td>';
				echo '<td>'.$rov[2].'</td>';
				echo '<td>'.$rov[3].'</td>';

				// LINK MATRICULADO PARA VER LA ASISTENCIA - OK
				//echo '<td><a onclick="javascript:window.open(\'asistenciac.php?sesion='.$sex.'&id='.$rov[7].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false" href="asistenciac.php"><font size=2>'.$rov[4].'</font></a></td>';

	// **********************

				// echo '<td><a onclick="javascript:window.open(\'asistenciac.php?sesion='.$sex.'&id='.$rov[7].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false" href="asistenciac.php"><font size=2>'.substr($rov[4],0,10).'</font></a><a onclick="javascript:window.open(\'asistenciac2.php?sesion='.$sex.'&id='.$rov[7].'&cur='.$rov[1].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false" href="asistenciac2.php"><font size=2>o</font></a></td>';

	echo '<td>
	<a onclick="javascript:window.open(\'asistenciac2.php?sesion='.$sex.'&id='.$rov[7].'&cur='.$rov[1].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false" href="asistenciac2.php"><font size=2>'.$rov[4].'</font></a></td>';

	// **********************

				echo '<td>'.$rov[6].'</td>';
				if($bolMostrarSilabo == true){
					echo '<td align="center">';
					if($rov[10] > 0){
						echo '<a href="#" onclick="AbrirPDFSilabo('.$rov[10].', '.$rov[9].', \''.$sex.'\')"><font size=2>Silabo</font></a>';
					}
					echo '</td>';
					echo '<td><font size=1><strong>'.$rov[12].'</strong></font></td>';
				}
				echo '</tr>';
				$j++;


				//if ($rov[5]>0)	{	echo '<tr><td><a href='.$rov[7].'><h6>Encuesta</h6></a></td><td><a href='.$url.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'  >'.$rov[0].'</a></td>';
				//}	else 	{	echo '<tr><td><a href='.$rov[7].'><h6>Encuesta</h6></a></td><td>'.$rov[0].'</td>';	}

			}
			//echo '<tr><td></td><td><b>'.$h.'</b></td></tr>';
		}
		cierra($result);
		noconex($conn);
	}
}
/*
function matri_enc($ite, $url)
{
	$sesc="c".$ite;
	$sesi="i".$ite;
	$sesd="d".$ite;
	$sess="s".$ite;
	$sesm="m".$ite;

	$sex=substr($url,18,31);

	$conn=conex();
	$sql="exec swmatri ".$_SESSION[$sesc].", ".$_SESSION[$sesi].", ".$_SESSION[$sess];
	//echo $sql;
	$result=luis($conn, $sql);
	$nx=numrow($result);
	echo '<td>Semestre:&nbsp;&nbsp;'.$_SESSION[$sesm].'</a></td></tr>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_SESSION[$sesd];
	if ($nx>0)
	{
		$h=0;
		$j=0;
		//$rov = mssql_fetch_row($result);
		echo '<br></br>';
		//echo $_SESSION[$sesd];
		echo '<br></br>';
		//echo '<table border="1" align="center"><tr><th></th><th>Codigo</th><th>Asignatura</th><th>Seccion</th><th>Credito</th><th>Estado</th><th>Modificado</th></tr>';
		echo '<table border="1" align="center"><tr><th>Codigo</th><th>Asignatura</th><th>Seccion</th><th>Credito</th><th>Estado</th><th>Modificado</th></tr>';
		//*saco-echo '<table border="1" align="center"><tr><th>Codigo</th><th>Asignatura</th><th>Seccion</th><th>Credito</th><th>Estado</th><th>Modificado</th></tr>';
		echo '<tr><td></td><td></td><td><b>'.$_SESSION[$sesm].'</b></td></tr>';
		//*saco-echo '<tr><td></td><td><b>'.$_SESSION[$sesm].'</b></td></tr>';
		while ($rov =fetchrow($result,-1))
		{
			$url2 = str_replace("alumno.php", "http://www.upt.edu.pe/sistencuesta/docentes.php", $url);
			//***saco encuesta
			//esvc-2010 (i)al comentarlo se activa la encust por curso, al desactivarlo se desactiva la encuesta
			$url2 = $url;

			//esvc-2010 (i
			//$rov=pg_fetch_row($result, $j);
			//$h=$h+$rov[3];
			$id_enc=2;
			$cuto= str_replace (" ", ".",rtrim($rov[1]));
			$cuto= str_replace ('"', '',$cuto);
			$cuto= str_replace ("'", "",$cuto);
			//echo $cuto;
			//*saco-if ($rov[5]>0)	{	echo '<tr><td><a href='.$url.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'  >'.$rov[0].'</a></td>';
			if ($rov[5]>0)	{	echo '<tr><td><a href='.$url2.'&ic='.$ite.'&c='.$rov[5].'&idenc='.md5($id_enc).'&id='.$_SESSION['codigo'].'&cu='.$rov[0].'  >'.$rov[0].'</a></td>';
			}	else 	{	echo '<tr><td><a href='.$url2.'&ic=0&c=0&d='.$cuto.'&id='.$_SESSION['codigo'].'&cu='.$rov[0].' >'.$rov[0].'</td>';	}
			//*saco-}	else 	{	echo '<tr><td>'.$rov[0].'</td>';	}
			echo '<td>'.$rov[1].'</td>';
			echo '<td>'.$rov[2].'</td>';
			echo '<td>'.$rov[3].'</td>';
			echo '<td><a onclick="javascript:window.open(\'asistenciac.php?sesion='.$sex.'&id='.$rov[7].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false" href="asistenciac.php">'.$rov[4].'</a></td>';
			echo '<td>'.$rov[6].'</td></tr>';
			$j++;


			//if ($rov[5]>0)	{	echo '<tr><td><a href='.$rov[7].'><h6>Encuesta</h6></a></td><td><a href='.$url.'&ic='.$ite.'&c='.$rov[5].'&d='.$cuto.'  >'.$rov[0].'</a></td>';
			//}	else 	{	echo '<tr><td><a href='.$rov[7].'><h6>Encuesta</h6></a></td><td>'.$rov[0].'</td>';	}

		}
		//echo '<tr><td></td><td><b>'.$h.'</b></td></tr>';
	}
      	cierra($result);
      	noconex($conn);
}*/


function curso($ite,$c,$d)
{
	$sesc="c".$ite;
	$sesi="i".$ite;
	$sesd="d".$ite;
	$sess="s".$ite;
	$sesm="m".$ite;

	// $conn=conex();
	// $sql="exec swcurso ".$_SESSION[$sesc].", ".$c;
	// //echo $sql;

/*nuevo 24-09-2019*/
	$sesk="k".$ite;

	$conn=conex();
	
	//echo $sql;

	if ( round($_SESSION[$sesk]/1000000) == 311)
	{
		$sql="exec usp_inp_listarnotaspostgrado ".$_SESSION[$sesc].", ".$c;
	}
	else
	{
		$sql="exec swcurso ".$_SESSION[$sesc].", ".$c;
	}
// echo $_SESSION['c1'];
// echo $sql;

	$result=luis($conn, $sql);
	$nx=numrow($result);
	echo '<td>Semestre1:&nbsp;&nbsp;'.$_SESSION[$sesm].'</a></td></tr>';
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$_SESSION[$sesd];
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$d;
	//echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=consultam.php?i='.$ite.'&sesion='.$sex.' >Regresar</a><br><br>';
	if ($nx>0)
	{

	echo '<table border="0" cellspacing="0"><tr><th bgcolor="#DBEAF5">Unidad</th><th bgcolor="#DBEAF5">Criterio</th><th bgcolor="#DBEAF5">Peso C.</th><th bgcolor="#DBEAF5">Peso U.</th><th bgcolor="#DBEAF5">Nota</th><th bgcolor="#DBEAF5">Fecha</th><th bgcolor="#DBEAF5">Descripcion</th></tr>';
	$tu=0;
	$tc=0;
	$lv=0;
	$tcl=0;
	$cn=0;
	$nu=0;
	$nc=0;
	$pro=0;
	$pru=0;
	$per=0;

	$in=1;
	fetchrow($result,$in-1);
        while ($row =fetchrow($result,-1))
        {
        if ($row[2]<>$lv)
        {
        	if ($tcl>0)
        	{
        		$apr="";
        		if (number_format($tc,  1, '.', ',')!=100)
			{
				$apr='color="#FF0000"';
			}
        		//$pro=round($pro,2);
        		//$pru=round($pru,2);
        		echo '<tr><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"><font size="1">&nbsp;</font></td><td bgcolor="#F3F9FC"><font '.$apr.' >'.number_format($tc,  1, '.', ',').' %</font></td><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC">&nbsp;&nbsp;'.number_format(($pro), 2, '.', ',').'</td><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"></td></tr>';
        	}
        	$lv=$row[2];
        }
        	$cn++;

        	if ($row[2]==2)
        	{
        		$nu++;
        		echo '<tr><td>'.$row[0].'</td><td></td>';
        		$tu=$tu+$row[1];
        		$per=$row[1];
			$tc=0;
			$pro=0;
			$tcl=0;
        		echo '<td></td><td>'.$row[1].'</td>';
        	}
        	else
        	{
        		$nc++;
        		echo '<tr><td></td><td>'.$row[0].'&nbsp;&nbsp;&nbsp;</td>';
        		$tc=$tc+$row[1];
        		$pro=$pro+($row[3]*($row[1]/100));
        		$pru=$pru+(($row[3]*($row[1]/100))*($per/100));


        		//print '<BR>'.$row[3].' '.($row[1]/100).' '.($row[3]*($row[1]/100));
        		//$pro=$pro+round(($row[3]*($row[1]/100)),2);
        		//$pru=$pru+round(($row[3]*($row[1]/100))*($per/100),2);
			$tcl++;
        		echo '<td>'.$row[1].'</td><td></td>';
        	}


               $apr='color="#FF0000"';
		if (number_format($row[3], 2, '.', ',')>=11)
		{
			$apr='color="#0000FF"';
		}
                echo ' <td><font '.$apr.' >&nbsp;&nbsp;'.$row[3].'</font></td>';
                echo ' <td>&nbsp;&nbsp;'.$row[4].'</td>';
                echo ' <td>&nbsp;&nbsp;'.$row[5].'</td></tr>';

        }
        if ($tcl>0)
        	{
        		$apr="";
        		if (number_format($tc,  1, '.', ',')!=100)
			{
				$apr='color="#FF0000"';
			}
        		$pro=round($pro,2);
        		$pru=round($pru,2);
        		echo '<tr><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC">&nbsp;</td><td bgcolor="#F3F9FC"><font '.$apr.' >'.number_format($tc,  1, '.', ',').' %</font></td><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC">&nbsp;&nbsp;'.number_format(($pro), 2, '.', ',').'</td><td bgcolor="#F3F9FC"></td><td bgcolor="#F3F9FC"></td></tr>';
        	}
        $apr="";
        if (number_format($tu,  1, '.', ',')!=100)
	{
		$apr='color="#FF0000"';
	}
	$pito=number_format(($pru/$nu), 2, '.', ',');

        echo '<tr><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5"><font '.$apr.' >'.number_format($tu,  1, '.', ',').' %</font></td><td bgcolor="#DBEAF5">&nbsp;&nbsp;'.number_format(($pru), 0, '.', ',').'</td><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5">&nbsp;</td></tr>';
        // echo '<tr><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5"><font '.$apr.' >'.number_format($tu,  1, '.', ',').' %</font></td><td bgcolor="#DBEAF5">&nbsp;&nbsp;'.number_format((ROUND ($pru,1)), 0, '.', ',').'</td><td bgcolor="#DBEAF5">&nbsp;</td><td bgcolor="#DBEAF5">&nbsp;</td></tr>'; //ECHO ROUND($pru,1);
	echo '</table>';

	}

      	cierra($result);
      	noconex($conn);
}

function deudax($codper)
{

	$conn=conex();
	// $sql="exec spensiona ".$codper;
	$sql="exec spensiona_v2 ".$codper;
	$result=luis($conn, $sql);
	$nx=numrow($result);
	$_SESSION['deuda']=0;
	if ($nx>0)
	{
		$h=0;
		$j=1;
		while ($rov = fetchrow($result,-1))
		{
			//$rov=pg_fetch_row($result, $j);
			$h=$h+$rov[1];
			if ($rov[3]==1)
			{
				$fo='#CC6600">';
			}
			else
			{
				$fo='#000000">';
			}


			$j++;
		}
	}
      	cierra($result);
      	noconex($conn);
      	// return round($h,1); 	// original
		return ($h);
}

//funion para deuda libros
//funion para deuda libros

function deudalibrosx($codper)
{
//aqui va el /*
	$conn=conex();
	$sql="exec sdeudabibliox ".$codper;
	$result=luis($conn, $sql);
	$nx=numrow($result);
	$_SESSION['deuda']=0;
	if ($nx>0)
	{
		$h=0;
		$j=1;
		while ($rov = fetchrow($result,-1))
		{
			//$rov=pg_fetch_row($result, $j);
			$h=$rov[3];
			if ($rov[3]==1)
			{
				$fo='#CC6600">';
			}
			else
			{
				$fo='#000000">';
			}


			$j++;
		}
	}
      	cierra($result);
      	noconex($conn);
      	return round($h,1);
//aqui cierra
}

function deuda($codper)
{
	require ("funciones_fichamatricula.php"); // PARA CONEXION AL MATRIX
	$conn=conex_fichamatricula();
	// $sql="exec spensiona ".$codper;
	$sql="exec spensiona_v2 ".$codper;
	$result=luis_fichamatricula($conn, $sql);
	$nx=numrow($result);
	echo 'Deuda:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	$_SESSION['deuda']=0;
	if ($nx>0)
	{
		$h=0;
		$j=1;
		echo '<table align="center"><tr><th>Item</th><th>Descripcion</th><th>Monto</th><th>Vence</th></tr>';
		while ($rov = fetchrow_fichamatricula($result,-1))
		{
			//$rov=pg_fetch_row($result, $j);
			$h=$h+$rov[1];
			if ($rov[3]==1)
			{
				$fo='#CC6600">';
			}
			else
			{
				$fo='#000000">';
			}
			// $rov[0] = descrip
			// $rov[1] = total
			// $rov[2] = vence
			// $rov[3] = multa
				if($rov[1] > 0)
				{
				echo '<tr><td><font color="'.$fo.$j.'</font></td>';
				echo '<td><font color="'.$fo.$rov[0].'</font></td>';
				echo '<td><font color="'.$fo.round($rov[1],2).'</font></td>';
				echo '<td><font color="'.$fo.$rov[2].'</font></td></tr>';
				}

			$j++;
		}
		// echo '<tr><td></td><td></td><td><b>Total S/.&nbsp;&nbsp;&nbsp;&nbsp;'.round($h,1).' </b></td></tr>'; 	// original
		echo '<tr><td></td><td></td><td><b>Total S/.&nbsp;&nbsp;&nbsp;&nbsp;'.$h.' </b></td></tr>';
		$_SESSION['deuda']=round($h,1);
	}
	else
	{
		echo '<br>S/.&nbsp;&nbsp;&nbsp;&nbsp;0.00 &nbsp;&nbsp;&nbsp;&nbsp;No tiene deuda hasta la fecha</br>';
	}
      	cierra_fichamatricula($result);
      	noconex_fichamatricula($conn);
}



// function deudaINICIO_anterior($codper)
// {
// 	//require ("funciones_fichamatricula.php"); // PARA CONEXION AL MATRIX
// 	$conn=conex_fichamatricula();
// 	// $sql="exec spensiona ".$codper;
// 	$sql="exec spensiona_v2 ".$codper;
// 	//echo $sql;
// 	$result=luis_fichamatricula($conn, $sql);
// 	$nx=numrow($result);
// 	echo '<font size="2"> <strong>Deuda:</strong> </font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
// 	$_SESSION['deuda']=0;
// 	if ($nx>0)
// 	{
// 		$h=0;
// 		$j=1;
// 		echo '<table  width="100%">
// 		<tr>
// 			<th><font size="2"> <strong>Nro</strong> </font></th>
// 			<th><font size="2"> <strong>Descripcion</strong> </font></th>
// 			<th><font size="2"> <strong>Monto</strong> </font></th>
// 			<th><font size="2"> <strong>Vence</strong> </font></th>
// 			<th><font size="2"> <strong>Dependecia</strong> </font></th>
// 			<th><font size="2"> <strong>Semestre</strong> </font></th>
// 		</tr>';
// 		while ($rov = fetchrow_fichamatricula($result,-1))
// 		{

// 			$h=$h+$rov[1];
// 			if ($rov[3]==1)
// 			{
// 				$fo='#CC6600">';
// 			}
// 			else
// 			{
// 				$fo='#000000">';
// 			}

// 				if($rov[1] > 0)
// 				{
// 				echo '<tr>
// 				<td align="center"><font color="'.$fo.$j.'</font></td>
// 				<td align="left"><font color="'.$fo.$rov[0].'</font></td>
// 				<td align="center"><font color="'.$fo.round($rov[1],2).'</font></td>
// 				<td align="center"><font color="'.$fo.$rov[2].'</font></td>
// 				<td align="center">&nbsp;&nbsp;&nbsp;&nbsp;<font color="'.$fo.$rov[3].'</font></td>
// 				<td align="center">&nbsp;&nbsp;&nbsp;&nbsp;<font color="'.$fo.$rov[4].'</font></td>
// 				<td align="center"><font color="'.$fo.$rov[5].'</font></td></tr>';
// 				}

// 			$j++;
// 		}
// 		// echo '<tr><td></td><td></td><td><b>Total S/.&nbsp;&nbsp;&nbsp;&nbsp;'.round($h,1).' </b></td></tr>'; 	// original
// 		echo '<tr><td></td><td align="right"><font size="2"> <strong>Total S/.</strong> </font></td><td><b>&nbsp;&nbsp;'.$h.'</b></td></tr>';
// 		$_SESSION['deuda']=round($h,1);
// 	}
// 	else
// 	{
// 		echo '<br><font size="2">S/.&nbsp;&nbsp;&nbsp;&nbsp;0.00 &nbsp;&nbsp;&nbsp;&nbsp;No tiene deuda hasta la fecha</font></br>';
// 	}
// 	cierra_fichamatricula($result);
// 	noconex_fichamatricula($conn);
// }

function deudaINICIO($codper)
{
	//require ("funciones_fichamatricula.php"); // PARA CONEXION AL MATRIX
	$conn=conex_fichamatricula();
	// $sql="exec spensiona ".$codper;
	$sql="exec spensiona_v2 ".$codper;
	//echo $sql;
	$result=luis_fichamatricula($conn, $sql);
	$nx=numrow($result);
	// echo '<font style="font-size:12px;"> <strong>Deuda:</strong> </font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	$_SESSION['deuda']=0;
	if ($nx>0)
	{
		$h=0;
		$j=1;
		echo '<table  width="100%" class="GeneratedTable">
		<thead>
			<tr>
				<th><font style="font-size:12px;"> <strong>Nro</strong> </font></th>
				<th><font style="font-size:12px;"> <strong>Descripción</strong> </font></th>
				<th><font style="font-size:12px;"> <strong>Monto</strong> </font></th>
				<th><font style="font-size:12px;"> <strong>Vence</strong> </font></th>
				<th><font style="font-size:12px;"> <strong>Dependencia</strong> </font></th>
				<th><font style="font-size:12px;"> <strong>Semestre</strong> </font></th>
			</tr>
		</thead>';
		echo '<tbody>';
		while ($rov = fetchrow_fichamatricula($result,-1))
		{

			$h=$h+$rov[1];
			if ($rov[3]==1)
			{
				$fo='#CC6600">';
			}
			else
			{
				$fo='#000000">';
			}

				if($rov[1] > 0)
				{
				echo '<tr>
				<td align="center"><font style="font-size:12px;" color="'.$fo.$j.'</font></td>
				<td align="left"><font style="font-size:12px;" color="'.$fo.$rov[0].'</font></td>
				<td align="center"><font style="font-size:12px;" color="'.$fo.round($rov[1],2).'</font></td>
				<td align="center"><font style="font-size:12px;" color="'.$fo.$rov[2].'</font></td>
				<td align="center"><font style="font-size:12px;" color="'.$fo.$rov[3].'</font></td>
				<td align="center"><font style="font-size:12px;" color="'.$fo.$rov[4].'</font></td>
				<!--<td align="center"><font style="font-size:12px;" color="'.$fo.$rov[5].'</font></td>--></tr>';
				}

			$j++;
		}
		echo '</tbody>';
		echo '<tfoot>';
		// echo '<tr><td></td><td></td><td><b>Total S/.&nbsp;&nbsp;&nbsp;&nbsp;'.round($h,1).' </b></td></tr>'; 	// original
		echo '<tr><td colspan="2" align="right"><font style="font-size:12px;"> <strong>Total S/.</strong> </font></td><td align="center"><b>'.$h.'</b></td><td colspan="3"></td></tr>';
		$_SESSION['deuda']=round($h,1);
		echo '</tfoot>';
		echo '<table>';
	}
	else
	{
		echo '<table  width="100%" class="GeneratedTable">
		<thead>
			<tr>
				<th><font style="font-size:12px;"> <strong>Nro</strong> </font></th>
				<th><font style="font-size:12px;"> <strong>Descripción</strong> </font></th>
				<th><font style="font-size:12px;"> <strong>Monto</strong> </font></th>
				<th><font style="font-size:12px;"> <strong>Vence</strong> </font></th>
				<th><font style="font-size:12px;"> <strong>Dependencia</strong> </font></th>
				<th><font style="font-size:12px;"> <strong>Semestre</strong> </font></th>
			</tr>
		</thead>';
		echo '<tbody>';
			echo '<tr><td colspan="6" align="center"><font style="font-size:13px;">No tiene deuda hasta la fecha</font></td></tr>';
		echo '</tbody>';
		echo '<table>';
	}
	cierra_fichamatricula($result);
	noconex_fichamatricula($conn);
}

function fncBuscarDeuda($codper)
{
	$conn = conex_fichamatricula();
	$sql = "exec spensiona_v2 ".$codper;
	$result = luis_fichamatricula($conn, $sql);
	$nx = numrow($result);

	$ValorDeuda = 0;

	if ($nx>0){
		$h = 0;

		while ($rov = fetchrow_fichamatricula($result,-1)){
			$h=$h+$rov[1];
		}
		$ValorDeuda = round($h, 1);
	}
	else{
		$ValorDeuda = 0;
	}
	cierra_fichamatricula($result);
	noconex_fichamatricula($conn);
	return $ValorDeuda;
}


// function deuda_libro($codper)
// {
// 	// $conn=conex();
// 	$conn = conex_fichamatricula();
// 	echo '<br/><br/>';
// 	$sqllp="exec deuda_libro_principal ".$codper;
// 	// $resultlp=luis($conn, $sqllp);
// 	$resultlp=luis_fichamatricula($conn, $sqllp);
// 	$nx=numrow($resultlp);
// 	echo '<font size="2"> <strong>Deuda Libro: </strong> </font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>';
// 	echo '<table width="100%"><tr><td colspan="4">';
// 	if ($nx>0)
// 	{
// 		echo '<font style="font-weight:bold;" color="#F00">Tiene Deuda de Libros</font>';
// 	}
// 	else
// 	{
// 		echo '<font size="2">No tiene deuda de libro hasta la fecha</font>';
// 	}
// 	echo '</td></tr></table>';
//       	cierra($result);
//       	noconex($conn);
// }

function deuda_libro($codper)
{
	$conn = conex_fichamatricula();
	echo '<br/>';
	$sqllp="exec deuda_libro_principal ".$codper;
	$resultlp=luis_fichamatricula($conn, $sqllp);
	$nx=numrow($resultlp);
	echo '<font style="font-size:12px;"> <strong>Deuda Libro: </strong> </font> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br/>';
	echo '<table width="100%"><tr><td colspan="4">';
	if ($nx>0)
	{
		echo '<font style="font-weight:bold; font-size:12px;" color="#F00">Tiene Deuda de Libros</font>';
	}
	else
	{
		echo '<font style="font-size:12px;">No tiene deuda de libro hasta la fecha</font>';
	}
	echo '</td></tr></table>';
      	cierra($result);
      	noconex($conn);
}


function matrivista($codigo, $conn)
{
	//$conn=conex();
		$kl=0;
		//$sqla="select a.idcurso, monto, creditos, a.descurso, dia, left(convert(varchar(11),ini,108),5) ini, left(convert(varchar(11),fin,108),5) fin, codcurso, ciclo, estado, a.tipo, a.iddepe, a.seccion from matweb a left join (select idhora, dia, idcurso, iddepe, idsem, seccion, fhora ini from horarioc where tipo=0 ) b on a.iddepe=b.iddepe and a.idcurso=b.idcurso and b.seccion=a.seccion and b.idsem in (select idsem from semestre where tipo=1 and activo=1) left join (select idhora, fhora fin from horarioc where tipo=1) c on b.idhora=c.idhora where tipo between 1 and 9 and coduniv=".$codigo." order by dia, ini";
		//$sqla="select a.idcurso, 0 monto, 0 creditos, ac.descurso, dia, left(convert(varchar(11),ini,108),5) ini, left(convert(varchar(11),fin,108),5) fin, ac.codcurso, ac.ciclocurso, 1 estado, 1 tipo, ab.iddepe, a.seccion from cursomatricula a inner join estudiante ab on a.coduniv=ab.coduniv and a.itemest=ab.itemest inner join curso ac on ac.idcurso=a.idcurso left join (select idhora, dia, idcurso, iddepe, idsem, seccion, fhora ini from horarioc where tipo=0 ) b on ab.iddepe=b.iddepe and a.idcurso=b.idcurso and b.seccion=a.seccion and b.idsem in (select idsem from semestre where tipo=1 and activo=1) left join (select idhora, fhora fin from horarioc where tipo=1) c on b.idhora=c.idhora where ab.iddepe=314047000 and a.idsem=20090201 and codestado between 1 and 9 and a.coduniv=".$codigo." order by dia, ini";
		$sqla="select a.idcurso, monto, creditos, a.descurso, dia, left(convert(varchar(11),ini,108),5) ini, left(convert(varchar(11),fin,108),5) fin, codcurso, ciclo, estado, a.tipo, a.iddepe, a.seccion from matweb a left join (select idhora, dia, idcurso, iddepe, idsem, seccion, fhora ini from horarioc where tipo=0 ) b on a.iddepe=b.iddepe and a.idcurso=b.idcurso and b.seccion=a.seccion and b.idsem in (select idsem from semestre where idsem = 20130231) left join (select idhora, fhora fin from horarioc where tipo=1) c on b.idhora=c.idhora where tipo between 1 and 9 and coduniv=".$codigo." order by dia, ini";
		$resulta=luis($conn, $sqla);
		//$resulta=luis($conn, "select * from estudiante");
		$ok=0;
		while ($row =fetchrow($resulta,-1))
		{

			$ok=1;//$k++;
			if ($kl<>$row[0]){
				$kl=$row[0];
				$vr=0;
				For ($i=1;$i<=$km;$i++)
				{
					if($kl==$va[$i][1]){ $vr=1; }
				}
				if ($vr==0){
					$km++;
					$va[$km][0]=$row[8].$row[7];
					$va[$km][1]=$row[0];
					$va[$km][2]=$row[7];
					$va[$km][3]=$row[3];
					$va[$km][4]=$row[2];
					$va[$km][5]=$row[9];
					$va[$km][6]=$row[12];
					$_SESSION['iddepe']=$row[11];
					if ($va[$i][5]==1 or $va[$i][5]==3){
						$toa=$toa+$row[1];
						$tob=$tob+$row[2];
						$tip=$va[$i][5];
						$ptip=$row[10];
					}
				}
			}
		     if ($row[9]==1 or $row[9]==3){
		   	if (is_null($row[4])){
				$g++;
				$tx[$g]=$row[3];
			}
			if ($row[4] == 1) {
			$d[1]++;
			$ta[1][$d[1]]=$row[3];
			$tb[1][$d[1]]=$row[5];
			$tc[1][$d[1]]=$row[6];
			$td[1][$d[1]]=0;
			} elseif ($row[4] == 2) {
    			$d[2]++;
			$ta[2][$d[2]]=$row[3];
			$tb[2][$d[2]]=$row[5];
			$tc[2][$d[2]]=$row[6];
			$td[2][$d[2]]=0;
			} elseif ($row[4] == 3) {
    			$d[3]++;
    			$ta[3][$d[3]]=$row[3];
			$tb[3][$d[3]]=$row[5];
			$tc[3][$d[3]]=$row[6];
			$td[3][$d[3]]=0;
			} elseif ($row[4] == 4) {
 		   	$d[4]++;
		    	$ta[4][$d[4]]=$row[3];
			$tb[4][$d[4]]=$row[5];
			$tc[4][$d[4]]=$row[6];
			$td[4][$d[4]]=0;
		    	} elseif ($row[4] == 5) {
		    	$d[5]++;


		    	$ta[5][$d[5]]=$row[3];
			$tb[5][$d[5]]=$row[5];
			$tc[5][$d[5]]=$row[6];
			$td[5][$d[5]]=0;
    			} elseif ($row[4] == 6) {
    			$d[6]++;
    			$ta[6][$d[6]]=$row[3];
			$tb[6][$d[6]]=$row[5];
			$tc[6][$d[6]]=$row[6];
			$td[6][$d[6]]=0;
    			} elseif ($row[4] == 7) {
    			$d[7]++;
    			$ta[7][$d[7]]=$row[3];
			$tb[7][$d[7]]=$row[5];
			$tc[7][$d[7]]=$row[6];
			$td[7][$d[7]]=0;
			}
		    }
		}
	//if ($ok==1 or $_SESSION['codigo']==364337){
	if ($ok==1 or $_SESSION['codigo']==2011040884){
		$btm=0;
		$mu=0;
		if ($tob>=18 and $tob<=28){
			$btm=1;
		}
		$actu="(";
		For ($i=1;$i<=$km;$i++)
		{
			$actu=$actu.$va[$i][1].',';
		}
		$actu=substr_replace ($actu, ')', (strlen($actu)-1), 1);
		$sqlo="select idcurso, seccion from lupita where iddepe=".$_SESSION['iddepe']." and idcurso in ".$actu." order by idcurso";
		$resulto=luis($conn, $sqlo);
		while ($rov=fetchrow($resulto,-1))
		{
			$nsec++;
			$rsec[0][$nsec]=$rov[0];
			$rsec[1][$nsec]=$rov[1];
		}
		cierra($resulto);
		sort($va);
		For ($i=0;$i<$km;$i++)
		{
			$_SESSION['idcur'.$i]=$va[$i][1];
			//$_SESSION['secc'.$i]=$va[$i][6];
		}
		if ($mu==0){
			For ($j=1;$j<=7;$j++)
			{
				if ($d[$j]>$mu)
				{
					$mu=$d[$j];
				}
			}
		}
		For ($j=1;$j<=7;$j++)
		{
			For ($k=1;$k<=$mu;$k++)
			{
				if ($d[$j]>0 and $d[$j]>=$k)
				{
					$xini=(substr($tb[$j][$k],0,2).substr($tb[$j][$k],3,2))*1+0.5;
					$xfin=(substr($tc[$j][$k],0,2).substr($tc[$j][$k],3,2))*1;
					$er=0;
					$erc=0;
					For ($l=1;$l<=$mu;$l++)
					{
						if ($l<>$k )
						{
							$yini=(substr($tb[$j][$l],0,2).substr($tb[$j][$l],3,2))*1+0.5;
							$yfin=(substr($tc[$j][$l],0,2).substr($tc[$j][$l],3,2))*1;
							if ($yini>=$xini and $yini<=$xfin and $yfin>0 and $xfin>0)
							{
								$er=1;
							}
							if ($yfin>=$xini and $yfin<=$xfin and $yfin>0 and $xfin>0)
							{
								$er=1;
							}
							if ($yini>$xfin and $xini>0)
							{
								$erc++;
							}
							if ($yfin>$xfin and $xini>0)
							{
								$erc++;
							}
						}
					}
					if (($erc%2)<>0)
					{
						$er=1;
					}
					$td[$j][$k]=$er;
				}
			}
		}

		//echo '<table border="1" width="100%" id="table9" cellspacing="0">';
		echo '<table border="1" width="100%" cellspacing="0">';
		//echo '<tr><td colspan="7"><font size="4">&nbsp;Horario tentativo de: '.$codigo.'&nbsp;&nbsp;&nbsp;</font><font size="1" color="#FF0000">*Nota </font><font size="1">si ve que algunos casilleros se pinta de este color:&nbsp;<span style="background-color: #feb6b1">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;es por que existe cruce de horarios</font></td></tr>';
		echo '<tr>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Lunes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Martes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Miércoles</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Jueves</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Viernes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Sábado</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Domingo</font></td>';
		echo '</tr>';
		For ($m=1;$m<=$g;$m++)
		{
			if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}
			echo '</tr>';
			For ($j=1;$j<=7;$j++)
			{
				if ($j==1) {
					echo '<td bgcolor="'.$tcol.'" width="92"><table border="0"><tr>';
					echo '<td bgcolor="'.$tcol.'"><font size="1">'.$tx[$m].'</font></td>';
					echo '<td bgcolor="'.$tcol.'"><font size="1">&nbsp;&nbsp;:&nbsp;&nbsp;</font></td>';
					echo '<td bgcolor="'.$tcol.'"><font size="1">&nbsp;&nbsp;:&nbsp;&nbsp;</font></td>';
					echo '</tr></table></td>';
				} else { echo '<td bgcolor="'.$tcol.'" width="92"></td>';}
			}
			echo '</tr>';
		}
		For ($k=1;$k<=$mu;$k++)
		{
			if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}
			echo '</tr>';
			For ($j=1;$j<=7;$j++)
			{
				if ($d[$j]>0 and $d[$j]>=$k) {
					if ($td[$j][$k]==1){$tcola='#FEB6B1';}else{$tcola=$tcol;}
					echo '<td bgcolor="'.$tcol.'" width="92"><table border="0" width="100%"><tr>';
					echo '<td bgcolor="'.$tcola.'"><font size="1">'.$ta[$j][$k].'</font></td>';
					echo '<td bgcolor="'.$tcola.'"><font size="1">'.$tb[$j][$k].'</font></td>';
					echo '<td bgcolor="'.$tcola.'"><font size="1">'.$tc[$j][$k].'</font></td>';
					echo '</tr></table></td>';
				} else { echo '<td bgcolor="'.$tcol.'" width="92"></td>';}
			}
			echo '</tr>';
		}
		echo '</table>';
		cierra($resulta);
		//deuda(43730);
	}else{echo ' Esta opcion no esta disponible para usted por el momento.';}
      	//noconex($conn);

}
function matricula($codigo, $sex)
{

	$conn=conex();
	if ($_POST["op"]==1)
	{
		//$actu="(";
		$actu="(";
		while (list ($clave, $val) = each ($_POST)) {
    			if (substr($clave, 0, 3)=="chk")
    			{
    				//$actu=$actu.$_SESSION['idcur'.$val].",";
    				$actu=$actu."'".$_POST['sec'.$val].$_SESSION['idcur'.$val]."',";
    				$ck++;
    				$tck[$ck]=$clave;
    			}
		}
		//echo $xactu;
		//print $actu;
		if (strlen($actu)>1){
			//$actu=substr_replace ($actu, ')', (strlen($actu)-1), 1);
			$actu=substr_replace ($actu, ')', (strlen($actu)-1), 1);
			$sql="update matweb set estado=case when b.seccion is null then 0 else 1 end, seccion=case when b.seccion is null then a.seccion else b.seccion end from matweb a left join (select idsem, iddepe, idcurso, seccion from Lupita where iddepe=".$_SESSION['iddepe']." and seccion+cast(idcurso as varchar(10)) in ".$actu." ) b on a.idsem=b.idsem and a.iddepe=b.iddepe and a.idcurso=b.idcurso where coduniv=".$codigo;
			//echo $sql;
			//$sql="update matweb set estado=case when idcurso in ".$actu." then 1 else 0 end where coduniv=".$codigo;
		}else{
			$sql="update matweb set estado=0 where coduniv= ".$codigo;
		}
		$result=luis($conn, $sql);
	}
		$kl=0;
		//saco -> $sqla="select a.idcurso, monto, creditos, a.descurso, dia, left(convert(varchar(11),ini,108),5) ini, left(convert(varchar(11),fin,108),5) fin, codcurso, ciclo, estado, a.tipo, a.iddepe, a.seccion from matweb a left join (select idhora, dia, idcurso, iddepe, idsem, seccion, fhora ini from horarioc where tipo=0 ) b on a.iddepe=b.iddepe and a.idcurso=b.idcurso and b.seccion=a.seccion and b.idsem in (select idsem from semestre where tipo=1 and activo=1) left join (select idhora, fhora fin from horarioc where tipo=1) c on b.idhora=c.idhora where tipo between 1 and 9 and coduniv=".$codigo." order by dia, ini";
		$sqla="select a.idcurso, monto, creditos, a.descurso, dia, left(convert(varchar(11),ini,108),5) ini, left(convert(varchar(11),fin,108),5) fin, codcurso, ciclo, estado, a.tipo, a.iddepe, a.seccion 		 from matweb a left join (select idhora, dia, idcurso, iddepe, idsem, seccion, fhora ini from horarioc where tipo=0 ) b on a.iddepe=b.iddepe and a.idcurso=b.idcurso and b.seccion=a.seccion and b.idsem in (select idsem from semestre where tipo=1 and activo=1) left join (select idhora, fhora fin from horarioc where tipo=1) c on b.idhora=c.idhora where tipo between 1 and 9 and coduniv=".$codigo." order by dia, ini";

		//echo $sqla;
		$resulta=luis($conn, $sqla);
		//$resulta=luis($conn, "select * from estudiante");
		//echo $sqla;
		$ok=0;
		//echo "01X";
		while ($row =fetchrow($resulta,-1))
		{
			$ok=1;//$k++;
			// echo "aqui estamos";
			if ($kl<>$row[0]){
				$kl=$row[0];
				$vr=0;
				For ($i=1;$i<=$km;$i++)
				{
					if($kl==$va[$i][1]){ $vr=1; }
				}
				if ($vr==0){
					$km++;
					$va[$km][0]=$row[8].$row[7]; 	// $row[8] EL ciclo Y $row[7] ES codcurso
					$va[$km][1]=$row[0]; 			// $row[0] ES idcurso
					$va[$km][2]=$row[7];			// $row[7] ES codcurso
					$va[$km][3]=$row[3];			// $row[3] ES descurso
					$va[$km][4]=$row[2];			// $row[2] ES creditos
					$va[$km][5]=$row[9];			// $row[9] ES estado
					$va[$km][6]=$row[12];			// $row[12] ES seccion
					$_SESSION['iddepe']=$row[11];	// $row[11] ES iddepe
					if ($va[$i][5]==1 or $va[$i][5]==3){
						$toa=$toa+$row[1];			// $row[1] ES monto
						$tob=$tob+$row[2];			// $row[2] ES creditos
						$tip=$va[$i][5];			// $row[5] ES ini
						$ptip=$row[10];				// $row[10] ES tipo
					}
				}
			}

		    if ($row[9]==1 or $row[9]==3){			// $row[9] ES estado
		   	if (is_null($row[4]))					// $row[4] ES dia
			{
				$g++;
				$tx[$g]=$row[3];
			}
			if ($row[4] == 1)   				// $row[4] ES dia
			{
			$d[1]++;
			$ta[1][$d[1]]=$row[3];				// $row[3] ES descurso
			$tb[1][$d[1]]=$row[5];				// $row[5] ES ini
			$tc[1][$d[1]]=$row[6];				// $row[6] ES fin
			$td[1][$d[1]]=0;
			$te[1][$d[1]]=$row[8];				// INDICA EL CICLO AL QUE PERTENCE EL CURSO
			}
			elseif ($row[4] == 2)  				// $row[4] ES dia
			{
    		$d[2]++;
			$ta[2][$d[2]]=$row[3];				// $row[3] ES descurso
			$tb[2][$d[2]]=$row[5];				// $row[5] ES ini
			$tc[2][$d[2]]=$row[6];				// $row[6] ES fin
			$td[2][$d[2]]=0;
			$te[2][$d[2]]=$row[8];				// INDICA EL CICLO AL QUE PERTENCE EL CURSO
			}
			elseif ($row[4] == 3)  				// $row[4] ES dia
			{
			$d[3]++;
			$ta[3][$d[3]]=$row[3];				// $row[3] ES descurso
			$tb[3][$d[3]]=$row[5];				// $row[5] ES ini
			$tc[3][$d[3]]=$row[6];				// $row[6] ES fin
			$td[3][$d[3]]=0;
			$te[3][$d[3]]=$row[8];				// INDICA EL CICLO AL QUE PERTENCE EL CURSO
			}
			elseif ($row[4] == 4)  				// $row[4] ES dia
			{
 		   	$d[4]++;
		    $ta[4][$d[4]]=$row[3];				// $row[3] ES descurso
			$tb[4][$d[4]]=$row[5];				// $row[5] ES ini
			$tc[4][$d[4]]=$row[6];				// $row[6] ES fin
			$td[4][$d[4]]=0;
  			$te[4][$d[4]]=$row[8];				// INDICA EL CICLO AL QUE PERTENCE EL CURSO
		    	}
			elseif ($row[4] == 5)  				// $row[4] ES dia
			{
			$d[5]++;
			$ta[5][$d[5]]=$row[3];				// $row[3] ES descurso
			$tb[5][$d[5]]=$row[5];				// $row[5] ES ini
			$tc[5][$d[5]]=$row[6];				// $row[6] ES fin
			$td[5][$d[5]]=0;
			$te[5][$d[5]]=$row[8];				// INDICA EL CICLO AL QUE PERTENCE EL CURSO
    		}
			elseif ($row[4] == 6)   			// $row[4] ES dia
			{
			$d[6]++;
			$ta[6][$d[6]]=$row[3];				// $row[3] ES descurso
			$tb[6][$d[6]]=$row[5];				// $row[5] ES ini
			$tc[6][$d[6]]=$row[6];				// $row[6] ES fin
			$td[6][$d[6]]=0;
			$te[6][$d[6]]=$row[8];				// INDICA EL CICLO AL QUE PERTENCE EL CURSO
    		}
			elseif ($row[4] == 7)   			// $row[4] ES dia
			{
    		$d[7]++;
    		$ta[7][$d[7]]=$row[3];				// $row[3] ES descurso
			$tb[7][$d[7]]=$row[5];				// $row[5] ES ini
			$tc[7][$d[7]]=$row[6];				// $row[6] ES fin
			$td[7][$d[7]]=0;
			$te[7][$d[7]]=$row[8];				// INDICA EL CICLO AL QUE PERTENCE EL CURSO
			}
		    }
		}
	//if ($ok==1 or $_SESSION['codigo']==364337){
//	echo $ok;
//	echo '<br>';
//	echo $tob;
//	echo '<br>';
//	echo $_POST["op"];
//	echo '<br>';
//	echo $codigo;
//	echo ' - ';
//	echo $_POST["pp"];
//	echo '<br>';

	// if ($ok==1 or $_SESSION['codigo']==2011040884){
	if ($ok==1){
		$btm=0;
		$mu=0;
		if ($tob>=18 and $tob<=28){
			$btm=1;
			if ($_POST["op"]==2){
				// echo $codigo;
				//	echo ' - ';
				// echo $_POST["pp"];
				$sql="exec karma ".$codigo.", ".$_POST["pp"];
				$result=luis($conn, $sql);
				$row=fetchrow($result,-1);
				if ($row[0]==3){
					For ($i=1;$i<=$km;$i++)
					{
						if ($va[$i][5]>0){
							$va[$i][5]=3;
						}else{
							$va[$i][5]=2;
						}
					}
					$tip=3;
				}elseif($row[0]==4){
					For ($i=1;$i<=$km;$i++)
					{
						$va[$i][5]=4;
					}
				}
				$msj=$row[1];
				cierra($result);
			}
			if ($_POST["op"]==3)
			{
				$sql="exec purga ".$codigo;
				$result=luis($conn, $sql);
				$row=fetchrow($result,-1);
				if ($row[0]==0){
					For ($i=1;$i<=$km;$i++)
					{
						$va[$i][5]=0;
					}
					$tip=0;
				}elseif($row[0]==0){
					For ($i=1;$i<=$km;$i++)
					{
						$va[$i][5]=4;
					}
				}
				$toa=0;
				$tob=0;
				$g=0;

				$mu=-1;
				$msj=$row[1];
				cierra($result);
			}
		}
		$actu="(";
		For ($i=1;$i<=$km;$i++)
		{
			$actu=$actu.$va[$i][1].',';
		}
		$actu=substr_replace ($actu, ')', (strlen($actu)-1), 1);
		// SELECCIONA LOS CURSOS Y LA SECCION
		//$_SESSION['iddepe']= 312041000;
		$sqlo="select idcurso, seccion from lupita where iddepe=".$_SESSION['iddepe']." and idcurso in ".$actu." order by idcurso";
		$resulto=luis($conn, $sqlo);
		//echo $sqlo;
		while ($rov=fetchrow($resulto,-1)){
			$nsec++;
			$rsec[0][$nsec]=$rov[0];
			$rsec[1][$nsec]=$rov[1];
		}
		cierra($resulto);
		sort($va);
		For ($i=0;$i<$km;$i++)
		{
			$_SESSION['idcur'.$i]=$va[$i][1];
			//$_SESSION['secc'.$i]=$va[$i][6];
		}
		if ($mu==0){
			For ($j=1;$j<=7;$j++)
			{
				if ($d[$j]>$mu)
				{
					$mu=$d[$j];
				}
			}
		}
		For ($j=1;$j<=7;$j++)
		{
			For ($k=1;$k<=$mu;$k++)
			{
				if ($d[$j]>0 and $d[$j]>=$k)
				{
					$xini=(substr($tb[$j][$k],0,2).substr($tb[$j][$k],3,2))*1+0.5;
					$xfin=(substr($tc[$j][$k],0,2).substr($tc[$j][$k],3,2))*1;
					$er=0;
					$erc=0;
					For ($l=1;$l<=$mu;$l++)
					{
						if ($l<>$k )
						{
							$yini=(substr($tb[$j][$l],0,2).substr($tb[$j][$l],3,2))*1+0.5;
							$yfin=(substr($tc[$j][$l],0,2).substr($tc[$j][$l],3,2))*1;
							if ($yini>=$xini and $yini<=$xfin and $yfin>0 and $xfin>0)
							{
								$er=1;
							}
							if ($yfin>=$xini and $yfin<=$xfin and $yfin>0 and $xfin>0)
							{
								$er=1;
							}
							if ($yini>$xfin and $xini>0)
							{
								$erc++;
							}
							if ($yfin>$xfin and $xini>0)
							{
								$erc++;
							}
						}
					}
					if (($erc%2)<>0)
					{
						$er=1;
					}
					$td[$j][$k]=$er;
				}
			}
		}
		echo '<FORM METHOD="POST" ACTION="alumno.php?co='.$codigo.'&sesion='.$sex.'" name="frmmatri">';

		echo '<table border="1" width="85%" cellspacing="0">';
		echo '<tr>
				<td colspan="2"><font size="4">Matricula</td>
				<td colspan="3"><font size="1" ><b>Pasos a seguir:</b></font><font size="1"> Seleccione los cursos a matricularse, dele click en Vista Previa para ver el costo de la pension y horarios; finalmente si esta deacuerdo dele click en Matricularse </font><font size="1" color="#0000FF"> [Minimo de creditos 18 Maximo 28]</font></td>
			  </tr>';
		echo '<tr>
				<td bgcolor="#dbeaf5"><font size="1">Seleccion</font></td>
				<td bgcolor="#dbeaf5"><font size="1">Codigo</font></td>
				<td bgcolor="#dbeaf5"><font size="1">Curso</font></td>
				<td bgcolor="#dbeaf5"><font size="1">Seccion</font></td>
				<td bgcolor="#dbeaf5"><font size="1">Creditos</font></td>
			  </tr>';
		For ($i=0;$i<$km;$i++)
		{
			if($va[$i][5]==0)
			{
				$tsel='<input type="checkbox" name="chk'.$i.'" value="'.$i.'" onClick="javascript:document.frmmatri.op.value=10" >';
			}
			elseif($va[$i][5]==1)
			{
				$tsel='<input type="checkbox" name="chk'.$i.'" value="'.$i.'" onClick="javascript:document.frmmatri.op.value=10" checked>';
			}
			elseif($va[$i][5]==2)
			{
				$tsel='<img src="imagenes/mat2.jpg" width=16 height=17 alt="Edit file" border=0>';
			}
			elseif($va[$i][5]==3)
			{
				$tsel='<img src="imagenes/mat3.jpg" width=16 height=17 alt="Edit file" border=0>';
			}
			else
			{
				$tsel='<img src="imagenes/mat4.jpg" width=16 height=17 alt="Edit file" border=0>';
			}
			if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}

			// echo $sqlo;	// devuelve idcurso, seccion de los cursos que puede llevar el estudiante

			echo '<tr>';
					echo '<td bgcolor="'.$tcol.'"><font size="1">'.$tsel.'</font></td>';  // Seleccion
					echo '<td bgcolor="'.$tcol.'"><font size="1">'.$va[$i][2].'</font></td>'; // Codigo
					echo '<td bgcolor="'.$tcol.'"><font size="1">'.$va[$i][3].'</font></td>';	// curso
			echo '<td bgcolor="'.$tcol.'"><select name="sec'.$i.'" size="1" >';			// Seccion

			// ****** 	para deteterminar que secciones puedo matricularme	********
//			$sqlo="select idcurso, seccion from lupita where iddepe=".$_SESSION['iddepe']." and idcurso in ".$actu." order by idcurso";
//			$resulto=luis($conn, $sqlo);
//		//echo $sqlo;
//			while ($rov=fetchrow($resulto,-1))
//			{
//				$nsec++;
//				$rsec[0][$nsec]=$rov[0];
//				$rsec[1][$nsec]=$rov[1];
//			}
//			cierra($resulto);
			// ****** 	hasta aqui para deteterminar que secciones puedo matricularme	****

			For ($j=0;$j<=$nsec;$j++)
			{
				if ($rsec[0][$j]==$va[$i][1])
				{
					if($rsec[1][$j]==$va[$i][6]){$lsec=" selected";}else{$lsec="";}
					// $rsec[0][$j] es el idcurso
					// $rsec[1][$j] es la seccion. Por ejemplo: A o B  ; M o N

					// actual 25/06/2013 , funciona
					//  echo '<option value="'.$rsec[1][$j].'" '.$lsec.' >XYZ'.$rsec[1][$j].'</option>';			// orginal funciona

			// *** PARA CONSULTAR LOS TOPES ***

			  $comita1 = "'%";
			  $comita2 = "%'";
			  $regular = 90101;
			  $comilla = "'";
			  $sqlidsem="SELECT * FROM semestre WHERE idmodalidadsem LIKE ".$comita1.$regular.$comita2." AND activo = 1";  // captura idsem actual
			  $resultaidsem=luis($conn,$sqlidsem);
			  while($rowidsem = fetchrow($resultaidsem,-1))
			  {
				  $estuidsem=$rowidsem[0]; // idsem
			  }
			  cierra($resultaidsem);

			$conn_2=conex_fichamatricula();
			// funciona 25-06-2013
			// $sqltope="select * from seccion_tope WHERE idsem = ".$estuidsem." AND idcurso = ".$rsec[0][$j]." AND seccion =".$comilla.$rsec[1][$j].$comilla;
			$sqltope="select tope,reg = (select count(coduniv) from cursomatricula where idsem = ".$estuidsem." and seccion =".$comilla.$rsec[1][$j].$comilla." and idcurso=".$rsec[0][$j]." and codestado = 1 ) from seccion_tope where idsem = ".$estuidsem." and seccion =".$comilla.$rsec[1][$j].$comilla." and iddepe = ".$_SESSION['iddepe']." and idcurso=".$rsec[0][$j];
			$resultado_tope=luis_fichamatricula($conn_2, $sqltope);
			while ($rowtope =fetchrow_fichamatricula($resultado_tope,-1))
				{
					// idsem , idcurso , seccion , tope , iddepe , reg
					//$idsem_tope = $rowtope[0];
					//$idcurso_tope = $rowtope[1];
					//$tope = $rowtope[3];
					//$reg = $rowtope[5];

					$tope = $rowtope[0];
					$reg = $rowtope[1];
					// $capacidad_aula = ($tope * 70)/ 100;
					$topes = (($tope * 70)/100);
					$capacidad_aula = round($topes);
					// $capacidad_aula = $tope - $reg; // original
				}
				cierra_fichamatricula($resultado_tope);


			// echo '<option value="'.$rsec[1][$j].'" '.$lsec.' >'.$rsec[1][$j].'-'.$estuidsem.'-'.$sqltope.'</option>';

					// if($capacidad_aula >=5 )  // original
					if($capacidad_aula > $reg)
					{
						// echo '<option value="'.$rsec[1][$j].'" '.$lsec.' >'.$rsec[1][$j].'-'.$estuidsem.'-'.$idsem_tope.'-'.$idcurso_tope.'-'.$tope.'-'.$reg.'-'.$capacidad_aula.'</option>';
						// iddepe  = $_SESSION['iddepe']
						echo '<option value="'.$rsec[1][$j].'" '.$lsec.' >'.$rsec[1][$j].'</option>';				// original
					}
			// *** FIN DE PARA CONSULTAR LOS TOPES ***
				}
			}
			echo '</select></td>';
			echo '<td bgcolor="'.$tcol.'"> <font size="1">'.$va[$i][4].'</font></td></tr>';
		}
		//echo '</table>';
		echo '<tr>
				<td colspan="3">
				<td align="right"><font size="1"><b>Total:&nbsp;&nbsp;</b></font></td>
				<td ><font size="1"><b> '.$tob.'</b></font></td>
			  </tr>
			</table>';
		// *****	FUNCION PARA DETERMINAR EL NUMERO ACTUAL DE CURSOS DEL CICLO	*****
		For ($k=1;$k<=$mu;$k++)
		{
			For ($j=1;$j<=7;$j++)
			{
				if ($d[$j]>0 and $d[$j]>=$k)
				{
					// ALGORITMO PARA DETERMINAR EL CICLO AL QUE PERTENECE EL CURSO
					switch ($te[$j][$k])
					{
					case 1:
						$C01 = 1;
						break;
					case 2:
						$C02 = 1;
						break;
					case 3:
						$C03 = 1;
						break;
					case 4:
						$C04 = 1;
						break;
					case 5:
						$C05 = 1;
						break;
					case 6:
						$C06 = 1;
						break;
					case 7:
						$C07 = 1;
						break;
					case 8:
						$C08 = 1;
						break;
					case 9:
						$C09 = 1;
						break;
					case 10:
						$C010 = 1;
						break;
					case 11:
						$C011 = 1;
						break;
					case 12:
						$C012 = 1;
						break;
					}
					// HASTA AQUI DETERMINO A QUE CICLO PERTENCE EL CURSO
				}
			}
		}

		$total_cursosabc=$C01 + $C02 + $C03 + $C04 + $C05 + $C06 + $C07 + $C08 + $C09 + $C10 + $C11 + $C12;
		// ******************	FIN NUMERO ACTUAL DE CURSOS 	*********************
		if ($tip==1 and $btm==1)
		{
			if($total_cursosabc<4)
			{
				$tbtn='<input type=button class="btns" value="Matricularse" name="btni" onClick="javascript:rebuildValues(2)">';
			}
			$tbtm='<input type=button class="btns" value="Vista Previa del Horario" name="btnu" onClick="javascript:rebuildValues(1)">';
		}
		elseif($tip==3)
		{
			$tbtn='<input type=button class="btns" value="Anular Matricula" name="btnd" onClick="javascript:rebuildValues(3)">';
			$tbtm='';
		}
		else
		{
			$tbtn='';
			$tbtm='<input type=button class="btns" value="Vista Previa del Horario" name="btnu" onClick="javascript:rebuildValues(1)">';
		}
		echo '<br>';
		echo '<table border="0" width="85%">';
		echo '<tr>';
				echo '<td width="88" align="center">'.$tbtm.'</td>';
				echo '<td width="200" align="center">'.$tbtn.'</td>';
				echo '<td>';
					echo '<input type="text" name="tmsj" value="    '.$msj.'" autocomplete="off" readonly="true" size="60" style="border:0; font-size:12pt; font-weight:bold">';
				echo '</td>';

		// *** PA LA PREGUNTA	***
	  //echo $_POST["chkpregunta"];
		if  (($tob > 0) and ($msj <> 'Se matriculo con Exito'))  // orden 2
		{
		  // ********************************
		  echo '<br>';
		  $seguroupt = 1;
		  echo '<strong>Pregunta:</strong> ¿Desea acceder al seguro para estudiantes de la Universidad?';
		  //echo '<input type="checkbox" name="chkpregunta" value="'.$seguroupt.'" >';
		  echo '&nbsp;&nbsp;&nbsp;';
		  echo '<strong>Si</strong> <input type="radio" value="1" checked name="chkpregunta">';
		  echo '&nbsp;&nbsp;&nbsp;';
		  echo '<strong>No</strong> <input type="radio" value="2" name="chkpregunta">';


		}
		else
		{
		  // if($_POST["chkpregunta"] > 0) // original
		  if($_POST["chkpregunta"] ==1)
		  {
			  $usuario = 'web';
			  $comita1 = "'%";
			  $comita2 = "%'";
			  $regular = 90101;
			  $comilla = "'";

			  $sqlidsem="SELECT * FROM semestre WHERE idmodalidadsem LIKE ".$comita1.$regular.$comita2." AND activo = 1";  // captura idsem actual
			  $resultaidsem=luis($conn,$sqlidsem);
			  while($rowidsem = fetchrow($resultaidsem,-1))
			  {
				  $estuidsem=$rowidsem[0]; // idsem
			  }
			  cierra($resultaidsem);

			  require_once ("funciones_fichamatricula.php"); // PARA CONEXION AL MATRIX
			  $conn_matrix=conex_fichamatricula();
			  $sqltrix="exec sp_add_seguroupt ".$_SESSION['codigo'].", ".$estuidsem.", ".$_SESSION['iddepe'].", ".$_POST["chkpregunta"].", ".$comilla.$usuario.$comilla;
			  $resultado_trix=luis_fichamatricula($conn_matrix, $sqltrix);
			  cierra_fichamatricula($resultado_trix);
			  noconex_fichamatricula($conn_matrix);
		  }
		  // else // original
		  if($_POST["chkpregunta"] ==2)
		  {
			  $zero = 0;
			  $usuario = 'web';
			  $comita1 = "'%";
			  $comita2 = "%'";
			  $regular = 90101;
			  $comilla = "'";

			  $sqlidsem="SELECT * FROM semestre WHERE idmodalidadsem LIKE ".$comita1.$regular.$comita2." AND activo = 1";  // captura idsem actual
			  $resultaidsem=luis($conn,$sqlidsem);
			  while($rowidsem = fetchrow($resultaidsem,-1))
			  {
				  $estuidsem=$rowidsem[0]; // idsem
			  }
			  cierra($resultaidsem);

			  require_once ("funciones_fichamatricula.php"); // PARA CONEXION AL MATRIX
			  $conn_matrix=conex_fichamatricula();
			  $sqltrix="exec sp_add_seguroupt ".$_SESSION['codigo'].", ".$estuidsem.", ".$_SESSION['iddepe'].", ".$zero.", ".$comilla.$usuario.$comilla;
			  $resultado_trix=luis_fichamatricula($conn_matrix, $sqltrix);
			  cierra_fichamatricula($resultado_trix);
			  noconex_fichamatricula($conn_matrix);
		  }
		}
		// ********************************
		// *** HASTA AQUI PA LA PREGUNTA	***

		echo '</tr>';
		if ($tob>0){
		        $toti=($toa+100);///saco 100 matricula
				// DESHABLITO EL EL PRONTO PAGO PARA QUE SE LLEVE A CABO UN  SOLO PAGO
				echo '<input type="hidden" name="pp" maxlength="10" value="2">';
				// *****************************************************
			//echo '<tr><td><font size="1">Pronto Pago</font></td><td><table border="0" width="100%">';
//			echo '<tr><td><input type="radio" value="2" checked name="pp"><font size="1">normal sin descuento</font></td></tr>';
//			echo '<tr><td><input type="radio" value="3" name="pp"><font size="1">tres cuotas 8% descto</font></td></tr>';
//			echo '<tr><td><input type="radio" value="4" name="pp"><font size="1">cuatro cuotas 9% descto</font></td></tr>';
//			echo '<tr><td><input type="radio" value="5" name="pp"><font size="1">cinco cuotas 10% descto</font></td></tr>';
			echo '</table></td>';

		// ********
			$hora_matricula = date("H");
			$minutos_matricula = date("i");
			$segundos_matricula = date("s");
			// IF para bancos BANBIF y BCP
			// IF BCP
			if($minutos_matricula > 20)
			{
				$hbcp = $hora_matricula + 1;
			}
			else
			{
				$hbcp = $hora_matricula;
			}
			// BANBIF
			$hbanbif = $hora_matricula + 1;
		if  ($msj == 'Se matriculo con Exito')
		{
				echo '<td width="88" align="center">';
				// PARA INDICAR SI SELECCIONO AFILIARSE AL SEGURO
				$conn_2=conex_fichamatricula();
				$sql_preg="select * from seguroupt WHERE coduniv = ".$codigo." and idsem =".$estuidsem;
				$resultado_preg=luis_fichamatricula($conn_2, $sql_preg);
				while ($rowpreg =fetchrow_fichamatricula($resultado_preg,-1))
					{
						$rpt_seguro = $rowpreg[3];
					}
				cierra_fichamatricula($resultado_preg);
				// *************
						if($rpt_seguro==1)
						{
							echo '<font size="2px;"> ';
							echo '<br>';
							echo 'Usted ha seleccionado afiliarse al Seguro Universitario Contra Accidentes.';
							echo '<br>';
							echo '</font>';
						}
						else
						{
							echo '<font size="2px;"> ';
							echo '<br>';
							echo 'Usted ha seleccionado no afiliarse al Seguro Universitario Contra Accidentes.';
							echo '<br>';
							echo '</font>';
						}
						echo '<font size="2px;"> ';
						echo '<br>';

						echo 'Puede aproximarse ha realizar el pago a partir del 1 de Julio.';
						//echo 'Puede aproximarse ha realizar el pago de la Matricula en:';
//						echo '<br><br>';
//						echo '&bull; Banco de Crédito o cualquier Agente BCP a partir de las '.$hbcp.':20:00'; // original
//						echo '<br>';
//						echo '&bull; Banco Interamericano de Finanzas ( BanBif ) a partir de las '.$hbcp.':20:00';	// original


						// ***** JUEVES	 *****
						 // $hbcpalternativo = 8; // hora alternativa
//						 echo '&bull; Banco de Crédito o cualquier Agente BCP a partir del Jueves 09 de Agosto a las '.$hbcpalternativo.':20:00'; // alternativo
//						 echo '<br>';
//						 echo '&bull; Banco Interamericano de Finanzas ( BanBif ) a partir del Jueves 09 de Agosto a las '.$hbcpalternativo.':20:00';	// alternativo
						// ***** FIN DE JUEVES  *****
						echo '<br><br>';

						// 427212 = telefono de la UPT
						// 243384 = telefono de la UPT
						echo '<strong>NOTA:</strong> Si desea realizar Pronto Pago puede llamar al número 427212 al Anexo 437 del Área de Cobranzas <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;para coordinar el respectivo procedimiento.';
						echo '</font>';
						// echo '<br>';
						// echo '&bull; En Caja Capanique de la Universidad.';
						echo '<br><br>';
				echo '</td>';
		}
		// ********

			echo '<td width="200" align="right">';
			echo '<table border="1" cellspacing="0" >';
			echo '<tr><td><font size="1">Matricula </font></td><td><font size="1">100.0</font></td></tr>';
			echo '<tr><td><font size="1">Valor de una cuota, de cinco cuotas: </font></td><td><font size="1">'.$toa.'</font></td></tr>';
			if ($_SESSION['iddepe']==314048000){
				$toti=($toti+3.2);
				echo '<tr><td><font size="1">Convenio Microsoft: </font></td><td><font size="1">3.2</font></td></tr>';
			}
			if ($ptip==5 or $ptip==7){
				$toti=($toti+80);
				echo '<tr><td><font size="1">Pago por Reingreso: </font></td><td><font size="1">80.0</font></td></tr>';
			}
			echo '<tr><td><font size="1"><b>Total a pagar: </b></font></td><td><font size="1"><b>'.round($toti,1).'</b></font></td></tr>';
			echo '</table></td></tr>';
	        }
		echo '</table><br>';

		echo '<table border="1" width="100%" id="table9" cellspacing="0">';
		echo '<tr><td colspan="7"><font size="4">&nbsp;Horario tentativo&nbsp;&nbsp;&nbsp;</font><font size="1" color="#FF0000">*Nota </font><font size="1">si ve que algunos casilleros se pinta de este color:&nbsp;<span style="background-color: #feb6b1">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;es por que existe cruce de horarios</font></td></tr>';
		echo '<tr>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Lunes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Martes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Miércoles</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Jueves</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Viernes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Sábado</font></td>';
		echo '<td bgcolor="#dbeaf5" align="right">';
		echo '<font size="1">Domingo</font></td>';
		echo '</tr>';
		For ($m=1;$m<=$g;$m++)
		{
			if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}
			echo '</tr>';
			For ($j=1;$j<=7;$j++)
			{
				if ($j==1) {
					echo '<td bgcolor="'.$tcol.'" width="92"><table border="0"><tr>';
					// tx[$m] = son los cursos y pero sin los horarios
					echo '<td bgcolor="'.$tcol.'"><font size="1">'.$tx[$m].'</font></td>';
					// ***********	MENSAJE DE ALERTA X FALTA DE HORARIO EN UN CURSO	*******
					$cadena=$tx[$m];
					$caracter   = ':     :';
					$posicion = strpos($cadena, $caracter);
					if ($posicion === false)
					{
					?>
					<script language="javascript">
					alert('El curso:  <?=$tx[$m] ?> no posee un horario, no se matricule en ese curso para evitar el cruce de horario')
					</script>
					<?
					}
					// ************************
					echo '<td bgcolor="'.$tcol.'"><font size="1">&nbsp;&nbsp;:&nbsp;&nbsp;</font></td>';
					echo '<td bgcolor="'.$tcol.'"><font size="1">&nbsp;&nbsp;:&nbsp;&nbsp;</font></td>';
					echo '</tr></table></td>';
				} else { echo '<td bgcolor="'.$tcol.'" width="92"></td>';}
			}
			echo '</tr>';
		}
		For ($k=1;$k<=$mu;$k++)
		{
			if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}
			echo '</tr>';
			For ($j=1;$j<=7;$j++)
			{
				if ($d[$j]>0 and $d[$j]>=$k) {
					// $td[$j][$k] =  indica si el curso tiene cruce de horario , presenta estado 1 si es cruce
					if ($td[$j][$k]==1){$tcola='#FEB6B1';}else{$tcola=$tcol;}
					echo '<td bgcolor="'.$tcol.'" width="92"><table border="0" width="100%"><tr>';
					// $ta[$j][$k] = son los cursos
					echo '<td bgcolor="'.$tcola.'"><font size="1">'.$ta[$j][$k].'</font></td>';
					// ***********	MENSAJE DE ALERTA X FALTA DE HORARIO EN UN CURSO	*******
					// $tb[$j][$k] = es la hora de inicio del curso
					echo '<td bgcolor="'.$tcola.'"><font size="1">'.$tb[$j][$k].'</font></td>';
					// $tc[$j][$k] = es la hora de fin del curso
					// $va[$j][0] = es el ciclo y el nombre del curso
					// ALGORITMO PARA DETERMINAR EL CICLO AL QUE PERTENECE EL CURSO
					switch ($te[$j][$k])
					{
					case 1:
						$C01 = 1;
						break;
					case 2:
						$C02 = 1;
						break;
					case 3:
						$C03 = 1;
						break;
					case 4:
						$C04 = 1;
						break;
					case 5:
						$C05 = 1;
						break;
					case 6:
						$C06 = 1;
						break;
					case 7:
						$C07 = 1;
						break;
					case 8:
						$C08 = 1;
						break;
					case 9:
						$C09 = 1;
						break;
					case 10:
						$C010 = 1;
						break;
					case 11:
						$C011 = 1;
						break;
					case 12:
						$C012 = 1;
						break;
					}
					// HASTA AQUI DETERMINO A QUE CICLO PERTENCE EL CURSO
					echo '<td bgcolor="'.$tcola.'"><font size="1">'.$tc[$j][$k].'</font></td>';
					echo '</tr></table></td>';
				}
				else
				{
					echo '<td bgcolor="'.$tcol.'" width="92"></td>';
				}
			}
			echo '</tr>';
		}
		// **********************************************************
		$total_cursos=$C01 + $C02 + $C03 + $C04 + $C05 + $C06 + $C07 + $C08 + $C09 + $C10 + $C11 + $C12;
		if ($total_cursos > 3)
		{
		?>
		<script language="javascript">
		alert('El estudiante sólo puede matricularse en asignaturas de 3 CICLOS ACADÉMICOS DIFERENTES, se encuentra en <?=$total_cursos ?> CICLOS ACADÉMICOS')
		</script>
		<?
		}
		// **********************************************************
		echo '</table>';
		cierra($resulta);
		echo '<input type="hidden" name="op" maxlength="7" value="0">';
		echo '</FORM>';
		//deuda(43730);
	}else{echo ' Esta opcion no esta disponible para usted por el momento.';}
      	noconex($conn);
}
/*genera de estado del docente en el archivo PHP  buscab.php, que indica los detalles del docente*/




/*AGREGUE LA VARIALBE $file_php PARA DETERMINAR SI ESTOY EN EL ARCHIVO estadistica.php O buscab.php*/
function individual($codigo, $sex, $codper, $busca, $file_php,$sem, $semestre_denominacion, $sin_semestres = false)//GABO AGREGO SEMESTRE DENOMINACION Y SIN_SEMESTRES
{
 	// Variable para mantener el estado en la URL de los formularios
 	$url_param_semestre = $sin_semestres ? '&sin_semestres=1' : '';

	//Validacion Plani --Yoel 23-10-18
	$vUno = $busca;
	$vDos = $file_php;

	$vTres = false;
	$Conexion = conex();
	$Consulta = 'SELECT g.idfac, g.idesc FROM gcodigo AS g INNER JOIN gcodigo_catalogo AS gc ON gc.idfac = g.idfac WHERE g.idfac = 317 AND g.codigo = '.$_SESSION['codigo'];
	// echo $Consulta;
	$dtValidacion = luis($Conexion, $Consulta);
	while ($drValidacion = fetchrow($dtValidacion, -1))
	{
		$vTres = true;
	}
	cierra($dtValidacion);
	//Fin Validacion Plani --Yoel 23-10-18

	$conn=conex_fichamatricula();
	$sql="SP_ListarDatosDocenteESCV2 ".$codigo.", ".$sem;
	// echo $sql;
	// exit;
	$result=luis_fichamatricula($conn, $sql);

	// $ValidarTiempoCompleto =$row[6];   //naty
	// echo $ValidarTiempoCompleto;

	//Gabo
	$nombreEscuelaPorDefecto = '';

	$ko=1;
	while ($row=fetchrow_fichamatricula($result,-1))
	{
		//Gabo
		if (empty($nombreEscuelaPorDefecto)) { // <--- 2. GUARDA EL VALOR SOLO UNA VEZ
			$nombreEscuelaPorDefecto = $row[1];
		}
		
		$ValidarTiempoCompleto = $row[6];
	// echo $ValidarTiempoCompleto;
	// //echo 'hola';
		if ($row[6]=="TC" or $row[6]=="DE" or $row[6]=="TP")
		{
			echo '<table border="1" width="100%" id="table9" cellspacing="0">';
			echo '<tr><td bgcolor="#dbeaf5" align="left"><font size="1">Facultad</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[0].'</font></td>';
			echo '<td bgcolor="#dbeaf5" align="left"><font size="1">Escuela</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[1].'</td></tr>';
			echo '<tr><td bgcolor="#dbeaf5" align="left"><font size="1">Nombre</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[2].'</font></td>';
			echo '<td bgcolor="#dbeaf5" align="left"><font size="1">Horas</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[3].'</font></td></tr>';
			echo '<tr><td bgcolor="#dbeaf5" align="left"><font size="1">Condición</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[4].'</font></td>';
			echo '<td bgcolor="#dbeaf5" align="left"><font size="1">Categoria</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[5].'</font></td></tr>';
			echo '<tr><td bgcolor="#dbeaf5" align="left"><font size="1">Dedicación</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[6].'</font></td>';
			echo '<td bgcolor="#dbeaf5" align="left"><font size="1"></font></td><td bgcolor="#FFFFFF" align="left"><font size="1"></font></td></tr>';
			echo '<tr><td bgcolor="#dbeaf5" align="left" colspan=4><font size="1">Grados Académicos</font></td></tr>';
			echo '<tr><td align="left" colspan=4><font size="1">
			<table border="1" width="100%" style="font-size:11px;" cellspacing="0">
			<tr style="font-weight:bold;">
				<td bgcolor="#dbeaf5">UNIVERSIDAD</td>
				<td bgcolor="#dbeaf5">CARRERA</td>
				<td bgcolor="#dbeaf5">GRADO</td>
			</tr>';

			$sql2="SP_ListarDatosDocenteGradosESC ".$codigo;
			// echo $sql2;
			$result2=luis_fichamatricula($conn, $sql2);
			while ($row2=fetchrow_fichamatricula($result2,-1))
			{
				echo '<tr style="font-size:9px;"><td>'.$row2[0].'</td>
				<td>'.$row2[1].'</td>
				<td>'.$row2[2].'</td>';
			}
			echo '</tr>
			</table>
			</font></td></tr>';
			echo '</table>';
			cierra_fichamatricula($result2);
		}
		else
		{
			$ko=0;
		}
	}
	if ($ko==1){
		echo '<br>';
		echo '<br>';
		cierra_fichamatricula($result);
		echo '<table border="1" width="100%" id="table9" cellspacing="0">';
		echo '<tr><th colspan="14">Detalle de Carga Lectiva</th></tr>';
		echo '<tr>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Codigo</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">seccion</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Dep.</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Hrs</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Alumnos</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Aula</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Calif.</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Lunes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Martes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Miércoles</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Jueves</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Viernes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Sábado</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Domingo</font></td>';
		echo '</tr>';

/*SE ENCARGA DE MOSTRAR LOS DATOS DEL DETALLE DE LA CARGA LECTIVA POR DOCENTE*/

$kl=0;

$conn=conex();
// exit;

// --- INICIO DE LA MODIFICACIÓN 1 ---

// Si estamos en modo "sin semestres", usamos una consulta directa que busca por fecha_inicio
if ($sin_semestres) {
    // Esta consulta directa busca en la tabla `trab` los registros que corresponden
    // a actividades sin cronograma (con fecha en 1900 o idsem 0) y devuelve una estructura
    // similar a la que el resto del código espera.
    // Nota: Llenamos las columnas faltantes con valores por defecto o de la propia tabla.
    $sql = "
        SELECT
            '' AS col0,                         -- Columna 0: Vacía, no utilizada
            t.seccion AS seccion,               -- Columna 1: seccion
            t.dactividad AS curso,              -- Columna 2: dactividad (nombre del curso/actividad)
            '' AS col3,                         -- Columna 3: Vacía, no utilizada
            t.horas AS horas,                   -- Columna 4: horas
            t.idtrab AS idtrab,                 -- Columna 5: idtrab (usado como identificador único)
            t.cant AS alumnos,                  -- Columna 6: cant (usado como 'Alumnos')
            0 AS dia_semana,                    -- Columna 7: día de la semana (por defecto 0, sin horario)
            '' AS hora_inicio,                  -- Columna 8: hora_inicio
            '' AS hora_fin,                     -- Columna 9: hora_fin
            t.dependencia AS dependencia,       -- Columna 10: dependencia
            '' AS aula                          -- Columna 11: aula
        FROM trab t
        WHERE t.codigo = " . $codigo . "
          AND (CAST(t.fecha_inicio AS DATE) = '1900-01-01' OR t.idsem = 0)
        ORDER BY t.idtrab ASC
    ";
} else {
    // Si no, usamos la lógica original con el procedimiento almacenado
    if($codigo==39222800){
        $sql="exec trabindividual_v4 ".$codigo.",".$sem;
    }
    else{
        $sql="exec trabindividual_v4 ".$codigo.",".$sem;
    }
}

// --- FIN DE LA MODIFICACIÓN 1 ---

// echo $sql;
// exit;
$resulta=luis($conn, $sql);



$idc="";
$fl=0;

do {
    //if ($idc<>$row[0] or $sec<>$row[1])
    if ($idc<>$row[5])
    {
	if ($fl>0)
	{
		For ($j=1;$j<=7;$j++)
		{
			if ($d[$j][$fl]>$d[0][$fl])
			{
				$d[0][$fl]=$d[$j][$fl];
			}
			}
	}
	$fl++;
	For ($i=1;$i<=7;$i++)
	{
		$d[$i][$fl]=0;
	}
    }
	if ($row[7] == 1) {
	$d[1][$fl]++;
	$ta[1][$fl][$d[1][$fl]]=$row[6];
	$tb[1][$fl][$d[1][$fl]]=$row[8];
	$tc[1][$fl][$d[1][$fl]]=$row[9];
	} elseif ($row[7] == 2) {
    	$d[2][$fl]++;
	$ta[2][$fl][$d[2][$fl]]=$row[6];
	$tb[2][$fl][$d[2][$fl]]=$row[8];
	$tc[2][$fl][$d[2][$fl]]=$row[9];
	} elseif ($row[7] == 3) {
    	$d[3][$fl]++;
    	$ta[3][$fl][$d[3][$fl]]=$row[6];
	$tb[3][$fl][$d[3][$fl]]=$row[8];
	$tc[3][$fl][$d[3][$fl]]=$row[9];
    	} elseif ($row[7] == 4) {
    	$d[4][$fl]++;
    	$ta[4][$fl][$d[4][$fl]]=$row[6];
	$tb[4][$fl][$d[4][$fl]]=$row[8];
	$tc[4][$fl][$d[4][$fl]]=$row[9];
    	} elseif ($row[7] == 5) {
    	$d[5][$fl]++;
    	$ta[5][$fl][$d[5][$fl]]=$row[6];
	$tb[5][$fl][$d[5][$fl]]=$row[8];
	$tc[5][$fl][$d[5][$fl]]=$row[9];
    	} elseif ($row[7] == 6) {
    	$d[6][$fl]++;
    	$ta[6][$fl][$d[6][$fl]]=$row[6];
	$tb[6][$fl][$d[6][$fl]]=$row[8];
	$tc[6][$fl][$d[6][$fl]]=$row[9];
    	} elseif ($row[7] == 7) {
    	$d[7][$fl]++;
    	$ta[7][$fl][$d[7][$fl]]=$row[6];
	$tb[7][$fl][$d[7][$fl]]=$row[8];
	$tc[7][$fl][$d[7][$fl]]=$row[9];
	}
   $idc=$row[5];

}while ($row=fetchrow($resulta,-1));

    if ($idc<>$row[5])
    {
	if ($fl>0)
	{
		For ($j=1;$j<=7;$j++)
		{
			if ($d[$j][$fl]>$d[0][$fl])
			{
				$d[0][$fl]=$d[$j][$fl];
			}
		}
	}
    }

$in=1;
fetchrow($resulta,$in-1);
$idc="";
//$sec="";
$l=0;
$hl=0;

// $rowDia = fetchrow($resulta,-1);
// $diaLectivo = 0;

// if(count($rowDia) > 0){
// 	$diaLectivo = $rowDia[7];
// }

// $rowCursoSeleccionado = fetchrow($resulta,-1);
$CursoSeleccionado = 0;
// if(count($rowCursoSeleccionado) > 0){
// 	$CursoSeleccionado = $rowCursoSeleccionado[5];
// }

while ($row=fetchrow($resulta,-1))
{

	if ($idc<>$row[5])
	{
		// $CursoSeleccionado = $row[5];
		if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}

		$l++;
		// echo $idc."<br>";
		if ($d[0][$l]>0)
		{
			// $CursoSeleccionado =
			// var_dump($d);
			// echo "<br>";
			// echo $d[0][$l]."<br>";
			For ($k=1;$k<=$d[0][$l];$k++)
			{
				echo '<tr>';
				//suma las horas lectivas
				// echo $hl." - ";
				if($CursoSeleccionado != $row[5]){
					if ($row[4]>0){$hl=$hl+$row[4];}  //comentado el 11-01-2018
					$CursoSeleccionado = $row[5];
				}
				// echo $hl."<br>";

				//naty 11-01-2018 - suma las horas por curso
				/*if($row[4]>0 and $row[5]==$row[5])
				{
					$hl3=$row[4];
				}
				else
				{
					$hl2=$hl3+$row[4];
				}

				$hl=$hl3 + $hl2;*/
				//naty 11-01-2018

				echo '<td width="45" bgcolor="'.$tcol.'"><font size="1">'.$row[2].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[1].'</font></td></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[10].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[4].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[6].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[11].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">1</font></td>';
				For ($j=1;$j<=7;$j++)
				{
					if ($ta[$j][$l][$k]>=0 )
					{
						// var_dump($ta);
						/*la variable  $tb[$j][$l][$k] y la variable $tc[$j][$l][$k] se encargan de mostrar las horas de cada curso dentro de los dias de la semana*/
						echo '<td width="75" bgcolor="'.$tcol.'"><table border="0" width="100%"><tr>';
						echo '<td bgcolor="'.$tcol.'"><font size="1">'.$tb[$j][$l][$k].'</font></td>';
						echo '<td bgcolor="'.$tcol.'"><font size="1">'.$tc[$j][$l][$k].'</font></td>';
						echo '</tr></table></td>';
					}else{
						echo '<td bgcolor="'.$tcol.'"></td>';
					}
				}
				echo '</tr>';
			}
		}
		else
		{
			// echo $row[7]."<br>";
			if($CursoSeleccionado != $row[5]){
				if ($row[4]>0){$hl=$hl+$row[4];}
				$CursoSeleccionado = $row[5];
			}

			echo '<td width="45" bgcolor="'.$tcol.'"><font size="1">'.$row[2].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[1].'</font></td></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[10].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[4].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[6].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">&nbsp;</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">1</font></td>';

			For ($j=1;$j<=7;$j++)
			{
				echo '<td bgcolor="'.$tcol.'"></td>';
			}
			echo '</tr>';
		}
  }
	$idc=$row[5];
	// echo $diaLectivo." - ";
	// $diaLectivo = $row[7];
	// echo $diaLectivo."<br>";
}
//echo $hl;
echo '<tr><td colspan="3" align="Center"><b><font size="2">Total de Hrs Lectivas</font></b></td><td colspan="9" align="left"><b><font size="2">'.$hl.'</font></b></td><td colspan="2" align="left"><font size="1"><!--25% hrs. = '.round(($hl*0.25),1).'--></font></td></tr>';
echo '</table><br>';
cierra($resulta);

// --- INICIO: Calcular Total Horas Lectivas Cuadro ---GABO
$total_horas_lectivas_cuadro = 0;
$CursoSeleccionado = null;

$sql_hl_cuadro = "exec trabindividual_v4 " . $codigo . ", " . $sem;
$result_hl_cuadro = luis($conn, $sql_hl_cuadro);

while ($row_hl = fetchrow($result_hl_cuadro, -1)) {
    if ($CursoSeleccionado != $row_hl[5]) {
        if ($row_hl[4] > 0) {
            $total_horas_lectivas_cuadro += $row_hl[4];
        }
        $CursoSeleccionado = $row_hl[5];
    }
}
cierra($result_hl_cuadro);

// Aplicar la fórmula: total / 4 y redondear
$total_horas_lectivas_cuadro = round($total_horas_lectivas_cuadro / 4);
// --- FIN: Calcular Total Horas Lectivas Cuadro ---

/*FORMULARIO DE CARGA NO LECTIVA , LISTA TODAS LAS ACTIVIDADES INGRESADAS EN EL FORMULARIO*/

// MODIFICACIÓN 1: Se cambia la condición para que incluya a los docentes TP y DE.
// Esto permite que el código de cálculo de horas y el resumen final se ejecuten para ellos.  GABO
if ($ValidarTiempoCompleto == "TC" || $ValidarTiempoCompleto == "TP" || $ValidarTiempoCompleto == "DE") {

	// MODIFICACIÓN 2: Se envuelve la apertura del FORMULARIO en una condición.
	// Solo los docentes TC verán los formularios y podrán interactuar con ellos.
	

	if ($ValidarTiempoCompleto == "TC") {
		if ($busca==0) //GABO
		{
			// Añadimos la variable $url_param_semestre al final de la URL
			echo '<FORM METHOD="POST" ACTION="carga.php?tr=1&sesion='.$sex.'&x='.$sem.$url_param_semestre.'" name="frmindiv">';
		}

		if ($busca==1)
		{
			// Hacemos lo mismo aquí
			echo '<FORM METHOD="POST" ACTION="buscab.php?tr=1&sesion='.$sex.$url_param_semestre.'" name="frmindiv">';
		}
	}

	if ($sin_semestres) {
		echo '<input type="hidden" name="sin_semestres" value="1">';
	}

	// El siguiente bloque es solo para TC, ya que la condición original se mantiene.
	if ($ValidarTiempoCompleto == "TC") {
		  //naty 27-08-2019
	// echo 'hola';
	// echo $ValidarTiempoCompleto ;
	$sql_semestre="exec trabisem ".$codigo;
	//echo $sql_semestre;

	$result_semestre=luis($conn, $sql_semestre);
	while ($row=fetchrow($result_semestre,-1))
	{
		$semestre=$row[1];
		$fecha=$row[2];
		$codigoo=$row[3];

	}
		/*LA VARIABLE $semestre_acti INDICA EL SEMESTRE AL QUE CORRESPONDE LA ACTIVIDAD*/
		if (!$sin_semestres) {
		    $semestre_acti=$semestre;
		} else {
		    $semestre_acti = "TODOS LOS SEMESTRES";
		}
		$fecha_semestre=$fecha;
		/*LA VARIABLE $codigo_acti INDICA EL CODIGO AL QUE CORRESPONDE LA ACTIVIDAD*/
		$codigo_acti=$codigoo;

		echo'<input type="hidden" name="mcodigo" value="'.$codigo_acti.'">';
		echo'<input type="hidden" name="msemestre" value="'.$semestre_acti.'">';

		cierra($result_semestre);


		$fecha_final =$fecha_semestre; /*LA VARIABLE $fecha_final ES = LA FECHA EN QUE INCIA EL SEMESTRE*/
		/*$fecha_final ='10/10/2011';*/
		$dia = substr( $fecha_final, 0, 2 );
		$mes=  substr( $fecha_final, 3, 2 );
		$ano=  substr( $fecha_final, 6, 4 );

		date_default_timezone_set('America/Lima');
		$dia_actual=date("d/m/Y");
		$fecha_inical =$dia_actual;
		/*$fecha_inical ='01/10/2011';*/
		$dia2 = substr( $fecha_inical, 0, 2 );
		$mes2=  substr( $fecha_inical, 3, 2 );
		$ano2=  substr( $fecha_inical, 6, 4 );

		/*calculo timestam de las fecha FECHA FINAL*/
		$timestamp1 = mktime(0,0,0,$mes,$dia,$ano);		/*FECHA FINAL = FECHA INICIO DEL SEMESTRE*/
		/*$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2);*/	/*FECHA INICIAL = FECHA ACTUAL*/
		$timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

		/*CALCULO EL NUMERO DE DIAS EN QUE INICIA EL SEMESTRE */
		$dias_segundos = $timestamp1;
		/*convierto segundos en días*/
		$num_dias = $dias_segundos / (60 * 60 * 24);
		/*obtengo el valor absoulto de los días (quito el posible signo negativo)*/
		$num_dias = abs($num_dias);
		/*quito los decimales a los días de diferencia*/
		$num_dias = floor($num_dias);
		$num_dias = $num_dias + 1;
		$dias_semetre=$num_dias+10; /*AGREGO 10 DIAS DE OLGURA PARA DESHABILITAR LOS BOTONES*/
		/*HASTA AQUI CALCULO EL NUMERO DE DIAS EN QUE INICIA EL SEMESTRE */

		/*CALCULO EL NUMERO DE DIAS ACTUALES  */
		$dias_segundos2 = $timestamp2;
		/*convierto segundos en días*/
		$num_dias2 = $dias_segundos2 / (60 * 60 * 24);
		/*obtengo el valor absoulto de los días (quito el posible signo negativo)*/
		$num_dias2 = abs($num_dias2);
		/*quito los decimales a los días de diferencia*/
		$num_dias2 = floor($num_dias2);
		$num_dias2 = $num_dias2 + 1;
		$dias_avance=$num_dias2; /*LA VARIABLE $dias_avance ES = LA FECHA ACTUAL QUE SERA COMPARADA
		CON LA CANTIDAD DE DIAS QUE POSEE EL SEMESTRE*/
		/*HASTA AQUI CALCULO EL NUMERO DE DIAS EN QUE INICIA EL SEMESTRE */

		/*
		echo'numero de dias de semestre';
		echo '<br>';
		echo $num_dias ;
		echo '<br>';
		echo'numero de dias de semestre + 10';
		echo '<br>';
		echo $num_dias = $num_dias  +10;
		echo '<br>';
		echo'numero de dias de actuales';
		echo '<br>';
		echo $num_dias2 ;
		echo '<br>';
		*/

		/*RADIO BUTONS PARA CAMBIAR DE ESTADO A TODAS LAS ACTIVIDADES*/
		$codigo_exec=$codigo;
		$sql="exec trabixdoc_v2 ".$codigo.",".$sem;
		//echo $sql;
		$result=luis($conn, $sql);
			while ($row =fetchrow($result,-1))
			{
			$idtrab=$row[0];
			}
			cierra($result);
			/*noconex($conn);	*/

			if ($idtrab>0)
			{

				if($file_php>0)
				{

					echo '<table border="1" cellspacing="0">';
					echo '<tr>';
						echo '<td colspan="1">
											<font size="3" face="Arial" ><strong>Descargar Guía de Usuario para Director de Escuela</strong>
											</font>
						</td>';
						echo '<td colspan="1">
							<font size="1" face="Arial">
									<blink>
										<a href="documentos/PIT_Docente/GUIA_PIT_DIRECTOR_V2.pdf" target="_blank">
											<center style="color: red;">( Haga clic aquí )</center>
										</a>
									</blink>
							</font>
						</td>';
					echo '</tr>';


					//Validacion Plani --Yoel 23-10-18
					if ( ($vUno == 0 && $vDos == 0 && $vTres == true) || ($vUno == 0 && $vDos == 0 && $vTres == false) || ($vUno == 1 && $vDos == 1 && $vTres == false) || ($vUno == 1 && $vDos == 0 && $vTres == false) )
					{
						echo '<tr>';
						echo '<td colspan="1">
								<font size="3" face="Arial" ><strong>Habilitar o deshabilitar las Actividades del Detalle de Carga No Lectiva</strong>
								</font>
							</td>';
						require_once('encripta_pdf.php');
						echo '<td colspan="1">
								<font size="1" face="Arial">
										<blink>
									<a onclick="javascript:window.open(\'habilitar_pit.php?sesion='.$sex.'&codigo='.fn_encriptar($codigo).'&x='.$sem.'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=400,top=40,left=50\');return false" href="habilitar_pit.php">
											<center style="color: red;">( Haga clic aquí )</center>
										</blink>
									</a></font>
							</td>';
						echo '</tr>';
					}//Fin Validacion Plani --Yoel 23-10-18

					echo '</table>';
					echo'<br>';


				}
			}

		/*HASTA AQUI SE GENERA LOS RADIO BUTONS */

			/*+++ ACORDEON ++*/

			$sql="select COUNT(0) from trab where codigo=".$codigo." and idsem =".$sem;

			$result=luis($conn, $sql);
			while ($row=fetchrow($result,-1))
			{
				$da++;
				$a=$row[0];
			}
			/*echo'+++';*/

		//Validacion Plani --Yoel 23-10-18
		if ( ($vUno == 0 && $vDos == 0 && $vTres == true) || ($vUno == 0 && $vDos == 0 && $vTres == false) || ($vUno == 1 && $vDos == 1 && $vTres == false) || ($vUno == 1 && $vDos == 0 && $vTres == false) )
		{

						// --- INICIO: Filtro por Mes --- GABO
			if (!$sin_semestres) {


				$semestre_acti = $semestre;
						// --- FIN DEPURACIÓN ---
				echo '<div style="margin: 20px 0; padding: 10px; background-color: #f0f0f0; border-radius: 5px;">';
				echo '<label for="filtro_mes_individual" style="font-weight: bold; margin-right: 10px;">Filtrar Actividades por Mes:</label>';
				echo '<select id="filtro_mes_individual" name="filtro_mes_individual" style="padding: 5px;">';
				echo '<option value="">-- Mostrar Todas --</option>';

				// Definir los meses según el semestre
				$meses_semestre = [];
				switch ($semestre_acti) {
					case '2025-I':
						$meses_semestre = ['Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'];
						break;
					case '2025-II':
						$meses_semestre = ['Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];
						break;
					case '2025-REC':
						$meses_semestre = ['Diciembre', 'Enero', 'Febrero'];
						break;
					case '2025-INT':
						$meses_semestre = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];
						break;
					default:
						// Si no coincide con ningún semestre conocido, mostrar todos los meses
						$meses_semestre = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Setiembre', 'Octubre', 'Noviembre', 'Diciembre'];
				}

				foreach ($meses_semestre as $mes) {
					echo '<option value="' . $mes . '">' . $mes . '</option>';
				}

				echo '</select>';
				echo '</div>';
			}
			// --- FIN: Filtro por Mes ---

			// MODIFICACIÓN 3: Se envuelve el formulario del "acordeón" en una condición
			// para que solo se muestre a los docentes TC.
			if ($ValidarTiempoCompleto == "TC") {
				if($a>0)
				{
					echo'
		<div id="main" style="text-align: left;">
			<div id="list3">
						<div>
							<div class="title"><img src="imagenes/flecha_trab.png" width=12 height=12 border=0></a> &nbsp;&nbsp;&nbsp; Registre el avance de su actividad - <font size="1"><blink> Haga clic izquierdo aqui para visualizar el formulario.</blink></font></div>
							<div>
								<p>';


								 /*+++++*/
								 /*CREO COMBO CON LOS DETALLES DE LAS ACTIVIDADES*/

								$sql="select idtrab, dactividad from trab where codigo=".$codigo." and idsem =".$sem;
								//echo $sql;

								$result=luis($conn, $sql);

								echo'<font size="1">Seleccione el detalle de su Actividad</font>';
								echo' &nbsp;&nbsp;&nbsp;&nbsp; ';
								echo'<select size="1" name="vdactividad_historial" title="Seleccionar el detalle de su actividad">';
									while ($row=fetchrow($result,-1))
									{
										$da++;
									echo '<option value="'.$row[0].'">'.$row[1].'</option>';
									}
								echo '</select>';

								cierra($result);
								/*noconex($conn);*/
								echo'<br>';
								echo'<br>';
								 /*+++*/
								  /* echo'<INPUT TYPE="text" class="ftexto" NAME="vdetalle_historial" title="Escribir el detalle de la actividad" size="133">';*/
								echo'<font size="1">Ingrese el Número del Informe:</font>';
								echo'<br>';
								echo'<br>';
								echo'
								<INPUT TYPE="text" class="ftexto" NAME="vnominfo_historial" title="Escribir el nombre del informe" size="156">';
								echo'<br>';
								echo'<br>';
								echo'<font size="1">Dirigido a:</font>';
								echo'<br>';
								echo'<br>';
								echo'
								<INPUT TYPE="text" class="ftexto" NAME="vdirigido_historial" title="Escribir el nombre  de la persona a quien va dirigido el informe" size="156">';
								echo'<br>';
								echo'<br>';
								echo'<font size="1">Cargo de la persona a quien va dirigido a:</font>';
								echo'<br>';
								echo'<br>';
								echo'
								<INPUT TYPE="text" class="ftexto" NAME="vcargo_historial" title="Escribir el cargo de la persona a quien va dirigido" size="156">';
								echo'<br>';
								echo'<br>';
								echo'<font size="1">Remitente:</font>';
								echo'<br>';
								echo'<br>';

								$sql="select codper, Nombres, sigla  from individual where codper =".$codper;
								$result_nombre=luis($conn, $sql);
								while ($row=fetchrow($result_nombre,-1))
								{
								$nombre_remitente=$row[1];
								$cargo_remitente=$row[2];
								}
								cierra($result_nombre);
								$espacio = ' ';
								$nombre_cargo_remitente =$cargo_remitente.$espacio.$nombre_remitente;
								/*++++++++++*/

								echo'
								<INPUT TYPE="text" class="ftexto" NAME="vremitente_historial" title="Escribir el nombre del informe" size="156" value="'.$nombre_cargo_remitente.'">';
								echo'<br>';
								echo'<br>';
								echo'<font size="1">Ingrese el Detalle de Acciones del Informe:</font>';
								echo'<br>';
								echo'<br>';
								echo'
								<textarea class="ftexto" NAME="vdetalle_historial" title="Escribir el detalle de la actividad" rows="3" cols="156" ></textarea>';
								/*echo'<br>';
								echo'<br>';
								echo'<font size="1">Ingrese el Detalle de Acciones del Informe - Parrafo 2:</font>';*/
								/*echo'<br>';
								echo'<br>';
								echo'
								<textarea class="ftexto" NAME="vdetalle2_historial" title="Escribir el detalle de la actividad" rows="3" cols="156" ></textarea>';*/
								echo'<br>';
								echo'<br>';

								echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Porcentaje de avance</font>';
								echo '&nbsp;&nbsp;&nbsp;';

								echo'<input type="number" name="vporcentaje_historial" title="Ingrese el porcentaje de avance (0-100)" min="0" max="100" style="width: 60px;" />';
								echo '&nbsp;&nbsp;&nbsp;';
								/*echo'<input class="btns" type="submit" name="addhistorial" value="Registrar"/>'; */
								echo '<input type="hidden" name="coduni" value="'.$codigo.'">';
								echo '<input type="hidden" name="addhistorial">';
		echo '<input class="btns" type=button onClick="javascript:msjregistrar()" value="Registrar"/>';

								echo'</p>
							</div>
						</div>
			</div>
		</div>
							';
				}
			} // Fin de la condición para ocultar el acordeón

			// --- INICIO: JavaScript para Filtro por Mes ---GABO
				?>
				<script>
				document.getElementById('filtro_mes_individual').addEventListener('change', function() {
					const mesSeleccionado = this.value;
					const todasLasTablas = document.querySelectorAll('table[border="1"][cellspacing="0"][width="100%"]');

					todasLasTablas.forEach(tabla => {
						// Verificar si la tabla es de "Detalle de Carga No Lectiva"
						const esTablaDeActividad = tabla.querySelector('td[colspan="1"] > font[size="1"]') && 
												tabla.querySelector('td[colspan="1"] > font[size="1"]').textContent.includes('Actividad');

						if (esTablaDeActividad) {
							// Obtener las fechas de la fila que contiene los inputs de fecha
							const filaFechas = tabla.querySelector('input[name^="dateboxx"]');
							if (filaFechas) {
								const fechaInicioInput = tabla.querySelector('input[name^="dateboxx"]');
								const fechaFinInput = tabla.querySelector('input[name^="dateboxx2"]');

								if (fechaInicioInput && fechaFinInput) {
									const fechaInicio = fechaInicioInput.value;
									const fechaFin = fechaFinInput.value;

									if (mesSeleccionado === "") {
										// Mostrar todas si no hay filtro
										tabla.style.display = '';
									} else {
										// Convertir el nombre del mes a número
										const mesNumero = obtenerNumeroMes(mesSeleccionado);

										// Verificar si el mes seleccionado está dentro del rango de fechas
										const estaEnRango = estaMesEnRango(fechaInicio, fechaFin, mesNumero);

										if (estaEnRango) {
											tabla.style.display = '';
										} else {
											tabla.style.display = 'none';
										}
									}
								}
							}
						}
					});
				});

					function obtenerNumeroMes(nombreMes) {
						const meses = {
							'Enero': 1, 'Febrero': 2, 'Marzo': 3, 'Abril': 4, 'Mayo': 5, 'Junio': 6,
							'Julio': 7, 'Agosto': 8, 'Setiembre': 9, 'Octubre': 10, 'Noviembre': 11, 'Diciembre': 12
						};
						return meses[nombreMes] || 0;
					}

					function estaMesEnRango(fechaInicioStr, fechaFinStr, mesBuscado) {
						// Convertir SOLO la fecha de inicio de string "dd/mm/yyyy" a objeto Date
						const partesInicio = fechaInicioStr.split('/');
						const fechaInicio = new Date(partesInicio[2], partesInicio[1] - 1, partesInicio[0]);

						// Obtener el mes de la fecha de inicio (enero = 0, por eso sumamos 1)
						const mesInicio = fechaInicio.getMonth() + 1;

						// Devolver true solo si el mes de inicio coincide con el mes buscado
						return mesInicio === mesBuscado;
					}
					</script>
					<?php
					// --- FIN: JavaScript para Filtro por Mes ---

			noconex($conn);

		} 
			/*FIN ACORDEON*/

		} //Fin Validacion Plani --Yoel 23-10-18

	} // Fin del bloque if ($ValidarTiempoCompleto == "TC")

/*HASTA AQUI CAPTURO LA FECHA EN QUE INICIA EL SEMESTRE SEMESTRE*/
//by gabo
//$sql="exec trabixdoc_v2 ".$codigo.",".$sem;
//by gabo

// --- INICIO DE LA MODIFICACIÓN ---

// Si la variable $sin_semestres es verdadera, aplicamos el filtro de fecha especial.
if ($sin_semestres) {
    // Esta consulta directa está diseñada para imitar la estructura de salida del procedimiento almacenado,
    // pero filtrando por la fecha '1900-01-01' en lugar de por un semestre.
    $sql = "
        SELECT 
            t.idtrab,                                   -- Columna 0
            t.actividad,                                -- Columna 1
            t.dactividad,                               -- Columna 2
            t.importancia,                              -- Columna 3
            t.medida,                                   -- Columna 4
            t.cant,                                     -- Columna 5
            t.horas,                                    -- Columna 6
            t.calif,                                    -- Columna 7
            t.meta,                                     -- Columna 8
            CONVERT(varchar(10), t.fecha_inicio, 103) AS FECHA_INICIO, -- Columna 9 (Formato dd/mm/yyyy)
            CASE 
                WHEN t.fecha_fin IS NULL THEN CONVERT(varchar(10), GETDATE(), 103) 
                ELSE CONVERT(varchar(10), t.fecha_fin, 103) 
            END AS FECHA_FIN,                            -- Columna 10
            ISNULL(t.porcentaje, 0) AS PORCENTAJE,      -- Columna 11
            t.idsem AS SEMESTRE,                        -- Columna 12
            t.codigo,                                   -- Columna 13
            ISNULL(t.estado, 0) AS ESTADO,              -- Columna 14
            -- Estas columnas adicionales son necesarias para que la estructura coincida
            CASE WHEN t.porcentaje = 100 THEN 1 ELSE 0 END AS validarestado, -- Columna 15
            CASE WHEN t.porcentaje = 100 AND thf.estado = 1 THEN 1 ELSE 0 END AS finalizacion, -- Columna 16
            t.dependencia,                              -- Columna 17
            t.detalle_actividad,                        -- Columna 18
            t.tipo_actividad                            -- Columna 19
        FROM trab t 
        LEFT JOIN trab_historial_finalizado thf ON thf.idtrab = t.idtrab AND thf.IdSem = t.idsem
        WHERE t.codigo = " . $codigo . "
          AND CAST(t.fecha_inicio AS DATE) = '1900-01-01' 
        ORDER BY t.idtrab ASC
    ";
} else {
    // Si no, se utiliza la consulta original que filtra por semestre.
    $sql = "exec trabixdoc_v2 " . $codigo . ", " . $sem;
}
// --- FIN DE LA MODIFICACIÓN ---
//$sql = "SELECT * FROM dbo.trab WHERE codigo = '" . $codigo . "' AND idsem = " . $sem . " ORDER BY idtrab ASC";

// Si codigo e idsem son números
// Asegurar tipos

// Por ejemplo

// Validar que sean números enteros

//echo $sql;
$da=0;
$hn=0;
$suma_porcentaje = 0;
$result=luis($conn, $sql);
$bandera=1;

while ($row=fetchrow($result,-1))
{
	$da++;
	$suma_porcentaje = $suma_porcentaje+$row[11];

	if ($ton==1){$tcol='DEEBE5';$ton=0;}else{$tcol='#DBEAF5';$ton=1;}
	if ($row[6]>0){$hn=$hn+$row[6];}

	$estado=$row[14];
	$idtrabajo = $row[0];

	$sqlxx1="SELECT TOP 1 th.estado FROM trab_historial th WHERE th.idtrab=".$idtrabajo." AND th.porcentaje=100 ORDER BY th.id_historial DESC";
	$resultatcxx1=luis($conn, $sqlxx1);
	while ($rowsss1=fetchrow($resultatcxx1,-1))
	{
		$estadofinalfaina = $rowsss1[0];
	}

	$sqlx="exec sp_consultar_trabindiv_finalizacion ".$idtrabajo;
	//echo $sqlx;
	$resultatc=luis($conn, $sqlx);
	$canV = numrow($resultatc);

	while ($rows=fetchrow($resultatc,-1))
	{

		$hora = $rows[0];
		$validarestado = $rows[2];

	}



	$estadox = $row[5];
	$porcentajex = $row[11];







?>
<script language="javascript">
function actualizarTipoActividad<?php echo $da; ?>() {
    var actividad = document.getElementById("actividad_editar<?php echo $da; ?>").value;
    var tipoSelect = document.getElementById("tipo_actividad_editar<?php echo $da; ?>");
    var detalleSelect = document.getElementById("detalle_actividad_editar<?php echo $da; ?>");

    // Limpiar los siguientes selects
    tipoSelect.innerHTML = "<option value=''>-- Seleccione --</option>";
    detalleSelect.innerHTML = "<option value=''>-- Seleccione --</option>";

    var opciones = [];
    if (actividad === "Academica") {
        opciones = [
            { value: "Lectiva", text: "Lectiva" },
            { value: "No_Lectiva", text: "No Lectiva" },
            { value: "Investigacion", text: "Investigación" },
            { value: "Responsabilidad_Social", text: "Responsabilidad Social" }
        ];
    } else if (actividad === "Administrativa") {
        opciones = [
            { value: "Gestion", text: "Gestión" }
        ];
    }

    // Limpiar opciones existentes
    tipoSelect.innerHTML = "";
    
    // Agregar opción por defecto
    var defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.text = "-- Seleccione --";
    tipoSelect.appendChild(defaultOption);

    opciones.forEach(function(opcion) {
        var opt = document.createElement("option");
        opt.value = opcion.value;
        opt.text = opcion.text;
        tipoSelect.appendChild(opt);
    });
}

function actualizarDetalleActividad<?php echo $da; ?>() {
    var tipo = document.getElementById("tipo_actividad_editar<?php echo $da; ?>").value;
    var detalleSelect = document.getElementById("detalle_actividad_editar<?php echo $da; ?>");

    // Limpiar opciones existentes
    detalleSelect.innerHTML = "";
    
    // Agregar opción por defecto
    var defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.text = "-- Seleccione --";
    detalleSelect.appendChild(defaultOption);

    var opciones = [];

    switch(tipo) {
        case "Lectiva":
            opciones = [{ value: "Preparacion_Clase", text: "Preparación de clase y evaluación" }];
            break;
        case "No_Lectiva":
            opciones = [
                { value: "Asesoria_Practicas", text: "Asesoramiento prácticas pre profesionales" },
                { value: "Consejeria", text: "Consejería" },
                { value: "Tutoria", text: "Tutoría" },
                { value: "Monitoreo_Seguimiento", text: "Monitoreo y seguimiento" },
                { value: "Organizacion_Eventos", text: "Organización de eventos" },
                { value: "Actividades_Academicas", text: "Actividades académicas" },
                { value: "Actividades_Acreditacion", text: "Actividades de acreditación" }
            ];
            break;
        case "Investigacion":
            opciones = [
                { value: "Asesoria_Tesis", text: "Asesoría de tesis" },
                { value: "Jurados", text: "Jurados" },
                { value: "Produccion_Intelectual", text: "Producción intelectual" },
                { value: "Articulos_Investigacion", text: "Artículos investigación" },
                { value: "Proyectos_Investigacion", text: "Proyectos de investigación" }
            ];
            break;
        case "Responsabilidad_Social":
            opciones = [
                { value: "Proyeccion_Social", text: "Proyección social" },
                { value: "Extension_Universitaria", text: "Extensión universitaria" },
                { value: "Responsabilidad_Social_Detalle", text: "Responsabilidad social" },
                { value: "PSSU", text: "PSSU" },
                { value: "Voluntariado", text: "Voluntariado" },
                { value: "Seguimiento_Egresados", text: "Seguimiento a egresados en escuelas" },
                { value: "GPSAlumni", text: "Actividades GPSAlumni" }
            ];
            break;
        case "Gestion":
            opciones = [
                { value: "Jefatura_Oficina", text: "Jefatura de oficina" },
                { value: "Unidad_Administrativa", text: "Jefatura de unidad administrativa" },
                { value: "Coordinador_Area", text: "Coordinador de área" },
                { value: "Coordinador_Unidad_Academica", text: "Coordinador de unidad académica" },
                { value: "Coordinador_Unidad_Investigacion", text: "Coordinador de unidad de investigación" },
                { value: "Coordinador_GPSAlumni", text: "Coordinador de GPSAlumni" },
                { value: "Coordinador_RS", text: "Coordinador de Responsabilidad social" },
                { value: "Comisiones", text: "Comisiones" },
                { value: "Comite_Mejora", text: "Comité de mejora continua" }
            ];
            break;
    }

    opciones.forEach(function(opcion) {
        var opt = document.createElement("option");
        opt.value = opcion.value;
        opt.text = opcion.text;
        detalleSelect.appendChild(opt);
    });
}
</script>
<?php
/*EN LA CARGA DEL DOCENTE*/

	// Array con las opciones para el nuevo combobox GABO
	// Realizar la consulta para obtener las dependencias desde la base de datos
		$sql_dependencias = "SELECT DISTINCT descrip FROM depe AS d WHERE d.estado = 0 ORDER BY descrip";
		$result_dependencias = luis($conn, $sql_dependencias); // Asumiendo que $conn está disponible y es válido
		$dependencias = array(); // Inicializar array vacío

		while ($row_dep = fetchrow($result_dependencias, -1)) {
			$dependencias[] = $row_dep[0]; // Añadir cada descripción al array
		}
		cierra($result_dependencias);

	echo '<table border="1" cellspacing="0" width="100%">';
		if ($bandera==1){echo '<tr><th colspan="1">Detalle de Carga No Lectiva</th></tr>';}

			/*Nuevo Cambio para que actualicen la calificación 19-10-2018 Yoel*/
			if ($row[7] == 6) { $NombreCalifActividad = 'Bienestar Universitario'; } elseif ($row[7] == 7) { $NombreCalifActividad = 'Administración'; }
			if ($row[7] == 6 || $row[7] == 7)
				{?>
					<tr><td colspan="1" bgcolor="#f8bacf"><font size="2"> A partir del semestre 2018-II. La Calificación de Actividad  <font style="text-decoration: underline;"><?php echo $NombreCalifActividad; ?>.</font> está desactivada <a href="documentos/PIT_Docente/ActividadesPITGA_V2.pdf" target="_blank"> VER GUIA RAPIDA </a></font></td></tr>
				<?}
			/*Hasta aqui 19-10-2018 Yoel*/

		echo '<tr>';
			if($estado>0 && $ValidarTiempoCompleto == 'TC') { // Solo es editable para TC
				$editable=' - el contenido de la actividad puede ser editado';
			} else if ($ValidarTiempoCompleto != 'TC') {
				$editable=' - <span style="color: red;"><strong>No editable para esta dedicación</strong></span>';
			} else {
				$editable=' - <span style="color: red;"><strong>el contenido de la actividad no puede ser editado</strong></span>';
			}
			echo '<td colspan="1" bgcolor="'.$tcol.'"><font size="1">Actividad '.$da.' '.$editable.'</font></td>';
		echo '</tr>';
		
				// Fila 1: Comboboxes de Actividad, Tipo, Detalle y Dependencia
		echo '<tr>';
			echo '<td><font size="1">';
				// Actividad
				echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Actividad:</font>';
				echo ' <select size="1" id="actividad_editar'.$da.'" name="vacti_editar'.$da.'" title="Seleccionar la actividad" onchange="actualizarTipoActividad'.$da.'()">';
				echo '<option value="">-- Seleccione --</option>';
				?>
					<option <?php if($row[1] == "Academica") { echo "selected"; } ?> value="Academica">Académica</option>
					<option <?php if($row[1] == "Administrativa") { echo "selected"; } ?> value="Administrativa">Administrativa</option>
				<?
				echo '</select>';
				
				// Tipo de Actividad
				echo '&nbsp;&nbsp;&nbsp;<font style="background-color: #F2F8FC" face="Verdana" size="1">Tipo de Actividad:</font>';
				echo ' <select size="1" id="tipo_actividad_editar'.$da.'" name="vtipo_editar'.$da.'" title="Seleccionar el tipo de actividad" onchange="actualizarDetalleActividad'.$da.'()">';
				echo '<option value="">-- Seleccione --</option>';
				if($row[1] == "Academica") {
					$tipos = ["Lectiva" => "Lectiva", "No_Lectiva" => "No Lectiva", "Investigacion" => "Investigación", "Responsabilidad_Social" => "Responsabilidad Social"];
				} elseif($row[1] == "Administrativa") {
					$tipos = ["Gestion" => "Gestión"];
				} else { $tipos = []; }
				foreach($tipos as $valor => $texto) {
					$selected = ($row[19] == $valor) ? "selected" : ""; //15
					echo '<option value="'.$valor.'" '.$selected.'>'.$texto.'</option>';
				}
				echo '</select>';
				
				// Detalle de Actividad
				echo '&nbsp;&nbsp;&nbsp;<font style="background-color: #F2F8FC" face="Verdana" size="1">Detalle:</font>';
				echo ' <select size="1" id="detalle_actividad_editar'.$da.'" name="vdetalle_editar'.$da.'" title="Seleccionar el detalle de actividad">';
				echo '<option value="">-- Seleccione --</option>';
				
				// --- INICIO CAMBIO 1: Mostrar y seleccionar el Detalle de Actividad guardado ---
				// Se asume que la lista completa de opciones se carga con JavaScript.
				// Esta línea asegura que el valor guardado ($row[18]) aparezca seleccionado al cargar la página.
				if (!empty($row[18])) {
					echo '<option value="' . htmlspecialchars($row[18]) . '" selected>' . htmlspecialchars($row[18]) . '</option>';
				}
				echo '</select>';
				
				// Dependencia
				echo '&nbsp;&nbsp;&nbsp;<font style="background-color: #F2F8FC" face="Verdana" size="1">Dependencia:</font>';
				echo ' <select size="1" name="vdependencia_editar'.$da.'">';
				echo '<option value="">-- Seleccione --</option>';
				foreach ($dependencias as $dependencia) {
					// --- INICIO CAMBIO 2: Lógica para preseleccionar la Dependencia guardada ($row[17]) ---
					$selected_dependencia = ($row[17] == $dependencia) ? "selected" : "";
					echo '<option value="' . htmlspecialchars($dependencia) . '" ' . $selected_dependencia . '>' . htmlspecialchars($dependencia) . '</option>';
					// --- FIN CAMBIO 2 ---
				}
				echo '</select>';
			echo '</font></td>';
		echo '</tr>';

		// Fila 2: Fechas y Porcentaje de avance
		echo '<tr>';
			echo '<td><font size="1">';
				date_default_timezone_set('America/Lima');
				$datebox="dateboxx".$da;
				$datebox2="dateboxx2".$da;

				// Fecha Inicio
				echo'<font style="background-color: #F2F8FC" face="Verdana" size="1">Fecha Inicio:</font>';
			?>
				<input name="<? echo $datebox ?>" readonly="true" autocomplete="off" size="10" onClick="displayCalendar(<? echo $datebox ?>,'dd/mm/yyyy',this)" type="text" value=<? echo $row[9] ?> >
			<?
				echo '&nbsp;&nbsp;&nbsp;';
				
				// Fecha Final
				echo'<font style="background-color: #F2F8FC" face="Verdana" size="1">Fecha Final:</font>';
			?>
				<input name=<? echo $datebox2 ?> readonly="true" autocomplete="off" size="10" onClick="displayCalendar(<? echo $datebox2 ?>,'dd/mm/yyyy',this)" type="text" value=<? echo $row[10] ?> >
			<?
				echo '&nbsp;&nbsp;&nbsp;';
				
				// Porcentaje de avance
				echo'<font style="background-color: #F2F8FC" face="Verdana" size="1">Porcentaje de avance:</font>';
				echo' <INPUT TYPE="text" class="ftexto" NAME="vporcentaje_editar'.$da.'" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="2" maxlength="2" value="'.$row[11].'">';
			echo '</font></td>';
		echo '</tr>';

		echo'<tr>';
			echo '<td bgcolor="'.$tcol.'"><font size="1">Detalle Actividad</font></td>';
		echo'</tr>';
		echo'<tr>';
			echo '<td><font size="1">';
			if ($estado>0 && $ValidarTiempoCompleto == 'TC') {
				echo'<INPUT TYPE="text" class="ftexto" NAME="vdacti_editar'.$da.'" title="Escribir la actividad que realizara" style="width: 98%;" maxlength="107" value="'.$row[2].'">';
			} else {
				echo'<INPUT TYPE="text" class="ftexto" NAME="vdacti_editar'.$da.'" title="Escribir la actividad que realizara" style="width: 98%;" readonly="readonly" maxlength="107" value="'.$row[2].'">';
			}
			echo '</font></td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td bgcolor="'.$tcol.'"><font size="1">Importancia</font></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><font size="1">';
				if ($estado>0 && $ValidarTiempoCompleto == 'TC') {
					echo'<INPUT TYPE="text" class="ftexto" NAME="vimporta_editar'.$da.'" title="Escribir la importancia de la actividad" style="width: 98%;" maxlength="255" value="'.$row[3].'">';
				} else {
					echo'<INPUT TYPE="text" class="ftexto" NAME="vimporta_editar'.$da.'" title="Escribir la importancia de la actividad" style="width: 98%;" readonly="readonly" maxlength="255" value="'.$row[3].'">';
				}
			echo'</font></td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td bgcolor="'.$tcol.'"><font size="1">Meta</font></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><font size="1">';
			if ($estado>0 && $ValidarTiempoCompleto == 'TC') {
				echo'<INPUT TYPE="text" class="ftexto" NAME="vmeta_editar'.$da.'" title="Escribir la meta a alcanzar en el semestre" style="width: 98%;" maxlength="255" value="'.$row[8].'">';
			} else {
				echo'<INPUT TYPE="text" class="ftexto" NAME="vmeta_editar'.$da.'" title="Escribir la meta a alcanzar en el semestre" style="width: 98%;" readonly="readonly" maxlength="255" value="'.$row[8].'">';
			}
			echo'</font></td>';
		echo '</tr>';

	

	//Validacion Plani --Yoel 23-10-18
	if ( ($vUno == 0 && $vDos == 0 && $vTres == true) || ($vUno == 0 && $vDos == 0 && $vTres == false) || ($vUno == 1 && $vDos == 1 && $vTres == false) || ($vUno == 1 && $vDos == 0 && $vTres == false) )
	{

		echo '<tr>';
			echo '<td  bgcolor="'.$tcol.'"><font size="1">Informes</font></td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td colspan="1" width="">';
			if($file_php>0)
			{
				require_once('encripta_pdf.php');

				echo '<font size="1">
				<a onclick="javascript:window.open(\'print_informe_trab_edit.php?sesion='.$sex.'&id='.fn_encriptar($row[0]).'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=600,top=40,left=50\');return false" href="print_informe_trab_edit.php">VER HISTORIAL DE INFORMES</a>
				</font>';
			}
			else
			{
				require_once('encripta_pdf.php');
				echo '<font size="1">
				<a onclick="javascript:window.open(\'print_informe_trab.php?sesion='.$sex.'&id='.fn_encriptar($row[0]).'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=550,top=40,left=50\');return false" href="print_informe_trab.php">VER HISTORIAL DE INFORMES</a>
				</font>';
			}

			$cod = $_SESSION['codigox'];

			if ($row[11]>0)
			{

				echo '<font size="1">&nbsp; &nbsp; &nbsp; -  &nbsp; &nbsp; </font>';
				//LLAMA AL ARCHIVO PHP encripta_pdf.php QUE CONTIENE LAS FUNCIONES PARA ENCRIPTAR
				require_once('encripta_pdf.php');
				echo '<font size="1">';
				echo '<a href="imprimir_trab_consolidado.php?codigo='.fn_encriptar($cod).'&codigo2='.fn_encriptar($codigo).'&x='.$sem.'" target="_blank">VER CONSOLIDADO DE ACTIVIDADES PDF</a>';
		echo '</font>';
			}
			echo '</td>';
		echo '</tr>';

	} //Fin Validacion Plani --Yoel 23-10-18

		echo '<tr>';
			echo '<td bgcolor="'.$tcol.'">
			<font size="1">
			Medida 	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Cant
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			Hrs. Semanales
			</font></td>';
		echo '</tr>';

		echo '<tr>';
			echo '<td>
			<font size="1">';
			echo '<select size="1" name="vmedida_editar'.$da.'" title="Seleccionar la magnitud que representa lo establecido como meta">';
			?>

			<option <?php if($row[4] == "Alumnos") { echo "selected"; } ?> value="Alumnos">Alumnos</option>
			<option <?php if($row[4] == "Documento") { echo "selected"; } ?> value="Documento">Documento(s)</option>
			<option <?php if($row[4] == "Permanente") { echo "selected"; } ?> value="Permanente">Permanente</option>

			<?
			echo '</select>';
			echo'</font>';
			echo'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="1">';
			if ($estado>0 && $ValidarTiempoCompleto == 'TC')
			{
				echo'<INPUT TYPE="text" class="ftexto" NAME="vcant_editar'.$da.'" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="5" maxlength="5" value="'.$row[5].'">';

			}
			else
			{
				echo'<INPUT TYPE="text" class="ftexto" NAME="vcant_editar'.$da.'" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="5" readonly="readonly" maxlength="5" value="'.$row[5].'">';
			}
			echo'</font>';

			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="1">';
			if ($validarestado==1 and $canV>0)
			{
				$color="red";
			}
			else
			{
				$color="black";
			}

			if ($estado>0 && $ValidarTiempoCompleto == 'TC')
			{
				echo '<INPUT TYPE="text" class="ftexto" style="color:'.$color.'" NAME="vhoras_editar'.$da.'" title="Escribir la cantidad de horas que demanda la actividad" size="2" maxlength="2" value="'.$row[6].'">';
			}
			else
			{
				echo '<INPUT TYPE="text" class="ftexto" style="color:'.$color.'" NAME="vhoras_editar'.$da.'" title="Escribir la cantidad de horas que demanda la actividad" size="2" readonly="readonly" maxlength="2" value="'.$row[6].'">';
			}
			echo '</font>';

			echo'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="1"></font>';

			if ($busca==0 or $busca==1)
			{
				$id_semestre=$row[12];
				$codigo_docente=$row[13];

				if ($validarestado==1 and $canV>0)
				{
					$va='disabled="disabled"';
				}
				else
				{
					$va='';
				}

		//Validacion Plani --Yoel 23-10-18
		if ( ($vUno == 0 && $vDos == 0 && $vTres == true) || ($vUno == 0 && $vDos == 0 && $vTres == false) || ($vUno == 1 && $vDos == 1 && $vTres == false) || ($vUno == 1 && $vDos == 0 && $vTres == false) )
		{

				// MODIFICACIÓN 4: Se envuelven los botones de acción (Eliminar, Editar, etc.)
				// en una condición para que solo los docentes TC los vean.
				if ($ValidarTiempoCompleto == "TC") {
					if (($dias_semetre>$dias_avance)or($estado>0) )
					{
						echo '&nbsp;&nbsp;&nbsp;';
						echo '<input class="btns" type="submit" '.$va.' name="de'.$da.'" value="Eliminar" onClick="return confirmdelete()">';
						echo '<input type="hidden" name="vi'.$da.'" value="'.$row[0].'">';
						echo '&nbsp;&nbsp;&nbsp;';
						echo '<input class="btns" type="submit" '.$va.' name="dedit'.$da.'" value="Editar"  onClick="return confirmSubmit()"/>';
						echo '&nbsp;&nbsp;&nbsp;';
						if ($porcentajex==100 and $estadox>0)
						{
						echo '<input class="btns" type="submit" '.$va.' name="delim'.$da.'" value="Finalizar"  onClick="return confirmFinalizar('.$estadofinalfaina.')"/>';
						echo '&nbsp;&nbsp;&nbsp;';
						echo '<input class="btns" type="submit" name="rever'.$da.'" value="Revertir" onClick="return confirmRevertir()"/>';
						}

					}
					else
					{
						echo '&nbsp;&nbsp;&nbsp;';
						echo '<input class="btns" type="submit" disabled="disabled" name="de'.$da.'" value="Eliminar">';
						echo '<input type="hidden" name="vi'.$da.'" value="'.$row[0].'">';
						echo '&nbsp;&nbsp;&nbsp;';
						echo '<input class="btns" type="submit" disabled="disabled" name="dedit'.$da.'" value="Editar"/>';
					}
				}

		} //Fin Validacion Plani --Yoel 23-10-18

				echo '</td>';
			}
			else
			{
				echo '<td width="120" ></td>';
			}
		echo '</tr>';
		echo '<td colspan="6" align="right" style="color:red"><b>';

		if ($canV>0)
		{
			if ($validarestado==1)
			{

				$vah++;
				$hora_descuento=$hora_descuento+$hora;
				echo 'Finalizado por el usuario día '.$fecha.' por llegar al 100%</b></td></tr>';
			}
		}

	echo '</table>';
	echo'<br>';
	$bandera=0;
}



	/*TOTAL DE HORAS NO ELECTIVAS*/
	if ($hn!=0)
	{

		if ($vah>0)
		{

			$diferencia_hora = ($hn-$hora_descuento);
			echo '<table border="1" cellspacing="0" width="100%">
					<tr>
					<td width="108" align="center"><b><font size="2">Total de Hrs No Lectivas</font></b></td>
					<td width="600"><b><font size="2">'.$diferencia_hora.'</font></b></td>
					</tr>
			  </table><br>';
		}
		else
		{

			echo '<table border="1" cellspacing="0" width="100%">
					<tr>
					<td width="108" align="center"><b><font size="2">Total de Hrs No Lectivas</font></b></td>
					<td width="600"><b><font size="2">'.$hn.'</font></b></td>
					</tr>
			  </table><br>';

		}

	}
	/*FIN*/


	//GABO
		// --- INICIO: BLOQUE DE CÓDIGO MODIFICADO ---
	$conn = conex();

	// 1. Obtener las horas laborales del docente
	$sql_resumen = "usp_Pit_ObtenerDatosResumen " . $codper . ", " . $sem;
	$result_resumen = luis($conn, $sql_resumen);
	$horas_laborales = 0;
	while ($row = fetchrow($result_resumen, -1)) {
		$horas_laborales = $row[4];
	}
	cierra($result_resumen);

	// 2. Obtener la dependencia del docente para verificar exención
	$sql_dependencia = "SELECT (SELECT descrip FROM depe WHERE depe.iddepe = individual.iddepe) AS dependencia FROM individual WHERE codper = " . $codper;
	$result_dependencia = luis($conn, $sql_dependencia);
	$dependencia_docente = '';
	while ($row_dep = fetchrow($result_dependencia, -1)) {
		$dependencia_docente = $row_dep[0];
	}
	cierra($result_dependencia);

	// 3. Calcular el total de horas (lectivas + no lectivas)
	$total_horas_calculadas = $hl + $hn;
	if ($vah > 0) {
		$total_horas_calculadas = $hl + ($hn - $hora_descuento);
	}

	// 4. Verificar si el docente está exento (Rector o Vicerrector)
	$es_exento = (
		stripos($dependencia_docente, 'rectorado') !== false ||
		stripos($dependencia_docente, 'vice rectorado') !== false
	);

	// 5. Aplicar las validaciones y generar el mensaje según cada dedicación
	$mensaje = ''; // Inicializar la variable del mensaje

	if ($es_exento) {
		$mensaje = ' Exento de carga académica-administrativa';
	} else {
		switch ($ValidarTiempoCompleto) {
			case 'TP': // Lógica para Tiempo Parcial
			case 'DE': // Lógica para Dedicación Exclusiva
				$mensaje = '';
				break;

			case 'TC': // Lógica para Tiempo Completo
			default:
				// Parte 1: Validar las horas totales
				if ($total_horas_calculadas < $horas_laborales) {
					$mensaje_total = ' Debe de completar las ' . $horas_laborales . ' horas.';
				} elseif ($total_horas_calculadas > $horas_laborales) {
					$mensaje_total = ' No debe de sobrepasar las ' . $horas_laborales . ' horas.';
				} else {
					$mensaje_total = ' Cumple con las ' . $horas_laborales . ' horas.';
				}

				// Parte 2: Validar las horas lectivas
				$mensaje_lectivas = '';
				if ($hl < 20) {
					$mensaje_lectivas = '<br> No cumple con el mínimo de 20 horas lectivas.';
				}

				// Combinar ambos mensajes
				$mensaje = $mensaje_total . $mensaje_lectivas;
				break;
		}
	}
	// --- FIN: BLOQUE DE CÓDIGO MODIFICADO ---

	// MODIFICACIÓN 5: Se ha eliminado el bloque de código redundante que recalculaba la variable $mensaje.
	// El bloque de "NUEVAS VALIDACIONES" anterior ya genera el mensaje correcto para todos los casos (TC, TP, DE).

	// --- Mostrar resultado según tipo de contrato ---GABO
	// Solo mostrar tabla de Total Hrs. si es TC
	if (!$es_exento && $ValidarTiempoCompleto == 'TC') {
		if ($vah > 0) {
			echo '<table style="font-size:20px; color:red;">
				<tr>
				<td width="150" colspan="2" align="right">
				<b>Total Hrs.&nbsp;</b></td>
				<td width="600" colspan="3"><b>' . ($hl + ($hn - $hora_descuento)) . ' -' . $mensaje . '</b></td>
				</tr>
			</table>';
		} else {
			echo '<table style="font-size:20px; color:red;">
				<tr>
				<td width="150" colspan="2" align="right">
				<b>Total Hrs.&nbsp;</b></td>
				<td width="600" colspan="3"><b>' . ($hl + $hn) . ' -' . $mensaje . '</b></td>
				</tr>
			</table>';
		}
	} else {
		// Para TP, DE o Exento, solo muestra el mensaje si aplica
		if (trim($mensaje) != '') {
			echo '<div style="font-size:20px; color:red;"><b>' . $mensaje . '</b></div>';
		}
	}



/*PARTE PARA INGRESAR EL DETALLE DEL TRABAJO*/

$sql_add_detalle_trab="exec sp_add_detalle_trab ".$codigo_docente.", ".$id_semestre.", '".$suma_porcentaje."','".$da."'";

//echo $sql_add_detalle_trab;
$result_detalle_trab=luis($conn, $sql_add_detalle_trab);
cierra($result_detalle_trab);

	// MODIFICACIÓN 6: Se envuelve el formulario final para "Agregar" actividad
	// en una condición para que solo los docentes TC lo vean.
	if ($ValidarTiempoCompleto == "TC") {
		echo '<br><table border="0" width="100%">';
		echo '<tr><td colspan="7"><b><font size="1">CALIFICACION</font></b></td></tr>
				<tr>
				<td><font size="1">1 = "Preparacion de Clase"</font></td>
				<td><font size="1">2 = "Asesoramiento"</font></td>
				<td><font size="1">5 = "Investigacion"</font></td>
				<td><font size="1">8 = "Gestion Administrativa"</font></td>
				<td><font size="1">9 = "Responsabilidad" Social"</font></td></tr>';
		echo '</table>';
		echo '<br><br>';
		echo 'Descargar Guía de Usuario<a href="documentos/PIT_Docente/GUIA_PIT_DOCENTE_V2.pdf" target="_blank"><span style="color: red;"><font size="1" face="Arial"><blink>( Haga clic aquí )</blink> </font></span></a>';
		echo '<br><br>';

		
		if ($busca==0 or $busca==1)
		{
			echo '<input type="hidden" name="vn" maxlength="7" value="'.$da.'">';
			echo '<input type="hidden" name="vcanthoras" maxlength="7" value="'.$total_horas_calculadas.'">';

			//Validacion Plani --Yoel 23-10-18
			if ( ($vUno == 0 && $vDos == 0 && $vTres == true) || ($vUno == 0 && $vDos == 0 && $vTres == false) || ($vUno == 1 && $vDos == 1 && $vTres == false) || ($vUno == 1 && $vDos == 0 && $vTres == false) )
			{

		?>
		<script language="javascript">
		function actualizarTipoActividad() {
			var actividad = document.getElementById("actividad").value;
			var tipoSelect = document.getElementById("tipo_actividad");
			var detalleSelect = document.getElementById("detalle_actividad");

			// Limpiar los siguientes selects
			tipoSelect.innerHTML = "<option value=''>-- Seleccione --</option>";
			detalleSelect.innerHTML = "<option value=''>-- Seleccione --</option>";

			var opciones = [];
			if (actividad === "Academica") {
				opciones = [
					{ value: "Lectiva", text: "Lectiva" },
					{ value: "No_Lectiva", text: "No Lectiva" },
					{ value: "Investigacion", text: "Investigación" },
					{ value: "Responsabilidad_Social", text: "Responsabilidad Social" }
				];
			} else if (actividad === "Administrativa") {
				opciones = [
					{ value: "Gestion", text: "Gestión" }
				];
			}

			// Limpiar opciones existentes
			tipoSelect.innerHTML = "";
			
			// Agregar opción por defecto
			var defaultOption = document.createElement("option");
			defaultOption.value = "";
			defaultOption.text = "-- Seleccione --";
			tipoSelect.appendChild(defaultOption);

			opciones.forEach(function(opcion) {
				var opt = document.createElement("option");
				opt.value = opcion.value;
				opt.text = opcion.text;
				tipoSelect.appendChild(opt);
			});
		}


		//Gabo
		function actualizarDetalleActividad() {
			var tipo = document.getElementById("tipo_actividad").value;
			var detalleSelect = document.getElementById("detalle_actividad");
			// Limpiar opciones existentes
			detalleSelect.innerHTML = "";
			// Agregar opción por defecto
			var defaultOption = document.createElement("option");
			defaultOption.value = "";
			defaultOption.text = "-- Seleccione --";
			detalleSelect.appendChild(defaultOption);
			var opciones = [];
			switch(tipo) {
				case "Lectiva":
					opciones = [{ value: "Preparacion_Clase", text: "Preparación de clase y evaluación" }];
					break;
				case "No_Lectiva":
					opciones = [
						{ value: "Asesoria_Practicas", text: "Asesoramiento prácticas pre profesionales" },
						{ value: "Consejeria", text: "Consejería" },
						{ value: "Tutoria", text: "Tutoría" },
						{ value: "Monitoreo_Seguimiento", text: "Monitoreo y seguimiento" },
						{ value: "Organizacion_Eventos", text: "Organización de eventos" },
						{ value: "Actividades_Academicas", text: "Actividades académicas" },
						{ value: "Actividades_Acreditacion", text: "Actividades de acreditación" }
					];
					break;
				case "Investigacion":
					opciones = [
						{ value: "Asesoria_Tesis", text: "Asesoría de tesis" },
						{ value: "Jurados", text: "Jurados" },
						{ value: "Produccion_Intelectual", text: "Producción intelectual" },
						{ value: "Articulos_Investigacion", text: "Artículos investigación" },
						{ value: "Proyectos_Investigacion", text: "Proyectos de investigación" }
					];
					break;
				case "Responsabilidad_Social":
					opciones = [
						{ value: "Proyeccion_Social", text: "Proyección social" },
						{ value: "Extension_Universitaria", text: "Extensión universitaria" },
						{ value: "Responsabilidad_Social_Detalle", text: "Responsabilidad social" },
						{ value: "PSSU", text: "PSSU" },
						{ value: "Voluntariado", text: "Voluntariado" },
						{ value: "Seguimiento_Egresados", text: "Seguimiento a egresados en escuelas" },
						{ value: "GPSAlumni", text: "Actividades GPSAlumni" }
					];
					break;
				case "Gestion":
					opciones = [
						{ value: "Jefatura_Oficina", text: "Jefatura de oficina" },
						{ value: "Unidad_Administrativa", text: "Jefatura de unidad administrativa" },
						{ value: "Coordinador_Area", text: "Coordinador de área" },
						{ value: "Coordinador_Unidad_Academica", text: "Coordinador de unidad académica" },
						{ value: "Coordinador_Unidad_Investigacion", text: "Coordinador de unidad de investigación" },
						{ value: "Coordinador_GPSAlumni", text: "Coordinador de GPSAlumni" },
						{ value: "Coordinador_RS", text: "Coordinador de Responsabilidad social" },
						{ value: "Comisiones", text: "Comisiones" },
						{ value: "Comite_Mejora", text: "Comité de mejora continua" }
					];
					break;
			}
			opciones.forEach(function(opcion) {
				var opt = document.createElement("option");
				opt.value = opcion.value;
				opt.text = opcion.text;
				detalleSelect.appendChild(opt);
			});

			// --- INICIO CAMBIO: Autorellenar horas si es Lectiva ---
			if (tipo === "Lectiva") {
				document.querySelector('input[name="vhoras"]').value = <?php echo $total_horas_lectivas_cuadro; ?>;
			} else {
				// Opcional: Limpiar el campo si se cambia de tipo
				// document.querySelector('input[name="vhoras"]').value = '';
			}
			// --- FIN CAMBIO ---
		}
		</script>

		<?php
				// --- INICIO CAMBIO: Reemplazar array manual con consulta SQL GABO ---
				// $dependencias = [ "AC : Área de Contabilidad", "AGPH : Área de Gestión del Potencial Humano", ... ]; // <-- Línea original a eliminar

				// Realizar la consulta para obtener las dependencias desde la base de datos
				$sql_dependencias = "SELECT DISTINCT descrip FROM depe AS d WHERE d.estado = 0 ORDER BY descrip";
				$result_dependencias = luis($conn, $sql_dependencias); // Asumiendo que $conn está disponible y es válido
				$dependencias = array(); // Inicializar array vacío

				while ($row_dep = fetchrow($result_dependencias, -1)) {
					$dependencias[] = $row_dep[0]; // Añadir cada descripción al array
				}
				cierra($result_dependencias);
				// --- FIN CAMBIO ---

				// Se define el número de columnas para que los colspan sean consistentes
				$num_columnas = 4;

				echo '<table border="0" cellspacing="2" bgcolor="#CCE6FF" style="width: 100%;">'; // Se añade un ancho a la tabla
						echo '<tr>';
					echo '<td colspan="'.$num_columnas.'"><font size="1">Actividad</font></td>';
					echo '</tr>';
					
					// Fila para los comboboxes, cada uno en su celda
					echo '<tr>';
					
					// Primer Combobox - Actividad
					echo '<td>';
					echo '<select size="1" id="actividad" name="vacti" onchange="actualizarTipoActividad()" style="width: 100%;">';
					echo '<option value="">-- Seleccione --</option>';
					echo '<option value="Academica">Académica</option>';
					echo '<option value="Administrativa">Administrativa</option>';
					echo '</select>';
					echo '</td>';
					
					// Segundo Combobox - Tipo de Actividad
					echo '<td>';
					echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Tipo de Actividad:</font>';
					echo '<select size="1" id="tipo_actividad" name="vtipo" onchange="actualizarDetalleActividad()" style="width: 100%;">';
					echo '<option value="">-- Seleccione --</option>';
					echo '</select>';
					echo '</td>';
					
					// Tercer Combobox - Detalle de Actividad
					echo '<td>';
					echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Detalle:</font>';
					echo '<select size="1" id="detalle_actividad" name="vdetalle" style="width: 100%;">';
					echo '<option value="">-- Seleccione --</option>';
					echo '</select>';
					echo '</td>';



					// Cuarto Combobox - Dependencia (NUEVO) 
					echo '<td>';
					echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Dependencia:</font>';
					echo '<select size="1" name="vdependencia" style="width: 100%;">';
					echo '<option value="">-- Seleccione --</option>';

					foreach ($dependencias as $dependencia) {
						$selected = '';
						// CORRECCIÓN: Comparamos si el nombre de la escuela está CONTENIDO
						// en la opción de dependencia, en lugar de una comparación exacta.
						// Usamos la variable que guardamos al principio.
						if (!empty($nombreEscuelaPorDefecto) && strpos($dependencia, $nombreEscuelaPorDefecto) !== false) {
							$selected = ' selected="selected"';
						}
						echo '<option value="' . htmlspecialchars($dependencia) . '"' . $selected . '>' . htmlspecialchars($dependencia) . '</option>';
					}
					echo '</select>';
					echo '</td>';
					
					echo '</tr>';
					
					// Fila para las fechas y el porcentaje
					echo '<tr>';
					
					date_default_timezone_set('America/Lima');
					$conn=conex();
					$sql="select s.inicioclases, s.finentregaactas from semestre s where s.activo>0 and s.idsem=".$sem;
					$result=luis($conn, $sql);
					while ($row=fetchrow($result,-1)) {
						$FechaInicio = $row[0];
						$FechaFin = $row[1];
					}
					cierra($result);
					$dia=date("d/m/Y",strtotime($FechaInicio));
					$dia2=date("d/m/Y",strtotime($FechaFin));

					if (isset($_POST["datebox"])==true){ $dia=$_POST["datebox"]; }
					if (isset($_POST["datebox2"])==true){ $dia2=$_POST["datebox2"]; }
					if ($sin_semestres != 1) {
					// Fecha Inicio
					echo '<td colspan="2">'; // Abarca 2 columnas
					echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Fecha Inicio:</font>';
					?>
					 <input name="datebox" readonly="true" autocomplete="off" size="10" onClick="displayCalendar(datebox,'dd/mm/yyyy',this)" type="text" value=<?php echo $dia; ?>>
					&nbsp;&nbsp;
					<?php
					// Fecha Final
					echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Fecha Final:</font>';
					?>
					 <input name="datebox2" readonly="true" autocomplete="off" size="10" onClick="displayCalendar(datebox2,'dd/mm/yyyy',this)" type="text" value=<?php echo $dia2; ?> >
					<?php
					echo '</td>';
					}

					// Porcentaje de avance
					echo '<td colspan="2">'; // Abarca 2 columnas
					echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Porcentaje de avance:</font>';
					echo '<INPUT TYPE="text" class="ftexto" NAME="vporcentaje" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="2" maxlength="2" value="10">';
					echo '</td>';

					echo '</tr>';

					echo'<tr>';
					echo '<td colspan="'.$num_columnas.'"><font size="1">Detalle Actividad</font></td>';
					echo'</tr>';
					echo'<tr>';
					// CAJA DE TEXTO PARA CAPTURAR EL Detalle Actividad, con ancho automático
					echo '<td colspan="'.$num_columnas.'"><INPUT TYPE="text" class="ftexto" NAME="vdacti" title="Escribir la actividad que realizara" style="width: 98%;" maxlength="100"></td>';
					echo '</tr>';
					
					echo '<tr>';
					echo '<td colspan="'.$num_columnas.'"><font size="1">Importancia</font></td>';
					echo '</tr>';
					echo '<tr>';
					// CAJA DE TEXTO QUE CAPTURA LA Importancia
					echo '<td colspan="'.$num_columnas.'"><font size="1"><INPUT TYPE="text" class="ftexto" NAME="vimporta" title="Escribir la importancia de la actividad" style="width: 98%;" maxlength="255"></font></td>';
					echo '</tr>';
					
					echo '<tr>';
					echo '<td colspan="'.$num_columnas.'"><font size="1">Meta</font></td>';
					echo '</tr>';
					echo '<tr>';
					// CAJA DE TEXTO QUE CAPTURA LA Meta
					echo '<td colspan="'.$num_columnas.'"><font size="1"><INPUT TYPE="text" class="ftexto" NAME="vmeta" title="Escribir la meta a alcanzar en el semestre" style="width: 98%;" maxlength="255"></font></td>';
					echo '</tr>';
					
					echo '<tr>';
						echo '<td><font size="1">Medida</font></td>';
						echo '<td><font size="1">Cant</font></td>';
						echo '<td><font size="1">Hrs. Semanales</font></td>';
						echo '<td>&nbsp;</td>'; // Celda vacía para alinear el botón
					echo '</tr>';
					
					echo '<tr>';
						echo '<td>';
						// genera el combo box de MEDIDA
						echo '<select size="1" name="vmedida" title="Seleccionar la magnitud que representa lo establecido como meta">';
						echo '<option value="Alumnos">Alumnos</option>';
						echo '<option value="Documento">Documento(s)</option>';
						echo '<option value="Permanente">Permanente</option>';
						echo '</select></td>';
						
						echo '<td><font size="1"><INPUT TYPE="text" class="ftexto" NAME="vcant" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="5" maxlength="5"></font></td>';
						
						echo '<td><font size="1"><INPUT TYPE="text" class="ftexto" NAME="vhoras" title="Escribir la cantidad de horas que demanda la actividad" size="2" maxlength="2"></font></td>';

						echo '<td>'; // Celda para el Botón
						// CAPTURO EL IDDEPE
						$sql="select Iddepe  from individual where codper =".$codper;
						$result_iddepe=luis($conn, $sql);
						while ($row=fetchrow($result_iddepe,-1)) {
							$iddepe=$row[0];
						}
						cierra($result_iddepe);

						echo '<input type="hidden" name="coduni" value="'.$codigo.'">';
						echo '<input type="hidden" name="viddepe" value="'.$iddepe.'">';
						echo '<input type="hidden" name="vagregar">';
						echo '<input class="btns" type=button onClick="javascript:msj()" value="Agregar"/>';
						echo '</td>';
					echo '</tr>';
				echo '</table>';


		} //Fin Validacion Plani --Yoel 23-10-18
		
		// Se cierra el formulario solo si es TC
		echo '</form>';
		}
	} // Fin de la condición para el formulario de "Agregar"
} // Fin de la condición principal (TC, TP, DE)
noconex($conn);
}



function meta($codigo, $sex, $codper, $busca,$sem )
{

$conn=conex();
		$sql="select (select descrip from depe where depe.iddepe=individual.iddepe) as facultad, nomcondicion, nomnivcateg, dedicacion, nrohoras, nombres, titulo, titulo2, titulo3, (select descrip from depe where depe.iddepe=idesc) as escuela from individual where codper=".$codper;
		//echo $sql;
		$result=luis($conn, $sql);
		$ko=1;
		while ($row=fetchrow($result,-1))
		{
		if ($row[3]=="TC" or $row[3]=="DE" or $row[3]=="TP"){

		}else{$ko=0;}
		}
	if ($ko==1){
		echo '<br>';
		cierra($result);
		echo '<table border="1" width="100%" id="table9" cellspacing="0">';
		echo '<tr><th colspan="14">Detalle de Carga Lectiva</th></tr>';
		echo '<tr>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Codigo</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">seccion</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Dep.</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Hrs</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Alumnos</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Aula</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Calif.</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Lunes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Martes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Miércoles</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Jueves</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Viernes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Sábado</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Domingo</font></td>';
		echo '</tr>';

$kl=0;
$sql="exec trabindividual_v4 ".$codigo.",".$sem;
//echo $sql;
$resulta=luis($conn, $sql);
$idc="";
$fl=0;

do {
    if ($idc<>$row[5])
    {
	if ($fl>0)
	{
		For ($j=1;$j<=7;$j++)
		{
			if ($d[$j][$fl]>$d[0][$fl])
			{
				$d[0][$fl]=$d[$j][$fl];
			}
			}
	}
	$fl++;
	For ($i=1;$i<=7;$i++)
	{
		$d[$i][$fl]=0;
	}
    }
	if ($row[7] == 1) {
	$d[1][$fl]++;
	$ta[1][$fl][$d[1][$fl]]=$row[6];
	$tb[1][$fl][$d[1][$fl]]=$row[8];
	$tc[1][$fl][$d[1][$fl]]=$row[9];
	} elseif ($row[7] == 2) {
    	$d[2][$fl]++;
	$ta[2][$fl][$d[2][$fl]]=$row[6];
	$tb[2][$fl][$d[2][$fl]]=$row[8];
	$tc[2][$fl][$d[2][$fl]]=$row[9];
	} elseif ($row[7] == 3) {
    	$d[3][$fl]++;
    	$ta[3][$fl][$d[3][$fl]]=$row[6];
	$tb[3][$fl][$d[3][$fl]]=$row[8];
	$tc[3][$fl][$d[3][$fl]]=$row[9];
    	} elseif ($row[7] == 4) {
    	$d[4][$fl]++;
    	$ta[4][$fl][$d[4][$fl]]=$row[6];
	$tb[4][$fl][$d[4][$fl]]=$row[8];
	$tc[4][$fl][$d[4][$fl]]=$row[9];
    	} elseif ($row[7] == 5) {
    	$d[5][$fl]++;
    	$ta[5][$fl][$d[5][$fl]]=$row[6];
	$tb[5][$fl][$d[5][$fl]]=$row[8];
	$tc[5][$fl][$d[5][$fl]]=$row[9];
    	} elseif ($row[7] == 6) {
    	$d[6][$fl]++;
    	$ta[6][$fl][$d[6][$fl]]=$row[6];
	$tb[6][$fl][$d[6][$fl]]=$row[8];
	$tc[6][$fl][$d[6][$fl]]=$row[9];
    	} elseif ($row[7] == 7) {
    	$d[7][$fl]++;
    	$ta[7][$fl][$d[7][$fl]]=$row[6];
	$tb[7][$fl][$d[7][$fl]]=$row[8];
	$tc[7][$fl][$d[7][$fl]]=$row[9];
	}
    $idc=$row[5];
}while ($row=fetchrow($resulta,-1));
    if ($idc<>$row[5])
    {
	if ($fl>0)
	{
		For ($j=1;$j<=7;$j++)
		{
			if ($d[$j][$fl]>$d[0][$fl])
			{
				$d[0][$fl]=$d[$j][$fl];
			}
		}
	}
    }

$in=1;
fetchrow($resulta,$in-1);
$idc="";
$l=0;
$hl=0;
while ($row=fetchrow($resulta,-1))
{
	if ($idc<>$row[5])
	{
		if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}

    		$l++;
		if ($d[0][$l]>0)
		{
			For ($k=1;$k<=$d[0][$l];$k++)
			{
				echo '<tr>';
				if ($row[4]>0){$hl=$hl+$row[4];}
				echo '<td width="45" bgcolor="'.$tcol.'"><font size="1">'.$row[2].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[1].'</font></td></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[10].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[4].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[6].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[11].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">1</font></td>';
				For ($j=1;$j<=7;$j++)
				{
					if ($ta[$j][$l][$k]>0 )
					{
						echo '<td width="75" bgcolor="'.$tcol.'"><table border="0" width="100%"><tr>';
						echo '<td bgcolor="'.$tcol.'"><font size="1">'.$tb[$j][$l][$k].'</font></td>';
						echo '<td bgcolor="'.$tcol.'"><font size="1">'.$tc[$j][$l][$k].'</font></td>';
						echo '</tr></table></td>';
					}else{
						echo '<td bgcolor="'.$tcol.'"></td>';
					}
				}
				echo '</tr>';
			}
		}else{
			if ($row[4]>0){$hl=$hl+$row[4];}
			echo '<td width="45" bgcolor="'.$tcol.'"><font size="1">'.$row[2].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[1].'</font></td></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[10].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[4].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[6].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">&nbsp;</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">1</font></td>';

			For ($j=1;$j<=7;$j++)
			{
				echo '<td bgcolor="'.$tcol.'"></td>';
			}
			echo '</tr>';
		}
    	}
	$idc=$row[5];
}
echo '<tr><td colspan="3" align="right"><b><font size="1">Hrs.&nbsp;</font></b></td><td colspan="9" align="left"><b><font size="1">'.$hl.'</font></b></td><td colspan="2" align="left"><font size="1"><!--25% hrs. = '.round(($hl*0.25),1).'--></font></td></tr>';
echo '</table><br>';
cierra($resulta);


/*ESTE PROCEDIMIENTO SE ENCARGA DE GENERAR  EL Detalle de Carga No Lectiva DEL ARCHIVO estadistica.php, AL REALIZAR CLIC SOBRE EL GIF METAS*/
//by gabo
$sql="exec trabixdoc_v2 ".$codigo.",".$sem;
//$sql = "SELECT * FROM dbo.trab WHERE codigo = $codigo AND idsem = $sem ORDER BY idtrab ASC";


//echo $sql;
$hn=0;

$case=1;

$bandera=1;
$result=luis($conn, $sql);
while ($row=fetchrow($result,-1))
{
	if ($bandera==1)
	{
		echo '<br>';
		echo '<table border="1" cellspacing="0">';
		echo '<th colspan="12">Detalle de Carga No Lectiva</th>';
		echo '<tr>
				<th bgcolor="#DBEAF5">
					<font size="1">Actividad</font>
				</th>
				<th bgcolor="#DBEAF5">
					<font size="1">Detalle Actividad</font>
				</th>
				<th bgcolor="#DBEAF5">
					<font size="1">Hrs.</font>
				</th>
				<th bgcolor="#DBEAF5">
					<font size="1">Medida</font>
				</th>
				<th bgcolor="#DBEAF5">
					<font size="1">Cant.</font>
				</th>
				<th bgcolor="#DBEAF5">
					<font size="1">Meta</font>
				</th>
				<br>
				<th bgcolor="#DBEAF5">
					<font size="1">Fecha Inicio</font>
				</th>
				<th bgcolor="#DBEAF5">
					<font size="1">Fecha Fin</font>
				</th>
				<th bgcolor="#DBEAF5">
					<font size="1">Nº días</font>
				</th>

				<th bgcolor="#DBEAF5">
					<font size="1">% de avance</font>
				</th>
				<th bgcolor="#DBEAF5">
					<font size="1">Indicador</font>
				</th>
				<th bgcolor="#DBEAF5">
					<font size="1">Observación</font>
				</th>
			</tr>';
		$bandera=0;
	}

	if ($row[6]>0)
	{
		if ($row[16]==0)
		{
			$hn=$hn+$row[6];
		}
	}
	if ($ton==1){$tcol='#FEF0FF';$ton=0;}else{$tcol='';$ton=1;}


	echo '<tr>
			<td bgcolor="'.$tcol.'">
				<font size="1">'.$row[1].'</font>
			</td>
			<td bgcolor="'.$tcol.'">
				<font size="1">'.$row[2].'</font>
			</td>';

			echo '	<td bgcolor="'.$tcol.'">';
				if ($row[16]==1)
				{
					echo '<font size="1" style="color:red;">'.$row[6].'</font>';
				}
				else
				{
					echo '<font size="1">'.$row[6].'</font>';
				}

			echo '</td>
			<td bgcolor="'.$tcol.'">
				<font size="1">'.$row[4].'</font>
			</td>
			<td bgcolor="'.$tcol.'">
				<font size="1">'.$row[5].'</font>
			</td>
			<td bgcolor="'.$tcol.'">
				<font size="1">'.$row[8].'</font>
			</td>
			<td bgcolor="'.$tcol.'">
				<font size="1">'.$row[9].'</font>
		  	</td>
			<td bgcolor="'.$tcol.'">
				<font size="1">'.$row[10].'</font>
		    </td>';

		/*CODIGO PARA CALCULAR EL NUMERO DE DIAS */

		$fecha_final =$row[10];
		$dia = substr( $fecha_final, 0, 2 );
		$mes=  substr( $fecha_final, 3, 2 );
		$ano=  substr( $fecha_final, 6, 4 );

		$fecha_inical =$row[9];
		$dia2 = substr( $fecha_inical, 0, 2 );
		$mes2=  substr( $fecha_inical, 3, 2 );
		$ano2=  substr( $fecha_inical, 6, 4 );

		/*calculo timestam de las dos fechas*/
		date_default_timezone_set('America/Lima');
		$timestamp1 = mktime(0,0,0,$mes,$dia,$ano);		/*FECHA FINAL*/
		$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2);	/*FECHA INICIAL*/
		$timestamp22 = mktime(0,0,0,$mes2,$dia2,$ano2);	/*FECHA INICIAL PARA DETERMINAR SI TODAVIA NO HA EMPEZADO LA FICHA INICIAL*/


		/*resto a una fecha la otra*/
		$segundos_diferencia = $timestamp1 - $timestamp2;
		/*convierto segundos en días*/
		$dias_diferencia = $segundos_diferencia / (60 * 60 * 24);
		/*obtengo el valor absoulto de los días (quito el posible signo negativo)*/
		$dias_diferencia = abs($dias_diferencia);
		/*quito los decimales a los días de diferencia*/
		$dias_diferencia = floor($dias_diferencia);
		$numero_dias = $dias_diferencia + 1;
		/*NOTA LE SUMO 1 PARA QUE  DE EL NUMERO EXACTO DE DIAS PARA EL REPORTE*/
		/*HASTA AQUI  ES EL CODIGO PARA CALCULAR EL NUMERO DE DIAS */

	echo '<td bgcolor="'.$tcol.'">
			<font size="1"> '.$numero_dias.' </font>
		  </td>';

	/*LA VARIABLE $row[11] INDICA EL Porcentje de avance*/
	echo '<td bgcolor="'.$tcol.'">
			<input type="number" name="porcentaje_avance_meta['.$row[0].']" value="'.$row[11].'" title="Porcentaje de avance (0-100)" min="0" max="100" style="width: 60px;" />
		  </td>';

	/*ALGORITMO PARA EL CALCULO DE LOS COLORES*/

	/******************/
	/*PASO 1*/
	/*CALCULO EL AVANCE DE DIAS DESDE LA FECHA INICIAL HASTA QUE LLEGA A LA FECHA LIMITE, ESTO ME AYUDA A DETERMINAR EL NUMERO DE DIAS QUE QUEDAN*/
	$fecha_actual=date("d/m/Y");
	$dia3 = substr( $fecha_actual, 0, 2 );
	$mes3=  substr( $fecha_actual, 3, 2 );
	$ano3=  substr( $fecha_actual, 6, 4 );

	/*calculo timestam de las dos fechas*/
	$timestamp3 = mktime(4,12,0,$mes3,$dia3,$ano3);
	/*resto a una fecha la otra*/
	$segundos_diferencia2 = $timestamp1 - $timestamp3;
	/*convierto segundos en días*/
	$dias_diferencia2 = $segundos_diferencia2 / (60 * 60 * 24);
	/*obtengo el valor absoulto de los días (quito el posible signo negativo)*/
	$dias_diferencia2 = abs($dias_diferencia2);
	/*quito los decimales a los días de diferencia*/
	$dias_diferencia2 = floor($dias_diferencia2);
	$numero_dias2 = $dias_diferencia2 + 1; /* $numero_dias2 ES EL NUMERO DE DIAS QUE QUEDAN PARA ACABAR LA ACTIVIDAD*/
	/*NOTA LE SUMO 1 PARA QUE  DE EL NUMERO EXACTO DE DIAS PARA EL REPORTE*/
	/*HASTA AQUI CALCULO LOS DIAS*/
	/******************/
	/*PASO 2*/
	/*DIVIDO EL NUMERO DE DIAS ENTRE 3 PARA ASIGNARLE LA CANTIDAD DE DIAS A CADA FASE*/
	$fase = $numero_dias/3;

	$fase1_dias = floor($fase);
	$fase2_dias = floor($fase)* 2;
	$fase3_dias = $fase* 3;
	/******************/
	/*PASO 3*/
	/*ASIGNO EL PORCENTAJE QUE TENDRA CADA FASE*/
	$porcentaje_fase1 = 33;
	$porcentaje_fase2 = 66;
	$porcentaje_fase3 = 99;
	/******************/
	/*PASO 4*/
	/*CALCULO LOS DIAS QUE HAN TRANSCURRIDO DEDES LA FECHA INCIAL HASTA LA FECHA ACTUAL, CON ESTO DETERMINO LOS DIAS QUE SE HA AVANZADO, ESTO ES LA RESTA DE LA  FECHA ACTUAL - LA FECHA INICIAL*/

	/*calculo timestam de las dos fechas*/

	$timestamp4 = mktime(0,0,0,$mes3,$dia3,$ano3);	/*  ES LA FECHA ACTUAL*/
	/*resto a una fecha la otra*/
	$segundos_diferencia3 = $timestamp4 - $timestamp2;
	/*convierto segundos en días*/
	$dias_diferencia3 = $segundos_diferencia3 / (60 * 60 * 24);
	/*obtengo el valor absoulto de los días (quito el posible signo negativo)*/
	$dias_diferencia3 = abs($dias_diferencia3);
	/*quito los decimales a los días de diferencia*/
	$dias_diferencia3 = floor($dias_diferencia3);
	$numero_dias3 = $dias_diferencia3 + 1; /* $numero_dias3 ES EL NUMERO DE DIAS QUE SE HA AVANZADO DESDE LA FECHA INICIAL HASTA LA FECHA FINAL*/
	/*NOTA LE SUMO 1 PARA QUE  DE EL NUMERO EXACTO DE DIAS PARA EL REPORTE*/

	/******************/
	/*PASO 5*/
	/*CREO IF ANIDADOES PARA DETERMINAR LOS COLORES*/
	/*$case++;*/

	/*ASIGNO EL VALOR DE 1 A EL PORCENTAJE INGRESADO EN CASO SEA CERO*/
/*	if($row[11]=0){
		$row[11]=1;
		}*/
	/*FIN DE LA ASIGNACION*/
	/*$timestamp22 ES LA FECHA INICIAL PARA DETERMINAR SI TODAVIA NO HA EMPEZADO LA FICHA INICIAL*/
	/*$timestamp4  ES LA FECHA ACTUAL*/

	/*if ($timestamp22>=$timestamp4) */
	if ($timestamp22>$timestamp4)
	{ /*ESTE IF ME INDICA SI LA FECHA ACTUAL AUN NO ES IGUAL A LA FECHA DE INICIO DE LA ACTIVIDAD*/
		/*$color = 'verde';	*/
		$color='<img src="imagenes/rojo.jpg" width=17 height=17 alt="Indicador" border=0></a>';
	}
	else
	{ /*ELSE #1*/
		if($numero_dias3 < $fase1_dias)
			{	/*IF GENERAL*/
			/*ESTE IF DETERMINA SI SE ENCUENTRA EN LA FASE 1 DIAS*/
			/*if($row[11]<=$porcentaje_fase1)*/  /* IF V1*/


			if($row[11]>$porcentaje_fase1)
				{ 	/*IF DE FASE 1*/
					/*$color = 'verde';*/
					$color='<img src="imagenes/verde.jpg" width=17 height=17 alt="Indicador" border=0></a>';
				}
			else
				{
				$porcentaje=$row[11];
				/*IGUALO EL PORCENTAJE INGRESADO A 1 EN CASO SEA CERO*/
				/*$porcentaje=$row[11];*/
				if($porcentaje==0) {
									$porcentaje=1;
									}
				/*TERMINO DE IGUALAR LOS PORCENTAJES A 1 */
				$indice_fase1 = $fase1_dias/$porcentaje_fase1;
				$indice_calculado = $numero_dias3/$porcentaje;


					/*if($indice_fase1<$indice_calculado_1_3)*/
					if($indice_fase1>$indice_calculado)
					{

						/*$color= 'amarillo';*/
						$color='<img src="imagenes/amarillo.jpg" width=17 height=17 alt="Indicador" border=0></a>';
					}
					else
					{
						/*$color= 'rojo';*/
						$color='<img src="imagenes/rojo.jpg" width=17 height=17 alt="Indicador" border=0></a>';

					}


				}	/*FIN DE IF DE FASE 1*/
			}	/*FIN DE IF GENERAL*/
		else
			{/*ELSE GENERAL*/
			if(($fase1_dias<=$numero_dias3)and($numero_dias3<=$fase3_dias))
				{
			/*ESTE IF DETERMINA SI SE ENCUENTRA EN LA FASE 2 DIAS*/
			/*if($row[11]<=$porcentaje_fase2)*/
				if($row[11]>$porcentaje_fase2)
					{ 	/*IF DE FASE 2*/
					/*$color = 'verde';*/
					$color='<img src="imagenes/verde.jpg" width=17 height=17 alt="Indicador" border=0></a>';
					}
				else
					{
						$porcentaje=$row[11];
				/*IGUALO EL PORCENTAJE INGRESADO A 1 EN CASO SEA CERO*/
				/*$porcentaje=$row[11];*/
				if($porcentaje==0) {
									$porcentaje=1;
									}
				/*TERMINO DE IGUALAR LOS PORCENTAJES A 1 */
						$indice_fase2 = $fase2_dias/$porcentaje_fase2;
						$indice_calculado = $numero_dias3/$porcentaje;

						if($indice_fase2>$indice_calculado)
						{
							/*$color = 'amarillo';*/
							$color='<img src="imagenes/amarillo.jpg" width=17 height=17 alt="Indicador" border=0></a>';
						}
						else
						{
							/*$color= 'rojo';*/
							$color='<img src="imagenes/rojo.jpg" width=17 height=17 alt="Indicador" border=0></a>';
						}

					}	/*FIN DE IF DE FASE 2*/

				}
			/*ELSE PARA FASE 3*/
			else
				{
					if(($fase2_dias<=$numero_dias3)and($numero_dias3<=$fase3_dias))
					{/*ABRE EL IF PARA LA FASE 3*/
					/*IGUALO EL PORCENTAJE INGRESADO A 1 EN CASO SEA CERO*/
					/*$porcentaje=$row[11];
					if($porcentaje==0) {
										$porcentaje=1;
										}*/
					/*TERMINO DE IGUALAR LOS PORCENTAJES A 1 */
					/*ESTE IF DETERMINA SI SE ENCUENTRA EN LA FASE 3 DIAS*/
					if($row[11]>$porcentaje_fase3)
						{ 	/*IF DE FASE 3*/
						/*$color = 'verde';*/
						$color='<img src="imagenes/verde.jpg" width=17 height=17 alt="Indicador" border=0></a>';


						}	/*FIN DE IF DE FASE 3*/

					else{ /*ELSE DE LA FASE 3*/
						$porcentaje=$row[11];
						if($porcentaje==0)
						{
							$porcentaje=1;
						}
						$indice_fase3 = $fase3_dias/$porcentaje_fase3;
						$indice_calculado = $numero_dias3/$porcentaje;

							if($indice_fase3>$indice_calculado)
							{
								/*$color = 'amarillo';*/
								$color='<img src="imagenes/amarillo.jpg" width=17 height=17 alt="Indicador" border=0></a>';
							}
							else
							{
								/*$color= 'rojo';*/
								$color='<img src="imagenes/rojo.jpg" width=17 height=17 alt="Indicador" border=0></a>';

							}
						} /*FIN DEL ELSE DE LA FASE 3*/

					}/*CIERRA EL IF PARA LA FASE 3*/

					/*1 CASO MAS*/
					else
					{
						if($fase3_dias<$numero_dias3)
						{
							if($row[11]>$porcentaje_fase3)
							{
							/*$color = 'verde';*/
							$color='<img src="imagenes/verde.jpg" width=17 height=17 alt="Indicador" border=0></a>';
							}
							else
							{
							/*$color= 'rojo';*/
							$color='<img src="imagenes/rojo.jpg" width=17 height=17 alt="Indicador" border=0></a>';
							}
						}
					}

				}

			}/*FIN DEL ELSE GENERAL*/


	}/* CIERRA EL  ELSE #1*/

	/******************/
	/*HASTA AQUI EL ALGORITMO PARA EL CALCULO DE LOS COLORES*/
	echo '<td bgcolor="'.$tcol.'">';
			/*echo '<font size="1">Queda _ '.$numero_dias2.' FASE 1 _ '.$fase1_dias.' FASE 2 _ '.$fase2_dias.' FASE 3 _'.$fase3_dias.' AVANZADO _ '.$numero_dias3.' _COLOR _ '.$color.'</font>';*/
			echo '<center><font size="1">'.$color.'</font></center>';
		 echo ' </td>';
		 		echo '	<td bgcolor="'.$tcol.'">';
				if ($row[16]==1)
				{
					echo '<font size="1">Finalizado por El usuario el dia '.$row[17].'</font>';
				}
				else
				{
					echo '<font size="1">-</font>';
				}

		    echo '</td>';



	echo'  </tr>';
}
if ($hn!=0)
{


	echo '<tr><td colspan="2" align="right"><b><font size="1">Hrs.&nbsp;</font></b></td><td colspan="4"><b><font size="1">'.$hn.'</font></b></td></tr>';
}
if ($bandera==0){echo '</table><br>';}

echo '<table><tr><td colspan="2" align="right"><b><font size="2">Total Hrs. Semanal:</font></b></td><td><b><font size="2">'.($hl+$hn).'</font></b></td></tr></table>';
}
echo '</table>';
noconex($conn);
}


/*AGREGO MI FUNCION PARA GENERAR FORM QYE INSERTA DATOS A LA TABLA trab_historial - 27/07/2011*/
function add_trab_historial($idtrab,$sex)
{
	$conn=conex();
	echo $idtrab;
	echo '+++';
	echo $sex;
	echo '+++';
	echo'<br>';
/*++++++++++++++++++++++++++++++++++++++++++++++++++*/

/*CODIGO PARA AGREGAR  A LA TABLA*/
	date_default_timezone_set('America/Lima');
	$dia=date("d/m/Y");

if ($_POST["vagregar2"]=="Agregar")
{
	$conn=conex();
	date_default_timezone_set('America/Lima');
	$dia=date("d/m/Y");
	$idtrab=$_POST["vidtrab"];
	$vdetalle=$_POST["vdetalle"];
	if (isset($vdetalle)==false){$vdetalle="";}

	/*$sql="exec sp_add_trab_historial ".$idtrab.", ".$vdetalle.",'".$dia."'";*/

	/***/
	$sql="exec sp_add_trab_historial ".$idtrab.", '".$vdetalle."', '".$dia."'";

	/*$sql_modificar="exec sp_add_trabregistro ".$idtrab.", '".$vporcentaje_editar."', '".$vdocumento_editar."', '".$fecha_registro."'";	*/

	/*++*/

	/*$sql="insert into trab_historial values ( ".$idtrab.", ".$vdetalle.",'".$dia."')";	*/
	$result=luis($conn, $sql);
	cierra($result);
	noconex($conn);
	echo'<br><br><br><br><br>';
	echo '**HHHHH***';
}

/*HASTA AQUI*/

/*++++++++++++++++++++++++++++++++++++++++++++++++++*/

	echo '<FORM METHOD="POST" ACTION="detalle_actividad.php?id='.$idtrab.'&sesion='.$sex.'" name="frmdetalle">';
	/*echo '<FORM METHOD="POST" ACTION="TransAddHistorial.php?id='.$idtrab.'&sesion='.$sex.'" name="frmhistorial">';	*/
	echo'<br>';

	$sql="select idtrab, actividad, dactividad from trab where idtrab=".$idtrab;
	$result=luis($conn, $sql);
	while ($row =fetchrow($result,-1))
	{
	echo '<table border="0"  bgcolor="#CCE6FF">';
	echo '<tr>';
	echo '<th width="150" ><font size="2">Historial de la Actividad</font></th>';
	echo '</tr>';
	/*CAJA DE TEXTO PARA QUE CAPTURA LA Actividad*/
	echo '<tr>';
	echo '<td width="150" ><font size="1">Actividad</font></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td width="150" >
			<font size="1">
				<INPUT TYPE="text" class="ftexto" NAME="vacti"  size="133" maxlength="100"  value="'.$row[1].'">
			</font>';
	echo '</td>';
	echo '</tr>';
	/*CAJA DE TEXTO PARA QUE CAPTURA EL Detalle Actividad*/
	echo '<tr>';
	echo '<td width="150"><font size="1">Detalle Actividad</font></td>';
	echo '</tr>';
	echo'<tr>';
	echo '<td width="150">
			<font size="1">
				<INPUT TYPE="text" class="ftexto" NAME="vdacti" size="133" maxlength="100" value="'.$row[2].'" >
			</font>
		</td>';
	echo '</tr>';
	/*TEXT AREA DE TEXTO PARA QUE CAPTURA LA descripcion del Avance de la Actividad*/
	echo '<tr>';
	echo '<td width="150" ><font size="1">Descripción del avance de la actividad</font></td>';
	echo '</tr>';
	echo '<tr>';

	echo '<td width="150" >
			<font size="1">';
	/*				echo'<textarea name="vdetalle" title="Escribir la importancia de la actividad" cols="100" rows="7" ></textarea>';*/


	echo '<INPUT TYPE="text" class="ftexto" NAME="vdetalle" title="Escribir el detalle de la actividad" size="133" maxlength="100">';


	/*echo'<input type="text" NAME="vdetalle" size="133" maxlength="100" value="'.$vdetalle.'" >';*/

		echo'</font>
		</td>';
	echo '</tr>';

	echo '<tr>';
	echo '<input type="hidden" name="vagregar2">';
	echo '<input type="hidden" name="vidtrab" value="'.$idtrab.'">';
	echo '<td width="150" >
			<font size="1">	';
		echo'<input class="btns" type=button onClick="javascript:msj()" value="Agregar"/>';
		/*echo'<input class="btns" type=submit  value="Agregar"/>';*/
		echo'</font>

		  </td>';
	echo '</tr>';
			}
 		cierra($result);

		/*Aumente un echo '</tr>';*/
		/*echo '</tr>';*/
		/*hasta aqui*/
		echo '</table>';

	echo '</form>';
	noconex($conn);
}
/*HASTA AQUI ES LA FUNCION*/
/*AGREGO MI FUNCION PARA GENERAR FORM QYE INSERTA DATOS A LA TABLA trab_historial - 03/08/2011*/
function view_trab_doc($idtrab,$sex)
{
	$conn=conex();
	/*echo $idtrab;
	echo '+++';
	echo $sex;
	echo '+++';
	echo'<br>';*/
	echo '<FORM METHOD="POST" ACTION="detalle_doc_actividad.php?id='.$idtrab.'&sesion='.$sex.'" name="frmdetalle">';
	echo'<br>';
	echo'<center>';
	echo '<table border="0" bgcolor="#CCE6FF">';
	echo '<tr>';
	echo '<th width="800" colspan="5" ><font size="2">Historial de la Actividad</font></th>';
	echo '</tr>';
	echo '<tr bgcolor="#F3F9FC">';
		echo '<td><center><font size="1"><strong>Detalle Actividad</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Historial Porcentaje</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Documennto Referencia</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Fecha de Registro</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Hora de Registro</strong></font></center></td>';
	echo '</tr>';


	$sql='exec sp_view_trabregistro_doc"'.$idtrab.'"';

	/*$sql="select tr.idtrab, tr.porcent_avance, tr.doc_referencia,
tr.fecha_registro, t.idtrab, t.dactividad from trab_registro tr left join trab t on tr.idtrab=t.idtrab  where tr.idtrab=".$idtrab." order by tr.fecha_registro asc";*/

	/*$sql="select tr.idtrab, tr.porcent_avance, tr.doc_referencia,
convert (varchar(10), tr.fecha_registro, 103), t.idtrab, t.dactividad from trab_registro tr left join trab t on tr.idtrab=t.idtrab  where tr.idtrab=".$idtrab." order by tr.fecha_registro asc";*/



	$result=luis($conn, $sql);
	while ($row =fetchrow($result,-1))
	{
	echo '<tr>';
	if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}
	echo '<td width="150" bgcolor="'.$tcol.'" ><font size="1">'.$row[7].'</font></td>';
	$doc=$row[3];
	if($doc== ' ')
	{
	$doc='No ingreso referencia';
		}
	echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[2].'</font></center></td>';
	echo '<td width="150" bgcolor="'.$tcol.'" ><font size="1">'.$doc.'</font></td>';
	echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[4].'</font></center></td>';
	echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[5].'</font></center></td>';
	echo '</tr>';
			}
 	cierra($result);
	echo '</table>';
	echo'</center>';
	echo '</form>';
	noconex($conn);
}
/*HASTA AQUI ES LA FUNCION*/

/*AGREGO MI FUNCION PARA GENERAR FORM QUE LISTA DATOS A LA TABLA trab_historial - 10/08/2011*/
/*function view_trab_informe($idtrab,$sex,$file)	*/
function view_trab_informe($idtrab,$sex)
{
	$conn=conex();

	/*echo $idtrab;
	echo'<br>';
	echo '+++';
	echo $sex;
	echo '+++';
	echo'<br>';*/
	/*echo $file;
	echo'<br>';	*/


	echo '<FORM METHOD="POST" ACTION="print_informe_trab.php?id='.$idtrab.'&sesion='.$sex.'" name="frmdetalle">';
		echo'<br>';
		echo'<center>';
		echo '<table border="0" bgcolor="#CCE6FF">';
		echo '<tr>';
		echo '<th width="800" colspan="8" ><font size="2">Historial de Informes de la Actividad</font></th>';
		echo '</tr>';
		echo '<tr bgcolor="#F3F9FC">';
			echo '<td><center><font size="1"><strong>Detalle Actividad</strong></font></center></td>';
			echo '<td><center><font size="1"><strong>Informe</strong></font></center></td>';
			echo '<td><center><font size="1"><strong>Porcentaje de Avance</strong></font></center></td>';

			echo '<td><center><font size="1"><strong>Indicador</strong></font></center></td>';

			echo '<td><center><font size="1"><strong>Fecha de Registro</strong></font></center></td>';
			echo '<td><center><font size="1"><strong>Hora de Registro</strong></font></center></td>';
			echo '<td><center><font size="1"><strong>Ver PDF</strong></font></center></td>';
			echo '<td><center><font size="1"><strong>Estado</strong></font></center></td>';
			echo '<td><center><font size="1"><strong>Motivo</strong></font></center></td>';
		echo '</tr>';


		$sql='exec sp_view_trabhistorial_info"'.$idtrab.'"';
		//echo $sql;
		$result=luis($conn, $sql);
		while ($row =fetchrow($result,-1))
		{
		echo '<tr>';
		if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}
		/*LA VARIABLE $row[7] ES EL detalle de la actividad*/
		echo '<td width="150" bgcolor="'.$tcol.'" ><font size="1">'.$row[7].'</font></td>';
		/*$doc=$row[3];
		if($doc== ' ')
		{
		$doc='No ingreso referencia';
			}*/
		/*LA VARIABLE $row[2] ES EL NOMBRE DEL informe*/
		echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[2].'</font></center></td>';
		/*LA VARIABLE $row[3] ES EL PORCENTAJE DE AVANCE DEL informe*/
		echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[3].'</font></center></td>';

		// DEFINO LOS COLORES DEL INDICADOR EN BASE AL % DE AVANCE DE LA ACTIVIDAD
		if($row[3]<33)
		{
			$color='<img src="imagenes/rojo.jpg" width=17 height=17 alt="Indicador" border=0></a>';
		}
		else
		{
			if(($row[3]>=33)and($row[3]<=66))
			{
				$color='<img src="imagenes/amarillo.jpg" width=17 height=17 alt="Indicador" border=0></a>';
			}
			else
			{
				if($row[3]>66)
				{
				$color='<img src="imagenes/verde.jpg" width=17 height=17 alt="Indicador" border=0></a>';
				}
			}
		}
		echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$color.'</font></center></td>';



		/*LA VARIABLE $row[4] ES LA Fecha de Registro*/
		echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[4].'</font></center></td>';
		/*LA VARIABLE $row[5] ES LA Hora de Registro*/
		echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[5].'</font></center></td>';


		//LLAMA AL ARCHIVO PHP encripta_pdf.php QUE CONTIENE LAS FUNCIONES PARA DESENCRIPTAR
		require_once('encripta_pdf.php');
		/*LA VARIABLE $row[6] ES Ver PDF*/
		echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">';
			echo '<a href="imprimir_trab.php?sesion='.$sex.'&idhistorial='.fn_encriptar($row[0]).'&idtrab='.fn_encriptar($row[6]).'" target="_blank" >PDF</a>';
		echo '</font></center></td>';

	$id_historial=$row[0]; // capturo el ultimo idhistorial de esta actividad
	$idtrab_historial=$row[6]; // capturo el ultimo idtrab_historial de esta actividad
	$estado_trabhistorial=$row[8];// capturo el ultimo estado_historial de esta actividad

	/*CAMBIO EL ESTADO A  10 SI ES 1 Y 20 SI ES 2 */
	if($estado_trabhistorial == 1)
	{
		$estado_trabhistorial=10;
		$sql_edita_estado="exec sp_edit_trabhistorial_estado ".$id_historial.", ".$idtrab_historial.", ".$estado_trabhistorial;
		$resultado=luis($conn, $sql_edita_estado);
		cierra($resultado);
	}
	else
	{
		if($estado_trabhistorial == 2)
		{
			$estado_trabhistorial=20;
			$sql_edita_estado="exec sp_edit_trabhistorial_estado ".$id_historial.", ".$idtrab_historial.", ".$estado_trabhistorial;
			$resultado=luis($conn, $sql_edita_estado);
			cierra($resultado);

		}
	}
	/*HASTA AQUI CAMBIO EL ESTADO*/

	// LA VARIABLE $row[8]; ES EL ESTADO DEL de Registro
	if($row[8]==0)
	/*if($row[8]==3)*/
	{
		$estado = 'Por revisar';
		$mensaje_estado = 'No se podrá registrar otro informe hasta que haya sido revisado el último informe.';
	}
	else
	{
		/*if($row[8]==1)*/
		if(substr($row[8], 0, 1)==1)
		{
			$estado = 'Aprobado';
			$mensaje_estado = 'Ya puede registrar otro informe.';
		}
		else
		{
				/*if($row[8]==2)*/
				if(substr($row[8], 0, 1)==2)
				{
					$estado = 'Rechazado';
					$mensaje_estado = 'Ya puede registrar otro informe.';
				}
		}
	}

		echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$estado.'</font></center></td>';

	//LINK PARA GENERAR PDF DE MOTIVO
		echo '<td width="150" bgcolor="'.$tcol.'" >';
			echo '<center><font size="1">';
			echo '<a href="imprimir_motivo.php?sesion='.$sex.'&idhistorial='.fn_encriptar($row[0]).'&idtrab='.fn_encriptar($row[6]).'" target="_blank" >Motivo en PDF</a>';

			echo'</font></center></td>';
	echo'</td>';

		echo '</tr>';
				}
		cierra($result);
		echo '</table>';
		echo'<br><br>';
		echo '<strong><font size="2">'.$mensaje_estado.'</font></strong>';

	/*CAMBIO EL ESTADO A  1 SI ES 10 Y 2 SI ES 20 DE LA ULTIMA ACTVIDAD*/
	if($estado_trabhistorial == 10)
	{

		$estado_trabhistorial=1;
		$sql_edita_estado="exec sp_edit_trabhistorial_estado ".$id_historial.", ".$idtrab_historial.", ".$estado_trabhistorial;
		$resultado=luis($conn, $sql_edita_estado);
		cierra($resultado);
	}
	else
	{
		if($estado_trabhistorial == 20)
		{

			$estado_trabhistorial=2;
			$sql_edita_estado="exec sp_edit_trabhistorial_estado ".$id_historial.", ".$idtrab_historial.", ".$estado_trabhistorial;
			$resultado=luis($conn, $sql_edita_estado);
			cierra($resultado);

		}
	}

	/*HASTA AQUI CAMBIO EL ESTADO*/

		echo'</center>';
		echo '</form>';

	noconex($conn);
}
/*HASTA AQUI ES LA FUNCION*/

/*AGREGO MI FUNCION PARA GENERAR FORM QUE LISTA DATOS A LA TABLA trab_historial - 10/08/2011*/
//ESTE FORMULARIO ME PERMITE MODIFICAR EL ESTADO DE LA TABLA trab_historial
/*function view_trab_informe($idtrab,$sex,$file)	*/
function view_trab_informe_vice($idtrab,$sex)
{
	$conn=conex();
	/*echo $idtrab;
	echo'<br>';
	echo '+++';
	echo $sex;
	echo '+++';
	echo'<br>';*/
	/*echo $file;
	echo'<br>';	*/
	echo '<FORM METHOD="POST" ACTION="print_informe_trab_edit.php?id='.$idtrab.'&sesion='.$sex.'" name="frmdetalle">';
	echo'<br>';
	echo'<center>';
	echo '<table border="0" bgcolor="#CCE6FF">';
	echo '<tr>';
	echo '<th width="800" colspan="10" ><font size="2">Historial de Informes de la Actividad</font></th>';
	echo '</tr>';
	echo '<tr bgcolor="#F3F9FC">';
		echo '<td><center><font size="1"><strong>Detalle Actividad</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Informe</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Porcentaje de Avance</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Indicador</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Fecha de Registro</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Hora de Registro</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Ver PDF</strong></font></center></td>';

		echo '<td><center><font size="1"><strong>Estado</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Aprobado</strong></font></center></td>';
		echo '<td><center><font size="1"><strong>Rechazado</strong></font></center></td>';

		echo '<td><center><font size="1"><strong>Motivo</strong></font></center></td>';

	echo '</tr>';


	$sql='exec sp_view_trabhistorial_info"'.$idtrab.'"';
	//echo $sql;
	$result=luis($conn, $sql);
	while ($row =fetchrow($result,-1))
	{
	echo '<tr>';
	if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}
	/*LA VARIABLE $row[7] ES EL detalle de la actividad*/
	echo '<td width="150" bgcolor="'.$tcol.'" ><font size="1">'.$row[7].'</font></td>';
	/*$doc=$row[3];
	if($doc== ' ')
	{
	$doc='No ingreso referencia';
		}*/
	/*LA VARIABLE $row[2] ES EL NOMBRE DEL informe*/
	echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[2].'</font></center></td>';
	/*LA VARIABLE $row[3] ES EL PORCENTAJE DE AVANCE DEL informe*/
	echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[3].'</font></center></td>';
	// DEFINO LOS COLORES DEL INDICADOR EN BASE AL % DE AVANCE DE LA ACTIVIDAD
		if($row[3]<33)
		{
			$color='<img src="imagenes/rojo.jpg" width=17 height=17 alt="Indicador" border=0></a>';
		}
		else
		{
			if(($row[3]>=33)and($row[3]<=66))
			{
				$color='<img src="imagenes/amarillo.jpg" width=17 height=17 alt="Indicador" border=0></a>';
			}
			else
			{
				if($row[3]>66)
				{
				$color='<img src="imagenes/verde.jpg" width=17 height=17 alt="Indicador" border=0></a>';
				}
			}
		}
		echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$color.'</font></center></td>';

	/*LA VARIABLE $row[4] ES LA Fecha de Registro*/
	echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[4].'</font></center></td>';
	/*LA VARIABLE $row[5] ES LA Hora de Registro*/
	echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$row[5].'</font></center></td>';

	//LLAMA AL ARCHIVO PHP encripta_pdf.php QUE CONTIENE LAS FUNCIONES PARA ENCRIPTAR
	require_once('encripta_pdf.php');
	/*LA VARIABLE $row[6] ES Ver PDF*/
	echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">';
		echo '<a href="imprimir_trab.php?sesion='.$sex.'&idhistorial='.fn_encriptar($row[0]).'&idtrab='.fn_encriptar($row[6]).'" target="_blank" >PDF</a>';
		//echo '****';
		//echo '<a href="imprimir_matricula.php?sesion='.$sex.'&idhistorial='.fn_encriptar($row[0]).'&idtrab='.fn_encriptar($row[6]).'" target="_blank" >PDF_TABLA</a>';
	echo '</font></center></td>';
	$id_historial=$row[0]; // capturo el ultimo idhistorial de esta actividad
	$idtrab_historial=$row[6]; // capturo el ultimo idtrab_historial de esta actividad
	$estado_trabhistorial=$row[8];// capturo el ultimo estado_historial de esta actividad

	/*CAMBIO EL ESTADO A  10 SI ES 1 Y 20 SI ES 2 */
	if($estado_trabhistorial == 1)
	{
		$estado_trabhistorial=10;
		$sql_edita_estado="exec sp_edit_trabhistorial_estado ".$id_historial.", ".$idtrab_historial.", ".$estado_trabhistorial;
		$resultado=luis($conn, $sql_edita_estado);
		cierra($resultado);
	}
	else
	{
		if($estado_trabhistorial == 2)
		{
			$estado_trabhistorial=20;
			$sql_edita_estado="exec sp_edit_trabhistorial_estado ".$id_historial.", ".$idtrab_historial.", ".$estado_trabhistorial;
			$resultado=luis($conn, $sql_edita_estado);
			cierra($resultado);

		}
	}
	/*HASTA AQUI CAMBIO EL ESTADO*/


	// LA VARIABLE $row[8]; ES EL ESTADO DEL de Registro
	if($row[8]==0)
	/*if($row[8]==3)*/
	{
		$estado = 'Por revisar';
		$mensaje_estado = 'Tiene un informe pendiente por revisar.';
	}
	else
	{
		/*if($row[8]==1)*/
		if(substr($row[8], 0, 1)==1)
		{
			$estado = 'Aprobado';
			$mensaje_estado = 'No tiene ningún informe pendiente por revisar.';
		}
		else
		{
				/*if($row[8]==2)*/
				if(substr($row[8], 0, 1)==2)
				{
					$estado = 'Rechazado';
					$mensaje_estado = 'No tiene ningún informe pendiente por revisar.';
				}
		}
	}

	echo '<td width="150" bgcolor="'.$tcol.'" ><center><font size="1">'.$estado.'</font></center></td>';

	//LINK CON VALOR 1 PARA MOSTRAR ESTADO APROBAR
	echo '<td width="150" bgcolor="'.$tcol.'" >';
			echo '<center><font size="1">';

			echo '
			<a onclick="javascript:window.open(\'print_informe_trab_edit.php?sesion='.$sex.'&id='.fn_encriptar($row[6]).'&idhistorial='.fn_encriptar($row[0]).'&estado_historal=10\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=550,top=40,left=50\');return false" href="print_informe_trab_edit.php">Aprobar</a>';

			echo'</font></center></td>';
	echo'</td>';

	//LINK CON VALOR 1 PARA MOSTRAR ESTADO DESAPROBAR
	echo '<td width="150" bgcolor="'.$tcol.'" >';
			echo '<center><font size="1">';


			echo '
			<a onclick="javascript:window.open(\'print_informe_trab_edit.php?sesion='.$sex.'&id='.fn_encriptar($row[6]).'&idhistorial='.fn_encriptar($row[0]).'&estado_historal=20\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=550,top=40,left=50\');return false" href="print_informe_trab_edit.php">Rechazar</a>';

			echo'</font></center></td>';
	echo'</td>';


	//LINK PARA GENERAR PDF DE MOTIVO
	echo '<td width="150" bgcolor="'.$tcol.'" >';
			echo '<center><font size="1">';
			echo '<a href="imprimir_motivo.php?sesion='.$sex.'&idhistorial='.fn_encriptar($row[0]).'&idtrab='.fn_encriptar($row[6]).'" target="_blank" >Motivo en PDF</a>';

			echo'</font></center></td>';
	echo'</td>';


	echo '</tr>';
			}
 	cierra($result);
	echo '</table>';
	echo'<br><br>';
	echo '<strong><font size="2">'.$mensaje_estado.'</font></strong>';
	echo'</center>';
	/*echo $_SESSION['codigox'];*/ // LA VARIABLE  $_SESSION['codigox'] ES = AL codper
	/*echo'<br>';
	echo $id_historial;
	echo'<br>';
	echo $idtrab_historial;
	echo'<br>';
	echo $estado_trabhistorial;*/
	/*CAMBIO EL ESTADO A  1 SI ES 10 Y 2 SI ES 20 DE LA ULTIMA ACTVIDAD*/
	if($estado_trabhistorial == 10)
	{
		/*$sql="update trab_historial set estado=1 where id_historial= ".$id_historial." and idtrab  ".$idtrab_historial;
		$result=luis($conn, $sql);
		cierra($result);*/
		$estado_trabhistorial=1;
		$sql_edita_estado="exec sp_edit_trabhistorial_estado ".$id_historial.", ".$idtrab_historial.", ".$estado_trabhistorial;
		$resultado=luis($conn, $sql_edita_estado);
		cierra($resultado);
	}
	else
	{
		if($estado_trabhistorial == 20)
		{
			/*$sql="update trab_historial set estado=2 where id_historial= ".$id_historial." and idtrab  ".$idtrab_historial;
			$result=luis($conn, $sql);
			cierra($result);*/
			$estado_trabhistorial=2;
			$sql_edita_estado="exec sp_edit_trabhistorial_estado ".$id_historial.", ".$idtrab_historial.", ".$estado_trabhistorial;
			$resultado=luis($conn, $sql_edita_estado);
			cierra($resultado);

		}
	}


		/*+++ ACORDEON ++*/

		/*$sql="select COUNT(0) from trab where codigo=".$codigo." and idsem =".$semestre_acti;
		$result=luis($conn, $sql);
		while ($row=fetchrow($result,-1))
		{
			$da++;
			$a=$row[0];
		}*/
		$a=1;
		if($a>0)
		{
			echo'
<div id="main">
	<div id="list3">
				<div>
					<div class="title">
					<img src="imagenes/flecha_trab.png" width=12 height=12 border=0></a> &nbsp;&nbsp;&nbsp; Ingrese el porque aprobo o desaprobo el informe -
					<font size="1"><blink> Haga clic izquierdo aqui para visualizar el formulario.</blink></font>
					</div>
					<div>
						<p>';
						 /*CREO COMBO CON LOS DETALLES DE LAS ACTIVIDADES*/
						$sql='exec sp_view_trabhistorial_info"'.$idtrab.'"';
						$result=luis($conn, $sql);

						echo'<font size="1">Seleccione el Informe</font>';
						echo' &nbsp;&nbsp;&nbsp;&nbsp; ';
						echo'<select size="1" name="vinforme_historial" title="Seleccionar el Informe">';
							while ($row=fetchrow($result,-1))
							{
								$da++;
								//LA VARIABLE $row[0] ES = id_historial
								//LA VARIABLE $row[1] ES = idtrab
								//LA VARIABLE $row[2] ES = informe
								$guion='-';
							echo '<option value="'.$cadena=$row[0].$guion.$row[1].'">'.$row[2].'</option>';
							}
						echo '</select>';

						noconex($conn);

						echo'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						echo'<input class="btns" type="submit" name="vingresar" value="Ingresar"  onClick="return confirmSubmit2()"/>';

						echo'<br>';
						echo'<br>';
						echo'<font size="1">Ingrese el porque aprobo o desaprobo el informe:</font>';
						/*echo'<br>'; */
						echo'<br>';
						echo'
						<textarea class="ftexto" NAME="vmotivo_historial" title="Escribir el detalle de la actividad" rows="3" cols="140"></textarea>';
						echo'<br>';


						/*echo'<input class="btns" type="submit" name="vingresar" value="Ingresar"  onClick="return confirmSubmit2()"/>';*/

						cierra($result);
						echo'</p>
					</div>
				</div>
	</div>
</div>
					';
		}
		/*noconex($conn);*/
		/*FIN ACORDEON*/


	/*HASTA AQUI CAMBIO EL ESTADO*/
	/*echo'</center>';*/
	echo '</form>';

}
	noconex($conn);

/*HASTA AQUI ES LA FUNCION*/

/*Gary - Añadi esta funcion para mostrar los horarios por el semestre seleccionado en el PIT para el modulo de estadistica varia en el procedimiento*/
/*AGREGUE LA VARIALBE $file_php PARA DETERMINAR SI ESTOY EN EL ARCHIVO estadistica.php O buscab.php*/
function individualPIT($codigo, $sex, $codper, $busca, $file_php,$sem) //GABO AÑADIO SEMESTRE_DENOMINACION
/*function individual($codigo, $sex, $codper, $busca);*/
{
$conn=conex();
		$sql="select (select descrip from depe where depe.iddepe=individual.iddepe) as facultad, nomcondicion, nomnivcateg, dedicacion, nrohoras, nombres, titulo, titulo2, titulo3, (select descrip from depe where depe.iddepe=idesc) as escuela from individual where codper=".$codper;
		//echo $sql;
		$result=luis($conn, $sql);
		$ko=1;
		while ($row=fetchrow($result,-1))
		{
		if ($row[3]=="TC" or $row[3]=="DE" or $row[3]=="TP"){
		echo '<table border="1" width="80%" id="table9" cellspacing="0">';
		echo '<tr><td bgcolor="#dbeaf5" align="left"><font size="1">Facultad</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[0].'</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left"><font size="1">Escuela</font></td><td bgcolor="#FFFFFF" align="left"><font size="1"></font>'.$row[9].'</td></tr>';
		echo '<tr><td bgcolor="#dbeaf5" align="left"><font size="1">Nombre</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[5].'</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left"><font size="1">Horas</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[4].'</font></td></tr>';
		echo '<tr><td bgcolor="#dbeaf5" align="left"><font size="1">Condicion</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[1].'</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left"><font size="1">Categoria</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[2].'</font></td></tr>';
		echo '<tr><td bgcolor="#dbeaf5" align="left"><font size="1">Titulo(s)</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[6].'</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left"><font size="1">Dedicacion</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[3].'</font></td></tr>';
		echo '<tr><td bgcolor="#dbeaf5" align="left"><font size="1">Otros</font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[7].'</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left"><font size="1"></font></td><td bgcolor="#FFFFFF" align="left"><font size="1">'.$row[8].'</font></td></tr>';
		echo '</table>';
															}
		else{
			$ko=0;
			}
		}
/*termina de generar la tabla que muestra los datos del docente*/

/*genera la tabla con los horarios de EL DETALLE DE LA CARGA LECTIVA*/
	if ($ko==1){
		echo '<br>';
		cierra($result);
		echo '<table border="1" width="100%" id="table9" cellspacing="0">';
		echo '<tr><th colspan="14">Detalle de Carga Lectiva</th></tr>';
		echo '<tr>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Codigo</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">seccion</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Dep.</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Hrs</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Alumnos</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Aula</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Calif.</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Lunes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Martes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Miércoles</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Jueves</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Viernes</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Sábado</font></td>';
		echo '<td bgcolor="#dbeaf5" align="left">';
		echo '<font size="1">Domingo</font></td>';
		echo '</tr>';

/* SE ENCARGA DE MOSTRAR LOS DATOS DEL DETALLE DE LA CARGA LECTIVA POR DOCENTE*/

$kl=0;
//$sql="select a.idcurso, a.seccion, codcurso, descurso, ht, a.idcarga, mat, dia, left(convert(varchar(11),ini,108),5) ini, left(convert(varchar(11),fin,108),5) fin, upt.dbo.fdepe(a.iddepe) as depe, a.usuario from carga a inner join curso d on a.idcurso=d.idcurso inner join (select count(i.coduniv) mat, idcarga from cursomatricula i, estudiante j, carga k where i.coduniv=j.coduniv and i.itemest=j.itemest and i.idsem=k.idsem and i.idcurso=k.idcurso and i.seccion=k.seccion and j.iddepe=k.iddepe and i.codestado<5 and codigo=".$codigo." group by k.idcarga) l on l.idcarga=a.idcarga inner join (select idcarga, sum(mi)/50 ht from (select a.idcarga, datediff(mi,ini,fin) mi from carga a left join (select idhora, dia, idcurso, iddepe, idsem, seccion, fhora ini from horarioc where tipo=0 ) b  on a.iddepe=b.iddepe and a.idcurso=b.idcurso and b.seccion=a.seccion and b.idsem in (select idsem from semestre where tipo=1 and activo=1) left join (select idhora, fhora fin from horarioc where tipo=1) c on b.idhora=c.idhora where codigo=".$codigo." ) a group by idcarga) e on e.idcarga=a.idcarga left join (select idhora, dia, idcurso, iddepe, idsem, seccion, fhora ini from horarioc where tipo=0 ) b  on a.iddepe=b.iddepe and a.idcurso=b.idcurso and b.seccion=a.seccion and b.idsem in (select idsem from semestre where tipo=1 and activo=1) left join (select idhora, fhora fin from horarioc where tipo=1) c on b.idhora=c.idhora where codigo=".$codigo." order by codcurso, a.seccion, dia, ini";
//$sql="exec trabindividual ".$codigo.",".$sem;
//$sql="exec trabindividual_v2 ".$codigo.",".$sem;	//Modificado para Mostrar antigua carga - GARy
$sql="exec trabindividual_v2_PIT ".$codigo.",".$sem;	//Modificado para Mostrar antigua carga - GARy
//echo $sql;	//-- GARY -- Estadisticas Trabahjo individual --
$resulta=luis($conn, $sql);
$idc="";
//$sec="";
$fl=0;

do {
    //if ($idc<>$row[0] or $sec<>$row[1])
    if ($idc<>$row[5])
    {
	if ($fl>0)
	{
		For ($j=1;$j<=7;$j++)
		{
			if ($d[$j][$fl]>$d[0][$fl])
			{
				$d[0][$fl]=$d[$j][$fl];
			}
			}
	}
	$fl++;
	For ($i=1;$i<=7;$i++)
	{
		$d[$i][$fl]=0;
	}
    }
	if ($row[7] == 1) {
	$d[1][$fl]++;
	$ta[1][$fl][$d[1][$fl]]=$row[6];
	$tb[1][$fl][$d[1][$fl]]=$row[8];
	$tc[1][$fl][$d[1][$fl]]=$row[9];
	} elseif ($row[7] == 2) {
    	$d[2][$fl]++;
	$ta[2][$fl][$d[2][$fl]]=$row[6];
	$tb[2][$fl][$d[2][$fl]]=$row[8];
	$tc[2][$fl][$d[2][$fl]]=$row[9];
	} elseif ($row[7] == 3) {
    	$d[3][$fl]++;
    	$ta[3][$fl][$d[3][$fl]]=$row[6];
	$tb[3][$fl][$d[3][$fl]]=$row[8];
	$tc[3][$fl][$d[3][$fl]]=$row[9];
    	} elseif ($row[7] == 4) {
    	$d[4][$fl]++;
    	$ta[4][$fl][$d[4][$fl]]=$row[6];
	$tb[4][$fl][$d[4][$fl]]=$row[8];
	$tc[4][$fl][$d[4][$fl]]=$row[9];
    	} elseif ($row[7] == 5) {
    	$d[5][$fl]++;
    	$ta[5][$fl][$d[5][$fl]]=$row[6];
	$tb[5][$fl][$d[5][$fl]]=$row[8];
	$tc[5][$fl][$d[5][$fl]]=$row[9];
    	} elseif ($row[7] == 6) {
    	$d[6][$fl]++;
    	$ta[6][$fl][$d[6][$fl]]=$row[6];
	$tb[6][$fl][$d[6][$fl]]=$row[8];
	$tc[6][$fl][$d[6][$fl]]=$row[9];
    	} elseif ($row[7] == 7) {
    	$d[7][$fl]++;
    	$ta[7][$fl][$d[7][$fl]]=$row[6];
	$tb[7][$fl][$d[7][$fl]]=$row[8];
	$tc[7][$fl][$d[7][$fl]]=$row[9];
	}
    $idc=$row[5];
    //$idc=$row[0];
    //$sec=$row[1];
}while ($row=fetchrow($resulta,-1));
    //if ($idc<>$row[0] or $sec<>$row[1])
    if ($idc<>$row[5])
    {
	if ($fl>0)
	{
		For ($j=1;$j<=7;$j++)
		{
			if ($d[$j][$fl]>$d[0][$fl])
			{
				$d[0][$fl]=$d[$j][$fl];
			}
		}
	}
    }

$in=1;
fetchrow($resulta,$in-1);
$idc="";
//$sec="";
$l=0;
$hl=0;
while ($row=fetchrow($resulta,-1))
{
	//if ($idc<>$row[0] or $sec<>$row[1])
	if ($idc<>$row[5])
	{
		if ($ton==1){$tcol='#F3F9FC';$ton=0;}else{$tcol='#FFFFFF';$ton=1;}
			//echo '<tr><td width="92" bgcolor="'.$tcol.'"><font size="1">'.$row[2].'</font></td>';
    			//echo '<td width="92" bgcolor="'.$tcol.'"><font size="1">'.$row[1].'</font></td>';
			//echo '<tr><td width="644" colspan="7" bgcolor="'.$tcol.'"><font size="1">'.$row[2].'&nbsp;&nbsp;'.$row[1].'&nbsp;&nbsp;'.$row[3].'</font></td></tr>';
    			//echo '<td width="92" bgcolor="'.$tcol.'"><font size="1">'.$row[4].'</font></td></tr>';

    		$l++;
		if ($d[0][$l]>0)
		{
			For ($k=1;$k<=$d[0][$l];$k++)
			{
				echo '<tr>';
				if ($row[4]>0){$hl=$hl+$row[4];}
				//echo '<td width="45" bgcolor="'.$tcol.'"><font size="1">'.$row[2].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[1].'</font></td></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[10].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[4].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[6].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[11].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">1</font></td>';

				/*la variable $row[2] es = al codigo del DETALLE DE LA CARGA LECTIVA */
				/*la variable $row[1] es = a la seccion del DETALLE DE LA CARGA LECTIVA */
				/*la variable $row[10] es = a la depe del DETALLE DE LA CARGA LECTIVA */
				/*la variable $row[4] es = a la hora del DETALLE DE LA CARGA LECTIVA */
				/*la variable $row[6] es = a numero de alumnos DETALLE DE LA CARGA LECTIVA */
				/*la variable $row[11] es = a el salon donde se dictan las clases DETALLE DE LA CARGA LECTIVA */


				echo '<td width="45" bgcolor="'.$tcol.'"><font size="1">'.$row[2].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[1].'</font></td></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[10].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[4].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[6].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[11].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">1</font></td>';
				For ($j=1;$j<=7;$j++)
				{
					// if ($ta[$j][$l][$k]>0 )	// original
					// $ta[$j][$l][$k] = mat , mat es la cantidad de matriculados en el curso
					if ($ta[$j][$l][$k]>=0 )
					{
						/*la variable  $tb[$j][$l][$k] y la variable $tc[$j][$l][$k] se encargan de mostrar las horas de cada curso dentro de los dias de la semana*/
						echo '<td width="75" bgcolor="'.$tcol.'"><table border="0" width="100%"><tr>';
						echo '<td bgcolor="'.$tcol.'"><font size="1">'.$tb[$j][$l][$k].'</font></td>';
						echo '<td bgcolor="'.$tcol.'"><font size="1">'.$tc[$j][$l][$k].'</font></td>';
						echo '</tr></table></td>';
					}else{
						echo '<td bgcolor="'.$tcol.'"></td>';
					}
				}
				echo '</tr>';
			}
		}else{
			//echo '<td width="45" bgcolor="'.$tcol.'"><font size="1">'.$row[2].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[1].'</font></td></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[10].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1"></font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[6].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">&nbsp;</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">1</font></td>';
			if ($row[4]>0){$hl=$hl+$row[4];}
			echo '<td width="45" bgcolor="'.$tcol.'"><font size="1">'.$row[2].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[1].'</font></td></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[10].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[4].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">'.$row[6].'</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">&nbsp;</font></td><td width="40" bgcolor="'.$tcol.'"><font size="1">1</font></td>';

			For ($j=1;$j<=7;$j++)
			{
				echo '<td bgcolor="'.$tcol.'"></td>';
			}
			echo '</tr>';
		}
    	}
	//$idc=$row[0];
	//$sec=$row[1];
	$idc=$row[5];
}
echo '<tr><td colspan="3" align="right"><b><font size="1">Hrs.&nbsp;</font></b></td><td colspan="9" align="left"><b><font size="1">'.$hl.'</font></b></td><td colspan="2" align="left"><font size="1"><!--25% hrs. = '.round(($hl*0.25),1).'--></font></td></tr>';
echo '</table><br>';
cierra($resulta);

/*FORMULARIO DE CARGA NO LECTIVA , LISTA TODAS LAS ACTIVIDADES INGRESADAS EN EL FORMULARIO*/

if ($busca==0){
echo '<FORM METHOD="POST" ACTION="carga.php?tr=1&sesion='.$sex.'&x='.$sem.'" name="frmindiv">';
}


/*AGREGO ESTO PARA MANTENER LA SESION EN EL ARCHIVO estadistica.php*/
/*if($file_php==1)
{
	$busca=15;
}
if ($busca==15){
echo '<FORM METHOD="POST" ACTION="estadistica.php?tr=1&sesion='.$sex.'" name="frmindiv">';
}*/
/*HASTA AQUI  MANTENGO LA SESION EN EL ARCHIVO estadistica.php*/
if ($busca==1){
echo '<FORM METHOD="POST" ACTION="buscab.php?tr=1&sesion='.$sex.'" name="frmindiv">';
}

//$sql="select idtrab, actividad, dactividad, importancia, medida, cant, horas, calif, meta from trab where idsem in (select min(idsem) from semestre where tipo=1 and activo=1) and codigo=".$codigo;
//$sql="select idtrab, actividad, dactividad, importancia, medida, cant, horas, calif, meta from trab where idsem in (select min(idsem) from semestre where tipo in (1) and activo=1 and idsem not in (20080105,20080111)) and codigo=".$codigo;

/*Este procedimiento almacenado se encarga de listar los campos visualizados en la tabla Detalle de Carga No Lectiva y en base al campo idtrab de la tabla trab se encargara de listarlos los detalles de cada campo ingresado para el detalle de la carga de trabajo individual */
/*echo '+++';
echo $codigo;*/
/*PROCEDIMIENTO PARA CAPTURAR LA FECHA EN QUE INICIA EL SEMESTRE*/
$sql_semestre="exec trabisem ".$codigo;
//echo $sql_semestre;
$result_semestre=luis($conn, $sql_semestre);
while ($row=fetchrow($result_semestre,-1))
{
	$semestre=$row[1];
	$fecha=$row[2];
	$codigoo=$row[3];

}
	/*LA VARIABLE $semestre_acti INDICA EL SEMESTRE AL QUE CORRESPONDE LA ACTIVIDAD*/
	$semestre_acti=$semestre;
	$fecha_semestre=$fecha;
	/*LA VARIABLE $codigo_acti INDICA EL CODIGO AL QUE CORRESPONDE LA ACTIVIDAD*/
	$codigo_acti=$codigoo;

	echo'<input type="hidden" name="mcodigo" value="'.$codigo_acti.'">';
	echo'<input type="hidden" name="msemestre" value="'.$semestre_acti.'">';
	/*echo $semestre_acti;
	echo '+++++';
	echo $fecha_semestre;
	echo '+++++';
	echo $codigo_acti;
	echo '+++++';
	echo'<br>';*/
cierra($result_semestre);
	/*LA VARIABLE $fecha_semestre INDICA LA FECHA DE INICIO DEL SEMESTRE*/
	/*echo $fecha_semestre;
	echo'<br>';*/

	$fecha_final =$fecha_semestre; /*LA VARIABLE $fecha_final ES = LA FECHA EN QUE INCIA EL SEMESTRE*/
	/*$fecha_final ='10/10/2011';*/
	$dia = substr( $fecha_final, 0, 2 );
	$mes=  substr( $fecha_final, 3, 2 );
	$ano=  substr( $fecha_final, 6, 4 );

	date_default_timezone_set('America/Lima');
	$dia_actual=date("d/m/Y");
	$fecha_inical =$dia_actual;
	/*$fecha_inical ='01/10/2011';*/
	$dia2 = substr( $fecha_inical, 0, 2 );
	$mes2=  substr( $fecha_inical, 3, 2 );
	$ano2=  substr( $fecha_inical, 6, 4 );

	/*calculo timestam de las fecha FECHA FINAL*/
	$timestamp1 = mktime(0,0,0,$mes,$dia,$ano);		/*FECHA FINAL = FECHA INICIO DEL SEMESTRE*/
	/*$timestamp2 = mktime(4,12,0,$mes2,$dia2,$ano2);*/	/*FECHA INICIAL = FECHA ACTUAL*/
	$timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

	/*CALCULO EL NUMERO DE DIAS EN QUE INICIA EL SEMESTRE */
	$dias_segundos = $timestamp1;
	/*convierto segundos en días*/
	$num_dias = $dias_segundos / (60 * 60 * 24);
	/*obtengo el valor absoulto de los días (quito el posible signo negativo)*/
	$num_dias = abs($num_dias);
	/*quito los decimales a los días de diferencia*/
	$num_dias = floor($num_dias);
	$num_dias = $num_dias + 1;
	$dias_semetre=$num_dias+10; /*AGREGO 10 DIAS DE OLGURA PARA DESHABILITAR LOS BOTONES*/
	/*HASTA AQUI CALCULO EL NUMERO DE DIAS EN QUE INICIA EL SEMESTRE */

	/*CALCULO EL NUMERO DE DIAS ACTUALES  */
	$dias_segundos2 = $timestamp2;
	/*convierto segundos en días*/
	$num_dias2 = $dias_segundos2 / (60 * 60 * 24);
	/*obtengo el valor absoulto de los días (quito el posible signo negativo)*/
	$num_dias2 = abs($num_dias2);
	/*quito los decimales a los días de diferencia*/
	$num_dias2 = floor($num_dias2);
	$num_dias2 = $num_dias2 + 1;
	$dias_avance=$num_dias2; /*LA VARIABLE $dias_avance ES = LA FECHA ACTUAL QUE SERA COMPARADA
	CON LA CANTIDAD DE DIAS QUE POSEE EL SEMESTRE*/
	/*HASTA AQUI CALCULO EL NUMERO DE DIAS EN QUE INICIA EL SEMESTRE */

	/*
	echo'numero de dias de semestre';
	echo '<br>';
	echo $num_dias ;
	echo '<br>';
	echo'numero de dias de semestre + 10';
	echo '<br>';
	echo $num_dias = $num_dias  +10;
	echo '<br>';
	echo'numero de dias de actuales';
	echo '<br>';
	echo $num_dias2 ;
	echo '<br>';
	*/

	/*RADIO BUTONS PARA CAMBIAR DE ESTADO A TODAS LAS ACTIVIDADES*/
	$codigo_exec=$codigo;
	$sql="exec trabixdoc_v2 ".$codigo.",".$sem;
	//echo $sql;
	$result=luis($conn, $sql);
		while ($row =fetchrow($result,-1))
		{
		$idtrab=$row[0];
		}
		cierra($result);
		/*noconex($conn);	*/

		if ($idtrab>0)
		{

			if($file_php>0)
			{

				echo '<table border="1" cellspacing="0">';
				echo '<tr>';
					echo '<td colspan="1">
										<font size="3" face="Arial" ><strong>Descargar Guía de Usuario para Director de Escuela</strong>
										</font>
					</td>';
					echo '<td colspan="1">
						<font size="1" face="Arial">
								<blink>
									<a href="documentos/PIT_Docente/GUIA_PIT_DIRECTOR_V2.pdf" target="_blank">
										<center style="color: red;">( Haga clic aquí )</center>
									</a>
								</blink>
						</font>
					</td>';
				echo '</tr>';

				echo '<tr>';
				echo '<td colspan="1">
						<font size="3" face="Arial" ><strong>Habilitar o deshabilitar las Actividades del Detalle de Carga No Lectiva</strong>
						</font>
					</td>';
				require_once('encripta_pdf.php');
				echo '<td colspan="1">
						<font size="1" face="Arial">
								<blink>
							<a onclick="javascript:window.open(\'habilitar_pit.php?sesion='.$sex.'&codigo='.fn_encriptar($codigo).'&x='.$sem.'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=400,top=40,left=50\');return false" href="habilitar_pit.php">
									<center style="color: red;">( Haga clic aquí )</center>
								</blink>
							</a></font>
					</td>';
				echo '</tr>';
				echo '</table>';
				echo'<br>';
//			echo 'Descargar Manual de Usuario para Director de Escuela<a href="documentos/guia_pit_director.pdf" target="_blank">
//<span style="color: red;"><font size="1" face="Arial"><blink>( Haga clic aquí )</blink> </font></span></a>';
//			echo '<br><br>';
//
//			echo '<table border="1" cellspacing="0">';
//			echo '<th colspan="1">Habilitar o Deshabilitar las Actividades del Detalle de Carga No Lectiva</th>';
//
//			echo '<tr>';
//
//			echo '<td colspan="1" width="">
//					<font size="1">';
//			echo '<input type="hidden" name="coduni" value="'.$codigo.'">';
//			echo 'Habilitar
//			<input type="radio" NAME="mestado_editar"  value="1" size="35" >
//			 &nbsp;
//			Deshabilitar
//			<input type="radio" NAME="mestado_editar"  value="0" size="35" checked="checked">
//			 &nbsp;&nbsp;&nbsp;
//			<input class="btns" type="submit" name="medit" onClick="return confirmModificarEstado()" value="Modificar"/>
//					</font>';
//			echo '</td>';
//
//			echo '</tr>';
//			echo '</table>';
//			echo'<br>';

			}
		}

	/*HASTA AQUI SE GENERA LOS RADIO BUTONS */

		/*+++ ACORDEON ++*/

		$sql="select COUNT(0) from trab where codigo=".$codigo." and idsem =".$sem;
		//echo $sql;
		$result=luis($conn, $sql);
		while ($row=fetchrow($result,-1))
		{
			$da++;
			$a=$row[0];
		}
		/*echo'+++';
		echo $a;*/
		if($a>0)
		{
			echo'
<div id="main">
	<div id="list3">
				<div>
					<div class="title"><img src="imagenes/flecha_trab.png" width=12 height=12 border=0></a> &nbsp;&nbsp;&nbsp; Registre el avance de su actividad - <font size="1"><blink> Haga clic izquierdo aqui para visualizar el formulario.</blink></font></div>
					<div>
						<p>';


						 /*+++++*/
						 /*CREO COMBO CON LOS DETALLES DE LAS ACTIVIDADES*/
						$sql="select idtrab, dactividad from trab where codigo=".$codigo." and idsem =".$sem;


						$result=luis($conn, $sql);

						echo'<font size="1">Seleccione el detalle de su Actividad</font>';
						echo' &nbsp;&nbsp;&nbsp;&nbsp; ';
						echo'<select size="1" name="vdactividad_historial" title="Seleccionar el detalle de su actividad">';
							while ($row=fetchrow($result,-1))
							{
								$da++;
							echo '<option value="'.$row[0].'">'.$row[1].'</option>';
							}
						echo '</select>';

						cierra($result);
						/*noconex($conn);*/
						echo'<br>';
						echo'<br>';
						 /*+++*/
						  /* echo'<INPUT TYPE="text" class="ftexto" NAME="vdetalle_historial" title="Escribir el detalle de la actividad" size="133">';*/
						echo'<font size="1">Ingrese el Número del Informe:</font>';
						echo'<br>';
						echo'<br>';
						echo'
						<INPUT TYPE="text" class="ftexto" NAME="vnominfo_historial" title="Escribir el nombre del informe" size="156">';
						echo'<br>';
						echo'<br>';
						echo'<font size="1">Dirigido a:</font>';
						echo'<br>';
						echo'<br>';
						echo'
						<INPUT TYPE="text" class="ftexto" NAME="vdirigido_historial" title="Escribir el nombre  de la persona a quien va dirigido el informe" size="156">';
						echo'<br>';
						echo'<br>';
						echo'<font size="1">Cargo de la persona a quien va dirigido a:</font>';
						echo'<br>';
						echo'<br>';
						echo'
						<INPUT TYPE="text" class="ftexto" NAME="vcargo_historial" title="Escribir el cargo de la persona a quien va dirigido" size="156">';
						echo'<br>';
						echo'<br>';
						echo'<font size="1">Remitente:</font>';
						echo'<br>';
						echo'<br>';
						/*++++++++++*/
						/*$sql="select (select descrip from depe where depe.iddepe=individual.iddepe) as facultad, nomcondicion, nomnivcateg, dedicacion, nrohoras, nombres, titulo, titulo2, titulo3, (select descrip from depe where depe.iddepe=idesc) as escuela from individual where codper=".$codper;
						$result=luis($conn, $sql);
						while ($row=fetchrow($result,-1))
						{
						$nombre=$row[5];
						}
						cierra($result);*/
						/*++++++++++*/

						/*++++++++++*/
						//echo $codper;
						$sql="select codper, Nombres, sigla  from individual where codper =".$codper;
						$result_nombre=luis($conn, $sql);
						while ($row=fetchrow($result_nombre,-1))
						{
						$nombre_remitente=$row[1];
						$cargo_remitente=$row[2];
						}
						cierra($result_nombre);
						$espacio = ' ';
						$nombre_cargo_remitente =$cargo_remitente.$espacio.$nombre_remitente;
						/*++++++++++*/

						echo'
						<INPUT TYPE="text" class="ftexto" NAME="vremitente_historial" title="Escribir el nombre del informe" size="156" value="'.$nombre_cargo_remitente.'">';
						echo'<br>';
						echo'<br>';
						echo'<font size="1">Ingrese el Detalle de Acciones del Informe:</font>';
						echo'<br>';
						echo'<br>';
						echo'
						<textarea class="ftexto" NAME="vdetalle_historial" title="Escribir el detalle de la actividad" rows="3" cols="156" ></textarea>';
						/*echo'<br>';
						echo'<br>';
						echo'<font size="1">Ingrese el Detalle de Acciones del Informe - Parrafo 2:</font>';*/
						/*echo'<br>';
						echo'<br>';
						echo'
						<textarea class="ftexto" NAME="vdetalle2_historial" title="Escribir el detalle de la actividad" rows="3" cols="156" ></textarea>';*/
						echo'<br>';
						echo'<br>';

						echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Porcentaje de avance</font>';
						echo '&nbsp;&nbsp;&nbsp;';

						echo'<select size="1" name="vporcentaje_historial" title="Seleccionar la magnitud que representa lo establecido como porcentaje de avance">';
							for($porcentaje=0;$porcentaje<=100;$porcentaje++)
							{
								echo '<option value="'.$porcentaje.'">'.$porcentaje.'</option>';
							}
						echo '</select>';
						echo '&nbsp;&nbsp;&nbsp;';
						/*echo'<input class="btns" type="submit" name="addhistorial" value="Registrar"/>'; */
						echo '<input type="hidden" name="coduni" value="'.$codigo.'">';
						echo '<input type="hidden" name="addhistorial">';
echo '<input class="btns" type=button onClick="javascript:msjregistrar()" value="Registrar"/>';

						echo'</p>
					</div>
				</div>
	</div>
</div>
					';
		}
		noconex($conn);
		/*FIN ACORDEON*/



/*HASTA AQUI CAPTURO LA FECHA EN QUE INICIA EL SEMESTRE SEMESTRE*/
$sql="exec trabixdoc_v2 ".$codigo.",".$sem;
//echo $sql;
$da=0;
$hn=0;
/*AGREGUE ESTO PARA LA SUMA DE LOS PORCENTAJES*/
$suma_porcentaje = 0;
/*HASTA AQUI SUMA DE LOS PORCENTAJES*/
$suma_horas_lectivas = 0;

// Log the SQL query
$timestamp = date('Y-m-d H:i:s');
$logMessage = sprintf(
    "[%s] EJECUTANDO CONSULTA TRABIXDOC_V2 - SQL: %s\n" . str_repeat('-', 80) . "\n",
    $timestamp,
    $sql
);
writeToLogFile($logMessage, __FILE__);

// Display log if debug mode is enabled
if (isset($_GET['debug'])) {
    echo "<div style='background-color: #f0f0f0; border: 1px solid #ccc; padding: 10px; margin: 10px; font-family: monospace; font-size: 12px;'>";
    echo "<strong>DEBUG LOG - TRABIXDOC_V2:</strong><br>";
    echo nl2br(htmlspecialchars($logMessage));
    echo "</div>";
}

// Execute query
$result=luis($conn, $sql);

// Log the number of rows
$numRows = numrow($result);
$logMessage2 = sprintf(
    "[%s] RESULTADO CONSULTA TRABIXDOC_V2 - Número de filas: %d\n" . str_repeat('-', 80) . "\n",
    $timestamp,
    $numRows
);
writeToLogFile($logMessage2, __FILE__);

// Display log if debug mode is enabled
if (isset($_GET['debug'])) {
    echo "<div style='background-color: #f0f0f0; border: 1px solid #ccc; padding: 10px; margin: 10px; font-family: monospace; font-size: 12px;'>";
    echo "<strong>DEBUG LOG - RESULTADO TRABIXDOC_V2:</strong><br>";
    echo nl2br(htmlspecialchars($logMessage2));
    echo "</div>";
}

// Maybe log some sample rows
if ($numRows > 0) {
    fetchrow($result, 0, true); // Reset cursor
    $sampleRow = fetchrow($result, -1);
    $logMessage3 = sprintf(
        "[%s] MUESTRA FILA TRABIXDOC_V2 - %s\n" . str_repeat('-', 80) . "\n",
        $timestamp,
        json_encode($sampleRow)
    );
    writeToLogFile($logMessage3, __FILE__);

    // Display log if debug mode is enabled
    if (isset($_GET['debug'])) {
        echo "<div style='background-color: #f0f0f0; border: 1px solid #ccc; padding: 10px; margin: 10px; font-family: monospace; font-size: 12px;'>";
        echo "<strong>DEBUG LOG - MUESTRA FILA TRABIXDOC_V2:</strong><br>";
        echo nl2br(htmlspecialchars($logMessage3));
        echo "</div>";
    }
}
$bandera=1;
/*genera la tabla de Detalle de Carga No Lectiva*/
while ($row=fetchrow($result,-1))
{
    $da++;
    $suma_porcentaje = $suma_porcentaje+$row[11];
    if ($row[7] == 1) {
        $suma_horas_lectivas += $row[6];
    }
	/*if ($ton==1){$tcol='#FFE1FF';$ton=0;}else{$tcol='#E6CDB5';$ton=1;}*/
	if ($ton==1){$tcol='DEEBE5';$ton=0;}else{$tcol='#DBEAF5';$ton=1;}
	if ($row[6]>0){$hn=$hn+$row[6];}

	/*LA VARIABLE $row[14] ES EL ESTADO DE LA ACTIVIDAD*/
	$estado=$row[14];
	/*echo'CREA TABLA DE Detalle de Carga No Lectiva';*/


	echo '<table border="1" cellspacing="0">';
	if ($bandera==1){echo '<th colspan="5">2.-Detalle de Carga No Lectiva</th>';}
	echo '<tr>';
	/*+*/
	if($estado>0)
	{
	$editable=' - el contenido de la actividad puede ser editado';
	}
	else{
	$editable=' - <span style="color: red;"><strong>el contenido de la actividad no puede ser editado</strong></span>';
	}
	/*+*/
	echo '<td width="" colspan="1" bgcolor="'.$tcol.'"><font size="1">Actividad '.$da.' '.$editable.'</font></td>';
	/*echo '<td width="600" colspan="3" bgcolor="'.$tcol.'"><font size="1">Detalle Actividad</font></td>';*/
	echo '</tr>';
	echo '<tr>';
	/*La variable $row[1] imprime la Actividad*/
	echo '<td >
			<font size="1">';

			echo '<select size="1" name="vacti_editar'.$da.'" title="Seleccionar la magnitud que representa lo establecido como meta">';

   	echo '<option value="'.$row[1].'">'.$row[1].'</option>';
   	echo '<option value="Academica">Academica</option>';
	echo '<option value="Administrativa">Administrativa</option>';
	echo '</select>';
	/*echo'</td>';*/
		/*echo'	<INPUT TYPE="text" class="ftexto" NAME="vacti_editar" title="Escribir la actividad que realizara" size="50" maxlength="100" value="'.$row[1].'">';*/

		/*CODIGO PARA CALENDARIO*/

	/*+++++VARIABLES PARA CALENDARIO++++++*/
	date_default_timezone_set('America/Lima');
	$dia11=date("d/m/Y");
	$dia22=$dia11;
	if (isset($_POST["dateboxx".$da])==true){
		$dia11=$_POST["dateboxx".$da];
	}
	if (isset($_POST["dateboxx2".$da])==true){
		$dia22=$_POST["dateboxx2".$da];
	}
	/*+++++++++++*/



	/*echo '<tr>';*/
	echo'<br>';
	echo'&nbsp;&nbsp; &nbsp; &nbsp; <font style="background-color: #F2F8FC" face="Verdana" size="1">Fecha Inicio:</font>';

		$datebox="dateboxx".$da;
		$datebox2="dateboxx2".$da;
		/*echo'++++++';
		echo $datebox;*/
		/*echo'Fecha Inicio:';
		echo $row[9];
		echo'<br>';
		echo'Fecha Fin:';
		echo $row[10];*/
		?>
         <!--++++++-->

      <input name="<? echo $datebox ?>" readonly="true" autocomplete="off" size="10" onClick="displayCalendar(<? echo $datebox ?>,'dd/mm/yyyy',this)" type="text" value=<? echo $row[9] ?> >
		<?
		 /*echo '</td>';*/
		/*echo '<td>';*/
		echo '&nbsp;&nbsp;&nbsp;';
	echo'<font style="background-color: #F2F8FC" face="Verdana" size="1">Fecha Final:</font>';
		?>
                <input name=<? echo $datebox2 ?> readonly="true" autocomplete="off" size="10" onClick="displayCalendar(<? echo $datebox2 ?>,'dd/mm/yyyy',this)" type="text" value=<? echo $row[10] ?> >
		<?

		/*++++*/
			/*echo'<td> <font style="background-color: #F2F8FC" face="Verdana" size="1">Fecha Inicio:</font>';
*/


			echo '&nbsp;&nbsp;&nbsp;';

			echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Porcentaje de avance</font>';
			echo '&nbsp;&nbsp;&nbsp;';

			/*echo'++++';
			echo$row[11];*/

			/*echo'<select size="1" name="vporcentaje_editar'.$da.'" title="Seleccionar la magnitud que representa lo establecido como porcentaje de avance">';


							echo '<option value="'.$row[11].'">'.$row[11].'</option>';
			for($porcentaje=0;$porcentaje<=100;$porcentaje++){
							echo '<option value="'.$porcentaje.'">'.$porcentaje.'</option>';
															}
			echo '</select>';*/

			echo'<INPUT TYPE="text" class="ftexto" NAME="vporcentaje_editar'.$da.'" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="2" maxlength="2" value="'.$row[11].'">';


		 echo '</td>';


	echo '</tr>';
	/*FIN DE CODIGO PARA CALENDARIO*/


	/*echo'</font></td>';*/
	echo'</tr>';

	echo'<tr>';

	echo '<td  bgcolor="'.$tcol.'"><font size="1">Detalle Actividad</font><td>';
		echo'</tr>';

	echo'<tr>';
	/*La variable $row[2] imprime el Detalle Actividad*/
			echo '<td><font size="1">';

			/*echo'<INPUT TYPE="text" class="ftexto" NAME="vdacti_editar'.$da.'" title="Escribir la actividad que realizara" size="160" maxlength="107" value="'.$row[2].'">';*/

		/*AUMENTO IF PARA DETERMINAR SI ES EDITABLE O NO EL Detalle Actividad */
		if ($estado>0)
		{
			echo'<INPUT TYPE="text" class="ftexto" NAME="vdacti_editar'.$da.'" title="Escribir la actividad que realizara" size="160" maxlength="107" value="'.$row[2].'">';
		}
		else
		{
			echo'<INPUT TYPE="text" class="ftexto" NAME="vdacti_editar'.$da.'" title="Escribir la actividad que realizara" size="160" readonly="readonly" maxlength="107" value="'.$row[2].'">';
		}

		/*CIERRA EL IF QUE  DETERMINAR SI ES EDITABLE O NO EL Detalle Actividad  */

			echo '</font></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td  bgcolor="'.$tcol.'"><font size="1">Importancia</font></td>';
	echo '</tr>';
	echo '<tr>';
	/*La variable $row[3] imprime la Importancia*/
	echo '<td colspan="1" width=""><font size="1">';

			/*echo'<INPUT TYPE="text" class="ftexto" NAME="vimporta_editar'.$da.'" title="Escribir la importancia de la actividad" size="160" maxlength="255" value="'.$row[3].'">';*/

		/*AUMENTO IF PARA DETERMINAR SI ES EDITABLE O NO LA Importancia*/
		if ($estado>0)
		{
			echo'<INPUT TYPE="text" class="ftexto" NAME="vimporta_editar'.$da.'" title="Escribir la importancia de la actividad" size="160" maxlength="255" value="'.$row[3].'">';
		}
		else
		{
			echo'<INPUT TYPE="text" class="ftexto" NAME="vimporta_editar'.$da.'" title="Escribir la importancia de la actividad" size="160" readonly="readonly" maxlength="255" value="'.$row[3].'">';
		}

		/*CIERRA EL IF QUE  DETERMINAR SI ES EDITABLE O NO LA Importancia  */

			echo'</font></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td  bgcolor="'.$tcol.'"><font size="1">Meta</font></td>';
	echo '</tr>';
	echo '<tr>';
	/*La variable $row[8] imprime la Meta*/
	echo '<td colspan="5"><font size="1">';

			/*echo'<INPUT TYPE="text" class="ftexto" NAME="vmeta_editar'.$da.'" title="Escribir la meta a alcanzar en el semestre" size="160" maxlength="255" value="'.$row[8].'">';*/

		/*AUMENTO IF PARA DETERMINAR SI ES EDITABLE O NO LA Meta*/
		if ($estado>0)
		{
			echo'<INPUT TYPE="text" class="ftexto" NAME="vmeta_editar'.$da.'" title="Escribir la meta a alcanzar en el semestre" size="160" maxlength="255" value="'.$row[8].'">';
		}
		else
		{
			echo'<INPUT TYPE="text" class="ftexto" NAME="vmeta_editar'.$da.'" title="Escribir la meta a alcanzar en el semestre" size="160" readonly="readonly" maxlength="255" value="'.$row[8].'">';
		}

		/*CIERRA EL IF QUE  DETERMINAR SI ES EDITABLE O NO LA Meta  */


			echo'</font></td>';
	echo '</tr>';

/*CAJA DE TEXTO PARA LAS OBSERVACIONES DEL FORMUALRIO EDITAR TRABAJO INDIVIDUAL*/
/*EDITADO 27/07/2011*/
	echo '<tr>';
		echo '<td  bgcolor="'.$tcol.'"><font size="1">Informes</font></td>';
	echo '</tr>';

	echo '<tr>';

	echo '<td colspan="1" width="">';
			/*echo'<font size="1">';*/
			/*echo '<INPUT TYPE="text" class="ftexto" NAME="vdocumento_editar'.$da.'" title="Escribir la observacion de la actividad" size="80" maxlength="255" ></font>';*/


			/*echo '<font size="1">
			<a onclick="javascript:window.open(\'detalle_actividad.php?sesion='.$sex.'&id='.$row[0].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false" href="detalle_actividad.php">DETALLE ACTIVIDAD</a>
			</font>';

			echo'++++';*/

			/*echo '<font size="1">
			<a onclick="javascript:window.open(\'detalle_doc_actividad.php?sesion='.$sex.'&id='.$row[0].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=550,top=40,left=50\');return false" href="detalle_doc_actividad.php">VER HISTORIAL DE DOCUMENTOS</a>
			</font>';*/

			/*echo'&nbsp;&nbsp;&nbsp;';*/
	if($file_php>0)
	{

			//LINK V1
			/*echo '<font size="1">
			<a onclick="javascript:window.open(\'print_informe_trab.php?sesion='.$sex.'&id='.$row[0].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=550,top=40,left=50\');return false" href="print_informe_trab.php">VER HISTORIAL DE INFORMES</a>
			</font>';*/

			//LINK V2
			/*echo '<font size="1">
			<a onclick="javascript:window.open(\'print_informe_trab.php?sesion='.$sex.'&id='.$row[0].'&file_php=1\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=550,top=40,left=50\');return false" href="print_informe_trab.php">VER HISTORIAL DE INFORMES</a>
			</font>';*/


			//LLAMA AL ARCHIVO PHP encripta_pdf.php QUE CONTIENE LAS FUNCIONES PARA ENCRIPTAR
			require_once('encripta_pdf.php');
			//LINK V3
			echo '<font size="1">
			<a onclick="javascript:window.open(\'print_informe_trab_edit.php?sesion='.$sex.'&id='.fn_encriptar($row[0]).'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=600,top=40,left=50\');return false" href="print_informe_trab_edit.php">VER HISTORIAL DE INFORMES</a>
			</font>';


	}
	else
	{
			//LINK V2
			/*echo '<font size="1">
			<a onclick="javascript:window.open(\'print_informe_trab.php?sesion='.$sex.'&id='.$row[0].'&file_php=0\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=550,top=40,left=50\');return false" href="print_informe_trab.php">VER HISTORIAL DE INFORMES</a>
			</font>';*/


			//LLAMA AL ARCHIVO PHP encripta_pdf.php QUE CONTIENE LAS FUNCIONES PARA ENCRIPTAR
			require_once('encripta_pdf.php');
			//LINK V3
			echo '<font size="1">
			<a onclick="javascript:window.open(\'print_informe_trab.php?sesion='.$sex.'&id='.fn_encriptar($row[0]).'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=550,top=40,left=50\');return false" href="print_informe_trab.php">VER HISTORIAL DE INFORMES</a>
			</font>';

	}
		// AGREGO LINK PARA VER EL CONSOLIDADO DE PDFs

		$cod = $_SESSION['codigox'];
		/*echo $cod;
		echo '++++++++';
		echo $codigo;
		echo'+++++';
		echo fn_encriptar($cod);*/
		/*echo  $row[11];*/ // LA VARIABLE  $row[11] ES EL PORCENTAJE DE LA ACTIVIDAD
		if ($row[11]>0)
		{

		echo '<font size="1">&nbsp; &nbsp; &nbsp; -  &nbsp; &nbsp; </font>';
		//LLAMA AL ARCHIVO PHP encripta_pdf.php QUE CONTIENE LAS FUNCIONES PARA ENCRIPTAR
		require_once('encripta_pdf.php');
		/*echo '<input type="hidden" name="coduni" value="'.fn_encriptar($cod).'">';*/
		/*echo '<input type="hidden" name="coduni" value="'.fn_encriptar($cod).'">';*/
		echo '<font size="1">';
			/*echo '<a href="imprimir_trab_consolidado.php?codigo='.fn_encriptar($cod).'" target="_blank">VER CONSOLIDADO DE ACTIVIDADES PDF</a>';*/
			echo '<a href="imprimir_trab_consolidado.php?codigo='.fn_encriptar($cod).'&codigo2='.fn_encriptar($codigo).'&x='.$sem.'" target="_blank">VER CONSOLIDADO DE ACTIVIDADES PDF</a>';
echo '</font>';
		}

		// AGREGO LINK PARA VER EL CONSOLIDADO DE PDFs
		echo '</td>';

	echo '</tr>';
	/*HASTA AQUI CAJA DE TEXTO*/
	/*RADIO BUTONS PARA CAMBIAR DE ESTADO*/
//	if($file_php>0)
//	{
//	echo '<tr>';
//		echo '<td  bgcolor="'.$tcol.'"><font size="1">Estado de la Actividad</font></td>';
//	echo '</tr>';
//
//	echo '<tr>';
//
//	echo '<td colspan="1" width="">
//			<font size="1">';
//	echo 'Habilitado
//	<input type="radio" NAME="vestado_editar'.$da.'"  value="1" size="35" >
//	 &nbsp;
//    Deshabilitado
//	<input type="radio" NAME="vestado_editar'.$da.'"  value="0" size="35" checked="checked">
//
//			</font>';
//	echo '</td>';
//
//	echo '</tr>';
//	}
	/*HASTA AQUI SE GENERA LOS RADIO BUTONS */
	echo '<tr>';
/*	echo '<td bgcolor="'.$tcol.'"><font size="1">Medida</font></td>';
	echo '<td bgcolor="'.$tcol.'"><font size="1">Cant</font></td>';
	echo '<td bgcolor="'.$tcol.'"><font size="1">Hrs.</font></td>';
	echo '<td bgcolor="'.$tcol.'"><font size="1">Calif.</font></td>';
	echo '<td bgcolor="'.$tcol.'"></td>';*/
	echo '<td bgcolor="'.$tcol.'">
	<font size="1">
	Medida 	  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	Cant
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	Hrs. Semanales
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;
	Calif.
	</font></td>';
	/*echo '<td bgcolor="'.$tcol.'"></td>';*/
	echo '</tr>';
	echo '<tr>';
	/*La variable $row[4] imprime la Medida*/
	echo '<td><font size="1">';

	echo '<select size="1" name="vmedida_editar'.$da.'" title="Seleccionar la magnitud que representa lo establecido como meta">';

   	echo '<option value="'.$row[4].'">'.$row[4].'</option>';
   	echo '<option value="Alumnos">Alumnos</option>';
	echo '<option value="Documento">Documento(s)</option>';
	echo '<option value="Permanente">Permanente</option>';
	echo '</select>';

		/*AUMENTO IF PARA DETERMINAR SI ES EDITABLE O NO LA Meta*/

		/*if ($estado>0)
		{

			echo '<select size="1" name="vmedida_editar'.$da.'" title="Seleccionar la magnitud que representa lo establecido como meta">';
			echo '<option value="'.$row[4].'">'.$row[4].'</option>';
			echo '<option value="Alumnos">Alumnos</option>';
			echo '<option value="Documento">Documento(s)</option>';
			echo '<option value="Permanente">Permanente</option>';
			echo '</select>';

		}
		else
		{

			echo $row[4];
		}*/

		/*CIERRA EL IF QUE  DETERMINAR SI ES EDITABLE O NO LA Meta  */


			/*echo'</td>';*/
			echo'</font>';
		/*echo'</td>';*/
	/*La variable $row[5] imprime la Cant*/
	/*echo '<td>';*/
			echo'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="1">';

			/*echo'<INPUT TYPE="text" class="ftexto" NAME="vcant_editar'.$da.'" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="2" maxlength="2" value="'.$row[5].'">';*/

		/*AUMENTO IF PARA DETERMINAR SI ES EDITABLE O NO Cant*/
		if ($estado>0)
		{

			echo'<INPUT TYPE="text" class="ftexto" NAME="vcant_editar'.$da.'" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="5" maxlength="5" value="'.$row[5].'">';

		}
		else
		{
			echo'<INPUT TYPE="text" class="ftexto" NAME="vcant_editar'.$da.'" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="5" readonly="readonly" maxlength="5" value="'.$row[5].'">';
		}

		/*CIERRA EL IF QUE  DETERMINAR SI ES EDITABLE O NO Cant  */


			echo'</font>';
		/*echo'</td>';*/
	/*La variable $row[6] imprime la Hrs*/
	/*echo '<td>';*/
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="1">';

			/*echo '<INPUT TYPE="text" class="ftexto" NAME="vhoras_editar'.$da.'" title="Escribir la cantidad de horas que demanda la actividad" size="2" maxlength="2" value="'.$row[6].'">';*/

		/*AUMENTO IF PARA DETERMINAR SI ES EDITABLE O NO Hrs*/
		if ($estado>0)
		{

			echo '<INPUT TYPE="text" class="ftexto" NAME="vhoras_editar'.$da.'" title="Escribir la cantidad de horas que demanda la actividad" size="2" maxlength="2" value="'.$row[6].'" onblur="if(document.getElementsByName(\'vcalif_editar'.$da.'\')[0].value == \'1\') checkLectiva(\'1\', \'vhoras_editar'.$da.'\', \''.$row[7].'\', \''.$row[6].'\')">';

		}
		else
		{
			echo '<INPUT TYPE="text" class="ftexto" NAME="vhoras_editar'.$da.'" title="Escribir la cantidad de horas que demanda la actividad" size="2" readonly="readonly" maxlength="2" value="'.$row[6].'">';
		}

		/*CIERRA EL IF QUE  DETERMINAR SI ES EDITABLE O NO Hrs  */

			echo '</font>';
		/*echo'</td>';*/
	/*La variable $row[7] imprime la Calif*/
	/*echo '<td>';*/
			//echo'&nbsp;&nbsp;&nbsp;<font size="1">';
			echo'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="1">';
			echo '<select size="1" name="vcalif_editar'.$da.'" title="Seleccionar el item relacionado con la actividad" onchange="checkLectiva(this.value, \'vhoras_editar'.$da.'\', \''.$row[7].'\', \''.$row[6].'\')">';

	switch ($row[7])
	{
	case 1:
     		$calificacion="Enseñanza";
			break;

	case 2:
     		$calificacion="Investigación";
			break;
	case 3:
     		$calificacion="Extensión Univ.";
			break;
	case 4:
			$calificacion="Proyección&nbsp; Social";
			break;

	case 5:
     		$calificacion="Producción";
			break;

	case 6:
			$calificacion="Bienestar Univ.";
			break;
	case 7:

			$calificacion="Administración";
			break;
	case 8:

			$calificacion="Gestión";
			break;
	case 9:

			$calificacion="Responsabilidad Social";
			break;

  	default:
     		$calificacion="Enseñanza";
	}

	echo '<option value="'.$row[7].'">'.$calificacion.'</option>';
   	echo '<option value="1">Enseñanza</option>';
	echo '<option value="2">Investigación</option>';
	echo '<option value="9">Responsabilidad Social</option>';
	echo '<option value="5">Producción</option>';
	echo '<option value="6">Bienestar Univ.</option>';
	echo '<option value="7">Administración</option>';
	echo '<option value="8">Gestión</option>';
	echo '</select>
			</font>';
		/*echo'</td>';*/
	if ($busca==0 or $busca==1){
		/*echo '<td><font size="1">+++++++++'.$da.'+++++++++</font></td>';*/
	/*La variable $da es el numero que se le asigna a cada tabla del Detalle de Carga No Lectivade*/
		/*echo '<td width="120" >';*/
		/*AGREGO ESTO PARA CAPTURAR EL IDSEM Y EL CODIGO*/

		$id_semestre=$row[12];
		$codigo_docente=$row[13];

		/*++++++*/
		/*AUMENTO IF PARA CREAR LOS BOTONES ELIMINAR Y EDITAR */
		/*$estado=$row[14];*/
		/*$estado = 1;*/
		/*if($dias_semetre>$dias_avance)*/
		if (($dias_semetre>$dias_avance)or($estado>0) )
		/*if($dias_semetre>0)*/
		{
		echo '&nbsp;&nbsp;&nbsp;';
		/*echo'<input class="btns" type="submit" name="de'.$da.'" value="Eliminar">';*/
		echo'<input class="btns" type="submit" name="de'.$da.'" value="Eliminar" onClick="return confirmdelete()">';
		echo'<input type="hidden" name="vi'.$da.'" value="'.$row[0].'">';
		echo '&nbsp;&nbsp;&nbsp;';
		/*echo'<input class="btns" type="submit" name="dedit'.$da.'" value="Editar"/>'*/
		/*echo'<input class="btns" type="submit" name="dedit'.$da.'" value="Editar"  onClick="return confirmSubmit()/>'*/;
				/*echo'<input class="btns" type="submit" name="dedit'.$da.'" value="Editar"  onClick="return confirmSubmit()" onClick="javascript:confirmSubmit()" />'*/;
				echo'<input class="btns" type="submit" name="dedit'.$da.'" value="Editar"  onClick="return confirmSubmit()"/>';
		}
		else
		{
		echo '&nbsp;&nbsp;&nbsp;';
		echo'<input class="btns" type="submit" disabled="disabled" name="de'.$da.'" value="Eliminar">';
		echo'<input type="hidden" name="vi'.$da.'" value="'.$row[0].'">';
		echo '&nbsp;&nbsp;&nbsp;';
		echo'<input class="btns" type="submit" disabled="disabled" name="dedit'.$da.'" value="Editar"/>';
		}
		//if($file_php>0)
//		{
//		echo'&nbsp;&nbsp;&nbsp;
//			<input class="btns" type="submit" name="modi'.$da.'" onClick="return confirmModificarEstado2()" value="Modificar Estado"/>';
//		}
		/*echo $estado;*/
		/*SI LA VARIABLE $file_php ES MAYOR A CERO MUESTRA EL BOTON Modificar Estado*/
		/*if($file_php>0)
		{*/
		/*echo'&nbsp;&nbsp;&nbsp;
			<input class="btns" type="submit" name="modi_estado'.$da.'" value="Modificar_Estado"/>';*/
		/*}*/
		/*CIERRA EL IF QUE GENERA LOS BOTONES ELIMINAR Y EDITAR*/
		echo '</td>';



		/*echo'<br>';
		echo $row[0];*/
		/* LA VARIABLE $row[0] ES = AL CAMPO idtrab DE LA TABLA trab */

		/*';*/
	/*  ++++++++ AGREGO BOTON EDITAR ++++++ */
	/*echo '<td>
			<input class="btns" type="submit" name="dedit'.$da.'" value="Editar"/>
		</td>';	*/
	/*++++++++*/

		/*echo '<td><font size="1">+++++++++'.$row[0].'+++++++++</font></td>';*/
		/*La variable $row[0] campo idtrab que se le asigna a cada tabla del Detalle de Carga No Lectivade*/
	}else{
		echo '<td width="120" ></td>';
	}
	echo '</tr>';
	echo '</table>';
	echo'<br>';
$bandera=0;
}
/*parece q */
//echo '<table border="0" width="100%"><tr><td width="150" colspan="2" align="right"><b><font size="1">Hrs.&nbsp;</font></b></td><td width="600" colspan="3"><b><font size="1">'.$hn.'</font></b></td></tr>';
if ($hn!=0) {echo '<table border="1" cellspacing="0" ><tr><td width="150" colspan="2" align="center"><b><font size="1">Total de Hrs No Lectivas.&nbsp;</font></b></td><td width="600" colspan="3"><b><font size="1">'.$hn.'</font></b></td></tr></table><br>';}
//echo '<tr><td width="150" colspan="2" align="right"><b><font size="2">Total Hrs.&nbsp;</font></b></td><td width="600" colspan="3"><b><font size="2">'.($hl+$hn).'</font></b></td></tr></table>';
/*echo'cierra la tabla de Detalle de Carga No Lectiva';*/
/*La suma de las variables ($hl+$hn) es = al Total Hrs.*/

/*DETERMINO SI EL TOTAL DE HORAS SOBREPASA LAS 40 HORAS Y MUESTRA MENSAJE*/

		// Obtener el total de horas laborales y la dedicación	
		//GABO
		// --- INICIO: NUEVAS VALIDACIONES ---
		$conn = conex();

		// 1. Obtener la dedicación y las horas laborales del docente
		$sql_resumen = "usp_Pit_ObtenerDatosResumen " . $codper . ", " . $sem;
		$result_resumen = luis($conn, $sql_resumen);
		$dedicacion = '';
		$horas_laborales = 0;
		while ($row = fetchrow($result_resumen, -1)) {
			$dedicacion = trim($row[3]); // Dedicación (DE, TC, TP)
			$horas_laborales = $row[4];  // Horas laborales (normalmente 40)
		}
		cierra($result_resumen);

		// 2. Obtener la dependencia del docente para verificar exención
		$sql_dependencia = "SELECT (SELECT descrip FROM depe WHERE depe.iddepe = individual.iddepe) AS dependencia FROM individual WHERE codper = " . $codper;
		$result_dependencia = luis($conn, $sql_dependencia);
		$dependencia_docente = '';
		while ($row_dep = fetchrow($result_dependencia, -1)) {
			$dependencia_docente = $row_dep[0];
		}
		cierra($result_dependencia);

		// 3. Calcular el total de horas (lectivas + no lectivas)
		$total_horas = $hl + $hn;
		if ($vah > 0) {
			$total_horas = $hl + ($hn - $hora_descuento);
		}

		// 4. Verificar si el docente está exento (Rector o Vicerrector)
		$es_exento = (
			stripos($dependencia_docente, 'rectorado') !== false ||
			stripos($dependencia_docente, 'vice rectorado') !== false
		);

		// 5. Aplicar las validaciones
		if ($es_exento) {
			// El docente está exento, no se aplican validaciones
			$mensaje = ' Exento de carga académica-administrativa';
		} else {
			// Aplicar validaciones según la dedicación
			if ($dedicacion === 'TP') {
				// Validación para Tiempo Parcial
				if ($total_horas >= 40) {
					$mensaje = ' No debe alcanzar o superar las 40 horas por ser tiempo parcial';
				} else {
					$mensaje = ' Cumple con el límite de menos de 40 horas por ser tiempo parcial';
				}
			} elseif ($dedicacion === 'DE') {
				// Validación para Dedicación Exclusiva
				if ($total_horas < 40) {
					$mensaje = ' No cumple con el mínimo de 40 horas requeridas para dedicación exclusiva';
				} else {
					$mensaje = ' Cumple con el mínimo de 40 horas requeridas para dedicación exclusiva';
				}
			} else {
				// Para Tiempo Completo (TC) y otros, usar la lógica original
				if ($total_horas > $horas_laborales) {
					$mensaje = ' No debe de sobrepasar las ' . $horas_laborales . ' horas';
				} else {
					if ($total_horas < $horas_laborales) {
						$mensaje = ' Debe de completar las ' . $horas_laborales . ' horas';
					} else {
						$mensaje = ' Cumple con las ' . $horas_laborales . ' horas';
					}
				}
			}
		}
// --- FIN: NUEVAS VALIDACIONES ---

		// Calcular el total de horas (lectivas + no lectivas)
		$total_horas = $hl + $hn;

		// Aplicar validaciones según la dedicación
		if ($dedicacion === 'DE') { // Dedicación Exclusiva
			if ($total_horas < 40) {
				$mensaje = ' No cumple con el mínimo de 40 horas requeridas para dedicación exclusiva';
			} else {
				$mensaje = ' Cumple con el mínimo de 40 horas requeridas para dedicación exclusiva';
			}
		} elseif ($dedicacion === 'TP') { // Tiempo Parcial
			if ($total_horas >= 40) {
				$mensaje = ' No debe alcanzar o superar las 40 horas por ser tiempo parcial';
			} else {
				$mensaje = ' Cumple con el límite de menos de 40 horas por ser tiempo parcial';
			}
		} else { // Para TC (Tiempo Completo) u otros, usar la lógica original basada en $horas_laborales
			if ($total_horas > $horas_laborales) {
				$mensaje = ' No debe de sobrepasar las ' . $horas_laborales . ' horas';
			} else {
				if ($total_horas < $horas_laborales) {
					$mensaje = ' Debe de completar las ' . $horas_laborales . ' horas';
				} else {
					$mensaje = ' Cumple con las ' . $horas_laborales . ' horas';
				}
			}
		}
	
		$limite_lectivas = $horas_laborales * 0.25;
		echo '<script type="text/javascript">';
		echo 'var sumaLectivas = ' . $suma_horas_lectivas . ';';
		echo 'var limiteLectivas = ' . $limite_lectivas . ';';
		echo 'var currentCalifAdd = "0";';
		echo 'function checkLectiva(value, horasName, currentCalif, currentHoras) {';
		echo '    if (value == "1") {';
		echo '        var horas = document.getElementsByName(horasName)[0].value || currentHoras;';
		echo '        var adjustedSuma = sumaLectivas;';
		echo '        if (currentCalif == "1") {';
		echo '            adjustedSuma -= parseInt(currentHoras);';
		echo '        }';
		echo '        if (parseInt(horas) + adjustedSuma > limiteLectivas) {';
		echo '            alert("La suma de horas de actividades lectivas no puede superar el 25% de las horas totales del docente (" + limiteLectivas + " horas).");';
		echo '            document.getElementsByName(horasName)[0].focus();';
		echo '        }';
		echo '    }';
		echo '}';
		echo '</script>';
	
	// if($hl+$hn>40)
		if($hl+$hn>$horas_laborales)
		{
			// $mensaje =' No debe de sobrepasar las 40 horas' ;
			$mensaje =' No debe de sobrepasar las '.$horas_laborales.' horas' ;
		}
		else
		{
			// if($hl+$hn<40)
			if($hl+$hn<$horas_laborales)
			{
				// $mensaje =' Debe de completar las 40 horas' ;
				$mensaje =' Debe de completar las '.$horas_laborales.' horas' ;
			}
			else
			{
				// $mensaje =' Cumple con las 40 horas' ;
				$mensaje =' Cumple con las '.$horas_laborales.' horas' ;
			}
		}
		$total_horas=$hl+$hn;
/*HASTA AQUI DETERMINO SI EL TOTAL DE HORAS SOBREPASA LAS 40 HORAS */
/* ESTAS TABLAS SON LA LEYENDE PARA CADA  CALIFICACION QUE SE LE ASIGANA AL Detalle de Carga No Lectiva*/
echo '<table><tr><td width="150" colspan="2" align="right"><b><font size="2">Total Hrs.&nbsp;</font></b></td><td width="600" colspan="3"><b><font size="2">'.($hl+$hn).' -'.$mensaje.'</font></b></td></tr></table>';
/*IMPRIME LA SUMA DE PORCENTAJES, EL NUMERO DE ACTIVIDADES, EL IDSEM Y CODIGO DEL DOCENTE*/
/*echo 'codigo_docente: ';
echo $codigo_docente;
echo'<br>';
echo 'id de semestre: ';
echo $id_semestre;
echo'<br>';
echo 'numero actividades ';
echo $da;
echo'<br>';
echo'suma % ++++';
echo $suma_porcentaje;
echo'++++';*/
/*PROCEDIMIENTO ALMACENADO PARA AGREGAR A LA TABLA DETALLE_TRAB*/
$sql_add_detalle_trab="exec sp_add_detalle_trab ".$codigo_docente.", ".$id_semestre.", '".$suma_porcentaje."','".$da."'";
// echo $sql_add_detalle_trab;
$result_detalle_trab=luis($conn, $sql_add_detalle_trab);
cierra($result_detalle_trab);
/*noconex($conn);*/
/*+++*/
echo '<br><table border="0" width="100%">';
echo '<tr><td colspan="7"><b><font size="1">CALIFICACION</font></b></td></tr>';
echo '<tr><td><font size="1">1=Enseñanza</font></td><td><font size="1">2=Investigación</font></td><td><font size="1">5=Producción</font></td><td><font size="1">6=Bienestar Univ.</font></td><td><font size="1">7=Administración</font></td><td><font size="1">8=Gestión</font></td><td><font size="1">9=Responsabilidad Social</font></td></tr>';
echo '</table>';
echo '<br><br>';
echo 'Descargar Guía de Usuario<a href="documentos/PIT_Docente/GUIA_PIT_DOCENTE_V2.pdf" target="_blank">
<span style="color: red;"><font size="1" face="Arial"><blink>( Haga clic aquí )</blink> </font></span></a>';

echo '<br><br>';
if ($busca==0 or $busca==1){
//echo '<FORM METHOD="POST" ACTION="carga.php?tr=1&sesion='.$sex.'" name="frmindiv">';
echo '<input type="hidden" name="vn" maxlength="7" value="'.$da.'">';
/*COLOCO LA SUMA DEL TOTAL DE HORAS PARA SER ENVIADO AL PROCEDIMIENTO ALMACENADO*/
echo '<input type="hidden" name="vcanthoras" maxlength="7" value="'.$total_horas.'">';
/*++++++*/
//echo '&nbsp;&nbsp;<font size="1">Descripcion</font>';
echo '<table border="0" cellspacing="2" bgcolor="#CCE6FF">';
	echo '<tr>';
	echo '<td width="120" colspan="2"><font size="1">Actividad</font></td>';
	/*echo '<td width="108" colspan="3"><font size="1">Detalle Actividad</font></td>';*/
	echo '</tr>';
	echo '<tr>';
	echo '<td width="120" colspan="2">';
	/*genera el combobox para las ACTIVIDADES*/
	echo '<select size="1" name="vacti">';
	$sql="select descrip from trabact";
	$resulta=luis($conn, $sql);
	while ($row=fetchrow($resulta,-1))
	{
		echo '<option value="'.$row[0].'">'.$row[0].'</option>';
	}
	cierra($resulta);
	echo '</select></td>';
	/*cierra el combobox de las ACTIVIDADES*/
	/*echo'</tr>';*/


		/*CODIGO PARA CALENDARIO*/

	/*+++++VARIABLES PARA CALENDARIO++++++*/
	date_default_timezone_set('America/Lima');
	$dia=date("d/m/Y");
	$dia2=$dia;
	if (isset($_POST["datebox"])==true){
		$dia=$_POST["datebox"];
	}
	if (isset($_POST["datebox2"])==true){
		$dia2=$_POST["datebox2"];
	}
	/*+++++++++++*/

    /*
    * INICIO DE LA MODIFICACIÓN:
    * Se ocultan los campos de fecha si la variable $sin_semestre es igual a 1.
    */
    if ($sin_semestre != 1) {
        echo '<td> <font style="background-color: #F2F8FC" face="Verdana" size="1">Fecha Inicio:</font>';
        ?>
            <input name="datebox" readonly="true" autocomplete="off" size="10" onClick="displayCalendar(datebox,'dd/mm/yyyy',this)" type="text" value=<?php echo $dia; ?>>
        <?php
        echo '&nbsp;&nbsp;&nbsp;';
        echo '<font style="background-color: #F2F8FC" face="Verdana" size="1">Fecha Final:</font>';
        ?>
            <input name="datebox2" readonly="true" autocomplete="off" size="10" onClick="displayCalendar(datebox2,'dd/mm/yyyy',this)" type="text" value=<?php echo $dia2; ?> >
        <?php
        echo '</td>';
    }
    /*
    * FIN DE LA MODIFICACIÓN
    */

	echo '</tr>';
	/*FIN DE CODIGO PARA CALENDARIO*/


	echo'<tr>';
	echo '<td width="108" colspan="3"><font size="1">Detalle Actividad</font></td>';
	echo'</tr>';

	echo'<tr>';
	/*CAJA DE TEXTO PARA QUE CAPTURA EL Detalle Actividad*/
	echo '<td width="108" colspan="3"><INPUT TYPE="text" class="ftexto" NAME="vdacti" title="Escribir la actividad que realizara" size="169" maxlength="100"></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td colspan="5"><font size="1">Importancia</font></td>';
	echo '</tr>';
	echo '<tr>';
	/*CAJA DE TEXTO QUE CAPTURA LA Importancia*/
	echo '<td colspan="5"><font size="1"><INPUT TYPE="text" class="ftexto" NAME="vimporta" title="Escribir la importancia de la actividad" size="169" maxlength="255"></font></td>';
	echo '</tr>';
	echo '<td colspan="5"><font size="1">Meta</font></td>';
	echo '</tr>';
	echo '<tr>';
	/*CAJA DE TEXTO QUE CAPTURA LA Meta*/
	echo '<td colspan="5"><font size="1"><INPUT TYPE="text" class="ftexto" NAME="vmeta" title="Escribir la meta a alcanzar en el semestre" size="169" maxlength="255"></font></td>';
	echo '</tr>';

/*COMBOBOX PARA LA DEPENDENCIA*/
	/*echo '</tr>';
	echo '<td colspan="5"><font size="1">Dependecia</font></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td width="120" colspan="2">';*/
	/*genera el combobox para las ACTIVIDADES*/
/*	echo '<select size="1" name="viddepe">';
	$sql="select * from depe where estado <1 and tipo <1 and iddepe<>303016000 order by descrip";
	$resulta=luis($conn, $sql);
	while ($row=fetchrow($resulta,-1))
	{

		$iddepe=$row[0];
		echo '<option value="'.$iddepe.'">'.$row[1].'</option>';
	}
	cierra($resulta);
	echo '</select></td>';
	echo '</tr>';*/
/*+++++++++++*/

	echo '<tr>';
	echo '<td ><font size="1">Medida</font></td>';
	echo '<td ><font size="1">Cant</font></td>';
	echo '<td >&nbsp;&nbsp;&nbsp;&nbsp;<font size="1">Hrs. Semanales &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Calif.</font></td>';
	/*echo '<td ><font size="1">Calif.</font></td>';*/
	/*echo '<td ></td>';*/
	echo '</tr>';
	echo '<tr>';
	echo '<td >';

	/*genera el combo box de MEDIDA*/
	echo '<select size="1" name="vmedida" title="Seleccionar la magnitud que representa lo establecido como meta">';
   	echo '<option value="Alumnos">Alumnos</option>';
	echo '<option value="Documento">Documento(s)</option>';
	echo '<option value="Permanente">Permanente</option>';
	echo '</select></td>';
	/*termina de generar el combo box de MEDIDA*/
	/*ESTA CAJA DE TEXTO CAPTURA LA CANTIDAD DE ALUMNOS , DOCENTES o PERMANTE */
	echo '<td ><font size="1"><INPUT TYPE="text" class="ftexto" NAME="vcant" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="5" maxlength="5"></font></td>';
	/*ESTA CAJA DE TEXTO CAPTURAS LAS HORAS DESTINADAS A LA MEDIDA */
	echo '<td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="1"><INPUT TYPE="text" class="ftexto" NAME="vhoras" title="Escribir la cantidad de horas que demanda la actividad" size="2" maxlength="2"></font>';
	/*echo '</td >';*/

	/*echo '<td >';*/
	echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	echo '<font size="1">';
	echo '<select size="1" name="vcalif" title="Seleccionar el item relacionado con la actividad" onchange="checkLectiva(this.value, \'vhoras\', \'0\', \'0\')">';
   	echo '<option value="1">Enseñanza</option>';
	echo '<option value="2">Investigación</option>';
	echo '<option value="9">Responsabilidad Social</option>';
	echo '<option value="5">Producción</option>';
	echo '<option value="6">Bienestar Univ.</option>';
	echo '<option value="7">Administración</option>';
	echo '<option value="8">Gestión</option>';
	echo '</select>';
	/*echo '</td>';*/


	/*echo'</tr>';
	echo'<tr>';*/

	//echo '<td width="120" ><input class="btns" type="submit" name="vagregar" value="Agregar"/></td>';
	echo '<input type="hidden" name="vagregar">';
	/*GENERA EL BOTON AGREGAR ,  EL CUAL INSERTA LOS VALORES DIGITADOS EN LAS CAJAS DE TEXTO Y ENCONTRADOS EN EL COMBO BOX*/
	/*echo'<td width="50" >';*/
	echo'&nbsp;&nbsp;&nbsp;&nbsp;';

	/*++++++++++*/
	//CAPTURO EL IDDEPE
	/*$sql="select left(Iddepe,3)  from individual where codper =".$codper;*/
	$sql="select Iddepe  from individual where codper =".$codper;
	$result_iddepe=luis($conn, $sql);
	while ($row=fetchrow($result_iddepe,-1))
	{
		$iddepe=$row[0];
	}
	cierra($result_iddepe);
	/*++++++++++*/
	/*echo'++++';
	echo $iddepe;
	echo'++++';
	echo'++++';
	echo $codper;
	echo'++++';*/

	echo '<input type="hidden" name="coduni" value="'.$codigo.'">';
	echo '<input type="hidden" name="viddepe" value="'.$iddepe.'">';
		echo'<input class="btns" type=button onClick="javascript:msj()" value="Agregar"/>';
	echo'</td>';
	//echo '<td width="120" ><input class="btns" type="button" onclick="javascript:document.frmindiv.submit();" name="vagregar" value="Agregar"/></td>';
	echo '</tr>';
echo '</table>';
echo '</form>';
}
	}
noconex($conn);
}
/* hasta aqui la funcion individualPIT*/
?>