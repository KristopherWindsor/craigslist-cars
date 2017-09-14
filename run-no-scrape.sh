php make-spreadsheet.php 'pages/yaris'      | grep -v '""' > yaris.csv
php make-spreadsheet.php 'pages/corolla'    | grep -v '""' > corolla.csv
php make-spreadsheet.php 'pages/fordfocus'  | grep -v '""' > fordfocus.csv
php make-spreadsheet.php 'pages/fordfiesta' | grep -v '""' > fordfiesta.csv
php make-spreadsheet.php 'pages/hondafit'   | grep -v '""' > hondafit.csv


head -n1 yaris.csv > all.csv
cat yaris.csv corolla.csv fordfocus.csv fordfiesta.csv hondafit.csv | grep -v '""' | grep -vi greylist >> all.csv

head -n1 yaris.csv > all-clean-automatic.csv
cat yaris.csv corolla.csv fordfocus.csv fordfiesta.csv hondafit.csv | grep -v '""' | grep -v salvage | grep -v rebuilt | grep -v manual | grep -vi greylist >> all-clean-automatic.csv

php make-web-graph.php all.csv > render.html
