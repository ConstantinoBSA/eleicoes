<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\EscolaSegmento;

class AjaxController extends Controller
{
    public function escola_segmentos()
    {
        $id = $_POST['id'];

        $segmentoModel = new EscolaSegmento();
        $registros = $segmentoModel
            ->where('escola_id', $id)
            ->get();

        $data = '';
        if(count($registros) > 0){
            $data = '<option value="">Selecione...</option>';
            foreach ($registros as $field) {
                $data .= '<option value="'.$field->segmento()->id.'">';
                $data .= $field->segmento()->nome;
                $data .= '</option>';
            }            
        }else{
            $data = '<option value="">...</option>';
        }

        echo $data;
    }
}
