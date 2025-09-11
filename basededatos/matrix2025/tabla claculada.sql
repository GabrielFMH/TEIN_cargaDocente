WITH DatosBase AS (
    SELECT 
        *,
        ROW_NUMBER() OVER (PARTITION BY IdPto_ClasificacionIndicador, IdIndicador ORDER BY Anio DESC) AS rn
    FROM Pto_PlanEstrategicoCalculado
    WHERE Estado = 1
),
UltimoAnioNoNullMA AS (
    SELECT 
        IdPto_ClasificacionIndicador,
        IdIndicador,
        MAX(Anio) AS UltimoAnio
    FROM DatosBase
    WHERE MetaAsignada IS NOT NULL
    GROUP BY IdPto_ClasificacionIndicador, IdIndicador
),
DatosFiltrados AS (
    SELECT 
        d.*,
        u.UltimoAnio
    FROM DatosBase d
    LEFT JOIN UltimoAnioNoNullMA u 
        ON d.IdPto_ClasificacionIndicador = u.IdPto_ClasificacionIndicador
        AND d.IdIndicador = u.IdIndicador
    WHERE 
        (d.IdPto_ClasificacionIndicador IN (0, 2)) 
        OR 
        (d.IdPto_ClasificacionIndicador = 1 AND d.Anio = u.UltimoAnio)
),
-- ? AJUSTE CLAVE: Limitar ME al valor de MA
DatosAjustados AS (
    SELECT 
        *,
        CASE 
            WHEN TotalMetaEjecutada > MetaAsignada THEN MetaAsignada 
            ELSE TotalMetaEjecutada 
        END AS ME_Ajustado
    FROM DatosFiltrados
),

-- ✅ CORRECCIÓN: AQUÍ VA LA COMA DESPUÉS DE DatosAjustados
-- Y AHORA PivotData como siguiente CTE

PivotData AS (
    SELECT 
        -- Metadatos: tomamos MAX (asumiendo que son consistentes por Indicador)
        MAX(IdLineaAccion) AS IdLineaAccion,
        MAX(IdPtoPerspectiva) AS IdPtoPerspectiva,
        MAX(Perspectiva) AS Perspectiva,
        MAX(Estrategia) AS Estrategia,
        MAX(EjeEstrategico) AS EjeEstrategico,
        MAX(IdObjetivoEspecifico) AS IdObjetivoEspecifico,
        MAX(ObjetivoEspecifico) AS ObjetivoEspecifico,
        MAX(IdObjetivoEstrategico) AS IdObjetivoEstrategico,
        MAX(ObjetivoEstrategico) AS ObjetivoEstrategico,
        MAX(LineaAccion) AS LineaAccion,
        MAX(CodigoLineaAccion) AS CodigoLineaAccion,
        Indicador,  -- ¡Clave! Agrupamos por este campo
        MAX(IdIndicador) AS IdIndicador,
        MAX(IdPtoParametroMedicion) AS IdPtoParametroMedicion,
        MAX(IdPto_ClasificacionIndicador) AS IdPto_ClasificacionIndicador,
        MAX(IdPtoSentidoIndicador) AS IdPtoSentidoIndicador,
        MAX(UnidadMedida) AS UnidadMedida,
        MAX(BaseIndicador) AS BaseIndicador,
        AVG(DPO_MetaPrevista) AS DPO_MetaPrevista,  -- Promedio
        AVG(PorcentajeActividad) AS PorcentajeActividad,  -- Promedio
        MAX(Responsable) AS Responsable,  -- Tomamos uno representativo

        -- Metas por año → PROMEDIAMOS en lugar de MAX
        AVG(CASE WHEN Anio = 2023 THEN MetaAsignada END) AS [2023_MA],
        AVG(CASE WHEN Anio = 2023 THEN ME_Ajustado END) AS [2023_ME],
        AVG(CASE WHEN Anio = 2024 THEN MetaAsignada END) AS [2024_MA],
        AVG(CASE WHEN Anio = 2024 THEN ME_Ajustado END) AS [2024_ME],
        AVG(CASE WHEN Anio = 2025 THEN MetaAsignada END) AS [2025_MA],
        AVG(CASE WHEN Anio = 2025 THEN ME_Ajustado END) AS [2025_ME],
        AVG(CASE WHEN Anio = 2026 THEN MetaAsignada END) AS [2026_MA],
        AVG(CASE WHEN Anio = 2026 THEN ME_Ajustado END) AS [2026_ME],
        AVG(CASE WHEN Anio = 2027 THEN MetaAsignada END) AS [2027_MA],
        AVG(CASE WHEN Anio = 2027 THEN ME_Ajustado END) AS [2027_ME]
    FROM DatosAjustados
    GROUP BY Indicador  -- ¡AGRUPAMOS SOLO POR INDICADOR!
),

-- Paso 2: Calculos → igual, pero ahora sobre promedios
Calculos AS (
    SELECT 
        *,
        -- %PPO = ME / MA * 100 → CORREGIDO (progreso real del año)
        CAST(
            CASE WHEN [2023_MA] <> 0 THEN ([2023_ME] * 100.0 / [2023_MA]) ELSE NULL END 
            AS DECIMAL(18,2)
        ) AS [2023_%PPO],
        CAST(
            CASE WHEN [2024_MA] <> 0 THEN ([2024_ME] * 100.0 / [2024_MA]) ELSE NULL END 
            AS DECIMAL(18,2)
        ) AS [2024_%PPO],
        CAST(
            CASE WHEN [2025_MA] <> 0 THEN ([2025_ME] * 100.0 / [2025_MA]) ELSE NULL END 
            AS DECIMAL(18,2)
        ) AS [2025_%PPO],
        CAST(
            CASE WHEN [2026_MA] <> 0 THEN ([2026_ME] * 100.0 / [2026_MA]) ELSE NULL END 
            AS DECIMAL(18,2)
        ) AS [2026_%PPO],
        CAST(
            CASE WHEN [2027_MA] <> 0 THEN ([2027_ME] * 100.0 / [2027_MA]) ELSE NULL END 
            AS DECIMAL(18,2)
        ) AS [2027_%PPO],

        -- %PPE = progreso esperado (depende de clasificación)
        CAST(
            CASE 
                WHEN IdPto_ClasificacionIndicador IN (0,2) AND [2023_MA] <> 0 
                    THEN ([2023_ME] * 100.0 / [2023_MA]) / 5.0
                WHEN IdPto_ClasificacionIndicador = 1 AND [2023_MA] <> 0 
                    THEN [2023_ME] * 100.0 / [2023_MA]
                ELSE NULL 
            END 
            AS DECIMAL(18,2)
        ) AS [2023_%PPE],
        CAST(
            CASE 
                WHEN IdPto_ClasificacionIndicador IN (0,2) AND [2024_MA] <> 0 
                    THEN ([2024_ME] * 100.0 / [2024_MA]) / 5.0
                WHEN IdPto_ClasificacionIndicador = 1 AND [2024_MA] <> 0 
                    THEN [2024_ME] * 100.0 / [2024_MA]
                ELSE NULL 
            END 
            AS DECIMAL(18,2)
        ) AS [2024_%PPE],
        CAST(
            CASE 
                WHEN IdPto_ClasificacionIndicador IN (0,2) AND [2025_MA] <> 0 
                    THEN ([2025_ME] * 100.0 / [2025_MA]) / 5.0
                WHEN IdPto_ClasificacionIndicador = 1 AND [2025_MA] <> 0 
                    THEN [2025_ME] * 100.0 / [2025_MA]
                ELSE NULL 
            END 
            AS DECIMAL(18,2)
        ) AS [2025_%PPE],
        CAST(
            CASE 
                WHEN IdPto_ClasificacionIndicador IN (0,2) AND [2026_MA] <> 0 
                    THEN ([2026_ME] * 100.0 / [2026_MA]) / 5.0
                WHEN IdPto_ClasificacionIndicador = 1 AND [2026_MA] <> 0 
                    THEN [2026_ME] * 100.0 / [2026_MA]
                ELSE NULL 
            END 
            AS DECIMAL(18,2)
        ) AS [2026_%PPE],
        CAST(
            CASE 
                WHEN IdPto_ClasificacionIndicador IN (0,2) AND [2027_MA] <> 0 
                    THEN ([2027_ME] * 100.0 / [2027_MA]) / 5.0
                WHEN IdPto_ClasificacionIndicador = 1 AND [2027_MA] <> 0 
                    THEN [2027_ME] * 100.0 / [2027_MA]
                ELSE NULL 
            END 
            AS DECIMAL(18,2)
        ) AS [2027_%PPE],

        -- TOTALES → SUMA de promedios anuales
        CAST(
            ISNULL([2023_MA],0) + ISNULL([2024_MA],0) + ISNULL([2025_MA],0) + ISNULL([2026_MA],0) + ISNULL([2027_MA],0) 
            AS DECIMAL(18,2)
        ) AS [T MA],
        CAST(
            ISNULL([2023_ME],0) + ISNULL([2024_ME],0) + ISNULL([2025_ME],0) + ISNULL([2026_ME],0) + ISNULL([2027_ME],0) 
            AS DECIMAL(18,2)
        ) AS [T ME]
    FROM PivotData
),

CalculoFinal AS (
    SELECT 
        *,
        CAST(
            CASE 
                WHEN [T MA] <> 0 THEN ([T ME] * 100.0 / [T MA]) 
                ELSE NULL 
            END 
            AS DECIMAL(18,2)
        ) AS [% TOTAL]
    FROM Calculos
)

-- RESULTADO FINAL: AGRUPADO POR INDICADOR, PROMEDIOS NUMÉRICOS, 2 DECIMALES
SELECT 
    IdLineaAccion,
    IdPtoPerspectiva,
    Perspectiva,
    Estrategia,
    EjeEstrategico,
    IdObjetivoEspecifico,
    ObjetivoEspecifico,
    IdObjetivoEstrategico,
    ObjetivoEstrategico,
    LineaAccion,
    CodigoLineaAccion,
    Indicador,
    IdIndicador,
    IdPtoParametroMedicion,
    IdPto_ClasificacionIndicador,
    IdPtoSentidoIndicador,
    UnidadMedida,
    BaseIndicador,
    CAST(DPO_MetaPrevista AS DECIMAL(18,2)) AS DPO_MetaPrevista,
    CAST(PorcentajeActividad AS DECIMAL(18,2)) AS PorcentajeActividad,
    Responsable,

    [T MA] AS MTPE,

    CAST([2023_MA] AS DECIMAL(18,2)) AS [2023_MA],
    CAST([2023_ME] AS DECIMAL(18,2)) AS [2023_ME],
    [2023_%PPO],
    [2023_%PPE],

    CAST([2024_MA] AS DECIMAL(18,2)) AS [2024_MA],
    CAST([2024_ME] AS DECIMAL(18,2)) AS [2024_ME],
    [2024_%PPO],
    [2024_%PPE],

    CAST([2025_MA] AS DECIMAL(18,2)) AS [2025_MA],
    CAST([2025_ME] AS DECIMAL(18,2)) AS [2025_ME],
    [2025_%PPO],
    [2025_%PPE],

    CAST([2026_MA] AS DECIMAL(18,2)) AS [2026_MA],
    CAST([2026_ME] AS DECIMAL(18,2)) AS [2026_ME],
    [2026_%PPO],
    [2026_%PPE],

    CAST([2027_MA] AS DECIMAL(18,2)) AS [2027_MA],
    CAST([2027_ME] AS DECIMAL(18,2)) AS [2027_ME],
    [2027_%PPO],
    [2027_%PPE],

    [T MA],
    [T ME],
    [% TOTAL]

FROM CalculoFinal
ORDER BY Indicador;