<?php
/* Funciones globales */
	define('PHCONTROLS', './phcontrols-0.4/');  //Librería Phcontrols.
	define('PROCOM', 'procom.php?'); //Procesador de comandos
	define('URL','//'.$_SERVER['HTTP_HOST']."/docutodo/"); //Procesador de comandos
	//Separadores para codificación de URLs
	define('SEP1','___');
	define('SEP2','_ee_');
	//Acceso a base de datos
	define('_HOST','localhost');   //Servidor de base de datos
	define('_USER','aprendo123_demo'); //Usuario
	define('_PASS','%h0lamund0');  //Contraseña
	define('_DBASE','aprendo123_demo');  //Nombre de base de datos
////////////// LIbrerías /////////////////
	//include PHCONTROLS.'phcontrols.php';
	include $_SERVER['DOCUMENT_ROOT']."/docutodo/_libs/phcontrols-trunk/phcontrols.php";
/////////////////  Manejo de sesión /////////////////////
function SesionInic() {
	/* Indica si se ha iniciado alguna sesión. */
	if (isset($_SESSION['usuario']) and $_SESSION['estado'] == 'Autenticado') {
		return true;
	} else {
		return false;
	}
}
function set_database() {
	// Fija parámetros de conexión de la base de datos.
	DB_set_mysql(_HOST, _USER, _PASS, _DBASE);
}

/////////////////  Encabezado y Pie de página /////////////////////
function incMenu($hlogout, $hchpass) {
	/* Genera el HTML del menú pricipal de la página.
	  Parámetros:
	  $hlogout -> URL a donde se direcciona al cierre de la sesión.
	  $hchpass -> URL a donde se direcciona para cambio de contraseña.
	*/
	ob_start(); //Inicia captura de HTML ?>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php echo URL; ?>"><h2 class="text-warning">Escribir123</h2></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mob-navbar" aria-label="Toggle">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mob-navbar">
                <ul class="navbar-nav mb-2 mb-lg-0 mx-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About Us</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Our Services</a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="#">Web designing</a></li>
                            <li><a class="dropdown-item" href="#">Web Development</a></li>
                            <li><a class="dropdown-item" href="#">SEO Analysis</a></li>
                            <li><hr class="dropdown-divider" /></li>
                            <li><a class="dropdown-item" href="#">Explore More</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact Us</a>
                    </li>
                </ul>
	<?php if (SesionInic()) { //Menú para sesión iniciada ?>
				<div class="navbar-nav dropdown">
					<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" 
						role="button" data-bs-toggle="dropdown" aria-expanded="false">Usuario</a>
					<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
						<!--li><a class="dropdown-item" href="#">Web designing</a></li-->
						<li><hr class="dropdown-divider" /></li>
						<li><a class="dropdown-item" 
						href="<?php echo PROCOM.'c=clo-ses' ?>">Cerrar sesión</a></li>
					</ul>
				</div>
	<?php } else { //Sesión no iniciada ?>
				<form class="d-flex" action="<?php echo hgo('ini-ses',''); ?>" method="post">
                    <input class="form-control me-2" type="text" placeholder="Username" name="usuario"/>
                    <input class="form-control me-2" type="password" placeholder="Password" name="clave"/>
                    <button class="btn btn-warning" type="submit">Login</button>
                </form>
	<?php } ?>
            </div>
        </div>
	</nav>
	<?php  echo ob_get_clean();  //Devuelve HTML
}
function incHeaderBegin($htitle) {
	/* Incluye el encabezado estándar (incluye el HTML inicial, el menú y el encabezado), que es
	variable de acuerdo a si se ha iniciado una sesión o no. 
	Parámetros:
	  $htitle   -> Título de la página.
	  $hlogout  -> URL a donde se direcciona al cierre de la sesión.
	*/
	echo '<!doctype html>';
	echo '<html>';
	echo '<head>';
	echo '  <meta charset="UTF-8">'."\n";
	echo '  <meta name="viewport" content="width=device-width, initial-scale=1.0">'."\n";
	echo '  <title>'.$htitle.'</title>';
	//Estilos propios
    //	echo '  <link rel="stylesheet" href="'.PHCONTROLS.'phcontrols.css">';
    echo '  <link rel="stylesheet" href="'.URL.'estilos.css">';
    //Estilos de la librería bs5-nav-tree
    echo '  <link rel="stylesheet" href="'.URL.'_libs/bootstrap/css/bootstrap.min.css">';
    echo '  <link rel="stylesheet" href="'.URL.'_libs/fontawesome/all.min.css" />';
    echo '  <link rel="stylesheet" href="'.URL.'_libs/bs5-nav-tree/dist/css/tree.css">';

	//Cierra encabezado
	echo '</head>';
}
function incHeaderEnd($hlogout, $hchpass) {
	echo '<body>';
	echo '  <div class="container">';   //DIV sin cerrar
	///////// Menú /////////////////////////
	incMenu($hlogout, $hchpass);
	///////// Encabezado(Banner) ///////////
	//echo '    <div class="head_img">';
	//echo '    <br>';
	//echo '    </div>';
}
function incHeader($htitle, $hlogout, $hchpass) {
	//Incluye el encabezado
	incHeaderBegin($htitle);
	//Aquí se pueden incluir etiquetas <style>
	incHeaderEnd($hlogout, $hchpass);
}
function incFooterBegin() {
	/*Incluye el inicio del pie de página. Se espera que esta función se use con  incFooterEnd() */
	include 'footer.html';
	echo '  </div>';  //cierra el div "container".
	echo '  <script src="'.URL.'_libs/bootstrap/js/bootstrap.bundle.min.js"></script>'."\n";
	// jQuery 
	//echo '  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>'."\n";
	// Incluye código Javascript definidas
	//	JSincludeScript();
}
function incFooterEnd() {
	/* Parte final de la plantilla de página. Se separa así para poder incluir 
	otros bloques SCRIPT, ya que aquí es el lugar apropiado */
	echo '</body>';  //cierra "body"
	echo '</html>';
}
function incFooter() {
	/* Rutina completa para incluir Pie de página. Solo es útil, cuando no se incluyen scripts 
	adicionales*/
	incFooterBegin();
	incFooterEnd();
}
/////////////////  Funciones para el panel lateral /////////////////////
function add_listbox($caption, $field_name, $items, $default, $class='') {
    if (isset($_POST[$field_name])) {
		//Hay un valor anterior
		$default = $_POST[$field_name]; //ignoramos el $default
		control_listbox($caption, $field_name, $items, $default, $class);
		return $default;
    } else {
		//No hay valor anterior
		control_listbox($caption, $field_name, $items, $default, $class);
		return $default;
    }
}
function button_crear($caption, $href) {
	/* Inserta un botón de estilo indicado en $style, con un enlace
	o URL asociado. */
	//echo '<div class="btn btn-warning" >';
	//echo '<span><a href="'.$href.'">'.$caption.'</a></span>';
	//echo '</div>';

	echo '<input type="submit" class="btn btn-warning"';
	echo '  value="'.$caption.'">';

}
function iniPanelBegin() { ?>
    <!--div id="mySidebar" class="sidebar">
		<a href="#">About</a>
		<a href="#">Services</a>
		<a href="#">Clients</a>
		<a href="#">Contact</a>
    </div-->
	<div class="contenido">
		<div class="panellat">
<?php }
function iniPanelEnd() {?>
		</div>
		<div id="main">
		<!--button class="openbtn" onclick="controlNav()">X</button-->
<?php }
function  iniPanel() {
	iniPanelBegin();
	//Espacio para poner los controles.
	iniPanelEnd();
}
function finPanel() { ?>
		</div>  <!-- main -->
	</div> <!-- contenido -->
	<!--script>
	function openNav() {
		document.getElementById("mySidebar").style.width = "250px";
		document.getElementById("main").style.marginLeft = "250px";
	}
	function closeNav() {
		document.getElementById("mySidebar").style.width = "0";
		document.getElementById("main").style.marginLeft = "0";
	}
	function controlNav() {
		if (event.target.innerText=="☰") {
			event.target.innerText="X";
			openNav();
		} else {
			event.target.innerText="☰";
			closeNav();
		}
	}
	openNav();
	</script-->
<?php }
///////// Codificación/Decodificación de URL //////////
function _cod_url(&$url) {
	/* Codiifca una URL en el formato que maneja hgo(). Se supone que
	la URL tiene la forma: comando&a=1&a=2 */
	$url = trim($url);
	$url = str_replace('&',SEP1, $url);  //Para poder juntar parámetros
	$url = str_replace('=',SEP2, $url);  //Para evitar el caracter "="
	/*Ahora debe quedar: comando<SEP1>a<SEP2>1<SEP1>b<SEP2>2 y se puede asignar
	a un solo parámetro de la URL. */
}
function add_rep_codurl(string &$codurl, string $par, string $val) {
	/* Agrega o reemplaza un parámetro a una URL codificada */
	$coms = explode(SEP1, $codurl);
	$codurl = $coms[0];  //primero el modo (o comando)
	$exist = false;
	for ($i=1; $i<count($coms); ++$i) {  //Los parámetros estan a partir del índice 1 
		$a = explode(SEP2, $coms[$i]);  //Los parámetros y sus valores están juntos
		if ($par==$a[0]) { //Ya existe, hay que reemplazar el valor
			$exist = true;
			$a[1]  = $val;
		}
		$codurl.=SEP1.$a[0].SEP2.$a[1];    //Acumula codificada
	}
	if (!$exist) {  //No existe, hay que agregar
		$codurl.=SEP1.$par.SEP2.$val;
	}
}
function encode_url($string) {
	//Codifica una URL en Base64
    return str_replace(['+','/','='], ['-','_',''], base64_encode($string));
}
function decode_url($string) {
    return base64_decode(str_replace(['-','_'], ['+','/'], $string));
}
function hgo(string $com, string $mod, string $mod1='', string $mod2='') {
	/* Redirecciona al procesador de comandos, pasando un comando ($comm) y uno 
	 o más modos ($mod, $mod1 y $mod2), con sus respectivos parámetros, en un
	 formato de URL codificado que permite diferenciar los parámetros del comando, 
	 de los parámetros de los modos. Al tener esa separación de comandos, se puede usar 
	 los mismos nombres para los parámetros de comando de los del modo.

	 Tanto el comando como los modos tienen la sgte sintaxis: 
		<comando o modo>&par1=<valor1>&par2=<valor2>
	 Un ejemplo de comando sería:
		del_tab&id=1&lim=5
	 En este caso se está enviando el comando "del_tab" con sus parámetros 
	 respectivos. Un ejemplo de modo sería:		
		list_tab
	 En este caso no se especifican parámetros y no deben escribirse "&".
	*/
	//Codifica los parámetros de comando en un solo parámetro.
	_cod_url($com);
	//Codifica los parámetros de modo en un solo parámetro.
	_cod_url($mod);
	_cod_url($mod1);
	_cod_url($mod2);
	$tmp = '';
	if ($com!='')  $tmp='c='.$com;
	if ($mod!='')  {if($tmp!='')$tmp.='&'; $tmp.='m='.$mod;}
	if ($mod1!='') {if($tmp!='')$tmp.='&'; $tmp.='m1='.$mod1;}
	if ($mod2!='') {if($tmp!='')$tmp.='&'; $tmp.='m2='.$mod2;}
	return PROCOM.$tmp;
}

function readCommand(string $com_cod) {
	/* Decodifica el comando y sus parámetros de la cadena de comando 
	codificada que genera hgo(): c=<comando codificado>.
	Devuelve el comando. Los parámetros se devuelven en el arreglo $com_pars.
	*/
	global $com_pars;
	$comms = [];
	$coms = explode(SEP1, $com_cod);
	for ($i=1; $i<count($coms); ++$i) {  //Los parámetros estan a partir del índice 1 
		$a = explode(SEP2, $coms[$i]);  //Los parámetros y sus valores están juntos
		$com_pars[$a[0]] = $a[1];   //Guarda aquí
	}
	return $coms[0];  //Devuelve comando.
}
function valid_login($usuario, $clave, &$perfil, &$perfil2, &$perfil3, &$perfil4) {
	/* Verifica si el usuario y contraseña son válidos en la tabla 
	USUARIOS de la base de datos actual. Si es válido, devuelve TRUE y actualiza
	las variables $perfil, $perfil2, $perfil3, $perfil4, son los perfiles 
	alternativos que puede adoptar el usuario.
	Se requiere que se haya llamado a DB_open().*/
	global $dbResult;
	//Realiza búsqueda de usuario
	$encontrado = FALSE;
	if ($clave == '') { //Validación
		//echo "Clave nula.";
		$encontrado = FALSE;
	} elseif (!valid_identifier($usuario)) {  //Para prevenir Inyección SQL
		//echo "Identif. no válido.";
		$encontrado = FALSE;
	} else {	//Consulta a base de datos
		//Usa iteración para protegerse de la inyección SQL en contraseña.
		try {
			DB_exec("SELECT * FROM usuarios WHERE IDUSUARIO='$usuario'");
			while ($fila = mysqli_fetch_assoc($dbResult)) {
				$perfil   = $fila["idPerfil"];
				$perfil2  = $fila["idPerfil2"];
				$perfil3  = $fila["idPerfil3"];
				$perfil4  = $fila["idPerfil4"];
				$clave_bd = $fila["clave"];
				if ($fila['habilitado'] == 0) break;  //Usuario deshabilitado
				//Verifica contraseña
				if (is_null($clave_bd)) {
					$encontrado = FALSE;
				} else {
					if ($clave == $clave_bd) $encontrado = TRUE;
					break;  //Ya no se debe buscar.
				}
			}
		} catch (Exception $e) {
			$encontrado = FALSE;
		}
	}
	return $encontrado;
}
function redir($error='') {
	/* Genera código de salida del script PHP, con los parámetros indicados.
	 Esta función realiza las siguientes tarea:
	 - Genera código de redirección a la página destino. La página destino es
	 la que misma que llamó a esta página, o a la página indicada en el 
	 parámetro GET: "dest".
	 - Devuelve el texto indicado en el parámetro "error", como parámetro GET
	 al momento de devolver el control a la página destino.
	 - Devuelve todos los parámetros que se reciben (como GET o POST), como
	 valores $_SESSION[], al momento de realizar la llamada a la página destino.
	 - Devuelve el parámetro "mode", como parámetro GET al realizar la llamada
	 a la página destino. El parámetro "mode" se devuelve de dos formas: Como 
	 parámetro GET "m", y como valor en $_SESSION[].
	 */
	//Lee dirección de retorno
	$target = $_SERVER['HTTP_REFERER']; //Por defecto es la página de donde vino. Casi siempre index.php.
	$target = explode('?',$target)[0];  //Quita parámetro GET, por si venía incluido.
	if ( isset($_GET['dest']) ) $target = $_GET['dest'];  //A menos que se indique este parámetro.
	//Devuelve los parámetros recibidos por GET
	$href = "location:".$target.'?';
	$sep = '';
	foreach($_GET as $campo => $valor){
		if ($campo=='c') continue;  //No se devuelve el comando. Ya se procesó aquí.
		$href .= $sep.$campo.'='.$valor;
		$sep = '&';
	}
	if ($error!='') $href.='&e='.$error;
	header($href);
	return 0;   //Por si se necesita usarlo como función.
}

?>