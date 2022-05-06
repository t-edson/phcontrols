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

    <!-- Side-Nav >
    <div
      class="side-navbar active-nav d-flex justify-content-between flex-wrap"
      id="sidebar"
    >
      <ul class="nav flex-column text-white w-100">
        <a href="#" class="nav-link h3 text-white my-2">
          Responsive </br>SideBar Nav
        </a>
        <li href="#" class="nav-link">
          <i class="bx bxs-dashboard"></i>
          <span class="mx-2">Home</span>
        </li>
        <li href="#" class="nav-link">
          <i class="bx bx-user-check"></i>
          <span class="mx-2">Profile</span>
        </li>
      </ul>
      <form action="">
        <input type="text" name="" id="">
      </form>
    </div-->

    <!-- Main Wrapper >
    <div class="p-1 my-container active-cont">
      <nav class="navbar top-navbar bg-light px-5">
        <a class="btn border-0" id="menu-btn"><i class="bx bx-menu"></i></a>
      </nav>
      <h3 class="text-dark p-3">RESPONSIVE SIDEBAR NAV
      </h3>
      <p class="px-3">Responsive navigation sidebar.</p>
    </div-->

    <table>
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