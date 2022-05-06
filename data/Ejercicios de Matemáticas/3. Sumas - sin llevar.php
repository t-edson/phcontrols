<!DOCTYPE html>
<html lang="en">
<head>
    <!--meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title-->

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bootstrap 5 Side Bar Navigation</title>
    <!-- bootstrap 5 css -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl"
      crossorigin="anonymous"
    />
    <!-- BOX ICONS CSS-->
    <link
      href="https://cdn.jsdelivr.net/npm/boxicons@2.0.5/css/boxicons.min.css"
      rel="stylesheet"
    />
</head>
<style>
  body {
      font-size: 2.5rem;
  }
  td {
      width: 22rem;
      padding: 1rem;
  }
  .side-navbar {
    width: 380px;
    height: 100%;
    position: fixed;
    margin-left: -380px;
    background-color: #100901;
    transition: 0.5s;
  }
  .my-container {
    transition: 0.4s;
  }
  .active-nav {
    margin-left: 0;
  }
  /* for main section */
  .active-cont {
    margin-left: 380px;
  }
  #menu-btn {
    background-color: #100901;
    color: #fff;
    margin-left: -62px;
  }
  .my-container input {
    border-radius: 2rem;
    padding: 2px 20px;
  }
</style>

<body>

    <table>
        <?php
          function poner_suma() {
            echo "<div style='display: flex;'>";
            echo   "<div >";
            echo     "<div style='text-align:right;margin-left:0.3rem;'>".rand(50,500)."</div>"; 
            echo     "<div style='text-align:right;margin-left:0.3rem;'>".rand(50,500)."</div>"; 
            echo     "<div style='border-top: 2px solid black'>&nbsp;</div>"; 
            echo   "</div>"; 
            echo   "<div class='signo'>+</div>"; 
            echo "</div>"; 
          }
          for ($i=0; $i < 5; $i++) {
              echo "<tr>";
              echo   "<td>"; poner_suma();
              echo   "</td>";
              echo   "<td>"; poner_suma();
              echo   "</td>";
              echo   "<td>"; poner_suma();
              echo   "</td>";
              echo "</tr>";
          }
        ?>
        </tr>
    </table>
    <script>
      var menu_btn = document.querySelector("#menu-btn");
      var sidebar = document.querySelector("#sidebar");
      var container = document.querySelector(".my-container");
      menu_btn.addEventListener("click", () => {
        sidebar.classList.toggle("active-nav");
        container.classList.toggle("active-cont");
      });
    </script>    
</body>
</html>