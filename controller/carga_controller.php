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

    private function handlePostActions()
    {
        $idsem = isset($_GET["x"]) ? $_GET["x"] : (isset($_SESSION['idsemindiv']) ? $_SESSION['idsemindiv'] : null);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST["variable2"]) && $_POST["variable2"] == "registro") {
                $i = 0;
                do {
                    if ($_FILES['archivo']['tmp_name'][$i] != "") {
                        $nuevonombre = $_SESSION['codigo'] . $idsem . ".xls";
                        move_uploaded_file($_FILES['archivo']['tmp_name'][$i], 'trabajoindividualex/' . $nuevonombre);
                        $this->model->uploadTrabajoIndividual($idsem, $_SESSION['codigo'], $nuevonombre);
                        echo "<script language='javascript'>alert('Registrado con Exito'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                    }
                    $i++;
                } while ($i < 10);
            }

            if (isset($_POST["vagregar"]) && $_POST["vagregar"] == "Agregar") {
                $_SESSION['codigox'] = $_POST["coduni"];
                if ($_POST["vacti"] == "Administrativa" && !in_array($_POST["vcalif"], [7, 8])) {
                    echo "<script language='javascript'>alert('La actividad Administrativa solo puede tener calificacion [Administración] o [Jefatura]'); window.location='{$_SERVER['HTTP_REFERER']}';</script>";
                } else {
                    $this->model->agregarTrabajo($_SESSION['codigox'], $_POST['vacti'], $_POST['vdacti'], $_POST['vimporta'], $_POST['vmedida'], $_POST['vcant'], $_POST['vhoras'], $_POST['vcalif'], $_POST['vmeta'], $_POST['datebox'], $_POST['datebox2'], $_POST['viddepe'], $_POST['vcanthoras'], $idsem);
                }
            }

            if (isset($_POST["medit"]) && $_POST["medit"] == "Modificar") {
                $this->model->modificarEstadoActividades($_POST["mcodigo"], $_POST["msemestre"], $_POST["mestado_editar"]);
            }

            if (isset($_POST["modi_estado".$i]) && $_POST["modi_estado".$i] == "Modificar_Estado") {
                $_SESSION['codigox'] = $_POST["coduni"];
                $this->model->modificarEstadoTrabajoIndividual($_SESSION['codigox'], $_POST["vi".$i], $_POST["vestado_editar".$i]);
            }

            if (isset($_POST["addhistorial"]) && $_POST["addhistorial"] == "Registrar") {
                $_SESSION['codigox'] = $_POST["coduni"];
                $this->model->registrarHistorial($_SESSION['codigox'], $_POST['vdactividad_historial'], $_POST['vnominfo_historial'], $_POST['vdirigido_historial'], $_POST['vcargo_historial'], $_POST['vremitente_historial'], $_POST['vdetalle_historial'], $_POST['vporcentaje_historial'], date("d/m/Y"));
            }

            if (isset($_POST["vn"]) && $_POST["vn"] > 0) {
                for ($i = 1; $i <= $_POST["vn"]; $i++) {
                    if (isset($_POST["de".$i]) && $_POST["de".$i] == "Eliminar") {
                        $_SESSION['codigox'] = $_POST["coduni"];
                        $this->model->eliminarTrabajo($_SESSION['codigox'], $_POST["vi".$i], $_POST["msemestre"]);
                    } elseif (isset($_POST["dedit".$i]) && $_POST["dedit".$i] == "Editar") {
                        $_SESSION['codigox'] = $_POST["coduni"];
                        $this->model->editarTrabajo($_SESSION['codigox'], $_POST["vi".$i], $_POST['vacti_editar'.$i], $_POST['vdacti_editar'.$i], $_POST['vimporta_editar'.$i], $_POST['vmedida_editar'.$i], $_POST['vcant_editar'.$i], $_POST['vhoras_editar'.$i], $_POST['vcalif_editar'.$i], $_POST['vmeta_editar'.$i], $_POST['dateboxx'.$i], $_POST['dateboxx2'.$i], $_POST['vporcentaje_editar'.$i]);
                    } elseif (isset($_POST["delim".$i]) && $_POST["delim".$i] == "Finalizar") {
                        $this->model->finalizarTrabajo($_POST["vi".$i], $idsem, $_POST["coduni"], 1);
                    } elseif (isset($_POST["rever".$i]) && $_POST["rever".$i] == "Revertir") {
                        $this->model->revertirTrabajo($_POST["vi".$i], $idsem, $_POST["coduni"], 0);
                    }
                }
            }

            if (isset($_POST["R1"]) && $_POST["R1"] > 0) {
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