<?php

namespace Database\Seeders;

use App\Models\Administracion\Evidencia;
use App\Models\Contabilidad\AsientoContable;
use App\Models\Contabilidad\CuentaContable;
use App\Models\Contabilidad\PeriodoContable;
use App\Models\Empresa;
use Illuminate\Database\Seeder;

class OceanExpressSeeder extends Seeder
{
    public function run(): void
    {
        $empresa = Empresa::firstOrCreate(
            ['nit' => '1123-220994-101-5'],
            [
                'nombre' => 'Ocean Express',
                'nombre_comercial' => 'Ocean Express & Ocean Blu Store',
                'representante_legal' => 'Milena Vanessa Amaya Castillo',
                'actividad_economica' => 'Envíos/Encomiendas EE. UU. – El Salvador, casillero internacional, trámites de aduana y venta de productos en tienda',
                'direccion' => '2a Calle Poniente, Barrio La Merced, costado Silver Gym, Usulután',
                'moneda' => 'USD',
                'activo' => true,
            ]
        );

        $periodo = PeriodoContable::firstOrCreate(
            [
                'empresa_id' => $empresa->id,
                'fecha_inicio' => '2026-09-01',
                'fecha_fin' => '2026-09-30',
            ],
            [
                'nombre' => 'Septiembre 2026',
                'estado' => 'abierto',
            ]
        );

        // Estructura: [codigo, nombre, tipo, naturaleza, permite_movimientos, codigo_padre], tengan cuidado si quieren meter mas registros, esto es del catalogo del cuentas
        $catalogo = [
            ['1', 'ACTIVO', 'activo', 'deudora', false, null],
            ['11', 'ACTIVO CORRIENTE', 'activo', 'deudora', false, '1'],
            ['1101', 'Efectivo y Equivalentes de Efectivo', 'activo', 'deudora', false, '11'],
            ['1101-01', 'Caja General', 'activo', 'deudora', true, '1101'],
            ['1101-02', 'Caja Chica', 'activo', 'deudora', true, '1101'],
            ['1101-03', 'Bancos (Cuenta de Cheques y Ahorros)', 'activo', 'deudora', true, '1101'],
            ['1102', 'Cuentas y Documentos por Cobrar', 'activo', 'deudora', false, '11'],
            ['1102-01', 'Clientes Locales (Envíos / Tienda)', 'activo', 'deudora', true, '1102'],
            ['1102-02', 'Clientes Internacionales', 'activo', 'deudora', true, '1102'],
            ['1102-03', 'Cuentas por Cobrar Varias', 'activo', 'deudora', true, '1102'],
            ['1102-04', '(-) Estimación para Cuentas Incobrables', 'activo', 'acreedora', true, '1102'],
            ['1103', 'Inventarios', 'activo', 'deudora', false, '11'],
            ['1103-01', 'Inventario de Mercadería para la Venta', 'activo', 'deudora', true, '1103'],
            ['1103-02', 'Inventario de Materiales de Empaque', 'activo', 'deudora', true, '1103'],
            ['1104', 'Gastos Pagados por Anticipado', 'activo', 'deudora', false, '11'],
            ['1104-01', 'Alquileres Pagados por Anticipado (Local/Bodega)', 'activo', 'deudora', true, '1104'],
            ['1104-02', 'Seguros Pagados por Anticipado', 'activo', 'deudora', true, '1104'],

            ['12', 'ACTIVO NO CORRIENTE', 'activo', 'deudora', false, '1'],
            ['1201', 'Propiedad, Planta y Equipo', 'activo', 'deudora', false, '12'],
            ['1201-01', 'Mobiliario y Equipo de Oficina', 'activo', 'deudora', true, '1201'],
            ['1201-02', 'Equipo de Cómputo y Oficina', 'activo', 'deudora', true, '1201'],
            ['1201-03', 'Vehículo de Transporte (Móvil para encomiendas)', 'activo', 'deudora', true, '1201'],
            ['1202', 'Depreciación Acumulada', 'activo', 'acreedora', false, '12'],
            ['1202-01', 'Depreciación Acumulada Mobiliario y Equipo', 'activo', 'acreedora', true, '1202'],
            ['1202-02', 'Depreciación Acumulada Equipo de Cómputo y Oficina', 'activo', 'acreedora', true, '1202'],
            ['1202-03', 'Depreciación Acumulada Vehículos', 'activo', 'acreedora', true, '1202'],

            ['2', 'PASIVO', 'pasivo', 'acreedora', false, null],
            ['21', 'PASIVO CORRIENTE', 'pasivo', 'acreedora', false, '2'],
            ['2101', 'Cuentas y Documentos por Pagar a Corto Plazo', 'pasivo', 'acreedora', false, '21'],
            ['2101-01', 'Proveedores Locales', 'pasivo', 'acreedora', true, '2101'],
            ['2101-02', 'Proveedores Internacionales / Agencias de Transporte', 'pasivo', 'acreedora', true, '2101'],
            ['2101-03', 'Transportistas Subcontratados', 'pasivo', 'acreedora', true, '2101'],
            ['2101-04', 'Acreedores Varios', 'pasivo', 'acreedora', true, '2101'],
            ['2102', 'Retenciones y Cotizaciones por Pagar', 'pasivo', 'acreedora', false, '21'],
            ['2102-01', 'Retención de Impuesto sobre la Renta (ISR)', 'pasivo', 'acreedora', true, '2102'],
            ['2102-02', 'Cotizaciones ISSS por Pagar', 'pasivo', 'acreedora', true, '2102'],
            ['2102-03', 'Cotizaciones AFP por Pagar', 'pasivo', 'acreedora', true, '2102'],
            ['2103', 'Impuestos por Pagar', 'pasivo', 'acreedora', false, '21'],
            ['2103-01', 'IVA Débito Fiscal (13%)', 'pasivo', 'acreedora', true, '2103'],
            ['2103-02', 'Pago a Cuenta ISR por Pagar (1.75%)', 'pasivo', 'acreedora', true, '2103'],
            ['2103-03', 'Impuestos Municipales por Pagar (Alcaldía de Usulután)', 'pasivo', 'acreedora', true, '2103'],

            ['22', 'PASIVO NO CORRIENTE', 'pasivo', 'acreedora', false, '2'],
            ['2201', 'Préstamos y Obligaciones Financieras a Largo Plazo', 'pasivo', 'acreedora', false, '22'],
            ['2201-01', 'Préstamos Bancarios', 'pasivo', 'acreedora', true, '2201'],
            ['2201-02', 'Financiamiento para Adquisición de Vehículo', 'pasivo', 'acreedora', true, '2201'],

            ['3', 'PATRIMONIO NETO', 'patrimonio', 'acreedora', false, null],
            ['31', 'PATRIMONIO', 'patrimonio', 'acreedora', false, '3'],
            ['3101', 'Capital Propietario', 'patrimonio', 'acreedora', false, '31'],
            ['3101-01', 'Capital Propietario', 'patrimonio', 'acreedora', true, '3101'],
            ['3102', 'Resultados Acumulados', 'patrimonio', 'acreedora', false, '31'],
            ['3102-01', 'Utilidades de Ejercicios Anteriores', 'patrimonio', 'acreedora', true, '3102'],
            ['3102-02', 'Pérdidas de Ejercicios Anteriores', 'patrimonio', 'deudora', true, '3102'],
            ['3103', 'Resultado del Ejercicio', 'patrimonio', 'acreedora', false, '31'],
            ['3103-01', 'Utilidad del Presente Ejercicio', 'patrimonio', 'acreedora', true, '3103'],
            ['3103-02', 'Pérdida del Presente Ejercicio', 'patrimonio', 'deudora', true, '3103'],

            ['4', 'INGRESOS', 'ingreso', 'acreedora', false, null],
            ['41', 'INGRESOS DE OPERACIÓN', 'ingreso', 'acreedora', false, '4'],
            ['4101', 'Ingresos por Servicios Operativos', 'ingreso', 'acreedora', false, '41'],
            ['4101-01', 'Ingresos por Servicio de Encomiendas EE. UU. / Local', 'ingreso', 'acreedora', true, '4101'],
            ['4101-02', 'Ingresos por Trámites de Aduana y Casillero Internacional', 'ingreso', 'acreedora', true, '4101'],
            ['4101-03', 'Ingresos por Ventas de Tienda', 'ingreso', 'acreedora', true, '4101'],
            ['4101-04', 'Ingresos por Fletes y Servicios de Entrega', 'ingreso', 'acreedora', true, '4101'],
            ['42', 'OTROS INGRESOS', 'ingreso', 'acreedora', false, '4'],
            ['4201', 'Ingresos No Operacionales', 'ingreso', 'acreedora', false, '42'],
            ['4201-01', 'Descuentos Sobre Compras y Servicios', 'ingreso', 'acreedora', true, '4201'],
            ['4201-02', 'Ingresos Varios', 'ingreso', 'acreedora', true, '4201'],

            ['5', 'COSTOS', 'costo', 'deudora', false, null],
            ['51', 'COSTOS DE OPERACIÓN Y SERVICIOS', 'costo', 'deudora', false, '5'],
            ['5101', 'Costos Directos de Servicios de Encomienda', 'costo', 'deudora', false, '51'],
            ['5101-01', 'Pasajes Aéreos de Transporte de Equipaje', 'costo', 'deudora', true, '5101'],
            ['5101-02', 'Gastos Aduanales e Importaciones', 'costo', 'deudora', true, '5101'],
            ['5101-03', 'Pago a Distribuidores Extranjeros / Locales', 'costo', 'deudora', true, '5101'],
            ['5101-04', 'Cajas, Bolsas y Materiales de Empaque', 'costo', 'deudora', true, '5101'],
            ['5102', 'Costos de Ventas', 'costo', 'deudora', false, '51'],
            ['5102-01', 'Costo de Ventas de Mercadería', 'costo', 'deudora', true, '5102'],

            ['6', 'GASTOS DE OPERACIÓN', 'gasto', 'deudora', false, null],
            ['61', 'GASTOS DE ADMINISTRACIÓN', 'gasto', 'deudora', false, '6'],
            ['6101', 'Gastos de Personal', 'gasto', 'deudora', false, '61'],
            ['6101-01', 'Sueldos y Salarios', 'gasto', 'deudora', true, '6101'],
            ['6101-02', 'Horas Extraordinarias y Bonificaciones', 'gasto', 'deudora', true, '6101'],
            ['6102', 'Gastos de Servicios Generales y Gestión', 'gasto', 'deudora', false, '61'],
            ['6102-01', 'Alquiler de Local Comercial Usulután', 'gasto', 'deudora', true, '6102'],
            ['6102-02', 'Alquiler de Bodega EE. UU.', 'gasto', 'deudora', true, '6102'],
            ['6102-03', 'Servicios Básicos', 'gasto', 'deudora', true, '6102'],
            ['6102-04', 'Papelería, Útiles de Oficina y Limpieza', 'gasto', 'deudora', true, '6102'],
            ['6103', 'Depreciaciones y Amortizaciones', 'gasto', 'deudora', false, '61'],
            ['6103-01', 'Depreciación Mobiliario y Equipo de Oficina', 'gasto', 'deudora', true, '6103'],
            ['6103-02', 'Depreciación Equipo de Cómputo y Oficina', 'gasto', 'deudora', true, '6103'],
            ['62', 'GASTOS DE VENTA Y PUBLICIDAD', 'gasto', 'deudora', false, '6'],
            ['6201', 'Gastos de Comercialización', 'gasto', 'deudora', false, '62'],
            ['6201-01', 'Publicidad y Propaganda Digital', 'gasto', 'deudora', true, '6201'],
            ['6201-02', 'Gastos de Representación', 'gasto', 'deudora', true, '6201'],
            ['63', 'GASTOS FINANCIEROS', 'gasto', 'deudora', false, '6'],
            ['6301', 'Gastos Financieros y Bancarios', 'gasto', 'deudora', false, '63'],
            ['6301-01', 'Comisiones Bancarias y POS Tarjetas', 'gasto', 'deudora', true, '6301'],
            ['6301-02', 'Intereses por Préstamos y Financiamiento', 'gasto', 'deudora', true, '6301'],
        ];

        $mapaCuentas = [];
        foreach ($catalogo as [$codigo, $nombre, $tipo, $naturaleza, $permite, $codigoPadre]) {
            $cuenta = CuentaContable::firstOrCreate(
                ['empresa_id' => $empresa->id, 'codigo' => $codigo],
                [
                    'cuenta_padre_id' => $codigoPadre ? ($mapaCuentas[$codigoPadre]->id ?? null) : null,
                    'nombre' => $nombre,
                    'tipo' => $tipo,
                    'naturaleza' => $naturaleza,
                    'permite_movimientos' => $permite,
                    'activa' => true,
                ]
            );
            $mapaCuentas[$codigo] = $cuenta;
        }

        // Evidencias documentales de campo
        Evidencia::firstOrCreate(
            ['empresa_id' => $empresa->id, 'codigo' => 'EV-001'],
            [
                'titulo' => 'Carta de Autorización Ocean Express',
                'descripcion' => 'Anexo A firmado por Milena Vanessa Amaya Castillo autorizando uso académico de datos.',
                'tipo' => 'autorizacion',
                'fecha_documento' => '2026-10-20',
                'emisor' => 'Ocean Express',
                'verificada' => true,
            ]
        );

        Evidencia::firstOrCreate(
            ['empresa_id' => $empresa->id, 'codigo' => 'EV-002'],
            [
                'titulo' => 'Reporte Ejecutivo DTE FacturaSV - Septiembre 2026',
                'descripcion' => 'Resumen fiscal DTE: Ventas netas $3,916.88 + IVA Débito Fiscal $509.16 = $4,426.04.',
                'tipo' => 'factura',
                'fecha_documento' => '2026-10-03',
                'emisor' => 'FacturaSV / Ministerio de Hacienda',
                'verificada' => true,
            ]
        );

        Evidencia::firstOrCreate(
            ['empresa_id' => $empresa->id, 'codigo' => 'EV-003'],
            [
                'titulo' => 'Cuadernos de Control Diario de Envíos y Ventas',
                'descripcion' => 'Anotaciones físicas de recepción de paquetes por libra y ventas de Ocean Blu Store.',
                'tipo' => 'registro_contable',
                'fecha_documento' => '2026-09-30',
                'emisor' => 'Ocean Express Usulután',
                'verificada' => true,
            ]
        );

        // Las 4 partidas oficiales de Septiembre 2026
        if (AsientoContable::where('empresa_id', $empresa->id)->count() === 0) {
            $p1 = AsientoContable::create([
                'empresa_id' => $empresa->id,
                'periodo_contable_id' => $periodo->id,
                'numero_asiento' => 'P-001',
                'fecha' => '2026-09-01',
                'concepto' => 'Asiento de apertura y patrimonio inicial según inventario valuado de activos y efectivo operativo.',
                'tipo' => 'apertura',
                'estado' => 'contabilizado',
                'referencia_documento' => 'EV-001',
            ]);
            $p1->detalles()->createMany([
                ['cuenta_contable_id' => $mapaCuentas['1101-01']->id, 'descripcion' => 'Efectivo inicial en caja', 'debe' => 500.00, 'haber' => 0],
                ['cuenta_contable_id' => $mapaCuentas['1201-01']->id, 'descripcion' => 'Inventario valuado de mobiliario y equipo', 'debe' => 1200.00, 'haber' => 0],
                ['cuenta_contable_id' => $mapaCuentas['1201-02']->id, 'descripcion' => 'Inventario valuado de equipo de cómputo y A/C', 'debe' => 1100.00, 'haber' => 0],
                ['cuenta_contable_id' => $mapaCuentas['3101-01']->id, 'descripcion' => 'Aporte de capital propietario inicial', 'debe' => 0, 'haber' => 2800.00],
            ]);

            $p2 = AsientoContable::create([
                'empresa_id' => $empresa->id,
                'periodo_contable_id' => $periodo->id,
                'numero_asiento' => 'P-002',
                'fecha' => '2026-09-30',
                'concepto' => 'Registro de ingresos por servicios de encomienda y tienda según reporte oficial DTE FacturaSV.',
                'tipo' => 'normal',
                'estado' => 'contabilizado',
                'referencia_documento' => 'EV-002',
            ]);
            $p2->detalles()->createMany([
                ['cuenta_contable_id' => $mapaCuentas['1101-01']->id, 'descripcion' => 'Cobro total facturado al contado', 'debe' => 4426.04, 'haber' => 0],
                ['cuenta_contable_id' => $mapaCuentas['4101-04']->id, 'descripcion' => 'Ingresos netos por servicios de encomienda y tienda', 'debe' => 0, 'haber' => 3916.88],
                ['cuenta_contable_id' => $mapaCuentas['2103-01']->id, 'descripcion' => 'IVA Débito Fiscal 13% generado en ventas', 'debe' => 0, 'haber' => 509.16],
            ]);

            $p3 = AsientoContable::create([
                'empresa_id' => $empresa->id,
                'periodo_contable_id' => $periodo->id,
                'numero_asiento' => 'P-003',
                'fecha' => '2026-09-30',
                'concepto' => 'Pago de gastos operativos fijos mensuales (alquileres Usulután/EE. UU. y salarios).',
                'tipo' => 'normal',
                'estado' => 'contabilizado',
                'referencia_documento' => 'EV-001',
            ]);
            $p3->detalles()->createMany([
                ['cuenta_contable_id' => $mapaCuentas['6102-01']->id, 'descripcion' => 'Alquiler mensual local comercial Usulután', 'debe' => 200.00, 'haber' => 0],
                ['cuenta_contable_id' => $mapaCuentas['6102-02']->id, 'descripcion' => 'Alquiler mensual bodega en EE. UU.', 'debe' => 200.00, 'haber' => 0],
                ['cuenta_contable_id' => $mapaCuentas['6101-01']->id, 'descripcion' => 'Salarios personal eventual del mes', 'debe' => 200.00, 'haber' => 0],
                ['cuenta_contable_id' => $mapaCuentas['1101-01']->id, 'descripcion' => 'Desembolso en efectivo por gastos operativos', 'debe' => 0, 'haber' => 600.00],
            ]);

            $p4 = AsientoContable::create([
                'empresa_id' => $empresa->id,
                'periodo_contable_id' => $periodo->id,
                'numero_asiento' => 'P-004',
                'fecha' => '2026-09-30',
                'concepto' => 'Ajuste por depreciación mensual en línea recta de activos fijos (Mobiliario 5 años; Cómputo 2 años).',
                'tipo' => 'ajuste',
                'estado' => 'contabilizado',
                'referencia_documento' => 'EV-001',
            ]);
            $p4->detalles()->createMany([
                ['cuenta_contable_id' => $mapaCuentas['6103-01']->id, 'descripcion' => 'Gasto depreciación mensual mobiliario ($1,200 / 60m)', 'debe' => 20.00, 'haber' => 0],
                ['cuenta_contable_id' => $mapaCuentas['6103-02']->id, 'descripcion' => 'Gasto depreciación mensual cómputo ($1,100 / 24m)', 'debe' => 45.83, 'haber' => 0],
                ['cuenta_contable_id' => $mapaCuentas['1202-01']->id, 'descripcion' => 'Depreciación acumulada mobiliario y equipo', 'debe' => 0, 'haber' => 20.00],
                ['cuenta_contable_id' => $mapaCuentas['1202-02']->id, 'descripcion' => 'Depreciación acumulada equipo de cómputo', 'debe' => 0, 'haber' => 45.83],
            ]);
        }
    }
}