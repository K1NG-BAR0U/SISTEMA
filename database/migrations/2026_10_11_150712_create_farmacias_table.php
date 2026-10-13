<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('farmacias', function (Blueprint $table) {
        $table->id();

        // Identificación del insumo
        $table->string('id_anexo', 20)->nullable();
        $table->unsignedInteger('partida')->nullable();
        $table->string('clas', 30)->nullable();
        $table->string('cucop', 8)->nullable();
        $table->string('clave_insumo', 20);
        $table->text('descripcion');
        $table->string('unidad', 20)->nullable();
        $table->decimal('temperatura_almacenamiento', 5, 1)->nullable();

        // Volúmenes del contrato
        $table->unsignedInteger('carga_aamates')->default(0);
        $table->unsignedInteger('adj_min_2526')->nullable();
        $table->unsignedInteger('adj_max_2526')->nullable();
        $table->unsignedInteger('resto_contrato')->default(0);
        $table->unsignedInteger('min_26')->default(0);
        $table->unsignedInteger('max_26')->default(0);

        // Pedido
        $table->string('proveedor')->nullable();
        $table->string('numero_pedido')->nullable();
        $table->string('numero_procedimiento')->nullable();
        $table->decimal('precio_unitario', 12, 2)->nullable();
        $table->unsignedInteger('volumen_requerir')->nullable();
        $table->decimal('subtotal', 12, 2)->nullable();
        $table->decimal('iva', 12, 2)->nullable();
        $table->decimal('costo_total', 12, 2)->nullable();
        $table->date('fecha_pedido')->nullable();
        $table->date('periodo_entrega_inicio')->nullable();
        $table->date('periodo_entrega_fin')->nullable();
        $table->string('nombre_os')->nullable();

        // Entrega
        $table->string('numero_entrada', 20)->nullable();
        $table->date('fecha_entrega')->nullable();
        $table->string('numero_remision', 50)->nullable();
        $table->unsignedInteger('volumen_entregado')->nullable();
        $table->string('laboratorio_marca')->nullable();
        $table->string('pais_origen', 60)->nullable();
        $table->string('lote', 50)->nullable();
        $table->date('caducidad')->nullable();
        $table->decimal('costo_total_pedido', 12, 2)->nullable();
        $table->integer('volumen_pendiente')->nullable();
        $table->decimal('porcentaje', 5, 2)->nullable();

        // Contrato
        $table->string('estatus_instrumento', 30)->nullable();
        $table->string('codigo_contrato_compranet', 60)->nullable();
        $table->date('fecha_inicio_contrato')->nullable();
        $table->date('fecha_fin_contrato')->nullable();
        $table->decimal('monto_minimo_contrato', 14, 2)->nullable();
        $table->decimal('monto_maximo_contrato', 14, 2)->nullable();
        $table->string('representante_legal')->nullable();
        $table->string('fabricante')->nullable();

        // Contacto y seguimiento
        $table->string('contacto')->nullable();
        $table->text('telefono')->nullable();
        $table->text('correos')->nullable();
        $table->text('observaciones')->nullable();
        $table->string('estatus')->nullable();

        $table->timestamps();

        $table->index('clave_insumo');
        $table->index('proveedor');
        $table->index('numero_pedido');
    });
}
    public function down(): void
    {
        Schema::dropIfExists('farmacias');
    }
};
