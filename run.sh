# Scraper is not run here -- it should be run separately via cron
# php scraper/scrape.php

# CSV Parser
php csv-maker/make-spreadsheet.php '../pages' | grep -v '""' > cars-all.csv
cat cars-all.csv | grep -v 'greylisted' > cars.csv

# Interactive Graph
php make-web-graph.php "cars.csv" > render.html
