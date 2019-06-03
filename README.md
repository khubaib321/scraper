# scraper
Crawls the [YCombinator Jobs](https://news.ycombinator.com/jobs) site and fetches new jobs.

## Installation Instructions
Make sure latest version of LAMP stack is installed on the system. Can be downloaded from the website.

Run xampp server (Apache and MySQL). 

### `git clone https://github.com/khubaib321/scraper.git `
Clone repository. 

From the project root run the following commands:

### `cd src/data`
### `php -f install.php`
The above commands will create the required database on MySQL server running on localhost. 

From the project root navigate to directory `src/data/config`.
Open file `local.php` and update database credentials according to the system and change value agains `database` key to 'scraper'.

## Run Scraper
From the project root run the following commands:

### `cd src/main`
### `php -f main.php`
The scraper will run and save the information in the database. It will keep running until it cannot find more jobs or the cannot connect to the [website](https://news.ycombinator.com/jobs).

Uses library [PHP Simple HTML DOM Parser](https://simplehtmldom.sourceforge.io/) for fetching the webpage.
- A HTML DOM parser written in PHP5+ let you manipulate HTML in a very easy way!
- Require PHP 5+.
- Supports invalid HTML.
- Find tags on an HTML page with selectors just like jQuery.
- Extract contents from HTML in a single line.


# Assumptions
- This is a backend job only. Does not require any frontend user interface.
- Scraper is allowed to run without any interruptions until it stops by itself.

Thank you.
