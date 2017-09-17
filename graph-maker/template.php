<!DOCTYPE HTML>
<html>
<head>
    <title>Craigslist cars for sale</title>
    <style>
        fieldset p {font-weight: bold;}
        fieldset {position: relative;}
        button {position: absolute; right: 1em; bottom: 1em; font-size: 48pt; background: none;}
    </style>
    <script type="text/javascript">

    var allData = [ DATA_GOES_HERE ];

    function updateGraph() {
        var filteredData = [], tmp, item;

        for (var i in allData) {
            item = allData[i];

            // Check filtering
            tmp = document.getElementById("vehicleTitle").value;
            if (tmp && tmp != item.vehicleTitle)
                continue;
            tmp = document.getElementById("transmission").value;
            if (tmp && tmp != item.transmission)
                continue;
            tmp = item.carModel;
            if (item.carModel == "Fiesta" && !document.getElementById("carModelFordFiesta").checked)
                continue;

            // Compute key (group name) for segmenting
            var key = '';
            if (document.getElementById("segCarModel").checked) {
                key = item.carModel;
            }
            if (document.getElementById("segModelYear").checked) {
                key += (key == "" ? "" : "+");
                tmp = Math.floor(item.modelYear / 5) * 5;
                key += tmp + "-" + (tmp + 4);
            }
            if (document.getElementById("segMileage").checked) {
                key += (key == "" ? "" : "+");
                tmp = Math.floor(item.mileage / 50) * 50;
                key += tmp + "k-" + (tmp + 49) + "k";
            }
            if (key == "") {
                key = "All cars";
            }

            // Group items for segmenting
            if (!filteredData[key]) {
                filteredData[key] = [];
            }
            filteredData[key].push(item);
        }

        // Format data for the chart tool
        var chartData = [];
        for (var i in filteredData) {
            chartData.push({
                type: "scatter",
                markerType: "square",
                toolTipContent: "<span style='\"'color: {color};'\"'><strong>{postTitle}</strong></span><br><img src='\"'{firstImage}'\"' style='\"'max-width: 200px; max-height: 200px;'\"'><br/><strong>${y}</strong> (expected ${expectedPrice}, score = {x})<br><strong>{mileage}k</strong> miles, <strong>{vehicleTitle}</strong> title, <strong>{transmission}</strong> transmission",
                name: i,
                showInLegend: true,
                dataPoints: filteredData[i],
                click: function(e) {
                    window.open(e.dataPoint.link, '_blank');
                }
            });
        }

        var chart = new CanvasJS.Chart("chartContainer",
        {
            title:{
                text: "Select cars recently for sale on Craigslist (scroll down for settings)",
                fontSize: 20
            },
            animationEnabled: false,
            axisX: {
                title:"Mileage + age (5,000 extra miles per year old)",
                titleFontSize: 16
                
            },
            axisY:{
                title: "Listing price",
                titleFontSize: 16
            },
            legend: {
                verticalAlign: 'bottom',
                horizontalAlign: "center"
            },
            data: chartData,
            legend:{
                cursor:"pointer",
                itemclick : function(e) {
                  if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                    e.dataSeries.visible = false;              
                  }
                  else {
                    e.dataSeries.visible = true;              
                  }
                  chart.render();
                }
            }
        });

        chart.render();
    }

    window.onload = function () {
        updateGraph();

        var elements = document.querySelectorAll('select');
        for (var i = 0; i < elements.length; i++)
            elements[i].onchange = updateGraph;

        elements = document.querySelectorAll('input');
        for (var i = 0; i < elements.length; i++)
            elements[i].onchange = updateGraph;
    }
</script>
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</head>
<body>
    <div id="chartContainer" style="height: 99vh; width: 99vw;">
    </div>

    <form>
        <div>
            <fieldset>
                <!-- <button onclick="updateGraph(); return false">Update graph</button> -->

                <p>Data filtering</p>
                <select id="vehicleTitle">
                    <option value="">Filter by car title...</option>
                    <option value="clean">Only show clean titles</option>
                    <option value="salvage">Only show salvage titles</option>
                </select>
                <select id="transmission">
                    <option value="">Filter by transmission...</option>
                    <option value="automatic">Only show automatic transmissions</option>
                    <option value="manual">Only show manual transmissions</option>
                </select>
                <br>
                <?php
                    require_once __DIR__ . '/../metadata/CarModels.php';
                    $models = new CarModels();
                    $models->onEach(function ($make, $model, $info) {
                        echo "<label>Show $make $model <input type=\"checkbox\" id=\"carModel$make$model\" checked></label><br>";
                    });
                ?>

                <p>Data segmenting</p>
                <label>Segment by car model <input type="checkbox" id="segCarModel"></label>
                <label>Segment by model year <input type="checkbox" id="segModelYear"></label>
                <label>Segment by miles <input type="checkbox" id="segMileage"></label>

                <br>
                <br>
                <br>
            </fieldset>
        </div>
    </form>
</body>
</html>