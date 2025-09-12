<?php
function genera_formulario_carga($conn, $codigo, $sem, $codper, $busca, $file_php, $da, $total_horas, $iddepe, $vUno, $vDos, $vTres) {
    //Validacion Plani --Yoel 23-10-18
    if ( ($vUno == 0 && $vDos == 0 && $vTres == true) || ($vUno == 0 && $vDos == 0 && $vTres == false) || ($vUno == 1 && $vDos == 1 && $vTres == false) || ($vUno == 1 && $vDos == 0 && $vTres == false) ) {
        echo '<table border="0" cellspacing="2" bgcolor="#CCE6FF">';
        echo '<tr>';
        echo '<td width="120" colspan="2"><font size="1">Actividad</font></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td width="120" colspan="2">';
        
        // Script JavaScript para combobox anidados
        echo '<script>
        function actualizarTipoActividad() {
            var actividad = document.getElementById("actividad").value;
            var tipoSelect = document.getElementById("tipo_actividad");
            var detalleSelect = document.getElementById("detalle_actividad");
            
            // Limpiar selects siguientes
            tipoSelect.innerHTML = "<option value=\"\">-- Seleccione --</option>";
            detalleSelect.innerHTML = "<option value=\"\">-- Seleccione --</option>";
            
            if (actividad === "Academica") {
                var opciones = [
                    { value: "Lectiva", text: "Lectiva" },
                    { value: "No_Lectiva", text: "No Lectiva" },
                    { value: "Investigacion", text: "Investigación" },
                    { value: "Responsabilidad_Social", text: "Responsabilidad Social" }
                ];
            } else if (actividad === "Administrativa") {
                var opciones = [
                    { value: "Gestion", text: "Gestión" }
                ];
            }
            
            opciones.forEach(function(opcion) {
                var opt = document.createElement("option");
                opt.value = opcion.value;
                opt.text = opcion.text;
                tipoSelect.appendChild(opt);
            });
        }
        
        function actualizarDetalleActividad() {
            var tipo = document.getElementById("tipo_actividad").value;
            var detalleSelect = document.getElementById("detalle_actividad");
            
            detalleSelect.innerHTML = "<option value=\"\">-- Seleccione --</option>";
            
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
                        { value: "Responsabilidad_Social", text: "Responsabilidad social" },
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
        </script>';
        
        // Primer combobox - Actividad
        echo '<select id="actividad" name="vacti" onchange="actualizarTipoActividad()">';
        echo '<option value="">-- Seleccione Actividad --</option>';
        echo '<option value="Academica">Académica</option>';
        echo '<option value="Administrativa">Administrativa</option>';
        echo '</select>';
        
        echo "&nbsp;&nbsp;&nbsp;";
        
        // Segundo combobox - Tipo de Actividad
        echo '<select id="tipo_actividad" name="vtipo" onchange="actualizarDetalleActividad()">';
        echo '<option value="">-- Seleccione Tipo --</option>';
        echo '</select>';
        
        echo "&nbsp;&nbsp;&nbsp;";
        
        // Tercer combobox - Detalle de Actividad
        echo '<select id="detalle_actividad" name="vdetalle">';
        echo '<option value="">-- Seleccione Detalle --</option>';
        echo '</select>';
        
        echo '</td>';
        echo '</tr>';

        date_default_timezone_set('America/Lima');
        //$dia=date("d/m/Y");
        //$dia2=$dia;

        /*Muestra la fecha de inicio y fin del semestre activo - 10-06-2016 naty cuando carga la pagina por primera vez*/
        $conn_temp=conex();
        $sql="select s.inicioclases, s.finentregaactas from semestre s where s.activo>0 and s.idsem=".$sem;
        //echo $sql;
        $result=luis($conn_temp, $sql);
        while ($row=fetchrow($result,-1))
        {
            $FechaInicio = $row[0];
            $FechaFin = $row[1];
        }
        cierra($result);
        $dia=date("d/m/Y",strtotime($FechaInicio));
        $dia2=date("d/m/Y",strtotime($FechaFin));
        /*Muestra la fecha de inicio y fin del semestre activo - 10-06-2016*/

        if (isset($_POST["datebox"])==true){
            $dia=$_POST["datebox"];
        }
        if (isset($_POST["datebox2"])==true){
            $dia2=$_POST["datebox2"];
        }

        echo'<tr>';
        echo '<td width="108" colspan="3"><font size="1">Fecha Inicio:</font></td>';
        echo'</tr>';
        echo'<tr>';
        echo '<td width="108" colspan="3">';
        ?>
         <input name="datebox" readonly="true" autocomplete="off" size="10" onClick="displayCalendar(datebox,'dd/mm/yyyy',this)" type="text" value="<? echo $dia ?>">
        <?
        echo '</td>';
        echo '</tr>';
        
        echo'<tr>';
        echo '<td width="108" colspan="3"><font size="1">Fecha Final:</font></td>';
        echo'</tr>';
        echo'<tr>';
        echo '<td width="108" colspan="3">';
        ?>
         <input name="datebox2" readonly="true" autocomplete="off" size="10" onClick="displayCalendar(datebox2,'dd/mm/yyyy',this)" type="text" value="<? echo $dia2 ?>">
        <?
        echo '</td>';
        echo '</tr>';

        echo'<tr>';
        echo '<td width="108" colspan="3"><font size="1">Importancia</font></td>';
        echo'</tr>';
        echo'<tr>';
        /*CAJA DE TEXTO QUE CAPTURA LA Importancia*/
        echo '<td width="108" colspan="3"><font size="1"><INPUT TYPE="text" class="ftexto" NAME="vimporta" title="Escribir la importancia de la actividad" size="169" maxlength="255"></font></td>';
        echo '</tr>';
        
        echo '<tr>';
        echo '<td colspan="5"><font size="1">Meta</font></td>';
        echo '</tr>';
        echo '<tr>';
        /*CAJA DE TEXTO QUE CAPTURA LA Meta*/
        echo '<td colspan="5"><font size="1"><INPUT TYPE="text" class="ftexto" NAME="vmeta" title="Escribir la meta a alcanzar en el semestre" size="169" maxlength="255"></font></td>';
        echo '</tr>';
        
        echo '<tr>';
            echo '<td ><font size="1">Medida</font></td>';
            echo '<td ><font size="1">Cant</font></td>';
            echo '<td >&nbsp;&nbsp;&nbsp;&nbsp;<font size="1">Hrs. Semanales</font></td>';
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
            echo '<td ><font size="1"><INPUT TYPE="number" class="ftexto" NAME="vcant" title="Escribir las cantidades que se tomarán en cuenta con respecto a la unidad de medida que hayan utilizado" size="5" maxlength="5" min="0"></font></td>';
            /*ESTA CAJA DE TEXTO CAPTURAS LAS HORAS DESTINADAS A LA MEDIDA */
            echo '<td >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font size="1"><INPUT TYPE="number" class="ftexto" NAME="vhoras" title="Escribir la cantidad de horas que demanda la actividad" size="2" maxlength="2" min="0"></font>';
            /*echo '</td >';*/

            /*echo '<td >';*/
            echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            echo '<font size="1">';

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


            echo '<input type="hidden" name="coduni" value="'.$codigo.'">';
            echo '<input type="hidden" name="viddepe" value="'.$iddepe.'">';
            echo'<input class="btns" type=button onClick="javascript:msj()" value="Agregar"/>';
            echo'</td>';
        echo '</tr>';
    echo '</table>';

} //Fin Validacion Plani --Yoel 23-10-18

    echo '</form>';
}
?>