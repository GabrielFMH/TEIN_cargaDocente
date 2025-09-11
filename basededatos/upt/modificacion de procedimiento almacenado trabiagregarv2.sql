-- ================================================================
-- SCRIPT DE MODIFICACI�N (MIGRACI�N HACIA ADELANTE)
-- Prop�sito: A�adir las nuevas columnas a la tabla 'tab'.
-- ================================================================

-- 1. A�ade la columna para el "Tipo de Actividad"
ALTER TABLE trab
ADD COLUMN tipo_actividad VARCHAR(50) NULL COMMENT 'Guarda la selecci�n del combobox Tipo de Actividad' AFTER actividad;

-- 2. A�ade la columna para el "Detalle" del combobox
ALTER TABLE trab
ADD COLUMN detalle_actividad VARCHAR(100) NULL COMMENT 'Guarda la selecci�n del combobox Detalle' AFTER tipo_actividad;

-- 3. A�ade la columna para la "Dependencia"
ALTER TABLE trab
ADD COLUMN dependencia VARCHAR(200) NULL COMMENT 'Guarda la selecci�n del combobox Dependencia' AFTER iddepe;