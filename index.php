<?php
	session_start();
	include('rsa.php');
	$rsa = new RSA();

	$keys = $rsa->GenerarClaves();
	$_SESSION['RSA_KEYS'] = $keys;
	$llavePublica = $keys['publica'];
	$modulo = $keys['modulo'];
?>
<!DOCTYPE html>
<html>
<head>
	<title>Prueba RSA (@pilmee)</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
	<style type="text/css">
		body {
			background-color:#000;
		}

		div.origen{
			color:#0f0;
			margin-bottom:1em;
		}

		div.origen textarea, div.origen button, div.origen label{ border:1px solid #0f0; color:#0f0; background-color:#000; padding:1em; }

		div.origen button { cursor: pointer; }

		div.resultado textarea, div.resultado button,  div.resultado label{ border:1px solid #F47414; color:#F47414; background-color:#000; padding:1em; }

		button { cursor: pointer; padding: .5em 1em !important; }

		textarea{ width: 100%; min-height: 200px }
		label{ border: none !important; padding: 0 !important;}
		i{ margin-right:1em !important; }
		.title{ border-bottom: 1px solid; padding-bottom:0.5em; margin-bottom:1em; }
		.sms{color:#3f3f3f;}
		textarea.input{ min-height:97.5px }
		.serialize{ color:#0ff !important; border-color:#0ff !important; }
		.unserialize{ color:#ff0 !important; border-color:#ff0 !important; }
	</style>

	<script type="text/javascript" language="javascript" src="./js/jquery.js"></script>
	<script type="text/javascript" language="javascript" src="./js/base64.js"></script>
	<script type="text/javascript" language="javascript" src="./js/bigint.js"></script>
	<script type="text/javascript" language="javascript" src="./js/rsa.js"></script>
	<script language="javascript" type="text/javascript">
	function eliminarBlancos(cadena) {
		for (i=0; i<cadena.length; ) {
			if (cadena[i] == " ")
				cadena = cadena.substring(i+1, cadena.length);
			else
				break;
		}

		for (i=cadena.length-1; i>=0; i=cadena.length-1)	{
			if (cadena[i] == " ")
				cadena = cadena.substring(0,i);
			else
				break;
		}

		return cadena;
	}

	function unserialize(serializedString){
		var str = decodeURI(serializedString);
		var pairs = str.split('&');
		var obj = {}, p, idx;
		for (var i=0, n=pairs.length; i < n; i++) {
			p = pairs[i].split('=');
			idx = p[0];
			if (obj[idx] === undefined) {
				obj[idx] = unescape(p[1]);
			}else{
				if (typeof obj[idx] == "string") {
					obj[idx]=[obj[idx]];
				}
				obj[idx].push(unescape(p[1]));
			}
		}
		return obj;
	};

	var serialize = function(){
		$("#dato_secreto_serializado").val($('#dato_secreto').serialize());
	};

	$(function() {
		var $llavePublica = '<?php echo $llavePublica; ?>';
		var $modulo = '<?php echo $modulo; ?>';

		$('#formulario').submit(function(e) {
			e.preventDefault();
			var mensaje = $('#dato_secreto').serialize();

			mensaje = eliminarBlancos(mensaje);
			if (mensaje.length > 0) {
				var valorCifrado = RSA.cifrar(mensaje, $llavePublica, $modulo);
				$('#dato_cifrado').val(valorCifrado);
				return true;
			} else {
				return false;
			}
		});

		$("#enviarMensaje").click(function(){
			var xhr = $.post("destino.php", { mensaje: $('#dato_cifrado').val() });
				xhr.success(function(data){
					$("#respuesta_secreta").val(data);
				})
		});

		$("#btnDescrifrarMensaje").click(function(){
			var rpta_cifrada = $("#respuesta_secreta").val();
			var rpta_descifrada = RSA.descifrar(rpta_cifrada, $llavePublica, $modulo);

			$("#respuesta_descifrada").val(rpta_descifrada);
			$("#respuesta_descifrada_parse").val(JSON.stringify(unserialize(rpta_descifrada)));
		});
	});
	</script>
</head>

<body>
	<div class="container">
		<h1>Cifrado <b>RSA</b> (@pilmee)</h1>

		<span class="pull-right sms">
			Cifrado en frontend con <b>llave publica</b> y descrifrado en backend con <b>llave privada</b>.
		</span>
		<h4 class="title">Origen: </h4>

		<div class="row origen">
			<div class="col-md-6">
				<form action="index.php" method="post" name="formulario" id="formulario">
					<div>
						<span class="pull-right">input text name: "dato_secreto"</span>
						<label for="dato_secreto">Mensaje original:</label>
						<textarea name="dato_secreto" id="dato_secreto" class="input" onkeyup="serialize()"></textarea>
						<textarea name="dato_secreto_serializado" id="dato_secreto_serializado" class="input serialize" readonly></textarea>
					</div>
					<div class="text-right">
						<button type="submit"><i class="glyphicon glyphicon-cog"></i> Cifrar mensaje</button>
					</div>
				</form>
			</div>
			<div class="col-md-6">
				<label for="dato_cifrado">Mensaje cifrado (RSA):</label>
				<textarea name="dato_cifrado" id="dato_cifrado" readonly></textarea>
				<div class="text-right">
					<button type="submit" id="enviarMensaje"><i class="glyphicon glyphicon-send"></i> Enviar mensaje</button>
				</div>
			</div>
		</div>

		<span class="pull-right sms">
			Cifrado en backend con <b>llave privada</b> y descrifrado en frontend con <b>llave publica</b>.
		</span>

		<h4 class="title">Respuesta: </h4>

		<div class="row resultado">
			<div class="col-md-6">
				<label for="respuesta_secreta">Respuesta servidor:</label>
				<textarea name="respuesta_secreta" id="respuesta_secreta" readonly></textarea>
				<div class="text-right">
					<button id="btnDescrifrarMensaje"><i class="glyphicon glyphicon-wrench"></i>Descifrar mensaje</button>
				</div>
			</div>

			<div class="col-md-6">
				<label for="respuesta_descifrada">Mensaje descifrado:</label>
				<textarea name="respuesta_descifrada" id="respuesta_descifrada" class="input" readonly></textarea>
				<textarea name="respuesta_descifrada_parse" id="respuesta_descifrada_parse" class="input unserialize" readonly></textarea>
			</div>
		</div>

	</div>
</body>

</html>
