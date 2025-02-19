/* Archivo: db.php (Conexión a la base de datos) */
<?php
$host = "localhost";
$usuario = "root";
$clave = "";
$bd = "tienda";
$conn = new mysqli($host, $usuario, $clave, $bd);
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
?>

/* Archivo: index.php (Página principal) */
<?php include('db.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Tienda en Línea</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Bienvenido a la Tienda</h1>
    <div class="productos">
        <?php
        $result = $conn->query("SELECT * FROM productos");
        while ($row = $result->fetch_assoc()) {
            echo "<div class='producto'>";
            echo "<h2>" . $row['nombre'] . "</h2>";
            echo "<p>Precio: $" . $row['precio'] . "</p>";
            echo "<a href='producto.php?id=" . $row['id'] . "'>Ver más</a>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>

/* Archivo: producto.php (Detalles del producto) */
<?php include('db.php'); ?>
<?php
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM productos WHERE id = $id");
$producto = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $producto['nombre']; ?></title>
</head>
<body>
    <h1><?php echo $producto['nombre']; ?></h1>
    <p>Precio: $<?php echo $producto['precio']; ?></p>
    <a href="carrito.php?agregar=<?php echo $producto['id']; ?>">Añadir al carrito</a>
</body>
</html>

/* Archivo: carrito.php (Carrito de compras) */
<?php
session_start();
include('db.php');
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
if (isset($_GET['agregar'])) {
    $id = $_GET['agregar'];
    $_SESSION['carrito'][] = $id;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Carrito de Compras</title>
</head>
<body>
    <h1>Carrito de Compras</h1>
    <ul>
        <?php
        foreach ($_SESSION['carrito'] as $id) {
            $result = $conn->query("SELECT * FROM productos WHERE id = $id");
            $producto = $result->fetch_assoc();
            echo "<li>" . $producto['nombre'] . " - $" . $producto['precio'] . "</li>";
        }
        ?>
    </ul>
    <a href="procesar_pago.php">Pagar</a>
</body>
</html>

/* Archivo: admin.php (Panel de administración) */
<?php include('db.php'); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Panel de Administración</title>
</head>
<body>
    <h1>Panel de Administración</h1>
    <form action="admin.php" method="POST">
        <input type="text" name="nombre" placeholder="Nombre del producto" required>
        <input type="number" name="precio" placeholder="Precio" required>
        <button type="submit">Agregar Producto</button>
    </form>
    <h2>Lista de Productos</h2>
    <ul>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $precio = $_POST['precio'];
            $conn->query("INSERT INTO productos (nombre, precio) VALUES ('$nombre', '$precio')");
        }
        $result = $conn->query("SELECT * FROM productos");
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row['nombre'] . " - $" . $row['precio'] . "</li>";
        }
        ?>
    </ul>
</body>
</html>

/* Archivo: procesar_pago.php (Integración con PayPal) */
<!DOCTYPE html>
<html>
<head>
    <title>Procesar Pago</title>
</head>
<body>
    <h1>Procesar Pago con PayPal</h1>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="business" value="tu-correo-paypal@example.com">
        <input type="hidden" name="item_name" value="Compra en Tienda">
        <input type="hidden" name="amount" value="10.00">
        <input type="hidden" name="currency_code" value="USD">
        <input type="submit" value="Pagar con PayPal">
    </form>
</body>
</html>
