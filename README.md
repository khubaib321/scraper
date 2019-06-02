# scraper
Crawls the [YCombinator Jobs](https://news.ycombinator.com/jobs) site and fetches new jobs.

## Installation Instructions
Make sure latest version of LAMP stack is installed on the system. Can be downloaded from the website.

Run xampp server (Apache and MySQL). 

### `git clone https://github.com/khubaib321/scraper.git `
Clone repository. 

From the project root navigate to directory `src/data/config`.
Open file `local.php` and update database credentials according to the system. Do not modify value for key `database`.

From the project root run the following commands:

### `cd src/data`
### `php -f install.php`
The above commands will create the database on MySQL server running on localhost. 

## Run Scraper
From the project root run the following commands:

### `cd src/main`
### `php -f main.php`
The scraper will run and save the information in the database. It will keep running until it cannot find more jobs or the cannot connect to the [website](https://news.ycombinator.com/jobs).

Thank you.