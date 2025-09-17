<?php

// Include error logger for comprehensive error logging
require_once __DIR__ . '/../error_logger.php';

class CargaModel
{
    private $conn;

    public function __construct()
    {
        require_once 'funciones.php';
        $this->conn = conex();
        if (!$this->conn) {
            throw new Exception('No se pudo establecer conexión con la base de datos');
        }
    }

    public function __destruct()
    {
        if ($this->conn) {
            noconex($this->conn);
        }
    }

    private function execute_query($sql)
    {
        try {
            $result = luis($this->conn, $sql);
            if (!$result) {
                // Intentar obtener el último error de la conexión
                $error_msg = 'Error desconocido en la ejecución de la consulta';
                if (function_exists('mssql_get_last_message')) {
                    $error_msg = mssql_get_last_message();
                } elseif (function_exists('sqlsrv_errors')) {
                    $errors = sqlsrv_errors();
                    if ($errors) {
                        $error_msg = $errors[0]['message'];
                    }
                }
                throw new Exception('Error al ejecutar consulta SQL: ' . $error_msg . '. SQL: ' . $sql);
            }
            cierra($result);
        } catch (Exception $e) {
            // Log del error para depuración usando el sistema personalizado
            $timestamp = date('Y-m-d H:i:s');
            $errorMessage = sprintf(
                "[%s] ERROR en execute_query: %s | SQL: %s\n" . str_repeat('-', 80) . "\n",
                $timestamp,
                $e->getMessage(),
                $sql
            );
            writeToLogFile($errorMessage, __FILE__);
            throw $e;
        }
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

    public function validacionHorasLectivas($codigo, $idsem, $horasNuevas, $idtrabExcluir = null)
    {
        // El tipo de actividad 'Lectiva' se identifica por el valor 'Lectiva' en la columna tipo_actividad.
        $sql = "SELECT ISNULL(SUM(horas), 0) as total_horas_lectivas 
                FROM trab 
                WHERE codigo = {$codigo} 
                AND idsem = {$idsem} 
                AND tipo_actividad = 'Lectiva'";

        // Si estamos editando una actividad, la excluimos de la suma total actual.
        if ($idtrabExcluir) {
            $sql .= " AND idtrab != {$idtrabExcluir}";
        }
        
        $result = luis($this->conn, $sql);
        $row = fetchrow($result, -1);
        $totalActual = (float)$row[0];
        cierra($result);
        
        $respuesta = [
            'valido' => true,
            'mensaje' => ''
        ];

        if (($totalActual + $horasNuevas) > 10) {
            $respuesta['valido'] = false;
            $respuesta['mensaje'] = 'La suma de horas para actividades de tipo "Lectiva" no puede superar las 10 horas semanales. Horas actuales: ' . $totalActual;
        }
        
        return $respuesta;
    }

    // Método para generar número automático de informe
    public function generarNumeroInforme($codigo, $idsem)
    {
        $sql = "SELECT ISNULL(MAX(numero_informe), 0) + 1 FROM trab WHERE codigo = {$codigo} AND idsem = {$idsem}";
        $result = luis($this->conn, $sql);
        $row = fetchrow($result, -1);
        if (!$row) {
            $numero = 1;
        } else {
            $numero = $row[0];
            if ($numero === null) {
                $numero = 1;
            }
        }
        cierra($result);
        return (int)$numero;
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

    public function agregarTrabajo($codigox, $vacti, $vdacti, $vimporta, $vmedida, $vcant, $vhoras, $vcalif, $vmeta, $vdatebox, $vdatebox2, $viddepe, $vcanthoras, $idsem, $vtipo_actividad = '', $vdetalle_actividad = '', $vdependencia = '')
    {
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

        try {
            // Intentar primero con el procedimiento almacenado
            $this->agregarTrabajoConProcedimiento($codigox, $vacti, $vdacti, $vimporta, $vmedida, $vcant, $vhoras, $vcalif, $vmeta, $vdatebox, $vdatebox2, $viddepe, $vcanthoras, $idsem, $vtipo_actividad, $vdetalle_actividad, $vdependencia);
        } catch (Exception $e) {
            // Log del error usando el sistema personalizado
            $timestamp = date('Y-m-d H:i:s');
            $errorMessage = sprintf(
                "[%s] PROCEDIMIENTO FALLÓ - Intentando inserción directa: %s\n" . str_repeat('-', 80) . "\n",
                $timestamp,
                $e->getMessage()
            );
            writeToLogFile($errorMessage, __FILE__);
            // Si el procedimiento falla, intentar inserción directa
            $this->agregarTrabajoDirecto($codigox, $vacti, $vdacti, $vimporta, $vmedida, $vcant, $vhoras, $vcalif, $vmeta, $vdatebox, $vdatebox2, $viddepe, $vcanthoras, $idsem, $vtipo_actividad, $vdetalle_actividad, $vdependencia);
        }
    }

    private function agregarTrabajoConProcedimiento($codigox, $vacti, $vdacti, $vimporta, $vmedida, $vcant, $vhoras, $vcalif, $vmeta, $vdatebox, $vdatebox2, $viddepe, $vcanthoras, $idsem, $vtipo_actividad, $vdetalle_actividad, $vdependencia)
    {
        // Verificar que el procedimiento existe antes de ejecutarlo
        $checkProc = "SELECT OBJECT_ID('trabiagregar_v2') as proc_id";
        $result = luis($this->conn, $checkProc);
        if (!$result) {
            throw new Exception('No se pudo verificar la existencia del procedimiento trabiagregar_v2');
        }
        $row = fetchrow($result, -1);
        cierra($result);

        if (!$row[0]) {
            throw new Exception('El procedimiento almacenado trabiagregar_v2 no existe en la base de datos');
        }

        // Preparar parámetros con escape adecuado y manejo de null
        $params = array(
            'codigox' => $codigox,
            'vacti' => $vacti !== null ? str_replace("'", "''", $vacti) : '',
            'vdacti' => $vdacti !== null ? str_replace("'", "''", $vdacti) : '',
            'vimporta' => $vimporta !== null ? str_replace("'", "''", $vimporta) : '',
            'vmedida' => $vmedida !== null ? str_replace("'", "''", $vmedida) : '',
            'vcant' => $vcant,
            'vhoras' => $vhoras,
            'vcalif' => $vcalif,
            'vmeta' => $vmeta !== null ? str_replace("'", "''", $vmeta) : '',
            'vdatebox' => $vdatebox,
            'vdatebox2' => $vdatebox2,
            'porcentaje' => $vcalif,  // porcentaje is vcalif
            'viddepe' => $viddepe,
            'vcanthoras' => $vcanthoras,
            'idsem' => $idsem,
            'vtipo_actividad' => $vtipo_actividad !== null ? str_replace("'", "''", $vtipo_actividad) : '',
            'vdetalle_actividad' => $vdetalle_actividad !== null ? str_replace("'", "''", $vdetalle_actividad) : '',
            'vdependencia' => $vdependencia !== null ? str_replace("'", "''", $vdependencia) : ''
        );

        $sql = "exec trabiagregar_v2 {$params['codigox']}, '{$params['vacti']}', '{$params['vdacti']}', '{$params['vimporta']}', '{$params['vmedida']}', {$params['vcant']}, {$params['vhoras']}, {$params['vcalif']}, '{$params['vmeta']}', '{$params['vdatebox']}', '{$params['vdatebox2']}', {$params['porcentaje']}, {$params['viddepe']}, {$params['vcanthoras']}, {$params['idsem']}, '{$params['vtipo_actividad']}', '{$params['vdetalle_actividad']}', '{$params['vdependencia']}'";

        // Log detallado usando el sistema personalizado
        $timestamp = date('Y-m-d H:i:s');
        $logMessage1 = sprintf(
            "[%s] AGREGAR TRABAJO - Parámetros preparados para trabiagregar_v2: %s\n" . str_repeat('-', 80) . "\n",
            $timestamp,
            json_encode($params, JSON_UNESCAPED_UNICODE)
        );
        writeToLogFile($logMessage1, __FILE__);

        $logMessage2 = sprintf(
            "[%s] AGREGAR TRABAJO - SQL completo: %s\n" . str_repeat('-', 80) . "\n",
            $timestamp,
            $sql
        );
        writeToLogFile($logMessage2, __FILE__);

        $this->execute_query($sql);

        // Verificar que el registro se insertó
        $checkInsert = "SELECT TOP 1 idtrab FROM trab WHERE codigo = {$codigox} AND actividad = '{$params['vacti']}' ORDER BY idtrab DESC";
        $resultCheck = luis($this->conn, $checkInsert);
        if ($resultCheck) {
            $rowCheck = fetchrow($resultCheck, -1);
            if ($rowCheck && $rowCheck[0]) {
                $timestamp = date('Y-m-d H:i:s');
                $successMessage = sprintf(
                    "[%s] AGREGAR TRABAJO - Registro insertado exitosamente con ID: %s\n" . str_repeat('-', 80) . "\n",
                    $timestamp,
                    $rowCheck[0]
                );
                writeToLogFile($successMessage, __FILE__);
            } else {
                $timestamp = date('Y-m-d H:i:s');
                $warningMessage = sprintf(
                    "[%s] AGREGAR TRABAJO - ADVERTENCIA: No se encontró el registro después de la inserción\n" . str_repeat('-', 80) . "\n",
                    $timestamp
                );
                writeToLogFile($warningMessage, __FILE__);
            }
            cierra($resultCheck);
        }
    }

    private function agregarTrabajoDirecto($codigox, $vacti, $vdacti, $vimporta, $vmedida, $vcant, $vhoras, $vcalif, $vmeta, $vdatebox, $vdatebox2, $viddepe, $vcanthoras, $idsem, $vtipo_actividad, $vdetalle_actividad, $vdependencia)
    {
        try {
            // Generar número automático de informe
            $numero_informe = $this->generarNumeroInforme($codigox, $idsem);

            // Preparar parámetros con escape adecuado y manejo de null
            $params = array(
                'codigo' => $codigox,
                'actividad' => $vacti !== null ? str_replace("'", "''", $vacti) : '',
                'dactividad' => $vdacti !== null ? str_replace("'", "''", $vdacti) : '',
                'importancia' => $vimporta !== null ? str_replace("'", "''", $vimporta) : '',
                'medida' => $vmedida !== null ? str_replace("'", "''", $vmedida) : '',
                'cant' => $vcant,
                'horas' => $vhoras,
                'calif' => $vcalif,
                'meta' => $vmeta !== null ? str_replace("'", "''", $vmeta) : '',
                'fecha_inicio' => $vdatebox,
                'fecha_fin' => $vdatebox2,
                'porcentaje' => $vcalif,
                'iddepe' => $viddepe,
                'numero_informe' => $numero_informe,
                'idsem' => $idsem,
                'tipo_actividad' => $vtipo_actividad !== null ? str_replace("'", "''", $vtipo_actividad) : '',
                'detalle_actividad' => $vdetalle_actividad !== null ? str_replace("'", "''", $vdetalle_actividad) : '',
                'dependencia' => $vdependencia !== null ? str_replace("'", "''", $vdependencia) : ''
            );

            $sql = "INSERT INTO trab (codigo, idsem, actividad, dactividad, importancia, medida, cant, horas, calif, meta, otros, fecha_inicio, fecha_fin, porcentaje, iddepe, numero_informe, estado, fecha_registro, fecha_modificacion, califNuevo, tipo_actividad, detalle_actividad, dependencia)
                    VALUES ({$params['codigo']}, {$params['idsem']}, '{$params['actividad']}', '{$params['dactividad']}', '{$params['importancia']}', '{$params['medida']}', {$params['cant']}, {$params['horas']}, {$params['calif']}, '{$params['meta']}', '', '{$params['fecha_inicio']}', '{$params['fecha_fin']}', {$params['porcentaje']}, {$params['iddepe']}, {$params['numero_informe']}, 1, GETDATE(), GETDATE(), (10+{$params['calif']}), '{$params['tipo_actividad']}', '{$params['detalle_actividad']}', '{$params['dependencia']}')";

            // Log de inserción directa usando el sistema personalizado
            $timestamp = date('Y-m-d H:i:s');
            $directInsertMessage = sprintf(
                "[%s] AGREGAR TRABAJO - Insertando directamente en trab: %s\n" . str_repeat('-', 80) . "\n",
                $timestamp,
                $sql
            );
            writeToLogFile($directInsertMessage, __FILE__);

            $this->execute_query($sql);

            // Verificar que el registro se insertó
            $checkInsert = "SELECT TOP 1 idtrab FROM trab WHERE codigo = {$codigox} AND actividad = '{$params['actividad']}' ORDER BY idtrab DESC";
            $resultCheck = luis($this->conn, $checkInsert);
            if ($resultCheck) {
                $rowCheck = fetchrow($resultCheck, -1);
                if ($rowCheck && $rowCheck[0]) {
                    $timestamp = date('Y-m-d H:i:s');
                    $directSuccessMessage = sprintf(
                        "[%s] AGREGAR TRABAJO - Registro insertado directamente exitosamente con ID: %s\n" . str_repeat('-', 80) . "\n",
                        $timestamp,
                        $rowCheck[0]
                    );
                    writeToLogFile($directSuccessMessage, __FILE__);
                } else {
                    throw new Exception('No se pudo verificar la inserción directa');
                }
                cierra($resultCheck);
            }

        } catch (Exception $e) {
            $timestamp = date('Y-m-d H:i:s');
            $directErrorMessage = sprintf(
                "[%s] AGREGAR TRABAJO - Error en inserción directa: %s\n" . str_repeat('-', 80) . "\n",
                $timestamp,
                $e->getMessage()
            );
            writeToLogFile($directErrorMessage, __FILE__);
            throw new Exception('Error en inserción directa a la tabla trab: ' . $e->getMessage());
        }
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

    public function editarTrabajo($codigox, $idtrab, $vacti_editar, $vdacti_editar, $vimporta_editar, $vmedida_editar, $vcant_editar, $vhoras_editar, $vcalif_editar, $vmeta_editar, $vdatebox_editar, $vdatebox2_editar, $vporcentaje_editar, $vtipo_actividad_editar = '', $vdetalle_actividad_editar = '', $vdependencia_editar = '')
    {

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

        // Validar horas lectivas si la actividad es de tipo 'Lectiva'
        if ($vtipo_actividad_editar == 'Lectiva') {
            $validacionLectiva = $this->validacionHorasLectivas($codigox, $idsem, $vhoras_editar, $idtrab);
            if (!$validacionLectiva['valido']) {
                throw new Exception($validacionLectiva['mensaje']);
            }
        }

        try {
            // Preparar parámetros con escape adecuado y manejo de null
            $vacti_editar_safe = $vacti_editar !== null ? str_replace("'", "''", $vacti_editar) : '';
            $vdacti_editar_safe = $vdacti_editar !== null ? str_replace("'", "''", $vdacti_editar) : '';
            $vimporta_editar_safe = $vimporta_editar !== null ? str_replace("'", "''", $vimporta_editar) : '';
            $vmedida_editar_safe = $vmedida_editar !== null ? str_replace("'", "''", $vmedida_editar) : '';
            $vmeta_editar_safe = $vmeta_editar !== null ? str_replace("'", "''", $vmeta_editar) : '';
            $vdatebox_editar_safe = $vdatebox_editar !== null ? $vdatebox_editar : '';
            $vdatebox2_editar_safe = $vdatebox2_editar !== null ? $vdatebox2_editar : '';
            $vtipo_actividad_editar_safe = $vtipo_actividad_editar !== null ? str_replace("'", "''", $vtipo_actividad_editar) : '';
            $vdetalle_actividad_editar_safe = $vdetalle_actividad_editar !== null ? str_replace("'", "''", $vdetalle_actividad_editar) : '';
            $vdependencia_editar_safe = $vdependencia_editar !== null ? str_replace("'", "''", $vdependencia_editar) : '';

            // Log detallado de parámetros enviados al procedimiento
            $parametrosLog = [
                'codigox' => $codigox,
                'idtrab' => $idtrab,
                'vacti_editar' => $vacti_editar,
                'vdacti_editar' => $vdacti_editar,
                'vimporta_editar' => $vimporta_editar,
                'vmedida_editar' => $vmedida_editar,
                'vcant_editar' => $vcant_editar,
                'vhoras_editar' => $vhoras_editar,
                'vcalif_editar' => $vcalif_editar,
                'vmeta_editar' => $vmeta_editar,
                'vdatebox_editar' => $vdatebox_editar,
                'vdatebox2_editar' => $vdatebox2_editar,
                'vporcentaje_editar' => $vporcentaje_editar,
                'vtipo_actividad_editar' => $vtipo_actividad_editar,
                'vdetalle_actividad_editar' => $vdetalle_actividad_editar,
                'vdependencia_editar' => $vdependencia_editar
            ];

            // Log detallado usando el sistema de error_logger.php
            $timestamp = date('Y-m-d H:i:s');
            $logMessage1 = sprintf(
                "[%s] EDITAR TRABAJO - Parámetros originales enviados: %s\n" . str_repeat('-', 80) . "\n",
                $timestamp,
                json_encode($parametrosLog, JSON_UNESCAPED_UNICODE)
            );
            writeToLogFile($logMessage1, __FILE__);

            $parametrosProcesadosLog = [
                'codigox' => $codigox,
                'idtrab' => $idtrab,
                'vacti_editar_safe' => $vacti_editar_safe,
                'vdacti_editar_safe' => $vdacti_editar_safe,
                'vimporta_editar_safe' => $vimporta_editar_safe,
                'vmedida_editar_safe' => $vmedida_editar_safe,
                'vcant_editar' => $vcant_editar,
                'vhoras_editar' => $vhoras_editar,
                'vcalif_editar' => $vcalif_editar,
                'vmeta_editar_safe' => $vmeta_editar_safe,
                'vdatebox_editar_safe' => $vdatebox_editar_safe,
                'vdatebox2_editar_safe' => $vdatebox2_editar_safe,
                'vporcentaje_editar' => $vporcentaje_editar,
                'vtipo_actividad_editar_safe' => $vtipo_actividad_editar_safe,
                'vdetalle_actividad_editar_safe' => $vdetalle_actividad_editar_safe,
                'vdependencia_editar_safe' => $vdependencia_editar_safe
            ];

            $logMessage2 = sprintf(
                "[%s] EDITAR TRABAJO - Parámetros procesados para SQL: %s\n" . str_repeat('-', 80) . "\n",
                $timestamp,
                json_encode($parametrosProcesadosLog, JSON_UNESCAPED_UNICODE)
            );
            writeToLogFile($logMessage2, __FILE__);

            $sql = "exec sp_editar_trabindiv {$codigox}, {$idtrab}, '{$vacti_editar_safe}', '{$vdacti_editar_safe}', '{$vimporta_editar_safe}', '{$vmedida_editar_safe}', {$vcant_editar}, {$vhoras_editar}, {$vcalif_editar}, '{$vmeta_editar_safe}', '{$vdatebox_editar_safe}', '{$vdatebox2_editar_safe}', {$vporcentaje_editar}, '{$vtipo_actividad_editar_safe}', '{$vdetalle_actividad_editar_safe}', '{$vdependencia_editar_safe}'";

            $logMessage3 = sprintf(
                "[%s] EDITAR TRABAJO - SQL completo a ejecutar: %s\n" . str_repeat('-', 80) . "\n",
                $timestamp,
                $sql
            );
            writeToLogFile($logMessage3, __FILE__);

            $this->execute_query($sql);

            $logMessage4 = sprintf(
                "[%s] EDITAR TRABAJO - SQL ejecutado exitosamente para idtrab: %s\n" . str_repeat('-', 80) . "\n",
                $timestamp,
                $idtrab
            );
            writeToLogFile($logMessage4, __FILE__);
        } catch (Exception $e) {
            $timestamp = date('Y-m-d H:i:s');
            $errorMessage = sprintf(
                "[%s] EDITAR TRABAJO - ERROR al ejecutar: %s | SQL: %s\n" . str_repeat('-', 80) . "\n",
                $timestamp,
                $e->getMessage(),
                (isset($sql) ? $sql : 'No generado')
            );
            writeToLogFile($errorMessage, __FILE__);
            throw new Exception('Error al ejecutar el procedimiento almacenado sp_editar_trabindiv: ' . $e->getMessage());
        }
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
                        t.dependencia,
                        t.detalle_actividad,
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
            $sheet->setCellValue('E1', 'Dependencia');
            $sheet->setCellValue('F1', 'Detalle Actividad');
            $sheet->setCellValue('G1', 'Clasificación');
            $sheet->setCellValue('H1', 'Fecha Inicio');
            $sheet->setCellValue('I1', 'Fecha Fin');
            $row = 2;
            while ($row_data = fetchrow($data, -1)) {
                $sheet->setCellValue('A'.$row, isset($row_data[0]) ? $row_data[0] : '');
                $sheet->setCellValue('B'.$row, isset($row_data[1]) ? $row_data[1] : '');
                $sheet->setCellValue('C'.$row, isset($row_data[2]) ? $row_data[2] : '');
                $sheet->setCellValue('D'.$row, isset($row_data[3]) ? $row_data[3] : '');
                $sheet->setCellValue('E'.$row, isset($row_data[6]) ? $row_data[6] : '');
                $sheet->setCellValue('F'.$row, isset($row_data[7]) ? $row_data[7] : '');
                $sheet->setCellValue('G'.$row, isset($row_data[8]) ? $row_data[8] : '');
                $sheet->setCellValue('H'.$row, isset($row_data[4]) ? $row_data[4] : '');
                $sheet->setCellValue('I'.$row, isset($row_data[5]) ? $row_data[5] : '');
                $row++;
            }
            cierra($data);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $filename = 'reporte_cargas_' . date('YmdHis') . '.xlsx';

            // Limpiar cualquier salida previa
            if (ob_get_level()) {
                ob_end_clean();
            }

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
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


    // Método para insertar datos de carga desde Excel
    public function insertarCargaDesdeExcel($excelData, $codigoUsuario, $codper)
    {
        try {
            foreach ($excelData as $row) {
                // Columnas del Excel en este orden:
                // 0: idcurso, 1: idsem, 2: seccion, 3: iddepe, 4: FechaExamen, 5: FechaAlternativa

                $idcurso = isset($row[0]) ? trim($row[0]) : null;
                $idsem = isset($row[1]) ? trim($row[1]) : null;
                $seccion = isset($row[2]) ? trim($row[2]) : '';
                $iddepe = isset($row[3]) ? trim($row[3]) : null;
                $fechaExamen = isset($row[4]) ? trim($row[4]) : null;
                $fechaAlternativa = isset($row[5]) ? trim($row[5]) : null;

                // Valores por defecto
                $activo = 1;
                $usuario = $codigoUsuario;
                $hora = date('Y-m-d H:i:s');
                $codigo = $codigoUsuario; // Usar codigoUsuario como codigo

                // Validar campos requeridos
                if (!$idcurso || !$idsem || !$codigoUsuario) {
                    continue; // Saltar filas incompletas
                }

                // Escapar valores para prevenir SQL injection
                $seccion = str_replace("'", "''", $seccion);
                
                // Construir la consulta SQL con manejo correcto de NULL y comillas
                $sql = "INSERT INTO dbo.carga (codper, idcurso, idsem, seccion, iddepe, codigo, [Fecha Examen], [Fecha Alternativa], activo, usuario, hora)
                        VALUES ($codper, $idcurso, $idsem, '$seccion', " .
                        ($iddepe ? $iddepe : "NULL") . ", $codigo, " .
                        ($fechaExamen ? "'$fechaExamen'" : "NULL") . ", " .
                        ($fechaAlternativa ? "'$fechaAlternativa'" : "NULL") . ", $activo, '$usuario', '$hora')";

                $this->execute_query($sql);
            }
        } catch (Exception $e) {
            // Registrar el error para depuración usando el sistema personalizado
            $timestamp = date('Y-m-d H:i:s');
            $excelErrorMessage = sprintf(
                "[%s] INSERTAR CARGA DESDE EXCEL - Error: %s\n" . str_repeat('-', 80) . "\n",
                $timestamp,
                $e->getMessage()
            );
            writeToLogFile($excelErrorMessage, __FILE__);
            throw new Exception('Error al insertar datos de carga: ' . $e->getMessage());
        }
    }

    // Método para obtener autoridades académicas del docente
    public function getAutoridadesAcademicas($codigo)
    {
        require_once 'funciones.php';
        $autoridades = getAutoridadesAcademicas();

        $jefes = array();
        $decanos = array();
        $directores = array();
        $coordinadores = array();
        $secretarios = array();
        $vicecargos = array();
        $rector = array();

        foreach ($autoridades as $autoridad) {
            $nombre_completo = $autoridad['nombres'] . ' ' . $autoridad['apellido_paterno'] . ' ' . $autoridad['apellido_materno'];
            $cargo_lower = strtolower($autoridad['cargo']);

            if (strpos($cargo_lower, 'jefe') !== false) {
                $jefes[] = $nombre_completo . ' (' . $autoridad['cargo'] . ')';
            } elseif (strpos($cargo_lower, 'decano') !== false) {
                $decanos[] = $nombre_completo . ' (' . $autoridad['cargo'] . ')';
            } elseif (strpos($cargo_lower, 'director') !== false) {
                $directores[] = $nombre_completo . ' (' . $autoridad['cargo'] . ')';
            } elseif (strpos($cargo_lower, 'coordinador') !== false) {
                $coordinadores[] = $nombre_completo . ' (' . $autoridad['cargo'] . ')';
            } elseif (strpos($cargo_lower, 'secretario') !== false) {
                $secretarios[] = $nombre_completo . ' (' . $autoridad['cargo'] . ')';
            } elseif (strpos($cargo_lower, 'vicerrector') !== false) {
                $vicecargos[] = $nombre_completo . ' (' . $autoridad['cargo'] . ')';
            } elseif (strpos($cargo_lower, 'rector') !== false) {
                $rector[] = $nombre_completo . ' (' . $autoridad['cargo'] . ')';
            }
        }

        return array(
            'jefes' => $jefes,
            'decanos' => $decanos,
            'directores' => $directores,
            'coordinadores' => $coordinadores,
            'secretarios' => $secretarios,
            'vicecargos' => $vicecargos,
            'rector' => $rector
        );
    }
}
?>