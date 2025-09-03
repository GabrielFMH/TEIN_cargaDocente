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

    // Método para validar que la suma de porcentajes no exceda 100%
    public function validarPorcentajeTotal($codigo, $idsem, $nuevoPorcentaje, $idtrabExcluir = null)
    {
        $sql = "SELECT ISNULL(SUM(CAST(porcentaje AS FLOAT)), 0) as total FROM trab WHERE codigo = {$codigo} AND idsem = {$idsem}";
        if ($idtrabExcluir) {
            $sql .= " AND idtrab != {$idtrabExcluir}";
        }
        
        $result = luis($this->conn, $sql);
        $row = fetchrow($result, -1);
        $totalActual = $row[0];
        cierra($result);
        
        return ($totalActual + $nuevoPorcentaje) <= 100;
    }

    // Método para generar número automático de informe
    public function generarNumeroInforme($codigo, $idsem)
    {
        $sql = "SELECT ISNULL(MAX(numero_informe), 0) + 1 FROM trab WHERE codigo = {$codigo} AND idsem = {$idsem}";
        $result = luis($this->conn, $sql);
        $row = fetchrow($result, -1);
        $numero = $row[0];
        cierra($result);
        return $numero;
    }

    // Método para validar carga horaria según reglas (20h TC, 40h totales, etc.)
    public function validarCargaHoraria($codigo, $idsem, $horasNuevas)
    {
        $sql = "SELECT 
                    SUM(CASE WHEN tipo_actividad = 'TC' THEN horas ELSE 0 END) as horas_tc,
                    SUM(horas) as horas_totales
                FROM trab 
                WHERE codigo = {$codigo} AND idsem = {$idsem}";
        
        $result = luis($this->conn, $sql);
        $row = fetchrow($result, -1);
        $horas_tc = $row[0] ? $row[0] : 0;
        $horas_totales = $row[1] ? $row[1] : 0;
        cierra($result);
        
        $respuesta = array(
            'valido' => true,
            'mensaje' => ''
        );
        
        // Validar mínimo 20h lectivas para TC
        if (($horas_tc + $horasNuevas) > 20) {
            $respuesta['valido'] = false;
            $respuesta['mensaje'] = 'Las horas lectivas (TC) deben ser mínimo 20 horas';
        }
        
        // Validar máximo 40h totales
        if (($horas_totales + $horasNuevas) > 40) {
            $respuesta['valido'] = false;
            $respuesta['mensaje'] = 'Las horas totales no pueden exceder 40 horas';
        }
        
        return $respuesta;
    }

    // Método para calcular porcentaje de avance automáticamente
    public function calcularPorcentajeAutomatico($codigo, $idsem, $numActividades)
    {
        if ($numActividades == 0) return 0;
        
        // Distribución equitativa para nuevas actividades
        return round(100 / $numActividades, 2);
    }

    // Método para obtener catálogo de actividades
    public function getCatalogoActividades()
    {
        $sql = "SELECT id, nombre, descripcion, tipo FROM catalogo_actividades WHERE activo = 1 ORDER BY nombre";
        return luis($this->conn, $sql);
    }

    // Método para obtener catálogo de comité electoral (nuevas actividades)
    public function getCatalogoComiteElectoral()
    {
        $actividades = array(
            'Preparacion, desarrollo y evaluacion de clases teoricas y practicas',
            'Orientacion de matricula',
            'Participacion en jurados',
            'Asesoramiento de practicas pre-profesionales',
            'Asesoramiento de tesis',
            'Comisiones',
            'Seguimiento de egresados',
            'Consejeria',
            'Tutoria',
            'Seminarios',
            'Investigacion',
            'Produccion Intelectual',
            'Proyeccion Social',
            'Extension Universitaria',
            'Gestion de gobierno universitario',
            'Jefatura de oficina o unidades administrativas',
            'Comisiones',
            'Produccion de bienes o prestacion de servicios'
        );
        
        return $actividades;
    }

    // Método para integración con Scalafon
    public function integrarDatosScalafon($codigo)
    {
        // Simulación de integración con Scalafon
        // En producción, aquí iría la lógica de conexión con el servicio
        $sql = "SELECT 
                    s.horas_lectivas,
                    s.horas_totales,
                    s.tipo_contrato,
                    s.dependencia,
                    s.costo_hora
                FROM scalafon_datos s 
                WHERE s.codigo_universitario = {$codigo}";
        
        return luis($this->conn, $sql);
    }

    // Método para obtener costo por hora por docente
    public function getCostoPorHoraDocente($codigo)
    {
        $sql = "SELECT 
                    ISNULL(costo_hora, 0) as costo_hora,
                    ISNULL(beneficios_sociales, 0) as beneficios_sociales
                FROM docente_costos 
                WHERE codigo = {$codigo}";
        
        $result = luis($this->conn, $sql);
        $row = fetchrow($result, -1);
        cierra($result);
        
        return array(
            'costo_hora' => $row[0],
            'beneficios_sociales' => $row[1]
        );
    }

    // Método para enlazar PTI con planilla presupuestal
    public function enlazarPTIPlanilla($idtrab, $id_planilla)
    {
        $sql = "UPDATE trab SET id_planilla_presupuestal = {$id_planilla} WHERE idtrab = {$idtrab}";
        $this->execute_query($sql);
    }

    // Método para consultas de presupuesto
    public function getPresupuestoInvestigacion($codigo, $idsem)
    {
        $sql = "SELECT 
                    p.planilla,
                    p.pta,
                    p.beneficios,
                    SUM(t.horas * dc.costo_hora) as costo_total
                FROM trab t
                INNER JOIN docente_costos dc ON dc.codigo = t.codigo
                INNER JOIN planilla_presupuestal p ON p.id = t.id_planilla_presupuestal
                WHERE t.codigo = {$codigo} AND t.idsem = {$idsem} AND t.tipo = 'INVESTIGACION'
                GROUP BY p.planilla, p.pta, p.beneficios";
        
        return luis($this->conn, $sql);
    }

    // Método para consultas de cobranzas
    public function getCobranzas($codigo)
    {
        $sql = "SELECT 
                    c.matriculas,
                    c.cuotas,
                    c.pronto_pago,
                    c.fecha_actualizacion
                FROM cobranzas_docente c
                WHERE c.codigo = {$codigo}";
        
        return luis($this->conn, $sql);
    }

    // Método para obtener estudiantes activos
    public function getEstudiantesActivos($codigo, $tipo_programa)
    {
        $sql = "SELECT 
                    COUNT(*) as total_estudiantes,
                    programa
                FROM estudiantes_activos
                WHERE codigo_docente = {$codigo} 
                AND tipo_programa = '{$tipo_programa}'
                GROUP BY programa";
        
        return luis($this->conn, $sql);
    }

    // Método para reportes de horas lectivas
    public function getReporteHorasLectivas($idsem, $tipo_docente = null, $escuela = null, $facultad = null)
    {
        $sql = "SELECT 
                    t.codigo,
                    d.nombre_docente,
                    SUM(CASE WHEN t.tipo_actividad = 'TC' THEN t.horas ELSE 0 END) as horas_tc,
                    SUM(CASE WHEN t.tipo_actividad = 'TP' THEN t.horas ELSE 0 END) as horas_tp,
                    SUM(t.horas) as horas_totales,
                    e.nombre_escuela,
                    f.nombre_facultad
                FROM trab t
                INNER JOIN docentes d ON d.codigo = t.codigo
                INNER JOIN escuelas e ON e.id = t.id_escuela
                INNER JOIN facultades f ON f.id = e.id_facultad
                WHERE t.idsem = {$idsem}";
        
        if ($tipo_docente) {
            $sql .= " AND t.tipo_docente = '{$tipo_docente}'";
        }
        
        if ($escuela) {
            $sql .= " AND e.id = {$escuela}";
        }
        
        if ($facultad) {
            $sql .= " AND f.id = {$facultad}";
        }
        
        $sql .= " GROUP BY t.codigo, d.nombre_docente, e.nombre_escuela, f.nombre_facultad
                  ORDER BY f.nombre_facultad, e.nombre_escuela, d.nombre_docente";
        
        return luis($this->conn, $sql);
    }

    // Método para reportes de horas programadas por clasificación
    public function getReporteHorasPorClasificacion($idsem, $escuela = null, $facultad = null)
    {
        $sql = "SELECT 
                    c.nombre_clasificacion,
                    SUM(t.horas) as horas_totales,
                    COUNT(t.idtrab) as cantidad_actividades,
                    e.nombre_escuela,
                    f.nombre_facultad
                FROM trab t
                INNER JOIN clasificaciones c ON c.id = t.id_clasificacion
                INNER JOIN escuelas e ON e.id = t.id_escuela
                INNER JOIN facultades f ON f.id = e.id_facultad
                WHERE t.idsem = {$idsem}";
        
        if ($escuela) {
            $sql .= " AND e.id = {$escuela}";
        }
        
        if ($facultad) {
            $sql .= " AND f.id = {$facultad}";
        }
        
        $sql .= " GROUP BY c.nombre_clasificacion, e.nombre_escuela, f.nombre_facultad
                  ORDER BY horas_totales DESC";
        
        return luis($this->conn, $sql);
    }

    public function uploadTrabajoIndividual($idsem, $codigo, $filename)
    {
        // Validar tipo de archivo antes de guardar
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!in_array($extension, array('xls', 'xlsx'))) {
            throw new Exception('Solo se permiten archivos Excel (.xls, .xlsx)');
        }
        
        $sql = "exec trabiagregardoc_v2 {$idsem}, {$codigo}, '{$filename}'";
        $this->execute_query($sql);
    }

    public function agregarTrabajo($codigox, $vacti, $vdacti, $vimporta, $vmedida, $vcant, $vhoras, $vcalif, $vmeta, $vdatebox, $vdatebox2, $viddepe, $vcanthoras, $idsem)
    {
        // Validar que vcant solo contenga números
        if (!is_numeric($vcant)) {
            throw new Exception('El campo cantidad debe ser numérico');
        }
        
        // Validar porcentaje total
        $porcentajeValido = $this->validarPorcentajeTotal($codigox, $idsem, $vcalif);
        if (!$porcentajeValido) {
            throw new Exception('La suma de porcentajes no puede exceder 100%');
        }
        
        // Validar carga horaria
        $validacionCarga = $this->validarCargaHoraria($codigox, $idsem, $vhoras);
        if (!$validacionCarga['valido']) {
            throw new Exception($validacionCarga['mensaje']);
        }
        
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
        $sql = "exec sp_editar_trabindiv_estado {$codigox}, {$idtrab}, '{$vestado_editar}'";
        $this->execute_query($sql);
    }

    public function registrarHistorial($codigox, $idtrab, $vnominfo_historial, $vdirigido_historial, $vcargo_historial, $vremitente_historial, $vdetalle_historial, $vporcentaje_historial, $dia)
    {
        // Validar que el porcentaje sea numérico
        if (!is_numeric($vporcentaje_historial)) {
            throw new Exception('El porcentaje debe ser numérico');
        }
        
        $sql = "exec sp_add_trab_historial {$codigox}, {$idtrab}, '{$vnominfo_historial}', '{$vdirigido_historial}', '{$vcargo_historial}', '{$vremitente_historial}', '{$vdetalle_historial}', {$vporcentaje_historial}, '{$dia}'";
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
        // Validar que vcant y vhoras sean numéricos
        if (!is_numeric($vcant_editar) || !is_numeric($vhoras_editar) || !is_numeric($vcalif_editar)) {
            throw new Exception('Los campos cantidad, horas y porcentaje deben ser numéricos');
        }
        
        // Obtener idsem del trabajo
        $sql_idsem = "SELECT idsem FROM trab WHERE idtrab = {$idtrab}";
        $result_idsem = luis($this->conn, $sql_idsem);
        $row_idsem = fetchrow($result_idsem, -1);
        $idsem = $row_idsem[0];
        cierra($result_idsem);

        // Validar porcentaje total excluyendo esta actividad
        $porcentajeValido = $this->validarPorcentajeTotal($codigox, $idsem, $vcalif_editar, $idtrab);
        if (!$porcentajeValido) {
            throw new Exception('La suma de porcentajes no puede exceder 100%');
        }
        
        $sql = "exec sp_editar_trabindiv {$codigox}, {$idtrab}, '{$vacti_editar}', '{$vdacti_editar}', '{$vimporta_editar}', '{$vmedida_editar}', {$vcant_editar}, {$vhoras_editar}, {$vcalif_editar}, '{$vmeta_editar}', '{$vdatebox_editar}', '{$vdatebox2_editar}', {$vporcentaje_editar}";
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
    
    // Método para filtrar actividades por meses (ciclos I, II, REC)
    public function filtrarActividadesPorMes($idsem, $mesInicio, $mesFin)
    {
        $sql = "SELECT * FROM trab 
                WHERE idsem = {$idsem} 
                AND MONTH(fecha_inicio) BETWEEN {$mesInicio} AND {$mesFin}
                ORDER BY fecha_inicio";
        return luis($this->conn, $sql);
    }
    
    // Método para verificar si una actividad es lectiva o no
    public function esActividadLectiva($idtrab)
    {
        $sql = "SELECT actividad FROM trab WHERE idtrab = {$idtrab}";
        $result = luis($this->conn, $sql);
        $row = fetchrow($result, -1);
        $actividad = strtolower($row[0]);
        cierra($result);

        return (strpos($actividad, 'teoria') !== false || strpos($actividad, 'practica') !== false);
    }
    public function getCargasLectivasNoLectivas($idsem, $codigo = null)
    {
        try {
            // Verificar conexión
            if (!$this->conn) {
                throw new Exception('Conexión a la base de datos no disponible.');
            }

            // Verificar parámetros
            if (!is_numeric($idsem)) {
                throw new Exception('El parámetro idsem debe ser numérico. Valor recibido: ' . $idsem);
            }
            if ($codigo !== null && !is_numeric($codigo)) {
                throw new Exception('El parámetro codigo debe ser numérico. Valor recibido: ' . $codigo);
            }

            // Consulta simplificada para depuración
            $sql = "SELECT COUNT(*) as total FROM trab WHERE idsem = {$idsem}";
            if ($codigo) {
                $sql .= " AND codigo = {$codigo}";
            }

            $result = luis($this->conn, $sql);
            if (!$result) {
                // Intentar obtener error de SQL Server
                $error = 'Error desconocido en consulta simplificada';
                if (function_exists('mssql_get_last_message')) {
                    $error = mssql_get_last_message();
                }
                throw new Exception('Error al ejecutar consulta simplificada. SQL: ' . $sql . '. Error: ' . $error);
            }

            $row = fetchrow($result, -1);
            cierra($result);

            if ($row[0] == 0) {
                throw new Exception('No se encontraron registros para el semestre ' . $idsem . ' y código ' . ($codigo ?: 'todos'));
            }

            // Consulta completa si hay datos (sin tipo_actividad ya que no existe en la tabla)
            $sql = "SELECT
                        t.idtrab,
                        t.codigo,
                        t.actividad,
                        t.horas,
                        t.fecha_inicio,
                        t.fecha_fin,
                        CASE WHEN t.actividad LIKE '%teoria%' OR t.actividad LIKE '%practica%' THEN 'Lectiva' ELSE 'No Lectiva' END as clasificacion
                    FROM trab t
                    WHERE t.idsem = {$idsem}";
            if ($codigo) {
                $sql .= " AND t.codigo = {$codigo}";
            }
            $sql .= " ORDER BY t.fecha_inicio";

            // Intentar ejecutar la consulta
            $result = luis($this->conn, $sql);
            if (!$result) {
                // Intentar obtener más información del error
                $errorInfo = 'No se pudo obtener información adicional del error.';
                // Si hay una función para errores, usarla aquí
                throw new Exception('Error al ejecutar la consulta SQL. SQL: ' . $sql . '. Info adicional: ' . $errorInfo);
            }

            return $result;
        } catch (Exception $e) {
            throw new Exception('Error en getCargasLectivasNoLectivas: ' . $e->getMessage());
        }
    }
    public function generarReporteExcel($idsem, $codigo = null)
    {
        try {
            // Verificar si PHPExcel existe usando ruta absoluta
            $phpExcelPath = __DIR__ . '/../assets/PHPExcel.php';
            if (!file_exists($phpExcelPath)) {
                throw new Exception('La biblioteca PHPExcel no está disponible en assets/. Verifica la instalación. Ruta buscada: ' . $phpExcelPath);
            }
            require_once $phpExcelPath;

            $data = $this->getCargasLectivasNoLectivas($idsem, $codigo);

            if (!$data) {
                throw new Exception('No se pudieron obtener los datos de cargas.');
            }

            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $sheet = $objPHPExcel->getActiveSheet();
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Código');
            $sheet->setCellValue('C1', 'Actividad');
            $sheet->setCellValue('D1', 'Horas');
            $sheet->setCellValue('E1', 'Clasificación');
            $sheet->setCellValue('F1', 'Fecha Inicio');
            $sheet->setCellValue('G1', 'Fecha Fin');
            $row = 2;
            while ($row_data = fetchrow($data, -1)) {
                $sheet->setCellValue('A'.$row, isset($row_data['idtrab']) ? $row_data['idtrab'] : '');
                $sheet->setCellValue('B'.$row, isset($row_data['codigo']) ? $row_data['codigo'] : '');
                $sheet->setCellValue('C'.$row, isset($row_data['actividad']) ? $row_data['actividad'] : '');
                $sheet->setCellValue('D'.$row, isset($row_data['horas']) ? $row_data['horas'] : '');
                $sheet->setCellValue('E'.$row, isset($row_data['clasificacion']) ? $row_data['clasificacion'] : '');
                $sheet->setCellValue('F'.$row, isset($row_data['fecha_inicio']) ? $row_data['fecha_inicio'] : '');
                $sheet->setCellValue('G'.$row, isset($row_data['fecha_fin']) ? $row_data['fecha_fin'] : '');
                $row++;
            }
            cierra($data);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $filename = 'reporte_cargas_' . date('YmdHis') . '.xls';

            // Limpiar cualquier salida previa
            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
            header('Cache-Control: max-age=0');
            header('Pragma: public');
            $objWriter->save('php://output');
            exit;
        } catch (Exception $e) {
            // En caso de error, mostrar mensaje y detener
            if (ob_get_level()) {
                ob_end_clean();
            }
            header('Content-Type: text/html; charset=UTF-8');
            echo '<html><body>';
            echo '<h1>Error al generar el reporte Excel</h1>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><a href="javascript:history.back()">Volver</a></p>';
            echo '</body></html>';
            exit;
        }
    }
    public function getDownloadButtonWithJS($idsem, $codigo = null)
    {
        $codigoParam = $codigo ? "&codigo={$codigo}" : "";
        $js = "<script>
            function downloadReport() {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', 'download_excel.php?idsem={$idsem}{$codigoParam}', true);
                xhr.responseType = 'blob';
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        var blob = new Blob([xhr.response], {type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'});
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'reporte_cargas.xlsx';
                        link.click();
                    }
                };
                xhr.send();
            }
        </script>";
        $button = "<button onclick='downloadReport()'>Descargar Reporte Excel</button>";
        return $js . $button;
    }
}
?>