<!-- Main Content -->
<div class="main-content">
   <section class="section">
      <div class="section-header">
         <h1>Analytics</h1>
         <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>admin/index">Dashboard</a></div>
            <div class="breadcrumb-item">Analytics</div>
         </div>
      </div>
 <div class="setting-section analyticsSection">
         <div class="tab">
            <button class="tablinks" onclick="firstTab(event, 'Real_time')" id="defaultOpen">Real time</button>
            <button class="tablinks" onclick="firstTab(event, 'Orders_and_Revenue')">Orders and Revenue</button>
            <button class="tablinks" onclick="firstTab(event, 'Engagement')">Engagement</button>
            <button class="tablinks" onclick="firstTab(event, 'Users')">Users</button>
            <!-- <button class="tablinks" onclick="firstTab(event, 'Extra')">Extra</button> -->
         </div>
         <div id="Real_time" class="tabcontent">
            <div class="row">
              <div class="col-md-4 col-xs-12">
                <select>
                  <option>Live Revenue</option>
                </select>
                 <div id="chartdiv"></div>
              </div>
               <div class="col-md-4 col-xs-12">
                  <select>
                  <option>Live Customers</option>
                </select>
                 <div id="chartdiv-1"></div>
              </div>
               <div class="col-md-4 col-xs-12">
                  <select>
                  <option>Live Orders</option>
                </select>
                 <div id="chartdiv-2"></div>
               
              </div>
            </div>

            <div class="row">
              <div class="col-md-12 col-xs-12">

                <select>
                  <option>Orders & Revenue</option>
                </select>
                 <div id="chartdiv-petro" style="height: 350px"></div>
              </div>
              
             
            </div>

         </div>
         <div id="Orders_and_Revenue" class="tabcontent">
            <div class="row">
              <div class="col-md-6 col-xs-12">
                <select>
                  <option>Order Stauts</option>
                </select>
                 <div id="chartdiv-circle" style="height: 350px"></div>
              </div>
              <div class="col-md-6 col-xs-12">
                <select>
                  <option>Monthly Revenue and Orders</option>
                </select>
                 <div id="chartdiv-petro1" style="height: 350px"></div>
              </div>
           
            </div>
              <div class="row">
              <div class="col-md-6 col-xs-12">
                <select>
                  <option>Product Sales</option>
                </select>
                 <div id="chartdiv-chart" style="height: 350px"></div>
              </div>
              <div class="col-md-6 col-xs-12">
                <select>
                  <option>Promo Code Used</option>
                </select>
                 <div id="chartdiv-line-2" style="height: 350px"></div>
              </div>
           
            </div>
              <div class="row">
             
              <div class="col-md-12 col-xs-12">
                <select>
                  <option>Orders & Revenue</option>
                </select>
                 <div id="chartdiv-petro11" style="height: 350px"></div>
              </div>
           
            </div>
         </div>
         <div id="Engagement" class="tabcontent">
            <div class="row">
               <div class="col-md-6 col-cs-12">
                  <select>
                  <option>Orders & Revenue</option>
                </select>
                 <div id="chartContainer-2" style="height: 350px; width: 100%"></div>
               </div>
                 <div class="col-md-6 col-cs-12">
                  <select>
                  <option>Orders & Revenue</option>
                </select>
                 <div id="chartContainer-11" style="height: 350px; width: 100%"></div>
               </div>
              </div>
              <div class="row">
               <div class="col-md-6 col-cs-12 orders_revenues">
                  <select>
                  <option>Orders & Revenue</option>
                </select>
                 <div id="line-3" style="height: 350px; width: 100%"></div>
               </div>
                 <div class="col-md-6 col-cs-12 orders_revenues">
                  <select>
                  <option>Orders & Revenue</option>
                </select>
                 <div id="line-4" style="height: 350px; width: 100%"></div>
               </div>
              </div>
            </div>
         </div>
         <div id="Users" class="tabcontent">
            <div class="row">
               <div class="col-md-12 col-cs-12">
                  <select>
                  <option>Orders & Revenue</option>
                </select>
                 <div id="line-5" style="height: 350px; width: 100%"></div>
               </div>
            </div>
              <div class="row">
               <div class="col-md-12 col-cs-12">
                  <select>
                  <option>Orders & Revenue</option>
                </select>
                 <div id="line-6" style="height: 350px; width: 100%"></div>
               </div>
            </div>
         </div>
      </div>
</div>
</section>
</div>

<!-- Revenue Chart JS -->
 <script src="<?php echo base_url(); ?>assets/js/d3.v5.min.js"></script>
 <script src="<?php echo base_url(); ?>assets/js/pluscharts.js"></script>
 <!-- Resources -->
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("chartdiv", am4charts.XYChart);

var data = [];
var value = 50;
for(var i = 0; i < 300; i++){
  var date = new Date();
  date.setHours(0,0,0,0);
  date.setDate(i);
  value -= Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
  data.push({date:date, value: value});
}

chart.data = data;

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
//dateAxis.renderer.minGridDistance = 60;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Create series
var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.valueY = "value";
series.dataFields.dateX = "date";
series.tooltipText = "{value}"

//series.tooltip.pointerOrientation = "vertical";

chart.cursor = new am4charts.XYCursor();
chart.cursor.snapToSeries = series;
chart.cursor.xAxis = dateAxis;

//chart.scrollbarY = new am4core.Scrollbar();
//chart.scrollbarX = new am4core.Scrollbar();

}); // end am4core.ready()
</script>


<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("chartdiv-1", am4charts.XYChart);

var data = [];
var value = 50;
for(var i = 0; i < 300; i++){
  var date = new Date();
  date.setHours(0,0,0,0);
  date.setDate(i);
  value -= Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
  data.push({date:date, value: value});
}

chart.data = data;

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
//dateAxis.renderer.minGridDistance = 60;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Create series
var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.valueY = "value";
series.dataFields.dateX = "date";
series.tooltipText = "{value}"

//series.tooltip.pointerOrientation = "vertical";

chart.cursor = new am4charts.XYCursor();
chart.cursor.snapToSeries = series;
chart.cursor.xAxis = dateAxis;

//chart.scrollbarY = new am4core.Scrollbar();
//chart.scrollbarX = new am4core.Scrollbar();

}); // end am4core.ready()
</script>

<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("chartdiv-2", am4charts.XYChart);

var data = [];
var value = 50;
for(var i = 0; i < 300; i++){
  var date = new Date();
  date.setHours(0,0,0,0);
  date.setDate(i);
  value -= Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
  data.push({date:date, value: value});
}

chart.data = data;

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
//dateAxis.renderer.minGridDistance = 60;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Create series
var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.valueY = "value";
series.dataFields.dateX = "date";
series.tooltipText = "{value}"

//series.tooltip.pointerOrientation = "vertical";

chart.cursor = new am4charts.XYCursor();
chart.cursor.snapToSeries = series;
chart.cursor.xAxis = dateAxis;

//chart.scrollbarY = new am4core.Scrollbar();
//chart.scrollbarX = new am4core.Scrollbar();

}); // end am4core.ready()
</script>


<!-- Chart code -->
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv-petro", am4charts.XYChart);

// Export
chart.exporting.menu = new am4core.ExportMenu();

// Data for both series
var data = [ {
  "year": "1998",
  "income": 23.5,
  "expenses": 22.1
},
{
  "year": "1999",
  "income": 123.5,
  "expenses": 122.1
},{
  "year": "1999",
  "income": 55.5,
  "expenses": 52.1
},{
  "year": "2000",
  "income": 38.5,
  "expenses": 37.1
},{
  "year": "2001",
  "income": 223.5,
  "expenses": 222.1
},{
  "year": "2002",
  "income": 525.5,
  "expenses": 522.1
},{
  "year": "2003",
  "income": 333.5,
  "expenses": 322.1
},{
  "year": "2004",
  "income": 213.5,
  "expenses": 212.1
},{
  "year": "2005",
  "income": 23.5,
  "expenses": 22.1
},{
  "year": "2006",
  "income": 199.5,
  "expenses": 22.1
},{
  "year": "2007",
  "income": 523.5,
  "expenses": 512.1
},{
  "year": "2008",
  "income": 359.5,
  "expenses": 92.1
}, {
  "year": "2010",
  "income": 26.2,
  "expenses": 24
}, {
  "year": "2011",
  "income": 230.1,
  "expenses":129
}, {
  "year": "2012",
  "income": 279.5,
  "expenses": 127
}, {
  "year": "2013",
  "income": 300.6,
  "expenses": 280.2,
  "lineDash": "5,5",
}, {
  "year": "2014",
  "income": 341.1,
  "expenses": 321.9,
  "strokeWidth": 10,
  "columnDash": "5,5",
  "fillOpacity": 0.2,
  "additional": "(projection)"
} ];

/* Create axes */
var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "year";
categoryAxis.renderer.minGridDistance = 30;

/* Create value axis */
var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

/* Create series */
var columnSeries = chart.series.push(new am4charts.ColumnSeries());
columnSeries.name = "Income";
columnSeries.dataFields.valueY = "income";
columnSeries.dataFields.categoryX = "year";

columnSeries.columns.template.tooltipText = "[#fff font-size: 15px]{name} in {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/] [#fff]{additional}[/]"
columnSeries.columns.template.propertyFields.fillOpacity = "fillOpacity";
columnSeries.columns.template.propertyFields.stroke = "stroke";
columnSeries.columns.template.propertyFields.strokeWidth = "strokeWidth";
columnSeries.columns.template.propertyFields.strokeDasharray = "columnDash";
columnSeries.tooltip.label.textAlign = "middle";

var lineSeries = chart.series.push(new am4charts.LineSeries());
lineSeries.name = "Expenses";
lineSeries.dataFields.valueY = "expenses";
lineSeries.dataFields.categoryX = "year";

lineSeries.stroke = am4core.color("#fdd400");
lineSeries.strokeWidth = 3;
lineSeries.propertyFields.strokeDasharray = "lineDash";
lineSeries.tooltip.label.textAlign = "middle";

var bullet = lineSeries.bullets.push(new am4charts.Bullet());
bullet.fill = am4core.color("#fdd400"); // tooltips grab fill from parent by default
bullet.tooltipText = "[#fff font-size: 15px]{name} in {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/] [#fff]{additional}[/]"
var circle = bullet.createChild(am4core.Circle);
circle.radius = 4;
circle.fill = am4core.color("#fff");
circle.strokeWidth = 3;

chart.data = data;

}); // end am4core.ready()
</script>

<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv-circle", am4charts.PieChart);

// Add data
chart.data = [ {
  "country": "Lithuania",
  "litres": 501.9
}, {
  "country": "Czech Republic",
  "litres": 301.9
}, {
  "country": "Ireland",
  "litres": 201.1
}];

// Set inner radius
chart.innerRadius = am4core.percent(50);

// Add and configure Series
var pieSeries = chart.series.push(new am4charts.PieSeries());
pieSeries.dataFields.value = "litres";
pieSeries.dataFields.category = "country";
pieSeries.slices.template.stroke = am4core.color("#fff");
pieSeries.slices.template.strokeWidth = 2;
pieSeries.slices.template.strokeOpacity = 1;

// This creates initial animation
pieSeries.hiddenState.properties.opacity = 1;
pieSeries.hiddenState.properties.endAngle = -90;
pieSeries.hiddenState.properties.startAngle = -90;

}); // end am4core.ready()
</script>

<!-- HTML -->


<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv-petro1", am4charts.XYChart);

// Export
chart.exporting.menu = new am4core.ExportMenu();

// Data for both series
var data = [ {
  "year": "1998",
  "income": 23.5,
  "expenses": 22.1
},
{
  "year": "1999",
  "income": 123.5,
  "expenses": 122.1
},{
  "year": "1999",
  "income": 55.5,
  "expenses": 52.1
},{
  "year": "2000",
  "income": 38.5,
  "expenses": 37.1
},{
  "year": "2001",
  "income": 223.5,
  "expenses": 222.1
},{
  "year": "2002",
  "income": 525.5,
  "expenses": 522.1
},{
  "year": "2003",
  "income": 333.5,
  "expenses": 322.1
},{
  "year": "2004",
  "income": 213.5,
  "expenses": 212.1
},{
  "year": "2005",
  "income": 23.5,
  "expenses": 22.1
},{
  "year": "2006",
  "income": 199.5,
  "expenses": 22.1
},{
  "year": "2007",
  "income": 523.5,
  "expenses": 512.1
},{
  "year": "2008",
  "income": 359.5,
  "expenses": 92.1
}, {
  "year": "2010",
  "income": 26.2,
  "expenses": 24
}, {
  "year": "2011",
  "income": 230.1,
  "expenses":129
}, {
  "year": "2012",
  "income": 279.5,
  "expenses": 127
}, {
  "year": "2013",
  "income": 300.6,
  "expenses": 280.2,
  "lineDash": "5,5",
}, {
  "year": "2014",
  "income": 341.1,
  "expenses": 321.9,
  "strokeWidth": 10,
  "columnDash": "5,5",
  "fillOpacity": 0.2,
  "additional": "(projection)"
} ];

/* Create axes */
var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "year";
categoryAxis.renderer.minGridDistance = 30;

/* Create value axis */
var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

/* Create series */
var columnSeries = chart.series.push(new am4charts.ColumnSeries());
columnSeries.name = "Income";
columnSeries.dataFields.valueY = "income";
columnSeries.dataFields.categoryX = "year";

columnSeries.columns.template.tooltipText = "[#fff font-size: 15px]{name} in {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/] [#fff]{additional}[/]"
columnSeries.columns.template.propertyFields.fillOpacity = "fillOpacity";
columnSeries.columns.template.propertyFields.stroke = "stroke";
columnSeries.columns.template.propertyFields.strokeWidth = "strokeWidth";
columnSeries.columns.template.propertyFields.strokeDasharray = "columnDash";
columnSeries.tooltip.label.textAlign = "middle";

var lineSeries = chart.series.push(new am4charts.LineSeries());
lineSeries.name = "Expenses";
lineSeries.dataFields.valueY = "expenses";
lineSeries.dataFields.categoryX = "year";

lineSeries.stroke = am4core.color("#fdd400");
lineSeries.strokeWidth = 3;
lineSeries.propertyFields.strokeDasharray = "lineDash";
lineSeries.tooltip.label.textAlign = "middle";

var bullet = lineSeries.bullets.push(new am4charts.Bullet());
bullet.fill = am4core.color("#fdd400"); // tooltips grab fill from parent by default
bullet.tooltipText = "[#fff font-size: 15px]{name} in {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/] [#fff]{additional}[/]"
var circle = bullet.createChild(am4core.Circle);
circle.radius = 4;
circle.fill = am4core.color("#fff");
circle.strokeWidth = 3;

chart.data = data;

}); // end am4core.ready()
</script>

<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("chartdiv-petro11", am4charts.XYChart);

// Export
chart.exporting.menu = new am4core.ExportMenu();

// Data for both series
var data = [ {
  "year": "1998",
  "income": 23.5,
  "expenses": 22.1
},
{
  "year": "1999",
  "income": 123.5,
  "expenses": 122.1
},{
  "year": "1999",
  "income": 55.5,
  "expenses": 52.1
},{
  "year": "2000",
  "income": 38.5,
  "expenses": 37.1
},{
  "year": "2001",
  "income": 223.5,
  "expenses": 222.1
},{
  "year": "2002",
  "income": 525.5,
  "expenses": 522.1
},{
  "year": "2003",
  "income": 333.5,
  "expenses": 322.1
},{
  "year": "2004",
  "income": 213.5,
  "expenses": 212.1
},{
  "year": "2005",
  "income": 23.5,
  "expenses": 22.1
},{
  "year": "2006",
  "income": 199.5,
  "expenses": 22.1
},{
  "year": "2007",
  "income": 523.5,
  "expenses": 512.1
},{
  "year": "2008",
  "income": 359.5,
  "expenses": 92.1
}, {
  "year": "2010",
  "income": 26.2,
  "expenses": 24
}, {
  "year": "2011",
  "income": 230.1,
  "expenses":129
}, {
  "year": "2012",
  "income": 279.5,
  "expenses": 127
}, {
  "year": "2013",
  "income": 300.6,
  "expenses": 280.2,
  "lineDash": "5,5",
}, {
  "year": "2014",
  "income": 341.1,
  "expenses": 321.9,
  "strokeWidth": 10,
  "columnDash": "5,5",
  "fillOpacity": 0.2,
  "additional": "(projection)"
} ];

/* Create axes */
var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.dataFields.category = "year";
categoryAxis.renderer.minGridDistance = 30;

/* Create value axis */
var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

/* Create series */
var columnSeries = chart.series.push(new am4charts.ColumnSeries());
columnSeries.name = "Income";
columnSeries.dataFields.valueY = "income";
columnSeries.dataFields.categoryX = "year";

columnSeries.columns.template.tooltipText = "[#fff font-size: 15px]{name} in {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/] [#fff]{additional}[/]"
columnSeries.columns.template.propertyFields.fillOpacity = "fillOpacity";
columnSeries.columns.template.propertyFields.stroke = "stroke";
columnSeries.columns.template.propertyFields.strokeWidth = "strokeWidth";
columnSeries.columns.template.propertyFields.strokeDasharray = "columnDash";
columnSeries.tooltip.label.textAlign = "middle";

var lineSeries = chart.series.push(new am4charts.LineSeries());
lineSeries.name = "Expenses";
lineSeries.dataFields.valueY = "expenses";
lineSeries.dataFields.categoryX = "year";

lineSeries.stroke = am4core.color("#fdd400");
lineSeries.strokeWidth = 3;
lineSeries.propertyFields.strokeDasharray = "lineDash";
lineSeries.tooltip.label.textAlign = "middle";

var bullet = lineSeries.bullets.push(new am4charts.Bullet());
bullet.fill = am4core.color("#fdd400"); // tooltips grab fill from parent by default
bullet.tooltipText = "[#fff font-size: 15px]{name} in {categoryX}:\n[/][#fff font-size: 20px]{valueY}[/] [#fff]{additional}[/]"
var circle = bullet.createChild(am4core.Circle);
circle.radius = 4;
circle.fill = am4core.color("#fff");
circle.strokeWidth = 3;

chart.data = data;

}); // end am4core.ready()
</script>


<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("chartdiv-chart", am4charts.XYChart);
chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

chart.data = [
  {
    country: "USA",
    visits: 23725
  },
  {
    country: "China",
    visits: 1882
  },
  {
    country: "Japan",
    visits: 1809
  },
  {
    country: "Germany",
    visits: 1322
  },
  {
    country: "UK",
    visits: 1122
  },
  {
    country: "France",
    visits: 1114
  },
  {
    country: "India",
    visits: 984
  },
  {
    country: "Spain",
    visits: 711
  },
  {
    country: "Netherlands",
    visits: 665
  },
  {
    country: "Russia",
    visits: 580
  },
  {
    country: "South Korea",
    visits: 443
  },
  {
    country: "Canada",
    visits: 441
  }
];

var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
categoryAxis.renderer.grid.template.location = 0;
categoryAxis.dataFields.category = "country";
categoryAxis.renderer.minGridDistance = 40;
categoryAxis.fontSize = 11;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
valueAxis.min = 0;
valueAxis.max = 24000;
valueAxis.strictMinMax = true;
valueAxis.renderer.minGridDistance = 30;
// axis break
var axisBreak = valueAxis.axisBreaks.create();
axisBreak.startValue = 2100;
axisBreak.endValue = 22900;
//axisBreak.breakSize = 0.005;

// fixed axis break
var d = (axisBreak.endValue - axisBreak.startValue) / (valueAxis.max - valueAxis.min);
axisBreak.breakSize = 0.05 * (1 - d) / d; // 0.05 means that the break will take 5% of the total value axis height

// make break expand on hover
var hoverState = axisBreak.states.create("hover");
hoverState.properties.breakSize = 1;
hoverState.properties.opacity = 0.1;
hoverState.transitionDuration = 1500;

axisBreak.defaultState.transitionDuration = 1000;
/*
// this is exactly the same, but with events
axisBreak.events.on("over", function() {
  axisBreak.animate(
    [{ property: "breakSize", to: 1 }, { property: "opacity", to: 0.1 }],
    1500,
    am4core.ease.sinOut
  );
});
axisBreak.events.on("out", function() {
  axisBreak.animate(
    [{ property: "breakSize", to: 0.005 }, { property: "opacity", to: 1 }],
    1000,
    am4core.ease.quadOut
  );
});*/

var series = chart.series.push(new am4charts.ColumnSeries());
series.dataFields.categoryX = "country";
series.dataFields.valueY = "visits";
series.columns.template.tooltipText = "{valueY.value}";
series.columns.template.tooltipY = 0;
series.columns.template.strokeOpacity = 0;

// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
series.columns.template.adapter.add("fill", function(fill, target) {
  return chart.colors.getIndex(target.dataItem.index);
});

}); // end am4core.ready()
</script>

<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("chartdiv-line-2", am4charts.XYChart);

var data = [];
var value = 50;
for(var i = 0; i < 300; i++){
  var date = new Date();
  date.setHours(0,0,0,0);
  date.setDate(i);
  value -= Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
  data.push({date:date, value: value});
}

chart.data = data;

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
//dateAxis.renderer.minGridDistance = 60;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Create series
var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.valueY = "value";
series.dataFields.dateX = "date";
series.tooltipText = "{value}"

//series.tooltip.pointerOrientation = "vertical";

chart.cursor = new am4charts.XYCursor();
chart.cursor.snapToSeries = series;
chart.cursor.xAxis = dateAxis;

//chart.scrollbarY = new am4core.Scrollbar();
//chart.scrollbarX = new am4core.Scrollbar();

}); // end am4core.ready()
</script>




<script type="text/javascript">
window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer-2", {
  title: {
    text: ""
  },
  data: [{
    type: "funnel",
    indexLabel: "{label} [{y}%]",
    toolTipContent: "{label} - {y}%",
    dataPoints: [
      { y: 100, label: "Candidates Applied" },
      { y: 63, label: "Aptitude Test" },
      { y: 35, label: "Technical Interview" },
      { y: 15, label: "HR Interview" },
      { y: 5, label: "Candidates Recruited" }
    ]
  }]
});
chart.render();

}
</script>
<script type="text/javascript">
window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer-11", {
  title: {
    text: ""
  },
  data: [{
    type: "funnel",
    indexLabel: "{label} [{y}%]",
    toolTipContent: "{label} - {y}%",
    dataPoints: [
      { y: 100, label: "Candidates Applied" },
      { y: 63, label: "Aptitude Test" },
      { y: 35, label: "Technical Interview" },
      { y: 15, label: "HR Interview" },
      { y: 5, label: "Candidates Recruited" }
    ]
  }]
});
chart.render();

}
</script>
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("line-3", am4charts.XYChart);

var data = [];
var value = 50;
for(var i = 0; i < 300; i++){
  var date = new Date();
  date.setHours(0,0,0,0);
  date.setDate(i);
  value -= Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
  data.push({date:date, value: value});
}

chart.data = data;

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
//dateAxis.renderer.minGridDistance = 60;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Create series
var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.valueY = "value";
series.dataFields.dateX = "date";
series.tooltipText = "{value}"

//series.tooltip.pointerOrientation = "vertical";

chart.cursor = new am4charts.XYCursor();
chart.cursor.snapToSeries = series;
chart.cursor.xAxis = dateAxis;

//chart.scrollbarY = new am4core.Scrollbar();
//chart.scrollbarX = new am4core.Scrollbar();

}); // end am4core.ready()
</script>
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("line-4", am4charts.XYChart);

var data = [];
var value = 50;
for(var i = 0; i < 300; i++){
  var date = new Date();
  date.setHours(0,0,0,0);
  date.setDate(i);
  value -= Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
  data.push({date:date, value: value});
}

chart.data = data;

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
//dateAxis.renderer.minGridDistance = 60;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Create series
var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.valueY = "value";
series.dataFields.dateX = "date";
series.tooltipText = "{value}"

//series.tooltip.pointerOrientation = "vertical";

chart.cursor = new am4charts.XYCursor();
chart.cursor.snapToSeries = series;
chart.cursor.xAxis = dateAxis;

//chart.scrollbarY = new am4core.Scrollbar();
//chart.scrollbarX = new am4core.Scrollbar();

}); // end am4core.ready()
</script>
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("line-5", am4charts.XYChart);

var data = [];
var value = 50;
for(var i = 0; i < 300; i++){
  var date = new Date();
  date.setHours(0,0,0,0);
  date.setDate(i);
  value -= Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
  data.push({date:date, value: value});
}

chart.data = data;

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
//dateAxis.renderer.minGridDistance = 60;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Create series
var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.valueY = "value";
series.dataFields.dateX = "date";
series.tooltipText = "{value}"

//series.tooltip.pointerOrientation = "vertical";

chart.cursor = new am4charts.XYCursor();
chart.cursor.snapToSeries = series;
chart.cursor.xAxis = dateAxis;

//chart.scrollbarY = new am4core.Scrollbar();
//chart.scrollbarX = new am4core.Scrollbar();

}); // end am4core.ready()
</script>
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

var chart = am4core.create("line-6", am4charts.XYChart);

var data = [];
var value = 50;
for(var i = 0; i < 300; i++){
  var date = new Date();
  date.setHours(0,0,0,0);
  date.setDate(i);
  value -= Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 10);
  data.push({date:date, value: value});
}

chart.data = data;

// Create axes
var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
//dateAxis.renderer.minGridDistance = 60;

var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());

// Create series
var series = chart.series.push(new am4charts.LineSeries());
series.dataFields.valueY = "value";
series.dataFields.dateX = "date";
series.tooltipText = "{value}"

//series.tooltip.pointerOrientation = "vertical";

chart.cursor = new am4charts.XYCursor();
chart.cursor.snapToSeries = series;
chart.cursor.xAxis = dateAxis;

//chart.scrollbarY = new am4core.Scrollbar();
//chart.scrollbarX = new am4core.Scrollbar();

}); // end am4core.ready()
</script>
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
<style type="text/css">
g[opacity][aria-labelledby][transform] {
    display: none !important;
}  
</style>
</body>
