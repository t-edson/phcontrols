<?php
	echo 'Hola';
include 'globales.php';
////Lee dirección de retorno
$destino = $_SERVER['HTTP_REFERER'];  //Por defecto es la página de donde vino
$destino = explode('?',$destino)[0];  //Quita parámetro GET, por si venía incluido.
if ( isset($_GET['dest']) ) $destino = $_GET['dest'];  //A menos que se indique este parámetro
//Lee comando principal
if (isset($_GET['c'])) {
	$comm = readCommand($_GET['c']);  //Decodifica
} else {
	$comm = '';
}
//Ejecuta comando
set_database();   //Configura base de datos
session_start();  //Actualiza $_SESSION
//Verificación de seguridad
if (!SesionInic() && $comm!="ini-ses") {
	header("location:".$destino.'?m=home');
	exit;
}
if ($comm=='') {  //No hay comando
	redir();  //Sale verificando si hay página o modo destino.
	exit;  //Sale
}
//Ejecuta commando
DB_open();
if ($comm=="clo-ses") {  //Cerrar sesión
	session_start(); 
	// Destruir todas las variables de sesión.
	$_SESSION = array();
	//Borra cookie de sesión.
	//if (ini_get("session.use_cookies")) {
	//    $params = session_get_cookie_params();
	//    setcookie(session_name(), '', time() - 42000,
	//        $params["path"], $params["domain"],
	//        $params["secure"], $params["httponly"] 
	//    );
	//}
	// Finalmente, destruir la sesión.
	session_destroy();
	//Registra la salida de sesión
	$usuario = $_SESSION['usuario'];
	DB_exee("INSERT INTO registro(hora, idUsuario, objeto, evento, descripcion)
		VALUES(now(), '$usuario', 'sesion', 'logout', '')");
	redir();
} else if ($comm=="ini-ses") {	//Intenta iniciar la sesión
	$usuario = $_POST['usuario'];
	$clave   = $_POST['clave'];
	//Toma acción de acuerdo a la búsqueda.
	if (valid_login($usuario, $clave, $perf, $perf2, $perf3, $perf4)) {
		//Registra el inicio de sesión
		DB_exee("INSERT INTO registro(hora, idUsuario, objeto, evento, descripcion)
			VALUES(now(), '$usuario', 'sesion', 'login', '')");
		//Inicia la sesion
		session_start();
		//Guarda información
		$_SESSION['usuario'] = $usuario;
		$_SESSION['estado']  = 'Autenticado'; 	
		//Perfiles disponibles
		$_SESSION['perfil1'] = $perf;
		$_SESSION['perfil2'] = $perf2;
		$_SESSION['perfil3'] = $perf3;
		$_SESSION['perfil4'] = $perf4;
		//Inicia el Perfil actual
		$_SESSION['perfil']  = $perf;
//		read_profiles();    //Lee los perfiles
//		set_profile($perf); //Lee información del perfil actual.
		//Se redireccionará siempre.
		header("location:".$destino.'?m='.$_SESSION['home']); 
	} else {
		header("location:".$destino.'?e=1');
	}
} else if ($comm=="chg-pas") {	//Cambio de contraseña
	$cur_pass = $_POST['cur_pass'];
	$new_pas1 = $_POST['new_pass1'];
	$new_pas2 = $_POST['new_pass2'];
	$idUsu = $_SESSION['usuario'];
	$f= DB_read("SELECT clave FROM usuarios WHERE idUsuario='$idUsu'");
	if ($f['clave']!=$cur_pass) {
		redir('Contraseña inválida.');
		exit;
	};
	//Coincide la contraseña anterior
	if ($new_pas1 != $new_pas2) {
		redir('Contraseñas no coinciden.');
		exit;
	}
	//Valida la nueva contraseña
	if ($new_pas1 == '') {
		redir('Contraseña no puede ser vacía.');
		exit;
	}
	//Coinciden las nuevas contraseñas
	DB_exee("UPDATE usuarios SET clave='$new_pas1' WHERE idUsuario='$idUsu'");
	$_GET['m'] = 'home';
	redir();
//} else if ($comm=="set-per") {	//Fija perfil
//	$nper = comm_param('nper');  //Número de perfil
//	switch ($nper) {
//		case '1': set_profile($_SESSION['perfil1']); break;
//		case '2': set_profile($_SESSION['perfil2']); break;
//		case '3': set_profile($_SESSION['perfil3']); break;
//		case '4': set_profile($_SESSION['perfil4']); break;
//	}
//	//Regresa al home porque puede que la página actual no sea accesible en el nuevo perfil.
//	$_GET['m'] = 'home';  
//	redir();
//} else if ($comm=="add2mod") {	//Agrega parámetro a modo actual
//	foreach($com_pars as $par=>$val) {
//		//Se reemplaza o agrega
//		add_rep_codurl($_GET['m'], $par, $val);
//	}
//	/* Opcionalmente, como ayuda a las rutinas javascript, se aceptan como
//	parámetros del comando, los siguientes: */
//	if ( isset($_GET['par1']) ) {
//		$par1 = $_GET['par1'];unset($_GET['par1']);
//		$val1 = $_GET['val1'];unset($_GET['val1']);
//		add_rep_codurl($_GET['m'], $par1, $val1);
//	}
//	redir();
} else {                         //Otro comando
	redir("Comando Desconocido:".$comm);  //Redirecciona
}
DB_close();
?>
