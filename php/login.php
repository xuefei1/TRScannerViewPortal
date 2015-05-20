<?php
require_once('load.php');
if(session_id() == ''){
    session_start();
}
//action submitted from html doc
if(isset($_GET['action'])){
    if($_GET['action'] == 'logout'){
        $loggedout = $main->logout();

    }
}

$logged = $main->login('index.php');
?>

<!doctype html>
<html class = "full" lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width = device-width initial-scale=1.0">
        <title>Login</title>
        <link href="../jsImgSlider/themes/1/js-image-slider.css" rel="stylesheet" type="text/css" />
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/the-big-picture.css" rel="stylesheet">
        <link href="../css/custom.css" rel="stylesheet">
        <script src="../jsImgSlider/themes/1/js-image-slider.js"  type="text/javascript"></script>
    </head>
    <body>
        <!--navbar-->
        <nav class="navbar-nav narbar navbar-inverse navbar-fixed-bottom" role="navigation">
            <div class="container">
                <a href="login.php" class="navbar-brand">Log in</a>
                <div class="navbar-header navbar-nav nav navbar-right">
                    <li class="dropdown dropdown-right">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Options<span class="caret"></span></a>
                        <ul class="dropdown-menu dropup" role="menu">
                            <li><a href="#">Action</a></li>
                            <li><a href="#">Another action</a></li>
                            <li><a href="#">Something else here</a></li>
                            <li class="divider"></li>
                            <li><a href="#">Separated link</a></li>
                            <li class="divider"></li>
                            <li><a href="#">One more separated link</a></li>
                        </ul>
                    </li>
                </div>
            </div>
        </nav>

        <!--main content-->
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-xs-12 col-md-4 col-lg-4">
                    <div class="account-wall">
                        <form class="form-signin" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <h1 class="text-center login-title">Sign in to access your information</h1>
                            <input type="text" class="form-control" placeholder="Username" name="username" required autofocus>
                            <input type="password" class="form-control" placeholder="Password" name="password" required>
                            <?php if($logged == 'invalid'): ?>
                            <p class="error_msg_0">The username/password combination is not recognized, please try again</p>
                            <?php endif; ?>
                            <?php if($logged == 'loggedin'): ?>
                            <p class="error_msg_0">Another user has already logged in, please log out of that account and try again.</p>
                            <?php endif; ?>
                            <?php if(isset($_GET['action'])): ?>
                            <?php if($_GET['action'] == 'logout'): ?>
                            <?php if($loggedout == true): ?>
                            <p class="notify_msg_0">You have successfully logged out</p>
                            <?php else: ?>

                            <?php endif; ?>
                            <?php endif; ?>
                            <?php endif; ?>
                            <button class="btn btn-lg btn-primary btn-block btn-custom" type="submit">
                                Sign in</button>
                            <label class="checkbox pull-left">
                                <input type="checkbox" value="remember-me">
                                Remember me
                            </label>
                            <a href="#" class="pull-right need-help">Need help? </a><span class="clearfix"></span>


                        </form>
                    </div>
                </div>

                <div class="col-sm-12 col-xs-12 col-md-8 col-lg-8" >
                    <div id="description_container" align="center">

                        <div id="sliderFrame">
                            <div id="slider">
                                <img src="../res/green.jpg"  />
                                <img src="../res/yellow.jpg" />
                                <a href="#"><img src="../res/teal.jpg" /></a>
                                <img src="../res/sample.jpg" />
                                <img src="../res/purple.jpg"  />
                            </div>
                        </div>
                        <div class="company_description">
                            <p class="text_notify_grey" style="margin-top:80px;">Here is the company description</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--footer-->

        <!--scripts-->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            $(window).resize(function(){
                var width = $(".company_description").width();
                document.getElementById("sliderFrame").setAttribute("style","width:"+width+"px");
                document.getElementById("slider").setAttribute("style","width:"+width+"px");
            });
        </script>
    </body>

</html>
