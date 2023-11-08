<?php
session_start();

// Establecer la conexión a la base de datos (asegúrate de tener una conexión válida)
$servername = "localhost";
$username = "root";
$password = "";
$database = "league_of_legends_champs";

$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
	die("Conexión fallida: " . $conn->connect_error);
}

// Inicializar el campeón aleatorio si aún no está configurado
if (!isset($_SESSION['randomChampion'])) {
	// Realizar una consulta SQL para seleccionar un campeón aleatorio desde la base de datos
	$randomChampionQuery = "SELECT name FROM champs ORDER BY RAND() LIMIT 1";
	$result = $conn->query($randomChampionQuery);

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		$_SESSION['randomChampion'] = $row['name'];
	}
}

// Inicializar el contador de intentos fallidos si aún no está configurado
if (!isset($_SESSION['failedAttempts'])) {
	$_SESSION['failedAttempts'] = 0;
}

// Comprobar si el usuario adivinó el campeón
$mensaje = '';
$mensaje1 = '';
$mensaje2 = '';
$mensaje3 = '';
$mensaje4 = '';
if (isset($_GET['intentoUsuario'])) {
	// Guardar el intento del usuario en una variable
	$intentoUsuario = $_GET['intentoUsuario'];

	// Comprobar si la clave 'randomChampion' existe en $_SESSION antes de acceder a ella
	if (isset($_SESSION['randomChampion'])) {
		// Comprobar si el intento del usuario coincide con el campeón aleatorio
		if (strcasecmp($intentoUsuario, $_SESSION['randomChampion']) === 0) {
			$mensaje = '¡Has ganado! El campeón era ' . $intentoUsuario;
			// Restablecer el campeón aleatorio para el próximo intento
			unset($_SESSION['randomChampion']);
			// Restablecer el contador de intentos fallidos
			$_SESSION['failedAttempts'] = 0;
		} else {
			$_SESSION['failedAttempts']++;
			$mensaje = '¡Inténtalo de nuevo!';
		}
	} else {
		$mensaje = 'No hay campeón aleatorio configurado.';
	}
}

// Comprobar si el usuario decidió perder
if (isset($_GET['perder'])) {
	// Mostrar un mensaje de derrota y restablecer el campeón aleatorio
	$mensaje = '¡Has decidido perder! El campeón era ' . $_SESSION['randomChampion'];
	unset($_SESSION['randomChampion']);
	// Restablecer el contador de intentos fallidos
	$_SESSION['failedAttempts'] = 0;
}

// Mostrar pistas después de 2 intentos fallidos
if ($_SESSION['failedAttempts'] >= 2) {
	// Realizar una consulta SQL para obtener el título y los tags del campeón
	$pistasQuery = "SELECT title, tags, image_url FROM champs WHERE name = ?";
	$pistasStmt = $conn->prepare($pistasQuery);

	if ($pistasStmt === FALSE) {
		die("Error en la preparación de la consulta de pistas: " . $conn->error);
	}

	$pistasStmt->bind_param("s", $_SESSION['randomChampion']);
	$pistasStmt->execute();
	$pistasStmt->bind_result($titulo, $tags, $image_url);

	if ($pistasStmt->fetch()) {
		$mensaje .= '<br>';
		if ($_SESSION['failedAttempts'] >= 2) {
			$mensaje1 .= 'Pista 1:<br>';
			$mensaje1 .= 'Título del campeón: ' . $titulo . '<br><br>';
		}
		if ($_SESSION['failedAttempts'] >= 3) {
			$mensaje2 .= 'Pista 2:<br>';
			$mensaje2 .= 'Tags del campeón: ' . $tags . '<br><br>';
		}
		if ($_SESSION['failedAttempts'] >= 4) {
			$mensaje3 .= 'Pista 3:<br><br>';
			$mensaje3 .= '<img src="' . $image_url . '" alt="Campeón" width="300px">';
		}
	}

	$pistasStmt->close();
}

// Cerrar la conexión a la base de datos
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
	<title>MIKEDLE</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
	<br>
	<h1 class="text-center mt-3">MIKEDLE</h1><br>
	<div class="container mt-3">
		<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="text-center">
			<div class="form-group">
				<label for="intentoUsuario">
					<h4>Adivina el campeón:</h4>
				</label><br><br>
				<input type="text" name="intentoUsuario" id="intentoUsuario" class="form-control">
			</div><br>
			<button type="submit" class="btn btn-primary">Adivinar</button>
			<button type="submit" name="perder" class="btn btn-danger">Perder</button>
		</form>

		<?php
		if (!empty($mensaje)) {
			echo '<div class="alert alert-info mt-3">' . $mensaje . '</div>';
		}
		if (!empty($mensaje1)) {
			echo '<div class="alert alert-info mt-3">' . $mensaje1 . '</div>';
		}
		if (!empty($mensaje2)) {
			echo '<div class="alert alert-info mt-3">' . $mensaje2 . '</div>';
		}
		if (!empty($mensaje3)) {
			echo '<div class="alert alert-info mt-3">' . $mensaje3 . '</div>';
		}
		if (!empty($mensaje4)) {
			echo '<div class="alert alert-info mt-3">' . $mensaje4 . '</div>';
		}
		?>
	</div>
</body>

</html>


























<?php
// // Establece la conexión a tu base de datos (asegúrate de tener una conexión válida)
// $servername = "localhost";
// $username = "root";
// $password = "";
// $database = "league_of_legends_champs";

// $conn = new mysqli($servername, $username, $password, $database);

// // Verifica la conexión
// if ($conn->connect_error) {
// 	die("Conexión fallida: " . $conn->connect_error);
// }

// // Descarga el JSON de campeones
// $json_url = "https://ddragon.leagueoflegends.com/cdn/6.24.1/data/en_US/champion.json";
// $json_data = file_get_contents($json_url);
// $data = json_decode($json_data, true);

// // Elimina la tabla 'champs' si existe
// $sql_drop_table = "DROP TABLE IF EXISTS champs";
// $conn->query($sql_drop_table);

// // Crea la tabla "champs"
// $sql_create_table = "CREATE TABLE champs (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     name VARCHAR(255) NOT NULL,
//     title VARCHAR(255) NOT NULL,
//     tags VARCHAR(255) NOT NULL,
//     image_url VARCHAR(255) NOT NULL
// )";

// if ($conn->query($sql_create_table) === TRUE) {
// 	echo "Tabla 'champs' creada exitosamente.<br>";

// 	// Prepara una declaración SQL para la inserción
// 	$insert_query = $conn->prepare("INSERT INTO champs (name, title, tags, image_url) VALUES (?, ?, ?, ?)");

// 	if ($insert_query === FALSE) {
// 		die("Error en la preparación de la declaración: " . $conn->error);
// 	}

// 	// Inserta los datos en la tabla
// 	$champions = $data["data"];
// 	foreach ($champions as $champion) {
// 		$name = $champion["name"];
// 		$title = $champion["title"];
// 		$tags = implode(", ", $champion["tags"]);
// 		$image_url = "https://ddragon.leagueoflegends.com/cdn/img/champion/splash/{$name}_0.jpg";

// 		$insert_query->bind_param("ssss", $name, $title, $tags, $image_url);

// 		if ($insert_query->execute()) {
// 			echo "Datos de $name insertados exitosamente.<br>";
// 		} else {
// 			echo "Error al insertar datos de $name: " . $insert_query->error . "<br>";
// 		}
// 	}

// 	$insert_query->close();
// } else {
// 	echo "Error al crear la tabla 'champs': " . $conn->error;
// }

// // Cierra la conexión
// $conn->close();
?>