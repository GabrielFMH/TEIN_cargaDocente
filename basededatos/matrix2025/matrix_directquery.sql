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

    -- Responsables concatenados (subconsulta escalar compatible con DirectQuery)
    (
        SELECT STRING_AGG(Responsable, ', ') 
        FROM (
            SELECT DISTINCT Responsable
            FROM Pto_PlanEstrategicoCalculado pec_inner
            WHERE pec_inner.Indicador = cf.Indicador
              AND pec_inner.Estado = 1
        ) AS distinct_resp
    ) AS Responsable,

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

FROM (

    -- Nivel: CalculoFinal (basado en Calculos)
    SELECT 
        *,
        CAST(CASE WHEN [T MA] <> 0 THEN ([2023_ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2023_%PPE],
        CAST(CASE WHEN [T MA] <> 0 THEN ([2024_ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2024_%PPE],
        CAST(CASE WHEN [T MA] <> 0 THEN ([2025_ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2025_%PPE],
        CAST(CASE WHEN [T MA] <> 0 THEN ([2026_ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2026_%PPE],
        CAST(CASE WHEN [T MA] <> 0 THEN ([2027_ME] * 100.0 / [T MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2027_%PPE],
        
        CAST(
            CASE
                WHEN IdPto_ClasificacionIndicador = 2 THEN
                    CASE
                        WHEN IdIndicador IN (623, 627, 629, 662) THEN
                            CASE 
                                WHEN ISNULL(AvgValorAuxiliar01PlanOperativo, 0) <> 0 
                                THEN (AvgValorEjecutadoPO * 100.0 / AvgValorAuxiliar01PlanOperativo)
                                ELSE NULL 
                            END
                        WHEN IdIndicador IN (624, 625, 628) THEN
                            CASE 
                                WHEN ISNULL(AvgValorInicialPE, 0) <> 0 
                                THEN ((AvgValorEjecutadoPO - AvgValorInicialPE) * 100.0 / AvgValorInicialPE)
                                ELSE NULL 
                            END
                        WHEN IdIndicador = 626 THEN AvgValorInicialPE
                        ELSE 
                            CASE WHEN [T MA] <> 0 THEN ([T ME] * 100.0 / [T MA]) ELSE NULL END
                    END
                ELSE
                    CASE WHEN [T MA] <> 0 THEN ([T ME] * 100.0 / [T MA]) ELSE NULL END
            END
        AS DECIMAL(18,2)) AS [% TOTAL]

    FROM (

        -- Nivel: Calculos (basado en PivotData)
        SELECT 
            *,
            CAST(CASE WHEN [2023_MA] <> 0 THEN ([2023_ME] * 100.0 / [2023_MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2023_%PPO],
            CAST(CASE WHEN [2024_MA] <> 0 THEN ([2024_ME] * 100.0 / [2024_MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2024_%PPO],
            CAST(CASE WHEN [2025_MA] <> 0 THEN ([2025_ME] * 100.0 / [2025_MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2025_%PPO],
            CAST(CASE WHEN [2026_MA] <> 0 THEN ([2026_ME] * 100.0 / [2026_MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2026_%PPO],
            CAST(CASE WHEN [2027_MA] <> 0 THEN ([2027_ME] * 100.0 / [2027_MA]) ELSE NULL END AS DECIMAL(18,2)) AS [2027_%PPO],

            CAST(
                CASE
                    WHEN IdPto_ClasificacionIndicador = 1 THEN
                        CASE
                            WHEN UltimoAnio = 2023 THEN ISNULL([2023_MA], 0)
                            WHEN UltimoAnio = 2024 THEN ISNULL([2024_MA], 0)
                            WHEN UltimoAnio = 2025 THEN ISNULL([2025_MA], 0)
                            WHEN UltimoAnio = 2026 THEN ISNULL([2026_MA], 0)
                            WHEN UltimoAnio = 2027 THEN ISNULL([2027_MA], 0)
                            ELSE 0
                        END
                    ELSE ISNULL([2023_MA],0) + ISNULL([2024_MA],0) + ISNULL([2025_MA],0) + ISNULL([2026_MA],0) + ISNULL([2027_MA],0)
                END
            AS DECIMAL(18,2)) AS [T MA],

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

        FROM (

            -- Nivel: PivotData (agregado por Indicador)
            SELECT 
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

                AVG(ValorEjecutadoPO) AS AvgValorEjecutadoPO,
                AVG(ValorAuxiliar01PlanOperativo) AS AvgValorAuxiliar01PlanOperativo,
                AVG(ValorInicialPE) AS AvgValorInicialPE,

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

            FROM (

                -- Nivel: DatosAjustados
                SELECT 
                    df.*,
                    CASE 
                        WHEN df.TotalMetaEjecutada > df.MetaAsignada THEN df.MetaAsignada 
                        ELSE df.TotalMetaEjecutada 
                    END AS ME_Ajustado
                FROM (

                    -- Nivel: DatosFiltrados
                    SELECT 
                        db.*,
                        u.UltimoAnio
                    FROM (

                        -- Nivel: DatosBase (con ROW_NUMBER)
                        SELECT 
                            *,
                            ROW_NUMBER() OVER (PARTITION BY IdPto_ClasificacionIndicador, IdIndicador ORDER BY Anio DESC) AS rn
                        FROM Pto_PlanEstrategicoCalculado
                        WHERE Estado = 1

                    ) AS db
                    LEFT JOIN (

                        -- Nivel: UltimoAnioNoNullMA
                        SELECT 
                            IdPto_ClasificacionIndicador,
                            IdIndicador,
                            MAX(Anio) AS UltimoAnio
                        FROM (
                            SELECT 
                                *,
                                ROW_NUMBER() OVER (PARTITION BY IdPto_ClasificacionIndicador, IdIndicador ORDER BY Anio DESC) AS rn
                            FROM Pto_PlanEstrategicoCalculado
                            WHERE Estado = 1
                        ) AS db_inner
                        WHERE MetaAsignada IS NOT NULL
                        GROUP BY IdPto_ClasificacionIndicador, IdIndicador

                    ) AS u 
                        ON db.IdPto_ClasificacionIndicador = u.IdPto_ClasificacionIndicador
                        AND db.IdIndicador = u.IdIndicador

                ) AS df

            ) AS da

            GROUP BY Indicador

        ) AS pd

    ) AS calc

) AS cf;