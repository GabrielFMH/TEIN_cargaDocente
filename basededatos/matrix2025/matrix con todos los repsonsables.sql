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
),
DatosAjustados AS (
    SELECT 
        *,
        CASE 
            WHEN TotalMetaEjecutada > MetaAsignada THEN MetaAsignada 
            ELSE TotalMetaEjecutada 
        END AS ME_Ajustado
    FROM DatosFiltrados
),
ResponsablesAgrupados AS (
    SELECT 
        Indicador,
        STUFF(
            (SELECT DISTINCT ', ' + T2.Responsable
             FROM DatosAjustados T2
             WHERE T1.Indicador = T2.Indicador
             FOR XML PATH('')), 
            1, 2, '') AS Responsable
    FROM DatosAjustados T1
    GROUP BY Indicador
),
PivotData AS (
    SELECT 
        -- Metadatos
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
        Indicador,
        MAX(IdIndicador) AS IdIndicador,
        MAX(IdPtoParametroMedicion) AS IdPtoParametroMedicion,
        MAX(IdPto_ClasificacionIndicador) AS IdPto_ClasificacionIndicador,
        MAX(IdPtoSentidoIndicador) AS IdPtoSentidoIndicador,
        MAX(UnidadMedida) AS UnidadMedida,
        MAX(BaseIndicador) AS BaseIndicador,
        AVG(DPO_MetaPrevista) AS DPO_MetaPrevista,
        AVG(PorcentajeActividad) AS PorcentajeActividad,
        MAX(UltimoAnio) AS UltimoAnio,

        -- Metas por año
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
    GROUP BY Indicador
),
Calculos AS (
    SELECT 
        *,
        -- %PPO
        CAST(CASE WHEN [2023_MA] <> 0 THEN ([2023_ME] * 100.0 / [2023_MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2023_%PPO],
        CAST(CASE WHEN [2024_MA] <> 0 THEN ([2024_ME] * 100.0 / [2024_MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2024_%PPO],
        CAST(CASE WHEN [2025_MA] <> 0 THEN ([2025_ME] * 100.0 / [2025_MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2025_%PPO],
        CAST(CASE WHEN [2026_MA] <> 0 THEN ([2026_ME] * 100.0 / [2026_MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2026_%PPO],
        CAST(CASE WHEN [2027_MA] <> 0 THEN ([2027_ME] * 100.0 / [2027_MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2027_%PPO],

        -- INICIO DE MODIFICACIÓN: Lógica condicional para [T MA] (MTPE)
        CAST(
            CASE
                -- Si es tipo 1, solo toma el valor de MA del último año para el total.
                WHEN IdPto_ClasificacionIndicador = 1 THEN
                    CASE
                        WHEN UltimoAnio = 2023 THEN ISNULL([2023_MA], 0)
                        WHEN UltimoAnio = 2024 THEN ISNULL([2024_MA], 0)
                        WHEN UltimoAnio = 2025 THEN ISNULL([2025_MA], 0)
                        WHEN UltimoAnio = 2026 THEN ISNULL([2026_MA], 0)
                        WHEN UltimoAnio = 2027 THEN ISNULL([2027_MA], 0)
                        ELSE 0
                    END
                -- Para los otros tipos (0 y 2), suma los MA de todos los años.
                ELSE ISNULL([2023_MA],0) + ISNULL([2024_MA],0) + ISNULL([2025_MA],0) + ISNULL([2026_MA],0) + ISNULL([2027_MA],0)
            END
        AS DECIMAL(18,2)) AS [T MA],
        -- FIN DE MODIFICACIÓN

        -- TOTAL ME (la lógica ya es correcta desde el ajuste anterior)
        CAST(
            CASE
                WHEN IdPto_ClasificacionIndicador = 1 AND UltimoAnio = 2023 THEN ISNULL([2023_ME], 0)
                WHEN IdPto_ClasificacionIndicador = 1 AND UltimoAnio = 2024 THEN ISNULL([2024_ME], 0)
                WHEN IdPto_ClasificacionIndicador = 1 AND UltimoAnio = 2025 THEN ISNULL([2025_ME], 0)
                WHEN IdPto_ClasificacionIndicador = 1 AND UltimoAnio = 2026 THEN ISNULL([2026_ME], 0)
                WHEN IdPto_ClasificacionIndicador = 1 AND UltimoAnio = 2027 THEN ISNULL([2027_ME], 0)
                WHEN IdPto_ClasificacionIndicador IN (0, 2) THEN ISNULL([2023_ME],0) + ISNULL([2024_ME],0) + ISNULL([2025_ME],0) + ISNULL([2026_ME],0) + ISNULL([2027_ME],0)
                ELSE 0 
            END
        AS DECIMAL(18,2)) AS [T ME]
    FROM PivotData
),
CalculoFinal AS (
    SELECT 
        *,
        -- Los cálculos de %PPE ahora usan el [T MA] correcto que fue calculado en el paso anterior.
        CAST(CASE WHEN [T MA] <> 0 THEN ([2023_ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2023_%PPE],
        CAST(CASE WHEN [T MA] <> 0 THEN ([2024_ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2024_%PPE],
        CAST(CASE WHEN [T MA] <> 0 THEN ([2025_ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2025_%PPE],
        CAST(CASE WHEN [T MA] <> 0 THEN ([2026_ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2026_%PPE],
        CAST(CASE WHEN [T MA] <> 0 THEN ([2027_ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2027_%PPE],
        
        -- El %TOTAL también usará automáticamente el [T MA] correcto.
        CAST(CASE WHEN [T MA] <> 0 THEN ([T ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [% TOTAL]
    FROM Calculos
)

-- RESULTADO FINAL
SELECT 
    cf.IdLineaAccion,
    cf.IdPtoPerspectiva,
    cf.Perspectiva,
    cf.Estrategia,
    cf.EjeEstrategico,
    cf.IdObjetivoEspecifico,
    cf.ObjetivoEspecifico,
    cf.IdObjetivoEstrategico,
    cf.ObjetivoEstrategico,
    cf.LineaAccion,
    cf.CodigoLineaAccion,
    cf.Indicador,
    cf.IdIndicador,
    cf.IdPtoParametroMedicion,
    cf.IdPto_ClasificacionIndicador,
    cf.IdPtoSentidoIndicador,
    cf.UnidadMedida,
    cf.BaseIndicador,
    CAST(cf.DPO_MetaPrevista AS DECIMAL(18,2)) AS DPO_MetaPrevista,
    CAST(cf.PorcentajeActividad AS DECIMAL(18,2)) AS PorcentajeActividad,
    ra.Responsable,
    cf.[T MA] AS MTPE,
    CAST(cf.[2023_MA] AS DECIMAL(18,2)) AS [2023_MA],
    CAST(cf.[2023_ME] AS DECIMAL(18,2)) AS [2023_ME],
    cf.[2023_%PPO],
    cf.[2023_%PPE],
    CAST(cf.[2024_MA] AS DECIMAL(18,2)) AS [2024_MA],
    CAST(cf.[2024_ME] AS DECIMAL(18,2)) AS [2024_ME],
    cf.[2024_%PPO],
    cf.[2024_%PPE],
    CAST(cf.[2025_MA] AS DECIMAL(18,2)) AS [2025_MA],
    CAST(cf.[2025_ME] AS DECIMAL(18,2)) AS [2025_ME],
    cf.[2025_%PPO],
    cf.[2025_%PPE],
    CAST(cf.[2026_MA] AS DECIMAL(18,2)) AS [2026_MA],
    CAST(cf.[2026_ME] AS DECIMAL(18,2)) AS [2026_ME],
    cf.[2026_%PPO],
    cf.[2026_%PPE],
    CAST(cf.[2027_MA] AS DECIMAL(18,2)) AS [2027_MA],
    CAST(cf.[2027_ME] AS DECIMAL(18,2)) AS [2027_ME],
    cf.[2027_%PPO],
    cf.[2027_%PPE],
    cf.[T MA],
    cf.[T ME],
    cf.[% TOTAL]
FROM CalculoFinal cf
JOIN ResponsablesAgrupados ra ON cf.Indicador = ra.Indicador
ORDER BY cf.Indicador;