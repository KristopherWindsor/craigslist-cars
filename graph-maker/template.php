<!DOCTYPE HTML>
<html>
<head>
    <title>Craigslist cars for sale</title>
    <style>
        body, html {margin: 0; padding: 0; max-width: 100vw; overflow-x: hidden;}
        fieldset p {font-weight: bold;}
        fieldset {position: relative;}
        button {position: absolute; right: 1em; bottom: 1em; font-size: 48pt; background: none;}

        select {width: 20em;}
        td {vertical-align: top;}
    </style>
    <script type="text/javascript">

    var allData = [ DATA_GOES_HERE ];

    function updateGraph() {
        var filteredData = [], tmp, item;

        for (var i in allData) {
            item = allData[i];

            // Check filtering
            tmp = document.getElementById("filLocation").value;
            if (tmp && tmp != item.location)
                continue;
            tmp = document.getElementById("filPostTitle").value.toLowerCase();
            if (tmp && !item.postTitle.toLowerCase().includes(tmp))
                continue;
            tmp = document.getElementById("filModel" + item.carMake + item.carModel).checked;
            if (!tmp)
                continue;
            tmp = document.getElementById("filModelSize").value;
            if (tmp && item.modelSize != tmp)
                continue;
            tmp = document.getElementById("filModelYear").value;
            if (tmp && "" + item.modelYear != tmp)
                continue;
            tmp = document.getElementById("filVehicleTitle").value;
            if (tmp && tmp != item.vehicleTitle)
                continue;
            tmp = document.getElementById("filTransmission").value;
            if (tmp && tmp != item.transmission)
                continue;
            tmp = parseFloat(document.getElementById("filMileageMin").value);
            if (!isNaN(tmp) && tmp > item.mileage)
                continue;
            tmp = parseFloat(document.getElementById("filMileageMax").value);
            if (!isNaN(tmp) && tmp < item.mileage)
                continue;
            tmp = parseFloat(document.getElementById("filPriceMin").value);
            if (!isNaN(tmp) && tmp > item.price)
                continue;
            tmp = parseFloat(document.getElementById("filPriceMax").value);
            if (!isNaN(tmp) && tmp < item.price)
                continue;



            // Compute key (group name) for segmenting
            var key = '';
            if (document.getElementById("segLocation").checked) {
                key = item.location;
            }
            if (document.getElementById("segCarMake").checked) {
                key += (key == "" ? "" : "+");
                key += item.carMake;
            }
            if (document.getElementById("segCarModel").checked) {
                key += (key == "" ? "" : "+");
                key += item.carModel;
            }
            if (document.getElementById("segModelSize").checked) {
                key += (key == "" ? "" : "+");
                key += item.modelSize;
            }
            if (document.getElementById("segModelYear").checked) {
                key += (key == "" ? "" : "+");
                tmp = Math.floor(item.modelYear / 5) * 5;
                key += tmp + "-" + (tmp + 4);
            }
            if (document.getElementById("segVehicleTitle").checked) {
                key += (key == "" ? "" : "+");
                key += item.vehicleTitle;
            }
            if (document.getElementById("segTransmission").checked) {
                key += (key == "" ? "" : "+");
                key += item.transmission;
            }
            if (document.getElementById("segMileage").checked) {
                key += (key == "" ? "" : "+");
                tmp = Math.floor(item.mileage / 50000) * 50;
                key += tmp + "k-" + (tmp + 49) + "k";
            }
            if (document.getElementById("segPrice").checked) {
                key += (key == "" ? "" : "+");
                if (item.price < 1000)
                    key += "< $1000";
                else if (item.price < 3000)
                    key += "$1-3k";
                else if (item.price < 5000)
                    key += "$3-5k";
                else if (item.price < 8000)
                    key += "$5-8k";
                else if (item.price < 16000)
                    key += "$8-16k";
                else if (item.price < 32000)
                    key += "$16-32k";
                else
                    key += "$32k+";
            }
            if (key == "") {
                key = "All cars";
            }

            // Group items for segmenting
            if (!filteredData[key]) {
                filteredData[key] = [];
            }
            item.x = item.myScore;
            item.y = item.price;
            filteredData[key].push(item);
        }

        // Format data for the chart tool
        var chartData = [];
        for (var i in filteredData) {
            chartData.push({
                type: "scatter",
                markerType: "square",
                toolTipContent: "<span style='\"'color: {color};'\"'><strong>{postTitle}</strong></span><br><img src='\"'{firstImage}'\"' style='\"'max-width: 200px; max-height: 200px;'\"'><br/><strong>${y}</strong> (expected ${expectedPrice}, score = {x})<br><strong>{mileage}</strong> miles, <strong>{vehicleTitle}</strong> title, <strong>{transmission}</strong> transmission",
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
    <div id="chartContainer" style="height: 100vh">
    </div>

    <form>
        <div>
            <fieldset>
                <p>Data filtering</p>

                <table>
                    <tr>
                        <td>
                            <select id="filLocation">
                                <option value="">Filter by location...</option>
                                <option>bakersfield</option>
                                <option>chico</option>
                                <option>fresno</option>
                                <option>gold country</option>
                                <option>hanford</option>
                                <option>humboldt</option>
                                <option>imperial co</option>
                                <option>inland empire</option>
                                <option>los angeles</option>
                                <option>mendocino</option>
                                <option>merced</option>
                                <option>modesto</option>
                                <option>monterey bay</option>
                                <option>orange co</option>
                                <option>palm springs</option>
                                <option>redding</option>
                                <option>reno</option>
                                <option>sacramento</option>
                                <option>san diego</option>
                                <option>san luis obispo</option>
                                <option>santa barbara</option>
                                <option>santa maria</option>
                                <option>SF bay area</option>
                                <option>siskiyou</option>
                                <option>stockton</option>
                                <option>susanville</option>
                                <option>ventura</option>
                                <option>visalia-tulare</option>
                                <option>yuba-sutter</option>
                            </select>
                            <br><br>
                            <input type="text" id="filPostTitle" placeholder="Filter by post title...">
                            <br><br>
                            <input type="text" id="filModelYear" placeholder="Filter by year...">
                            <br><br>
                            <select id="filVehicleTitle">
                                <option value="">Filter by car title...</option>
                                <option value="clean">Only show clean titles</option>
                                <option value="salvage">Only show salvage titles</option>
                            </select>
                            <br><br>
                            <select id="filTransmission">
                                <option value="">Filter by transmission...</option>
                                <option value="automatic">Only show automatic transmissions</option>
                                <option value="manual">Only show manual transmissions</option>
                            </select>
                            <br><br>
                            <input type="text" id="filMileageMin" placeholder="Filter by mileage (min)...">
                            <input type="text" id="filMileageMax" placeholder="Filter by mileage (max)...">
                            <br><br>
                            <input type="text" id="filPriceMin" placeholder="Filter by price (min)...">
                            <input type="text" id="filPriceMax" placeholder="Filter by price (max)...">
                            <br><br>
                            <select id="filModelSize">
                                <option value="">Filter by vehicle size...</option>
                                <option>sub-compact</option>
                                <option>compact</option>
                                <option>mid-size</option>
                                <option>full-size</option>
                            </select>

                        </td>
                        <td>

                            <?php
                                require_once __DIR__ . '/../metadata/CarModels.php';
                                $models = new CarModels();
                                $models->onEach(function ($make, $model, $info) {
                                    echo "<label><input type=\"checkbox\" id=\"filModel$make$model\" checked> Show $make $model</label><br>";
                                });
                            ?>

                        </td>
                    </tr>
                </table>

                <p>Data segmenting</p>
                <label><input type="checkbox" id="segLocation"> Segment by location</label>
                <label><input type="checkbox" id="segCarMake"> Segment by car make</label>
                <label><input type="checkbox" id="segCarModel"> Segment by car model</label>
                <label><input type="checkbox" id="segModelSize"> Segment by vehicle size</label>
                <label><input type="checkbox" id="segModelYear"> Segment by model year</label>
                <label><input type="checkbox" id="segVehicleTitle"> Segment by vehicle title</label>
                <label><input type="checkbox" id="segTransmission"> Segment by transmission type</label>
                <label><input type="checkbox" id="segMileage"> Segment by miles</label>
                <label><input type="checkbox" id="segPrice"> Segment by price</label>

                <br>
                <br>
                <br>
            </fieldset>
        </div>
    </form>
</body>
</html>
