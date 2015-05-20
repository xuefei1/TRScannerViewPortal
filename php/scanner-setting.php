<?php

function loadSimpleIntegerOptions($init, $key, $num){
    for($i = $init; $i<=$num; $i++){
        echo "<option value=".$key.$i.">$i</option>";
    } 
}

?>

<!DOCTYPE html>
<html lang="en" >
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width = device-width initial-scale=1.0">
        <title>Scanner Settings</title>
        <link href="../css/bootstrap.min.css" rel="stylesheet">
        <link href="../bootstrap-select/dist/css/bootstrap-select.css" rel="stylesheet">
        <link href="../bootstrap-switch-master/dist/css/bootstrap3/bootstrap-switch.css" rel="stylesheet">
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
        <script src="../bootstrap-select/dist/js/bootstrap-select.min.js"></script>
        <script src="../bootstrap-switch-master/dist/js/bootstrap-switch.min.js"></script>
    </head>

    <body style="background-color:#f0f4c3;">
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
            <div class="border-std col-md-16 col-sm-12 col-xs-12 col-lg-12 " style="margin: 40px auto;">

                <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6" style="margin-bottom:35px;">
                    <div class="page-header" style="margin:0 auto; ">
                        <h4 class="text_grey">Current RFID Reader Settings</h4>
                    </div>
                    <div id="settings-panel" >
                        <form id="settings-form" onsubmit="return false" method="post">
                            <input type="hidden" name="action" value="save-setting">
                            <!--switches-->
                            <div class="checkbox row">
                                <div><p style="">Continuous Scanning: </p><input id="continuous_scanning" type="checkbox" name="continuous_scanning" value="k" ></div>
                            </div>
                            <div class="checkbox row">
                                <div><p style="">Tag ID Streaming: </p><input id="tag_id_streaming" type="checkbox" name="tag_id_streaming" value="d" style="float:right"></div>
                            </div>
                            <div class="checkbox row">
                                <div><p style="">Frequency Hopping: </p><input id="frequency_hopping" type="checkbox" name="frequency_hopping" value="h"></div>
                            </div>
                            <div class="checkbox row">
                                <div><p style="">Upload Tag to Server: </p><input id="upload_tag_to_server" type="checkbox" name="upload_tag_to_server" value="true"></div>
                            </div>
                            <div class="checkbox row">
                                <div><p style="">Protocol Type Output: </p><input id="protocol_type_output" type="checkbox" name="protocol_type_output" value="m"></div>
                            </div>
                            <div class="checkbox row">
                                <div><p style="">Reader ID Output: </p><input id="reader_id_output" type="checkbox" name="reader_id_output" value="i"></div>
                            </div>
                            <div class="checkbox row">
                                <div><p style="">Frequency Channel Output:</p> <input id="frequency_channel_output" type="checkbox" name="frequency_channel_output" value="v"></div>
                            </div>
                            <div class="checkbox row">
                                <div><p style="">Protocol Control Output:</p> <input id="protocol_control_output" type="checkbox" name="protocol_control_output" value="o"></div>
                            </div>

                            <!--dropdown selection-->

                            <div class="checkbox row">
                                <p>Protocol Mode:</p>
                                <select id="protocol_mode" form="settings-form" class="selectpicker" name="protocol_mode">
                                    <option value="P0">Multi-Protocol Mode</option>
                                    <option value="P1">EPCG1</option>
                                    <option value="P2">EPCG2 ASK</option>
                                </select>

                            </div>

                            <div class="checkbox row">
                                <p>Transmit Frequency:</p>
                                <select id="transmit_frequency" form="settings-form" class="selectpicker" name="transmit_frequency">
                                    <option value="F1">North American (~915MHz)</option>
                                    <option value="F2">European (~868MHz)</option>
                                    <option value="F4">Korean (~912MHz)</option>
                                </select>

                            </div>

                            <div class="checkbox row">
                                <p>Frequency Channel:</p>

                                <select id="frequency_channel" form="settings-form" class="selectpicker" name="frequency_channel">
                                    <?php loadSimpleIntegerOptions(1, 'G', 50); ?>
                                </select>

                            </div>

                            <div class="checkbox row">
                                <p>Output Power Level:</p>
                                <select id="output_power_level" form="settings-form" class="selectpicker" name="output_power_level">
                                    <?php loadSimpleIntegerOptions(0, 'J' ,27); ?>
                                </select>

                            </div>

                            <div class="checkbox row">
                                <p>I/Q Receive Channel: </p>
                                <select id="iq_receive_channel" form="settings-form" class="selectpicker" name="iq_receive_channel">
                                    <option value="A0">Both I/Q Channels</option>
                                    <option value="A1">OnlyI Channel</option>
                                    <option value="A2">Only Q Channel</option>
                                </select>

                            </div>
                            <div class="checkboxrow">
                                <p>Set Reader Session: </p>
                                <select id="set_reader_session" form="settings-form" class="selectpicker" name="set_reader_session">
                                    <option value="Q0">Session 0</option>
                                    <option value="Q1">Session 1</option>
                                    <option value="Q2">Session 2</option>
                                    <option value="Q3">Session 3</option>
                                </select>

                            </div>
                            <div class="checkbox row">
                                <p>Number of Collision Slots: </p>
                                <select id="number_of_collision_slots" form="settings-form" class="selectpicker" name="slt">
                                    <?php loadSimpleIntegerOptions(0, 'N', 9); ?>
                                </select>

                            </div>
                            <div class="checkbox row">
                                <p>Number of Collision Attempts: </p>
                                <select id="number_of_collision_attempts" form="settings-form" class="selectpicker" name="atp">
                                    <?php loadSimpleIntegerOptions(0, 'Y', 9); ?>
                                </select>

                            </div>

                            <!-- input boxes-->
                            <div class="row">
                                <p>Username:</p>
                                <input id="username" type="text" required name ="username" class="form-control form-std" placeholder="Username" style="max-width:300px">
                            </div>
                            <div class="row">
                                <p>Password:</p>
                                <input id="password" type="text" required name ="password" class="form-control form-std" placeholder="Password" style="max-width:300px">
                            </div>
                            <div class="row">
                                <p>Access Password:</p>
                                <input id="set_access_password" type="text" required name ="set_access_password" class="form-control form-std" placeholder="Set Access Password" style="max-width:300px">
                            </div>
                            <div class="row">
                                <p>Kill Password:</p>
                                <input id="set_kill_password" type="text" required name ="set_kill_password" class="form-control form-std" placeholder="Set Kill Password" style="max-width:300px">
                            </div>

                            <div align="left">
                                <button style="min-width:100px; max-width:160px;" type="submit" id="save-setting" class="btn btn-default btn_standard">Save</button> 
                                <p id="msg-success" style="color:#669900;font-size: 15px; display:none; margin:0 20px;">Saved!</p>
                                <p id="msg-error" style=" color:#FF0000;font-size: 15px; display:none; margin:0 20px;">Server error</p>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-6 col-sm-12 col-xs-12 col-lg-6" >
                    <div class="page-header" style="margin:0 auto; ">
                        <h4 class="text_grey">Tags</h4>
                    </div>
                    <div id="tags-panel" >
                        
                    </div>
                </div>

            </div>
        </div>
        <style>
            .border-std{
                border:25px solid #FFFFFF;border-radius: 3px;box-shadow: 0px 2px 5px #333333;background-color:#FFFFFF;
            }

            .row{
                margin: 35px auto;
            }

            p{
                color: #6E6E6E;
                font-size: 16px;
            }
        </style>

        <!--scripts-->
        <script type="text/javascript">
            window.onbeforeunload = function () {
                return "Make sure to save all the changes.";
            };
            $.fn.bootstrapSwitch.defaults.handleWidth = '60px';
            $.fn.bootstrapSwitch.defaults.labelWidth = '50px';
            loadSettingPreference();
            $('.selectpicker').selectpicker({
                style: 'btn-default',
            });
            $("[name='continuous_scanning']").bootstrapSwitch();
            $("[name='tag_id_streaming']").bootstrapSwitch();
            $("[name='frequency_hopping']").bootstrapSwitch();
            $("[name='upload_tag_to_server']").bootstrapSwitch();
            $("[name='protocol_type_output']").bootstrapSwitch();
            $("[name='reader_id_output']").bootstrapSwitch();
            $("[name='frequency_channel_output']").bootstrapSwitch();
            $("[name='protocol_control_output']").bootstrapSwitch();


            function loadSettingPreference(){
                $.getJSON('rfid-data.php', {action:'load-settings'}, function(data) {
                    /* data will hold the php array as a javascript object */
                    var arr = [];
                    $.each(data, function(key, val) {
                        arr[key] = val;
                    });
                    updateSettingPreference(arr);
                });
            }

            function updateSettingPreference(arr){
                updateCheckBox('continuous_scanning', getBool(arr['continuous_scanning']));
                updateCheckBox('tag_id_streaming', getBool(arr['tag_id_streaming']));
                updateCheckBox('frequency_hopping', getBool(arr['frequency_hopping']));
                updateCheckBox('upload_tag_to_server', getBool(arr['upload_tag_to_server']));
                updateCheckBox('protocol_type_output', getBool(arr['protocol_type_output']));
                updateCheckBox('reader_id_output', getBool(arr['reader_id_output']));
                updateCheckBox('frequency_channel_output', getBool(arr['frequency_channel_output']));
                updateCheckBox('protocol_control_output', getBool(arr['protocol_control_output']));


                updateSelection('protocol_mode', arr['protocol_mode']);
                updateSelection('transmit_frequency', arr['transmit_frequency']);
                updateSelection('frequency_channel', arr['frequency_channel']);
                updateSelection('output_power_level', arr['output_power_level']);
                updateSelection('iq_receive_channel', arr['iq_receive_channel']);
                updateSelection('set_reader_session', arr['set_reader_session']);
                updateSelection('slt', arr['number_of_collision_slots']);
                updateSelection('atp', arr['number_of_collision_attempts']);
                updateValue('username', arr['username']);
                updateValue('password', arr['password']);
                updateValue('set_access_password', arr['set_access_password']);
                updateValue('set_kill_password', arr['set_kill_password']);

            }



            function updateCheckBox(id, bool){
                if(bool){
                    check(id);
                }else{
                    uncheck(id);
                }
            }

            function updateSelection(id, val){
                $('select[name='+id+']').val(val);
                $('.selectpicker').selectpicker('refresh');
            }

            function updateValue(id, val){
                document.getElementById(id).value = val;
            }

            function check(id) {
                $('input[id='+id+']').bootstrapSwitch('state', true, true);
                document.getElementById(id).checked = true;
            }

            function uncheck(id) {
                $('input[id='+id+']').bootstrapSwitch('state', false, false);
                document.getElementById(id).checked = false;
            }

            function getBool(data){
                if(data.toLowerCase()  == 'true'){
                    return true;
                }else if(data.toLowerCase() == 'false'){
                    return false;
                }
                else if(data == data.toLowerCase()){
                    return true;
                }
                else{
                    return false;
                }
            }

            $('#settings-form').submit(function(event){
                $.ajax({
                    url: 'rfid-data.php',
                    type: 'post',
                    dataType: 'html',   //expect return data as html from server
                    data: $('#settings-form').serialize(),
                    success: function(response, textStatus, jqXHR){
                        if(response.trim() == 'saved'){
                            document.getElementById("msg-success").style.display = "inline-block";
                            $('#msg-success').fadeIn(5000, function () {
                                $(this).fadeOut(3000);
                            });
                        }else{
                            document.getElementById("msg-error").style.display = "inline-block";
                            $('#msg-error').fadeIn(5000, function () {
                                $(this).fadeOut(3000);
                            });
                        }
                        loadSettingPreference()
                    },
                    error: function(jqXHR, textStatus, errorThrown){
                        console.log('error(s):'+textStatus, errorThrown);
                    }
                });
            });

            $(document).ready(function () {
                var interval = 60000;   //number of mili seconds between each call
                var refresh = function() {
                    $.post('load-tags.php', { action: 'load-tags'}, 
                           function(data) {
                        $('#tags-panel').fadeOut(300, function () {
                            $('#tags-panel').html(data);
                            $(this).fadeIn(300);
                        });
                        setTimeout(function() {
                            refresh();
                        }, interval);
                    }
                          );
                };
                refresh();
            });
        </script>
    </body>
</html>