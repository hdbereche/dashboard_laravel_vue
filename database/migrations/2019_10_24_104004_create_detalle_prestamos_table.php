<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetallePrestamosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_prestamos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idprestamo')->unsigned();
            $table->foreign('idprestamo')->references('id')->on('prestamos')->onDelete('cascade');
            $table->integer('idproducto')->unsigned();
            $table->foreign('idproducto')->references('id')->on('productos');
            $table->integer('cantidad');
            $table->decimal('precio', 11, 2);
            $table->decimal('descuento', 11, 2);
            $table->dateTime('fecha_devuelto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detalle_prestamos');
    }
}
