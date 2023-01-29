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

function quitarRepetidos(array $array_original){ //Crea a partir de un arreglo, otro sin los valores repetidos de cada sub array
    
        $result_array = array();
        $unique_array = array();
        foreach ($array_original as $key => $sub_array) {
            $unique_array = array_unique($sub_array);
            $result_array[$key] = $unique_array;
            
        }
        
        return $result_array;
}

function sumByClient($original_array){
    $result = [];
    foreach ($original_array as $sub_array) {
        if (!isset($result[$sub_array[0]])) {
            $result[$sub_array[0]] = 0;
        }   
        $result[$sub_array[0]] += $sub_array[1];
    }
    
    return $result;
}

//function orderDesc($original_array){
//    
//    var_dump($original_array);
//    $result = [];
//
//    foreach ($original_array as $key => $value) {
//      if (!isset($result[(string)$key])) {
//        $result[(string)$key] = 0;
//      }
//
//      $result[(string)$key] += $value;
//    }
//    var_dump($result);
//    return $result;
//}