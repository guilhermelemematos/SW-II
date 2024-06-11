<?php
    //Aqui vai ser o APP
    require_once __DIR__ .'../../../api/config.php';
    require_once __DIR__ .'../../../api/response.php';
    
    echo Response::resposta(200,'sucess',[
        'version' => API_VERSION,
        'status' => 'MANUTENÇÃO'
    ]);    
?>