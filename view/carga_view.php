<?php
// Este es el archivo de la vista: view/carga_view.php

// Verificar si se está solicitando una exportación y evitar cargar la vista en ese caso
if (isset($_GET['exportar'])) {
    // No cargar la vista si se está exportando
    return;
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="tree.css" />

<!--AGREGUE ESTA LINEA DE CODIGO PARA LLAMAR A LA LIBRERIA CALENDARIO-->
<link rel="stylesheet" type="text/css"  href="dhtmlgoodies_calendar.css?random=20051112"/>
<!--HASTA AQUI -->
<!--AGREGUE ESTO PARA EL ACORDEON -->
	<link rel="stylesheet" href="file_demos/jquery-accordion/demo/demo.css" />

	<script type="text/javascript" src="file_demos/jquery-accordion/lib/jquery.js"></script>
	<script type="text/javascript" src="file_demos/jquery-accordion/lib/chili-1.7.pack.js"></script>

	<script type="text/javascript" src="file_demos/jquery-accordion/lib/jquery.easing.js"></script>
	<script type="text/javascript" src="file_demos/jquery-accordion/lib/jquery.dimensions.js"></script>
	<script type="text/javascript" src="file_demos/jquery-accordion/jquery.accordion.js"></script>

	<script type="text/javascript" src="uptlogin/scripts/jquery.rightclick.js"></script>
	<style>
		.tooltip {
			position: relative;
			display: inline-block;
			border-bottom: 1px dotted black;
		}
		.tooltip .tooltiptext {
			visibility: hidden;
			background-color: #555;
			width: 300px;
			color: #fff;
			text-align: center;
			border-radius: 6px;
			padding: 5px 0;
			position: absolute;
			z-index: 1;
			left: 200px;
			margin-left: -60px;
			opacity: 0;
			transition: opacity 0.3s;
		}
		.tooltip:hover .tooltiptext {
			visibility: visible;
			opacity: 1;
		}
	</style>
	<script type="text/javascript">
	jQuery().ready(function(){
		// simple accordion
		jQuery('#list1a').accordion();
		jQuery('#list1b').accordion({
			autoheight: false
		});

		// second simple accordion with special markup
		jQuery('#navigation').accordion({
			active: false,
			header: '.head',
			navigation: true,
			event: 'mouseover',
			fillSpace: true,
			animated: 'easeslide'
		});

		// highly customized accordion
		jQuery('#list2').accordion({
			event: 'mouseover',
			active: '.selected',
			selectedClass: 'active',
			animated: "bounceslide",
			header: "dt"
		}).bind("change.ui-accordion", function(event, ui) {
			jQuery('<div>' + ui.oldHeader.text() + ' hidden, ' + ui.newHeader.text() + ' shown</div>').appendTo('#log');
		});

		// first simple accordion with special markup
		jQuery('#list3').accordion({
			header: 'div.title',
			active: false,
			alwaysOpen: false,
			animated: false,
			autoheight: false
		});

		var wizard = $("#wizard").accordion({
			header: '.title',
			event: false
		});

		var wizardButtons = $([]);
		$("div.title", wizard).each(function(index) {
			wizardButtons = wizardButtons.add($(this)
			.next()
			.children(":button")
			.filter(".next, .previous")
			.click(function() {
				wizard.accordion("activate", index + ($(this).is(".next") ? 1 : -1))
			}));
		});

		// bind to change event of select to control first and seconds accordion
		// similar to tab's plugin triggerTab(), without an extra method
		var accordions = jQuery('#list1a, #list1b, #list2, #list3, #navigation, #wizard');

		jQuery('#switch select').change(function() {
			accordions.accordion("activate", this.selectedIndex-1 );
		});
		jQuery('#close').click(function() {
			accordions.accordion("activate", -1);
		});
		jQuery('#switch2').change(function() {
			accordions.accordion("activate", this.value);
		});
		jQuery('#enable').click(function() {
			accordions.accordion("enable");
		});
		jQuery('#disable').click(function() {
			accordions.accordion("disable");
		});
		jQuery('#remove').click(function() {
			accordions.accordion("destroy");
			wizardButtons.unbind("click");
		});
	});
	</script>
<script type="text/javascript">
 $(document).keydown(function(e){
  var code = (e.keyCode ? e.keyCode : e.which);
  if(code == 116) {
   e.preventDefault();
   jConfirm('¿Deseas recargar la página?', 'Confirmación', function(r) {
       if(r)
        location.reload();
   });
  }
 });
</script>
<script type="text/javascript">
$(function(){
	$('body').noContext();
});
</script>

<!--HASTA AQUI ES EL ACORDEON -->

<title>Net.UPT.edu.pe</title>
<!-- html2pdf.js v0.10.1 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<!-- SheetJS (xlsx.js) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body >

<?php
echo $data['menu'];

echo '<div id="contents">';






echo $data['content'];

?>

<!-- Modal para mostrar autoridades -->
<div id="modalAutoridades" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: white; padding: 20px; border-radius: 10px; width: 700px; max-height: 80%; overflow-y: auto; text-align: center;">
        <h3>Autoridades Académicas</h3>
        <?php if (isset($data['autoridades'])): ?>
            <?php
            $categorias = [
                'rector' => 'Rector',
                'vicecargos' => 'Vicerectores',
                'decanos' => 'Decanos',
                'directores' => 'Directores',
                'coordinadores' => 'Coordinadores',
                'secretarios' => 'Secretarios',
                'jefes' => 'Jefes'
            ];
            $hasAutoridades = false;
            foreach ($categorias as $key => $titulo) {
                if (!empty($data['autoridades'][$key])) {
                    $hasAutoridades = true;
                    break;
                }
            }
            ?>

            <?php if ($hasAutoridades): ?>
                <?php foreach ($categorias as $key => $titulo): ?>
                    <?php if (!empty($data['autoridades'][$key])): ?>
                        <h4><?php echo $titulo; ?>:</h4>
                        <ul style="text-align: left; display: inline-block; margin-bottom: 15px;">
                            <?php foreach ($data['autoridades'][$key] as $autoridad): ?>
                                <li><?php echo htmlspecialchars($autoridad); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No se encontraron autoridades académicas activas.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>No se pudieron cargar las autoridades académicas.</p>
        <?php endif; ?>
        <br><br>
        <button onclick="cerrarModal()" style="padding: 10px 20px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;">Cerrar</button>
    </div>
</div>

</div>
</body>

<!--AGREGUE ESTA LINEA DE CODIGO PARA EL CALENDARIO-->
<script type="text/javascript" src="dhtmlgoodies_calendar.js?random=20060118"></script>
<!--HASTA AQUI CALENDARIO-->

<script language="JavaScript" >
function pele(op)
{
	document.frminicio.submit();
}
function msj()
{
	//var d = document.forms[1].elements[2];
	var d = document.forms[1].vdacti;
	//var h = document.forms[1].elements[7];
	var h = document.forms[1].vhoras;

	if (d.value=="" || (h.value)=="")
	{
		alert ('Debe ingresar el Detalle de Actividad y las Horas.  ¡No se puedo guardar!');
		//alert(d.name);
		//alert(h.name);

	}
	else
	{
		//alert(d.name);
		//alert(h.name);
		document.frmindiv.vagregar.value="Agregar";
		document.frmindiv.submit();
	}
}
</script>
<!--AGREGO ESTO PARA VALIDAR LOS BOTONES DE EDITAR , ELIMINAR-->
	<script LANGUAGE="JavaScript">

    function confirmSubmit()
    {
    var agree=confirm("¿Esta seguro que desea editar la actividad?");
    if (agree)
        return true ;
    else
        return false ;
    }

    function confirmFinalizar(x)
    {

		if (x==0 || x==2)
		{
			alert("Para finalizar la actividad, tiene quer ser aprobada por su jefe inmediato.");
			return false ;
		}
		else
		{
			var agree=confirm("¿Esta seguro que desea finalizar la actividad?");
			if (agree)
				return true ;
			else
				return false ;
		}

    }

    function confirmRevertir()
    {
    var agree=confirm("¿Esta seguro que desea revertir la actividad?");
    if (agree)
        return true ;
    else
        return false ;
    }

	function confirmdelete()
    {
    var agree=confirm("¿Esta seguro que desea eliminar la actividad?");
    if (agree)
        return true ;
    else
        return false ;
    }

	function msjregistrar()
	{

		var nominfo_historial = document.forms[1].vnominfo_historial;
		var dirigido_historial = document.forms[1].vdirigido_historial;
		var cargo_historial = document.forms[1].vcargo_historial;
		var remitente_historial = document.forms[1].vremitente_historial;
		var detalle_historial = document.forms[1].vdetalle_historial;

		if (nominfo_historial.value=="" || (dirigido_historial.value)=="" || (cargo_historial.value)=="" || (remitente_historial.value)=="" || (detalle_historial.value)=="")
		{
			alert ('Debe ingresar datos en cada campo. ');

		}
		else
		{
			document.frmindiv.addhistorial.value="Registrar";
			document.frmindiv.submit();
		}
	}

    </script>
<!--HASTA AQUI ES EL SCRIPT PARA LOS BOTONES-->
<script type="text/javascript">
/* === Abrir diálogo de impresión del navegador (Ctrl+P) === */
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('btnGenerarPDF');
    if (!btn) return;

    btn.addEventListener('click', function (ev) {
        ev.preventDefault();   // evita que el botón envíe el formulario
        /* Opcional: desplazarse al inicio para asegurar que todo se pinte */
        window.scrollTo(0, 0);

        /* Llamamos a la herramienta de impresión nativa del navegador */
        window.print();
    });

    /* === Descargar Excel usando WebScraping === */
    var btnExcelWebScraping = document.getElementById('btnGenerarExcelWebScraping');
if (btnExcelWebScraping) {
    btnExcelWebScraping.addEventListener('click', function(ev) {
        ev.preventDefault();

        // Crear un nuevo libro de trabajo
        var wb = XLSX.utils.book_new();

        // Encontrar el contenedor principal de contenidos
        var contentsDiv = document.getElementById('contents');
        if (!contentsDiv) return;

        // Crear un contenedor temporal para el contenido que queremos exportar
        var tempDiv = document.createElement('div');

        // --- PRIMER WEBSCRAPING: Carga Lectiva ---
        var thElementsLectiva = contentsDiv.querySelectorAll('th[colspan="14"]');
        var startElementLectiva = null;
        for (var i = 0; i < thElementsLectiva.length; i++) {
            if (thElementsLectiva[i].textContent.trim() === 'Detalle de Carga Lectiva') {
                startElementLectiva = thElementsLectiva[i].closest('table');
                break;
            }
        }

        var allElements = contentsDiv.querySelectorAll('*');
        var endElementLectiva = null;
        for (var i = 0; i < allElements.length; i++) {
            if (allElements[i].textContent.trim().includes('Descargar Guía de Usuario')) {
                endElementLectiva = allElements[i];
                break;
            }
        }

        if (startElementLectiva && endElementLectiva) {
            var currentNode = startElementLectiva;
            while (currentNode) {
                if (currentNode.nodeType === 1 && (currentNode === endElementLectiva || currentNode.contains(endElementLectiva))) {
                    break;
                }
                if (currentNode.nodeType === 1) {
                    tempDiv.appendChild(currentNode.cloneNode(true));
                }
                currentNode = currentNode.nextSibling;
            }
        } else {
            console.log("Marcadores para Carga Lectiva no encontrados.");
        }

        // --- SEGUNDO WEBSCRAPING: Carga No Lectiva hasta el marcador final ---
        var thElementsNoLectiva = contentsDiv.querySelectorAll('th[colspan="5"]');
        var startNodeNoLectiva = null;
        // AQUÍ ESTABA EL ERROR: 'ika' ha sido corregido a '0'
        for (var i = 0; i < thElementsNoLectiva.length; i++) {
            if (thElementsNoLectiva[i].textContent.trim() === 'Detalle de Carga No Lectiva') {
                startNodeNoLectiva = thElementsNoLectiva[i].closest('table');
                break;
            }
        }

        var tdElements = contentsDiv.querySelectorAll('td[colspan="7"]');
        var finalEndElement = null;
        for (var i = 0; i < tdElements.length; i++) {
            if (tdElements[i].textContent.trim() === 'CALIFICACION') {
                finalEndElement = tdElements[i];
                break;
            }
        }

        if (startNodeNoLectiva) {
            if (tempDiv.children.length > 0) {
                tempDiv.appendChild(document.createElement('br'));
                tempDiv.appendChild(document.createElement('br'));
            }

            var currentNode = startNodeNoLectiva;
            while (currentNode) {
                if (finalEndElement && currentNode.nodeType === 1 && currentNode.contains(finalEndElement)) {
                    console.log("Deteniendo el scraping antes de la tabla que contiene 'CALIFICACION'.");
                    break;
                }
                if (currentNode.nodeType === 1 && currentNode.tagName === 'TABLE') {
                    tempDiv.appendChild(currentNode.cloneNode(true));
                    tempDiv.appendChild(document.createElement('br'));
                }
                currentNode = currentNode.nextSibling;
            }
        } else {
            console.log("Tabla inicial de Carga No Lectiva no encontrada.");
        }

        // --- 3. EXTRAER EL CAMPO ESPECÍFICO (bloque de actividad no lectiva) ---
        const targetSelect = contentsDiv.querySelector('select[name="vacti_editar1"]');
        if (targetSelect) {
            const fontWrapper = targetSelect.closest('font');
            if (fontWrapper) {
                const clonedBlock = fontWrapper.cloneNode(true);
                const container = document.createElement('div');
                container.innerHTML = '<strong>Actividad No Lectiva Detalle:</strong>';
                container.appendChild(clonedBlock);
                tempDiv.appendChild(container);
                tempDiv.appendChild(document.createElement('br'));
            }
        }

        // --- 4. Procesar los datos: Reemplazar inputs y selects con su valor en texto ---
        tempDiv.querySelectorAll('input[type="text"]').forEach(input => {
            if (input.parentNode) {
                const span = document.createElement('span');
                span.textContent = input.value || '';
                input.parentNode.replaceChild(span, input);
            }
        });

        tempDiv.querySelectorAll('select').forEach(select => {
            const selectedOption = select.querySelector('option[selected]') || select.options[select.selectedIndex];
            const span = document.createElement('span');
            span.textContent = selectedOption?.text || '';
            select.parentNode.replaceChild(span, select);
        });

        // --- 5. Limpieza de celdas ---
        tempDiv.querySelectorAll('td, th').forEach(function(cell) {
            cell.textContent = cell.innerText.trim();
        });

        // --- 6. PROCESAMIENTO PARA CARGA LECTIVA: Añadir guion ---
        const tables = tempDiv.querySelectorAll('table');
        tables.forEach(table => {
            // Buscamos la fila de encabezado
            const headerRow = table.querySelector('tr');
            if (!headerRow) return; // Si la tabla no tiene filas, la ignoramos

            const headers = Array.from(headerRow.querySelectorAll('th, td'));
            const dayColumns = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            const targetIndices = [];

            // Obtenemos los índices de las columnas de los días de la semana
            headers.forEach((header, index) => {
                if (dayColumns.includes(header.textContent.trim())) {
                    targetIndices.push(index);
                }
            });

            // Si encontramos columnas de días, procesamos las filas
            if (targetIndices.length > 0) {
                const dataRows = table.querySelectorAll('tr');
                dataRows.forEach((row, rowIndex) => {
                    if (rowIndex === 0) return; // Omitimos la fila de encabezado

                    const cells = row.querySelectorAll('td');
                    targetIndices.forEach(colIndex => {
                        if (cells[colIndex]) {
                            const cell = cells[colIndex];
                            let text = cell.textContent.trim();
                            // Aplicamos el formato solo si el texto es numérico y tiene la longitud adecuada
                            if (text.length > 5 && /^\d+$/.test(text)) {
                                cell.textContent = text.substring(0, 5) + '-' + text.substring(5);
                            }
                        }
                    });
                });
            }
        });

        // --- 7. Convertir a Excel y descargar ---
        if (tempDiv.children.length > 0) {
            var tempTable = document.createElement('table');
            tempTable.appendChild(tempDiv);
            var ws = XLSX.utils.table_to_sheet(tempTable, {
                sheet: "Carga Docente"
            });

            // Aplicar estilos
            for (var cell in ws) {
                if (cell[0] === '!') continue;
                if (!ws[cell].s) ws[cell].s = {};
                ws[cell].s.border = {
                    top: { style: "thin", color: { auto: 1 } },
                    bottom: { style: "thin", color: { auto: 1 } },
                    left: { style: "thin", color: { auto: 1 } },
                    right: { style: "thin", color: { auto: 1 } }
                };
            }

            XLSX.utils.book_append_sheet(wb, ws, "Carga Docente");
        }

        var filename = 'carga_docente_webscraping_<?php echo $data['idsem']; ?>.xlsx';
        XLSX.writeFile(wb, filename);
    });
}
});

// Función para mostrar el modal de autoridades
document.getElementById('btnVerAutoridades').addEventListener('click', function() {
    document.getElementById('modalAutoridades').style.display = 'block';
});

function cerrarModal() {
    document.getElementById('modalAutoridades').style.display = 'none';
}

// Cerrar modal al hacer clic fuera
window.addEventListener('click', function(event) {
    var modal = document.getElementById('modalAutoridades');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
});
</script>
</html>