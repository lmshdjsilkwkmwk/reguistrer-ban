<?php
session_start();

if (!isset($_SESSION['nombre_usuario'])) {
    echo "Por favor, inicie sesión primero.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cuesta</title>
</head>
<body>
    <div>
        <div>
            <h2>Informacion de la Cuenta</h2>
            <div>
                <p>Nombre de Usuario: <?php echo $_SESSION['nombre_usuario'];?></p>
                <p>Saldo: <?php echo $_SESSION['saldo']; ?></p>
                <p>Numero de Cuenta: <?php echo $_SESSION['numero_cuenta']; ?></p>
                <form action="logout.php" method="POST">
                    <button type="submit">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

