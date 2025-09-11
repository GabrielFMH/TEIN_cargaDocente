-- ================================================================
-- SCRIPT DE MODIFICACIÓN (MIGRACIÓN HACIA ADELANTE)
-- Propósito: Añadir las nuevas columnas a la tabla 'tab'.
-- ================================================================

-- 1. Añade la columna para el "Tipo de Actividad"
ALTER TABLE trab
ADD COLUMN tipo_actividad VARCHAR(50) NULL COMMENT 'Guarda la selección del combobox Tipo de Actividad' AFTER actividad;

-- 2. Añade la columna para el "Detalle" del combobox
ALTER TABLE trab
ADD COLUMN detalle_actividad VARCHAR(100) NULL COMMENT 'Guarda la selección del combobox Detalle' AFTER tipo_actividad;

-- 3. Añade la columna para la "Dependencia"
ALTER TABLE trab
ADD COLUMN dependencia VARCHAR(200) NULL COMMENT 'Guarda la selección del combobox Dependencia' AFTER iddepe;