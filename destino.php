<?php
	session_start();

	include 'rsa.php';

	try {
		$rsa = new RSA();

		$keys = $_SESSION['RSA_KEYS'];
		$llavePrivada = $keys['privada'];
		$modulo = $keys['modulo'];

		//var_dump($keys, $llavePrivada, $modulo);

		$dato_descifrado = $rsa->descifrar($_POST["mensaje"], $llavePrivada, $modulo);


		$resultado = array();
		parse_str($dato_descifrado, $resultado);

		$resultado["rpta_secreta"] = "Respuesta del servidor";

		$resultado = http_build_query($resultado);

		$dato_cifrado = $rsa->cifrar($resultado, $keys['privada'], $modulo);

		echo($dato_cifrado);

	} catch (exception $e) {
		echo $e->getMessage();
	}

?>