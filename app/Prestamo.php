<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    protected $fillable =[
        'idcliente', 
        'idusuario',
        'tipo_comprobante',
        'serie_comprobante',
        'num_comprobante',
        'fecha_prestamo',
        'impuesto',
        'total',
        'estado'
    ];
}
