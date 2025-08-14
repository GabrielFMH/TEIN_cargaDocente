Project Overview
Purpose: Este proyecto es una aplicación web para la gestión de la carga horaria docente, desarrollada sobre un entorno XAMPP, con soporte para bases de datos SQL Server. Está diseñado para registrar, visualizar y gestionar las horas laborales asignadas a docentes.
Key Technologies:
PHP 5.5.33 (versión estable usada en producción)
Apache (XAMPP)
SQL Server (via sqlsrv extension, conectado mediante conexion.php)
HTML, CSS, JavaScript (para interfaces dinámicas)
Extensión php_sqlsrv_55_nts.dll habilitada en el entorno
Main Entry Points:
index.php: Página principal del sistema.
carga_horaria.php: Módulo principal para carga y edición de horarios docentes.
reportes.php: Generación de reportes de carga horaria por docente o curso.
login.php: Autenticación de usuarios (docentes/administradores).
Database Connection:
El archivo conexion.php maneja la conexión a SQL Server usando el driver sqlsrv. Asegúrate de que la extensión php_sqlsrv_55_nts.dll esté activa en php.ini.
Gemini CLI Interaction Guidelines
Preferred Language: English for commands and responses.
Common Tasks:
Debugging PHP scripts (especialmente aquellos relacionados con consultas SQL Server).
Modificando archivos HTML/CSS dentro del directorio dashboard/ o views/.
Verificando la conectividad a SQL Server (conexion.php).
Creando nuevos módulos PHP (por ejemplo, nuevo_docente.php, actualizar_carga.php).
Validando sentencias SQL con sintaxis compatible con T-SQL.
Sensitive Information:
No solicites al CLI que muestre ni copie credenciales de base de datos desde conexion.php.
Evita mostrar cadenas de conexión como Server=...;Database=...;UID=...;PWD=....

XAMPP Root: C:\xampp\htdocs
PHP Version: 5.5.33 (compatibilidad crítica con sqlsrv y mssql extensions)
SQL Server:
Versión soportada: SQL Server 2008 R2 o superior.
Driver requerido: Microsoft Drivers for SQL Server (v3.0 o v4.0).
Extensión activa en php.ini: extension=php_sqlsrv_55_nts.dll
Asegúrate de que php_mssql.dll no esté activo si usas sqlsrv.
Database Schema (Ejemplo):