<?php
 function mostrarErrores($errores,$campo){
     $alerta='';
     
     if(isset($errores[$campo]) && !empty($campo)){
         $alerta="<div class='alerta alerta-error'>".$errores[$campo].'</div>';
     }
     return $alerta;
 }
 function borrarErrores(){

    if(isset($_SESSION['errores'])){
        unset($_SESSION['errores']);
    }
    
 }
 
function generarCSV($arreglo, $ruta, $delimitador, $encapsulador){
    $file_handle = fopen($ruta, 'w');
    foreach ($arreglo as $linea) {
        
        fputcsv($file_handle, $linea, $delimitador, $encapsulador);
    }
    rewind($file_handle);
    fclose($file_handle);
}
