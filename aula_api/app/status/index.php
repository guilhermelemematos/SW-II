<?php
    //Aqui vai ser o APP
    require_once __DIR__ .'../../../api/config.php';
    require_once __DIR__ .'../../../api/response.php';

    if(API_IS_ACTIVE){
        echo Response::resposta(200,'sucess',[
            'version' => API_VERSION,
            'status' => 'ATIVA'
        ]);
    }
    else{
        echo Response::resposta(200,'sucess',[
            'version' => API_VERSION,
            'status' => 'MANUTENÇÃO'
        ]);    
    }

?>