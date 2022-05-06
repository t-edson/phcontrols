<?php
include 'globales.php';

session_start();  //Para identificar a la sesión
incHeader("TodoDocs", "#","#");
?>
  <!--button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal">
    Abrir modal
  </button-->
  <!-- Diálogo Modal -->
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title">Mensaje</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          Usuario o contraseña errónea.
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-warning" data-bs-dismiss="modal">Close</button>
        </div>

      </div>
    </div>
  </div>
  <br>
  <h1 class="text-center">Bienvenido a Escribir123</h1>
  <p>
      Aquí podrá generar, de forma rápida y gratuita, todo tipo de documentos, como cartas, solicitudes, oficios, recibos, etc.
  </p>
  <h3>Empiece ahora mismo</h3>
  <?php 
    function getDirContents($dir, $hpath, &$nid) {
        /* Obtiene una lista anidada que representa a la estructura de directorio que 
        inicia en "$dir".
        $nid -> Contador usado para asignarle un id único a cada ítem de la lista.
        $hpath -> URL base desde donde se crearán los enlaces <a>
        */
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            $hlink = $hpath .  $value;
            if (!is_dir($path)) {  //Archivo
                echo "\n".'<li id="li'.$nid.'">'; $nid++;
                //echo $value;
                echo '<a href="'.$hlink.'">'.$value.'</a>';
                echo "</li>";
            } else if ($value[0] == "_") { //Se asume que son carpetas ocultas
            } else if ($value != "." && $value != "..") {  //Directorio
                echo "\n".'<li id="li'.$nid.'">'; $nid++;
                echo   '<a>'.$value.'</a>';  //En enlace para que sea expandible
                echo   "\n"."<ul>";
                getDirContents($path, $hpath.$value.'/', $nid);
                echo   "</ul>";
                echo "</li>";
            }
        }
    }
    $n = 1;
    echo "<ul id='nav-tree'>";
    getDirContents('data/', 'data/',  $n);
    echo "</ul>";
  ?>
  <!--div class="col-4 mx-auto">
  </div-->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>    
  <script src="_libs/bs5-nav-tree/dist/js/tree.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      window.nav = new NavTree("#nav-tree", {
        searchable: true,
        showEmptyGroups: true,

        groupOpenIconClass: "fas",
        groupOpenIcon: "fa-chevron-down",

        groupCloseIconClass: "fas",
        groupCloseIcon: "fa-chevron-right",

        linkIconClass: "fas",
        linkIcon: "fa-link",

        iconWidth: "25px",

        searchPlaceholderText: "Search",
      });
    });
    <?php if ( isset($_GET['e']) ) { //Error en el intento de inicio de sesión. ?>
      var myModal = new bootstrap.Modal(document.getElementById('myModal'), {})
      myModal.show()    
    <?php } ?>
  </script>

<?php
incFooter()
?>