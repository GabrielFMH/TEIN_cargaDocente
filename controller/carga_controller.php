<?php

require_once BASE_PATH . '/model/carga_model.php';

class CargaController
{
    private $model;

    public function __construct()
    {
        $this->model = new CargaModel();
    }

    public function handleRequest()
    {
        $sex = isset($_GET['sesion']) ? $_GET['sesion'] : '';
        $this->handleSession($sex);

        $this->handlePostActions();

        $data = $this->prepareViewData($sex);

        require BASE_PATH . '/view/carga_view.php';
    }

    private function handleSession($sex)
    {
        require_once 'token_admin.php';
        require_once 'funciones.php';
        require_once 'funciones_fichamatricula.php';
        pageheader();
        callse($sex);
        session_name($sex);
        session_start();
        $tiempo = timeup($_SESSION['timer']);
        if ($tiempo) {
            $_SESSION['timer'] = time();
        } else {
            header("Location: logout.php?sesion=" . $sex);
            exit;
        }

        $gok=0;
        for ($l=1;$l<=$_SESSION['grupa0'];$l++)
        {
            if ($_SESSION['grupa'.$l]==200){$gok=1;}
        }

        if ($gok==0){header("Location: logout.php?sesion=".$sex); exit;}
        if ($_SESSION['tipo']!=3){header("Location: cambio.php?sesion=".$sex); exit;}

    }

    // Método para validar tipo de archivo
    private function validarTipoArchivo($archivo)
    {
        $tiposPermitidos = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $extensionesPermitidas = array('xls', 'xlsx');
        
        // Validar extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $extensionesPermitidas)) {
            return false;
        }
        
        // Validar tipo MIME
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            return false;
        }
        
        // Validar tamaño (5MB máximo)
        if ($archivo['size'] > 5242880) { // 5MB en bytes
            return false;
        }
        
        return true;
    }

    // Método para validar que solo entren números en campos específicos
    private function validarCampoNumerico($valor)
    {
        return is_numeric($valor) && preg_match('/^[0-9]+(\.[0-9]+)?$/', $valor);
    }

    // Método para verificar autorización por roles
    private function verificarAutorizacion($rolRequerido)
    {
        // Verificar si el usuario tiene el rol requerido
        for ($l=1; $l<=$_SESSION['grupa0']; $l++) {
            if ($_SESSION['grupa'.$l] == $rolRequerido) {
                return true;
            }
        }
        return false;
    }

    // Método para manejar combos dinámicos
    public function getActividadesPorTipo($tipoActividad)
    {
        // Esta función se llamaría via AJAX para actualizar combos dinámicamente
        $actividades = $this->model->getCatalogoActividades();
        $resultado = array();
        
        // Filtrar actividades según el tipo seleccionado
        while ($row = fetchrow($actividades, -1)) {
            if ($row[3] == $tipoActividad) { // asumiendo que la columna 3 es el tipo
                $resultado[] = array('id' => $row[0], 'nombre' => $row[1]);
            }
        }
        
        return json_encode($resultado);
    }

    // Método para exportar carga docente a PDF o Excel
    public function exportarCargaDocente($formato = 'pdf')
    {
        $idsem = isset($_GET["x"]) ? $_GET["x"] : (isset($_SESSION['idsemindiv']) ? $_SESSION['idsemindiv'] : null);
        
        if (!$idsem) {
            throw new Exception('No se especificó el semestre para exportar');
        }
        
        // Obtener datos de la carga docente
        $cursos = $this->model->getCursos($_SESSION['codper'], $idsem, 0);
        
        if ($formato == 'excel') {
            $this->exportarAExcel($cursos, $idsem);
        } else {
            $this->exportarAPDF($cursos, $idsem);
        }
    }

    // Método para exportar a Excel
    private function exportarAExcel($cursos, $idsem)
    {
        // Configurar headers para descarga de Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="carga_docente_'.$idsem.'.xls"');
        header('Cache-Control: max-age=0');
        
        echo "<table border='1'>";
        echo "<tr><th>CodCurso</th><th>Seccion</th><th>Curso</th><th>Semestre</th><th>Escuela</th><th>Hrs.</th></tr>";
        
        while ($row = fetchrow($cursos, -1)) {
            echo "<tr>";
            echo "<td>".$row[0]."</td>";
            echo "<td>".$row[1]."</td>";
            echo "<td>".$row[2]."</td>";
            echo "<td>".$row[3]."</td>";
            echo "<td>".$row[4]."</td>";
            echo "<td>".$row[6]."</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        exit;
    }

    // Método para exportar a PDF (simulación básica)
    private function exportarAPDF($cursos, $idsem)
    {
        // En una implementación real, aquí se usaría una librería como TCPDF o FPDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="carga_docente_'.$idsem.'.pdf"');
        
        echo "Reporte de Carga Docente - Semestre: ".$idsem."\n\n";
        echo "CodCurso\tSeccion\tCurso\tSemestre\tEscuela\tHrs.\n";
        
        while ($row = fetchrow($cursos, -1)) {
            echo $row[0]."\t".$row[1]."\t".$row[2]."\t".$row[3]."\t".$row[4]."\t".$row[6]."\n";
        }
        
        exit;
    }

    private function handlePostActions()
    {
        $idsem = isset($_GET["x"]) ? $_GET["x"] : (isset($_SESSION['idsemindiv']) ? $_SESSION['idsemindiv'] : null);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST["R1"]) && $_POST["R1"] > 0) {
                // Validar autorización
                if (!$this->verificarAutorizacion(200)) {
                    echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    return;
                }
                
                $dc = $_SESSION['car' . $_POST["R1"]];
                $dcEsTaex = $_SESSION['taex' . $_POST["R1"]];
                $result = $this->model->getCursoInfo($dc, $_SESSION['codigo'], $dcEsTaex);
                
                $cursoEncontrado = false;
                while ($row = fetchrow($result, -1)) {
                    $_SESSION['curso'] = isset($row[0]) ? $row[0] : '';
                    $_SESSION['idcurso'] = isset($row[1]) ? $row[1] : '';
                    $_SESSION['idsem'] = isset($row[2]) ? $row[2] : '';
                    $_SESSION['idcarga'] = $dc;
                    $_SESSION['iddepe'] = isset($row[4]) ? $row[4] : '';
                    $_SESSION['seccion'] = isset($row[5]) ? $row[5] : '';
                    $_SESSION['escuela'] = isset($row[7]) ? $row[7] : '';
                    $_SESSION['semestre'] = isset($row[8]) ? $row[8] : '';
                    $_SESSION['codcurso'] = isset($row[9]) ? $row[9] : '';
                    $_SESSION['validarb'] = isset($row[10]) ? $row[10] : '';
                    $_SESSION['conso'] = (isset($row[6]) && $row[6] > 0) ? 1 : 0;
                    $_SESSION['estaex'] = $dcEsTaex;
                    $cursoEncontrado = true;
                }
                
                if ($cursoEncontrado) {
                    // Usar la variable $sex que viene del método handleRequest
                    $sex = isset($_GET['sesion']) ? $_GET['sesion'] : (isset($_POST['sesion']) ? $_POST['sesion'] : '');
                    if (empty($sex)) {
                        // Si no se encuentra en GET/POST, usar de la sesión
                        $sex = session_name();
                    }
                    header("Location: unidad.php?sesion=" . $sex);
                    exit;
                } else {
                    echo "<script language='javascript'>alert('No se encontró información del curso seleccionado'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    exit;
                }
            }

            if (isset($_POST["vagregar"]) && $_POST["vagregar"] == "Agregar") {
                // Validar autorización
                if (!$this->verificarAutorizacion(200)) {
                    echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    return;
                }
                
                $_SESSION['codigox'] = $_POST["coduni"];
                
                // Validar campos numéricos
                if (!$this->validarCampoNumerico($_POST['vcant']) || 
                    !$this->validarCampoNumerico($_POST['vhoras']) || 
                    !$this->validarCampoNumerico($_POST['vcalif'])) {
                    echo "<script language='javascript'>alert('Los campos cantidad, horas y porcentaje deben ser numéricos'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    return;
                }
                
                if ($_POST["vacti"] == "Administrativa" && !in_array($_POST["vcalif"], [7, 8])) {
                    echo "<script language='javascript'>alert('La actividad Administrativa solo puede tener calificacion [Administración] o [Jefatura]'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                } else {
                    try {
                        $this->model->agregarTrabajo($_SESSION['codigox'], $_POST['vacti'], $_POST['vdacti'], $_POST['vimporta'], $_POST['vmedida'], $_POST['vcant'], $_POST['vhoras'], $_POST['vcalif'], $_POST['vmeta'], $_POST['datebox'], $_POST['datebox2'], $_POST['viddepe'], $_POST['vcanthoras'], $idsem);
                        echo "<script language='javascript'>alert('Actividad agregada correctamente'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    } catch (Exception $e) {
                        echo "<script language='javascript'>alert('Error al agregar actividad: " . $e->getMessage() . "'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    }
                }
            }

            if (isset($_POST["medit"]) && $_POST["medit"] == "Modificar") {
                // Validar autorización
                if (!$this->verificarAutorizacion(200)) {
                    echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    return;
                }
                
                $this->model->modificarEstadoActividades($_POST["mcodigo"], $_POST["msemestre"], $_POST["mestado_editar"]);
            }

            if (isset($_POST["modi_estado".$i]) && $_POST["modi_estado".$i] == "Modificar_Estado") {
                // Validar autorización
                if (!$this->verificarAutorizacion(200)) {
                    echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    return;
                }
                
                $_SESSION['codigox'] = $_POST["coduni"];
                $this->model->modificarEstadoTrabajoIndividual($_SESSION['codigox'], $_POST["vi".$i], $_POST["vestado_editar".$i]);
            }

            if (isset($_POST["addhistorial"]) && $_POST["addhistorial"] == "Registrar") {
                // Validar autorización
                if (!$this->verificarAutorizacion(200)) {
                    echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    return;
                }
                
                $_SESSION['codigox'] = $_POST["coduni"];
                
                // Validar campo numérico para porcentaje
                if (!$this->validarCampoNumerico($_POST['vporcentaje_historial'])) {
                    echo "<script language='javascript'>alert('El porcentaje debe ser numérico'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    return;
                }
                
                $this->model->registrarHistorial($_SESSION['codigox'], $_POST['vdactividad_historial'], $_POST['vnominfo_historial'], $_POST['vdirigido_historial'], $_POST['vcargo_historial'], $_POST['vremitente_historial'], $_POST['vdetalle_historial'], $_POST['vporcentaje_historial'], date("d/m/Y"));
            }

            if (isset($_POST["vn"]) && $_POST["vn"] > 0) {
                for ($i = 1; $i <= $_POST["vn"]; $i++) {
                    if (isset($_POST["de".$i]) && $_POST["de".$i] == "Eliminar") {
                        // Validar autorización
                        if (!$this->verificarAutorizacion(200)) {
                            echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                            return;
                        }
                        
                        $_SESSION['codigox'] = $_POST["coduni"];
                        $this->model->eliminarTrabajo($_SESSION['codigox'], $_POST["vi".$i], $_POST["msemestre"]);
                    } elseif (isset($_POST["dedit".$i]) && $_POST["dedit".$i] == "Editar") {
                        // Validar autorización
                        if (!$this->verificarAutorizacion(200)) {
                            echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                            return;
                        }
                        
                        $_SESSION['codigox'] = $_POST["coduni"];
                        
                        // Validar campos numéricos
                        if (!$this->validarCampoNumerico($_POST['vcant_editar'.$i]) || 
                            !$this->validarCampoNumerico($_POST['vhoras_editar'.$i]) || 
                            !$this->validarCampoNumerico($_POST['vcalif_editar'.$i]) ||
                            !$this->validarCampoNumerico($_POST['vporcentaje_editar'.$i])) {
                            echo "<script language='javascript'>alert('Los campos numéricos deben contener solo números'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                            return;
                        }
                        
                        $this->model->editarTrabajo($_SESSION['codigox'], $_POST["vi".$i], $_POST['vacti_editar'.$i], $_POST['vdacti_editar'.$i], $_POST['vimporta_editar'.$i], $_POST['vmedida_editar'.$i], $_POST['vcant_editar'.$i], $_POST['vhoras_editar'.$i], $_POST['vcalif_editar'.$i], $_POST['vmeta_editar'.$i], $_POST['dateboxx'.$i], $_POST['dateboxx2'.$i], $_POST['vporcentaje_editar'.$i]);
                    } elseif (isset($_POST["delim".$i]) && $_POST["delim".$i] == "Finalizar") {
                        // Validar autorización
                        if (!$this->verificarAutorizacion(200)) {
                            echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                            return;
                        }
                        
                        $this->model->finalizarTrabajo($_POST["vi".$i], $idsem, $_POST["coduni"], 1);
                    } elseif (isset($_POST["rever".$i]) && $_POST["rever".$i] == "Revertir") {
                        // Validar autorización
                        if (!$this->verificarAutorizacion(200)) {
                            echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                            return;
                        }
                        
                        $this->model->revertirTrabajo($_POST["vi".$i], $idsem, $_POST["coduni"], 0);
                    }
                }
            }

            if (isset($_POST["R1"]) && $_POST["R1"] > 0) {
                // Validar autorización
                if (!$this->verificarAutorizacion(200)) {
                    echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    return;
                }
                
                $dc = $_SESSION['car' . $_POST["R1"]];
                $dcEsTaex = $_SESSION['taex' . $_POST["R1"]];
                $result = $this->model->getCursoInfo($dc, $_SESSION['codigo'], $dcEsTaex);
                while ($row = fetchrow($result, -1)) {
                    $_SESSION['curso'] = $row[0];
                    $_SESSION['idcurso'] = $row[1];
                    $_SESSION['idsem'] = $row[2];
                    $_SESSION['idcarga'] = $dc;
                    $_SESSION['iddepe'] = $row[4];
                    $_SESSION['seccion'] = $row[5];
                    $_SESSION['escuela'] = $row[7];
                    $_SESSION['semestre'] = $row[8];
                    $_SESSION['codcurso'] = $row[9];
                    $_SESSION['validarb'] = $row[10];
                    $_SESSION['conso'] = ($row[6] > 0) ? 1 : 0;
                    $_SESSION['estaex'] = $dcEsTaex;
                }
                header("Location: unidad.php?sesion=" . $sex);
                exit;
            }
        }
        
        // Manejar solicitudes AJAX para combos dinámicos
        if (isset($_GET['accion']) && $_GET['accion'] == 'get_actividades') {
            $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';
            echo $this->getActividadesPorTipo($tipo);
            exit;
        }
        
        // Manejar exportaciones
        if (isset($_GET['exportar'])) {
            $formato = isset($_GET['formato']) ? $_GET['formato'] : 'pdf';
            $this->exportarCargaDocente($formato);
        }
    }

    private function prepareViewData($sex)
    {
        $data = [];
        $data['sex'] = $sex;
        $data['idsem'] = isset($_GET["x"]) ? $_GET["x"] : (isset($_SESSION['idsemindiv']) ? $_SESSION['idsemindiv'] : null);
        $data['esSemestreTaex'] = isset($_GET["tx"]) ? $_GET["tx"] : 0;
        $data['name'] = $_SESSION['name'];
        $data['codigo'] = $_SESSION['codigo'];
        $data['codper'] = $_SESSION['codper'];
        $data['grupa0'] = $_SESSION['grupa0'];
        for ($l=1;$l<=$data['grupa0'];$l++){
            $data['grupa'.$l] = $_SESSION['grupa'.$l];
        }
        $data['wf'] = isset($_GET['wf']) ? $_GET['wf'] : 0;
        $data['wifi'] = isset($_SESSION['wifi']) ? $_SESSION['wifi'] : '';
        $data['tr'] = isset($_GET['tr']) ? $_GET['tr'] : false;

        if ($_SESSION['check'] > 0) {
            $cargas = $this->model->getCarga($_SESSION['codigo']);
            while ($row = fetchrow($cargas, -1)) {
                if ($row[4] > 0) {
                    $this->model->updateCarga($row[0], $row[1], $row[2], $row[3], $row[5], $_SESSION['codigo']);
                }
            }
            $_SESSION['check'] = 0;
        }

        $data['semestres'] = $this->model->getSemestres($_SESSION['codper']);
        $data['semestre_info'] = $this->model->getSemestreInfo($data['idsem'], $data['esSemestreTaex']);
        $data['director_depe'] = $this->model->getDirectorIdDepe($_SESSION['codigo']);
        
        $cursos = $this->model->getCursos($_SESSION['codper'], $data['idsem'], $data['esSemestreTaex']);
        $ji=0;
        while ($row =fetchrow($cursos,-1))
        {
            $ji++;
            $_SESSION['car'.$ji]=$row[5];
            $_SESSION['taex'.$ji]=$row[9];
            $_SESSION['idsemindiv']=$row[7];
        }
        $_SESSION['car0']=$ji;
        // Rewind the cursor to be used in the view
        fetchrow($cursos, 0, true);
        $data['cursos'] = $cursos;

        if($data['tr']){
            $trab_doc = $this->model->getTrabajoIndividualDoc($_SESSION['codigo'], $data['idsem']);
            if(numrow($trab_doc) > 0){
                $data['trabajo_individual_doc'] = $trab_doc;
            }
        }

        $docente_dato_eval = $this->model->getDocenteDatoEvaluacion($_SESSION['codigo']);
        if(numrow($docente_dato_eval) > 0){
            $row_email = fetchrow($docente_dato_eval, -1);
            $pIdDocenteDatoEvaluacion = $row_email[0];
            $data['ultimos_accesos'] = $this->model->getUltimosAccesos($pIdDocenteDatoEvaluacion, $data['idsem']);
        }

        return $data;
    }
}
?>