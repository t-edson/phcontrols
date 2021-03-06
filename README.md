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
  * DB_exec($sql)
  * DB_exee($sql)
  * DB_read($sql)
  * DB_close()
  * DB_table_exist(string $tab_name): bool

* Routines to create Messages:
  * alert($msg, $class)
  * alert_success($msg, $class='')
  * alert_warning($msg, $class='')
  * alert_danger($msg, $class='')
  * jumbotron($title, $butlink='', $buttxt='Go back &raquo;')

* Routines to create Controls
  * label($caption, $for)
  * editbox($id, $default, $disabled)
  * passbox($id, $default, $disabled)
  * textbox($name, $default, $nrows, $disabled)
  * listbox($name, array $items, $default, $disabled)
  * abutton($caption, $action, $style="btn-primary")
  * hbutton($caption, $href, $style="btn-primary")
  * abutton_add($caption, $action)
  * hbutton_add($caption, $action)
  * abutton_save($caption, $action)
  * button_submit($caption)
  * link_inline($caption, $href)
  * link_block($caption, $href)
  * form_post($action, $class='')
  * end_form_post()

* Routines to create Controls for Database
  * control_edit($caption, $field_name, $default, $class='')
  * control_text($caption, $field_name, $default, $class='')
  * control_password($caption, $field_name, $default, $class='')
  * control_number($caption, $field_name, $default, $step, $class='')
  * control_date($caption, $field_name, $default, $class='')
  * control_time($caption, $field_name, $default, $class='')
  * control_switch($caption, $field_name, $default, $class='')
  * control_listbox($caption, $field_name, $items, $default, $class='')

* Routines to create Frames
  * startBlock($title, $title_buttons=[], $class='')
  * endBlock()
  * block_separatorh()

* Routines for front-end
  * block_table_icons($title, $icon, $tabla, $col_id, $col_txt, $msj_agre, $hadd, $hsel, $hdel)
  * table_list($fsql, $hidecols, $buttons)
  * form_insert($table, $fields, $hins, $msj_agre)
  * form_update($table, $fields, $hupd, $msj_agre, $cond_reg)
  * create_menu($description, $class)

* Routines for back-end
  * get_SQL_insert($table)
  * get_SQL_update($table, $cond_reg)
  * read_col_POST($col_name)

## Database connection

Only MySQL databases are supported. 

The database can be relational or not. 

Databases must be created according to the following rules:

 - Tables must have a primary key, if the table is going to be edited by the functions of the library.
 - Boolean columns must be represented as Tinyint type, because functions, that edits tables, work in that way.
 - Password columns must be defined as CHAR data types in order to be shown correctly.

# Installation

No special installation is required. The code of the library just includes:

- A PHP file: phcontrols.php
- A SCSS file: phcontrols.scss

SASS is used to create the Style sheet, but a common CSS file is included too.

To use the PHP library, just copy the files in an accesible path and includes the following code in a PHP file:

```
<?php
include 'phcontrols.php';
...

?>
```

To use the style sheet, it can be included in the header of the PHP or HTML file:

```
...
<link rel="stylesheet" href="phcontrols.css">
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

![sample page](https://github.com/t-edson/phcontrols/blob/0.2/_screens/sample1.png?raw=true)

A simple database connnection to show a table list is:

```
<head>
	<link rel="stylesheet" href="phcontrols.css">
</head>
<body>
	<?php
	DB_set_mysql('localhost', 'user', 'pass', 'myDB');
	DB_open();
	$fsql = "SELECT idUsuario, nombres, idPErfil, horarios FROM usuarios";
	table_list($fsql, 0, []);
	DB_close();
	?>	
</body>
```

The output would be:

![sample page](https://github.com/t-edson/phcontrols/blob/0.2/_screens/sample2.png?raw=true)
