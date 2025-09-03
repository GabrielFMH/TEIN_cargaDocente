<div class="notification-container"></div>
<style>
    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        z-index: 1000; /* Para asegurarse de que esté encima de otros elementos */
    }

    .notification {
        background-color: #f8d7da; /* Color de fondo rojo claro, puedes cambiarlo según tu diseño */
        color: #721c24; /* Color de texto oscuro */
        padding: 10px 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Sombra ligera */
        margin-bottom: 10px; /* Espacio entre notificaciones */
        display: flex;
        flex-direction: column;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }

    .notification-title {
        font-size: 15px;
        font-weight: bold;
    }

    .notification-content {
        font-size: 12px;
        cursor: pointer;
    }

    .close-btn {
        cursor: pointer;
        border: none;
        background: none;
        padding: 0;
        margin: 0;
        width: 24px;
        height: 24px;
        fill: #721c24; /* Color del icono */
    }
</style>
<script>
    function showNotification($titulo, $contenido) {
        // Crear un elemento de notificación
        var notification = document.createElement('div');
        notification.className = 'notification';

        // Header de la notificación
        var header = document.createElement('div');
        header.className = 'notification-header';

        // Título de la notificación
        var title = document.createElement('div');
        title.className = 'notification-title';
        title.textContent = $titulo;
        header.appendChild(title);

        // Botón para cerrar la notificación
        var closeButton = document.createElement('button');
        closeButton.className = 'close-btn';
        closeButton.onclick = function() {
            notification.remove();
        };

        // Imagen SVG para el botón de cerrar
        var closeIcon = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        closeIcon.setAttribute("width", "24");
        closeIcon.setAttribute("height", "24");
        closeIcon.setAttribute("viewBox", "0 0 24 24");
        closeIcon.innerHTML = "<path d=\"M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z\"></path>";

        closeButton.appendChild(closeIcon);
        header.appendChild(closeButton);

        notification.appendChild(header);

        // Contenido de la notificación
        var content = document.createElement('div');
        content.className = 'notification-content';
        content.textContent = $contenido;
        content.addEventListener('click', function() {
            // Copiar el contenido al portapapeles
            var text = content.textContent;
            navigator.clipboard.writeText(text).then(function() {
                // console.log('Texto copiado al portapapeles: ' + text);
                // alert('Texto copiado al portapapeles: ' + text);
            }, function(err) {
                // console.error('Error al copiar al portapapeles: ', err);
                // alert('Error al copiar al portapapeles');
            });
        });
        notification.appendChild(content);

        // Agregar el elemento de notificación al contenedor
        var container = document.querySelector('.notification-container');
        container.appendChild(notification);

        // Remover la notificación después de cierto tiempo
        setTimeout(function() {
            notification.remove();
        }, 60000); // Remover después de 3 segundos (ajusta según tus necesidades)
    }
</script>

<?php

    function MostrarTextoAdmin($texto, $script)
    {
        $MostrarInformacion = true;
        $AdminIPS = array();
        array_push($AdminIPS, '172.30.117.241');
        array_push($AdminIPS, '172.30.117.11');
        array_push($AdminIPS, '172.30.117.9');

        $bolAdmin = false;
        for ($ip = 0; $ip < count($AdminIPS); $ip++) { 
            if( trim($AdminIPS[$ip]) == $_SERVER['REMOTE_ADDR'] ){
                $bolAdmin = true;
            }
        }
        if($bolAdmin && $MostrarInformacion){
            echo "<script>";
            echo "showNotification('".addslashes($texto)."', '".addslashes($script)."');";
            // echo "showNotification('".$texto."', '".$script."');";
            echo "</script>";
        }
    }
?>