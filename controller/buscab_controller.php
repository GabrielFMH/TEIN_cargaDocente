<?php
require_once BASE_PATH . '/model/buscab_model.php';

class BuscabController
{
    private $model;

    public function __construct()
    {
        $this->model = new BuscabModel();
    }

    public function handleRequest()
    {
        $sex = isset($_GET['sesion']) ? $_GET['sesion'] : '';
        
        // Session and authentication
        $this->handleSession($sex);

        // Handle POST actions
        $this->handlePostActions();

        // Prepare data for the view
        $data = $this->prepareViewData();

        // Load the view
        require BASE_PATH .'/view/buscab_view.php';
    }

    private function handleSession($sex)
    {
        require_once 'funciones.php';
        pageheader();
        callse($sex);
        session_name($sex);
        session_start();

        if (isset($_SESSION['timer']) && timeup($_SESSION['timer'])) {
            $_SESSION['timer'] = time();
        } else {
            header("Location: logout.php?sesion=" . $sex);
            exit;
        }

        if ($_SESSION['tipo'] != 3) {
            header("Location: cambio.php?sesion=" . $sex);
            exit;
        }
    }

    private function handlePostActions()
    {
        // Add new work item
        if (isset($_POST['vagregar']) && $_POST['vagregar'] == 'Agregar') {
            $this->model->agregarTrabajo(
                $_SESSION['codigox'], $_POST['vacti'], $_POST['vdacti'], $_POST['vimporta'],
                $_POST['vmedida'], $_POST['vcant'], $_POST['vhoras'], $_POST['vcalif'],
                $_POST['vmeta'], $_POST['datebox'], $_POST['datebox2'], $_POST['viddepe'], $_POST['vcanthoras']
            );
        }

        // Other POST actions would be handled here...
    }

    private function prepareViewData()
    {
        $data = [];
        $data['sex'] = isset($_GET['sesion']) ? $_GET['sesion'] : '';
        $data['namex'] = isset($_SESSION['namex']) ? $_SESSION['namex'] : '';
        $data['codigox'] = isset($_SESSION['codigox']) ? $_SESSION['codigox'] : '';
        $data['grupa0'] = isset($_SESSION['grupa0']) ? $_SESSION['grupa0'] : 0;
        $data['recod'] = 0;

        // Determine permissions
        for ($l = 1; $l <= $data['grupa0']; $l++) {
            if ($_SESSION['grupa' . $l] == 421) {
                $data['recod'] = 1;
                break;
            }
        }

        // Get teacher's courses
        $ga = '';
        for ($l = 1; $l <= $data['grupa0']; $l++) {
            if ($_SESSION['grupa' . $l] == 420) {
                // Logic for building $ga string
            }
        }
        $data['cursos'] = $this->model->getCursosDocente($_SESSION['codperx'], $ga);

        // Get evaluation data if 'dc' is present
        if (isset($_GET['dc']) && $_GET['dc'] > 0) {
            $data['evaluacion_data'] = $this->model->getEvaluacionData($_GET['dc']);
        }

        return $data;
    }
}
?>