<?php
/*                       PHCONTROLS
 Librería que incluye rutinas de conexión a base de datos MySQL
 y la generación de controles y formularios HTML.
 La conexión con la base de datos permite agregar, editar o 
 eliminar registros de una tabla.
 Las tablas en MySQL deben complir con:
 - Las tablas a agregar o editar, deben tener siempre una clave 
   primaria.
 - Los valores booleanos se representarán como valores Tinyint, porque
   las rutinas internas asumen que todo campo Tinyint se manejará como 
   booleno, al momento de agregar o modificar tablas.
 - Los valores booleanos deben tener como valor por defecto el 
   valor 0, para evitar que se ingresen valores NULL.
 - Los campos de contraseña, se representarán como campos de tipo
   CHAR, para que se muestren correctamente en los formularios.

				Por Tito Hinostroza 2020 - Derechos Reservados
*/
////////// Variables globales de base de datos //////////////
    //Variables que se deben incializar antes de iniciar la conexión.
    $DB_HOST = 'localhost';  
    $DB_USER = '';
    $DB_PASS = '';
    $DB_NAME = '';
    //Variables actualizadas después de iniciar una conexión.
	$dbConex  = NULL; //Conexión a la base de datos
	$dbResult = NULL; //Resultado de consulta
	$dbRowNum = 0;    //Número de filas del resultado
	$dbError  = '';	  //Mensaje de error

function removeQuotes(&$txt) {
	/* Elimina la primera y última comilla de una cadena, si es
	que están presentes. */
	if ($txt=='') return;
	$tmp = ($txt[0]=="'")? substr($txt, 1): $txt;  //Quita caracter inicial
	if ($tmp=='') {$txt = $tmp; return;}
	$txt = (substr($tmp, -1)=="'")? substr($tmp, 0, -1): $tmp; //Quita último caracter
}
//////////////////// Javascript /////////////////////
//Contenedores de rutinas Javascript.
$JScode = array();  //Líneas de código Javascript
$JSfunctions = array(); //Funciones Javascript
//Funciones para generar código Javascript
function JSaddCode($code) {
	/* Agrega código Javascript que luego se insertará
	en la página Web. */
	global $JScode;
	array_push($JScode, $code);
}
function JSaddFunction($name, $code) {
	/* Agrega una función Javascript a la sección <script>.
	Si la función ya existe, no la ingresa de nuevo. 
	En el parámetro $code, se debe escribir la definición completa
	de la función incluyendo el encabezado. Por ejemplo:
		JSaddFunction('abc', 'function abc() {...}');
	*/
	global $JSfunctions;
	if (array_key_exists($name, $JSfunctions ) ) {
		//Ya existe la función, no lo agrega.
	} else {
		//Agrega la función al código
		$JSfunctions[$name] = $code;
	}
}
function JSincludeScript() {
	/* Incluye el código Javascript generado con las rutinas:
	 JSaddCode() y JSaddFunction().
	 De preferencia, se debe llamar, después del pie de página. */
	global $JScode;
	global $JSfunctions;
	echo '<script>';
	//Incluye código Javascript
	foreach ($JScode as &$valor) {
		echo $valor;
	}
	//Incluye funciones Javascript
	foreach ($JSfunctions as &$valor) {
		echo $valor;
	}
	echo '</script>'."\n";
}
/////////////////  Manejo de base de datos /////////////////////
function DB_set_mysql($host, $user, $pass, $name) {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    $DB_HOST = $host;
    $DB_USER = $user;
    $DB_PASS = $pass;
    $DB_NAME = $name;
}
function InicConexBD() {
	/*Inicia la conexión a la base de datos. Actualiza la variable global $dbConex.*/
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
    global $dbConex;
	$dbConex=mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
	mysqli_set_charset($dbConex, "UTF8");
};
function EjBD($sql) {
	/*Ejecuta una consulta. Actualiza las variables globales $dbResult, $dbRowNum y $dbError.
	Si la consulta se ejecuta exitosamente:
	- Devuelve el resultado en: $dbResult. 
	- Devuelve el número de filas en $dbRowNum.
	- Devuelve TRUE.
	Si la consulta se ejecuta con error:
	- Devuelve mensaje de error en $dbError.
	- Devuelve FALSE.
	- Libera el objeto $dbConex.
	La forma usual de uso es:
	if (!EjecBD('...')) exit;   //sale generando error, si se produce.
	*/
	global $dbConex;
	global $dbResult;
	global $dbRowNum;
	global $dbError;
	$dbResult=mysqli_query($dbConex, $sql);
	if($dbResult === FALSE) {
		$dbError = mysqli_error($dbConex);
		//jumbotron('Lo siento :( Tenemos problemas: '.$dbError, 'index.php');
		mysqli_close($dbConex);
		return false;
	} else {
		if (is_object($dbResult)) {
			//Solo tiene sentido en las consultas que devuelven registros
			$dbRowNum = mysqli_num_rows($dbResult); 
		}
		$dbError = '';
		return true;
	}
}
function EjecBD($sql) {
	/*Similar a EjBD(), pero cuando hay error Genera código Bootstrap con mensaje de error.

	La forma usual de uso es:
	if (!EjecBD('...')) exit;   //sale generando error, si se produce.
	*/
	global $dbError;
	$result = EjBD($sql);
	if (!$result) jumbotron('Lo siento :( Tenemos problemas: '.$dbError, 'index.php');
	return $result;
}
function CerrarBD() {
	/* Cierra la conexión a la base de datos y libera el objeto $dbResult, si es que se 
	hubiera usado */
	global $dbConex;
	global $dbResult;
    if ($dbResult!=NULL) mysqli_free_result($dbResult);
	if ($dbConex!=NULL) mysqli_close($dbConex);
}
//////////////// Mensajes ///////////////////////////////
function alert_warning($msg) {
	/* Genera HTML para un mensaje de advertencia. */
	echo '<br>';	
	echo '<div class="alert-warning">';
	echo '  <p>'.$msg.'</p>';
	echo '</div>';
}
function alert_danger($msg) {
	/* Genera HTML para un mensaje de error. */
	echo '<div class="alert alert-danger">';
	echo $msg;
	echo '</div>';
}
function alert_danger_small($msg) {
	/* Genera HTML para un mensaje de error. */
	echo '<div class="alert alert-danger small">';
	echo $msg;
	echo '</div>';
}
function jumbotron($msg, $pagRetorno, $txtBoton='Volver &raquo;') {
	/* Muestra un mensaje con letras grandes que llena toda el área disponible.
	Se incluye también un botón par regresar a una página específica.
	*/
	echo '<br>';	
	echo '<div class="col-xs-12 col-sm-8 col-md-6 col-sm-offset-2 col-md-offset-3">';
	echo '  <div class="jumbotron">';
	echo '    <p>'.$msg.'</p>';
	echo '    <p> ';
	echo '    <a class="btn btn-lg btn-primary" href="'.HWEB.'/'.$pagRetorno.'" role="button">'.$txtBoton.'</a>';
	echo '    </p>';
	echo '  </div>';
	echo '</div>';
}
//////////////// Controls //////////////////////////////
function control_edit($caption, $field_name, $default, $class='') {
	/* Inserta control de edición para texto. El control tendrá la forma: 
	 <caption> <control de edición> 
	 El parámetro $field_name, se escribirá como atributo "name" e "id" del
	 control <input>. */
	if ( substr($caption, -1)=='*' ) {$caption = substr($caption, 0, -1).'<strong>&nbsp;*</strong>';}
	echo '<div class="control-field '.$class.'">';
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';  //Campo para el control en sí
	echo '    <input type="text" class="form-control" 
			  name="'.$field_name.'" value="'.$default.'" ';
	if ($class=='cnt-disabled')	echo ' disabled ';
	echo '    id="'.$field_name.'">';
	echo '  </div>';
	//Campo para el mensaje de error	
	echo '  <div class="msg">';
	echo '  </div>';
	echo '</div>'."\n";
}
function control_password($caption, $field_name, $default, $class='') {
	/* Inserta control de edición para texto. El control tendrá la forma: 
	 <caption> <control de edición> 
	 El parámetro $field_name, se escribirá como atributo "name" e "id" del
	 control <input>. */
	if ( substr($caption, -1)=='*' ) {$caption = substr($caption, 0, -1).'<strong>&nbsp;*</strong>';}
	echo '<div class="control-field '.$class.'">';
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';
	echo '    <input type="password" class="form-control" 
	          name="'.$field_name.'" value="'.$default.'" ';
	if ($class=='cnt-disabled')	echo ' disabled ';
	echo '    id="'.$field_name.'">';
	echo '  </div>';
	//Campo para el mensaje de error
	echo '  <div class="msg">';
	echo '  </div>';
	echo '</div>';
}
function control_number($caption, $field_name, $default, $step, $class='') {
	/* Inserta control de edición para campo numérico entero. El control tendrá la forma: 
	 <caption> <control de edición> 
	 El parámetro $field_name, se escribirá como atributo "name" e "id" del
	 control <input>. */
	if ( substr($caption, -1)=='*' ) {$caption = substr($caption, 0, -1).'<strong>&nbsp;*</strong>';}
	echo '<div class="control-field '.$class.'">';
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';
	echo '    <input type="number" class="form-control" ';
	if ($step!='') { echo 'step="'.$step.'"'; }
	echo '    name="'.$field_name.'" value="'.$default.'" ';
	if ($class=='cnt-disabled')	echo ' disabled ';
	echo '    id="'.$field_name.'">';
	echo '  </div>';
	//Campo para el mensaje de error
	echo '  <div class="msg">';
	echo '  </div>';
	echo '</div>'."\n";
}
function control_switch($caption, $field_name, $default, $class='') {
	/* Inserta control de edición para campo de tipo 
	ON-OFF. El campo $default, se espera que sea un número 
	con valores 0 o 1. El control tendrá la forma: 
	 <caption> <control on-off> 
	 El parámetro $field_name, se escribirá como atributo "name" e "id" del
	 control <input>. */
	if ( substr($caption, -1)=='*' ) {$caption = substr($caption, 0, -1).'<strong>&nbsp;*</strong>';}
	echo '<div class="control-field '.$class.'">';
	// Etiqueta
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';
	echo '    <label class="switch">';
	if ($default == 1) {
		echo '  <input type="checkbox" name="'.$field_name.'" checked ';
		if ($class=='cnt-disabled')	echo ' disabled ';
		echo '    id="'.$field_name.'">';
	} else {
		echo '  <input type="checkbox" name="'.$field_name.'" ';
		if ($class=='cnt-disabled')	echo ' disabled ';
		echo '    id="'.$field_name.'">';
	}
	echo '  	<span class="slider"></span>';
	echo '    </label>';
	echo '  </div>';
	//Campo para el mensaje de error
	echo '  <div class="msg">';
	echo '  </div>';
	echo '</div>';
}
function control_listbox($caption, $field_name, $items, $default, $class='') {
	/* Inserta control de selección de lista. El control tendrá la forma: 
	 <caption> <lista de selección> 
	 El parámetro $field_name, se escribirá como atributo "name" e "id" del
	 control <select>. 
	 $items es un arreglo de cadenas, usados para llenar la lista. Tienen
	 la forma:  <valor>\t<etiqueta>
	*/
	if ( substr($caption, -1)=='*' ) {$caption = substr($caption, 0, -1).'<strong>&nbsp;*</strong>';}
	echo '<div class="control-field '.$class.'">';
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';
	echo '    <select class="form-control" ';
	echo '    name="'.$field_name.'" ';
	if ($class=='cnt-disabled')	echo ' disabled ';
	echo '    id="'.$field_name.'">';
	foreach ($items as $txt) {
		$a = explode("\t", $txt);
		$value = $a[0];  //Valor.
		$label = (count($a)>1)?$a[1]:$value;  //Etiqueta
		removeQuotes($label); //Para mostrar siempre se quita comillas
		echo '  <option value="'.$value.'" ';
		if ($value == $default) echo ' selected ';
		echo ' >'.$label.'</option>';
	}
    echo '    </select>';
	echo '  </div>';
	//Campo para el mensaje de error
	echo '  <div class="msg">';
	echo '  </div>';
	echo '</div>';
}
function button_add($caption, $action) {
	/* Genera código HTML para un botón.
	Un ejemplo de llamada, sería: button_grab('Grabar',"alert('aaa')"); */
	echo '<div class="btn btn-primary" 
			onclick="'.$action.'"
		>';
	echo '✚&nbsp;&nbsp;'.'<span>'.$caption.'</span>';
	echo '</div>';
}
function button_grab($caption, $action) {
	/* Genera código HTML para un botón.
	Un ejemplo de llamada, sería: button_grab('Grabar',"alert('aaa')"); */
	echo '<div class="btn btn-primary" 
			onclick="'.$action.'"
		>';
	echo '&#x1f5ab; '.'<span>'.$caption.'</span>';
	echo '</div>';
}
function button_submit($caption) {
	echo '<input type="submit" class="btn btn-primary"';
	echo '  value="'.$caption.'">';
}
///////////////////// Bloques /////////////////////
function startBlock($title, $title_buttons=[]) {
	/* Genera el HTML para crear el inicio de un bloque.
	El bloque debe cerrarse con endBlock(). 
	Parámetros:
	  $title -> Título en la barra de título del bloque.
	  $title_buttons -> Arreglo con información de botones.
	El arreglo $title_buttons tiene la forma:
	  $buttons = array( '+'=>'/index.php',
						'-'=>'/otro.php');
	*/
	echo '<div class="panel_block">';
	echo '  <div class="panel-heading">';
	echo '    <div class="text">'.$title.'</div>';
	echo '    <div class="btns">'; 
	foreach($title_buttons as $key => $val) {
		//print "$key = $val <br>";
		echo '  <a href="'.$val.'">'.$key.'</a>';
	}
	echo '    </div>';
	echo '  </div>';
    echo '  <div class="panel-body">';
}
function endBlock() {
	/* Genera el HTML para crear el inicio de un bloque */
    echo '  </div>'."\n";
    echo '</div>'."\n";
}
function block_separatorh() {
	echo '<div class="block_seph"></div>';
}
///////////////// Rutinas front-end /////////////////
function FormInicioSesion($institucion, $url_ini_ses, $msg_inf, $hvalidar) {
	/* Genera Html para crear un formulario de Inicio de sesión.
	   Parámetros:
		 $institucion -> Institución para el cual se debe validar al usuario. Para
		 				 el usuario "super" debe ser "inst_super".
		 $url_ini_ses -> Es la URL destino a donde se regresará después de realizar la validación.
		 $msg_inf     -> Mensaje adicional a mostrar como información adicional.
		 $hvalidar    -> URL para la validación de  la sesión.
	*/
	//$url_ini_ses = HWEB."index.php";
	$res = '<form class="form_log1" action="'.$hvalidar.'" method="post">';
	$res.= '<h3>Inicio de sesión</h3>';
	$res.= '<div class="form-group">';
    //$res.= '	<label> Usuario: </label> ';
	$res.= '	<input type="text" class="form-control" placeholder="&#128102; Usuario" name="usuario">';
	$res.= '</div>';
	$res.= '<div class="form-group">';
	//$res.= '	<label> Clave: </label> ';
	$res.= '	<input type="password" class="form-control" placeholder="&#128274; Clave" name="clave">';
	if ($msg_inf!='') {
	$res.= '	<div class="alert alert-danger small">';
	$res.= 	      $msg_inf;
	$res.= '	</div>'; 
	}
	//Campos ocultos
	$res.= '	<input type="hidden" name="destino" value="'.$url_ini_ses.'"/>';
	$res.= '	<input type="hidden" name="instit" value="'.$institucion.'"/>';
	//	
	$res.= '</div>';
	$res.= '<input type="submit" class="btn btn-primary" value="Ingresar">';
	$res.= '</form>';
	echo $res;
}
function _item_bloque($nombre, $img, $id, $hsel, $draggable=true) {
	/* Genera HTML para un ítem a dibujarse en forma de bloque 
	  cuadrado. 
	  Parámetros:
		$nombre -> Título que aparece en el ítem.
		$img    -> Ruta de Imagen o caracter a mostrar.
		$id     -> Identificador único del ítem. Debería corresponder 
				   al PK en la base de datos.
		$hsel   -> Enlace a donde se dirigirá cuando se haga click en
				   el ítem.
	*/
	/*El <button> será el contenedor principal que representará
	al botón. A este botón se le dará comportamiento INLINE-BLOCK
	para que los botones se distribuyan horizontalmente.*/
	if ($draggable) {
		echo '<button class="item_bloque panel"
		id="'.$id.'" 
		onclick="location.href=\''.$hsel.'\'"
		draggable="true" ondragstart="dragBTI(event)">';
	} else {
		echo '<button class="item_bloque panel"
		onclick="location.href=\''.$hsel.'\'"
		draggable="false" >';
	}
	/*Este <div> será el contenedor secundario para darle 
	display: FLEX y definir el alineamiento de los elemenos que contiene.*/
	echo '  <div>';
	/* Elemento para agrupar la imagen y el texto */
	echo '  <span>';
	//Desactiva "draggable" del <img> (activo por defecto) para que no interfiera.
	echo '    <img draggable="false" src="'.HWEB.$img.'" />';
	echo      $nombre;
	echo '  </span>';
	echo '  </div>';
	echo '</button>';
}
function block_table_icons($titulo, $icon, $tabla, 
			$col_id, $col_txt, $msj_agre, $hadd, $hsel, $hdel) {
	/* Genera el HTML de una lista de bloques, a partir de registros
	  de una tabla de la base de datos. Se debe haber llamado primero 
	  a InicConexBD(). 
	  Parámetros:
	    $tabla -> Nombre de la tabla de la base de datos.
	    $col_id -> Columna de la tabla que se usará como clave primaria.
	    $col_txt -> Columna de la tabla que se usará como etiqueta del ícono.
	    $msj_agre -> Mensaje que aparece en el ícono de "Agregar".
		$hadd -> Enlace a activar, cuando se hace click en el botón "Agregar".
		$hsel -> Enlace a activar, cuando se hace click en el ítem.
		$hdel -> Enlace a activar, cuando se elimina un ítem.
	*/
	global $dbRowNum, $dbResult;
	global $ICO_TRASH, $ICO_UPDATE;
	EjecBD("SELECT * FROM $tabla ORDER BY $col_id");
	//Convierte ícono en destino "soltable" para el arrastre.
	$ico_drop = str_replace('<img', 
	  '<img ondragover="event.preventDefault()"
	  	ondrop="dropBTI(event)" 
	  ',
	  $ICO_TRASH);
	//Genera encabezado
	$buttons = array($ico_drop => '#');
	startBlock($titulo, $buttons);
	if ($dbRowNum==0) {
      //echo '<div> Usted no tiene instituciones aún :( </div>';
      //echo '<p></p>';
	} else {
	  //Hay filas de datos
	  //Explora las filas
      while ($fila = mysqli_fetch_assoc($dbResult)){ 
      	$id_inst = $fila[$col_id];
		$nombre = $fila[$col_txt];  //Columna con el texto a mostrar en el ícono
		//$hsel = $hsel.'&id='.$id_inst;  //Completa con información del ID
		$h = str_replace('{id}', $id_inst, $hsel);
		_item_bloque($nombre, $icon, $id_inst, $h);
	  }
	}
	//Agrega botón "Agregar ..."
	//$hadd = str_replace('{id}', '', $hadd);
	_item_bloque($msj_agre,'/images/add64.png', '', $hadd, false);
	endBlock();
	//Agrega rutinas Javascript
	//Rutina para drag-drop. Basada en: https://www.w3schools.com/html/tryit.asp?filename=tryhtml5_draganddrop 
	//$hdel = str_replace('{id}', '', $hdel);
	JSaddFunction('dropBTI', '
	function dropBTI(ev) {
		var idd = ev.dataTransfer.getData("text");
		var hdel = "'.$hdel.'";
		hdel = hdel.replace("{id}", idd);
		document.location.href=hdel;
	}
	');
	JSaddFunction('dragBTI', '
	function dragBTI(ev) {
		ev.dataTransfer.setData("text", ev.target.id);
	  }	
	');
}
function table_list($fsql, $hidecols, $buttons) {
	/* Genera el HTML de una tabla HTML que representa los registros
	 de una tabla de la base de datos. Se debe haber llamado primero 
	 a InicConexBD(). 
	 Parámetros:
		$fsql    -> Consulta a la base de datos para extraer las filas a 
					mostrar. 
		$hidecols-> Número de columnas de la consulta que se ocultarán.
					Las columnas a ocultar, serán siempre las primeras.
		$buttons -> Arreglo de botones a coloar en la última columna.
	 La consulta en $sql, debe ser de la forma:
		SELECT campo1, campo2 FROM tabla WHERE ...;
	 Todos los campos indicados se mostrarán en la tabla HTML.
	 El parámetro $buttons debe contener cadenas de la forma: 
		<url_botón>|<icono_botón>|<descripcion_icono>
	 El campo <url_botón> puede incluir referencias a campos de la consulta
	 de modo que se personalicen para cada fila. Ejemplo:
	   www.sitio.com?command=del-user&id={idUsuario}
	*/
	global $dbRowNum, $dbResult, $dbConex;
	global $ICO_TRASH, $ICO_UPDATE;
	$autonum = true;
	if ($result = mysqli_query($dbConex, $fsql)) {
		// Hay filas de datos.
		echo '<table class="table_list">';
		// Lee información de las columnas del resultado.
		$fieldinfo = mysqli_fetch_fields($result);
		//foreach ($fieldinfo as $val) {
		//  echo "Name: ". $val->name;
		//  echo "Type: ". $val->type;
		//  echo "Max. Len: ". $val->max_length. '<br>';
		//}
		//Genera encabezado
		echo '<tr>';
		if ($autonum) echo '  <th>#</th>'; //Columna de numeración
		$ncol = 0;  //Inicia contador de columna
		foreach ($fieldinfo as $val) {
			$ncol++;
			if ($ncol <= $hidecols) continue;  //Columna oculta
			echo '<th>'; 
			echo $val->name;
			echo '</th>';
		}
		if (sizeof($buttons)>0) echo '  <th>Acciones</th>'; //Columna de acciones
		echo "</tr>\n"; 
		//Genera filas
		$nrow = 0;
		while ($fila = mysqli_fetch_assoc($result)) {
			$nrow++;
			echo '<tr>';
			if ($autonum) echo '  <td>'.$nrow.'</td>';   //Columna de numeración
			$ncol = 0;  //Inicia contador de columna
			//Escribe las primeras columnas
			foreach ($fieldinfo as $val) {
				$ncol++;
				if ($ncol <= $hidecols) continue;  //Columna oculta
				echo '  <td class="t'.$val->type.'">';  //Indica el tipo en la clase
				echo $fila[$val->name];
				echo '  </td>';
			}
			//Genera la Columna de acciones
			if (sizeof($buttons)>0) {  
				echo '<td class="actions">';
				foreach ($buttons as $valor) {
					$items = explode('|', $valor);
					//Reemplaza las variables en URL
					$tmp = $items[0];
					foreach ($fieldinfo as $val) {
						$href = str_replace('{'.$val->name.'}', $fila[$val->name], $tmp);
						$tmp = $href;   //Prepara otro reemplazo
					}
					//Genera enlace con ícono y etiqueta
					echo '<a href="'.$href.'">';
					echo '  <img src="'.$items[1].'" alt="1" title="'.$items[2].'" height="16">';
					echo '</a>';
					//echo '&nbsp;';
					//echo $items[2];
				}
				echo '</td>';
			}
			echo "</tr>\n";
		}
		//Cierra tabla
	  	echo '</table>';
	} else {
		//No hay información.
	}
}
function _decode_row($row, &$name, &$type_nam, &$type_arg, &$default, &$extra, &$null) {
	/* Decodifica una fila de DESCRIBE table_name. Función para
	ser usada por únicamente por: form_edit_table() */
	$name    = $row['Field'];
	$type_all= $row['Type'];
	$default = $row["Default"];
	$extra   = $row['Extra'];
	$null    = $row['Null'];
	//Obtiene nombre del tipo y argumento (lo que va entre paréntesis).
	$posPar = strpos($type_all, '(');  //Posición del paréntesis
	if ($posPar === false) {
		$type_nam = $type_all;
		$type_arg = '';
	} else {
		$type_nam = substr($type_all, 0, $posPar);
		$type_arg = substr($type_all, $posPar+1);
		$type_arg = substr($type_arg, 0, -1);  //Quita último paréntesis
	}
}
function _gen_control($etiq, $name, $type_nam, $type_arg, $default, $class) {
	/* Genera el html de un control, de acuerdo a los parámetros
	 indicados:
	  $etiq    -> Título o etiqueta que se colcoará delante del control.
	  $name    -> Nombre a usar para el elemento INPUT o SELECT del control.
	  		    Este valor también se usará para el ID.
	  $type_nam-> Nombre del tipo de dato a usar para el control.
	  $type_arg-> Dato adicional del tipo.
	  $default -> Valor por defecto que toma el control.
	  $class   -> Clase a usar para el control.
	*/
	switch ($type_nam) {
		case "varchar":
			control_edit($etiq, $name, $default, $class);
			break;
		case "char":
			control_password($etiq, $name, $default, $class);
			break;
		case "int":
			control_number($etiq, $name, $default, '', $class);
			break;
		case "tinyint":
			control_switch($etiq, $name, $default, $class);
			break;
		case "enum":
			//$type_arg = str_replace("'",'', $type_arg); 
			$items = explode(',', $type_arg);
			control_listbox($etiq, $name, $items, $default, $class);
			break;
		case "decimal":
			control_number($etiq, $name, $default, '', $class);
			break;
		default: 
			echo "!!!$etiq - $type_nam - $type_arg <br>";
			//control_edit($etiq, $name, $default);
	}
}
function _gen_control_columns($cols, $column, $etiq, $subq, $valini, &$in_id) {
	/* Genera un control en HTML para una columna de una tabla 
	  Parámetros:
	  $cols   -> Arreglo con información sobre las columnas de la tabla.
	  $column -> Nombre de la columna de la tabla para la que se creará
				 el control. Se ignora la caja.
	  $etiq   -> Etiqueta o título que se pondrá antes del control.
	  $subq   -> Subquery. Consulta que se debe hacer para obtener los valores
				   permitidos de este campo.
	  $valini -> Valor inicial del campo. Si es cadena vacía, se ignora.
	  $in_id  -> Devuelve el ID del elemento INPUT o equivalente, que alamcena 
	  			 el valor leído.
	*/
	global $dbConex;
	foreach ($cols as $row) {
		_decode_row($row, $name, $type_nam, $type_arg, $default, $extra, $null);
		if ($valini != '') $default = $valini;
		if (strcasecmp($name, $column) == 0) {  //Compara
			// Se encontró el campo.
			/* Se generará un valor para el "Name" y el "ID" del control de la forma:
				<nombre de columna>-<tipo>-<obligatoriedad>-<auto_increment>
			Esto es necesario para que las rutinas que insertan valores, y las rutinas
			de verificación "javascript", puedan trabajar correctamente. */
			//Verifica si el campo requiere consulta a la base de datos.
			if ($subq!='') {
				/*Se genera un control de tipo lista desplegable, cambiando 
				el tipo del campo. Hay que tomar en cuenta este posible cambio 
				para evitar problemas posteriores. */
				$type_nam='enum';  //Se considera como enum. 
				//Obtiene valores
				$type_arg = '';
				$q = mysqli_query($dbConex, $subq);
				while ($row = mysqli_fetch_array($q)) {
					$cols[] = $row; //Acumula en el arreglo.
					$type_arg.= $row[0].",";  //Se deja libertad a la consulta para incluir comillas o no.
					//$type_arg.="'".$row[0]."',";
				}
				if ($type_arg!='') {  //Quita coma final
					$type_arg = substr($type_arg, 0, -1);
				}
				//$type_arg="'aaa','bbb'";  //Valores 
			}
			if ($null=='NO') {  //Es obligatorio, No puede ser NULL.
				//Agrega código Javascript de verificación.
				//JSaddCode('$("#'.$in_id.'").on("change keyup paste", function(){
				//valid("#'.$in_id.'"); });');
				if ($extra=='auto_increment') {
					$in_id = $name.'-'.$type_nam.'-1-1';  //Obligatorio y autoincrement
					//Los campos auto_increment no se editan. Aparecerán deshabilitados
					_gen_control($etiq, $in_id, $type_nam, $type_arg, '', 'cnt-disabled');
				} else {
					$in_id = $name.'-'.$type_nam.'-1-0';  //Obligatorio y normal.
					_gen_control($etiq.'*', $in_id, $type_nam, $type_arg, $default, '');
				}
			} else {  //Campo no obligatorio.
				if ($extra=='auto_increment') {  //Sería raro un auto_increment "Nullable".
					$in_id = $name.'-'.$type_nam.'-0-1';  //Obligatorio y autoincrement
					//Los campos auto_increment no se editan. Aparecerán deshabilitados
					_gen_control($etiq, $in_id, $type_nam, $type_arg, '', 'cnt-disabled');
				} else {
					$in_id = $name.'-'.$type_nam.'-0-0';  //Obligatorio y normal.
					_gen_control($etiq, $in_id, $type_nam, $type_arg, $default, '');
				}
			}
			break;   //Sale porque ya encontró.
		}
	}
}
function form_insert($table, $fields, $hins, $msj_agre){
	/* Genera HTML de un formulario para agregar registros a una
	  tabla. Parámetros:
		$table  -> Tabla a editar.
		$fields -> Arreglo de campos que se desean editar. Debe tener la forma: 
			       $fields = ['idReg|ID','Nombre', 'direccion|Dirección'];
		$hins   -> Enlace a donde se envía con el botón "Agregar".
		El formato de los ítems de $fields[] es:
			<nombre columna>|<etiqueta>|<Subconsulta>
		<Subconsulta> es la consulta SQL que devuelve los valores que puede
		tomar el campo. La consulta debe devolver una lista de valores, de la 
		forma:	
			<valor>
			<valor>
			<valor>
		O también de la forma:
			<valor>-<etiqueta>
			<valor>-<etiqueta>
			<valor>-<etiqueta>
		Ejemplos de subconsultas son:
			select idInstitucion from instituciones
			select concat('''',idPerfil,'''-',idPerfil) from perfiles
		El campo <valor> se usará para construir la sentencia INSERT cuando
		se agregue el registro. Si los valores a insertar son cadena, se deben 
		encerrar entre apóstrofos.
	*/
	global $dbConex;
	//Lee información de la tabla en el arreglo $cols()
	$cols = array();  //Inicia arreglo
	$q = mysqli_query($dbConex, "DESCRIBE $table");
	while($row = mysqli_fetch_array($q)) {
		$cols[] = $row;  //Acumula en el arreglo.
	}
	echo '<form class="form_insert" action="'.$hins.'" method="post" >';
	//Genera controles
	if ($fields == []) { //Para todas las columnas de la tabla.
		$ids = [];   //Inicia arreglo
		foreach ($cols as $row) {
			_decode_row($row, $name, $type_nam, $type_arg, $default,  $extra, $null);
			_gen_control_columns($cols, $name, $name, '', '', $in_id);
			$ids[] =  $in_id;  //Devuelve índice 
		}
	} else {  //Para las columnas indicadas
		/* Se espera que el formato sea: 
			<nombre columna>|<etiqueta>|<subquery> 
		*/
		$ids = [];   //Inicia arreglo
		foreach($fields as $item) {  //Explora las columna indicadas
			$tmp = trim($item);  
			//Extrae campos
			$a = explode('|', $tmp);
			$name = trim($a[0]);  //Nombre de columna 
			if (count($a)>1) $etiq=trim($a[1]); //Etiqueta 
			else $etiq=$name; 
			if (count($a)>2) $subquery=trim($a[2]); //Subquery
			else $subquery='';
			//if (count($a)>2) $obli = '*'; else $obli = '';
			_gen_control_columns($cols, $name, $etiq, $subquery, '', $in_id);
			$ids[] =  $in_id;  //Devuelve índice 
		}
	}
	button_submit($msj_agre);
	echo '</form>';
	/*Genera código javascript para validación de datos obligatorios.
	La función devuelve TRUE si el campo no es válido. */
	JSaddCode('function valid(id) {'.
		//'console.log("aaa:"+$(id).val());'.
		'var l=$(id).parent().parent().find(".msg");'.
		'if ($(id).val()=="") {'.
		'	l.text("Ingresar valor.");'.
		'	return true;'.
		'} else {'.
		'	l.text("");'.
		'	return false;'.
		'}
	};');
	/*Código para temporizar.*/
	$js = '';
	foreach ($ids as $campo) {  //Código para llamar a las rutinas de verifiación de llenado de campos obligatorios.
		$a = explode('-',$campo);
		//$campo_tip = $a[1];
		if ($a[2]=='0') continue; //Campo que puede ser NULL.
		if ($a[3]=='1') continue; //Campo Auto_increment
		$js.='if (valid("#'.$campo.'")) f=true;';  //Llamada a validación
		//echo '<br>---'.$campo;
	}
	JSaddCode('$(document).ready(function() {'.
		//primer refresco 
		'refrescar();'.
		//Configura temporizador
		'var temporizador=setInterval(function(){refrescar()}, 1000);'. 
		//Funciones dentro del bloque ready().
		'function refrescar(){'.
		'	var f=false;'.
		$js.  //Verifica
		//Se activa o desactiva, el botón "Agregar" de acuerdo
		'	if (f) {'.  //Falta completar
		'		$(":submit").prop("disabled", true);'.
		'		$(":submit").attr("class", "btn btn-disabled");'.
		'	} else {'.
		'		$(":submit").prop("disabled", false);'.
		'		$(":submit").attr("class", "btn btn-primary");'.
		'	}'.
		'}
	  });
	');		
}
function form_update($table, $fields, $hupd, $msj_agre, $cond_reg){
	/* Genera HTML de un formulario para editar registros de una 
	  tabla. Parámetros:
		$table  -> Tabla a editar.
		$fields -> Arreglo de campos que se desean editar. Debe tener la forma: 
			       $fields = ['idReg|ID','Nombre', 'direccion|Dirección'];
		$hupd   -> Enlace a donde se envía con el botón "Grabar".
		$cond   -> Condición de la consulta que devolverá un registro. Se espera
					que sea de la forma: "ID = 12345"
		El formato de los ítems de $fields[] es:
			<nombre columna>|<etiqueta>|<Subconsulta>
		<Subconsulta> es la consulta SQL que devuelve los valores que puede
		tomar el campo. La consulta debe devolver una lista de valores, de la 
		forma:	
			<valor>
			<valor>
			<valor>
		O también de la forma:
			<valor>-<etiqueta>
			<valor>-<etiqueta>
			<valor>-<etiqueta>
		Ejemplos de subconsultas son:
			select idInstitucion from instituciones
			select concat('''',idPerfil,'''-',idPerfil) from perfiles
		El campo <valor> se usará para construir la sentencia INSERT cuando
		se agregue el registro. Si los valores a insertar son cadena, se deben 
		encerrar entre apóstrofos.
	*/
	global $dbConex;
	//Lee información de la tabla en el arreglo $cols()
	$cols = array();  //Inicia arreglo
	$q = mysqli_query($dbConex, "DESCRIBE $table");
	while($row = mysqli_fetch_array($q)) {
		$cols[] = $row; //Acumula en el arreglo.
	}
	//Consulta para acceder al registro a editar
	$q = mysqli_query($dbConex, "select * from $table where $cond_reg");
	if ($row = mysqli_fetch_array($q)) {
		//En $row[] tenemos el resultado.
		echo '<form class="form_insert" action="'.$hupd.'" method="post" >';
		$ids = [];   //Inicia arreglo
		foreach($fields as $item) {  //Explora las columna indicadas
			/* Se espera que el formato de $item sea:
				<nombre columna>|<etiqueta>|<subconsulta> 
			*/
			$tmp = trim($item);  
			//Extrae campos
			$a = explode('|', $tmp);
			$name = trim($a[0]);  //Nombre de columna 
			if (count($a)>1) $etiq=trim($a[1]); //Etiqueta 
			else $etiq=$name; 
			if (count($a)>2) $subquery=trim($a[2]); //Subquery
			else $subquery='';
			$valor = $row[$name];
			_gen_control_columns($cols, $name, $etiq, $subquery, $valor, $in_id);
			$ids[] =  $in_id;  //Devuelve índice 
		}

		button_submit($msj_agre);
		echo '</form>';
	} else {
		//No hay resultados.
	}
	/*Genera código javascript para validación de datos obligatorios.
	La función devuelve TRUE si el campo no es válido. */

	JSaddCode('function valid(id) {'.
		//'console.log("aaa:"+$(id).val());'.
		'var l=$(id).parent().parent().find(".msg");'.
		'if ($(id).val()=="") {'.
		'	l.text("Ingresar valor.");'.
		'	return true;'.
		'} else {'.
		'	l.text("");'.
		'	return false;'.
		'}
	};');
	// Código para temporizar
	$js = '';
	foreach ($ids as $campo) {  //Código para llamar a las rutinas de verifiación de llenado de campos obligatorios.
		$a = explode('-',$campo);
		//$campo_tip = $a[1];
		if ($a[2]=='0') continue; //Campo que puede ser NULL.
		if ($a[3]=='1') continue; //Campo Auto_increment
		$js.='if (valid("#'.$campo.'")) f=true;';  //Llamada a validación
		//echo '<br>---'.$campo;
	}
	JSaddCode('$(document).ready(function() {'.
		//primer refresco 
		'refrescar();'.
		//Configura temporizador
		'var temporizador=setInterval(function(){refrescar()}, 1000);'. 
		//Funciones dentro del bloque ready().
		'function refrescar(){'.
		'	var f=false;'.
		$js.  //Verifica
		//Se activa o desactiva, el botón "Agregar" de acuerdo
		'	if (f) {'.  //Falta completar
		'		$(":submit").prop("disabled", true);'.
		'		$(":submit").attr("class", "btn btn-disabled");'.
		'	} else {'.
		'		$(":submit").prop("disabled", false);'.
		'		$(":submit").attr("class", "btn btn-primary");'.
		'	}'.
		'}
	  });
	');
}
///////////////// Rutinas back-end ///////////////
function redirect($modo, $url_destino, $error='') {
	/* Genera código de salida del script PHP, con los parámetros
	indicados. */
	$_SESSION['mode'] = $modo;
	if ($error=='') {
		header("location:".$url_destino);
	} else {
		header("location:".$url_destino."?e=".$error);
	}
	return 0;   //Por si se necesita usarlo como función.
}
function _decodCampoPOST($campo, &$valor, &$campo_nom) {
	/* Obtiene del valor de un control que viene por POST[] de
	un formulario creado con form_insert(). 
	El valor de $campo tiene la forma: 
		<nombre de columna>-<tipo>-<obligatoriedad>-<auto_increment>
	*/
	$a = explode('-',$campo);
	//echo $campo.':'.$valor.'<br>';	
	$campo_nom = $a[0];
	$campo_tip = $a[1];
	//Conforma los valores de acuerdo al tipo de dato
	//Las cadenas se completan con comillas para el INSERT
	if ($campo_tip=='varchar') $valor="'".$valor."'";
	if ($campo_tip=='char')    $valor="'".$valor."'";
	//Los campos TINYINT se consdieran como boolean							  
	if ($campo_tip=='tinyint') {
		if ($valor=='on') $valor=1; else $valor=0;
	}
	/*Los tipos enumerados no se completan. Se insertarán como 
	vengan, con comillas o sin ellas.
	if ($campo_tip=='enum') */
	//Los valores vacíos se consideran como NULL.
	if ($valor=='') $valor='NULL';
}
function get_SQL_insert($table) {
	/* Genera el código SQL de una sentencia INSERT a partir
	de los parámetros POST generados por la función: 
	form_insert() */
	$campos = '';
	$valores = '';
	foreach($_POST as $campo => $valor){
		if ($campos!='') {$campos.=','; $valores.=',';}
		_decodCampoPOST($campo, $valor, $campo_nom);
		//Acumula
		$campos  .= $campo_nom;
		$valores .= $valor;
	}
	return "INSERT INTO $table($campos) VALUES ($valores)";
}
function get_SQL_update($table, $cond_reg) {
	/* Genera el código SQL de una sentencia UPDATE a partir
	de los parámetros POST generados por la función:
	form_update() */
	$campos = '';
	foreach($_POST as $campo => $valor){
		if ($campos!='') {$campos.=','; }
		_decodCampoPOST($campo, $valor, $campo_nom);
		//Acumula
		$campos  .= $campo_nom.'='.$valor;
	}
	return "UPDATE $table SET $campos WHERE $cond_reg";
}
function read_col_POST($col_name) {
	/* Explora todos los valores _POST[] que vienen de un formulario
	para buscar uno en particular. Si lo encuentra devuelve el valor.
	Si no lo encuentra, devuelve cadena vacía. */
	foreach($_POST as $campo => $valor){
		$a = explode('-',$campo);
		//Aprovecha para obtener Path
		if ($a[0] == $col_name) return $valor;
	}
	return '';
}
?>