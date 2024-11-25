<?php

namespace App\Http\Controllers\Estudiante;
use App\Http\Controllers\Controller;
use App\Models\Estudiante\Semestre;
use Exception;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\TryCatch;

class SemestreController extends Controller{

    public function obtenerSemestres(){
        try{
            $semestres = Semestre::where('Eliminado','=',0)
                                    ->orderBy('anio','desc')
                                    ->get();
            if ($semestres->isNotEmpty()){
                return response()->json([
                    "salida" => true,
                    "semestres" => $semestres
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                ],200);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function obtenerSemestre($num,$anio){
        try {
            $buscar = Semestre::where('semestre','=',$num)
                                ->where('anio','=',$anio)  
                                ->where('Eliminado','=',0)
                                ->first();
            if ($buscar){
                return $buscar['id'];
            }

            $semestre = new Semestre();
            $semestre->cadena = "$num-$anio";
            $semestre->semestre = $num;
            $semestre->anio = $anio;
            $semestre->save();
            return $semestre->id;
        } catch (Exception $e) {
            return null;
        }
    }

    
}