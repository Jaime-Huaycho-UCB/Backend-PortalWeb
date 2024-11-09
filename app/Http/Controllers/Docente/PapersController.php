<?php

namespace App\Http\Controllers\Docente;
use App\Http\Controllers\Controller;
use App\Models\Docente\Papers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PapersController extends Controller{

    public function obtenerPapers($idDocente){
        $query = "SELECT PAPERS.link as link FROM PAPERS,PAPERS_DOCENTE,DOCENTE WHERE PAPERS.id = PAPERS_DOCENTE.papers and DOCENTE.id = PAPERS_DOCENTE.docente and DOCENTE.id = ?";
        $papers = DB::select($query,[$idDocente]);
        if ($papers){
            return response()->json([
                "salida" => true,
                "links" => $papers
            ],200);
        }else{
            return response()->json([
                "salida" => false,
                "mensaje" => "EL docente no tiene papers"
            ],200);
        }
    }

    public function ingresarPaper(Request $request){

        $token = $request->input('token');
        $idUsuario = $request->input('idUsuario');
        if (!($this->tokenValido($idUsuario,$token))){
            return response()->json([
                "salida" => false,
                "mensaje" =>$this->TOKEN_INVALIDO
            ],200);
        }

        $papersDocente = new PapersDocenteController();
        try{
            $idDocente = $request->input('idDocente');
            $papers = new Papers();
            $papers->link = $request->input('papers');
            $papers->save();
            if ($papersDocente->relacionarPapersDocente($idDocente,$papers->id)){
                return response()->json([
                    "salida" => true,
                    "mensaje" => "Se ingreso exitosamente el papers"
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "Error al ingresar el papers"
                ],200);
            }
        } catch (Exception $e){
            return response()->json([
                "salida" => false,
                "mensaje" => "Error: {$e->getMessage()}"
            ],200);
        }
    }
}