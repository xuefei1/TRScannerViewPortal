<?php
require_once('load.php');
$main->register('login.php');
?>

<!doctype html>
<html class = "full" lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width = device-width initial-scale=1.0">
    </head>
    <body>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <style>
                ul{
                    list-style-type: none;
                }
            </style>
            <ul>

                <li>
                    <p>username:</p>
                    <input type="text" method="post" name="userlogin"/>
                </li>

                <li>
                    <p>password:</p>
                    <input type="text" method="post" name="password"/>
                </li>

                <li>
                    <input type="hidden" name="date" id="todayDate"/>
                </li>
                <script>
                    var today = new Date();
                    var dd = today.getDate();
                    var mm = today.getMonth()+1; //January is 0!
                    var yyyy = today.getFullYear();
                    if(dd<10){dd='0'+dd} if(mm<10){mm='0'+mm} today = yyyy+mm+dd;
                    document.getElementById("todayDate").value = today;
                </script>
                <li>
                    <input type="submit">
                </li>
            </ul>
        </form>
    </body>
</html>
