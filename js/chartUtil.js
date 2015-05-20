var currChartType = 'week';
var currChartDate = today();

var TYPE_YEAR = 'year';
var TYPE_MONTH = 'month';
var TYPE_WEEK = 'week';
var TYPE_DAY = 'day';

var MILLI_YEAR = 3.15569e10;
var MILLI_MONTH = 2.62974e9;
var MILLI_WEEK = 604800000;
var MILLI_DAY = 86400000;

function today(){
    var date = new Date();
    return date.getTime();
}

function parseTimeInMillis(time){
    var d = Date.parse(time);
    if(d){
        return d;
    }else{
        bootbox.alert("Please pick a valid date.", function(){});
        return today();
    }
}

function isTimeWithinInterval(time, ref, type){
    var today = ref;
    var min, max;
    if(type == TYPE_YEAR){
        min = today - MILLI_YEAR;
        max = today + MILLI_YEAR;
        if(time >= min && time <= max){
            return true;
        }else{
            return false;
        }
    } else if(type == TYPE_MONTH){
        min = today - MILLI_MONTH;
        max = today + MILLI_MONTH;
        if(time >= min && time <= max){
            return true;
        }else{
            return false;
        }
    } else if(type == TYPE_WEEK){
        min = today - MILLI_WEEK;
        max = today + MILLI_WEEK;
        if(time >= min && time <= max){
            return true;
        }else{
            return false;
        }
    } else if(type == TYPE_DAY){
        min = today - MILLI_DAY;
        max = today + MILLI_DAY;
        if(time >= min && time <= max){
            return true;
        }else{
            return false;
        }
    } else{
        return false;
    }
}

function processDataForInterval(refDate, data, type){
    var arrayLength = data.length;
    var array = [];
    for (var i = 0; i < arrayLength; i++) {
        if(isTimeWithinInterval(data[i][0], refDate, type)){
            array.push(data[i]);
        }
    }
    return array;
}

function initLineChart(refDate, data, type, name, target, title, Ytitle){
    data = processDataForInterval(refDate, data, type);
    if (typeof chart == 'undefined'){
        alert("chart variable not defined!");
        return;
    }

    chart = new Highcharts.Chart({
        chart: {
            renderTo: target,
            type: 'line',
            zoomType: 'x'
        },
        title: {
            text: title
        },
        xAxis: {
            gridLineWidth: 1,
            type: "datetime",
            tickInterval: 24 * 3600 * 1000,
            labels: {
                rotation: -45,
                align: 'right'
            },
            dateTimeLabelFormats: {
                day: '%e. %b',
            }
        },
        yAxis: {
            gridLineWidth: 1,
            title: {
                text: Ytitle
            }
        },
        tooltip: {
            shared: true,
            crosshairs: true
        },
        plotOptions: {
            series: {
                cursor: 'pointer',
                point: {
                    events: {
                        click: function (e) {

                            bootbox.alert(
                                "<h4>"+this.series.name +" </h4></br> "+Highcharts.dateFormat('%H:%M:%S, %A, %b %e, %Y', this.x) + ':<br/> ' +
                                this.y + ' scan(s)', function() {
                                    //callback function
                                });
                        }
                    }
                },
                marker: {
                    lineWidth: 1
                }
            }
        },
        series: [{
            name: name,
            data: data
        }]
    });
    if(data.length == 0){
        text = chart.renderer.text("No data recorded for this period.").add();
        textBBox = text.getBBox();
        x = chart.plotLeft + (chart.plotWidth  * 0.5) - (textBBox.width  * 0.5);
        y = chart.plotTop  + (chart.plotHeight * 0.5) + (textBBox.height * 0.25);
        text.attr({x: x, y: y});
    }
}