/**
 * Created by gatovago on 11/17/14.
 */


var options = {
    //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
    scaleBeginAtZero : true,
    //Boolean - Whether grid lines are shown across the chart
    scaleShowGridLines : true,

    inGraphDataShow : true,

    inGraphDataAlign : "center",
    inGraphDataVAlign : "middle",
    inGraphDataRotate : "inRadiusAxisRotateLabels",

    //String - Colour of the grid lines
    scaleGridLineColor : "rgba(0,0,0,.05)",

    legend: true,

    //Number - Width of the grid lines
    scaleGridLineWidth : 1,

    //Boolean - If there is a stroke on each bar
    barShowStroke : true,

    //Number - Pixel width of the bar stroke
    barStrokeWidth : 2,

    //Number - Spacing between each of the X value sets
    barValueSpacing : 5,

    //Number - Spacing between data sets within X values
    barDatasetSpacing : 1,

    //String - A legend template
    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"

}

function vgt_plot_bar_chart(data, options, ctx)
{
    return new Chart(ctx).Bar(data, options);

}