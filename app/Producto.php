<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $fillable =[
        'idcategoria','codbarras','nombre','descripcion','talla','precio_venta','precio_alquiler','stock','imagen','condicion'
    ];

    public function categoria(){
        return $this->belongsTo('App\Categoria');
    }

    public static $image_ext = ['jpg', 'jpeg', 'png', 'gif'];

    public static function getMaxSize()
    {
        return (int)ini_get('upload_max_filesize') * 1000;
    }

    public static function getAllExtensions()
    {
        $merged_arr = array_merge(self::$image_ext);
        return implode(',', $merged_arr);
    }
}
