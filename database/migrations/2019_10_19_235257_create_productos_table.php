<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('idcategoria')->unsigned();
            $table->string('codbarras', 50)->nullable();
            $table->string('nombre', 100)->unique();
            $table->string('descripcion', 256)->nullable();
            $table->string('talla', 5);
            $table->decimal('precio_venta', 11, 2);
            $table->decimal('precio_alquiler', 11, 2);
            $table->integer('stock');
            $table->string('imagen',200)->nullable();
            $table->boolean('condicion')->default(1);
            $table->timestamps();

            $table->foreign('idcategoria')->references('id')->on('categorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
}
