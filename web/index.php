<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
  <div id = "background">
    <div class = "general_container">  

      <h3>Login or register</h3>

      <div>
        <form action="../php-backend/authenticate.php" method="post">     
          Username: <input type="text" id="uname" name="uname" required> <br>
          Password: <input type="password" id="pwd " name="pwd" required> <br>
          <input type="submit" value="Login" class="button" style="margin-left: 0px; width: 100%;">
        </form>

        <form action="registration.php" method="post">     
          <input type="submit" value="Register" class="button" style="margin-left: 0px; width: 100%;">
        </form>
      </div>

      <h1 class = "title" style="margin-top: 80px;">Sports Sphere</h1>
      <h2 class = "subtitle">Fantasy Sports Leagues</h2>

    </div>
  </div>
</body>
</html>