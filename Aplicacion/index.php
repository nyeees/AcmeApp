<?php 
    require_once 'includes/helpers.php';
    require_once 'includes/header.php';
    if(!isset($_SESSION)){ 
    session_start(); }
    
    
    
?>

    
    <body>
        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <?php if(isset($_SESSION['errores'])) : ?>
            <h3><?= isset($_SESSION['errores']) ? mostrarErrores($_SESSION['errores'], 'extension') : '' ?></h3>
            
            <?php endif;?>
            <label for="clienteInfo">Información de clientes:</label>
            <input type="file" name="clienteInfo" /> <br><br>
            
            <label for="productosInfo">Información de productos:</label>
            <input type="file" name="productosInfo" /> <br><br>
            
            <label for="pedidosInfo">Información de pedidos:</label>
            <input type="file" name="pedidosInfo" /> <br><br>
            
            
            <input type="submit" name="uploadBtn" value="Enviar" />

            
        </form>
        <?php borrarErrores()?>
    </body>
</html>