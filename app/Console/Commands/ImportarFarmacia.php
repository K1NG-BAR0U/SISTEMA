<?php

namespace App\Console\Commands;

use App\Models\Farmacia;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportarFarmacia extends Command
{
    protected $signature = 'farmacia:importar {archivo : Ruta del CSV} {--limpiar : Borra los registros actuales antes de importar}';
    protected $description = 'Importa el Anexo FG (CSV) a la tabla farmacias';

    private const MESES = [
        'ene' => 1, 'feb' => 2, 'mar' => 3, 'abr' => 4, 'may' => 5, 'jun' => 6,
        'jul' => 7, 'ago' => 8, 'sep' => 9, 'sept' => 9, 'oct' => 10, 'nov' => 11, 'dic' => 12,
    ];

    public function handle(): int
    {
        $ruta = $this->argument('archivo');
        if (! is_file($ruta)) {
            $this->error("No se encontró el archivo: $ruta");
            return self::FAILURE;
        }
        if (Farmacia::count() > 0 && ! $this->option('limpiar')) {
            $this->error('La tabla ya tiene registros. Usa --limpiar para reemplazarlos.');
            return self::FAILURE;
        }

        $contenido = preg_replace('/^\xEF\xBB\xBF/', '', file_get_contents($ruta));
        if (! mb_check_encoding($contenido, 'UTF-8')) {
            $contenido = mb_convert_encoding($contenido, 'UTF-8', 'Windows-1252');
        }
        $flujo = fopen('php://temp', 'r+');
        fwrite($flujo, $contenido);
        rewind($flujo);

        fgetcsv($flujo); // encabezados
        $registros = [];
        $omitidas = 0;
        while (($f = fgetcsv($flujo)) !== false) {
            $f = array_pad($f, 51, '');
            if (trim($f[5]) === '') { $omitidas++; continue; }

            $registros[] = [
                'id_anexo' => $this->texto($f[1]),
                'partida' => $this->entero($f[2]),
                'clas' => $this->texto($f[3]),
                'cucop' => $this->texto($f[4]),
                'clave_insumo' => $this->texto($f[5]),
                'descripcion' => $this->texto($f[6]),
                'unidad' => $this->texto($f[7]),
                'carga_aamates' => $this->entero($f[8]) ?? 0,
                'adj_min_2526' => $this->entero($f[9]),
                'adj_max_2526' => $this->entero($f[10]),
                'resto_contrato' => $this->entero($f[11]) ?? 0,
                'min_26' => $this->entero($f[12]) ?? 0,
                'max_26' => $this->entero($f[13]) ?? 0,
                'proveedor' => $this->texto($f[14]),
                'numero_pedido' => $this->texto($f[15]),
                'numero_procedimiento' => $this->texto($f[16]),
                'precio_unitario' => $this->decimal($f[17]),
                'volumen_requerir' => $this->entero($f[18]),
                'subtotal' => $this->decimal($f[19]),
                'iva' => $this->decimal($f[20]),
                'costo_total' => $this->decimal($f[21]),
                'fecha_pedido' => $this->fecha($f[22]),
                'periodo_entrega_inicio' => $this->fecha($f[23]),
                'periodo_entrega_fin' => $this->fecha($f[24]),
                'nombre_os' => $this->texto($f[25]),
                'numero_entrada' => $this->texto($f[26]),
                'fecha_entrega' => $this->fecha($f[27]),
                'numero_remision' => $this->texto($f[28]),
                'volumen_entregado' => $this->entero($f[29]),
                'laboratorio_marca' => $this->texto($f[30]),
                'pais_origen' => $this->texto($f[31]),
                'lote' => $this->texto($f[32]),
                'caducidad' => $this->caducidad($f[33]),
                'costo_total_pedido' => $this->decimal($f[34]),
                'volumen_pendiente' => $this->entero($f[35]),
                'porcentaje' => $this->decimal($f[36]),
                'temperatura_almacenamiento' => $this->decimal($f[37]),
                'estatus_instrumento' => $this->texto($f[38]),
                'codigo_contrato_compranet' => $this->texto($f[39]),
                'fecha_inicio_contrato' => $this->fecha($f[40]),
                'fecha_fin_contrato' => $this->fecha($f[41]),
                'monto_minimo_contrato' => $this->decimal($f[42]),
                'monto_maximo_contrato' => $this->decimal($f[43]),
                'representante_legal' => $this->texto($f[44]),
                'fabricante' => $this->texto($f[45]),
                'contacto' => $this->texto($f[46]),
                'telefono' => $this->texto($f[47]),
                'correos' => $this->texto($f[48]),
                'observaciones' => $this->texto($f[49]),
                'estatus' => $this->texto($f[50]),
            ];
        }
        fclose($flujo);

        DB::transaction(function () use ($registros) {
            if ($this->option('limpiar')) {
                Farmacia::query()->delete();
            }
            foreach ($registros as $registro) {
                Farmacia::create($registro);
            }
        });

        $this->info('Se importaron ' . count($registros) . " registros ($omitidas filas vacías omitidas).");
        return self::SUCCESS;
    }

    private function texto(?string $v): ?string
    {
        $v = trim((string) $v);
        return ($v === '' || str_starts_with($v, '#')) ? null : $v;
    }

    private function decimal(?string $v): ?float
    {
        $v = preg_replace('/[\s$,%]/', '', (string) $v);
        return is_numeric($v) ? (float) $v : null;
    }

    private function entero(?string $v): ?int
    {
        $n = $this->decimal($v);
        return $n === null ? null : (int) round($n);
    }

    private function fecha(?string $v): ?string
    {
        $v = trim((string) $v);
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $v, $m)) {
            return checkdate((int) $m[2], (int) $m[1], (int) $m[3])
                ? sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1]) : null;
        }
        if (preg_match('/^(\d{1,2})-([a-z]{3,4})\.?-(\d{2})$/i', $v, $m)) {
            $mes = self::MESES[strtolower($m[2])] ?? null;
            $anio = 2000 + (int) $m[3];
            if (! $mes || $anio === 2000 || ! checkdate($mes, (int) $m[1], $anio)) {
                return null; // año 00 = renglón sin fecha
            }
            return sprintf('%04d-%02d-%02d', $anio, $mes, $m[1]);
        }
        return null;
    }

    private function caducidad(?string $v): ?string
    {
        if (preg_match('/^([a-z]{3,4})\.?-(\d{2})$/i', trim((string) $v), $m)) {
            $mes = self::MESES[strtolower($m[1])] ?? null;
            if ($mes) {
                return Carbon::create(2000 + (int) $m[2], $mes, 1)->endOfMonth()->toDateString();
            }
        }
        return $this->fecha($v);
    }
}