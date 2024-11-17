<?php

namespace App\Http\Controllers\Docente;
use App\Http\Controllers\Controller;
use App\Models\Docente\Papers;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class PapersController extends Controller{

    public function obtenerPapers($idDocente){
        try {
            $query = "SELECT PAPERS.id as id ,PAPERS.titulo as titulo ,PAPERS.link as link FROM PAPERS,PAPERS_DOCENTE,DOCENTE WHERE PAPERS.id = PAPERS_DOCENTE.papers and DOCENTE.id = PAPERS_DOCENTE.docente and DOCENTE.id = ? and PAPERS.Eliminado = 0";
            $papers = DB::select($query,[$idDocente]);
            if ($papers){
                return response()->json([
                    "salida" => true,
                    "papers" => $papers
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "EL docente no tiene papers"
                ],200);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function ingresarPaper(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" =>$this->TOKEN_INVALIDO
                ],200);
            }

            $papersDocente = new PapersDocenteController();
            $idDocente = $request->input('idDocente');
            $papers = new Papers();
            $papers->titulo = $request->input('titulo');
            $papers->link = $request->input('link');
            $papers->save();
            if ($papersDocente->relacionarPapersDocente($idDocente,$papers->id)){
                return response()->json([
                    "salida" => true,
                    "mensaje" => "Se ingreso exitosamente el papers"
                ],200);
            }else{
                return response()->json([
                    "salida" => false,
                    "mensaje" => "Error al ingresar el papers"
                ],200);
            }
        } catch (Exception $e){
            return $this->Error($e);
        }
    }

    public function eliminarPaper(Request $request){
        try{
            $token = $request->input('token');
            $idUsuario = $request->input('idUsuario');
            if (!($this->tokenValido($idUsuario,$token))){
                return response()->json([
                    "salida" => false,
                    "mensaje" =>$this->TOKEN_INVALIDO
                ],200);
            }

            $idPaper = $request->input('idPaper');
            $paper = Papers::find($idPaper);
            $paper->Eliminado = 1;
            $paper->save();
            return response()->json([
                "salida" => true,
                "mensaje" => "El paper fue eliminado exitosamente"
            ],200);
        } catch (Exception $e){
            return $this->Error($e);
        }
    }
}