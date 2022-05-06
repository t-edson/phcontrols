<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    body {
        font-size: 2.5rem;
    }
    td {
        width: 22rem;
        padding: 1rem;
    }
</style>
<body>
    <table>
        <?php
            for ($i=0; $i < 12; $i++) {
                echo "<tr>";
                do {$n1 = rand(0,10); $n2 = rand(0,8);} while ($n1 < $n2); 
                echo "<td>".$n1."-".$n2."= __"."</td>";
                do {$n1 = rand(0,10); $n2 = rand(0,7);} while ($n1 < $n2); 
                echo "<td>".$n1."-".$n2."= __"."</td>";
                echo "</tr>";
            }
        ?>
        </tr>
    </table>
</body>
</html>
