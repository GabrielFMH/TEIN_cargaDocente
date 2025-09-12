<?php
/**
 * Custom Error Logger for PHP
 * Saves all PHP errors, warnings, and notices to a log file
 */

// Configuration
define('ERROR_LOG_BASE_DIR', __DIR__ . '/logs');
define('ERROR_LOG_MAX_SIZE', 5 * 1024 * 1024); // 5MB max file size per file

// Create logs directory if it doesn't exist
if (!is_dir(ERROR_LOG_BASE_DIR)) {
    mkdir(ERROR_LOG_BASE_DIR, 0755, true);
}

/**
 * Custom error handler function
 */
function customErrorHandler($errno, $errstr, $errfile, $errline, $errcontext = null) {
    // Error types mapping
    $errorTypes = array(
        E_ERROR => 'E_ERROR',
        E_WARNING => 'E_WARNING',
        E_PARSE => 'E_PARSE',
        E_NOTICE => 'E_NOTICE',
        E_CORE_ERROR => 'E_CORE_ERROR',
        E_CORE_WARNING => 'E_CORE_WARNING',
        E_COMPILE_ERROR => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING => 'E_COMPILE_WARNING',
        E_USER_ERROR => 'E_USER_ERROR',
        E_USER_WARNING => 'E_USER_WARNING',
        E_USER_NOTICE => 'E_USER_NOTICE',
        E_STRICT => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED => 'E_DEPRECATED',
        E_USER_DEPRECATED => 'E_USER_DEPRECATED',
        E_ALL => 'E_ALL'
    );

    $errorType = isset($errorTypes[$errno]) ? $errorTypes[$errno] : 'UNKNOWN';

    // Format the error message
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = sprintf(
        "[%s] %s: %s on line %d\n",
        $timestamp,
        $errorType,
        $errstr,
        $errline
    );

    // Add context if available
    if ($errcontext !== null && is_array($errcontext)) {
        $logMessage .= "Context: " . json_encode($errcontext) . "\n";
    }

    // Add stack trace for errors
    if ($errno === E_ERROR || $errno === E_USER_ERROR || $errno === E_RECOVERABLE_ERROR) {
        $logMessage .= "Stack Trace:\n" . getStackTrace() . "\n";
    }

    $logMessage .= str_repeat('-', 80) . "\n";

    // Write to log file grouped by file
    writeToLogFile($logMessage, $errfile);

    // Don't execute PHP's internal error handler
    return true;
}

/**
 * Custom exception handler
 */
function customExceptionHandler($exception) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = sprintf(
        "[%s] EXCEPTION: %s on line %d\n",
        $timestamp,
        $exception->getMessage(),
        $exception->getLine()
    );

    $logMessage .= "Stack Trace:\n" . $exception->getTraceAsString() . "\n";
    $logMessage .= str_repeat('-', 80) . "\n";

    writeToLogFile($logMessage, $exception->getFile());
}

/**
 * Custom shutdown function to catch fatal errors
 */
function customShutdownFunction() {
    $error = error_get_last();
    if ($error !== null) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = sprintf(
            "[%s] FATAL ERROR: %s on line %d\n",
            $timestamp,
            $error['message'],
            $error['line']
        );

        $logMessage .= "Stack Trace:\n" . getStackTrace() . "\n";
        $logMessage .= str_repeat('-', 80) . "\n";

        writeToLogFile($logMessage, $error['file']);
    }
}

/**
 * Write message to log file grouped by file path with creation timestamp
 */
function writeToLogFile($message, $filePath) {
    // Clean and normalize the file path for filename
    $cleanPath = str_replace(['/', '\\', ':'], ['_', '_', '_'], $filePath);
    $cleanPath = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $cleanPath);

    // Create subdirectory for the file
    $fileDir = ERROR_LOG_BASE_DIR . '/' . $cleanPath;
    if (!is_dir($fileDir)) {
        mkdir($fileDir, 0755, true);
    }

    // Generate log filename with creation date/time
    $creationDate = date('Y-m-d_H-i-s');
    $logFile = $fileDir . '/errors_' . $creationDate . '.log';

    // Check if we need to rotate existing files in this directory
    $existingFiles = glob($fileDir . '/errors_*.log');
    $totalSize = 0;

    foreach ($existingFiles as $existingFile) {
        if (file_exists($existingFile)) {
            $totalSize += filesize($existingFile);
        }
    }

    // If total size exceeds limit, create a compressed archive and clean up
    if ($totalSize > ERROR_LOG_MAX_SIZE) {
        $archiveName = $fileDir . '/archive_' . date('Y-m-d_H-i-s') . '.tar.gz';

        // Create archive of existing files
        createArchive($existingFiles, $archiveName);

        // Remove old files
        foreach ($existingFiles as $existingFile) {
            if (file_exists($existingFile)) {
                unlink($existingFile);
            }
        }
    }

    // Write to the new log file
    file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX);
}

/**
 * Get stack trace
 */
function getStackTrace() {
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    $stackTrace = '';

    foreach ($trace as $index => $frame) {
        $file = isset($frame['file']) ? $frame['file'] : 'unknown';
        $line = isset($frame['line']) ? $frame['line'] : 'unknown';
        $function = isset($frame['function']) ? $frame['function'] : 'unknown';

        $stackTrace .= "#{$index} {$file}({$line}): {$function}\n";
    }

    return $stackTrace;
}

/**
 * Create compressed archive of log files
 */
function createArchive($files, $archivePath) {
    // Simple file concatenation for archive (since we don't have tar/zip extensions)
    $archiveContent = "ARCHIVE CREATED: " . date('Y-m-d H:i:s') . "\n";
    $archiveContent .= "FILES INCLUDED:\n";

    foreach ($files as $file) {
        if (file_exists($file)) {
            $archiveContent .= "- " . basename($file) . " (" . filesize($file) . " bytes)\n";
            $archiveContent .= "=== CONTENT OF " . basename($file) . " ===\n";
            $archiveContent .= file_get_contents($file);
            $archiveContent .= "\n=== END OF " . basename($file) . " ===\n\n";
        }
    }

    file_put_contents($archivePath, $archiveContent);
}

/**
 * Initialize error logging
 */
function initErrorLogging() {
    // Set error reporting to catch all errors
    error_reporting(E_ALL);

    // Set custom error handler
    set_error_handler('customErrorHandler');

    // Set custom exception handler
    set_exception_handler('customExceptionHandler');

    // Set shutdown function for fatal errors
    register_shutdown_function('customShutdownFunction');

    // Log initialization to a general log file
    $timestamp = date('Y-m-d H:i:s');
    $initMessage = sprintf(
        "[%s] Error logging system initialized. Logs grouped by file in: %s\n" . str_repeat('-', 80) . "\n",
        $timestamp,
        ERROR_LOG_BASE_DIR
    );

    // Create a general initialization log
    $initLogFile = ERROR_LOG_BASE_DIR . '/system_initialization.log';
    file_put_contents($initLogFile, $initMessage, FILE_APPEND | LOCK_EX);
}

// Initialize error logging when this file is included
initErrorLogging();

?>