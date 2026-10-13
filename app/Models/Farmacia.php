<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farmacia extends Model
{
    protected $guarded = ['id'];

protected $casts = [
    'fecha_pedido' => 'date',
    'periodo_entrega_inicio' => 'date',
    'periodo_entrega_fin' => 'date',
    'fecha_entrega' => 'date',
    'caducidad' => 'date',
    'fecha_inicio_contrato' => 'date',
    'fecha_fin_contrato' => 'date',
];
public function calcular(): void
{
    $conIva = $this->clas === 'MAT CURACION';
    $entregado = $this->volumen_entregado ?? 0;
    $precio = $this->precio_unitario ?? 0;

    $this->subtotal = round(($this->volumen_requerir ?? 0) * $precio, 2);
    $this->iva = $conIva ? round($this->subtotal * 0.16, 2) : null;
    $this->costo_total = round($this->subtotal + ($this->iva ?? 0), 2);

    if ($this->fecha_pedido) {
        $this->periodo_entrega_inicio = $this->fecha_pedido->copy()->addDay();
        $this->periodo_entrega_fin = $this->periodo_entrega_inicio->copy()->addDays(14);
    }

    $this->costo_total_pedido = round($precio * $entregado * ($conIva ? 1.16 : 1), 2);

    $base = $this->resto_contrato > 0 ? $this->resto_contrato : $this->max_26;
    $this->volumen_pendiente = $base - $entregado;

    if ($this->adj_max_2526 > 0) {
        $this->porcentaje = $this->resto_contrato > 0
            ? round((1 - $this->volumen_pendiente / $this->adj_max_2526) * 100, 2)
            : round($entregado / $this->adj_max_2526 * 100, 2);
    }
}

/** Campos agrupados por sección: [etiqueta, tipo, calculado?] */
public static function secciones(): array
{
    return [
        'Insumo' => [
            'id_anexo' => ['ID (Anexo)', 'texto'],
            'partida' => ['Partida', 'entero'],
            'clas' => ['Clasificación', 'clas'],
            'cucop' => ['CUCoP', 'texto'],
            'clave_insumo' => ['Clave de insumo', 'texto'],
            'descripcion' => ['Descripción', 'area'],
            'unidad' => ['Unidad', 'texto'],
            'temperatura_almacenamiento' => ['Temperatura de almacenamiento (°C)', 'decimal'],
        ],
        'Volúmenes del contrato' => [
            'carga_aamates' => ['Carga AAMATES', 'entero'],
            'adj_min_2526' => ['ADJ DFSSA003985 mínimo (25-26)', 'entero'],
            'adj_max_2526' => ['ADJ DFSSA003985 máximo (25-26)', 'entero'],
            'resto_contrato' => ['Resto del contrato', 'entero'],
            'min_26' => ['Mínimo 2026', 'entero'],
            'max_26' => ['Máximo 2026', 'entero'],
        ],
        'Pedido' => [
            'proveedor' => ['Proveedor', 'texto'],
            'numero_pedido' => ['N° de pedido', 'texto'],
            'numero_procedimiento' => ['N° de procedimiento', 'texto'],
            'precio_unitario' => ['Precio unitario', 'dinero'],
            'volumen_requerir' => ['Volumen a requerir', 'entero'],
            'fecha_pedido' => ['Fecha del pedido', 'fecha'],
            'nombre_os' => ['Nombre OS', 'texto'],
            'subtotal' => ['Subtotal', 'dinero', true],
            'iva' => ['IVA', 'dinero', true],
            'costo_total' => ['Costo total', 'dinero', true],
            'periodo_entrega_inicio' => ['Periodo de entrega (inicio)', 'fecha', true],
            'periodo_entrega_fin' => ['Periodo de entrega (fin)', 'fecha', true],
        ],
        'Entrega' => [
            'numero_entrada' => ['N° de entrada', 'texto'],
            'fecha_entrega' => ['Fecha de entrega', 'fecha'],
            'numero_remision' => ['N° de remisión', 'texto'],
            'volumen_entregado' => ['Volumen entregado', 'entero'],
            'laboratorio_marca' => ['Laboratorio o marca', 'texto'],
            'pais_origen' => ['País de origen', 'texto'],
            'lote' => ['Lote', 'texto'],
            'caducidad' => ['Caducidad', 'fecha'],
            'costo_total_pedido' => ['Costo total por pedido', 'dinero', true],
            'volumen_pendiente' => ['Volumen pendiente', 'entero', true],
            'porcentaje' => ['Porcentaje de avance', 'porcentaje', true],
        ],
        'Contrato' => [
            'estatus_instrumento' => ['Estatus del instrumento jurídico', 'texto'],
            'codigo_contrato_compranet' => ['Código de contrato (CompraNet)', 'texto'],
            'fecha_inicio_contrato' => ['Fecha de inicio de contrato', 'fecha'],
            'fecha_fin_contrato' => ['Fecha de fin de contrato', 'fecha'],
            'monto_minimo_contrato' => ['Monto mínimo del contrato', 'dinero'],
            'monto_maximo_contrato' => ['Monto máximo del contrato', 'dinero'],
            'representante_legal' => ['Representante legal del proveedor', 'texto'],
            'fabricante' => ['Fabricante', 'texto'],
        ],
        'Contacto y seguimiento' => [
            'contacto' => ['Contacto', 'texto'],
            'telefono' => ['Teléfono', 'area'],
            'correos' => ['Correos', 'area'],
            'observaciones' => ['Observaciones', 'area'],
            'estatus' => ['Estatus', 'texto'],
        ],
    ];
}

}
