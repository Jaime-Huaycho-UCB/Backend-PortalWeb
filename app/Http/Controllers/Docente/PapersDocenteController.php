<?php

namespace App\Http\Controllers\Docente;
use App\Http\Controllers\Controller;
use App\Models\Docente\PapersDocente;
use Exception;
use Illuminate\Http\Request;


class PapersDocenteController extends Controller{
    
    public function relacionarPapersDocente($idDocente,$idPaper){
        try{
            $papersDocente = new PapersDocente();
            $papersDocente->docente = $idDocente;
            $papersDocente->papers = $idPaper;
            $papersDocente->save();
            return true;
        } catch (Exception $e){
            return false;
        }
    }
}