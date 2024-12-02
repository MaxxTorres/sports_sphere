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

      <h3>User Registration</h3>

      <div>
        <form action="../php-backend/register.php" method="post" autocomplete="off">     
          Full name: <input type="text" id="fullname" name="fullname"> <br>
          email: <input type="text" id="email" name="email" required> <br>
          Preferences: <input type="text" id="prefer" name="prefer"> <br> <br>
          Username: <input autocomplete="off" type="text" id="username" name="username" required> <br>
          Password: <input autocomplete="new-password" type="password" id="pw" name="pw" required=""> <br>
          <input type="submit" value="Submit" class="button" style="margin-left: 0px; width: 100%;">
        </form>
        <form action="index.php" method="post">     
          <input type="submit" value="Back to Login" class="button" style="margin-left: 0px; width: 100%;">
        </form>
      </div>

      <h1 class = "title" style="margin-top: 80px;">Sports Sphere</h1>
      <h2 class = "subtitle">Fantasy Sports Leagues</h2>

    </div>
  </div>
</body>
</html>