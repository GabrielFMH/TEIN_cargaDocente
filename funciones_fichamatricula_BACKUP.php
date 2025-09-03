<? 
function resulta_fichamatricula($result,$n,$f)
{
	require ("config_fichamatricula.php");
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
function fetchrow_fichamatricula($result,$n)
{
	require ("config_fichamatricula.php");
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
function cierra_fichamatricula($resultx)
{
	require ("config_fichamatricula.php");
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
function noconex_fichamatricula($conn)
{
	require ("config_fichamatricula.php");
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
function conex_fichamatricula()
{
	require ("config_fichamatricula.php");
	if ($dbx==0)
	{
		$conn = pg_connect("host=".$host." port=".$port." user=".$usuario." password=".$pass." dbname=".$data );
	}
	if ($dbx==1)
	{
		// $conn = @mssql_connect($host, $usuario, $pass); 
		// @mssql_select_db($data, $conn);
		// mssql_query('set dateformat dmy'); 
		// mssql_query('set datefirst 1');		

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
function luis_fichamatricula($conn,$sql)
{
	require ("config_fichamatricula.php");
	if ($dbx==0)
	{
		$result = pg_exec($conn,$sql);
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
function callse_fichamatricula($sex)
{
	require ("config_fichamatricula.php");
	session_save_path($rutase);

}
?>
