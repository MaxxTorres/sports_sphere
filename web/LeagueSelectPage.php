<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>League Select</title>
</head>
<body>
    <div id = "background">
        <div id = "league_select_container" style = "position: relative;">  
            <div class = "container_header" style = "color: white;">
                League Selection 
            </div>  
            <a href = "../php-backend/league_select.php?league_name=NBA%20League">
                <div><p>NBA League</p><img src = "./images/nba_logo.png"></div>
            </a>
            <a href = "../php-backend/league_select.php?league_name=NFL%20League">
                <div><p>NFL League</p><img src = "./images/nfl_logo.png"></div>
            </a>
            <a href = "../php-backend/league_select.php?league_name=MLS%20League">
                <div><p>MLS League</p><img src = "./images/mls_logo.png"></div>
            </a>
        </div>
    </div>
</body>
</html>