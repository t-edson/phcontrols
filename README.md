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
  * item_block($name, $img, $id, $action, $hsel, $draggable=true)
  * block_table_icons($title, $icon, $tabla, $col_id, $col_txt, $msj_agre, $hadd, $hsel, $hdel)
  * pagination_links($n_pages, $page, $f_href)
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

The library facilitates the connection to the database and the queries execution through several functions.

First of all, the connection parameters initialization must be done using DB_set_mysql():

```
DB_set_mysql('localhost', 'user', 'password', 'my_database_name');
```

This instruction set some internal variables to allow open the database connection:

```
DB_open();
```

No parameters are needed because, they were set before with DB_set_mysql().

After the database was opened, we can send SQL queries using one of the following functions:

```
function DB_exec($sql)
function DB_exee($sql)
function DB_read($sql)
```

DB_exec() and DB_exee() are used when te result is expected to have many rows. DB_read() is used to retrieve just one row as result.

After calling to DB_exec() and DB_exee(), some variables are initiated:

$dbResult -> The mysqli_result object for the query. Used to iterate.
$dbRowNum -> The number of rows returned for the query.
$dbError  -> The error message text. Empty string if no error was produced.

A sample code to launch a SQL query and iterate the result cpuld be:

```
	DB_set_mysql('localhost', 'user', 'password', 'my_database_name');
	DB_open();
	DB_exec("SELECT FROM table");
	if ($dbError!='') {echo $dbError;}
		//Iterate result
		while ($row = mysqli_fetch_assoc($dbResult)){
			$id = $row['id'];
			//...
	}
```

The result for DB_exec() is false when a error is produced. So we don't need to check $dbError to detect some error, we can use:

```
	if (DB_exec($sql) ) {  //OK

	} else {	//Some problem

	};
```

The function DB_exee() is similar to DB_exec() but, if error is produced, the error message is written to output using "echo" instructions.

Db_read() only returns the first record of the result.

```
	$reg = DB_read("SELECT title, value FROM some_table WHERE id=123"); 
	$title   = $reg['title'];
	$value   = $reg['value'];
```

# Database viewing and editing

phControls makes easy the database table viewing and editing.

Table viewing is done using three main functions:

* table_list($fsql, $hidecols, $buttons, $autonum, $page, $page_size)

* form_insert($table, $fields, $hins, $hret, $msj_agre)

* form_update($table, $fields, $hupd, $hret, $msj_agre, $cond_reg)

In order to have a correct viewing, databases must be created according to the following rules:

 - Tables must have a primary key, if the table is going to be edited by the functions of the library.
 - Boolean columns must be represented as Tinyint type, because functions, that edits tables, work in that way.
 - Password columns must be defined as CHAR data types in order to be shown correctly.
 - Columns for storing multi-line text must be declared as TEXT datatype, in order to be shown as a multiline text input.


# table_list()

Generates the HTML of a table that represents the content of a table in the database or the result of a SQL query. 

DB_open() must have been called first.

A simple way to use is:

```
	$fsql = "SELECT idUsuario, nombres, idPerfil, horarios FROM usuarios";
	table_list($fsql, 0, []);
```

Information is shown in table format:

![sample page](https://github.com/t-edson/phcontrols/blob/0.2/_screens/sample2.png?raw=true)

The definition is: 

table_list($fsql, $hidecols, $buttons, $autonum = true, int $page=0, int $page_size=20)

When pagination is activated, the function returns the number of pages needed to display all the rows of he query. When pagination is disabled, always returns 1.

PARAMETER $fsql

Is the SQL query used to obtain the rows to show. The query must be of the form:
	
	SELECT field1, field2 FROM table WHERE ...
	
You can also use: 

	SELECT * FROM table ...
	
All the indicated fields will be displayed in the HTML table.

To change the name of the column to display, you can use the renaming using SQL:

	SELECT field1 as NAME, field2 as AGE, ...

PARAMETER $hidecols

Number of columns in the query to hide. The columns to hide will always be the first. The possibility of hiding columns allows necessary fields to be included in the SELECT (such as the PK of the table that may be needed for the $buttons parameter), which are not shown in the list.

PARAMETER $buttons 

Array defining buttons to implement actions applied to the rows.

Parameter $buttons define the buttons to be placed in the last column. It's an array containing strings of the form:
				<action_url>|<button_icon>|<icon_description>|<msg_confirm>
				
The field <action_url> can include references to columns of the query, so that they can be  customized for each row. Example:
	   			www.site.com?command=_dosomething&id={id_row_name}

In this case, variable {id_row_name} will be replaced for the column "id_row_name" that should be part of the columns selected in the SQL query. That means it's necessary to include a Primary Key in the query if we are going to include action buttons. If we don't want to show the Primary key in the table, we can put it in the first position and then hide this column.

Teh field "msj_confirm" indicates that confirmation should be requested
before pressing the icon. It's useful when action is going to delete some row of the table.

An example of code, using buttons is:

```
	$fsql = "SELECT idPlan, name, description FROM plan_evaluac";
	$hedi = 'edit_page.php&id={idPlan}|pencil.png|Edit';
	$hdel = 'delete_page.php&id={idPlan}|bin.png|Delete|¿Delete item?';
	table_list($fsql, 0, [$hedi, $hdel]);
```

PARAMETER $autonum

Activate o deactivate the numeration of rows. When activated, a new column is added at the beginning of the table, containing the number of row.

PARAMETER $page

Page number to display. When it takes a value greater than zero, pagination will be used in the table, and a maximum of the number of rows indicated in the $ page_size parameter will be displayed.

PARAMETER $page_size

It is the maximum number of rows displayed when using pagination, that is, when $page>0. When $page_size is greater than the number of rows in the result, pagination is disabled.

When pagination is used, it's possible to show a list of buttons to navigate in the pages of the result. To do this, we use the function: pagination_links().

An example of using table_list() with pagination is:

```
	$page = $_GET['pag'];   //Could be 1, 2 or 3
	$n_pages = table_list($fsql, 0, [], true, $page, 5);
	pagination_links($n_pages, $page, 
		function($p) {return 'index.php&pag='.$p);} );
```

![sample page](https://github.com/t-edson/phcontrols/blob/0.3/_screens/table_pag.png?raw=true)


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
