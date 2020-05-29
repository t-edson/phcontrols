# phcontrols

PHP library to create HTML forms and connection to MySQL database

Phcontrols is a light PHP library aimed to ease the construction of HTML forms interacting with a MySQL database.

## Design

The library is just a set of considerably independent (but related) functions. No OOP paradigm is used.

The front-end is created using some functions of the library. There are just a few visual elements:

- Blocks.- A rectangle and rounded frame.
- Forms.- Allows to edit and create rows on a Database. Includes controls.
- Table.- A Tabular representation of a Database table.
- Controls.- Includes edition controls (INPUT or SELECT) and buttons.

The front-end created includes automatically generated Javascript code. Custom Javascript code can be included too.

It's not needed to work with a database to generate front-end, but some functions require to have a connection.

The library doesn't force to use a special architecture for the application.

## Routines

* Routines to include Javascript code:
  * JSaddCode($code)
  * JSaddFunction($name, $code)
  * JSincludeScript()

* Routines to access MySQL database:
  * DB_set_mysql()
  * DB_open()
  * EjBD($sql)
  * EjecBD($sql)
  * DB_close()

* Routines to create Messages:
  * alert_warning($msg)
  * alert_danger($msg)
  * alert_danger_small($msg)
  * jumbotron($msg, $pagRetorno, $txtBoton)

* Routines to create Controls
  * control_edit($caption, $field_name, $default, $class='')
  * control_password($caption, $field_name, $default, $class='')
  * control_number($caption, $field_name, $default, $step, $class='')
  * control_switch($caption, $field_name, $default, $class='')
  * control_listbox($caption, $field_name, $items, $default, $class='')
  * button_add($caption, $action)
  * button_grab($caption, $action)
  * button_submit($caption)

* Routines to create Frames
  * startBlock($title, $title_buttons=[])
  * endBlock()
  * block_separatorh()

* Routines for front-end
  * FormInicioSesion($institucion, $url_ini_ses, $msg_inf, $hvalidar)
  * block_table_icons($titulo, $icon, $tabla, $col_id, $col_txt, $msj_agre, $hadd, $hsel, $hdel)
  * table_list($fsql, $hidecols, $buttons)
  * form_insert($table, $fields, $hins, $msj_agre)
  * form_update($table, $fields, $hupd, $msj_agre, $cond_reg)

* Routines for back-end
  * redirect($modo, $url_destino, $error='')
  * get_SQL_insert($table)
  * get_SQL_update($table, $cond_reg)
  * read_col_POST($col_name)

## Database connection

Only MySQL databases are supported. 

The database can be relational or not. 

Databases must be created according to the following rules:

 - Tables must have a primary key, if the table is going to be edited by the functions of the library.
 - Boolean columns must be represented as Tinyint type, because functions, that edits tables, work in that way.
 - Boolean columns must have a default value of FALSE to avoid generates NULL values.
 - Password columns must be defined as CHAR data types in order to be shown correctly.

# Installation

No special installation is required. The code of the library just includes:

- A PHP file: phcontrols.php
- A SCSS file: phcontrols.scss

SASS is used to create the Style sheet, but a common CSS file is included too.

To use the PHP library, it's needed to include the code in a PHP file:

```
<?php
include 'phcontrols.php';
...

?>
```

To use the style sheet, it can be included in the header of the PHP or HTML file:

```
...
<link rel="stylesheet" href="'.HWEB.'/phcontrols.css">
...
```


## Sample Code

Hello World page can be created in a file index.php with the following code:

```
<head>
	<link rel="stylesheet" href="phcontrols.css">
</head>
<body>
	<?php
	include 'phcontrols.php';
	startBlock('My Block');
	echo 'Hello world';
	endBlock();
	?>	
</body>
```

The output would be:

![sample page](https://github.com/t-edson/phcontrols/blob/master/sample1.png?raw=true)
