<?php
require_once 'includes/header.php';
require_once 'includes/helpers.php';
require_once 'includes/objects.php';

if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Enviar') {
    session_start();

    $fileTmpPathC = $_FILES['clienteInfo']['tmp_name'];
    $fileNameC = $_FILES['clienteInfo']['name'];
    $file_extensionC = pathinfo($fileNameC, PATHINFO_EXTENSION); //Asi obtenemos la extension
    $fileNameCmpsC = explode(".", $fileNameC);
    $fileExtensionC = strtolower(end($fileNameCmpsC));
    
    $fileTmpPathPr = $_FILES['productosInfo']['tmp_name'];
    $fileNamePr = $_FILES['productosInfo']['name'];
    $file_extensionPr = pathinfo($fileNamePr, PATHINFO_EXTENSION);
    $fileNameCmpsPr = explode(".", $fileNamePr);
    $fileExtensionPr = strtolower(end($fileNameCmpsPr));
    
    $fileTmpPathPe = $_FILES['pedidosInfo']['tmp_name'];
    $fileNamePe = $_FILES['pedidosInfo']['name'];
    $file_extensionPe = pathinfo($fileNamePe, PATHINFO_EXTENSION);
    $fileNameCmpsPe = explode(".", $fileNamePe);
    $fileExtensionPe = strtolower(end($fileNameCmpsPe));
    
    $errores = array(); // Creamos un array con los errores
    
    if ($file_extensionC != "csv" || $file_extensionPr != "csv" || $file_extensionPe != "csv") {
        $errores['extension']= "Tu extensión no es valida";
        $extension_success=false;
    }else{
        $extension_success=true;
    }
    
    $uploadedFileDir = './uploadedFiles/';
    $dest_pathC = $uploadedFileDir . $fileNameC;
    $dest_pathPr = $uploadedFileDir . $fileNamePr;
    $dest_pathPe = $uploadedFileDir . $fileNamePe;
    
    if($extension_success){
        if(move_uploaded_file($fileTmpPathC, $dest_pathC) && move_uploaded_file($fileTmpPathPr, $dest_pathPr) && move_uploaded_file($fileTmpPathPe, $dest_pathPe)){
            $uploaded_success =true;
        }else{
           $errores['upload']= 'Error al cargar algun archivo';
           $uploaded_success =false;
        }
    }

    if(count($errores) == 0){
        $fp = fopen ("uploadedFiles/products.csv","r");
        $products=[]; //Guardaremos todos los objetos tipo Product
        $i=0;  //La primera fila del csv que no nos interesa por ahora
        
        while ($data = fgetcsv ($fp, 1000, ",")) {   
            if($i>0){ //La primera fila del csv no la queremos
                $productCSV=new Product($data[0],"$data[1]",$data[2]);//CREAMOS UN OBJETO PRODUCTO POR CADA PRODUCTO
                $products[]=$productCSV; //Vamos introduciendo los objetos en el array
            }
            $i++;  
        }
        
        fclose ($fp);
        
        
        
        $orders=[]; //Guardaremos todos los objetos tipo Order
        $i=0;
        $fp = fopen ("uploadedFiles/orders.csv","r");
        
        while ($data = fgetcsv ($fp, 1000, ",")) {
            if($i>0){
                $orderCSV=new Order($data[0],$data[1],"$data[2]");
                $orders[]=$orderCSV; //Vamos introduciendo los objetos en el array
            }
            $i++;
        }
        
        fclose ($fp);
        
        
        
        $i=0;
        $fp = fopen ("uploadedFiles/customers.csv","r");
        $customers=[];//Guardaremos todos los objetos tipo Customer

        while ($data = fgetcsv ($fp, 1000, ",")) {
            if($i>0){
                $customerCSV=new customer($data[0],"$data[1]","$data[2]"); 
                $customers[]=$customerCSV;
            }
            $i++;
        }
        
        fclose ($fp);
        
        
        
        $sum=0;
        $arrayReporte1= array();
        $arrayReporte3= array();
        
        array_push($arrayReporte1, array("id","total"));
        // LOGICA REPORTE 1 Y 3
        
                foreach($orders as $key => $order){ //Recorremos el array con todos los pedidos
                    $idProductos=$order->getIdProducts();//Almacenamos los productos de cada pedido
                    
                    for ($j = 0; $j <= strlen($idProductos)/2 ; $j++) { //Recorremos el string $idProductos
                        $idProducto=$idProductos[2*$j]; 
                        
                        foreach($products as $product){
                            if($product->getId()==$idProducto){
                                $sum+=$product->getCost();
                            }
                        }    
                    }
                    
                    $arrayReporte3[$key+1]=array($order->getIdCustomer(),$sum);
                    $arrayReporte1[$key+1]=array($order->getId(),$sum);
                    $sum=0;
                }

                //Sumar los costes cuando coincida el id de cliente REPORTE 3
                $array3UniqCust = [];
                foreach ($arrayReporte3 as $item) { //Sumamos los $sum que tengan el mismo id de cliente
                  if (!isset($array3UniqCust[$item[0]])) {//Si no existe aun ese id
                    $array3UniqCust[$item[0]] = $item[1]; //Introducimos el float por primera vez en la posicion $item[0] que es el id del cliente
                  } else {
                    $array3UniqCust[$item[0]] += $item[1];//Le sumamos el float en esa posicion
                  }
                }
                
                arsort($array3UniqCust); //Ordenamos descendentemente

                $final3 = [];
                foreach ($array3UniqCust as $key => $value) {//Es mas comodo al generar el csv convertirlo en un array mulitdimensional
                  $final3[] = [$key, $value]; //Preparado para el csv
                }

                
        //Crear CSV para el reporte 1

        $ruta ="uploadedFiles/order_prices.csv";
        
        
        generarCSV($arrayReporte1, $ruta, $delimitador = ',', $encapsulador = '"');

        $idClientesConProducto =array_fill(0, count($products), array());
        
        //LOGICA REPORTE 2
        
        foreach($products as $key => $product){
            foreach ($orders as $order){
                $idProductos=$order->getIdProducts();//Id del producto del order en el que nos encontramos
                
                    for ($j = 0; $j <= strlen($idProductos)/2 ; $j++) { //Recorremos el string $idProductos
                        $idProducto=$idProductos[2*$j]; 
                        
                        if($idProducto==$product->getId()){
                            array_push($idClientesConProducto[$key],$order->getIdCustomer());
                            
                        }       
                    }
                }
         }
         
        $idClientesConProducto = array_map('array_unique', $idClientesConProducto);

        
        //Crear CSV para el reporte 2
        $array_final=array("id","customer_ids");
        
        foreach ($idClientesConProducto as $key => $sub_array){
            array_push($array_final,"$key");
            array_push($array_final,implode(" ", $sub_array)); 
        }
        
        
        $separatedInPairs = array_chunk($array_final, 2);
        
        $ruta ="uploadedFiles/product_customers.csv";
        generarCSV($separatedInPairs, $ruta, $delimitador = ',', $encapsulador = ' ');
        
        
        //LOGICA REPORTE 3
     
        array_unshift($final3,array("id","total","name","lastname"));
        foreach($final3 as $key => $sub_array){//Recorremos array con id de cliente y su total gastado
            foreach ($customers as $customer){//Recorremos todos los clientes
                
                
                if($customer->getId()== $sub_array[0]){ //Si coincide el id de cliente del array y del foreach..
                    
                    $final3[$key][2]= $customer->getFirstName(); //añadimos nombre y apellidos
                    $final3[$key][3]= $customer->getLastName();
                }
            }
        }

        
        
        //Crear CSV para el reporte 3
        
        $ruta ="uploadedFiles/customer_ranking.csv";
        generarCSV($final3, $ruta, $delimitador = ',', $encapsulador = ' ');
        
        
        
        
        echo "<a class='boton' href='download.php?file=order_prices.csv'>Descargar order_prices.csv</a><br> <br> ";
        echo "<a class='boton' href='download.php?file=product_customers.csv'>Descargar product_customers.csv</a><br> ";
        echo "<a class='boton' href='download.php?file=customer_ranking.csv'>Descargar customer_ranking.csv</a> <br> ";
        
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: index.php");
    }
    
    
}
