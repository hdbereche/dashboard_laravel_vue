<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetallePrestamo extends Model
{
    protected $table = 'detalle_prestamos';
    protected $fillable = [
        'idprestamo', 
        'idarticulo',
        'cantidad',
        'precio',
        'descuento',
        'fecha_devuelto'
    ];
    public $timestamps = false;
}
