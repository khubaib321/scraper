<?php
require_once 'scraper/JobScraper.php';

$jobScraper = new JobScraper();
$jobScraper->startCrawling('https://news.ycombinator.com/jobs', true);
