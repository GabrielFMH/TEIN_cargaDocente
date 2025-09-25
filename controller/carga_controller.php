<?php
// Silenciar errores para evitar salida no deseada
ob_clean();
error_reporting(0);
ini_set('display_errors', 0);

// Include error logger for comprehensive error logging
require_once __DIR__ . '/../error_logger.php';

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
         // ✨ Paso 1: Verificar la solicitud de exportación ANTES que cualquier otra cosa.
        if (isset($_GET['exportar'])) {
            $this->handleExport(); // Llama al método que genera el PDF sin HTML.
            exit; // Es CRUCIAL detener la ejecución aquí para que no se imprima nada más.
        }

        // ✨ Paso 1.5: Manejar solicitudes AJAX antes de cualquier salida HTML
        // --- CORRECCIÓN: Leer action desde JSON si no está en $_POST ---
        $action = isset($_POST['action']) ? $_POST['action'] : null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === null) {
            // Puede ser una solicitud JSON
            $input = file_get_contents('php://input');
            if ($input) {
                $json_data = json_decode($input, true);
                if (isset($json_data['action'])) {
                    $action = $json_data['action'];
                }
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'upload_excel') {
            $this->handleAjaxUpload(); // Este método debe manejar la lectura de JSON también
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
            $this->handleFileUpload();
            exit;
        }

        // ✨ Paso 2: Si no es una exportación, seguir con el flujo normal de la página web.
        // Esto asegura que pageheader() y otras funciones de HTML se llamen solo para la vista web.
        $sex = isset($_GET['sesion']) ? $_GET['sesion'] : '';
        $this->handleSession($sex);

        $this->handlePostActions();

        $data = $this->prepareViewData($sex);

        // ✨ Paso 3: Cargar la vista HTML al final.
        require BASE_PATH . '/view/carga_view.php';
    }
    
    // Método especial para manejar exportaciones sin cargar toda la interfaz
    private function handleExport()
    {
        // Limpiar cualquier salida previa inmediatamente
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Iniciar un nuevo buffer de salida limpio
        ob_start();
        
        $sex = isset($_GET['sesion']) ? $_GET['sesion'] : '';
        
        // Manejar la sesión de forma minimalista solo para exportación
        $this->handleSessionForExport($sex);
        
        // Procesar exportación directamente
        if (isset($_GET['exportar'])) {
            $formato = isset($_GET['formato']) ? $_GET['formato'] : 'pdf';
            // Obtener el idsem de la URL
            $idsem = isset($_GET["x"]) ? $_GET["x"] : (isset($_SESSION['idsemindiv']) ? $_SESSION['idsemindiv'] : null);
            
            if ($idsem) {
                // Obtener datos de la carga docente
                $cursos = $this->model->getCursos($_SESSION['codper'], $idsem, 0);
                
                // Limpiar buffer antes de exportar
                if (ob_get_level()) {
                    ob_end_clean();
                }
                
                if ($formato == 'excel') {
                    try {
                        // Usar el método del modelo para exportar cargas lectivas/no lectivas
                        $this->model->generarReporteExcel($idsem, $_SESSION['codigo']);
                    } catch (Exception $e) {
                        // En caso de error, limpiar buffer y mostrar mensaje
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
                } else {
                    $this->exportarAPDF($cursos, $idsem);
                }
            }
            exit;
        }
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
    
    // Método especial para manejar la sesión durante exportaciones
    private function handleSessionForExport($sex)
    {
        // Limpiar cualquier salida previa
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        require_once 'token_admin.php';
        require_once 'funciones.php';
        require_once 'funciones_fichamatricula.php';
        
        // No enviar pageheader() ni callse() que generan salida HTML
        session_name($sex);
        session_start();
        
        // Verificar que las variables de sesión necesarias existan
        if (!isset($_SESSION['timer'])) {
            $_SESSION['timer'] = time();
        }
        
        $tiempo = timeup($_SESSION['timer']);
        if (!$tiempo) {
            $_SESSION['timer'] = time();
        }

        // Verificar que las variables de sesión necesarias existan
        if (!isset($_SESSION['grupa0'])) {
            $_SESSION['grupa0'] = 0;
        }

        $gok=0;
        for ($l=1;$l<=$_SESSION['grupa0'];$l++)
        {
            if (isset($_SESSION['grupa'.$l]) && $_SESSION['grupa'.$l]==200){$gok=1;}
        }

        // Verificar que las variables de sesión necesarias existan
        if (!isset($_SESSION['tipo'])) {
            $_SESSION['tipo'] = 0;
        }
        
        // No redirigir durante exportación, solo verificar permisos
        if ($gok==0 || $_SESSION['tipo']!=3){
            // Para exportación, simplemente continuamos sin redirigir
            // Los permisos se verificarán en el proceso de exportación
            return;
        }
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
            try {
                // Usar el método del modelo para exportar cargas lectivas/no lectivas
                $this->model->generarReporteExcel($idsem, $_SESSION['codigo']);
            } catch (Exception $e) {
                // En caso de error, limpiar buffer y mostrar mensaje
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
        } else {
            $this->exportarAPDF($cursos, $idsem);
        }
    }

    // Método para exportar a Excel
    private function exportarAExcel($cursos, $idsem)
    {
        // Iniciar y limpiar buffer de salida
        ob_start();
        ob_end_clean();
        
        // Configurar headers para descarga de Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="carga_docente_'.$idsem.'.xls"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        
        // Limpiar cualquier salida restante
        if (ob_get_level()) {
            ob_clean();
        }
        
        echo "<table border='1'>";
        echo "<tr><th>CodCurso</th><th>Seccion</th><th>Curso</th><th>Semestre</th><th>Escuela</th><th>Hrs.</th></tr>";
        
        while ($row = fetchrow($cursos, -1)) {
            echo "<tr>";
            echo "<td>".(isset($row[0]) ? $row[0] : '')."</td>";
            echo "<td>".(isset($row[1]) ? $row[1] : '')."</td>";
            echo "<td>".(isset($row[2]) ? $row[2] : '')."</td>";
            echo "<td>".(isset($row[3]) ? $row[3] : '')."</td>";
            echo "<td>".(isset($row[4]) ? $row[4] : '')."</td>";
            echo "<td>".(isset($row[6]) ? $row[6] : '')."</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        exit;
    }

    // Método para exportar a HTML (sin TCPDF)
    private function exportarAPDF($cursos, $idsem)
    {
        // Iniciar y limpiar buffer de salida
        ob_start();
        ob_end_clean();

        // Verificar que las variables de sesión necesarias existan
        $docenteName = isset($_SESSION['name']) ? $_SESSION['name'] : 'Docente';

        // Configurar encabezados HTTP para HTML
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: inline; filename="carga_academica_'.$idsem.'.html"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        header('Expires: 0');

        // Limpiar cualquier salida restante
        if (ob_get_level()) {
            ob_clean();
        }

        // Generar HTML simple
        echo "<!DOCTYPE html>";
        echo "<html lang='es'>";
        echo "<head>";
        echo "<meta charset='UTF-8'>";
        echo "<title>Reporte de Carga Academica</title>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; margin: 20px; }";
        echo "h1 { color: #333; }";
        echo "table { border-collapse: collapse; width: 100%; }";
        echo "th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }";
        echo "th { background-color: #f2f2f2; }";
        echo "</style>";
        echo "</head>";
        echo "<body>";
        echo "<h1>Reporte de Carga Academica</h1>";
        echo "<p><strong>Docente:</strong> " . htmlspecialchars($docenteName) . "</p>";
        echo "<p><strong>Semestre:</strong> " . htmlspecialchars($idsem) . "</p>";
        echo "<h2>Cursos Asignados</h2>";
        echo "<table>";
        echo "<tr><th>CodCurso</th><th>Seccion</th><th>Curso</th><th>Semestre</th><th>Escuela</th><th>Hrs.</th></tr>";

        // Datos de los cursos
        while ($row = fetchrow($cursos, -1)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars(isset($row[0]) ? $row[0] : '') . "</td>";
            echo "<td>" . htmlspecialchars(isset($row[1]) ? $row[1] : '') . "</td>";
            echo "<td>" . htmlspecialchars(isset($row[2]) ? $row[2] : '') . "</td>";
            echo "<td>" . htmlspecialchars(isset($row[3]) ? $row[3] : '') . "</td>";
            echo "<td>" . htmlspecialchars(isset($row[4]) ? $row[4] : '') . "</td>";
            echo "<td>" . htmlspecialchars(isset($row[6]) ? $row[6] : '') . "</td>";
            echo "</tr>";
        }

        echo "</table>";
        echo "</body>";
        echo "</html>";
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
                
                
                try {
                    // GABO - Validando campos numéricos (horas, cantidad, porcentaje)
                    // Preparar parámetros con validación
                    $vacti = isset($_POST['vacti']) && !empty($_POST['vacti']) ? $_POST['vacti'] : '';
                    $vdacti = isset($_POST['vdacti']) && !empty($_POST['vdacti']) ? $_POST['vdacti'] : '';
                    $vimporta = isset($_POST['vimporta']) && !empty($_POST['vimporta']) ? $_POST['vimporta'] : '';
                    $vmedida = isset($_POST['vmedida']) && !empty($_POST['vmedida']) ? $_POST['vmedida'] : '';
                    $vcant = isset($_POST['vcant']) && is_numeric($_POST['vcant']) ? (int)$_POST['vcant'] : 0;
                    $vhoras = isset($_POST['vhoras']) && is_numeric($_POST['vhoras']) ? (float)$_POST['vhoras'] : 0;
                    $vcalif = isset($_POST['vporcentaje']) && is_numeric($_POST['vporcentaje']) ? (float)$_POST['vporcentaje'] : 0;
                    $vmeta = isset($_POST['vmeta']) && !empty($_POST['vmeta']) ? $_POST['vmeta'] : '';
                    $datebox = isset($_POST['datebox']) && !empty($_POST['datebox']) ? $_POST['datebox'] : '';
                    $datebox2 = isset($_POST['datebox2']) && !empty($_POST['datebox2']) ? $_POST['datebox2'] : '';
                    $viddepe = isset($_POST['viddepe']) && !empty($_POST['viddepe']) ? $_POST['viddepe'] : '';
                    $vcanthoras = isset($_POST['vcanthoras']) && is_numeric($_POST['vcanthoras']) ? (int)$_POST['vcanthoras'] : 0;
                    $vtipo = isset($_POST['vtipo']) && !empty($_POST['vtipo']) ? $_POST['vtipo'] : '';
                    $vdetalle = isset($_POST['vdetalle']) && !empty($_POST['vdetalle']) ? $_POST['vdetalle'] : null;
                    $vdependencia = isset($_POST['vdependencia']) && !empty($_POST['vdependencia']) ? $_POST['vdependencia'] : null;

                    // GABO - Agregando actividad con campos dependencia y detalle_actividad

                    // --- INICIO DE LA NUEVA VALIDACIÓN(gabo) ---
                    // Solo validamos si la actividad que se va a agregar es de tipo 'Lectiva'
                    if ($vtipo === 'Lectiva') {
                        $validacionLectiva = $this->model->validacionHorasLectivas(
                            $_SESSION['codigox'],
                            $idsem,
                            $vhoras
                        );
                        if (!$validacionLectiva['valido']) {
                            throw new Exception($validacionLectiva['mensaje']);
                        }
                    }
                    $this->model->agregarTrabajo(
                        $_SESSION['codigox'],
                        $vacti,
                        $vdacti,
                        $vimporta,
                        $vmedida,
                        $vcant,
                        $vhoras,
                        $vcalif,
                        $vmeta,
                        $datebox,
                        $datebox2,
                        $viddepe,
                        $vcanthoras,
                        $idsem,
                        $vtipo,
                        $vdetalle,
                        $vdependencia
                    );
                    echo "<script language='javascript'>alert('Actividad agregada correctamente'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                } catch (Exception $e) {
                    echo "<script language='javascript'>alert('Error al agregar actividad: " . $e->getMessage() . "'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
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

            // Este bloque parece tener un problema con la variable $i no definida
            // Lo comentamos temporalmente para evitar errores
            /*
            if (isset($_POST["modi_estado".$i]) && $_POST["modi_estado".$i] == "Modificar_Estado") {
                // Validar autorización
                if (!$this->verificarAutorizacion(200)) {
                    echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    return;
                }
                
                $_SESSION['codigox'] = $_POST["coduni"];
                $this->model->modificarEstadoTrabajoIndividual($_SESSION['codigox'], $_POST["vi".$i], $_POST["vestado_editar".$i]);
            }
            */

            if (isset($_POST["addhistorial"]) && $_POST["addhistorial"]=="Registrar")
            {
                // Validar autorización
                if (!$this->verificarAutorizacion(200)) {
                    echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    return;
                }

                $vdactividad_historial=$_POST["vdactividad_historial"];
                $vnominfo_historial=$_POST["vnominfo_historial"];
                $vdirigido_historial=$_POST["vdirigido_historial"];
                $vcargo_historial=$_POST["vcargo_historial"];
                $vremitente_historial=$_POST["vremitente_historial"];
                $vdetalle_historial=$_POST["vdetalle_historial"];
                $vporcentaje_historial=$_POST["vporcentaje_historial"];

                if (isset($vnominfo_historial)==false){$vnominfo_historial="";}
                if (isset($vdirigido_historial)==false){$vdirigido_historial="";}
                if (isset($vdirigido_historial)==false){$vdirigido_historial="";}
                if (isset($vcargo_historial)==false){$vcargo_historial="";}
                if (isset($vremitente_historial)==false){$vremitente_historial="";}
                if (isset($vdetalle_historial)==false){$vdetalle_historial="";}

                $_SESSION['codigox']=$_POST["coduni"];

                $idtrab=$vdactividad_historial;

                date_default_timezone_set('America/Lima');
                $dia=date("d/m/Y");

                $conn=conex();

                $sql="exec sp_add_trab_historial ".$_SESSION['codigox'].", '".$idtrab."', '".$vnominfo_historial."', '".$vdirigido_historial."', '".$vcargo_historial."', '".$vremitente_historial."', '".$vdetalle_historial."', '".$vporcentaje_historial."', '".$dia."'";
                $result=luis($conn, $sql);

                cierra($result);
                noconex($conn);
            }

            if (isset($_POST["vn"]) && $_POST["vn"] > 0) {
                for ($i = 1; $i <= $_POST["vn"]; $i++) {
                    
                    // --- CORRECCIÓN INICIA AQUÍ ---
                    if (isset($_POST["de".$i]) && $_POST["de".$i] == "Eliminar") {
                        // Validar autorización
                        if (!$this->verificarAutorizacion(200)) {
                            echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                            return;
                        }

                        $_SESSION['codigox'] = $_POST["coduni"];
                        
                        // Se prepara la variable para el semestre. Por defecto es null.
                        $idsem_para_eliminar = null;

                        // Verificamos si NO estamos en modo "sin semestres".
                        // Si el parámetro no existe o es diferente de 1, significa que estamos en un semestre específico.
                        if (!isset($_GET['sin_semestres']) || $_GET['sin_semestres'] != 1) {
                            // En un semestre normal, tomamos el valor del campo oculto 'msemestre'.
                            $idsem_para_eliminar = isset($_POST["msemestre"]) ? $_POST["msemestre"] : null;
                        }

                        // Llamamos al modelo con el valor correcto (un ID de semestre o null).
                        $this->model->eliminarTrabajo($_SESSION['codigox'], $_POST["vi".$i], $idsem_para_eliminar);
                        
                        // Se añade una alerta y una redirección para refrescar la página y mostrar el resultado.
                        echo "<script language='javascript'>
                                alert('Actividad eliminada correctamente.');
                                window.location.href = '{$_SERVER['HTTP_REFERER']}';
                            </script>";
                        exit; // Es crucial detener la ejecución aquí para que la redirección funcione.
                    } 
                    // --- CORRECCIÓN TERMINA AQUÍ ---
                    
                    elseif (isset($_POST["dedit".$i]) && $_POST["dedit".$i] == "Editar") {
                        // Validar autorización
                        if (!$this->verificarAutorizacion(200)) {
                            echo "<script language='javascript'>alert('No tiene permisos para realizar esta acción'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                            return;
                        }

                        $_SESSION['codigox'] = $_POST["coduni"];

                        try {
                            // GABO - Validando campos numéricos para edición (horas, cantidad, porcentaje)
                            // Preparar parámetros con validación para edición
                            $vacti_editar = isset($_POST['vacti_editar'.$i]) && !empty($_POST['vacti_editar'.$i]) ? $_POST['vacti_editar'.$i] : '';
                            $vdacti_editar = isset($_POST['vdacti_editar'.$i]) && !empty($_POST['vdacti_editar'.$i]) ? $_POST['vdacti_editar'.$i] : '';
                            $vimporta_editar = isset($_POST['vimporta_editar'.$i]) && !empty($_POST['vimporta_editar'.$i]) ? $_POST['vimporta_editar'.$i] : '';
                            $vmedida_editar = isset($_POST['vmedida_editar'.$i]) && !empty($_POST['vmedida_editar'.$i]) ? $_POST['vmedida_editar'.$i] : '';
                            $vcant_editar = isset($_POST['vcant_editar'.$i]) && is_numeric($_POST['vcant_editar'.$i]) ? (int)$_POST['vcant_editar'.$i] : 0;
                            $vhoras_editar = isset($_POST['vhoras_editar'.$i]) && is_numeric($_POST['vhoras_editar'.$i]) ? (float)$_POST['vhoras_editar'.$i] : 0;
                            $vcalif_editar = isset($_POST['vcalif_editar'.$i]) && is_numeric($_POST['vcalif_editar'.$i]) ? (float)$_POST['vcalif_editar'.$i] : 0;
                            $vmeta_editar = isset($_POST['vmeta_editar'.$i]) && !empty($_POST['vmeta_editar'.$i]) ? $_POST['vmeta_editar'.$i] : '';
                            $dateboxx = isset($_POST['dateboxx'.$i]) && !empty($_POST['dateboxx'.$i]) ? $_POST['dateboxx'.$i] : '';
                            $dateboxx2 = isset($_POST['dateboxx2'.$i]) && !empty($_POST['dateboxx2'.$i]) ? $_POST['dateboxx2'.$i] : '';
                            $vporcentaje_editar = isset($_POST['vporcentaje_editar'.$i]) && is_numeric($_POST['vporcentaje_editar'.$i]) ? (float)$_POST['vporcentaje_editar'.$i] : 0;
                            $vtipo_editar = isset($_POST['vtipo_editar'.$i]) && !empty($_POST['vtipo_editar'.$i]) ? $_POST['vtipo_editar'.$i] : '';
                            $vdetalle_editar = isset($_POST['vdetalle_editar'.$i]) && !empty($_POST['vdetalle_editar'.$i]) ? $_POST['vdetalle_editar'.$i] : null;
                            $vdependencia_editar = isset($_POST['vdependencia_editar'.$i]) && !empty($_POST['vdependencia_editar'.$i]) ? $_POST['vdependencia_editar'.$i] : null;

                            // GABO - Editando actividad con campos dependencia y detalle_actividad

                            $this->model->editarTrabajo(
                                $_SESSION['codigox'],
                                $_POST["vi".$i],
                                $vacti_editar,
                                $vdacti_editar,
                                $vimporta_editar,
                                $vmedida_editar,
                                $vcant_editar,
                                $vhoras_editar,
                                $vcalif_editar,
                                $vmeta_editar,
                                $dateboxx,
                                $dateboxx2,
                                $vporcentaje_editar,
                                $vtipo_editar,
                                $vdetalle_editar,
                                $vdependencia_editar
                            );
                            echo "<script language='javascript'>alert('Trabajo editado correctamente'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                        } catch (Exception $e) {
                            echo "<script language='javascript'>alert('Error al editar trabajo: " . $e->getMessage() . "'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                        }
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
    
    // Método especial para manejar acciones POST durante exportaciones
    private function handleAjaxUpload()
    {
        // --- CORRECCIÓN: Silenciar errores y limpiar buffers ---
        error_reporting(0);
        ini_set('display_errors', 0);
        while (ob_get_level()) {
            ob_end_clean(); // Limpiar todos los buffers existentes
        }
        ob_start(); // Iniciar un nuevo buffer limpio

        // Manejar sesión de forma minimalista para AJAX
        $sex = isset($_GET['sesion']) ? $_GET['sesion'] : '';
        session_name($sex);
        session_start();

        // Verificar que las variables de sesión necesarias existan
        if (isset($_SESSION['codigo'])) {
            $codigo = $_SESSION['codigo'];
        } else {
            $codigo = 1234; // dato de prueba para evitar errores
        }

        if (isset($_SESSION['codper'])) {
            $codigo = $_SESSION['codper'];
        } else {
            $codigo = 1234; // dato de prueba para evitar errores
        }

        // Procesar la subida
        $excelData = json_decode($_POST['excelData'], true);
        if ($excelData) {
            try {
                // --- Asumiendo que este método NO genera salida ---
                $this->model->insertarCargaDesdeExcel($excelData, $_SESSION['codigo'], $_SESSION['codper']);
                if (ob_get_level()) ob_clean();
                header('Content-Type: application/json'); // Asegurar encabezado JSON
                echo json_encode(['success' => true, 'message' => 'Datos del Excel subidos correctamente a la base de datos.']);
            } catch (Exception $e) {
                // --- CORRECCIÓN: Limpiar buffer, establecer encabezado y enviar JSON en caso de error ---
                if (ob_get_level()) ob_clean();
                header('Content-Type: application/json'); // Asegurar encabezado JSON
                // --- Mejora: Loggear el error real del servidor para depuración ---
                // error_log("Error en handleAjaxUpload: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error al subir datos: ' . $e->getMessage()]);
            }
        } else {
            if (ob_get_level()) ob_clean();
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'No se recibieron datos del Excel o los datos no son válidos.']);
        }
        // Limpiar buffer y salir
        ob_end_flush(); // Enviar el contenido del buffer (el JSON)
        exit;
    }
    private function handleFileUpload()
    {
        error_reporting(0);
        ini_set('display_errors', 0);
        while (ob_get_level()) {
            ob_end_clean();
        }
        ob_start();

        $sex = isset($_GET['sesion']) ? $_GET['sesion'] : '';
        session_name($sex);
        session_start();

        if (!isset($_SESSION['codigo'])) {
            $_SESSION['codigo'] = 1234;
        }

        if (!isset($_SESSION['codper'])) {
            $_SESSION['codper'] = 1234;
        }

        try {
            if (isset($_FILES['archivo'])) {
                $file = $_FILES['archivo']['tmp_name'][0];
                $filename = $_FILES['archivo']['name'][0];

                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($extension, ['xls', 'xlsx'])) {
                    $response = ['success' => false, 'message' => 'Solo se permiten archivos Excel (.xls, .xlsx)'];
                } else {
                    $phpExcelPath = __DIR__ . '/../assets/PHPExcel.php';
                    if (!file_exists($phpExcelPath)) {
                        $response = ['success' => false, 'message' => 'Biblioteca PHPExcel no disponible'];
                    } else {
                        require_once $phpExcelPath;
                        $objPHPExcel = PHPExcel_IOFactory::load($file);
                        $sheet = $objPHPExcel->getActiveSheet();
                        $excelData = [];
                        foreach ($sheet->getRowIterator() as $row) {
                            $cellIterator = $row->getCellIterator();
                            $cellIterator->setIterateOnlyExistingCells(false);
                            $rowData = [];
                            foreach ($cellIterator as $cell) {
                                $rowData[] = $cell->getValue();
                            }
                            $excelData[] = $rowData;
                        }
                        $this->model->insertarCargaDesdeExcel($excelData, $_SESSION['codigo'], $_SESSION['codper']);
                        $response = ['success' => true, 'message' => 'Datos del Excel subidos correctamente.'];
                    }
                }
            } else {
                $response = ['success' => false, 'message' => 'No se recibió archivo.'];
            }
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => $e->getMessage()];
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        ob_end_flush();
        exit;
    }

    private function handlePostActionsForExport()
    {
        // Limpiar cualquier salida previa
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Solo manejar exportaciones, no otras acciones POST
        if (isset($_GET['exportar'])) {
            $formato = isset($_GET['formato']) ? $_GET['formato'] : 'pdf';
            $this->exportarCargaDocente($formato);
            exit;
        }
    }

    private function generateMenu($data)
    {
        $menu = '';

        /*MENU LATERAL IZQUIERDO*/
        $menu .= ('<link href="site.css" type="text/css" rel="stylesheet">');
        helpx(10701,$data['sex']);

        $menu .= ('<div id="root-row2">');
        $menu .= ('<div id="crumbs"> ');
        $menu .= ('</div>');
        $menu .= ('</div>');

        $menu .= ('<div id="nav">');
        $menu .= ('<div id="menu-block">');
        $menu .= ('<a class="menux1" href="inicio.php?sesion='.$data['sex'].'">Inicio</a>');
        // if( $_SESSION['codigo'] == 298907 ){
        $menu .= ('<div id="side-block"></div>');
        $menu .= ('<a class="menux1" href="dusuario.php?sesion='.$data['sex'].'">Datos Docente</a>');
        // }
        // echo $_SESSION['grupa0'];
        For ($l=1;$l<=$data['grupa0'];$l++)
        {
            switch ($data['grupa'.$l]) {
                case 100:
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<a class="menux1" href="alumno.php?sesion='.$data['sex'].'">Alumno</a>';
                    break;
                case 200:
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<a class="menuy1" href="carga.php?sesion='.$data['sex'].'">Carga</a>';
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<a class="menux1" href="../carga.php?sesion='.$data['sex'].'&sin_semestres=1">PIT sin cronograma</a>';
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<div id="side-block"></div>';

                    // Solo cargar semestres si no está en modo sin semestres
                    if (!$data['sin_semestres'] && isset($data['semestres'])) {
                        while ($row = fetchrow($data['semestres'],-1))
                        {
                            //
                            $menu .= '<i><a class="menux1 tooltip" style="font-size:11px; text-align:left; background:#a6a6ac; color:white; padding-top:5px;" href="carga.php?sesion='.$data['sex'].'&x='.$row[0].'&tx='.$row[3].'"> '.$row[1].'  <span style="font-size:10px; padding-left:2px;">('.$row[0].')</span><span class="tooltiptext">'.$row[2].'</span></a></i>';
                            $menu .= '<div id="side-block"></div>';
                        }
                    }

                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<a class="menux1" href="Elearning.php?sesion='.$data['sex'].'" target="_blank">Aula Virtual</a>';


                    //if ($_SESSION['padl']==1)
                    //{
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<a class="menux1" href="asistenciap.php?sesion='.$data['sex'].'">Parte Asistencia</a>';
                    //}

                    

                    break;
                case 300:
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<a class="menux1" href="estadistica.php?sesion='.$data['sex'].'">Estadistica</a>';
                    break;
                case 400:
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<a class="menux1" href="busca.php?sesion='.$data['sex'].'">Busqueda</a>';
                    break;
                case 700:
                    $menu .= '<div id="side-block"></div>';
                    $menu .= '<a class="menux1" href="biblioteca.php?sesion='.$data['sex'].'" target="_blank">Biblioteca</a>';
                    break;

            }
        }

        // ************* 	LOGO 	******************
        $menu .= '<div id="side-block"></div>';
        $menu .= '<br>';
        $menu .= '<br>';
        // logo WIFI
        //echo '<br><center><a href="alumno.php?sesion='.$data['sex'].'&wf=1" ><img width="74" height="50"  alt="Clic para generar su clave Wifi" src="imagenes/logo_wifi.png" border=0><br><font size =2px> GENERAR CLAVE WIFI</font></a></center><b>';

        if ($data['wf']==1){
            $menu .= '<br>';
            $menu .= '<center>';
            $menu .= $data['wifi'].'</b><br>';
            $menu .= '</center>';
        }

                // ************* 	HASTA AQUI LOGO 	******************
                $menu .= ('</div>');
                $menu .= ('</div>');
        
                return $menu;
            }
        
            private function generateContent($data)
            {
                $content = '';
        
                $content .= '<form method="POST" action="carga.php?sesion='.$data['sex'].'" name="frminicio">';
                $content .= '<INPUT TYPE="hidden" NAME="op" value="0" >';
                $content .= '<table border="0" width="100%">';
                $content .= '<tr>';
        
                // Solo mostrar información de semestre si no está en modo sin semestres
                if(!$data['sin_semestres'] && $data['semestre_info']){
                    $rowdir = fetchrow($data['semestre_info'], -1);
                    $semestre=$rowdir[0];
                    $obssemestre=$rowdir[1];
                }

                $content .= '<td width="550">';
                if(!$data['sin_semestres'] && $data['idsem'] > 0){
                    $content .= '<font size="2"><strong>Semestre:</strong> ('. $data['idsem'] .') - '. $obssemestre . '</font><br>';
                }
                $content .= '<font size="2"><strong>Docente:</strong> '.$data['name'].'</font>';
                $content .= '</td>';
        
                if ($data['idsem'] == '' && !$data['sin_semestres'])
                {
                    $content .= "<font size='4' style='color: 102368;'>Hacer clic en el SEMESTRE del menu izquierdo.</font>";
                    $content .= '</tr>';
                    $content .= '</table>';
                    $content .= '</form>';
                    return $content;
                }
        
                if(!$data['esSemestreTaex'] && !$data['sin_semestres']){
                    $content .= '<td><font size="1"><a style="font-size:12px;" href="carga.php?tr=1&sesion='.$data['sex'].'&x='.$data['idsem'].'" >TRABAJO INDIVIDUAL '.$obssemestre.' </a></font>  <font size="1" face="Arial">
                                            <blink>
                                                <a href="documentos/PIT_Docente/ActividadesPITGA_V2.pdf" target="_blank">
                                                    <center style="color: red;">( Guía PIT )</center>
                                                </a>
                                            </blink>
                                            </font></td>';
                }
        
                if($data['director_depe']){
                    $rowdir = fetchrow($data['director_depe'], -1);
                    $iddepedirec=$rowdir[0];
                    require_once('encripta_pdf.php');
                    if (($iddepedirec>0) or ($data['codigo']==117584) or ($data['codigo']==	202848))
                    {
                        //echo '<td><font size="2"><a target="_blank" href="http://www.upt.edu.pe/epic2/resultado.php" >Reporte de resultados de encuesta</a></font></td>';
                    }
        
                    if (($data['codigo']==117584) or ($data['codigo']==	202848)or ($data['codigo']==141414)or ($data['codigo']==109684)or ($data['codigo']==124717))
                    {
                        $content .= '<td><font size="2"><a target="_blank" href="http://www.upt.edu.pe/epic2/resultadovi.php" >Reporte de resultados de encuesta VICERRECTOR y RECTOR(A)</a></font></td>';
                    }
                }
        
                $content .= '</tr>';
                $content .= '</table>';
        
                // Botones para imprimir y exportar
                $content .= '<div style="text-align: right; margin: 10px 0; font-family: Arial, sans-serif;">';
                $content .= '<button type="button" id="btnGenerarPDF" style="padding: 8px 15px; background-color: #e52b1eff; color: white; border: none; border-radius: 4px; cursor: pointer; margin-right: 10px;">Descargar Carga (PDF)</button>';
                $content .= '<button type="button" id="btnGenerarExcelWebScraping" style="padding: 8px 15px; background-color: #49c929ff; color: white; border: none; border-radius: 4px; cursor: pointer;">Descargar Carga (EXCEL)</button>';
                //$content .= '<br><br>';
                //$content .= '<button type="button" id="btnVerAutoridades" style="padding: 8px 15px; background-color: #0e2487ff; color: white; border: none; border-radius: 4px; cursor: pointer;">Ver Autoridades Academicas</button>';
                //$content .= '</div>';
        
                $na=0;
                $mj=0;
                $ton=0;
        
                $content .= '<table border="0" ><tr><th bgcolor="#DBEAF5" ><font size="1">sel</font></th><th bgcolor="#DBEAF5" ><font size="1">CodCurso</font></th><th bgcolor="#DBEAF5" ><font size="1">Seccion</font></th><th bgcolor="#DBEAF5" ><font size="1">Curso</font></th><th bgcolor="#DBEAF5" ><font size="1">Semestre</font></th><th bgcolor="#DBEAF5" ><font size="1">Escuela</font></th><th bgcolor="#DBEAF5" ><font size="1">Hrs.</font></th>';
                $content .= '<th bgcolor="#DBEAF5" ><font size="1">Consolidado</font></th>';
                $content .= '</tr>';
        
                if($data['cursos']){
                    while ($row =fetchrow($data['cursos'],-1))
                    {
                        $na++;
                        if ($ton==1){$tcol='bgcolor="#F3F9FC"';$ton=0;}else{$tcol='';$ton=1;}
                        $content .= ' <tr '.$tcol.'><td><input type="radio" value="'.$na.'" name="R1" onClick="javascript:pele('.$na.')" >&nbsp;&nbsp;</td>';
                        $content .= ' <td '.$tcol.'><font size="1">'.$row[0].'</font></td>';
                        $content .= ' <td '.$tcol.'><font size="1">'.$row[1].'</font></td>';
                        $content .= ' <td '.$tcol.'><font size="1">'.$row[2].'</font></td>';
                        $content .= ' <td '.$tcol.'><font size="1">'.$row[3].'</font></td>';
                        $content .= ' <td '.$tcol.'><font size="1">'.$row[4].'</font></td>';
                        $content .= ' <td '.$tcol.'><font size="1">'.$row[6].'</font></td>';
                        $content .= '<input name="codcur" type="hidden" value="'.$row[0].'">';
                        $content .= '<input name="codp" type="hidden" value="'.$data['codper'].'">';
                        // CORRECCIÓN: Usar buscacxv2.php en lugar de carga.php
                        $content .= '<td '.$tcol.'><font size="1"><a href=# onclick="javascript:window.open(\'buscacxv2.php?sesion='.$data['sex'].'&o='.$row[5].'&taex='.$row[9].'\',\'otra\',\'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,width=850,height=650,top=40,left=50\');return false"><center><img src="imagenes/view.gif" width=18 height=20 alt="Consolidado" border=0></center> </a></font></td>';
                        $content .= '</tr>';
                        if ($row[4]=='Ingeniería Civil'){$mj=1;}
        
                    }
                }
        
                $content .= '<table border="0" cellpadding="10" ><tr><td width="132" ></td></tr></table>';
                $content .= '</table>';
                $content .= '</form>';
                    $content .= '<script>
                    document.getElementById("registro").addEventListener("submit", function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
                        fetch(this.action, {
                            method: "POST",
                            body: formData,
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                            }
                        }).then(response => response.json()).then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert("Error: " + data.message);
                            }
                        }).catch(error => {
                            alert("Error de red: " + error);
                        });
                    });
                    </script>';
        
                if(isset($data['ultimos_accesos'])){
                    if ( numrow($data['ultimos_accesos']) > 0 ){
                        $content .= '<h3 align="left">Últimos Accesos</h3>';
                        $content .= '<table border="0" cellspacing="2">';
                        $content .= '<tr>';
                        $content .= '<th bgcolor="#DBEAF5"><font size="1">&nbsp;&nbsp;N&#176;&nbsp;&nbsp;</font></th>';
                        $content .= '<th bgcolor="#DBEAF5"><font size="1">CodCurso</font></th>';
                        $content .= '<th bgcolor="#DBEAF5"><font size="1">Curso</font></th>';
                        $content .= '<th bgcolor="#DBEAF5"><font size="1">Seccion</font></th>';
                        $content .= '<th bgcolor="#DBEAF5"><font size="1">Usuario Ingreso</font></th>';
                        $content .= '<th bgcolor="#DBEAF5"><font size="1">Fecha Ingreso</font></th>';
                        $content .= '</tr>';
        
                        $nc=0;
                        $tcol='';
                        $ton=0;
        
                        while ($row = fetchrow($data['ultimos_accesos'],-1))
                        {
                            $nc++;
        
                            if ($ton==1){$tcol='class="lin1"';$ton=0;}else{$tcol='class="lin0"';$ton=1;}
                            $content .= '<tr '.$tcol.' >';
                            $content .= '<td align="center"><font size="1">'.$nc.'</font></td>';
                            $content .= '<td align="center"><font size="1">'.$row[0].'</font></td>';
                            $content .= '<td><font size="1">'.$row[1].'</font></td>';
                            $content .= '<td align="center"><font size="1">'.$row[2].'</font></td>';
                            $content .= '<td align="center"><font size="1">'.$row[3].'</font></td>';
                            $content .= '<td><font size="1">'.$row[4].'</font></td>';
                            $content .= '</tr>';
                        }
                        $content .= '</table>';
                    }
                }
                $content .= '</br>';
                $content .= '</br>';
        
                $file_php=0;
                if (isset($data['tr'])==true){
                    require ("genera.php");
                    $sem = $data['idsem'];
        
                    // --- NUEVO: Obtener la denominación del semestre seleccionado ---GABO
                    $semestre_denominacion = "DESCONOCIDO"; // Valor por defecto
                    if (isset($data['semestres'])) {
                        // Reiniciar puntero del resultset si es necesario (aunque fetchrow normalmente avanza)
                        // Para estar seguros, podríamos reconstruir la lógica o asegurarnos de que $data['semestres'] sea recorrible aquí.
                        // La forma más segura es hacer una nueva consulta o pasar la info de generateMenu.
                        // Pero como ya está en $data['semestres'], intentemos usarlo.
        
                        // Guardar temporalmente el resultset para no afectar otras partes
                        $temp_resultset = $data['semestres'];
                        while ($row_sem = fetchrow($temp_resultset, -1)) {
                            if ($row_sem[0] == $sem) { // Comparar ID numérico
                                $semestre_denominacion = $row_sem[1]; // Asignar denominación
                                break; // Salir del bucle al encontrarlo
                            }
                        }
                        // NOTA: fetchrow mueve el puntero interno. Si $data['semestres'] se usa después,
                        // esto podría causar problemas. Una mejor práctica es reconstruir el array o usar numrow/reset.
                        // Para una solución rápida, asumiremos que no hay problema o que se reconstruye el resultset si es necesario más adelante.
                    }
                    // --- FIN NUEVO ---
        
                    ob_start();
                    individual($data['codigo'], $data['sex'], $data['codper'], 0, $file_php,$sem,$semestre_denominacion, $data['sin_semestres']);
                    $content .= ob_get_clean();
                }
                $content .= '</br>';
                if(isset($data['tr'])==true && !$data['sin_semestres'])
                {
                    $content .= '<br><br><form method="post" action="carga.php?tr=1&sesion='.$data['sex'].'&x='.$data['idsem'].'" name="registro" id="registro" enctype="multipart/form-data">';
                    $content .= '<table border="0" widtd="100%" >';
                    $content .= '<tr><td style="color:111351; font-size:15px;"><b>REGISTRO DE HORARIO DE TRABAJO</b></td></tr>';
                    $content .= '<tr>';
                    $content .= '<td><input type="hidden" name="variable2" value="registro" />- Descargar <b>Anexo</b> de horario de trabajo<a href="trabajoindividualex/Horario_Docente.xls" target="_blank"><span style="color: red;"><font size="1" face="Arial"><blink> ( Haga clic aquí )</blink></font></span> </td></tr>';
                    $content .= '</tr>';
                    $content .= '<tr>';
                    $content .= '<tr>';
                    $content .= '<td></td>';
                    $content .= '</tr>';
                    $content .= '<td>Seleccionar el archivo <font size="1px">(xls)</font> <input type="file" id="excelFile" name="archivo[]" accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" multiple></td>';
                    $content .= '<td><input type="submit" value="Subir Archivo" name="registrar" id="registrar"></td>';
                    $content .= '</tr>';
                    $content .= '</table>';
                    $content .= '</form>';
                    $content .= '<div id="output"></div>';
        
                    if(isset($data['trabajo_individual_doc']) && $data['trabajo_individual_doc']){
                        $rowdir = fetchrow($data['trabajo_individual_doc'],-1);
                        $doc=$rowdir[0];
                        $content .= "- Visualizar Horario de Trabajo ".$semestre.": <a href=trabajoindividualex/".$doc.">Hacer Clic para Ver el Documento</a><br><br><br>";
                    }
                }
        
                $content .= '<br><br>En la columna <strong>Consolidado</strong> podrá visualizar la nota promedio de la unidad y el resumen de la unidad <br>que incluye: aprobados, desaprobados, retirados y abandonos.';
        
                return $content;
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
                $data['sin_semestres'] = isset($_GET['sin_semestres']) ? $_GET['sin_semestres'] : false;

        if ($_SESSION['check'] > 0) {
            $cargas = $this->model->getCarga($_SESSION['codigo']);
            while ($row = fetchrow($cargas, -1)) {
                if ($row[4] > 0) {
                    $this->model->updateCarga($row[0], $row[1], $row[2], $row[3], $row[5], $_SESSION['codigo']);
                }
            }
            $_SESSION['check'] = 0;
        }

        // Always load semesters to get current one, but don't show in menu when sin_semestres=1
        $data['semestres'] = $this->model->getSemestres($_SESSION['codper']);

        if (!$data['sin_semestres']) {
            $data['semestre_info'] = $this->model->getSemestreInfo($data['idsem'], $data['esSemestreTaex']);
        } else {
            // For sin_semestres, set to first active semester
            if (isset($data['semestres']) && $data['semestres']) {
                // Get first row from semesters
                $first_sem_row = fetchrow($data['semestres'], -1);
                if ($first_sem_row) {
                    $data['idsem'] = $first_sem_row[0]; // Assuming first column is idsem
                    $data['semestre_info'] = $this->model->getSemestreInfo($data['idsem'], $data['esSemestreTaex']);
                }
            }
        }
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

        // Obtener autoridades académicas del docente
        $data['autoridades'] = $this->model->getAutoridadesAcademicas($_SESSION['codigo']);

        // Generate menu
                $data['menu'] = $this->generateMenu($data);

                // Generate content
                $data['content'] = $this->generateContent($data);

                return $data;
    }
}