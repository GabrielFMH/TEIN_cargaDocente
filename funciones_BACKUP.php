<?php 
function iptodec($ip)
{
	list($octet1, $octet2, $octet3, $octet4) = explode (".", $ip, 4);
	if ($ip =="" ) 
	{
		$dec=""; $hex="";
	}
	else 
	{
		$dec = ((((($octet1 * 256 + $octet2) * 256) + $octet3) * 256) + $octet4);
		//$hex = dechex($dec);
	}
	return($dec);
}

function dectoip($dec)
{
   $b=array(0,0,0,0);
   $c = 16777216.0;
   $dec += 0.0;
   for ($i = 0; $i < 4; $i++) 
   {
       $k = (int) ($dec / $c);
       $dec -= $c * $k;
       $b[$i]= $k;
       $c /=256.0;
   }
   $d=join('.', $b);
   return($d);
}
function resulta($result,$n,$f)
{
	require ("config.php");
	if ($dbx==0)
	{
		$resul = pg_fetch_result($result, $n, $f);
	}
	if ($dbx==1)
	{
		$resul = @mssql_result($result, $n, $f);
	}
	if ($dbx==2)
	{
		$resul = @mysql_result($result, $n, $f);
	}
	return $resul;
}
function fnum($a)
{
	$n=0;
	$h=0;
	if (strlen($a)<11)
	{
		While (strlen($a)>$h)
		{	
			if (strstr("0123456789", substr($a,$h,1)))
			{
				$n=1; 
			}
			else
			{
				$n=0;
				break;
			}
			$h++;
		}
	}
	return $n;
}
function fetchrow($result,$n)
{
	require ("config.php");
	if ($dbx==0)
	{
		$row = pg_fetch_row($result, $n);
	}
	if ($dbx==1)
	{
		if ($n==-1)
		{
			$row = @mssql_fetch_row($result);
		}
		else
		{
			$row = @mssql_data_seek ( $result,$n );
		}
		
	}
	if ($dbx==2)
	{
		$row = mysql_fetch_row($result);
	}
	return $row;
}	
function timepad()
{
	require ("config.php");
	return $padtime;
}
function idpad()
{
	srand(time());
	$ini="";
	$a="0123456789";
	$h=10;
	$n=0;
	While ($h>0)
	{	
		$b=$a;
		$x=substr($a, (rand()%(strlen($a))),1);
		$a=str_replace($x, "", $a);
		$k[$n]=$x;
		$n=$n+1;
		$h=$h-1;
	}
	return $k;
}
function sepad()	
{
	$a="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	$id="";
	for($i=0; $i<=30; $i++)
	{
		$id.=substr($a, (rand()%(strlen($a))),1);
	}
	//return(md5(uniqid($id,1)));
	return $id;
}
function pageheader()
{
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");	
	
}
function pageheadercache()
{
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: public");
}
function phpswf()
{
	session_cache_limiter('public_no_cache');
}
function timeup($stiempo)
{
	require ("config.php");
	if ( ($stiempo+$tiempo)>time() )
	{
		$ntiempo=time();
	}
	else
	{
		$ntiempo=0;
	}
	return $ntiempo;
}
function cierra($resultx)
{
	require ("config.php");
	if ($dbx==0)
	{
		pg_FreeResult($resultx);
	}
	if ($dbx==1)
	{
		@mssql_free_result($resultx);
	}
	if ($dbx==2)
	{
		mysql_free_result($resultx);
	}
}	
function noconex($conn)
{
	require ("config.php");
	if ($dbx==0)
	{
		pg_Close($conn);
	}
	if ($dbx==1)
	{
		@mssql_close($conn);
	}
	if ($dbx==2)
	{
		@mysql_close($conn);
	}
}
function conex()
{
	require ("config.php");
	if ($dbx==0)
	{
		$conn = pg_connect("host=".$host." port=".$port." user=".$usuario." password=".$pass." dbname=".$data );
	}
	if ($dbx==1)
	{
		$conn = @mssql_connect($host, $usuario, $pass); 
		@mssql_select_db($data, $conn); 
		//mssql_query('set language spanish'); 
		mssql_query('set dateformat dmy'); 
		mssql_query('set datefirst 1');
		mssql_query("SET ANSI_NULLS ON");
		mssql_query("SET ANSI_WARNINGS ON");		
		
	}
	if ($dbx==2)
	{
		$conn = @mysql_connect($host, $usuario, $pass); 
		@mysql_select_db($data, $conn); 
	}
	return $conn;
}
function luis($conn,$sql)
{
	require ("config.php");
	if ($dbx==0)
	{
		$result = pg_query($conn,$sql);		//Modificacion a pg_query antes estaba pg_exec //Gary
	}
	if ($dbx==1)
	{
		$result = @mssql_query($sql,$conn); 
	}
	if ($dbx==2)
	{
		$result = @mysql_query($sql) or die ("Invalid query"); 
	}
	return $result;
}


function conexMa()
{
	require ("config_fichamatricula.php");
	if ($dbx==0)
	{
		$conn = pg_connect("host=".$host." port=".$port." user=".$usuario." password=".$pass." dbname=".$data );
	}
	if ($dbx==1)
	{
		$conn = @mssql_connect($host, $usuario, $pass); 
		@mssql_select_db($data, $conn); 
	}
	if ($dbx==2)
	{
		$conn = @mysql_connect($host, $usuario, $pass); 
		@mysql_select_db($data, $conn); 
	}
	return $conn;
}


function dbdbmatrix($conn,$sql)     //       --------- conexion
{
	require ("config_fichamatricula.php");
	if ($dbx==0)
	{
		$result = pg_query($conn,$sql); 	//Modificacion a pg_query antes estaba pg_exec 	//Gary
	}
	if ($dbx==1)
	{
		$result = @mssql_query($sql,$conn); 
	}
	if ($dbx==2)
	{
		$result = @mysql_query($sql) or die ("Invalid query"); 
	}
	return $result;
}
function regse($sex,$pass,$result)
{
	require ("config.php");
	session_save_path($rutase);
	session_name($sex);
	
	//session_cache_limiter('public');
	//$cache_limiter = session_cache_limiter();
	//session_cache_expire(1);
	
	session_start();
	if ($dbx==0)
	{
		$row = pg_fetch_row($result);
	}
	if ($dbx==1)
	{
		$row = @mssql_fetch_row($result);
	}
	if ($dbx==2)
	{
		$row = mysql_fetch_row($result);
	}
	$_SESSION['contrasepa']=$_POST['P1'];
	$_SESSION['usuario']=$row[1];
	$_SESSION['iduped']=$row[1];
	$_SESSION['iddepe']=$row[2];
	$_SESSION['descrip']=$row[3];
	$_SESSION['nombre']=$row[4];
	$_SESSION['cargo']=$row[5];
	$_SESSION['cargo']="KYLIE";
	
}
function callse($sex)
{
	require ("config.php");
	session_save_path($rutase);

}
function nse()
{
	srand(time());
	$a="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  	for($i; $i<=30; $i++)
    	{
     		$id.=substr($a, (rand()%(strlen($a))),1);
    	}
	return $id;
}
function numrow($resultx)
{
	require ("config.php");
	$numr=0;
	if ($dbx==0)
	{
		$numr=pg_numrows($resultx);
	}
	if ($dbx==1)
	{
		$numr=@mssql_num_rows($resultx);
	}
	if ($dbx==2)
	{
		$numr=@mysql_num_rows($resultx);
	}
	return $numr;
}
function qwhere($wr,$a)
{
	$h=0;
	$k=0;
	$uh=1;
	While (strlen($a)>$h)
	{	
		if ($uh==1) 
		{
			$k=$k+1;
			$b[$k]="";			
		}	
		if (substr($a,$h,1)==" ") 
		{
			$uh=$uh+1;
		}
		else
		{
			
			$b[$k]=$b[$k].substr($a,$h,1);
			$uh=0;
		}	
		$h=$h+1;
	}
	$qx[1]=$k;
	For ($i=1;$i<=$k;$i++)
	{
		$b[$i]=trim(str_replace("+", " ", $b[$i]));
		$qx[$i+1]=$b[$i];
	}
	$qw="";
	For ($i=1;$i<=$k;$i++)
	{
		$qw=$qw." ".$wr." like '%$b[$i]%' ";
		if ($i!=$k)
		{
			$qw=$qw."and"; 	
		}
		else
		{
			$qw=$qw." ";
		}
	
	}
	$qx[0]=$qw;
	return $qx;
}
function npag($nkt,$cn,$z)
{
	For ($j=1;$j<=12;$j++)
	{
		$pg[($j-1)]=0;
		//echo '['.$j.'|'.$pg[($j-1)].']';
	}
	//$z=2;	
	if($nkt>(10*$z))
	{
		$nk=$nkt;
		if($nk>990)
		{
			$nk=990;
		}	
	
		if(bcmod ($nk,(10*$z))==0)
		{
			$tot=$nk;
		}
		else
		{
			$tot=(bcdiv($nk,(10*$z))*(10*$z))+(10*$z);
		}
		//echo '[tot='.((bcdiv($nk,20)*20)+20).']';
		
		$ct=$cn+(100*$z);
		$cx=$cn;
		$fin=0;
		$cm=0;
		$cy=0;
		$cz=0;
		$i=0;
		$kylie=0;
		//$lim=0;
		if ($tot>=$ct)
		{
			$lim=$ct;
			//echo '[lim='.$lim.']';
		}
		else
		{
			$lim=$tot+(10*$z);
			$fin=1;
			if ($cn>($lim-(100*$z)) and $tot>(100*$z))
			{
				$cx=$lim-(100*$z);
			}
			if ($tot<=(100*$z))
			{
				$cx=(10*$z);
			}	
			//echo '[cx='.$cx.']';
		}
		$cm=$cx;
		if ($cn>(10*$z))
		{
			$pg[0]=($cn-(10*$z));
		}
		//echo '[pg0='.$pg[0].']';
		while ($cx<$lim)
		{	
			if ($cx>(50*$z))
			{
				if ($fin==0)
				{
					$cz=($cx-(50*$z));
				}
				else
				{
					if ($cx<($lim-(50*$z)))
					{
						$cz=($cx-(50*$z));
					}
					else
					{
						if ($tot<=(100*$z))
						{
							$cz=(10*$z);
						}
						else
						{
							$cz=$lim-(100*$z);
						}
					}
				}
			}
			else
			{
				$cz=(10*$z);
			}
			if ($cx==$cn)
			{
				$cy=$cz;
			}
			//echo '[cz='.$cz.']';
			$cx=$cx+(10*$z);
		}
		$ig=0;
		while ($cy<$lim)
		{
			if ($i<10)
			{
				if ($cy==$cn)
				{
					$ig=$ig+1;
					$pg[$ig]=$cy;
					$kylie=$cy;
				}
				else
				{
					$ig=$ig+1;
					$pg[$ig]=$cy;
				}
			}
			else
			{
				break;
			}
			//echo '[cy='.$cy.']';
			$cy=$cy+(10*$z); 
			$i=$i+1;
		}
		if ($kylie<$tot)
		{
			$pg[11]=($kylie+(10*$z));
		}
	}
	return $pg;
}
function pagina($a,$nro,$cn, $refpag, $z)
{         
	$ax=str_replace( "+", "%2B", $a );
	$pg=npag($nro,$cn,$z);
	echo ' <center> ';
	For ($j=1;$j<=12;$j++)
	{
		if (($pg[($j-1)])>0)
		{
			if ($j==1)
			{
				echo '<a href="'.$refpag.'&q='.$ax.'&p='.$pg[($j-1)].'" class="tex-vin">Anterior</a>&nbsp;';
			}
			if ($j>1 and $j<12)
			{
				if ($pg[($j-1)]==$cn)
				{
					echo (bcdiv($pg[($j-1)],(10*$z))*1).'&nbsp;'; 
				}
				else
				{
					echo '<a href="'.$refpag.'&q='.$ax.'&p='.$pg[($j-1)].'" class="tex-vin">'.(bcdiv($pg[($j-1)],(10*$z))*1).'</a>&nbsp;'; 
				}
			}
			if ($j==12)
			{
				echo '<a href="'.$refpag.'&q='.$ax.'&p='.$pg[($j-1)].'" class="tex-vin">Siguiente</a>&nbsp;';
			}
		}
	
	}
	echo '</center>';	
}


function qjoin($join)
{
	$h=0;
	$k=0;
	$uh=1;
	$a=str_replace(","," ",$join);
	While (strlen($a)>$h)
	{	
		if ($uh==1) 
		{
			$k=$k+1;
			$b[$k]="";			
		}	
		if (substr($a,$h,1)==" ") 
		{
			$uh=$uh+1;
		}
		else
		{
			
			$b[$k]=$b[$k].substr($a,$h,1);
			$uh=0;
		}	
		$h=$h+1;
	}
	$qx[1]=$k;
	
	$qw="";
	For ($i=1;$i<=$k;$i++)
	{
		$qw= $qw." ke.$b[$i]=ki.$b[$i] ";
		if ($i!=$k)
		{
			$qw=$qw."and"; 	
		}
		else
		{
			$qw=$qw." ";
		}
	
	}
	$qx[0]=$qw;
	return $qx;
}
function qki($join)
{
	$h=0;
	$k=0;
	$uh=1;
	$a=str_replace(","," ",$join);
	While (strlen($a)>$h)
	{	
		if ($uh==1) 
		{
			$k=$k+1;
			$b[$k]="";			
		}	
		if (substr($a,$h,1)==" ") 
		{
			$uh=$uh+1;
		}
		else
		{
			
			$b[$k]=$b[$k].substr($a,$h,1);
			$uh=0;
		}	
		$h=$h+1;
	}
	$qx[1]=$k;
	
	$qw="";
	For ($i=1;$i<=$k;$i++)
	{
		$qw= $qw." ki.$b[$i] is null ";
		if ($i!=$k)
		{
			$qw=$qw."and"; 	
		}
		else
		{
			$qw=$qw." ";
		}
	
	}
	$qx[0]=$qw;
	return $qx;
}
function qselect($join)
{
	$h=0;
	$k=0;
	$uh=1;
	$a=trim($join);
	While (strlen($a)>$h)
	{	
		if ($uh==1) 
		{
			$k=$k+1;
			$b[$k]="";			
		}	
		if (substr($a,$h,1)==" ") 
		{
			$uh=$uh+1;
		}
		else
		{
			
			$b[$k]=$b[$k].substr($a,$h,1);
			$uh=0;
		}	
		$h=$h+1;
	}
	$qx[1]=$k;
	
	$qw="";
	For ($i=1;$i<=$k;$i++)
	{
		$qw= $qw." ke.$b[$i] ";
		if ($i!=$k)
		{
			$qw=$qw." "; 	
		}
		else
		{
			$qw=$qw." ";
		}
	
	}
	$qx[0]=$qw;
	return $qx;
}

function txtbusca($xselect,$xfielda,$xcondi,$xbusca,$xfieldb,$xorder,$cn,$z)
{
$xwherea=qwhere($xcondi,$xbusca);
$xconsult=$xselect." and ".$xwherea[0];
$xwhereb=qjoin($xfieldb);
$xfieldc=qselect($xfielda);
$xfieldd=qki($xfieldb);
$q[0]="select ".$xfieldc[0]." from (select top ".$cn." ".$xfielda." from ( ".$xconsult." ) as ka order by ".$xorder." ) as ke left join ( select top ".($cn-(10*$z))." ".$xfieldb." from ( ".$xconsult." ) as ko order by ".$xorder." )as ki on ".$xwhereb[0]." where ".$xfieldd[0]; 
$q[1]="select count(0) from ( ".$xconsult." ) as lo ";
return $q;
}
function txtlista($xconsult,$xfielda,$xfieldb,$xorder,$cn,$z)
{
$xwhereb=qjoin($xfieldb);
$xfieldc=qselect($xfielda);
$xfieldd=qki($xfieldb);
$q[0]="select ".$xfieldc[0]." from (select top ".$cn." ".$xfielda." from ( ".$xconsult." ) as ka order by ".$xorder." ) as ke left join ( select top ".($cn-(10*$z))." ".$xfieldb." from ( ".$xconsult." ) as ko order by ".$xorder." )as ki on ".$xwhereb[0]." where ".$xfieldd[0]; 
$q[1]="select count(0) from ( ".$xconsult." ) as lo ";
return $q;
}	

function helpx($sl,$sex)
{
	echo ('<link href="fontawesome-all.css" type="text/css" rel="stylesheet">');
  echo '<div id="root-row">';
		echo '<table border="0" width="100%" cellspacing="0" cellpadding="0" style="padding-top:2px;">';
			echo '<tr><td ><font style="font-size:19px;" color="white">&nbsp;&nbsp;Net.UPT.edu.pe</font></td>';
			if (isset($_SESSION['name'])){ 
				echo '<td><font color="white" style="font-size:12px;"><strong>Usuario:</strong> '.$_SESSION['name'].'</font></td>';
				//echo '<td><div id="crumbsx"><a href="javascript:void(window.open('http://'.$_SERVER['HTTP_HOST'].'/notas/help.htm?op='.$sl.'','otra','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=750,height=550,top=40,left=50'));">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a></div></td></tr></table>';
				/* ACTUAL 2012 , SE COMENTO DEBIDO A QUE LA AYUDA NO APERECIA EN EL INTERNET EXPLORER 
				EN OTROS NAVEGADORES NO HABIA PROBLEMA, EN LA LINEA DE ABAJO SE QUITO http:'.$_SERVER['HTTP_HOST'].'/help*/
				//echo '<td><div id="crumbsx"><a href="help.php" onclick="javascript:window.open(\'http:'.$_SERVER['HTTP_HOST'].'/help.php?op='.$sl.'','otra','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;';	
				/* ESTE ES EL LINK DE AYUDA ACTUAL, SE COPIARON TODOS LOS ARCHIVOS DE AYUDA A LA CARPETA secure*/
				//echo '<td><div id="crumbsx"><a href="help.php" onclick="javascript:window.open(\'help.php?op='.$sl.'','otra','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;';
				echo '<td><div id="crumbsx" style="font-size:15px;"><a href="#">Ayuda <i class="fas fa-question-circle"></i></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <i class="fas fa-arrow-alt-circle-right"></i></a>&nbsp;&nbsp;&nbsp;';
				//reloj
				include "reloj_crom.php";
				echo '</div></td>';
			}
		echo '</tr></table>';

	echo '</div>';
}

// function helpx_anterior($sl,$sex)
// {
// 	echo '<div id="root-row">';
// 		echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
// 			echo '<tr><td ><font size="4" color="white">&nbsp;&nbsp;Net.UPT.edu.pe</font></td>';
// 			if (isset($_SESSION['name'])){ 
// 				echo '<td><font color="white" size="1">Usuario: '.$_SESSION['name'].'</font></td>';
// 				//echo '<td><div id="crumbsx"><a href="javascript:void(window.open('http://'.$_SERVER['HTTP_HOST'].'/notas/help.htm?op='.$sl.'','otra','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=750,height=550,top=40,left=50'));">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a></div></td></tr></table>';
// 				/* ACTUAL 2012 , SE COMENTO DEBIDO A QUE LA AYUDA NO APERECIA EN EL INTERNET EXPLORER 
// 				EN OTROS NAVEGADORES NO HABIA PROBLEMA, EN LA LINEA DE ABAJO SE QUITO http:'.$_SERVER['HTTP_HOST'].'/help*/
// 				//echo '<td><div id="crumbsx"><a href="help.php" onclick="javascript:window.open(\'http:'.$_SERVER['HTTP_HOST'].'/help.php?op='.$sl.'','otra','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;';	
// 				/* ESTE ES EL LINK DE AYUDA ACTUAL, SE COPIARON TODOS LOS ARCHIVOS DE AYUDA A LA CARPETA secure*/
// 				//echo '<td><div id="crumbsx"><a href="help.php" onclick="javascript:window.open(\'help.php?op='.$sl.'','otra','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;';
// 				echo '<td><div id="crumbsx"><a href="#">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;';
// 				//reloj
// 				include "reloj_crom.php";
// 				echo '</div></td>';
// 			}
// 		echo '</tr></table>';

// 	echo '</div>';
// //   echo '<div id="root-row">';
// //        echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
// //       	echo '<tr><td ><font size="4" color="white">&nbsp;&nbsp;Net.UPT.edu.pe</font></td>';
// // 	echo '<td><font color="white" size="1">Usuario: '.$_SESSION['name'].'</font></td>';
// // 	//echo '<td><div id="crumbsx"><a href="javascript:void(window.open('http://'.$_SERVER['HTTP_HOST'].'/notas/help.htm?op='.$sl.'','otra','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=750,height=550,top=40,left=50'));">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a></div></td></tr></table>';
// // 	/* ACTUAL 2012 , SE COMENTO DEBIDO A QUE LA AYUDA NO APERECIA EN EL INTERNET EXPLORER 
// // 	EN OTROS NAVEGADORES NO HABIA PROBLEMA, EN LA LINEA DE ABAJO SE QUITO http:'.$_SERVER['HTTP_HOST'].'/help*/
// // 	//echo '<td><div id="crumbsx"><a href="help.php" onclick="javascript:window.open(\'http:'.$_SERVER['HTTP_HOST'].'/help.php?op='.$sl.'','otra','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;';	
// // 	/* ESTE ES EL LINK DE AYUDA ACTUAL, SE COPIARON TODOS LOS ARCHIVOS DE AYUDA A LA CARPETA secure*/
// // 	//echo '<td><div id="crumbsx"><a href="help.php" onclick="javascript:window.open(\'help.php?op='.$sl.'','otra','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=nos,width=850,height=650,top=40,left=50\');return false">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;';
// // 	echo '<td><div id="crumbsx"><a href="#">Ayuda <img border="0" src="imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="imagenes/salir.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;';
// // 	//reloj
// // include "reloj_crom.php";
// // 	echo '</div></td></tr></table>';

// //    echo '</div>';
// }


function helpx2($sl,$sex)
{
  echo '<div id="root-row">';
       echo '<table border="0" width="100%" cellspacing="0" cellpadding="0">';
      	echo '<tr><td ><font size="4" color="white">&nbsp;&nbsp;Net.UPT.edu.pe</font></td>';
	echo '<td><font color="white" size="1">Usuario: '.$_SESSION['name'].'</font></td>';
	
	echo '<td><div id="crumbsx"><a href="#" >Ayuda <img border="0" src="../imagenes/help.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;<a href="logout.php?sesion='.$sex.'" >Finalizar <img border="0" src="../imagenes/salir.gif" width="18" height="18"></a>&nbsp;&nbsp;&nbsp;';
	//reloj
include "reloj_crom.php";
	echo '</div></td></tr></table>';

   echo '</div>';
}

/*POSTGRADO 26-08-2019*/
function helpx3($sl,$sex)
{
	echo ('<link href="fontawesome-all.css" type="text/css" rel="stylesheet">');
  echo '<div id="root-row">';
		echo '<table border="0" width="100%" cellspacing="0" cellpadding="0" style="padding-top:2px;">';
			echo '<tr><td ><font style="font-size:19px;" color="white">&nbsp;&nbsp;Net.UPT.edu.pe</font></td>';
			if (isset($_SESSION['name'])){ 
				echo '<td><font color="white" style="font-size:12px;"><strong>Usuario:</strong> '.$_SESSION['name'].'</font></td>';
				echo '<td><div id="crumbsx" style="font-size:15px;"><a href="#">Ayuda <i class="fas fa-question-circle"></i></a>&nbsp;&nbsp;&nbsp;<a href="../logout.php?sesion='.$sex.'" >Finalizar <i class="fas fa-arrow-alt-circle-right"></i></a>&nbsp;&nbsp;&nbsp;';
				include "reloj_crom.php";
				echo '</div></td>';
			}
		echo '</tr></table>';

	echo '</div>';
}
/**/

function fecha()
{
putenv('TZ=America/Lima');

$mes[0]="-";
$mes[1]="enero";
$mes[2]="febrero";
$mes[3]="marzo";
$mes[4]="abril";
$mes[5]="mayo";
$mes[6]="junio";
$mes[7]="julio";
$mes[8]="agosto";
$mes[9]="septiembre";
$mes[10]="octubre";
$mes[11]="noviembre";
$mes[12]="diciembre";

/* Definición de los días de la semana */

$dia[0]="Domingo";
$dia[1]="Lunes";
$dia[2]="Martes";
$dia[3]="Miércoles";
$dia[4]="Jueves";
$dia[5]="Viernes";
$dia[6]="Sábado";

/* Implementación de las variables que calculan la fecha */

$gisett=(int)date("w");
$mesnum=(int)date("m");

/* Variable que calcula la hora
*/

$hora = date(" H:i",time());

/* Presentación de los resultados en una forma similar a la siguiente:
Miércoles, 23 de junio de 2004 | 17:20
*/
echo $dia[$gisett].", ".date("d")." de ".$mes[$mesnum]." de ".date("Y")." | ".$hora;
}
function semi($sha,$shb)
{
	require ("config.php");
	$semix=md5($semi[0].$sha.$shb);
	return $semix;
}
function semix($sha,$shb,$shc)
{
	require ("config.php");
	$semix[0]=md5($semi[0].$sha.$shb);
	$semix[1]=md5($semi[0].$sha.$shc);
	$semix[2]=md5($sha.$semi[1].$shc);
	$semix[3]=md5($sha.$shc.$semi[2]);
	return $semix;
}
function combo($iddepe)
{
$fac=0;
$esc=0;
switch ($iddepe) 
{
     case 313042100:
     	$fac=1;
			$esc=1;     	
			break;
     case 313042200:
     	$fac=1;
			$esc=2;     	
			break;
		case 313042300:
     	$fac=1;
			$esc=3;     	
			break;
		case 313042301:
     	$fac=1;
			$esc=4;     	
			break;
		case 313042302:
     	$fac=1;
			$esc=5;     	
			break;
		case 313042303:
     	$fac=1;
			$esc=6;     	
			break;
		case 313042306:
     	$fac=1;
			$esc=7;     	
			break;
		case 313042400:
     	$fac=1;
			$esc=8;     	
			break;
		case 313042410:
     	$fac=1;
			$esc=9;     	
			break;
		case 313042414:
     	$fac=1;
			$esc=10;     	
			break;
		case 313042418:
     	$fac=1;
			$esc=11;     	
			break;
		case 313042500:
     	$fac=1;
			$esc=12;     	
			break;
		case 313046000:
     	$fac=1;
			$esc=13;     	
			break;

		case 314047000:
     	$fac=2;
			$esc=1;     	
			break;
		case 314048000:
     	$fac=2;
			$esc=2;     	
			break;
		case 314049000:
     	$fac=2;
			$esc=3;     	
			break;
		case 314088000:
     	$fac=2;
			$esc=4;     	
			break;			

		case 316052000:
     	$fac=3;
			$esc=1;     	
			break;
		case 316053000:
     	$fac=3;
			$esc=2;     	
			break;
		case 316054000:
     	$fac=3;
			$esc=3;     	
			break;
						
		case 317055000:
     	$fac=4;
			$esc=1;     	
			break;
			
		case 312041000:
     	$fac=5;
			$esc=1;     	
			break;
			
		case 315050000:
     	$fac=6;
			$esc=1;     	
			break;
		case 315051000:
     	$fac=6;
			$esc=2;     	
			break;	
			
     default:
     	$fac=0;
			$esc=0;     	
}
$facesc[0]=$fac;
$facesc[1]=$esc;
return $facesc;
}

function minuto_hora($min)
{
 $resultado='00:00';
 if ($min>0)
 {
  $minutos=$min;
  $horas=floor($minutos/60);
  $minutos2=$minutos%60;
  if($minutos2<10)$minutos2='0'.$minutos2;
  if($horas==0)$resultado='00'.':'.$minutos2;
  else $resultado= $horas.':'.$minutos2;
 }
 return $resultado;
}
function restarMin($t1, $t2)
{	
 $separar[1]=explode(':',$t1);
 $separar[2]=explode(':',$t2);
 $m1 = ($separar[1][0]*60)+$separar[1][1];
 $m2 = ($separar[2][0]*60)+$separar[2][1];
 $mRes = $m1-$m2;
 return ($mRes);
}
function Validar_docente($cod, $pas,$ip,$est)
{
	$conn=conex();
	$sql="exec SP_login ".$cod.", '".$pas."', '".$ip."', ".$est;	
	//$result=luis($conn, $sql);e

	$query = mssql_query($sql,$conn); 
		if (!$query) {
		    die('MSSQL error: ' . mssql_get_last_message());
			}
$row = mssql_fetch_row($query);
	$men=$row[0];
	return  ($men);
//	return $query ;
			
//	cierra($query);
	//noconex($conn);
}
function generar_clave($long){ 
$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";  
    mt_srand((double)microtime()*1000000); 
	$i=0;  
    while ($i != $long) {  
        $rand=mt_rand() % strlen($chars);  
        $tmp=$chars[$rand];  
        $pass=$pass . $tmp;  
        $chars=str_replace($tmp, "", $chars);  
        $i++;  
    }  
    return strrev($pass);  
}

function Obliga_cambios($cod)
{
	$conn=conex();
	$sql="exec SP_NewDocente ".$cod ;	
	//$result=luis($conn, $sql);e

	$query = mssql_query($sql,$conn); 
		if (!$query) {
		    die('MSSQL error: ' . mssql_get_last_message());
			}
$row = mssql_fetch_row($query);
	$men=$row[0];
	return  ($men);
//	return $query ;
			
//	cierra($query);
	//noconex($conn);

}
function claveportal($length=6,$uc=false,$n=TRUE,$sc=FALSE)
{
	$source = '9876543210';//'abcdefghijklmnopqrstuvwxyz';
	if($uc==1) $source .= '';//'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if($n==1) $source .= '123456';//'1234567890';
	if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
	if($length>0){
		$rstr = "";
		$source = str_split($source,1);
		for($i=1; $i<=$length; $i++){
			mt_srand((double)microtime() * 1000000);
			$num = mt_rand(1,count($source));
			$rstr .= $source[$num-1];
		}

	}
	return $rstr;
}
function jaqx($sex,$ml,$fx,$dv,$n,$op=''){
	$vx='';
	$vy='';
	for ($i=1;$i<=$n;$i++){
		if($i<$n){
			$vx=$vx.'a'.$i.',';
			$vy=$vy.'a'.$i.': ""+a'.$i.'+"", ';
		}else{
			$vx=$vx.'a'.$i;
			$vy=$vy.'a'.$i.': ""+a'.$i.'+""';
		}
	}	
	echo 'function '.$fx.'('.$vx.'){';
	if ($op=='autocompletar'){
		//echo 'alert(a3);';
		echo 'pal="#s"+a2;';
		echo 'pel="#l"+a2;';
	}else{
		echo 'pel="#'.$dv.'";';	
	}
	echo '$.post("dump.php?sesion='.$sex.'&ml='.$ml.'&fx='.$fx.'", {'.$vy.'},';
	echo '		function(data){';
	echo '			if(data.length >0) {';
	if ($op=='autocompletar'){
		echo '			$(pal).show();';
	}
	echo '				$(pel).html(data);';
	echo '				}';
	echo '		}';
	echo '	);';
	echo '}';
	if ($op=='fill'){
		echo 'function fill2(a1,a2,thisValue3,pulpo,k,nk,sug) {';
		echo 'pal="#a"+nk+pulpo;';
		echo 'pel="#b"+nk+pulpo;';
		echo '$(pal).val(thisValue);';
		echo '$(pel).val(thisValue2);';
		echo 'clearsuggestions(k,sug);';
		echo '}';
	}
}	



function generateRandomString($length = 10,$solo_numeros = false) {
		if ($solo_numeros == true){
			$characters = '0123456789';	
		}else{
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
    
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
} 


function fncQuitarTildes($cadena) {
	$cade = utf8_decode($cadena);
	$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");$permitidas= array (“a”,”e”,”i”,”o”,”u”,”A”,”E”,”I”,”O”,”U”,”n”,”N”,”A”,”E”,”I”,”O”,”U”,”a”,”e”,”i”,”o”,”u”,”c”,”C”,”a”,”e”,”i”,”o”,”u”,”A”,”E”,”I”,”O”,”U”,”u”,”o”,”O”,”i”,”a”,”e”,”U”,”I”,”A”,”E”,”N”,”a”,”e”,”i”,”o”,”u”);
	$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
	$texto = str_replace($no_permitidas, $permitidas ,$cade);
	return $texto;

	// $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
	// $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
	// $texto = str_replace($no_permitidas, $permitidas ,$cadena);
	// return $texto;
}

function getUserIpAdress()
{
 
   if( $_SERVER['HTTP_X_FORWARDED_FOR'] != '' )
   {
      $client_ip = 
         ( !empty($_SERVER['REMOTE_ADDR']) ) ? 
            $_SERVER['REMOTE_ADDR'] 
            : 
            ( ( !empty($_ENV['REMOTE_ADDR']) ) ? 
               $_ENV['REMOTE_ADDR'] 
               : 
               "unknown" );
 
      // los proxys van añadiendo al final de esta cabecera
      // las direcciones ip que van "ocultando". Para localizar la ip real
      // del usuario se comienza a mirar por el principio hasta encontrar 
      // una dirección ip que no sea del rango privado. En caso de no 
      // encontrarse ninguna se toma como valor el REMOTE_ADDR
 
      $entries = preg_split('/[, ]/', $_SERVER['HTTP_X_FORWARDED_FOR']);
 
      reset($entries);
      while (list(, $entry) = each($entries)) 
      {
         $entry = trim($entry);
         if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list) )
         {
            // http://www.faqs.org/rfcs/rfc1918.html
            $private_ip = array(
                  '/^0\./', 
                  '/^127\.0\.0\.1/', 
                  '/^192\.168\..*/', 
                  '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/', 
                  '/^10\..*/');
 
            $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
 
            if ($client_ip != $found_ip)
            {
               $client_ip = $found_ip;
               break;
            }
         }
      }
   }
   else
   {
      $client_ip = 
         ( !empty($_SERVER['REMOTE_ADDR']) ) ? 
            $_SERVER['REMOTE_ADDR'] 
            : 
            ( ( !empty($_ENV['REMOTE_ADDR']) ) ? 
               $_ENV['REMOTE_ADDR'] 
               : 
               "unknown" );
   }
 
   return $client_ip;
 
}

function getNavegador()
{
$user_agent = $_SERVER['HTTP_USER_AGENT'];
if(strpos($user_agent, 'MSIE') !== FALSE)
   return 'Internet explorer';
 elseif(strpos($user_agent, 'Edge') !== FALSE) //Microsoft Edge
   return 'Microsoft Edge';
 elseif(strpos($user_agent, 'Trident') !== FALSE) //IE 11
    return 'Internet explorer';
 elseif(strpos($user_agent, 'Opera Mini') !== FALSE)
   return "Opera Mini";
 elseif(strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
   return "Opera";
 elseif(strpos($user_agent, 'Iridium') !== FALSE)
   return 'Iridium Browser'; 
 elseif(strpos($user_agent, 'Iron') !== FALSE)
   return 'SwIron';
 elseif(strpos($user_agent, 'Chromodo') !== FALSE)
   return 'Chromodo Web Browser';
 elseif(strpos($user_agent, 'Firefox') !== FALSE)
   return 'Mozilla Firefox';
 elseif(strpos($user_agent, 'Chrome') !== FALSE)
   return 'Google Chrome';
 elseif(strpos($user_agent, 'Safari') !== FALSE)
   return "Safari";
 else
   return 'TOR';
}


function GetMAC(){
    ob_start();
    system('getmac');
    $Content = ob_get_contents();
    ob_clean();
    return substr($Content, strpos($Content,'\\')-20, 17);
}

function getDireccionMac()
{
ob_start();
//Get the ipconfig details using system commond
system('ipconfig /all');
 
// Capture the output into a variable
$mycom=ob_get_contents();
// Clean (erase) the output buffer
ob_clean();
 
$findme = "Physical";
//Search the "Physical" | Find the position of Physical text
$pmac = strpos($mycom, $findme);
 
// Get Physical Address
$mac=substr($mycom,($pmac+36),17);
//Display Mac Address
return $mac;
}




function fncEnvioCorreoNotasDocente($sex, $pvarEmail, $pvarEmailToken){

	$correo_envio = $pvarEmail;
	$correo_titulo = "Verificacion de Correo Electronico";
	// $correo_titulo = html_entity_decode($correo_titulo);

	$correo_mensaje  = "Estimado Docente <br><br>";
	$correo_mensaje .= "Mediante el siguiente correo se esta realizando la operacion de verificacion del correo electronico que usted ingreso en nuestro portal de intranet. <br>";
	$correo_mensaje .= "Como ultimo paso usted debera ingresar al siguiente enlace para la válidacion de la informacion brindada. <br><br>";

	$url_correo = "https://net.upt.edu.pe/dValidacion.php?t=".$pvarEmailToken."&sesion=".$sex;

	$correo_mensaje .= '<b>Enlace de Validacion:</b> <a href="'.$url_correo.'" >'.$url_correo.'</a><br><br>';
	$correo_mensaje .= 'Si tiene inconvenientes o problemas en este proceso de validacion, comuníquese con la oficina de Tecnologias de la Informacion a los anexos 455 <br><br>';
	$correo_mensaje .= '<small><strong>El enlace proporcionado solo tendra como tiempo limitado 24 horas</strong></small>.';
	
	return fncEnviarCorreoElectronico($correo_envio, $correo_titulo, $correo_mensaje);

}

function fncEnvioCorreoTokenCarga($sex, $Semestre, $pvarEmail, $pvarEmailToken){
 
	$correo_envio = $pvarEmail;
	$correo_titulo = "Token de Verificacion - Semestre: ".strtoupper(fncQuitarTildes($Semestre));
	// $correo_titulo = html_entity_decode($correo_titulo);

	$correo_mensaje  = "Estimado Docente <br><br>";
	$correo_mensaje .= "Mediante el siguiente correo se esta realizando la operacion de verificacion al ingreso de notas del semestre: ".strtoupper(fncQuitarTildes($Semestre))." para que asi usted pueda realizar las operaciones dentro de sus cursos. <br>";
	$correo_mensaje .= "Como ultimo paso usted debera ingresar al siguiente codigo para la válidacion en el ingreso. <br><br>";

	$correo_mensaje .= '<b>Token de Validacion:</b> <a href="#" >'.$pvarEmailToken.'</a><br><br>';

	$correo_mensaje .= 'Si tiene inconvenientes o problemas en este proceso de validacion, comuníquese con la oficina de Tecnologias de la Informacion a los anexos 455 <br><br>';
	$correo_mensaje .= '<small><strong>El token proporcionado solo tendra como tiempo limitado 24 horas</strong></small>.';

	return fncEnviarCorreoElectronico($correo_envio, $correo_titulo, $correo_mensaje);

}

function fncMostrarMensajeDatosUsuarioEmail($sex, $varEmail, $varEmailToken, $bitEmailValidado, $intEmailIntentos, $width = 100, $height = 70 ){

	include ("config_email.php");

	$pintEmailIntentos 		= 0;

	$bolCambiarEmail 			= 1;
	$bolMostrarMensaje 		= true;
	$strTituloMensaje 		= 'INFORMATIVO';
	$strClaseMensaje 			= 'notice';
	$strContenidoMensaje 	= 'Comunicado a la plana docente. Ahora tiene usted la opci&oacute;n de mejorar la seguridad en el proceso de registro de sus notas. Para tener el beneficio actualice y verifique su correo electr&oacute;nico y su n&uacute;mero celular(<i><b>Opcional</b></i> ) <a href="dusuario.php?sesion='.$sex.'">Aqui!</a>. ';

	if( (isset($varEmail) and $varEmail !== "") ){

		$pintEmailIntentos 		= $intEmailIntentos;

		if( $bitEmailValidado == 0 ){
			$bolMostrarMensaje 		= true;
			if ( $pintEmailIntentos >= $gMaximoIntentosEmail ){
				$bolCambiarEmail 			= 0;
				$strTituloMensaje 		= 'ALERTA';
				$strClaseMensaje 			= 'error';
				$strContenidoMensaje 	= 'Usted ya excedi&oacute; el l&iacute;mite de veces que puede enviar correo de verificaci&oacute;n del correo. Para modificarlo o corregirlo comunicarse con <strong>computo@upt.edu.pe</strong> o a los <strong>anexos 455</strong>';		
			}else{
				if (trim($varEmailToken) !== ""){
					$strTituloMensaje 		= 'ALERTA';
					$strClaseMensaje 			= 'warning';
					$strContenidoMensaje 	= 'Usted ha registrado un correo electr&oacute;nico en la plataforma. S&oacute;lo tiene como &uacute;ltimo paso verificarlo. Ingrese al correo brindado y verifique su correo';	
				}else{
					$bolCambiarEmail 			= 2;
					$strTituloMensaje 		= 'ALERTA';
					$strClaseMensaje 			= 'warning';
					$strContenidoMensaje 	= 'Usted ha registrado un correo electr&oacute;nico en la plataforma, pero debe verificarlo nuevamente. Presione la opcion de verificaci&oacute;n y valide su correo';	
				}
						
			}						
							
		}else{
			$bolMostrarMensaje 	= false;
		}

	}

	// COMENTAR PARA FUNCIONAMIENTO DE ENVIO DE CORREOS
	// $bolCambiarEmail 			= true;
	// $bolMostrarMensaje 		= false;

	if( $bolMostrarMensaje == true ){

		// echo '<tr><td>&nbsp;</td></tr>';
		// echo '<table width="'.$width.'%" >';
		echo '	<tr height="'.$height.'">';
		echo '		<td colspan="2">';
		echo '			<div style="padding-bottom:15px;">';
		echo '				<div  class="alert-box '.$strClaseMensaje.'" >';
		echo '					<span style="font-size:13px;">'.strtoupper($strTituloMensaje).'</span><br />';
		echo '					<font style="font-size:12px;">'.$strContenidoMensaje."</font>";
		echo '				</div>';
		echo '			</div>';
		echo '		</td>';
		echo '	</tr>';
		// echo '</table>';

	}

	return $bolCambiarEmail;

}


function fncMostrarMensajeDatosUsuarioCelular($varCelular, $bitCelularValidado, $intCelularIntentos, $width = 100, $height = 70 ){

	include ("config_email.php");

	$pintCelularIntentos 		= 0;

	$bolCambiarCelular 			= true;
	$bolMostrarMensaje 		= false;
	$strTituloMensaje 		= 'INFORMATIVO';
	$strClaseMensaje 			= 'notice';
	$strContenidoMensaje 	= 'Comunicado a la plana docente. Ahora tiene usted la opci&oacute;n de mejorar la seguridad en el proceso de registro de sus notas. Para tener el beneficio actualice y verifique su correo electr&oacute;nico y su n&uacute;mero celular(<i><b>Opcional</b></i> ). ';


	if( (isset($varCelular) and $varCelular !== "") ){

		$pintCelularIntentos 		= $intCelularIntentos;

		if( $bitCelularValidado == 0 ){
			$bolMostrarMensaje 		= true;
			if ( $pintCelularIntentos >= $gMaximoIntentosCelular ){
				$bolCambiarCelular 			= false;
				$strTituloMensaje 		= 'ALERTA';
				$strClaseMensaje 			= 'error';
				$strContenidoMensaje 	= 'Usted ya excedi&oacute; el l&iacute;mite de veces que puede enviar mensajes de texto de verificaci&oacute;n del celular. Para modificarlo o corregirlo comunicarse con <strong>computo@upt.edu.pe</strong> o a los <strong>anexos 455</strong>';		
			}else{
				$strTituloMensaje 		= 'ALERTA';
				$strClaseMensaje 			= 'warning';
				$strContenidoMensaje 	= 'Usted ha registrado un n&uacute;mero de celular en la plataforma. S&oacute;lo tiene como &uacute;ltimo paso verificarlo. Ingrese al c&oacute;digo brindado y verifique su n&uacute;mero de celular';		
			}						
							
		}else{
			$bolMostrarMensaje 	= false;
		}

	}

	// COMENTAR PARA FUNCIONAMIENTO DE ENVIO DE MENSAJES
	// $bolCambiarCelular 			= true;
	// $bolMostrarMensaje 		= false;

	if( $bolMostrarMensaje == true ){

		// echo '<table width="'.$width.'%" >';
		echo '	<tr height="'.$height.'">';
		echo '		<td colspan="2">';
		echo '			<div>';
		echo '				<div  class="alert-box '.$strClaseMensaje.'" >';
		echo '					<span>'.strtoupper($strTituloMensaje).'</span><br />';
		echo '					'.$strContenidoMensaje;
		echo '				</div>';
		echo '			</div>';
		echo '		</td>';
		echo '	</tr>';
		// echo '</table>';

	}

	return $bolCambiarCelular;

}

function fncRegistrarDatoEvaluacionDocenteEmailLog($IdDocenteDatoEvaluacion, $pvarEmail, $varEmailMensaje, $pvarEmailToken){
	$conn_docente 	= conex();

	$sql_docente		=	"
		INSERT INTO Aud_DocenteDatoEvaluacionEmailLog
		(
			IdDocenteDatoEvaluacion,varEmail,varEmailMensaje,varEmailToken,datCreado,varDireccionIp
		)
		VALUES
		(
			".$IdDocenteDatoEvaluacion.",
			'".$pvarEmail."',
			'".$varEmailMensaje."',
			'".$pvarEmailToken."',
			'".date('Y-d-m h:i:s')."',
			'".getUserIpAdress()."'
		)
	";
	// echo $sql_docente;
	$resul_docente	=	luis($conn_docente, $sql_docente);
	cierra($resul_docente);
	noconex($conn_docente);
}

function fncRegistrarDatoEvaluacionDocenteCelularLog($IdDocenteDatoEvaluacion, $pvarCelularGenerado, $varCelularMensaje, $pvarCelularTokenGenerado){
	$conn_docente 	= conex();

	$intIdProveedor = 0;

	if( $pvarCelularGenerado != "" ){ 
		$sql_docente_info		=	"SELECT TOP 1 ap.IdProveedor FROM Aud_Proveedor AS ap WHERE ap.intEstado = 1";
		$resul_docente_info	=	luis($conn_docente, $sql_docente_info);
		$row_docente_info 	=	fetchrow($resul_docente_info,-1);

		if( isset($row_docente_info[0]) ){
			$intIdProveedor = $row_docente_info[0];
		}
		cierra($resul_docente_info);
	}

	$sql_docente		=	"
		INSERT INTO Aud_DocenteDatoEvaluacionCelularLog
		(
			IdDocenteDatoEvaluacion,IdProveedor,varCelular,varCelularMensaje,varCelularToken,datCreado,varDireccionIp
		)
		VALUES
		(
			".$IdDocenteDatoEvaluacion.",
			".$intIdProveedor.",
			'".$pvarCelularGenerado."',
			'".$varCelularMensaje."',
			'".$pvarCelularTokenGenerado."',
			'".date('Y-d-m h:i:s')."',
			'".getUserIpAdress()."'
		)
	";
	// echo $sql_docente;
	$resul_docente	=	luis($conn_docente, $sql_docente);
	cierra($resul_docente);
	noconex($conn_docente);
}

function fncInsertarLogEnvioNota($CodUniv, $ItemEst, $varEmail, $varEmailMensaje, $varCelular, $varCelularMensaje, $IdCarga, $IdEval, $varOperacion, $varObservacion,$esTaex = 0){
		$conn_docente 	= conex();

		$varCelular = 0;
		$varCelularMensaje = "";

		$sql_docente		=	"
			INSERT INTO Aud_LogEnvioNota
			(
				CodUniv,ItemEst,varEmail,varEmailMensajeEnvio,varCelular,varCelularMensajeEnvio,IdCarga,IdEval,varOperacion,varObservacion,datCreado,varDireccionIp,esTaex
			)
			VALUES
			(
				".$CodUniv.",
				".$ItemEst.",
				'".$varEmail."',
				'".$varEmailMensaje."',
				".$varCelular.",
				'".$varCelularMensaje."',
				".$IdCarga.",
				".$IdEval.",
				'".$varOperacion."',
				'".$varObservacion."',
				'".date('Y-d-m h:i:s')."',
				'".getUserIpAdress()."',
				".$esTaex."
			)
		";
		// echo $sql_docente;
		$resul_docente	=	luis($conn_docente, $sql_docente);
		cierra($resul_docente);
		noconex($conn_docente);
}


function fechaCastellano ($fecha) {
  $fecha = substr($fecha, 0, 10);
  $numeroDia = date('d', strtotime($fecha));
  $dia = date('l', strtotime($fecha));
  $mes = date('F', strtotime($fecha));
  $anio = date('Y', strtotime($fecha));
  $dias_ES = array("Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo");
  $dias_EN = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
  $nombredia = str_replace($dias_EN, $dias_ES, $dia);
	$meses_ES = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
  $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
  $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
  return $nombredia." ".$numeroDia." de ".$nombreMes." de ".$anio;
}


function sqlsrv_getdata($result) 
{
	$type = '2';
	$data = array();
	$i = 0;
	switch ($type) {
	  case '1':
	    while ($row = @mssql_fetch_array($result, MSSQL_BOTH)) {
	        $data[$i] = $row;
	        $i++;
	    }
	  break;
	  case '2':
	    while ($row = @mssql_fetch_array($result, MSSQL_ASSOC)) {
	        $data[$i] = $row;
	        $i++;
	    }
	  break;
	  case '3':
	    while ($row = @mssql_fetch_array($result, MSSQL_NUM)) {
	        $data[$i] = $row;
	        $i++;
	    }
	  break;
	}
	//error_log('retornando'.sizeof($data));
	return $data;
}


function eliminar_tildes($cadena){
 
    //Codificamos la cadena en formato utf8 en caso de que nos de errores
    $cadena = utf8_encode($cadena);
 
    //Ahora reemplazamos las letras
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $cadena
    );
 
    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $cadena );
 
    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $cadena );
 
    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $cadena );
 
    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $cadena );
 
    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C'),
        $cadena
    );
 
    return $cadena;
}


function fncEnviarCorreoElectronicoIntranet($correo_envio, $correo_titulo, $correo_mensaje){

	$para 	   = $correo_envio;
	$titulo    = html_entity_decode($correo_titulo);
	$mensaje   = $correo_mensaje;
	
	$headers = "MIME-Version: 1.0\r\n"; 
	$headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";
	
	$email_from = "informa@upt.edu.pe";
	$headers .= 'From: '.$email_from."\r\n".
	'Reply-To: '.$email_from."\r\n" .
	'X-Mailer: PHP/' . phpversion();

	if(@mail($para, $titulo, $mensaje, $headers)){
		// echo "Correo enviado";
		return "OK";
	} else {
	 	// echo "Error al enviar el mail";
	 	$e = @error_get_last();
	 	return "Error al enviar el mail. ".$e['message'];
	}  
	
	// return "OK";
}




function fncEnvioEmailUpt($email, $correo, $mensaje_correo){

	$para 	   = $email;
	$titulo    = html_entity_decode($correo);
	$mensaje   = $mensaje_correo;
	
	$headers = "MIME-Version: 1.0\r\n"; 
	$headers .= "Content-type: text/html; charset=iso-8859-1 \r\n";
	
	$email_from = "informa@upt.edu.pe";
	$headers .= 'From: '.$email_from."\r\n".
	'Reply-To: '.$email_from."\r\n" .
	'X-Mailer: PHP/' . phpversion();

	if(@mail($para, $titulo, $mensaje, $headers)){
		return "OK";
	} else {
	 	$e = @error_get_last();
	 	return "Error al enviar el mail. ".$e['message'];
	}  
	
}



?>