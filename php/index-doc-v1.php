<?php
require_once('load.php');

if(session_id() == ''){
    session_start();
}
$patient_info = null;
$logged  = $main->checkLogin('index-doc.php');
$info = null;
if($logged == false){
    //build redirect
    $url = "http".((!empty($SERVER['HTTPS']))? "s":"")."://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $redirect = str_replace('index-doc.php','login.php',$url);
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
    if(!$main->isDoctor($info[DB_COL_HEALTHCARE_NO])){
        header("Location:index.php");
    }
    $_SESSION[DB_COL_HEALTHCARE_NO] = $info[DB_COL_HEALTHCARE_NO];
}
?>

<?php
$journal_submit = $main->saveJournal($info[DB_COL_HEALTHCARE_NO]);
?>

<!DOCTYPE html>
<html lang="en" >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width = device-width initial-scale=1.0">
        <title>Welcome, Doc</title>

        <script src="../chart/Chart.min.js"></script>
        <script src="../js/chart-config.js" type="text/javascript"></script>
        <script src="../js/line-chart.js" type="text/javascript"></script>
        <script type="text/javascript">
            var currPatientID = -1;
            var currPage = 0;
            var imgView = 0;
            function initPageContent(){
                if(currPatientID == -1){
                    document.getElementById('no-selection-notify').style.display = "block";
                    document.getElementById('picturesDiv').style.display = "none";
                    document.getElementById('graphDiv').style.display = "none";
                    document.getElementById('journalDiv').style.display = "none";
                    document.getElementById('moreDiv').style.display = "none";
                    return;
                }
                switch(currPage){
                    case 0:
                        showPictures();
                        break;
                    case 1:
                        showGraph();
                        break;
                    case 2:
                        showJournals();
                        break;
                    case 3:
                        showMore();
                        break;
                }
            }
        </script>
        <script type="text/javascript">
            Chart.defaults.global = {
                // Boolean - Whether to animate the chart
                animation: true,

                // Number - Number of animation steps
                animationSteps: 100,

                // String - Animation easing effect
                animationEasing: "easeOutQuart",

                // Boolean - If we should show the scale at all
                showScale: true,

                // Boolean - If we want to override with a hard coded scale
                scaleOverride: false,

                // ** Required if scaleOverride is true **
                // Number - The number of steps in a hard coded scale
                scaleSteps: null,
                // Number - The value jump in the hard coded scale
                scaleStepWidth: null,
                // Number - The scale starting value
                scaleStartValue: null,

                // String - Colour of the scale line
                scaleLineColor: "rgba(0,0,0,.1)",

                // Number - Pixel width of the scale line
                scaleLineWidth: 1,

                // Boolean - Whether to show labels on the scale
                scaleShowLabels: true,

                // Interpolated JS string - can access value
                scaleLabel: "<%=value%>",

                // Boolean - Whether the scale should stick to integers, not floats even if drawing space is there
                scaleIntegersOnly: true,

                // Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
                scaleBeginAtZero: false,

                // String - Scale label font declaration for the scale label
                scaleFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",

                // Number - Scale label font size in pixels
                scaleFontSize: 14,

                // String - Scale label font weight style
                scaleFontStyle: "normal",

                // String - Scale label font colour
                scaleFontColor: "#666",

                // Boolean - whether or not the chart should be responsive and resize when the browser does.
                responsive: true,

                // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio: true,

                // Boolean - Determines whether to draw tooltips on the canvas or not
                showTooltips: true,

                // Function - Determines whether to execute the customTooltips function instead of drawing the built in tooltips (See [Advanced - External Tooltips](#advanced-usage-custom-tooltips))
                customTooltips: false,

                // Array - Array of string names to attach tooltip events
                tooltipEvents: ["mousemove", "touchstart", "touchmove"],

                // String - Tooltip background colour
                tooltipFillColor: "rgba(0,0,0,0.8)",

                // String - Tooltip label font declaration for the scale label
                tooltipFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",

                // Number - Tooltip label font size in pixels
                tooltipFontSize: 14,

                // String - Tooltip font weight style
                tooltipFontStyle: "normal",

                // String - Tooltip label font colour
                tooltipFontColor: "#fff",

                // String - Tooltip title font declaration for the scale label
                tooltipTitleFontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",

                // Number - Tooltip title font size in pixels
                tooltipTitleFontSize: 14,

                // String - Tooltip title font weight style
                tooltipTitleFontStyle: "bold",

                // String - Tooltip title font colour
                tooltipTitleFontColor: "#fff",

                // Number - pixel width of padding around tooltip text
                tooltipYPadding: 6,

                // Number - pixel width of padding around tooltip text
                tooltipXPadding: 6,

                // Number - Size of the caret on the tooltip
                tooltipCaretSize: 8,

                // Number - Pixel radius of the tooltip border
                tooltipCornerRadius: 6,

                // Number - Pixel offset from point x to tooltip edge
                tooltipXOffset: 10,

                // String - Template string for single tooltips
                tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",

                // String - Template string for multiple tooltips
                multiTooltipTemplate: "<%= value %>",

                // Function - Will fire on animation progression.
                onAnimationProgress: function(){},

                // Function - Will fire on animation completion.
                onAnimationComplete: function(){}
            }

            Chart.defaults.global.responsive = true;
        </script>
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

    <body onresize="onPageResize()" onload="initPageContent()" style="background:url('../res/bg.jpg') no-repeat center center fixed;">
        <nav class="navbar navbar-default">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index-doc.php">Home</a>
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

        <div class=" container main-frame">
            <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                <div class="content-left panel panel-default ">
                    <div style="text-align:center; margin-bottom:20px;">
                        <div id="user-profile-img-div" style=" overflow:hidden; background-size:cover;">
                            <script type="text/javascript">
                                loadUserProfileImg();
                                function loadUserProfileImg(){
                                    $.post('load-user-profile-img.php', { action: 'load-user-profile', healthcare_no: "<?php $info[DB_COL_HEALTHCARE_NO]; ?>"},
                                           function(data){
                                        $("#user-profile-img-div").fadeOut(300, function () {
                                            $(this).html(data);
                                            $(this).fadeIn(300);
                                        })
                                    });
                                }
                            </script>
                        </div>
                        <span class="label label-primary">Edit my profile</span>
                    </div>
                    <div style="text-align:left; margin: 0 auto; width:180px;">
                        <p style="font-size: 15px; color: #8E8E8E;">Name:&nbsp;&nbsp;<?php if(isset($info[DB_COL_NAME])){
    echo $info[DB_COL_NAME]; 
}
                            ?></p>
                        <p style="font-size: 15px; color: #8E8E8E;">Gender:&nbsp;&nbsp;<?php if(isset($info[DB_COL_GENDER])){
                                echo $info[DB_COL_GENDER]; 
                            }
                            ?></p>
                        <p style="font-size: 15px; color: #8E8E8E;">Age:&nbsp;&nbsp;<?php if(isset($info[DB_COL_AGE])){
                                echo $info[DB_COL_AGE]; 
                            }
                            ?></p>
                        <p style="font-size: 15px; color: #8E8E8E;">Email:&nbsp;&nbsp;<?php if(isset($info[DB_COL_EMAIL])){
                                echo $info[DB_COL_EMAIL]; 
                            }
                            ?></p>
                        <p style="font-size: 15px; color: #8E8E8E;">Address:&nbsp;&nbsp;<?php if(isset($info[DB_COL_ADDRESS])){
                                echo $info[DB_COL_ADDRESS]; 
                            }
                            ?></p>
                        <p style="font-size: 15px; color: #8E8E8E;">Phone:&nbsp;&nbsp;<?php if(isset($info[DB_COL_PHONE])){
                                echo $info[DB_COL_PHONE]; 
                            }
                            ?></p>
                    </div>
                </div>

            </div>

            <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">

                <div class="content-info panel panel-default" id="search-patient-div">
                    <div class="panel-body">
                        <div style="width:100%; margin: 0px auto; padding-left:10px;">
                            <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#" style="width:100%;" onclick="onSearchButtonToggle()" id="btn-toggle-search">Hide Search</button>
                        </div>
                        <div id="search-area">
                            <div class="input-group" style="width:100%; padding-left:10px; margin: 20px auto;">
                                <input id="search"  class="form-control" name="search" placeholder="Search by Name" type="text" data-list="#patient-namelist-data" autocomplete="off">
                            </div>
                            <div class="list-group " id="patient-namelist">
                                <ul style="padding-left:10px;" id="patient-namelist-data">
                                    <?php $main->queryAllPatients(); ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="panel panel-default content-btn-bar ">
                    <div class="panel-body">
                        <div class="btn-group content-btn-group" role="group" aria-label="">
                            <button type="button" class="btn btn-default btn-width-quarter" onclick="showPictures()">Pictures</button>
                            <button type="button" class="btn btn-default btn-width-quarter" onclick="showGraph()">Graph</button>
                            <button type="button" class="btn btn-default btn-width-quarter" onclick="showJournals()">Journals</button>
                            <button type="button" class="btn btn-default btn-width-quarter" onclick="showMore()">More</button>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default content-deck " id="main_content">
                    <div class="panel-body">
                        <script type="text/javascript">

                            function showPictures(){
                                if(currPatientID !=-1){
                                    document.getElementById('no-selection-notify').style.display = "none";
                                    document.getElementById('picturesDiv').style.display = "block";
                                    document.getElementById('graphDiv').style.display = "none";
                                    document.getElementById('journalDiv').style.display = "none";
                                    document.getElementById('moreDiv').style.display = "none";
                                    currPage = 0;
                                    if(imgView == 0){
                                        showImgOverview();
                                    }else{
                                        showImgDetail();
                                    }
                                }
                            }
                        </script>
                        <div id="no-selection-notify">
                            <p class="text_notify_grey"> Select a patient to view his/her profile</p>
                        </div>
                        <div id="picturesDiv" class="center_align" align="center" style="display:block">
                            <div id="view-select" class="btn-group content-btn-group twin-btn-group" role="group" aria-label="">
                                <button type="button" class="btn btn-default btn-width-half" id="btn-overview" onclick="showImgOverview()">Overview</button>
                                <button type="button" class="btn btn-default btn-width-half" onclick="showImgDetail()">Details</button>
                            </div>

                            <div id="overview-div" style="display:block">
                                <script type="text/javascript">
                                    function updateImgOverview(id){
                                        document.getElementById("view-select").style.display = "block";
                                        $.post('main.php', { action: 'updateImgOverview', healthcare_no: id },
                                               function(data){
                                            $('#overview-div').fadeOut(200, function () {
                                                $(this).html(data);
                                                $(this).fadeIn(200);
                                            });
                                        });
                                    }
                                </script>
                            </div>
                            <div id="detail-div" style="display:none">
                                <script type="text/javascript">
                                    function updateImgDetail(id){
                                        $.post('main.php', { action: 'updateImgDetail', healthcare_no: id },
                                               function(data){
                                            $('#detail-viewer-container').fadeOut(200, function () {
                                                $(this).html(data);
                                                initGalleria();
                                                $(this).fadeIn(200);
                                            });
                                        });
                                    }
                                </script>
                                <div id="detail-viewer-container"></div>
                                <div id="detail-comment-container">
                                    <form id="img_comment_form" onsubmit="return false" method="post">
                                        <div class="input-group" id="comment-box">
                                            <textarea type="text"  placeholder = "Leave a comment" required name="comment" class="form-control form-std"></textarea>
                                            <input type="hidden" name="healthcare_no" id="pic_owner" value=""/>  
                                            <input type="hidden" id="imagefile" name="picture_file"/>
                                            <input type="hidden" name="action" value="insert_comment_for_patient"/>
                                            <input type="hidden" name="commenter" value="<?php echo $info[DB_COL_HEALTHCARE_NO]; ?>"/>
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

                                        <script type="text/javascript">
                                            $('#img_comment_form').submit(function(event){
                                                document.getElementById("imagefile").value = currImageName;
                                                document.getElementById("pic_owner").value = currPatientID;
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

                            <script type="text/javascript">
                                function showImgOverview(){
                                    imgView = 0;
                                    document.getElementById("overview-div").style.display="block";
                                    document.getElementById("detail-div").style.display="none";
                                    updateImgOverview(currPatientID);
                                };
                                function showImgDetail(){
                                    imgView = 1;
                                    document.getElementById("overview-div").style.display="none";
                                    document.getElementById("detail-div").style.display="block";
                                    updateImgDetail(currPatientID);
                                }
                            </script>
                        </div>
                        <script type="text/javascript">
                            function showGraph(){
                                if(currPatientID != -1){
                                    document.getElementById('no-selection-notify').style.display = "none";
                                    document.getElementById('picturesDiv').style.display = "none";
                                    document.getElementById('graphDiv').style.display = "block";
                                    document.getElementById('journalDiv').style.display = "none";
                                    document.getElementById('moreDiv').style.display = "none";
                                    if(chart == null){
                                        initGraph('week');
                                    }else{
                                         generateScanData();
                                    }
                                    currPage = 1;
                                }
                            }
                        </script>
                        <div id="graphDiv" style="display:none">
                            <div style="margin: 10px auto;">
                                <div class="input-group" style="float:left; width:50%; padding:5px;">
                                    <input onchange="onDateChange(this.value)" class="form-control" placeholder="Pick a date" data-provide="datepicker" id="datepicker">
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
                                <div id="container" style="width:auto; height:auto; position:relative">
                                </div>
                            </div>
                            <script>
                                var name = "<?php if(isset($info[DB_COL_NAME])){echo $info[DB_COL_NAME]; }?>";
                                function initGraph(type){
                                    currChartType = type;
                                    //initLineChart(today(), currChartData, type, name, 'container', 'Sample Data', 'Scan Count');
                                     generateScanData();
                                }
                                function onDateChange(newDate){
                                    $('#datepicker').datepicker('hide')
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

                                    $.getJSON('main.php', { action: 'generateScanData', healthcare_no: currPatientID }, function(data){
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

                        <script type="text/javascript">
                            function showJournals(){
                                if(currPatientID != -1){
                                    document.getElementById('no-selection-notify').style.display = "none";
                                    document.getElementById('picturesDiv').style.display = "none";
                                    document.getElementById('graphDiv').style.display = "none";
                                    document.getElementById('journalDiv').style.display = "block";
                                    document.getElementById('moreDiv').style.display = "none";
                                    currPage = 2;
                                    updateJournalForUser(currPatientID);
                                }
                            };
                        </script>
                        <div id="journalDiv" class="center_align" style="display:none">
                            <div id="journals_container">  
                                <div id="scrollbox" >  
                                    <div id="comment-list-group" class="center_align">  
                                        <script type="text/javascript">
                                            function updateJournalForUser(id){
                                                $.post('main.php', { action: 'updateJournalForUser', healthcare_no: id },
                                                       function(data){
                                                    $('#comment-list-group').fadeOut(300, function () {
                                                        $(this).html(data);
                                                        $(this).fadeIn(300);
                                                    });
                                                });
                                            }
                                        </script>

                                    </div>  
                                </div>  
                            </div> 
                        </div>

                        <script type="text/javascript">
                            function showMore(){
                                if(currPatientID != -1){
                                    document.getElementById('no-selection-notify').style.display = "none";
                                    document.getElementById('picturesDiv').style.display = "none";
                                    document.getElementById('graphDiv').style.display = "none";
                                    document.getElementById('journalDiv').style.display = "none";
                                    document.getElementById('moreDiv').style.display = "block";
                                    currPage = 3;
                                }
                            };
                        </script>
                        <div id="moreDiv" style="display:none">
                            <p id="label">show more</p>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12 col-xs-12 col-lg-3">
                <div class="content-right  panel panel-default " style="text-align:center;">
                    <div style="text-align:center; margin-bottom:20px;">
                        <div id="patient-profile-img-div" style=" overflow:hidden; background-size:cover;">
                            <img id="default-profile-img" class="profile-img" src="../res/icon-user-default.png" style="margin: 0px auto 20px auto;"/>
                            <script type="text/javascript">
                                function loadPatientProfileImg(id){
                                    $.post('load-user-profile-img.php', { action: 'load-patient-profile', healthcare_no: id},
                                           function(data){
                                        $("#patient-profile-img-div").fadeOut(300, function () {
                                            $(this).html(data);
                                            $(this).fadeIn(300);
                                        })
                                    });
                                }
                            </script>
                        </div>
                        <span class="label label-default">Currently selected patient</span>
                    </div>
                    <div style="text-align:left; margin: 0 auto; width:180px;" >
                        <p style="font-size: 15px; color: #8E8E8E;" id="pName">Name: </p>
                        <p style="font-size: 15px; color: #8E8E8E;" id="pGender">Gender: </p>
                        <p style="font-size: 15px; color: #8E8E8E;" id="pAge">Age: </p>
                        <p style="font-size: 15px; color: #8E8E8E;" id="pEmail">Email: </p>
                        <p style="font-size: 15px; color: #8E8E8E;" id="pAddress">Address: </p>
                        <p style="font-size: 15px; color: #8E8E8E;" id="pPhone">Phone: </p>
                    </div>
                </div>
            </div>
        </div>

        <!--footer-->
        <footer class="footer">
            <div class="container">
                <p class="text-muted text-center" >&copy;2015 All rights reserved</p>
            </div>
        </footer>
        <!--scripts-->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        <script src="../js/bootbox.min.js"></script>
        <script src="../HideSeek-master/jquery.hideseek.min.js"></script>
        <script src="http://code.highcharts.com/highcharts.js"></script>
        <script src="../js/chartUtil.js"></script>
        <script src="../bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

        <script type="text/javascript">$('#search').hideseek({nodata: 'No results found'});</script>

        <script type="text/javascript">
            function onSearchButtonToggle(){
                if( $("#search-patient-div").outerHeight() == 430){
                    $("#search-area").fadeOut(500, function (){
                        $("#search-patient-div").outerHeight(100);
                        document.getElementById("btn-toggle-search").innerHTML = "Show Search";
                    });

                }else{
                    $("#search-patient-div").outerHeight(430);
                    document.getElementById("btn-toggle-search").innerHTML = "Hide Search";
                    $("#search-area").fadeIn(500, function (){

                    });

                }
            }
            var previous_click = null;
            function onPatientClick(link){
                link.className += " active";
                if(previous_click != null){
                    if(previous_click!=link){
                        previous_click.className = previous_click.className.replace( /(?:^|\s)active(?!\S)/g , '' );
                    }
                }
                previous_click = link;
                $(document).ready(function(){
                    /* call the php that has the php array which is json_encoded */
                    $.getJSON('load-patient-info.php', {action: 'update-patient', healthcare_no: link.id}, function(data) {
                        /* data will hold the php array as a javascript object */
                        var arr = [];
                        $.each(data, function(key, val) {
                            arr[key] = val;
                        });
                        document.getElementById("pName").innerHTML = "Name: "+arr["name"];
                        document.getElementById("pGender").innerHTML = "Gender: "+arr['gender'];
                        document.getElementById("pAddress").innerHTML = "Address: "+arr['address'];
                        document.getElementById("pEmail").innerHTML = "Email: "+arr["email"];
                        document.getElementById("pAge").innerHTML = "Age: "+arr['age'];
                        document.getElementById("pPhone").innerHTML = "Phone: "+arr['phone'];
                        currPatientID = arr['healthcare_no'];
                        loadPatientProfileImg(currPatientID);
                        initPageContent();
                        
                    });

                });
            }
        </script>
        <script type="text/javascript">

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
                                bootbox.alert("Update failed, make sure both fields are filled.", function() {
                                });
                                return;
                            }

                            $.post('update-journals.php', { action: 'update', note_id: element.id , note_title: t, note: c},
                                   function(data){
                                if(data){
                                    reloadJournalsDiv();
                                }else{
                                    bootbox.alert("Update failed  due to server error", function() {
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
                    $.post('img-comment.php', { action: 'get_comments_with_id', file: f, patient_id: currPatientID },
                           function(data){
                        $('#comments_list').fadeOut(200, function () {
                            $(this).html(data);
                            $(this).fadeIn(200);
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