USE [UPT]
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

ALTER PROCEDURE [dbo].[trabiagregar_v2]
	@Codigo INT,
	@vacti VARCHAR(100),
    @vtipo VARCHAR(50),             -- NUEVO PARÁMETRO: Tipo de Actividad
    @vdetalle VARCHAR(100),        -- NUEVO PARÁMETRO: Detalle del combobox
	@vdacti VARCHAR(100),
	@vimporta VARCHAR(255),
	@vmedida VARCHAR(100),
	@vcant VARCHAR(100),           -- Mantener como VARCHAR si viene así, pero castear en el INSERT
	@vhoras VARCHAR(100),          -- Mantener como VARCHAR si viene así, pero castear en el INSERT
	@vcalif VARCHAR(100),          -- Mantener como VARCHAR si viene así, pero castear en el INSERT
	@vmeta VARCHAR(255),
	@vdatebox SMALLDATETIME,       -- Para fecha_inicio
	@vdatebox2 SMALLDATETIME,      -- Para fecha_fin
    @vporcentaje INT,              -- NUEVO PARÁMETRO: Para la columna 'porcentaje' (antes '','')
	@viddepe INT,
	@vcanthoras INT,               -- Asumo que es el total de horas previas o límite
	@vidsemestre INT,              -- Para idsem
    @vdependencia VARCHAR(200)     -- NUEVO PARÁMETRO: Para la Dependencia
AS
-- Primero, asegurémonos de que @vhoras se pueda convertir a INT antes de usarlo en la condición
-- SQL Server 2012+ tiene TRY_CAST/TRY_CONVERT para manejar errores de conversión gracefully
IF ISNUMERIC(@vhoras) = 1 AND (@vcanthoras + CAST(@vhoras AS INT) < 41)
BEGIN
	-- Es mucho más seguro y legible listar las columnas explícitamente en el INSERT.
	-- De esta forma, si el orden de las columnas en la tabla cambia, tu INSERT no se romperá.
	INSERT INTO tab
	(
		codigo,
		idsem,
		actividad,
        tipo_actividad,       -- Nueva columna
        detalle_actividad,    -- Nueva columna
		dactividad,
		importancia,
		medida,
		cant,                 -- Se castea de VARCHAR a INT
		horas,                -- Se castea de VARCHAR a INT
		calif,                -- Se castea de VARCHAR a INT
		meta,
		otros,
		fecha_inicio,
		fecha_fin,
		porcentaje,           -- Nueva columna y valor
		iddepe,
        dependencia,          -- Nueva columna
		estado,
		fecha_registro,
		fecha_modificacion,
		califNuevo
	)
	VALUES
	(
		@Codigo,
		@vidsemestre,
		@vacti,
        @vtipo,               -- Nuevo valor
        @vdetalle,            -- Nuevo valor
		@vdacti,
		@vimporta,
		@vmedida,
		CAST(@vcant AS INT),    -- Conversión a INT
		CAST(@vhoras AS INT),   -- Conversión a INT
		CAST(@vcalif AS INT),   -- Conversión a INT
		@vmeta,
		'',                     -- Columna 'otros'
		@vdatebox,
		@vdatebox2,
        @vporcentaje,           -- Nuevo valor para porcentaje
		@viddepe,
        @vdependencia,          -- Nuevo valor
		1,                      -- Columna 'estado' (valor fijo)
		GETDATE(),              -- Columna 'fecha_registro'
		GETDATE(),              -- Columna 'fecha_modificacion'
		(10 + CAST(@vcalif AS INT)) -- Columna 'califNuevo' (conversión a INT para la suma)
	);
END
GO