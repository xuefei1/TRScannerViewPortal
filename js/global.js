var chart = null;

var currChartData = [[1397902400000, 1],
                     [1398902400000, 4],
                     [1399839200000, 5],
                     [1399902400000, 6],
                     [1399939200000, 1],
                     [1400197600000, 3],
                     [1400195600000, 2],
                     [1400295600000, 4]];

function updateImgDetail(id){
    $.post('main.php', { action: 'updateImgDetail', healthcare_no: id },
           function(data){
        $('#galleria-container').fadeOut(200, function () {
            if(data == 0){
                document.getElementById('galleria-container').style.display = "none"; document.getElementById('no-image-notify-div').innerHTML='<p class="text_notify_grey">No uploaded picture(s) found.</p>'; 
                document.getElementById('comment-section-div').style.display = "none";
                return;
            }
            document.getElementById('galleria-container').style.display = "block";
            document.getElementById('no-image-notify-div').innerHTML=''; 
            document.getElementById('comment-section-div').style.display = "block";
            $(this).empty();
            $(this).html(data);
            initGalleria();
            $(this).fadeIn(200);
        });
    });
}



