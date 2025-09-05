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
    const btnExcelWebScraping = document.getElementById('btnGenerarExcelWebScraping');
    if (btnExcelWebScraping) {
        // Helper para encontrar un elemento que contenga un texto específico
        const findElement = (parent, selector, text) =>
            Array.from(parent.querySelectorAll(selector)).find(el => el.textContent.trim().includes(text));

        btnExcelWebScraping.addEventListener('click', (ev) => {
            ev.preventDefault();

            const contentsDiv = document.getElementById('contents');
            if (!contentsDiv) return;

            const tempDiv = document.createElement('div');

            // --- 1. Definir los marcadores de inicio y fin para el scraping ---
            const startLectiva = findElement(contentsDiv, 'th[colspan="14"]', 'Detalle de Carga Lectiva')?.closest('table');
            const endLectiva = findElement(contentsDiv, '*', 'Descargar Guía de Usuario');
            const startNoLectiva = findElement(contentsDiv, 'th[colspan="5"]', 'Detalle de Carga No Lectiva')?.closest('table');
            const endNoLectiva = findElement(contentsDiv, 'td[colspan="7"]', 'CALIFICACION');

            // --- 2. Realizar el scraping de ambas secciones ---
            // Scrapea la sección de Carga Lectiva
            if (startLectiva) {
                let currentNode = startLectiva;
                while (currentNode) {
                    if (endLectiva && currentNode.nodeType === 1 && currentNode.contains(endLectiva)) break;
                    if (currentNode.nodeType === 1) tempDiv.appendChild(currentNode.cloneNode(true));
                    currentNode = currentNode.nextSibling;
                }
            }

            // Scrapea la Carga No Lectiva y las tablas siguientes (solo elementos <table>)
            if (startNoLectiva) {
                tempDiv.appendChild(document.createElement('br'));
                let currentNode = startNoLectiva;
                while (currentNode) {
                    if (endNoLectiva && currentNode.nodeType === 1 && currentNode.contains(endNoLectiva)) break;
                    if (currentNode.tagName === 'TABLE') {
                        tempDiv.appendChild(currentNode.cloneNode(true));
                        tempDiv.appendChild(document.createElement('br'));
                    }
                    currentNode = currentNode.nextSibling;
                }
            }

            // --- 3. EXTRAER EL CAMPO ESPECÍFICO QUE QUIERES (tu bloque de actividad no lectiva) ---
            // Buscamos el select por su name: vacti_editar1
            const targetSelect = contentsDiv.querySelector('select[name="vacti_editar1"]');
            if (targetSelect) {
                const fontWrapper = targetSelect.closest('font'); // El <font> que contiene todo
                if (fontWrapper) {
                    const clonedBlock = fontWrapper.cloneNode(true);
                    const container = document.createElement('div');
                    container.innerHTML = '<strong>Actividad No Lectiva Detalle:</strong>';
                    container.appendChild(clonedBlock);
                    tempDiv.appendChild(container);
                    tempDiv.appendChild(document.createElement('br'));
                }
            }

            if (tempDiv.children.length === 0) return;

            // --- 4. Procesar los datos: Reemplazar inputs con su valor en texto ---
            tempDiv.querySelectorAll('input[type="text"]').forEach(input => {
                if (input.parentNode) {
                    const span = document.createElement('span');
                    span.textContent = input.value || '';
                    input.parentNode.replaceChild(span, input);
                }
            });

            // Reemplazar selects con su valor seleccionado
            tempDiv.querySelectorAll('select').forEach(select => {
                const selectedOption = select.querySelector('option[selected]') || select.options[select.selectedIndex];
                const span = document.createElement('span');
                span.textContent = selectedOption?.text || '';
                select.parentNode.replaceChild(span, select);
            });

            // --- 5. Generar y descargar el archivo Excel ---
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(tempDiv, { sheet: "Carga Docente" });

            // Aplicar estilos a las celdas
            for (const address in ws) {
                if (address[0] === '!') continue;
                const cellRef = XLSX.utils.decode_cell(address);
                ws[address].s = {
                    border: {
                        top: { style: "thick", color: { rgb: "000000" } },
                        bottom: { style: "thick", color: { rgb: "000000" } },
                        left: { style: "thick", color: { rgb: "000000" } },
                        right: { style: "thick", color: { rgb: "000000" } }
                    },
                    fill: {
                        patternType: "solid",
                        fgColor: { rgb: cellRef.r === 0 ? "CCCCCC" : "FFFFFF" }
                    }
                };
            }

            XLSX.utils.book_append_sheet(wb, ws, "Carga Docente");
            XLSX.writeFile(wb, `carga_docente_EXCEL_<?php echo $data['idsem']; ?>.xlsx`);
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