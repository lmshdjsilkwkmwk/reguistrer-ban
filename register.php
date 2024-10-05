<?php
// Conexión a la base de datos
$conn = new mysqli('localhost', 'limber01', '5oc9UI]WU_1VRHMF', 'banco');

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los valores del formulario
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$correo = $_POST['correo'];
$telefono = $_POST['telefono'];
$nombre_usuario = $_POST['nombre_usuario'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT);

// Verificar si el nombre de usuario ya existe
$sql_verificar = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
$stmt = $conn->prepare($sql_verificar);
$stmt->bind_param("s", $nombre_usuario);
$stmt->execute();
$resultado_verificar = $stmt->get_result();

if ($resultado_verificar->num_rows > 0) {
    echo "El nombre de usuario ya está en uso. Por favor, elige otro.";
} else {
    // Generar número de cuenta
    $numero_cuenta = uniqid();

    // Preparar la consulta para insertar los datos
    $sql_insertar = "INSERT INTO usuarios (nombre, apellidos, fecha_nacimiento, correo, telefono, nombre_usuario, contrasena, numero_cuenta)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insertar = $conn->prepare($sql_insertar);
    $stmt_insertar->bind_param("ssssssss", $nombre, $apellidos, $fecha_nacimiento, $correo, $telefono, $nombre_usuario, $contrasena, $numero_cuenta);

    // Ejecutar la consulta de inserción
    if ($stmt_insertar->execute()) {
        echo "Registro exitoso.";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Cerrar las conexiones
$stmt->close();
$stmt_insertar->close();
$conn->close();
?>
