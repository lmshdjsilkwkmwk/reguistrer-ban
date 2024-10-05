<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli('localhost', 'limber01', '5oc9UI]WU_1VRHMF', 'banco');

// Verificar si la conexión falló
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los valores del formulario
$nombre_usuario = $_POST['nombre_usuario'];
$contrasena = $_POST['contrasena'];

// Preparar la consulta para evitar inyección SQL
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE nombre_usuario = ?");
$stmt->bind_param("s", $nombre_usuario);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si el usuario existe
if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    
    // Verificar la contraseña
    if (password_verify($contrasena, $usuario['contrasena'])) {
        // Iniciar sesión
        $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
        $_SESSION['saldo'] = $usuario['saldo'];
        $_SESSION['numero_cuenta'] = $usuario['numero_cuenta'];

        // Redirigir al usuario a la página de saldo
        header("Location: saldo.php");
        exit();
    } else {
        echo "Contraseña incorrecta.";
    }
} else {
    echo "El nombre de usuario no existe.";
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
