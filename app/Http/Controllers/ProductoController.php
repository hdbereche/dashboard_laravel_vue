<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Producto;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $productos = Producto::join('categorias','productos.idcategoria','=','categorias.id')
            ->select('productos.id','productos.idcategoria','productos.codbarras','productos.nombre',
            'productos.descripcion','productos.talla','categorias.nombre as nombre_categoria','productos.precio_venta',
            'productos.precio_alquiler','productos.stock','productos.condicion')
            ->orderBy('productos.id', 'desc')->paginate(5);
        }
        else{
            $productos = Producto::join('categorias','productos.idcategoria','=','categorias.id')
            ->select('productos.id','productos.idcategoria','productos.codbarras','productos.nombre',
            'productos.descripcion','productos.talla','categorias.nombre as nombre_categoria','productos.precio_venta',
            'productos.precio_alquiler','productos.stock','productos.condicion')
            ->where('productos.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('productos.id', 'desc')->paginate(5);
        }
        

        return [
            'pagination' => [
                'total'        => $productos->total(),
                'current_page' => $productos->currentPage(),
                'per_page'     => $productos->perPage(),
                'last_page'    => $productos->lastPage(),
                'from'         => $productos->firstItem(),
                'to'           => $productos->lastItem(),
            ],
            'productos' => $productos
        ];
    }

    public function listarProducto(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $productos = Producto::join('categorias','productos.idcategoria','=','categorias.id')
            ->select('productos.id','productos.idcategoria','productos.codbarras','productos.nombre','categorias.nombre as nombre_categoria','productos.precio_venta','productos.stock','productos.descripcion','productos.condicion')
            ->orderBy('productos.id', 'desc')->paginate(10);
        }
        else{
            $productos = Producto::join('categorias','productos.idcategoria','=','categorias.id')
            ->select('productos.id','productos.idcategoria','productos.codbarras','productos.nombre','categorias.nombre as nombre_categoria','productos.precio_venta','productos.stock','productos.descripcion','productos.condicion')
            ->where('productos.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('productos.id', 'desc')->paginate(10);
        }
        

        return ['productos' => $productos];
    }
 
    public function listarProductoVenta(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $productos = Producto::join('categorias','productos.idcategoria','=','categorias.id')
            ->select('productos.id','productos.idcategoria','productos.codbarras','productos.nombre','categorias.nombre as nombre_categoria','productos.precio_venta','productos.stock','productos.descripcion','productos.condicion')
            ->where('productos.stock','>','0')
            ->orderBy('productos.id', 'desc')->paginate(10);
        }
        else{
            $productos = Producto::join('categorias','productos.idcategoria','=','categorias.id')
            ->select('productos.id','productos.idcategoria','productos.codbarras','productos.nombre','categorias.nombre as nombre_categoria','productos.precio_venta','productos.stock','productos.descripcion','productos.condicion')
            ->where('productos.'.$criterio, 'like', '%'. $buscar . '%')
            ->where('productos.stock','>','0')
            ->orderBy('productos.id', 'desc')->paginate(10);
        }
        

        return ['productos' => $productos];
    }

    public function listarProductoAlquiler(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $productos = Producto::join('categorias','productos.idcategoria','=','categorias.id')
            ->select('productos.id','productos.idcategoria','productos.codbarras','productos.nombre','categorias.nombre as nombre_categoria','productos.precio_alquiler','productos.stock','productos.descripcion','productos.condicion')
            ->where('productos.stock','>','0')
            ->orderBy('productos.id', 'desc')->paginate(10);
        }
        else{
            $productos = Producto::join('categorias','productos.idcategoria','=','categorias.id')
            ->select('productos.id','productos.idcategoria','productos.codbarras','productos.nombre','categorias.nombre as nombre_categoria','productos.precio_alquiler','productos.stock','productos.descripcion','productos.condicion')
            ->where('productos.'.$criterio, 'like', '%'. $buscar . '%')
            ->where('productos.stock','>','0')
            ->orderBy('productos.id', 'desc')->paginate(10);
        }
        

        return ['productos' => $productos];
    }

    public function listarPdf(){
        $productos = Producto::join('categorias','productos.idcategoria','=','categorias.id')
            ->select('productos.id','productos.idcategoria','productos.codbarras','productos.nombre','categorias.nombre as categoria','productos.descripcion',
            'productos.talla','productos.precio_venta','productos.precio_alquiler','productos.stock','productos.condicion')
            ->orderBy('productos.nombre', 'desc')->get();

        $cont=Producto::count();

        $pdf = \PDF::loadView('pdf.productospdf',['productos'=>$productos,'cont'=>$cont]);
        return $pdf->download('productos.pdf');
    }

    public function buscarProducto(Request $request){
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $productos = Producto::where('codbarras','=', $filtro)
        ->select('id','nombre')->orderBy('nombre', 'asc')->take(1)->get();

        return ['productos' => $productos];
    }

    public function buscarProductoVenta(Request $request){
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $productos = Producto::where('codbarras','=', $filtro)
        ->select('id','nombre','stock','precio_venta')
        ->where('stock','>','0')
        ->orderBy('nombre', 'asc')
        ->take(1)->get();

        return ['productos' => $productos];
    }

    public function buscarProductoAlquiler(Request $request){
        if (!$request->ajax()) return redirect('/');

        $filtro = $request->filtro;
        $productos = Producto::where('codbarras','=', $filtro)
        ->select('id','nombre','stock','precio_alquiler')
        ->where('stock','>','0')
        ->orderBy('nombre', 'asc')
        ->take(1)->get();

        return ['productos' => $productos];
    }
    
    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $producto = new Producto();
        $producto->idcategoria = $request->idcategoria;
        $producto->codbarras = $request->codbarras;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->talla = $request->talla;
        $producto->precio_venta = $request->precio_venta;
        $producto->precio_alquiler = $request->precio_alquiler;
        $producto->stock = $request->stock;

        if($archivo=$request->file('imagen')){
            $nombre_imagen = $archivo->getClientOriginalName();
            $archivo->move("imagenes",$nombre_imagen);
            $producto->imagen = $nombre_imagen;
        }
        $producto->condicion = '1';
        $producto->save();
    }
    public function update(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $producto = Producto::findOrFail($request->id);
        $producto->idcategoria = $request->idcategoria;
        $producto->codbarras = $request->codbarras;
        $producto->nombre = $request->nombre;
        $producto->descripcion = $request->descripcion;
        $producto->talla = $request->talla;
        $producto->precio_venta = $request->precio_venta;
        $producto->precio_alquiler = $request->precio_alquiler;
        $producto->stock = $request->stock;
        $producto->imagen = $request->imagen;
        $producto->condicion = '1';
        $producto->save();
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $producto = Producto::findOrFail($request->id);
        $producto->condicion = '0';
        $producto->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $producto = Producto::findOrFail($request->id);
        $producto->condicion = '1';
        $producto->save();
    }

}
