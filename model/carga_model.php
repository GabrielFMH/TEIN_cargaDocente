<?php

class CargaModel
{
    private $conn;

    public function __construct()
    {
        require_once 'funciones.php';
        $this->conn = conex();
    }

    public function __destruct()
    {
        if ($this->conn) {
            noconex($this->conn);
        }
    }

    private function execute_query($sql)
    {
        $result = luis($this->conn, $sql);
        cierra($result);
    }

    public function uploadTrabajoIndividual($idsem, $codigo, $filename)
    {
        $sql = "exec trabiagregardoc_v2 {$idsem}, {$codigo}, '{$filename}'";
        $this->execute_query($sql);
    }

    public function agregarTrabajo($codigox, $vacti, $vdacti, $vimporta, $vmedida, $vcant, $vhoras, $vcalif, $vmeta, $vdatebox, $vdatebox2, $viddepe, $vcanthoras, $idsem)
    {
        $sql = "exec trabiagregar_v2 {$codigox}, '{$vacti}', '{$vdacti}', '{$vimporta}', '{$vmedida}', {$vcant}, {$vhoras}, {$vcalif}, '{$vmeta}', '{$vdatebox}', '{$vdatebox2}', '{$viddepe}', '{$vcanthoras}', {$idsem}";
        $this->execute_query($sql);
    }

    public function modificarEstadoActividades($mcodigo, $msemestre, $mestado_editar)
    {
        $sql = "exec sp_edit_estado_trabindiv {$mcodigo}, {$msemestre}, {$mestado_editar}";
        $this->execute_query($sql);
    }

    public function modificarEstadoTrabajoIndividual($codigox, $idtrab, $vestado_editar)
    {
        $sql = "exec sp_editar_trabindiv_estado {$codigox}, '{$idtrab}', '{$vestado_editar}'";
        $this->execute_query($sql);
    }

    public function registrarHistorial($codigox, $idtrab, $vnominfo_historial, $vdirigido_historial, $vcargo_historial, $vremitente_historial, $vdetalle_historial, $vporcentaje_historial, $dia)
    {
        $sql = "exec sp_add_trab_historial {$codigox}, '{$idtrab}', '{$vnominfo_historial}', '{$vdirigido_historial}', '{$vcargo_historial}', '{$vremitente_historial}', '{$vdetalle_historial}', '{$vporcentaje_historial}', '{$dia}'";
        $this->execute_query($sql);
    }

    public function eliminarTrabajo($codigox, $idtrab, $msemestre)
    {
        $sql = "delete from trab where codigo={$codigox} and idtrab={$idtrab}";
        $this->execute_query($sql);

        $sql_historial = "delete from trab_historial where idtrab={$idtrab}";
        $this->execute_query($sql_historial);

        $sql_detalle = "delete from detalle_trab where codigo={$codigox} and idsem={$msemestre}";
        $this->execute_query($sql_detalle);
    }

    public function editarTrabajo($codigox, $idtrab, $vacti_editar, $vdacti_editar, $vimporta_editar, $vmedida_editar, $vcant_editar, $vhoras_editar, $vcalif_editar, $vmeta_editar, $vdatebox_editar, $vdatebox2_editar, $vporcentaje_editar)
    {
        $sql = "exec sp_editar_trabindiv {$codigox}, '{$idtrab}', '{$vacti_editar}', '{$vdacti_editar}', '{$vimporta_editar}', '{$vmedida_editar}', {$vcant_editar}, {$vhoras_editar}, {$vcalif_editar}, '{$vmeta_editar}', '{$vdatebox_editar}', '{$vdatebox2_editar}', '{$vporcentaje_editar}'";
        $this->execute_query($sql);
    }
    
    public function finalizarTrabajo($idtrab, $idsem, $codigo, $estado)
    {
        $sql = "exec sp_registrar_trabindiv_finalizacion {$idtrab}, {$idsem}, {$codigo}, {$estado}";
        $this->execute_query($sql);
    }

    public function revertirTrabajo($idtrab, $idsem, $codigo, $estado)
    {
        $sql = "exec sp_update_trabindiv_reversion {$idtrab}, {$idsem}, {$codigo}, {$estado}";
        $this->execute_query($sql);
    }

    public function getCarga($codigo)
    {
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
		       AND carga.codigo =  {$codigo}
		UNION ALL
		SELECT cch.IdCargaCROM idCarga, cs.IdSem,cch.IdPesPlanEstudioCurso IdCurso,cch.Seccion,e.idarbol,cch.IdEscuela
		FROM Cho_CargaHoraria cch
		LEFT JOIN eval e ON cch.IdCargaCROM = e.idcarga AND e.idarbol = 1000000
		INNER JOIN Cho_PeriodoPersona cpp ON cpp.IdPeriodoPersona = cch.IdPeriodoPersona
		INNER JOIN Cho_Persona cp ON cp.IdChoPersona = cpp.IdChoPersona
		INNER JOIN ucodigo u ON u.CodPer = cp.CodPer
		INNER JOIN Cho_Semestre cs ON cs.IdChoSemestre = cpp.IdChoSemestre
		INNER JOIN SEMESTRE s ON s.IdSem = cs.IdSem
		WHERE u.coduniv = {$codigo} AND cch.Estado = 1 AND s.Activo = 1";
        return luis($this->conn, $sql);
    }

    public function updateCarga($idcarga, $idsem, $idcurso, $seccion, $iddepe, $codigo)
    {
        $sqla="exec uretiro {$idcarga}, {$idsem}, {$idcurso}, '{$seccion}', {$iddepe}";
        $sqlb="exec inuevo {$idcarga}, {$idsem}, {$idcurso}, '{$seccion}', {$codigo}, {$iddepe}";
        luis($this->conn, $sqla);
        luis($this->conn, $sqlb);
    }

    public function getCursoInfo($dc, $codigo, $esSemestreTaex)
    {
        if(!$esSemestreTaex){
            $sql = "sp_int_ListarIdCargaDocente {$codigo}, {$dc}";
        }else{
            $sql = "sp_int_ListarIdCargaDocenteTaex {$codigo}, {$dc}";
        }
        return luis($this->conn, $sql);
    }

    public function getCursos($codper, $idsem, $esSemestreTaex)
    {
        $sql = "exec sp_int_ListarCargaDocenteNuevo {$codper}, {$idsem}, {$esSemestreTaex}";
        return luis($this->conn, $sql);
    }
    
    public function getSemestres($codper)
    {
        $sql="sp_LISTADO_SEMESTRE_CARGA_NUEVO ".$codper;
        return luis($this->conn, $sql);
    }

    public function getSemestreInfo($idsem, $esSemestreTaex)
    {
        if(!$esSemestreTaex){
            $sql="SELECT TOP 1 s.Semestre, Observ FROM SEMESTRE s WHERE s.IdSem=".$idsem;
        }else{
            $sql="SELECT TOP 1 s.Semestre, Nombre FROM Tax_SemestreConsolidado s WHERE s.IdSem=".$idsem;
        }
        return luis($this->conn, $sql);
    }

    public function getDirectorIdDepe($codigo)
    {
        $sql="SELECT idesc AS iddepe FROM gcodigo WHERE  idfac = 421 AND codigo =".$codigo;
        return luis($this->conn, $sql);
    }

    public function getTrabajoIndividualDoc($codigo, $idsem)
    {
        $sql="SELECT TOP 1 s.archivo FROM trabdoc s WHERE s.codigo={$codigo} and s.idsem = {$idsem}";
        return luis($this->conn, $sql);
    }

    public function getDocenteDatoEvaluacion($codigo)
    {
        $sql = "SELECT dde.IdDocenteDatoEvaluacion FROM Aud_DocenteDatoEvaluacion AS dde WHERE dde.CodUniv = {$codigo} AND dde.ItemEst = 1 and dde.bitEliminado = 0";
        return luis($this->conn, $sql);
    }

    public function getUltimosAccesos($pIdDocenteDatoEvaluacion, $idsem)
    {
        $sql_accesos = "SELECT tb.CodigoCurso, tb.Asignatura, tb.Seccion, UPPER(tb.Docente) Docente, FORMAT(tb.Fecha ,'dd/MM/yyyy hh:mm:ss' ,'es') Fecha
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
				WHERE adtc.IdDocenteDatoEvaluacion = {$pIdDocenteDatoEvaluacion}
				AND adtc.Idsem = {$idsem}
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
				WHERE adtc.IdDocenteDatoEvaluacion = {$pIdDocenteDatoEvaluacion}
				AND adtc.IdSem = {$idsem}
				AND adtc.bitEliminado = 0
				AND adtcd.esTaex = 1

			) AS tb 
			ORDER BY tb.Id DESC";
        return luis($this->conn, $sql_accesos);
    }
}
?>