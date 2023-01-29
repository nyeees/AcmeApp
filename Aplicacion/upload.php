<?php
require_once 'includes/header.php';
require_once 'includes/helpers.php';
require_once 'includes/objects.php';

if (isset($_POST['uploadBtn']) && $_POST['uploadBtn'] == 'Enviar') {
    session_start();

    $fileTmpPathC = $_FILES['clienteInfo']['tmp_name'];
    $fileNameC = $_FILES['clienteInfo']['name'];
    //$fileSizeC = $_FILES['clienteInfo']['size'];
    //$fileTypeC = $_FILES['clienteInfo']['type'];
    $file_extensionC = pathinfo($fileNameC, PATHINFO_EXTENSION); //Asi obtenemos la extension
    
    $fileNameCmpsC = explode(".", $fileNameC);
    $fileExtensionC = strtolower(end($fileNameCmpsC));
    
    $fileTmpPathPr = $_FILES['productosInfo']['tmp_name'];
    $fileNamePr = $_FILES['productosInfo']['name'];
    //$fileSizePr = $_FILES['productosInfo']['size'];
    //$fileTypePr = $_FILES['productosInfo']['type'];
    $file_extensionPr = pathinfo($fileNamePr, PATHINFO_EXTENSION);
    
    $fileNameCmpsPr = explode(".", $fileNamePr);
    $fileExtensionPr = strtolower(end($fileNameCmpsPr));
    
    $fileTmpPathPe = $_FILES['pedidosInfo']['tmp_name'];
    $fileNamePe = $_FILES['pedidosInfo']['name'];
    //$fileSizePe = $_FILES['pedidosInfo']['size'];
   // $fileTypePe = $_FILES['pedidosInfo']['type'];
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
        $products=[]; //Guardaremos todos los id de producto del excel
        $i=-1;
        
        while ($data = fgetcsv ($fp, 1000, ",")) {
            
            if($i>=0){ //La primera fila del csv no la queremos
                
                //CREAMOS UN OBJETO PRODUCTO POR CADA PRODUCTO
                ${"product" . "$i"}=new product($data[0],"$data[1]",$data[2]);
               
                
                array_push($products, ${"product" . "$i"}->getId()); //De esta manera puedo buscar el cliente ya que no se usar LARAVEL
            }
            
            $i++;
            
        }

        
        
        fclose ($fp);
        
        $customersInOrders=[]; //
        $i=-1; //-1 ya que esta sera la primera fila del csv que no nos interesa
        $fp = fopen ("uploadedFiles/orders.csv","r");
        
        while ($data = fgetcsv ($fp, 1000, ",")) {
            
            if($i>=0){
                ${"order" . "$i"}=new order($data[0],$data[1],"$data[2]");
                array_push($customersInOrders, ${"order" . "$i"}->getIdCustomer());
            }
            
            
            $i++;
            
            
        }
        
        
        
        $i=-1;
        fclose ($fp);
        
        $fp = fopen ("uploadedFiles/customers.csv","r");
        $customers=[]; //Guardare los ids tal como vienen en el excel
          
                
        while ($data = fgetcsv ($fp, 1000, ",")) {
            
            
            if($i>=0){ //La primera fila del csv no la queremos

                ${"customer" . "$i"}=new customer($data[0],"$data[1]","$data[2]"); 
                array_push($customers, ${"customer" . "$i"}->getId());
            }
            
            
            
            $i++;
            
            
        }
        
        
          
        fclose ($fp);
       
        $sum=0.0;
        $arrayReporte1= array();
        $arrayReporte3= array();
        $arrayReporte4= array();
        $arrayReporte5=array();
        
        $arrayReporte1[0]=array("id","total");//Primero mostraremos los nombres de los campos
        
        //LOGICA CON OBJETOS REPORTE 1 Y 3
       
        
        for($i=0; $i<=Order::getCount()-1; $i++){ //Recorremos todos los objetos Order
            
            $idProductos=${"order" . "$i"}->getIdProducts();

            for ($j = 0; $j <= strlen($idProductos)/2 ; $j++) { //Recorremos los ids de producto de orders.csv
                $idProducto=$idProductos[2*$j]; //Vamos cogiendo los pares que es donde se encuentran los numeros
                
                $sum+=${"product" . "$idProducto"}->getCost();
               
               
                $arrayReporte1[$i+1]=array(${"order" . "$i"}->getId(),$sum);//IdOrder-TotalPriceOrder
                $arrayParaReporte3[$i+1]=array($customersInOrders[$i],$sum);//IdOrder-IdCustomer-TotalPriceOrder
                
            }

            $sum=0;

        }
        

        


        
        
        
        $arrayParaReporte3Suma=sumByClient($arrayParaReporte3);
        arsort($arrayParaReporte3Suma);

        
   
        
        
        
        //Crear CSV para el reporte 1

        $ruta ="uploadedFiles/order_prices.csv";
        
        
        generarCSV($arrayReporte1, $ruta, $delimitador = ',', $encapsulador = '"');

        $idClientesConProducto =array_fill(0, 6, array());
        
        
        //LOGICA REPORTE 2 CON OBJETOS: SACAR EL ID DEL PRODUCTO Y LOS IDS DE TODOS LOS CLIENTES QUE TENGAN UN PEDIDO CON ESE PRODUCTO
        
        for($i=0; $i<=Product::getCount()-1; $i++){ //Recorremos todos los objetos Product
            $idProducto=${"product" . "$i"}->getId();
            
            for($j=0; $j<=Order::getCount()-1; $j++){ //Recorremos todos los objetos Order
                $idProductos=${"order" . "$j"}->getIdProducts();
                $idCostumer= ${"order" . "$j"}->getIdCustomer();
                
                for ($k = 0; $k <= strlen($idProductos)/2 ; $k++) { //Recorremos los ids de producto de orders.csv
                    
                    
                    
                    
                    if($idProducto==$idProductos[2*$k]){ //Problem i dont use Laravel
                        //array_push($idClientesConProducto[$i],${"customer" . "$id"};
                        //echo "cuando Estamos en: $idProducto  y encontramos el ${idProductos[2*$k]} del costumer: $customersInOrders[$j]";
                        array_push($idClientesConProducto[$i], $customersInOrders[$j]);
                        //var_dump($idClientesConProducto[$i]);
                    }
                    
                }
            }
            
            
        }
        
        //var_dump($idClientesConProducto);
        
        
        
        //Quitamos los repetidos de cada subArray
        
//        $result_array = array();
//        $unique_array = array();
//        foreach ($idClientesConProducto as $key => $sub_array) {
//            $unique_array = array_unique($sub_array);
//            $result_array[$key] = $unique_array;
//            
//        }
        
        $result_array= quitarRepetidos($idClientesConProducto);

       
        
        //Crear CSV para el reporte 2
        $array_final=array();
        
        array_push($array_final,"id");
        array_push($array_final,"customer_ids");
        
        foreach ($result_array as $key => $sub_array){
            $IdsWithSpace=implode(" ", $sub_array);
            
            array_push($array_final,"$key");
            array_push($array_final,$IdsWithSpace); 
        }
        
        
        $separatedInPairs = array_chunk($array_final, 2);
        
        $ruta ="uploadedFiles/product_customers.csv";
        generarCSV($separatedInPairs, $ruta, $delimitador = ',', $encapsulador = ' ');
        
        
        //LOGICA REPORTE 3 : SACAR EL ID DEL PRODUCTO Y LOS IDS DE TODOS LOS CLIENTES QUE TENGAN UN PEDIDO CON ESE PRODUCTO
        //$arrayParaReporte3Suma
        $arrayFinal3[0]=array("IdCliente","FirstName","LastName","Total");
        
        
        foreach ($arrayParaReporte3Suma as $key => $valor){
            for($i=0; $i<=Customer::getCount()-1; $i++){
                $costumerId= ${"customer" . "$i"}->getId();
                $costumerFirstName= ${"customer" . "$i"}->getFirstName();
                $costumerLastName= ${"customer" . "$i"}->getLastName(); 
                
                if($costumerId == $key){
                    $arrayFinal3[$i+1]=array($costumerId,$costumerFirstName,$costumerLastName,$valor);
                    
                }
            }
        }
        

        
        
        //Crear CSV para el reporte 3
        
        $ruta ="uploadedFiles/customer_ranking.csv";
        generarCSV($arrayFinal3, $ruta, $delimitador = ',', $encapsulador = ' ');
        
        
        
        
        echo "<a class='boton' href='download.php?file=order_prices.csv'>Descargar order_prices.csv</a><br> <br> ";
        echo "<a class='boton' href='download.php?file=product_customers.csv'>Descargar product_customers.csv</a><br> ";
        echo "<a class='boton' href='download.php?file=customer_ranking.csv'>Descargar customer_ranking.csv</a> <br> ";
        
    }else{
        $_SESSION['errores'] = $errores;
        header("Location: index.php");
    }
    
    
}
