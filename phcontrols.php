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
 - Los campos que almacenen cadenas de varias líneas deben tener
   el tipo TEXT, para que se representen como cuadro de texto de 
   Varias líneas.

				Por Tito Hinostroza 2020 - Derechos Reservados
*/
////////// Íconos usados
	$ICO_TRASH  = '<img src="'.__DIR__.'/bin.png" alt="trash icon">';
	$ICO_UPDATE = '<img src="'.__DIR__.'/spinner11.png" alt="update icon">';
////////// Variables globales de base de datos //////////////
	//Contador para generar IDs únicos
	$id_cnt = 0;
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
function valid_identifier($identif) {
	/*Valida que la cadena $identif, tenga caracteres válidos para un 
	identificador. Si no cumple, devuelve FALSE.*/
    return !preg_match('/[^A-Za-z0-9.$]/', $identif);
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
function DB_open() {
	/*Inicia la conexión a la base de datos. Actualiza la variable global $dbConex.*/
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;
	global $dbConex;
	$dbConex=mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
	mysqli_set_charset($dbConex, "UTF8");
};
function DB_exec($sql) {
	/*Ejecuta una consulta. Actualiza las variables globales $dbResult, $dbRowNum y $dbError.
	 Si la consulta se ejecuta exitosamente:
	 - Devuelve el resultado en: $dbResult. 
	 - Devuelve el número de filas en $dbRowNum.
	 - Devuelve TRUE.
	 Si la consulta se ejecuta con error:
	 - Devuelve mensaje de error en $dbError.
	 - Devuelve FALSE.
	 - Libera el objeto $dbConex.
	*/
	global $dbConex;
	global $dbResult;
	global $dbRowNum;
	global $dbError;
	$dbResult=mysqli_query($dbConex, $sql);
	if($dbResult === FALSE) {
		$dbError = mysqli_error($dbConex);
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
function DB_exee($sql) {
	/*Similar a DB_exec(), pero cuando hay error Genera mensaje de error.

	La forma usual de uso es:
	if (!DB_exee('...')) exit;   //sale generando error, si se produce.
	*/
	global $dbError;
	$result = DB_exec($sql);
	if (!$result) jumbotron('Error!!!'.'We have problems: '.$dbError);
	return $result;
}
function DB_read($sql) {
	/* Ejecuta una consulta y devuelve el primer registro del resultado. */
	global $dbError, $dbConex;
	$dbResult = mysqli_query($dbConex, $sql);
	if ($dbResult == FALSE) {
		jumbotron('Error!!!'.'We have problems: '.$dbError);
	} else {
		return mysqli_fetch_assoc($dbResult);
	}
}
function DB_close() {
	/* Cierra la conexión a la base de datos y libera el objeto $dbResult, si es que se 
	hubiera usado */
	global $dbConex;
	global $dbResult;
    //if ($dbResult!=NULL) mysqli_free_result($dbResult);
	if ($dbConex!=NULL) mysqli_close($dbConex);
}
function DB_table_exist(string $tab_name): bool {
	global $dbConex;
	if ( mysqli_query($dbConex, "DESCRIBE $tab_name" ) ) {
	  return true;
	} else {
	  return false;
	}
	
}
//////////////// Mensajes ///////////////////////////////
function alert($msg, $class) {
	/* Genera HTML para un mensaje con botón de cerrar. */
	global $id_cnt;
	$id_cnt++;
	$id_alert = 'ale'.$id_cnt;  //Genera ID diferente.
	echo '<div class="alert '.$class.'" id="'.$id_alert.'">';
	echo  '<div>'.$msg.'</div>';
	echo  '<a onclick="alert_close(\''.$id_alert.'\')" class="but_close" href="#"></a>';
	echo '</div>';
	//Función para desaparecer alerta
	JSaddFunction('alert_close',"function alert_close(id){
        $('#'+id).fadeOut('fast'); }; ");
}
function alert_success($msg, $class='') {
	/* Genera HTML para un mensaje de advertencia. */
	alert($msg, 'alert-success '.$class);
}
function alert_warning($msg, $class='') {
	/* Genera HTML para un mensaje de advertencia. */
	alert($msg, 'alert-warning '.$class);
}
function alert_danger($msg, $class='') {
	/* Genera HTML para un mensaje de error. */
	alert($msg, 'alert-danger '.$class);
}
function jumbotron($title, $butlink='', $buttxt='Go back &raquo;') {
	/* Muestra un mensaje con letras grandes que llena toda el área disponible.
	Se incluye también un botón para ir a una página específica.
	Considerar que para que se muestre en el formato correcto, se debe haber cargado 
	la hoja de estilos "phcontrols.css".
	*/
	echo '<br>';	
	echo '  <div class="jumbotron">';
	echo '    <h1>'.$title.'</h1>';
	//echo '    <p>'.$txt.'</p>';
	echo '    <p> ';
	if ($butlink!='') {
		echo '<a class="btn btn-lg btn-primary" href="'.$butlink.'" role="button">';
		echo   $buttxt;
		echo '</a>';
	}
	echo '    </p>';
	echo '  </div>';
}
//////////////// Controls //////////////////////////////
function label($caption, $for) {
	/* Crea un control label, con la etiqueta "$caption". El parámetro $for 
	es para asociar la etiqueta a un control. */
	echo '  <label class="label" for="'.$for.'">'.$caption.'</label><br>';
}
function editbox($id, $default, $disabled) {
	/* Control edit. Genera un cuadro de texto. El parámetro $id se usa como 
	atributo "name" e "$id". */
	echo '<input type="text" class="form-control" 
			  name="'.$id.'" value="'.$default.'" ';
	if ($disabled) echo ' disabled ';
	echo '    id="'.$id.'">';
}
function passbox($id, $default, $disabled) {
	/* Control edit para contraseñas. Genera un cuadro de texto. El parámetro 
	$id se usa como atributo "name" e "$id". */
	echo '<input type="password" class="form-control" 
			  name="'.$id.'" value="'.$default.'" ';
	if ($disabled) echo ' disabled ';
	echo '    id="'.$id.'">';
}
function textbox($name, $default, $nrows, $disabled) {
	/* Control edit. Genera un cuadro de texto de varias líneas.*/
	echo '<textarea class="form-control" 
			name="'.$name.'" rows="'.$nrows.'" ';
	if ($disabled) echo ' disabled ';
	echo '    id="'.$name.'">'. $default;
	echo '</textarea>';
}
function listbox($name, array $items, $default, $disabled) {
	/* Control listbox. Genera una lista desplegable. Los valores de la lista
	se obtienen del $items El parámetro $default es el valor que se 
	seleccionará de la lista. Debe existir en $items (en el atributo value, no
	en la etiqueta) o no se seleccionará. 
	Si $default no existe en $items, devolverá FALSE. */
	echo '    <select class="form-control" ';
	echo '    name="'.$name.'" ';
	if ($disabled)	echo ' disabled ';
	echo '    id="'.$name.'">';
	$selected = false;  
	foreach ($items as $txt) {
		$a = explode("\t", $txt);
		$value = $a[0];  //Valor.
		$label = (count($a)>1)?$a[1]:$value;  //Etiqueta
		removeQuotes($value); //Siempre se quita comillas
		removeQuotes($label); //Siempre se quita comillas
		echo '  <option value="'.$value.'" ';
		if ($value == $default) {
			echo ' selected ';
			$selected = true;
		}
		echo ' >'.$label.'</option>';
	}
    echo '    </select>';
	return $selected;
}
function abutton($caption, $action, $style="btn-primary") {
	/* Inserta un botón de estilo indicado en $style, con una acción 
	definida en el evento onclick="".
	Ejemplo de uso: abutton('Grabar',"alert('aaa')"); */
	echo '<div class="btn '.$style.'"';
	if ($action!='') echo ' onclick="'.$action.'" ';
	echo '>';
	echo '<span>'.$caption.'</span>';
	echo '</div>';
}
function hbutton($caption, $href, $style="btn-primary") {
	/* Inserta un botón de estilo indicado en $style, con un enlace
	o URL asociado. */
	echo '<div class="btn '.$style.'" >';
	echo '<span><a href="'.$href.'">'.$caption.'</a></span>';
	echo '</div>';
}
function abutton_add($caption, $action) { // Botón con ícono "+".
	abutton('✚&nbsp;&nbsp;'.$caption, $action);
}
function hbutton_add($caption, $href) { // Botón con ícono "+".
	hbutton('✚&nbsp;&nbsp;'.$caption, $href);
}
function abutton_save($caption, $action) { // Botón con ícono de diskette.
	abutton('&#x1f5ab;&nbsp;'.$caption, $action);
}
function button_submit($caption, $class='btn-primary') {  //Botón para enviar datos de un formulario
	echo '<input type="submit" class="btn '.$class.'"';
	echo '  value="'.$caption.'">';
}
function link_inline($caption, $href) {  
	//Crea un enlace con el estilo de cambio de color 
	echo '<span class="link_inline">';
	echo '<a href="'.$href.'">'.$caption.'</a>';
	echo '</span>';
}
function link_block($caption, $href) {  
	//Crea un enlace con el estilo de cambio de color 
	echo '<div class="link_block">';
	echo '<a href="'.$href.'">'.$caption.'</a>';
	echo '</div>';
}
function form_post($action, $class='') {
	echo '<form class="'.$class.'" action="'.$action.'" method="post">';
}
function end_form_post() {
	echo '</form>';
}
//Controles para formularios de tablas de Base de datos.
function control_edit($caption, $field_name, $default, $class='') {
	/* Inserta control de edición para texto. El control tendrá la forma: 
	 <caption> <control de edición> 
	 El parámetro $field_name, se escribirá como atributo "name" e "id" del
	 control <input>. */
	if ( substr($caption, -1)=='*' ) {$caption = substr($caption, 0, -1).'<strong>&nbsp;*</strong>';}
	$disabled = ($class=='cnt-disabled')? true : false;
	echo '<div class="control-field '.$class.'">';
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';  //Campo para el control en sí
	editbox($field_name, $default, $disabled);	
	echo '  </div>';
	//Campo para el mensaje de error	
	echo '  <div class="msg">';
	echo '  </div>';
	echo '</div>'."\n";
}
function control_text($caption, $field_name, $default, $class='') {
	/* Inserta control de edición para texto de varias líneas. El control 
	tendrá la forma: 
	 <caption> <control de edición> 
	 El parámetro $field_name, se escribirá como atributo "name" e "id" del
	 control <input>. */
	if ( substr($caption, -1)=='*' ) {$caption = substr($caption, 0, -1).'<strong>&nbsp;*</strong>';}
	$disabled = ($class=='cnt-disabled')? true : false;
	echo '<div class="control-field '.$class.'">';
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';  //Campo para el control en sí
	textbox($field_name, $default, 5, $disabled);	
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
	$disabled = ($class=='cnt-disabled')? true : false;
	echo '<div class="control-field '.$class.'">';
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';
	passbox($field_name, $default, $disabled);
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
function control_date($caption, $field_name, $default, $class='') {
	/* Inserta control de edición para campo de tipo fecha. El control tendrá la forma: 
	 <caption> <control de edición> 
	 El parámetro $field_name, se escribirá como atributo "name" e "id" del
	 control <input>. */
	if ( substr($caption, -1)=='*' ) {$caption = substr($caption, 0, -1).'<strong>&nbsp;*</strong>';}
	echo '<div class="control-field '.$class.'">';
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';
	echo '    <input type="date" class="form-control" ';
	echo '    name="'.$field_name.'" value="'.$default.'" ';
	if ($class=='cnt-disabled')	echo ' disabled ';
	echo '    id="'.$field_name.'"  min="2019-01-01" max="2021-12-31">';
	echo '  </div>';
	//Campo para el mensaje de error
	echo '  <div class="msg">';
	echo '  </div>';
	echo '</div>'."\n";
}
function control_time($caption, $field_name, $default, $class='') {
	/* Inserta control de edición para campo de tipo fecha. El control tendrá la forma: 
	 <caption> <control de edición> 
	 El parámetro $field_name, se escribirá como atributo "name" e "id" del
	 control <input>. */
	if ( substr($caption, -1)=='*' ) {$caption = substr($caption, 0, -1).'<strong>&nbsp;*</strong>';}
	echo '<div class="control-field '.$class.'">';
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';
	echo '    <input type="time" class="form-control" ';
	echo '    name="'.$field_name.'" value="'.$default.'" ';
	if ($class=='cnt-disabled')	echo ' disabled ';
	echo '    id="'.$field_name.'" >';
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
	/*Control oculto adicional para que el requerimiento POST envíe información
	 cuando el checkbox siguiente esté en falso (De otra forma no se envía por POST).*/
	echo '  <input type="hidden" value="0" name="'.$field_name.'">';
	//Control "checkbox" principal.
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
	 Parámetros: 
	 $field_name-> Es el valor que se utilizará para los atributos "name" e 
	 			"id" del control <select>. 
	 $items 	-> Es un arreglo de cadenas, usados para llenar la lista. Cada
				 elemento del arreglo puede ser una sola cadena (que se usará 
				 como etiqueta o valor del <SELECT>) o puede ser un par de 
				 valores, la forma:  <valor>\t<etiqueta>
	 $class		-> Nombre de clase que se usará para el contenedor principal.
				Puede tener cualquier valor, pero cuando se indica la clase
				'cnt-disabled', se genera el control deshabilitado.
	*/
	if ( substr($caption, -1)=='*' ) {$caption = substr($caption, 0, -1).'<strong>&nbsp;*</strong>';}
	$disabled = ($class=='cnt-disabled')? true : false;
	echo '<div class="control-field '.$class.'">';
	echo '  <label class="label" for="'.$field_name.'">'.$caption.'</label><br>';
	// Control
	echo '  <div class="control">';
	listbox($field_name, $items, $default, $disabled);
	echo '  </div>';
	//Campo para el mensaje de error
	echo '  <div class="msg">';
	echo '  </div>';
	echo '</div>';
}
///////////////////// Bloques /////////////////////
function startBlock($title, $title_buttons=[], $class='') {
	/* Genera el HTML para crear el inicio de un bloque rectangular.
	  El bloque debe cerrarse con endBlock(). 
	  Parámetros:
		$title        -> Título en la barra de título del bloque (En la parte 
						superior).
	    $title_buttons-> Arreglo con información de botones. Tiene la forma:
	    				$buttons = array( '+'=>'/index.php',
	  									'texto'=>'/otro.php');
						Es decir que cada elemento del arreglo incluye un texto
						(etiqueta o html del botón) y una URL. Tanto el texto
						como la URL puede incluir la variable {idblk}, que será
						reemplazada por el ID del bloque. Si la URL es nula, no
						se genera la etiqueta <a href="..." >
		$class        -> Clase que se usará para identificar al bloque 
						principal, adicionalmente a la clase "panel_block".

	  La función retorna el ID del contenedor div del bloque.
	  Cada llamada a la función genera un ID diferente, de la 
	  forma: block1, block2.
	*/
	global $id_cnt;
	$id_cnt++;
	$idblock = 'block'.$id_cnt;
	echo '<div class="panel_block '.$class.'" id="'.$idblock.'" >';
	echo '  <div class="panel-heading">';
	echo '    <div class="text">'.$title.'</div>';
	echo '    <div class="btns">'; 
	foreach($title_buttons as $key => $val) {
		//print "$key = $val <br>";
		$txt = str_replace('{idblk}', $idblock, $key);
		$href = str_replace('{idblk}', $idblock, $val);
		if ($href=='') {
			echo $txt;
		} else {
			echo '  <a href="'.$href.'">'.$txt.'</a>';
		}
	}
	echo '    </div>';
	echo '  </div>';
	echo '  <div class="panel-body">';
	return $idblock;   //Devuelve ID
}
function endBlock() {
	/* Genera el HTML para crear el inicio de un bloque */
    echo '  </div>';
    echo '</div>';
}
function block_separatorh() {
	echo '<div class="block_seph"></div>';
}
///////////////// Rutinas front-end /////////////////
function _item_bloque($name, $img, $id, $hsel, $draggable=true) {
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
	echo      $name;
	echo '  </span>';
	echo '  </div>';
	echo '</button>';
}
function block_table_icons($title, $icon, $table,
			$col_id, $col_txt, $msj_agre, $hadd, $hsel, $hdel) {
	/* Genera el HTML de una lista de bloques, a partir de registros
	  de una tabla de la base de datos. Se debe haber llamado primero 
	  a DB_open(). 
	  Parámetros:
	    $table -> Nombre de la tabla de la base de datos.
	    $col_id -> Columna de la tabla que se usará como clave primaria.
	    $col_txt -> Columna de la tabla que se usará como etiqueta del ícono.
	    $msj_agre -> Mensaje que aparece en el ícono de "Agregar".
		$hadd -> Enlace a activar, cuando se hace click en el botón "Agregar".
		$hsel -> Enlace a activar, cuando se hace click en el ítem.
		$hdel -> Enlace a activar, cuando se elimina un ítem.
	*/
	global $dbRowNum, $dbResult;
	global $ICO_TRASH, $ICO_UPDATE;
	DB_exec("SELECT * FROM $table ORDER BY $col_id");
	//Convierte ícono en destino "soltable" para el arrastre.
	$ico_drop = str_replace('<img', 
	  '<img ondragover="event.preventDefault()"
	  	ondrop="dropBTI(event)" 
	  ',
	  $ICO_TRASH);
	//Genera encabezado
	$buttons = array($ico_drop => '#');
	startBlock($title, $buttons);
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
	//_item_bloque($msj_agre,'/images/add64.png', '', $hadd, false);
	_item_bloque($msj_agre, __DIR__ .'add64.png', '', $hadd, false);
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
function pagination_links($n_pages, $page, $f_href){
	/* Crea una barra horizontal con enlaces de tipo: 1,2,3 ... usado para
	cambiar la página de algún control que se muestre por páginas. 
	$n_pages->	Número de páginas a mostrar.	
	$page	->	Página actual mostrada.
	$f_href ->	Función que se usará para obtener la URL a poner en el botón.
				Debe tener un parámetro que es donde recibirá el número de
				página.
	*/
	if ($n_pages<=1) return;
	//Hay paginación
	echo '<br>';
	echo '<div class="page_sel">';
	//Botón <<
	if ($page>1) {
		$href = $f_href($page-1); 
		echo '<a class="but" href="'.$href.'"> < </a>';
	} else {
		echo '<a class="but" href="#"> < </a>';
	}
	//Botones centrales
	for($pag = 1; $pag<= $n_pages; $pag++) {  
		if ($pag==$page) {  //Página actual
			$href = $f_href($pag);
			echo '<a class="butsel" href="'.$href.'">'.$pag.' </a>';
		} else {
			$href = $f_href($pag);
			echo '<a class="but" href="'.$href.'">'.$pag.' </a>';
		}
	}
	//Botón >>
	if ($page<$n_pages) {
		$href = $f_href($page+1);
		echo '<a class="but" href="'.$href.'"> > </a>';
	} else {
		echo '<a class="but" href="#"> > </a>';
	}
	echo '</div>';
}
function table_list($fsql, $hidecols, $buttons, $autonum = true, 
					int $page=0, int $page_size=20) {
	/* Genera el HTML de una tabla que representa los registros
	 de una tabla de la base de datos. Se debe haber llamado primero 
	 a DB_open(). 
	 Parámetros:
	 $fsql    -> Consulta a la base de datos para extraer las filas a mostrar.
	 			La consulta debe ser de la forma:
				 SELECT campo1, campo2 FROM tabla WHERE ...;
				También se puede usar: SELECT * FROM tabla ...
				Todos los campos indicados se mostrarán en la tabla HTML.
				Para cambiar el nombre de la columna a mostrar, se puede usar 
				el cambio de nombre mediante SQL:
				 SELECT campo1 as NOMBRE, campo2 as EDAD, ...
	 $hidecols-> Número de columnas de la consulta que se ocultarán. Las 
				columnas a ocultar, serán siempre las primeras. La posibilidad
				de ocultar columnas, permite que se incluyan campos necesarios
				en el SELECT (como el PK de la tabla que se puede necesitar
				para el parámetro $buttons), que no se dessen mostrar en la 
				lista.
	 $buttons -> Arreglo de botones a colocar en la última columna. Debe
				contener cadenas de la forma: 
				 <url_botón>|<icono_botón>|<descripcion_icono>|<msj_confirm>
				El campo <url_botón> puede incluir referencias a campos de la
				consulta, de modo que se personalicen para cada fila. Ejemplo:
	   			 www.sitio.com?command=_del_usu&id={idUsuario}
	 			El campo "msj_confirm" indica que se debe pedir confirmación
	 			antes de pulsar el ícono.
	 $autonum-> Bandera booleana que indica si se incluye una columna con el 
				número de fila mostrada.
	 $page	 -> Número de página a mostrar. Cuando toma un valor mayor a cero,
				se usará paginación, y se mostrarán como máximo, la cantidad de
				filas indicadas en el parámetro $page_size.
	 $page_size->Es el número de filas mostradas como máximo cuando se usa 
				paginación, es decir, cuando $page>0. Cuando $page_size es 
				mayor el el número de filas del resultado, se desactiva la 
				paginación.

	 Devuelve en número de páginas mostradas, cuando se usa paginación.
	*/
	global $dbRowNum, $dbResult, $dbConex;
	if ($result = mysqli_query($dbConex, $fsql)) { // Hay filas de datos.
		if ($page==0) {	//No se aplica paginación
			$page_first_result = 0;
			$n_pages = 1;
		} else { //Se pide paginar
			//Prepara paginación de datos
			$page_first_result = ($page-1) * $page_size;
			$rownum = mysqli_num_rows($result);
			$n_pages = ceil($rownum / $page_size);
			if ($n_pages>1) {  //Hay paginación
				//Nueva consulta con límites.
				$result = mysqli_query($dbConex, $fsql.
				' LIMIT '.$page_first_result.','.$page_size); 
			}
		}
		//Crea tabla
		echo '<table class="table_list">';
		// Lee información de las columnas del resultado.
		$fieldinfo = mysqli_fetch_fields($result);
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
		$nrow = $page_first_result;
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
					$confirm = count($items)>3 && $items[3]!=''? 'onclick="return confirm(\''.$items[3].'\')" ': '';  //Requiere confirmación
					echo '<a href="'.$href.'" '.$confirm.' >';
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
	return $n_pages;
}
function _decode_row($row, &$field, &$type_nam, &$type_arg, &$default, &$extra, &$null) {
	/* Decodifica una fila de DESCRIBE table_name. Función para
	ser usada por únicamente por: form_edit_table() */
	$field   = $row['Field'];
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
function _gen_control($etiq, $list_val, 
	$col_name, $type_nam, $type_arg, $default, $extra, $null) {
	/* Genera el html de un control, de acuerdo a los parámetros
	 indicados:
	  $etiq    -> Título o etiqueta que se colocará delante del control.
	  $list_val-> Lista de valores para generar una lista desplegable. Se espera 
				que la lista sea de la forma: 'a','b','c'
				O de la forma: 1,2,3
				Las comillas no se considerarán y el tipo se los ítems se asumira
				de $type_nam.
				La lista también puede incluir dos campos separador por tabulación:
					<valor1>\t<etiqueta1>,<valor2>\t<etiqueta2>, ...
				Solo se genera la lista cuando contiene texto.
	  $col_name-> Nombre de la columna de la tabla a editar.
	  $type_nam-> Nombre del tipo de dato a usar para el control.
	  $type_arg-> Dato adicional del tipo.
	  $default -> Valor por defecto que toma el control.
	  $extra   -> Parámetros extra de la columna en base de datos.
	  $null    -> Indica si el campo soporta valores nulos.

	 La función devuelve un ID creado para el control, de la forma:

		<nombre de columna>-<tipo>-<obligatoriedad>-<auto_increment>

	 La forma de este ID es necesario para las rutinas que insertan valores en la 
	 tabla (back-end), y para las rutinas de verificación "javascript" (front-end)
	 que verifican si los campos obligatorios son llenados. 
	*/
	//Verifica si debe deshabilitar el control y si debe ponerse el "*" en el campo.
	if ($extra=='auto_increment') {
		//Los campos auto-incrementables, no deben editarse
		$class = 'cnt-disabled';  //Para deshabilitar el control
		$default = '';  //Para que no aparezca ningún valor
	} else {
		$class = '';
		//Verifica si debe agregar el "*" en campos obligatorios.
		if ($null=='NO') {
			$etiq = $etiq.'*';
		}		
	}
	//Construye ID del control: 
	/* Se construye el ID del control, en la forma prevista. Este valor se
	usará luego para los atributos "id" y "name" del control a crear. */
	if ($extra=='auto_increment') $a_inc = '1'; else $a_inc = '0';
	if ($null =='NO') $oblig = '1'; else $oblig = '0';
	$in_id = $col_name.'-'.$type_nam.'-'.$oblig.'-'.$a_inc;  
	//Crea el control de acuerdo al tipo.
	if ($list_val!='') {
		//Caso especial. Se pide crear una lista desplegable.
		$items = explode(',', $list_val);  //Fallará si hay comas dentro de las comillas.
		control_listbox($etiq, $in_id, $items, $default, $class);
	} else {
		//Crea control de acuerdo al tipo de columna.
		switch ($type_nam) {
			case "varchar":
				control_edit($etiq, $in_id, $default, $class);
				break;
			case "text":
				control_text($etiq, $in_id, $default, $class);
				break;
			case "char":
				control_password($etiq, $in_id, $default, $class);
				break;
			case "int":
				control_number($etiq, $in_id, $default, '', $class);
				break;
			case "tinyint":
				control_switch($etiq, $in_id, $default, $class);
				break;
			case "enum":
				/*No se espera usar este caso, porque los enumerados se convierten
				al tipo cadena y se pone la lista de valores en "list_val" */
				$items = explode(',', $type_arg);
				control_listbox($etiq, $in_id, $items, $default, $class);
				break;
			case "decimal":
				control_number($etiq, $in_id, $default, '', $class);
				break;
			case "float":
				control_number($etiq, $in_id, $default, 'any', $class);
				break;
			case "date":
				control_date($etiq, $in_id, $default, $class);
				break;
			case "time":
				control_time($etiq, $in_id, $default, $class);
				break;
			default: 
				echo "!!!$etiq - $type_nam - $type_arg <br>";
				//control_edit($etiq, $in_id, $default);
		}
	}
	return $in_id;
}
function _gen_control_columns($cols, $column, $etiq, $subq, $valini, &$in_id, 
				$modo = 'insert') {
	/* Genera un control en HTML para una columna de una tabla 
	 Parámetros:
	 $cols   -> Arreglo con información sobre las columnas de la tabla.
	 $column -> Nombre de la columna de la tabla para la que se creará
				el control. Se ignora la caja.
	 $etiq   -> Etiqueta o título que se pondrá antes del control.
	 $subq   -> Subquery. Consulta que se debe hacer para obtener los valores
				permitidos de este campo.
	 $valini -> Valor inicial del campo. Si es cadena vacía, se ignora.
	 $in_id  -> Devuelve el ID del elemento INPUT o equivalente, que almacena 
				el valor leído.
	 $modo	 -> Modo de trabajo: "insert" o "update". En el modo "insert" se
				ignora el valor $valini.
	*/
	global $dbConex;
	$found = FALSE;
	foreach ($cols as $row) {
		$col_name = $row['Field'];  //Nombre de la columna de la tabla.
		if (strcasecmp($col_name, $column) == 0) {  //Compara
			//Decodifica toda la información de la columna.
			_decode_row($row, $col_name, $type_nam, $type_arg, $default, $extra, $null);
			// Se encontró el campo.
			$found = TRUE;
			$list_val = '';  //Inicializa lista de valores.
			//Verifica valor inicial.
			if ($modo == 'insert') {  //Modo insert
				//Se deja el $default.
			} else {				//Modo Update
				$default = $valini;  //Se fija el valor
			}
			//Genera lista desplegable para los enumerados
			if ($type_nam=='enum') {
				$list_val = $type_arg;  //Para que genere lista.
				$type_nam = 'varchar';  //Todos los enumerados son de tipo cadena en MYSQL
				/* Notar que se está convirtiendo el tipo "enum" a "varchar" para
				que las rutinas de back-end construyan bien el SQL.*/
				//Verifica valor inicial.
				if ($default!='') {  //Hay valor inicial
					removeQuotes($default);  //Quita comillas por si acaso
				}
			}
			//Verifica si el campo requiere consulta a la base de datos.
			if ($subq!='') {  //Hay subconsulta.
				/*Se genera un control de tipo lista desplegable, manteniendo el 
				tipo del campo. */
				$list_val = '';
				$q = mysqli_query($dbConex, $subq);
				while ($val = mysqli_fetch_array($q)) {
					$list_val.= $val[0].",";  
					//$list_val.="'".$val[0]."',";
				}
				if ($list_val!='') {  //Quita coma final
					$list_val = substr($list_val, 0, -1);
				}
				//$list_val="'aaa','bbb'";  //Valores 
			}
			//Genera el control
			$in_id = _gen_control($etiq, $list_val, 
				$col_name, $type_nam, $type_arg, $default, $extra, $null);
			break;   //Sale porque ya encontró.
		}
	}
	if (!$found) alert_danger('Column not found: '.$column);
}
function form_insert($table, $fields, $hins, $hret, $msj_agre){
	/* Genera HTML de un formulario para agregar registros a una tabla. Un 
	 formulario, de este tipo, aparece con sus campos en blanco o con los
	 valores por defecto, que se hayan definido en la creación de la tabla.
	 Parámetros:
	 $table  -> Tabla a editar.
	 $fields -> Arreglo de campos que se desean editar. Debe tener la forma:
					$fields = ['idReg|ID','Nombre', 'direccion|Dirección'];
				El formato completo de los ítems de $fields[] es:
					<nombre_colum>|<etiqueta>|<Subconsulta>|<valor_inic>
				El valor <nombre_colum> es el nombre de la columna, de $table,
				que se usará para este campo.
				Si no se indica <etiqueta>, se usará <nombre_colum> en su lugar.
				<Subconsulta> es la consulta SQL que devuelve los valores que 
					puede tomar el campo. La consulta debe devolver una lista de
					valores, de la forma:	
						<valor>
						<valor>
					O también de la forma:
						<valor><tabulación><etiqueta>
						<valor><tabulación><etiqueta>
					Ejemplos de subconsultas son:
						select idInstitucion from instituciones
						select concat(idPerfil,'\t',idPerfil) from perfiles
					El campo <valor> se usará para construir la sentencia INSERT
					cuando se agregue el registro.
				<valor_inic> es el valor inicial que se le asignará al campo 
				cuando se muestre el fomulario. Normalmente no se especificará un
				valor inicial al campo, porque el formulario es para crear un
				nuevo registro de la tabla.
	 $hins   -> Enlace a donde se envía con el botón "Agregar".
	 $hret	->	Enlace a donde se envía con el botón "Volver". Si es una
				cadena nula, no se genera este botón.
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
			_gen_control_columns($cols, $name, $name, '', '', $in_id, 'insert');
			$ids[] =  $in_id;  //Devuelve índice 
		}
	} else {  //Para las columnas indicadas
		/* Se espera que el formato sea: 
			<nombre_colum>|<etiqueta>|<Subconsulta>|<valor inicial>
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
			if (count($a)>3) $valini = trim($a[3]); //Valor inicial
			else $valini = '';  
			if ($valini!='') {  //Caso especial donde se indica un valor inicial
				_gen_control_columns($cols, $name, $etiq, $subquery, $valini, $in_id, 'update');
			} else {  //Caso normal de campo en modo "Insert".
				_gen_control_columns($cols, $name, $etiq, $subquery, '', $in_id, 'insert');
			}
			$ids[] =  $in_id;  //Devuelve índice 
		}
	}
	if ($hret=='') {  //Botón único
		button_submit($msj_agre);
	} else {  //Con botón "Volver"
		echo '<div class="buttons">';
		button_submit($msj_agre);
		hbutton('<< Volver', $hret);
		echo '</div>';
	} 
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
	foreach ($ids as $campo) {  //Código para llamar a las rutinas de verificación de llenado de campos obligatorios.
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
function form_update($table, $fields, $hupd, $hret, $msj_agre, $cond_reg){
	/* Genera HTML de un formulario para editar registros de una 
	 tabla. Parámetros:
	 $table  -> Tabla a editar.
	 $fields -> Arreglo de campos que se desean editar. Debe tener la forma:
				$fields = ['idReg|ID','Nombre', 'direccion|Dirección'];
				El formato de los ítems de $fields[] es:
					<nombre colum>|<etiqueta>|<Subconsulta>
				El valor <nombre_colum> es el nombre de la columna, de $table,
				que se usará para este campo.
				Si no se indica <etiqueta>, se usará <nombre_colum> en su lugar.
				<Subconsulta> es la consulta SQL que devuelve los valores que 
					puede tomar el campo. La consulta debe devolver una lista de
					valores, de la forma:	
						<valor>
						<valor>
					O también de la forma:
						<valor><tabulación><etiqueta>
						<valor><tabulación><etiqueta>
					Ejemplos de subconsultas son:
						select idInstitucion from instituciones
						select concat(idPerfil,'\t',idPerfil) from perfiles
					El campo <valor> se usará para construir la sentencia INSERT
					cuando se agregue el registro.
	$hupd   -> Enlace a donde se envía con el botón "Grabar".
	$hret	-> Enlace a donde se envía con el botón "Volver". Si es una 
				cadena nula, no se genera este botón.
	$cond_reg->Condición de la consulta que devolverá un registro. Se 
				espera que sea de la forma: "ID = 12345"
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
		//En $row[] tenemos el resultado. Solo una fila.
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
			_gen_control_columns($cols, $name, $etiq, $subquery, $valor, $in_id, 'update');
			$ids[] =  $in_id;  //Devuelve índice 
		}

		if ($hret=='') {  //Botón único
			button_submit($msj_agre);
		} else {  //Con botón "Volver"
			echo '<div class="buttons">';
			button_submit($msj_agre);
			hbutton('<< Volver', $hret);
			echo '</div>';
		} 
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
function create_menu($description, $class) {
	/* Genera HTML de un menú, en la forma de <ul> ... </ul> 
	 El parámetro $description tiene la forma de un arreglo de elementos:
		[elemento1, elemento2, ... ]
	 Cada elemento es a la vez un arreglo de  2 o 3 elementos:
		[título, enlace]
		[título, enlace, ícono, submenú]
	 Un ejemplo para un menú sería:
 
		$href_home= ['Inicio', 'www.misitio.com'];
		$href_cur = ['Cursos', 'www.misitio.com/_cur_list'];
		$href_usu = ['Usuarios', 'www.misitio.com/_usu_list'];
		create_menu([$href_home, $href_cur, $href_usu], 'menu');
	*/
	echo '<ul class="'.$class.'">';
	foreach ($description as $menu) {
		echo '<li>';
		echo '  <a href="'.$menu[2].'">';
		if ($menu[1]!='') {
			echo '<img src="'.$menu[1].'" alt="ícono">';
		}
		echo 	  $menu[0];
		echo '  </a>';
		if (count($menu)>3 && count($menu[3])>0 ) {
			create_menu($menu[3],'submenu');
		}
		echo '</li>';
	}
	echo '</ul>';
}
///////////////// Rutinas back-end ///////////////
function _decodCampoPOST($campo, &$valor, &$campo_nom) {
	/* Obtiene del valor de un control que viene por POST[] de
	un formulario creado con form_insert(). 
	El valor de $campo tiene la forma: 
		<nombre de columna>-<tipo>-<obligatoriedad>-<auto_increment>
	Los campos <obligatoriedad> y <auto_increment> no se usan aquí. pero se 
	han incluido en el ID de los controles porque las rutinas Javascript lo 
	necesitan así.
	Los campos de tipo 'auto-increment' no llegan aquí, porque no se envían 
	desde el formulario.
	*/
	$a = explode('-',$campo);
	//echo $campo.':'.$valor.'<br>';	
	$campo_nom = $a[0];
	$campo_tip = $a[1];
	//Conforma los valores de acuerdo al tipo de dato
	//Las cadenas se completan con comillas para el INSERT
	if ($campo_tip=='varchar') $valor="'".$valor."'";
	if ($campo_tip=='char')    $valor="'".$valor."'";
	if ($campo_tip=='text')    $valor="'".$valor."'";
	if ($campo_tip=='date') {
		if ($valor=='') {
			$valor = 'NULL';
		} else {
			$valor="'".$valor."'";
		}
	}
	if ($campo_tip=='time')    $valor="'".$valor."'";
	//Los campos TINYINT se consideran como "boolean".
	if ($campo_tip=='tinyint') {
		if ($valor=='on') $valor=1; else $valor=0;
	}
	/*Los tipos enumerados no llegan a este nivel. Aparecen como campo de tipo
	cadena o de otro tipo.
	if ($campo_tip=='enum') */
	//Las cadenas vacías se consideran como NULL.
	if ($valor==='') $valor='NULL';
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