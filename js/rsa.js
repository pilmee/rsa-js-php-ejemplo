
function intval(mixed_var, base) {
  var tmp;

  var type = typeof mixed_var;

  if (type === 'boolean') {
    return +mixed_var;
  } else if (type === 'string') {
    tmp = str2bigInt(mixed_var, base || 10, 0);
    var a =(isNaN(bigInt2str(tmp)) || !isFinite(bigInt2str(tmp))) ? 0 : tmp;
	return a;
  } else if (type === 'number' && isFinite(mixed_var)) {
    return mixed_var | 0;
  } else {
    return 0;
  }
}

function gmp_mod(a, b){
	var r = mod(a, b);
	return r;
}

function gmp_mul(a, b){
	return mult(a,b);
}
function gmp_strval(a){
	return bigInt2str(a, 10);
}

function expMod ($base,  $exp,  $mod) {
	var $ac = str2bigInt('1', 10 , 0);
	var $b = $base;
	var $m = str2bigInt($mod, 10 , 0);
	while ($exp > 0) {
		if (($exp & 1) == 1) {
			$ac = gmp_mod(gmp_mul($ac, $b), $m);
		}
		$b = gmp_mod(gmp_mul($b, $b), $m);
		$exp = $exp >> 1;
	}
	return gmp_strval($ac);
}

var RSA = {
	asc : function (str) {
		return str.charCodeAt(0);
	},

	chr : function (code) {
		return String.fromCharCode(code);
	},

	cifrar : function (mensaje, e, m) {
		mensaje = Base64.encode(mensaje);
		e = str2bigInt(e, 10 , 0);
		m = str2bigInt(m, 10 , 0);
		var i = 0;
		var codificado = new Array();
		var codigo, codBigInt;
		for (i = 0; i < mensaje.length; i++) {
			codigo = RSA.asc(mensaje[i]);
			codBigInt = str2bigInt(codigo.toString(), 10, 0);
			codBigInt = powMod(codBigInt, e, m);
			codificado[codificado.length] = bigInt2str(codBigInt, 10);
		}
		return codificado.join(' ');
	},

	descifrar: function(mensaje, llave, modulo){
		var codigos = mensaje.split(" ");
		var $texto   = "";

		for(var pos in codigos){
			var codigo = codigos[pos];
			var cod = expMod(intval(codigo), llave, modulo);
			$texto += this.chr(cod);
		}

		return Base64.decode($texto);
	}
}