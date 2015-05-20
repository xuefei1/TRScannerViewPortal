<?php

require_once('load.php');
if(session_id() == ''){
    session_start();
}
$logged  = $main->checkLogin('index.php');
$info = null;
if($logged == false){
    //build redirect
    $url = "http".((!empty($SERVER['HTTPS']))? "s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $redirect = str_replace('index.php','login.php',$url);
    header("Location:$redirect?msg=login");
    exit;
}else{
    $cookie = $_COOKIE['loginauth'];
    //grap info from the cookie
    $user = $cookie['user'];
    $authID = $cookie['authID'];
    $sql = "SELECT * FROM ".DB_USER_TABLE." WHERE ".DB_COL_USERLOGIN." = '".$user."'";
    $sql1 = "SELECT * FROM ".DB_PATIENT_TABLE." WHERE ".DB_COL_PATIENT_LOGIN." ='".$user."' LIMIT 1";
    $results = $db->select($sql);
    $info = $db->select($sql1);
    //does the user name exist?
    if(!$results){
        die('Invalid cookie, username does not exist');
    }
    //turn the result into an associative array
    $results = mysql_fetch_assoc( $results );
    $info = mysql_fetch_assoc( $info );
    if($main->isDoctor($info[DB_COL_HEALTHCARE_NO])){
        header("Location:index-doc.php");
    }
    $_SESSION[DB_COL_HEALTHCARE_NO] = $info[DB_COL_HEALTHCARE_NO];
}

?>

<!DOCTYPE html>
<html lang="en" >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width = device-width initial-scale=1.0">
        <title>Welcome</title>
        <script src="../chart/Chart.min.js"></script>
        <script src="../js/chart-config.js" type="text/javascript"></script>
        <script src="../js/line-chart.js" type="text/javascript"></script>
        <script type="text/javascript" src="../highslide/highslide-full.min.js"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="../bootstrap-select/dist/js/bootstrap-select.min.js"></script>
        <script type="text/javascript" src="../js/global.js"></script>
        <script type="text/javascript">
            hs.graphicsDir = '../highslide/graphics/';
            hs.align = 'center';
            hs.transitions = ['expand', 'crossfade'];
            hs.outlineType = 'rounded-white';
            hs.fadeInOut = true;
            hs.addSlideshow({
                interval: 5000,
                repeat: true,
                useControls: true,
                fixedControls: 'fit',
                overlayOptions: {
                    opacity: 0.75,
                    position: 'bottom center',
                    hideOnMouseOut: true
                }
            });
        </script>
        <script src="../galleria/galleria-1.4.2.min.js"></script>
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../css/custom.css" rel="stylesheet">
        <link href="../css/index.css" rel="stylesheet">
        <link href="../highslide/highslide.css" rel="stylesheet">
        <link href="../bootstrap-select/dist/css/bootstrap-select.css" rel="stylesheet">
        <link href="../bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet">
    </head>

    <body onresize="onPageResize()" style="background-color:#f0f4c3;">

        <nav class="navbar navbar-default" style="margin-bottom: 0px;">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.php">Home</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="login.php?action=logout">Log out</a></li>
                    </ul>
                    <ul class="navbar-nav nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Options<span class="caret"></span></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Action</a></li>
                                <li><a href="#">Another action</a></li>
                                <li><a href="#">Something else here</a></li>
                                <li class="divider"></li>
                                <li><a href="#">Separated link</a></li>
                                <li class="divider"></li>
                                <li><a href="#">One more separated link</a></li>
                            </ul>
                        </li>
                    </ul>

                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>

        <div id="main-container">
            <div class="page-cover">
                <div style="width:100%; height:55px; position: absolute; bottom:0; background:rgba(255,255,255,0.25);"></div>
            </div>

            <div id="weather" class="col-md-4 col-sm-12 col-xs-12 col-lg-4" style="position:absolute; top:45px; right:30px; z-index:1;" >
            </div>

            <div class="content-container-bg" >
                <div class="col-md-4 col-sm-12 col-xs-12 col-lg-4 profile-img-div" id="user-profile-img-div">
                    <img src="../res/icon-user-default.png" style="width:216px; height:216px; border:8px solid #FFFFFF;border-radius: 6px;box-shadow: 0px 2px 5px #333333; margin:0 auto; display:inline-block;"/>
                </div>
                <div class="content-container">
                    <div id="left-content-container" class="col-md-4 col-sm-12 col-xs-12 col-lg-4">
                        <div class=" user-info-div">
                            <div style="max-width:280px; margin:0 auto;">
                                <h3 class="text-grey"><?php if(isset($info[DB_COL_NAME])){echo $info[DB_COL_NAME];}else{ echo 'Unknown User';}?></h3>
                                <div style="max-width:280px; overflow:hidden;"><span class="glyphicon glyphicon-map-marker" style="height:20px; width:20px;"></span><h4 class="text-grey" style="display: inline-block;"><?php if(isset($info[DB_COL_CITY]) && isset($info[DB_COL_PROVINCE])){echo $info[DB_COL_CITY] .", ".$info[DB_COL_PROVINCE];}else{echo 'Unknown location';}?></h4></div>
                                <div style="max-width:280px; overflow:hidden;"><span class="glyphicon glyphicon-envelope" style="height:20px; width:20px;"></span><h4 class="text-grey" style="display: inline-block;"><?php if(isset($info[DB_COL_EMAIL])){echo $info[DB_COL_EMAIL];}else{echo 'No Email found';}?></h4></div>
                            </div>
                        </div>
                        <div id="left-content-container left-content-div" style="margin-top:60px; margin-bottom:60px; padding: 0 50px; ">
                            <div class="border-std" style="margin:0 auto; max-height: 640px;">
                                <div class="page-header" style="margin:0 auto; ">
                                    <h4 class="text_grey">Recent Uploads</h4>
                                </div>
                                <div id="overview-div" style="margin: 20px auto; max-height:500px; text-align:center; overflow-y:auto; overflow-x:hidden;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 col-xs-12 col-lg-7" id="right-content-container">
                        <div class="content-btn-bar ">
                            <div class="">
                                <div class="btn-group content-btn-group" role="group" aria-label="">
                                    <button type="button" class="btn btn-default btn-width-quarter" onclick="showActivitySection()">Activity</button>
                                    <button type="button" class="btn btn-default btn-width-quarter" onclick="showDataSection()">Data</button>
                                    <button type="button" class="btn btn-default btn-width-quarter" onclick="showJournalsSection()">Journals</button>
                                    <button type="button" class="btn btn-default btn-width-quarter" onclick="showMoreSection()">More</button>
                                </div>
                            </div>
                        </div>

                        <div id="activity-div" style="display:block">  
                            <div class="border-std" style="margin:40px auto;">
                                <p class="text_notify_grey" style="">No Recent Activity</p>
                            </div>
                        </div>

                        <div id="data-div" style="display:none">

                            <div id="graph-div" class="border-std" style="margin:40px auto;">
                                <div class="page-header" style="margin:0 auto; ">
                                    <h4 class="text_grey">Analysis</h4>
                                </div>
                                <div style="margin: 10px auto;">
                                    <div class="input-group" style="float:left; width:50%; padding:5px;">
                                        <input onchange="onDateChange(this.value)" class="form-control" placeholder="Pick a date" data-provide="datepicker" id="datepicker" >
                                    </div>
                                    <div style="padding: 5px;">
                                        <select  class="selectpicker" onchange="updateGraph(this.value);">
                                            <option value="day">Show data within a Day</option>
                                            <option selected="selected" value="week">Show data within a Week</option>
                                            <option value="month">Show data within a Month</option>
                                            <option value="year">Show data within a Year</option>
                                        </select>
                                    </div>
                                    <script type="text/javascript">
                                        $('.selectpicker').selectpicker({
                                            style: 'btn-default',
                                            width: '50%',

                                        });
                                    </script>
                                </div>

                                <div class="canvas_container">
                                    <div id="container" style="width:auto; height:auto; position:relative"></div>
                                </div>

                                <script>
                                    var name = "<?php if(isset($info[DB_COL_NAME])){echo $info[DB_COL_NAME]; }?>";
                                    function initGraph(type){
                                        currChartType = type;
                                        generateScanData();

                                    }
                                    function onDateChange(newDate){
                                        $('#datepicker').datepicker('hide');
                                        var date = parseTimeInMillis(newDate);
                                        currChartDate = date;
                                        initLineChart(date, currChartData, currChartType, name, 'container', 'Sample Data', 'Scan Count');
                                    }
                                    function updateGraph(type){
                                        currChartType = type;
                                        initLineChart(currChartDate, currChartData, type, name, 'container', 'Sample Data', 'Scan Count');
                                    }
                                    function refreshGraph(){
                                        initLineChart(currChartDate, currChartData,  currChartType, name, 'container', 'Sample Data', 'Scan Count');
                                    }
                                    function generateScanData(){

                                        $.getJSON('main.php', { action: 'generateScanData', healthcare_no: "<?php echo $info[DB_COL_HEALTHCARE_NO]; ?>" }, function(data){
                                            var arr = [];
                                            $.each(data, function(key, val) {
                                                arr[key] = [val, 1];
                                            });
                                            currChartData = arr;
                                            refreshGraph();
                                        });
                                    }


                                </script>
                            </div>
                            <div id="detail-div" style="display:block">
                                <div id="galleria-div" class="border-std">
                                    <div class="page-header" style="margin:0px auto 20px auto;">
                                        <h4 class="text_grey">Pictures</h4>
                                    </div>
                                    <div id="no-image-notify-div">

                                    </div>
                                    <div id="galleria-container">

                                        <?php //$main->showDetailImg($info[DB_COL_HEALTHCARE_NO])?>
                                    </div>

                                    <div id="comment-section-div" style="display:block">
                                        <form id="img_comment_form" onsubmit="return false" method="post">
                                            <div class="input-group" id="comment-box">
                                                <textarea type="text"  placeholder = "Leave a comment" required name="comment" class="form-control form-std"></textarea>
                                                <input type="hidden" name="healthcare_no" value="<?php echo $info[DB_COL_HEALTHCARE_NO]?>"/>  
                                                <input type="hidden" id="imagefile" name="picture_file"/>
                                                <input type="hidden" name="action" value="insert_comment"/>
                                            </div>
                                            <div align="right">
                                                <button type="reset" class="btn btn-default btn_standard">Clear</button>
                                                <button type="submit" id="submit_comment" class="btn btn-default btn_standard">Submit</button> 
                                            </div>
                                        </form>
                                        <div class="single_line_container" style="display:none" id="comment_warning">
                                            <p class="error_msg_1">Failed to load/submit comments due to server errors :(</p>
                                        </div>
                                        <div id="comments_list">
                                            <div class="listview" id="comment-container">
                                                <?php if(isset($_POST['action'])){if($_POST['action'] == 'get_comments'){get_comments();}}?>
                                            </div>
                                            <script type="text/javascript">
                                                $('#img_comment_form').submit(function(event){
                                                    document.getElementById("imagefile").value = +currImageName;

                                                    $.ajax({
                                                        url: 'img-comment.php',
                                                        type: 'post',
                                                        dataType: 'html',   //expect return data as html from server
                                                        data: $('#img_comment_form').serialize(), 
                                                        success: function(response, textStatus, jqXHR){
                                                            if(response == 1){
                                                                document.getElementById("comment_warning").style.display = "block";
                                                                $('#comment_warning').fadeIn(5000, function () {
                                                                    $(this).fadeOut(3000);
                                                                });
                                                            }else{
                                                                refreshCommentSection(response);
                                                            }
                                                        },
                                                        error: function(jqXHR, textStatus, errorThrown){
                                                            console.log('error(s):'+textStatus, errorThrown);
                                                        }
                                                    });

                                                });

                                                function refreshCommentSection(data){
                                                    $('#comments_list').fadeOut(500, function () {
                                                        $(this).html(data);
                                                        $(this).fadeIn(500);
                                                    });
                                                    document.getElementById("img_comment_form").reset();
                                                }
                                            </script>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div id="journals-div" class="border-std" style="margin:40px auto; display:none;">
                            <div class="page-header" style="margin:0 auto; ">
                                <h4 class="text_grey">View Your Journals</h4>
                            </div>
                            <div id="journals_container">
                                <div  style="margin:10px auto; min-height:65px; max-height:2000px; height:auto; overflow-y:auto; overflow-x:hidden;">  
                                    <div id="list-group" class="center_align" >  
                                        <?php $main->showJournals($info[DB_COL_HEALTHCARE_NO]);?>
                                    </div>  
                                </div>
                            </div>
                            <form id="journal_form" onsubmit="return false">
                                <input type="hidden" value="<?php echo $info[DB_COL_HEALTHCARE_NO] ;?>" name="healthcare_no">
                                <input type="hidden" value="submit_journal" name="action">
                                <div class="input-group box_fill">
                                    <input type="text" required name ="title" class="form-control form-std" placeholder="title">
                                </div>
                                <div class="input-group box_fill">
                                    <textarea type="text" rows="10" required name="content" class="form-control form-std"></textarea>
                                </div>
                                <div align="right">
                                    <button type="reset" class="btn btn-default btn_standard">Clear</button>
                                    <button type="submit" id="submit_journal" class="btn btn-default btn_standard">Save</button> 
                                </div>
                            </form>   
                            <script>
                                $('#journal_form').submit(function(event){
                                    $.ajax({
                                        url: 'journal-submit.php',
                                        type: 'post',
                                        dataType: 'html',   //expect return data as html from server
                                        data: $('#journal_form').serialize(),
                                        success: function(response, textStatus, jqXHR){
                                            reloadJournalsDiv();
                                        },
                                        error: function(jqXHR, textStatus, errorThrown){
                                            console.log('error(s):'+textStatus, errorThrown);
                                        }
                                    });
                                });

                                function reloadJournalsDiv(){
                                    $('#journals_container').fadeOut(300, function () {
                                        $('#journals_container').load(document.URL +  ' #journals_container');
                                        $(this).fadeIn(300);
                                    });
                                    document.getElementById("journal_form").reset();
                                }

                                $(document).ready(function() {
                                    $.simpleWeather({
                                        location: 'Edmonton, AB',
                                        woeid: '',
                                        unit: 'c',
                                        success: function(weather) {
                                            html = '<h2><i class="icon-'+weather.code+'"></i> '+weather.temp+'&deg;'+weather.units.temp+'</h2>';
                                            html += '<ul><li>'+weather.city+', '+weather.region+'</li>';
                                            html += '<li class="currently">'+weather.currently+'</li>';
                                            $("#weather").html(html);
                                        },
                                        error: function(error) {
                                            $("#weather").html('<p>'+error+'</p>');
                                        }
                                    });
                                });
                            </script>
                        </div>

                        <div id="more-div" style="display:none;">
                            <div class="border-std" style="margin:40px auto;">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!--footer-->
        <footer class="footer col-md-12 col-sm-12 col-xs-12 col-lg-12" style="margin-top: 80px;">
            <div class="container">
                <p class="text-muted text-center" >&copy;2015 All rights reserved</p>
            </div>
        </footer>

        <!--scripts-->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        <script src="../js/bootbox.min.js"></script>
        <script src="http://code.highcharts.com/highcharts.js"></script>
        <script src="../js/chartUtil.js"></script>
        <script src="../bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="../weather/jquery.simpleWeather.min.js"></script>
        <script type="text/javascript">
            var currPage = 0;
            initPageContent();
            loadUserProfileImg();
            loadRecentUploadedImages();
            function initPageContent(){
                switch(currPage){
                    case 0:
                        showActivitySection();
                        break;
                    case 1:
                        showDataSection();
                        break;
                    case 2:
                        showJournalsSection();
                        break;
                    case 3:
                        showMoreSection();
                        break;
                }
            }

            function showActivitySection(){
                if (document.readyState === "complete") { 
                    document.getElementById('activity-div').style.display = "block";
                    document.getElementById('data-div').style.display = "none";
                    document.getElementById('journals-div').style.display = "none";
                    document.getElementById('more-div').style.display = "none";
                    currPage = 0;
                }
            }

            function showDataSection(){
                if (document.readyState === "complete") { 
                    document.getElementById('activity-div').style.display = "none";
                    document.getElementById('data-div').style.display = "block";
                    document.getElementById('journals-div').style.display = "none";
                    document.getElementById('more-div').style.display = "none";
                    //initGalleria();
                    updateImgDetail("<?php echo $info[DB_COL_HEALTHCARE_NO] ;?>");
                    if(chart == null){
                        initGraph('week');
                    }else{
                        updateGraph(currChartType);
                    }
                    currPage = 1;
                }
            }

            function showJournalsSection(){
                if (document.readyState === "complete") { 
                    document.getElementById('activity-div').style.display = "none";
                    document.getElementById('data-div').style.display = "none";
                    document.getElementById('journals-div').style.display = "block";
                    document.getElementById('more-div').style.display = "none";
                    currPage = 2;
                }
            }

            function showMoreSection(){
                if (document.readyState === "complete") { 
                    document.getElementById('activity-div').style.display = "none";
                    document.getElementById('data-div').style.display = "none";
                    document.getElementById('journals-div').style.display = "none";
                    document.getElementById('more-div').style.display = "block";
                    currPage = 3;
                }
            }

            function onPageResize(){
                switch (currPage){
                    case 0:
                        break;
                    case 1:
                        break;
                    case 2:
                        break;
                    case 3:
                        break;
                }
            }

            function loadUserProfileImg(){
                $("#user-profile-img-div").html("");
                $.post('load-user-profile-img.php', { action: 'load-user-profile', healthcare_no: "<?php $info[DB_COL_HEALTHCARE_NO]; ?>"},
                       function(data){
                    $("#user-profile-img-div").fadeOut(300, function () {
                        $(this).html(data);
                        $(this).fadeIn(300);
                    })
                });
            }

            function loadRecentUploadedImages(){
                $.post('load-user-image.php', {action: 'load-recent-image', healthcare_no: "<?php echo $info[DB_COL_HEALTHCARE_NO]; ?>"},
                       function(data){
                    $("#overview-div").fadeOut(300, function () {
                        $(this).html(data);
                        $(this).fadeIn(300);
                    })
                });
            }
        </script>


        <script>
            function editJournal(element, title, content){
                bootbox.confirm(
                    "<div class=\"page-header\"><h1 style=\"color: #9E9E9E; font-size: 24px; text-align: center;\">Edit Journal</h1></div>                                                                                         <form id=\"infos\" onsubmit=\"return false\">                                                                               <div class=\"input-group  center_align\" >                                                                                <input placeHolder = \"title\" class =\"form-control required form-std\" id = \"new_journal_title\" type='text' name='note_title' style=\"margin:20px auto\" value=\""+title+"\"/>                                                                                                                   <div class=\"input-group box_fill\">                                                                                   <textarea required id = \"new_journal_content\" type='text' name='last_name' rows=\"10\"class=\"form-std box-fill form-control\" name=\"note\">"+content+"</textarea></div></div></form>", 
                    function(result) {
                        if(result){
                            var t = document.getElementById("new_journal_title").value;
                            var c = document.getElementById("new_journal_content").value;
                            if(t == "" || c == ""){
                                bootbox.alert("Update failed, make sure both fields are filled.", function(){
                                });
                                return;
                            }

                            $.post('update-journals.php', { action: 'update', note_id: element.id , note_title: t, note: c},
                                   function(data){
                                if(data){
                                    reloadJournalsDiv();
                                }else{
                                    bootbox.alert("Update failed  due to server error", function(){
                                    });
                                }
                            }
                                  );
                        }
                    }
                );
            }
        </script>
        <script>
            function deleteJournal(element){
                bootbox.confirm("Are you sure you want to delete this journal?", function(result) {
                    if(result){
                        $.post('update-journals.php', { action: 'delete', note_id: element.id },
                               function(data){
                            if(data){
                                reloadJournalsDiv();
                            }else{
                                bootbox.alert("Deletion failed  due to server error", function() {
                                });
                            }
                        }
                              );
                    }
                }); 
            }
        </script>
        <script>
            //problems with the following scripts
            $('document').ready(function(){  
                updatestatus();  
                scrollalert();  
            });  
            function updatestatus(){  
                //Show number of loaded items  
                var totalItems=$('#journals_content p').length;  
                //$('#scrollbox_status').text('Loaded '+totalItems+' Items');  
            }  
            function scrollalert(){  
                var scrolltop=$('#scrollbox').attr('scrollTop');  
                var scrollheight=$('#scrollbox').attr('scrollHeight');  
                var windowheight=$('#scrollbox').attr('clientHeight');  
                var scrolloffset=20;  
                if(scrolltop>=(scrollheight-(windowheight+scrolloffset)))  
                {  
                    //fetch new items  
                    //$('#scrollbox_status').text('Loading...');  
                    $.get('index.php', '', function(newitems){  
                        $('#journals_content').append(newitems);  
                        updatestatus();  
                    });  
                }  
                setTimeout('scrollalert();', 1500);  
            }  
        </script>
        <script>
            var currImageName = null;
            function initGalleria(){
                Galleria.on('image', function(e) {
                    var f = e.imageTarget.src.replace(/^.*[\\\/]/, '').replace(/\.[^/.]+$/,'');
                    currImageName = f;
                    $.post('img-comment.php', { action: 'get_comments', file: f },
                           function(data){
                        $('#comments_list').fadeOut(300, function () {
                            $(this).html(data);
                            $(this).fadeIn(300);
                        });
                    });

                });
                Galleria.loadTheme('../galleria/themes/classic/galleria.classic.min.js');
                Galleria.run('.galleria', {
                    transition: 'fade'
                });

            }
        </script>
        <style>
            .galleria{ width: auto; height: 400px; background: #000 }
        </style>
    </body>
</html>