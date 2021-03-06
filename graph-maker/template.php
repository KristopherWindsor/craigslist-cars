<!DOCTYPE HTML><?php require_once __DIR__ . '/../metadata/CarModels.php'; require_once __DIR__ . '/../metadata/CraigslistSites.php'; ?>
<html>
<head>
    <title>Graphs - recent used car listings on Craigslist (sfbay)</title>
    <style>
        body, html {margin: 0; padding: 0; max-width: 100vw; overflow-x: hidden;}

        #hint {
            position: absolute; bottom: 0; right: 5em; max-width: 200px; padding: 1em;
            background-color: #8c8;
        }

        .controls {background-color: #d7dbf7; padding: 1em;}
        .controls p {font-weight: bold;}
        .controls select {width: 20em;}
        .controls td {vertical-align: top; padding-right: 2em;}

        .tagList {background-color: #fff; margin: 0; padding: .5em; max-width: 30vw;}
        .tagList li {
            list-style-type: none; display: inline-block; padding: 2px 4px;
            border: 1px solid #ccc; margin: .4em; font-size: 14px;
        }
        .tagList li:after {content: "\274C"; font-size: 10px; margin: 0 2px 0 .5em;}
        .tagList li:first-child:after {content: ""; display: none;}
        .tagList li:hover {cursor: pointer;}
        .tagList li:first-child:hover {cursor: default;}
        .tagList input {border: none;}

        .segmenting label {display: inline-block;}
    </style>
    <script type="text/javascript">

    var allData = [ DATA_GOES_HERE ];

    var makeModelFilters = {
        showAll: false,
        makes: {},
        models: {}
    };

    var locationFilters = {
        showAll: false,
        states: {},
        locations: {}
    };

    function updateGraph() {
        var filteredData = [], tmp, item;

        var dimensionFnNames = {
            "mileage": "Mileage",
            "datePosted": "Time of post (X hours ago)",
            "modelYear": "Model year",
            "price": "Price",
            "fn1": "Fn1: Mileage + 5000 mi. per year old",
            "fn2": "Fn2: Expected price based on fn1"
        };
        var dimensionFns = {
            "mileage": function (item) {
                return item.mileage;
            },
            "datePosted": function (item) {
                return (Date.now() - (new Date(item.datePosted))) / 1000 / 3600;
            },
            "modelYear": function (item) {
                return item.modelYear;
            },
            "price": function (item) {
                return item.price;
            },
            "fn1": function (item) {
                return item.mileage + (2018 - item.modelYear) * 5000;
            },
            "fn2": function (item) {
                var fn1 = item.mileage + (2018 - item.modelYear) * 5000;
                return 66854 - 5142 * Math.log(fn1);
            }
        };
        var xDimFn = dimensionFns[document.getElementById("xDim").value];
        var yDimFn = dimensionFns[document.getElementById("yDim").value];

        for (var i in allData) {
            item = allData[i];

            // Check filtering
            if (!locationFilters.showAll &&
                !locationFilters.states[item.locationState.toLowerCase()] &&
                !locationFilters.locations[item.location.toLowerCase()])
                continue;
            tmp = document.getElementById("filPostDate").value;
            if (tmp) {
                tmp = new Date(Date.now() - parseFloat(tmp) * 3600 * 1000);
                if (new Date(item.datePosted) < tmp)
                    continue;
            }
            tmp = document.getElementById("filPostTitle").value.toLowerCase();
            if (tmp && !item.postTitle.toLowerCase().includes(tmp))
                continue;
            if (!makeModelFilters.showAll &&
                !makeModelFilters.makes[item.carMake.toLowerCase()] &&
                !makeModelFilters.models[item.carMake.toLowerCase() + " " + item.carModel.toLowerCase()])
                continue;
            tmp = document.getElementById("filModelSize").value;
            if (tmp && item.modelSize != tmp)
                continue;
            tmp = parseFloat(document.getElementById("filModelYearMin").value);
            if (tmp && item.modelYear < tmp)
                continue;
            tmp = parseFloat(document.getElementById("filModelYearMax").value);
            if (tmp && item.modelYear > tmp)
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
            /*if (document.getElementById("segLocation").checked) {
                key = item.location;
            }
            if (document.getElementById("segLocationState").checked) {
                key += (key == "" ? "" : "+");
                key += item.locationState;
            }*/
            if (document.getElementById("segDatePosted").checked) {
                key += (key == "" ? "" : "+");
                tmp = (Date.now() - (new Date(item.datePosted))) / 1000 / 3600;
                if (tmp < 4)
                    key += "Last 4 hours";
                else if (tmp < 8)
                    key += "2-8 hours ago";
                else if (tmp < 24)
                    key += "8-24 hours ago";
                else if (tmp < 72)
                    key += "2-3 days ago";
                else if (tmp < 168)
                    key += "3-7 days ago";
                else if (tmp < 336)
                    key += "1-2 weeks ago";
                else
                    key += "2+ weeks ago";
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
                    key += "<$1000";
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
            } else if (key == "keys")
                key = "keys ";

            // Group items for segmenting
            if (!filteredData[key]) {
                filteredData[key] = [];
            }
            item.x = xDimFn(item);
            item.y = yDimFn(item);
            filteredData[key].push(item);
        }

        // Format data for the chart tool
        var chartData = [];
        for (var i in filteredData) {
            chartData.push({
                type: "scatter",
                markerType: "square",
                toolTipContent: "<span style='\"'color: {color};'\"'><strong>{postTitle}</strong></span><br><img src='\"'{firstImage}'\"' style='\"'max-width: 200px; max-height: 200px;'\"'><br/><strong>${price}</strong><br><strong>{mileage}</strong> miles, <strong>{vehicleTitle}</strong> title, <strong>{transmission}</strong> transmission",
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
                text: dimensionFnNames[document.getElementById("xDim").value] + " vs. " +
                      dimensionFnNames[document.getElementById("yDim").value] +
                      " - recent used car listings on Craigslist (sfbay)",
                fontSize: 18
            },
            animationEnabled: false,
            axisX: {
                title: dimensionFnNames[document.getElementById("xDim").value],
                titleFontSize: 16
            },
            axisY:{
                title: dimensionFnNames[document.getElementById("yDim").value],
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

    function searchLocation(skipGraphUpdate) {
        var searchTerm = document.getElementById("selectLocation").value.toUpperCase();

        var addTagToDataModel = function (tagText) {
            tagText = tagText.toLowerCase();

            if (tagText == "show all") {
                if (locationFilters.showAll)
                    return false;
                locationFilters.showAll = true;
            } else if (tagText.indexOf('(all)') > 0) {
                var index = tagText.replace(" (all)", "");
                if (locationFilters.states[index])
                    return false;
                locationFilters.states[index] = true;
            } else {
                if (locationFilters.locations[tagText])
                    return false;
                locationFilters.locations[tagText] = true;
            }
            return true;
        };

        var removeTagFromDataModel = function (li) {
            var tagText = li.innerHTML.toLowerCase();

            if (tagText == "show all")
                locationFilters.showAll = false;
            else if (tagText.indexOf('(all)') > 0) {
                var index = tagText.replace(" (all)", "");
                locationFilters.states[index] = false;
            } else
                locationFilters.locations[tagText] = false;
        };

        // If it matches a search option, then add it to the search list
        var datalistElement = document.getElementById("locations"), found = false;
        for (var i in datalistElement.children) {
            if (!datalistElement.children[i].value)
                continue;

            var j = datalistElement.children[i].value;
            if (searchTerm == j.toUpperCase()) {
                // Reset the search box
                document.getElementById("selectLocation").value = "";

                if (addTagToDataModel(j)) {
                    // If adding anything, make sure "Show all" tag is removed
                    for (var k in document.getElementById("filLocation").children) {
                        var kthChild = document.getElementById("filLocation").children[k];
                        if (kthChild.innerHTML == "Show all") {
                            removeTagFromDataModel(kthChild);
                            document.getElementById("filLocation").removeChild(kthChild);
                        }
                    }

                    // Add the tag to the UI
                    var entry = document.createElement('li');
                    entry.appendChild(document.createTextNode(j));
                    entry.onclick = function () {
                        removeTagFromDataModel(entry);
                        document.getElementById("filLocation").removeChild(entry);
                        updateGraph();
                    };
                    document.getElementById("filLocation").appendChild(entry);

                    // Update graph
                    if (!skipGraphUpdate)
                        updateGraph();
                }
                return;
            }
        }
    }

    function searchMakeAndModel() {
        var searchTerm = document.getElementById("selectMakeAndModel").value.toUpperCase();

        var addTagToDataModel = function (tagText) {
            tagText = tagText.toLowerCase();

            if (tagText == "show all") {
                if (makeModelFilters.showAll)
                    return false;
                makeModelFilters.showAll = true;
            } else if (tagText.indexOf('(all models)') > 0) {
                var index = tagText.replace(" (all models)", "");
                if (makeModelFilters.makes[index])
                    return false;
                makeModelFilters.makes[index] = true;
            } else {
                if (makeModelFilters.models[tagText])
                    return false;
                makeModelFilters.models[tagText] = true;
            }
            return true;
        };

        var removeTagFromDataModel = function (li) {
            var tagText = li.innerHTML.toLowerCase();

            if (tagText == "show all")
                makeModelFilters.showAll = false;
            else if (tagText.indexOf('(all models)') > 0) {
                var index = tagText.replace(" (all models)", "");
                makeModelFilters.makes[index] = false;
            } else
                makeModelFilters.models[tagText] = false;
        };

        // If it matches a search option, then add it to the search list
        var datalistElement = document.getElementById("makesAndModels"), found = false;
        for (var i in datalistElement.children) {
            if (!datalistElement.children[i].value)
                continue;

            var j = datalistElement.children[i].value;
            if (searchTerm == j.toUpperCase()) {
                // Reset the search box
                document.getElementById("selectMakeAndModel").value = "";

                if (addTagToDataModel(j)) {
                    // If adding anything, make sure "Show all" tag is removed
                    for (var k in document.getElementById("filMakeModel").children) {
                        var kthChild = document.getElementById("filMakeModel").children[k];
                        if (kthChild.innerHTML == "Show all") {
                            removeTagFromDataModel(kthChild);
                            document.getElementById("filMakeModel").removeChild(kthChild);
                        }
                    }

                    // Add the tag to the UI
                    var entry = document.createElement('li');
                    entry.appendChild(document.createTextNode(j));
                    entry.onclick = function () {
                        removeTagFromDataModel(entry);
                        document.getElementById("filMakeModel").removeChild(entry);
                        updateGraph();
                    };
                    document.getElementById("filMakeModel").appendChild(entry);

                    // Update graph
                    updateGraph();
                }
                return;
            }
        }
    }

    window.onload = function () {
        var elements = document.querySelectorAll('select');
        for (var i = 0; i < elements.length; i++)
            elements[i].onchange = updateGraph;

        elements = document.querySelectorAll('input');
        for (var i = 0; i < elements.length; i++)
            if (elements[i].id != "selectMakeAndModel" && elements[i].id != "selectLocation")
                elements[i].onchange = updateGraph;

        // Triggers a graph update
        searchLocation(true);
        searchMakeAndModel();

        window.setTimeout(
            function (e) {
                document.getElementById("hint").remove();
            },
            5000
        );
    }
</script>
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</head>
<body>
    <div id="chartContainer" style="height: 100vh">
    </div>

    <div id="hint">
        Scroll down to change the graph settings
    </div>

    <form>
        <div class="controls">
            <p>Data filtering</p>

            <table>
                <tr>
                    <td>
                        <input type="text" id="filPostTitle" placeholder="Filter by post title...">
                        <br><br>
                        <select id="filPostDate">
                            <option value="">Filter by date of post...</option>
                            <!-- <option value="4">Last 4 hours</option>
                            <option value="8">Last 8 hours</option> -->
                            <option value="24">Last 24 hours</option>
                            <option value="72">Last 3 days</option>
                            <option value="168">Last 7 days</option>
                            <option value="336">Last 14 days</option>
                        </select>
                        <br><br>

                        <!-- <div>Filter by location (state or site)...</div> -->
                        <br>
                        <datalist id="locations">
                            <option value="Show all">
                            <?php
                                $craigslistSites = new CraigslistSites();
                                foreach ($craigslistSites->getAllStates() as $state) {
                                    echo '<option value="' . $state . ' (all)">';
                                }
                                foreach ($craigslistSites->getAllSiteUrls() as $url) {
                                    $shortLocation = $craigslistSites->convertUrlToShortLocation($url);
                                    echo '<option value="' . $shortLocation . '">';
                                }
                            ?>
                        </datalist>
                        <ul id="filLocation" class="tagList" style="display: none">
                            <li>
                                <input type="text" id="selectLocation" list="locations"
                                    onInput="searchLocation()"
                                    placeholder="Add item..." value="Show all">
                            </li>
                        </ul>
                    </td>
                    <td>
                        <input type="text" id="filModelYearMin" placeholder="Filter by year (min)...">
                        <input type="text" id="filModelYearMax" placeholder="Filter by year (max)...">
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
                        <div>Choose car makes and models to display...</div>
                        <br>
                        <datalist id="makesAndModels">
                            <option value="Show all">
                            <?php
                                $models = new CarModels();
                                foreach ($models->getAllMakes() as $make) {
                                    echo '<option value="' . $make . ' (all models)">';
                                }
                                $models->onEach(function ($make, $model) {
                                    echo '<option value="' . $make . ' ' . $model . '">';
                                });
                            ?>
                        </datalist>
                        <ul id="filMakeModel" class="tagList">
                            <li>
                                <input type="text" id="selectMakeAndModel" list="makesAndModels"
                                    onInput="searchMakeAndModel()"
                                    placeholder="Add item..." value="Show all">
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>

            <p>Data segmenting</p>
            <div class="segmenting">
                <!-- <label><input type="checkbox" id="segLocation"> Post location site</label>
                <label><input type="checkbox" id="segLocationState"> Post location state</label> -->
                <label><input type="checkbox" id="segDatePosted"> Date of post</label>
                <label><input type="checkbox" id="segCarMake"> Car make</label>
                <label><input type="checkbox" id="segCarModel"> Car model</label>
                <label><input type="checkbox" id="segModelYear"> Model year</label>
                <label><input type="checkbox" id="segVehicleTitle" checked> Vehicle title</label>
                <label><input type="checkbox" id="segTransmission"> Transmission type</label>
                <label><input type="checkbox" id="segMileage"> Mileage</label>
                <label><input type="checkbox" id="segPrice"> Price</label>
                <label><input type="checkbox" id="segModelSize"> Vehicle size</label>
            </div>

            <p>Graph dimensions</p>
            X dimension:
            <select id="xDim">
                <option value="mileage">Mileage</option>
                <option value="datePosted">Time of post (X hours ago)</option>
                <option value="modelYear">Model year</option>
                <option value="price">Price</option>

                <option value="fn1">Fn1: Mileage + 5000 mi. per year old</option>
                <option value="fn2">Fn2: Expected price based on fn1</option>
            </select>
            <br>
            Y dimension:
            <select id="yDim">
                <option value="price">Price</option>
                <option value="modelYear">Model year</option>
                <option value="datePosted">Time of post (X hours ago)</option>
                <option value="mileage">Mileage</option>

                <option value="fn1">Fn1: Mileage + 5000 mi. per year old</option>
                <option value="fn2">Fn2: Expected price based on fn1</option>
            </select>

            <br><br><br>
            <br><br><br>
            <br><br><br>
        </div>
    </form>
</body>
</html>
