<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Prestamo;
use App\DetallePrestamo;
use App\User;

class PrestamoController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $prestamos = Prestamo::join('personas','prestamos.idcliente','=','personas.id')
            ->join('users','prestamos.idusuario','=','users.id')
            ->select('prestamos.id','prestamos.tipo_comprobante','prestamos.serie_comprobante',
            'prestamos.num_comprobante','prestamos.fecha_prestamo',
            'prestamos.impuesto','prestamos.total','prestamos.estado','personas.nombre','users.usuario')
            ->orderBy('prestamos.id', 'desc')->paginate(3);
        }
        else{
            $prestamos = Prestamo::join('personas','prestamos.idcliente','=','personas.id')
            ->join('users','prestamos.idusuario','=','users.id')
            ->select('prestamos.id','prestamos.tipo_comprobante','prestamos.serie_comprobante',
            'prestamos.num_comprobante','prestamos.fecha_prestamo',
            'prestamos.impuesto','prestamos.total','prestamos.estado','personas.nombre','users.usuario')
            ->where('prestamos.'.$criterio, 'like', '%'. $buscar . '%')
            ->orderBy('prestamos.id', 'desc')->paginate(3);
        }
        
        return [
            'pagination' => [
                'total'        => $prestamos->total(),
                'current_page' => $prestamos->currentPage(),
                'per_page'     => $prestamos->perPage(),
                'last_page'    => $prestamos->lastPage(),
                'from'         => $prestamos->firstItem(),
                'to'           => $prestamos->lastItem(),
            ],
            'prestamos' => $prestamos
        ];
    }

    public function alquilados(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        
        if ($buscar==''){
            $prestamos = Prestamo::join('personas','prestamos.idcliente','=','personas.id')
            ->join('users','prestamos.idusuario','=','users.id')
            ->select('prestamos.id','prestamos.tipo_comprobante','prestamos.serie_comprobante',
            'prestamos.num_comprobante','prestamos.fecha_prestamo',
            'prestamos.impuesto','prestamos.total','prestamos.estado','personas.nombre','users.usuario')
            ->where('prestamos.estado','=','Alquilado')
            ->orderBy('prestamos.id', 'desc')->paginate(3);
        }
        else{
            $prestamos = Prestamo::join('personas','prestamos.idcliente','=','personas.id')
            ->join('users','prestamos.idusuario','=','users.id')
            ->select('prestamos.id','prestamos.tipo_comprobante','prestamos.serie_comprobante',
            'prestamos.num_comprobante','prestamos.fecha_prestamo',
            'prestamos.impuesto','prestamos.total','prestamos.estado','personas.nombre','users.usuario')
            ->where('prestamos.'.$criterio, 'like', '%'. $buscar . '%','and','prestamos.estado','=','Alquilado')
            ->orderBy('prestamos.id', 'desc')->paginate(3);
        }
        
        return [
            'pagination' => [
                'total'        => $prestamos->total(),
                'current_page' => $prestamos->currentPage(),
                'per_page'     => $prestamos->perPage(),
                'last_page'    => $prestamos->lastPage(),
                'from'         => $prestamos->firstItem(),
                'to'           => $prestamos->lastItem(),
            ],
            'prestamos' => $prestamos
        ];
    }
    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $prestamo = Prestamo::join('personas','prestamos.idcliente','=','personas.id')
        ->join('users','prestamos.idusuario','=','users.id')
        ->select('prestamos.id','prestamos.tipo_comprobante','prestamos.serie_comprobante',
        'prestamos.num_comprobante','prestamos.fecha_prestamo','prestamos.impuesto','prestamos.total','prestamos.estado','personas.nombre','users.usuario')
        ->where('prestamos.id','=',$id)
        ->orderBy('prestamos.id', 'desc')->take(1)->get();
        
        return ['prestamo' => $prestamo];
    }
    public function obtenerDetalles(Request $request){
        if (!$request->ajax()) return redirect('/');

        $id = $request->id;
        $detalles = DetallePrestamo::join('productos','detalle_prestamos.idproducto','=','productos.id')
        ->select('detalle_prestamos.cantidad','detalle_prestamos.precio','detalle_prestamos.descuento','detalle_prestamos.fecha_devuelto',
        'productos.nombre as producto')
        ->where('detalle_prestamos.idprestamo','=',$id)
        ->orderBy('detalle_prestamos.id', 'desc')->get();
        
        return ['detalles' => $detalles];
    }
    public function pdf(Request $request,$id){
        $prestamo = Prestamo::join('personas','prestamos.idcliente','=','personas.id')
        ->join('users','prestamos.idusuario','=','users.id')
        ->select('prestamos.id','prestamos.tipo_comprobante','prestamos.serie_comprobante',
        'prestamos.num_comprobante','prestamos.created_at','prestamos.impuesto','prestamos.total',
        'prestamos.estado','personas.nombre','personas.tipo_documento','personas.num_documento',
        'personas.direccion','personas.email',
        'personas.telefono','users.usuario')
        ->where('prestamos.id','=',$id)
        ->orderBy('prestamos.id', 'desc')->take(1)->get();

        $detalles = DetallePrestamo::join('productos','detalle_prestamos.idproducto','=','productos.id')
        ->select('detalle_prestamos.cantidad','detalle_prestamos.precio','detalle_prestamos.descuento',
        'productos.nombre as producto')
        ->where('detalle_prestamos.idprestamo','=',$id)
        ->orderBy('detalle_prestamos.id', 'desc')->get();

        $numprestamo=Prestamo::select('num_comprobante')->where('id',$id)->get();

        $pdf = \PDF::loadView('pdf.prestamo',['prestamo'=>$prestamo,'detalles'=>$detalles]);
        return $pdf->download('prestamo-'.$numprestamo[0]->num_comprobante.'.pdf');
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $mytime= Carbon::now('America/Lima');

            $prestamo = new Prestamo();
            $prestamo->idcliente = $request->idcliente;
            $prestamo->idusuario = \Auth::user()->id;
            $prestamo->tipo_comprobante = $request->tipo_comprobante;
            $prestamo->serie_comprobante = $request->serie_comprobante;
            $prestamo->num_comprobante = $request->num_comprobante;
            $prestamo->fecha_prestamo = $mytime->toDateString();
            $prestamo->impuesto = $request->impuesto;
            $prestamo->total = $request->total;
            $prestamo->estado = 'Alquilado';
            $prestamo->save();

            $detalles = $request->data;//Array de detalles
            //Recorro todos los elementos

            foreach($detalles as $ep=>$det)
            {
                $detalle = new DetallePrestamo();
                $detalle->idprestamo = $prestamo->id;
                $detalle->idproducto = $det['idproducto'];
                $detalle->cantidad = $det['cantidad'];
                $detalle->precio = $det['precio'];
                $detalle->descuento = $det['descuento'];         
                $detalle->save();
            }          

            $fechaActual= date('Y-m-d');
            $numPrestamos = DB::table('prestamos')->whereDate('created_at', $fechaActual)->count(); 
            $numIngresos = DB::table('ingresos')->whereDate('created_at',$fechaActual)->count(); 

            $arregloDatos = [ 
            'prestamos' => [ 
                        'numero' => $numPrestamos, 
                        'msj' => 'prestamos' 
                    ], 
            'ingresos' => [ 
                        'numero' => $numIngresos, 
                        'msj' => 'Ingresos' 
                    ] 
            ];                
        /*    $allUsers = User::all();

            foreach ($allUsers as $notificar) { 
                User::findOrFail($notificar->id)->notify(new NotifyAdmin($arregloDatos)); 
            }*/
            
            DB::commit();
            return [
                'id' => $prestamo->id
            ];
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $prestamo = Prestamo::findOrFail($request->id);
        $prestamo->estado = 'Anulado';
        $prestamo->save();
    }

    public function devolver(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $prestamo = Prestamo::findOrFail($request->id);
        $prestamo->estado = 'Devuelto';
        $prestamo->save();
    }
}
