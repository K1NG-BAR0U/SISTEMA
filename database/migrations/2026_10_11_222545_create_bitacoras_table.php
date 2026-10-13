<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('bitacoras', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id')->nullable();
        $table->string('usuario')->default('Sistema');
        $table->string('accion', 30);
        $table->string('tabla', 60)->nullable();
        $table->unsignedBigInteger('registro_id')->nullable();
        $table->text('detalle')->nullable();
        $table->string('ip', 45)->nullable();
        $table->timestamp('created_at')->nullable();

        $table->index(['tabla', 'registro_id']);
        $table->index('created_at');
    });

    DB::unprepared("CREATE TRIGGER bitacoras_no_update BEFORE UPDATE ON bitacoras
        BEGIN SELECT RAISE(ABORT, 'La bitácora no se puede modificar'); END;");
    DB::unprepared("CREATE TRIGGER bitacoras_no_delete BEFORE DELETE ON bitacoras
        BEGIN SELECT RAISE(ABORT, 'La bitácora no se puede eliminar'); END;");
}

public function down(): void
{
    DB::unprepared('DROP TRIGGER IF EXISTS bitacoras_no_update');
    DB::unprepared('DROP TRIGGER IF EXISTS bitacoras_no_delete');
    Schema::dropIfExists('bitacoras');
}
};
