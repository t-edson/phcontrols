<?php
include '../globales.php';
session_start();  //Para identificar a la sesión
incHeaderBegin("TodoDocs");?>
<style>
    .aaa td {
        font-size: 2.5rem;
        margin: 0px;
        width: 22rem;
        padding: 1rem;
    }
</style>
<?php
incHeaderEnd("#","#");
iniPanelBegin();
    echo '<form class="form_insert" action="" method="post" >';

    control_number('Num. de sumandos:', 'nsum', '1', 1);

    add_listbox('Núm. de sumandos', 'nsum', 
        ['2 sumandos', '3 sumandos', '2 o 3 sumandos'], '2 sumandos');
    echo '<br>';
    button_crear('Crear', '');

    echo '</form>';
iniPanelEnd();
?>
    <table class="aaa">
        <?php
        for ($i=0; $i < 12; $i++) {
            echo "<tr>";
            if (rand(0,10)>6) {
                echo "<td>".rand(0,7)."+".rand(0,6)."+".rand(0,5)."= ___"."</td>";
            } else {
                echo "<td>".rand(0,10)."+".rand(0,10)."= ___"."</td>";
            }
            echo "<td>".rand(0,10)."+".rand(0,10)."= ___"."</td>";
            echo "</tr>";
        }
        ?>
    </table>
<?php
finPanel();
incFooter()
?>