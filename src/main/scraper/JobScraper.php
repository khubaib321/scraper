<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JobScraper
 *
 * @author Khubaib-LT
 */
require_once 'Scraperbase.php';
require_once 'entities/Job.php';
require_once 'entities/Company.php';

class JobScraper extends ScraperBase
{

    public $stop = false;
    public $jobName = '';
    public $companyName = '';
    public $companyLocation = '';

    public function startCrawling($url, $keepCrawling = false)
    {
        if (empty($url)) {
            return false;
        }
        $this->setBaseUrl($url);
        $this->currentPage = $url;
        $maxJobID = $this->getMaxJobID();
        echo 'MAX JOB ID => ', $maxJobID, "\n";
        $scraped = $this->scrapeCurrentPage($maxJobID);
        while ($scraped && $keepCrawling) {
            $moreLink = $this->htmlDom->find('tr td.title a.morelink');
            $this->currentPage = $this->baseUrl . '/' . $moreLink[0]->href;
            $scraped = $this->scrapeCurrentPage($maxJobID);
            $keepCrawling = !$this->stop;
        }
    }

    /**
     * Start scraping the url set in $webPage
     * @param int $maxJobID
     * @return boolean true if page was successfully loaded
     */
    private function scrapeCurrentPage($maxJobID)
    {
        echo "\n" . 'CONNECTING TO WEBPAGE => ', $this->currentPage, "\n";
        if ($this->stop) {
            return false;
        }
        if (!$this->loadPage($this->currentPage)) {
            return false;
        }
        if (!method_exists($this->htmlDom, 'find')) {
            return false;
        }

        require 'utils/commonWords.php';
        $jobLinks = $this->htmlDom->find('tr.athing td.title a.storylink');
        foreach ($jobLinks as $i => $a) {
            $id = $a->parent()->parent()->id;

            if ($maxJobID >= intval($id)) {
                // we have already saved this and all after that
                $this->stop = true;
                break;
            }

            $jobText = (trim($a->plaintext));
            $jobTextRelevant = trim(preg_replace('/\b(' . implode('|', $commonWords) . ')\b/i', '', $jobText));
            $this->findJobDetails($jobTextRelevant);

            $newJob = new Job($this->jobName);
            $newJob->save();

            $newCompany = new Company($this->companyName, $this->companyLocation);
            $newCompany->save();

            $newCompany->linkJob($id, $newJob);

            echo $id, ' => ', $jobTextRelevant, "\n";
        }
        return true;
    }

    /**
     * Extract job info from given string
     * @param string $string
     */
    public function findJobDetails($string)
    {
        $key = $this->findCompanyName($string);
        if (!empty($key)) {
            $this->jobName = $this->getStringBetween($string, " {$key} ", ' in ');
            $this->companyLocation = $this->getStringAfter($string, ' in ');
        }
    }

    /**
     * Extract company name from given string
     * @param string $string
     * @return word before which company name is found in the string
     */
    public function findCompanyName($string)
    {
        $breakWords = [
            'hire',
            'want',
            'need',
            'wants',
            'needs',
            'hiring',
            'looking'
        ];
        $key = '';
        foreach ($breakWords as $word) {
            $this->companyName = $this->getStringBefore($string, " {$word}");
            if (!empty($this->companyName)) {
                $key = $word;
                break;
            }
        }
        return $key;
    }

    /**
     * Get maximum job id already stored in DB
     * @return int
     */
    public function getMaxJobID()
    {
        $sql = 'SELECT MAX(id) as ID FROM companies_jobs';
        $result = DBManager::executeRawQuery($sql);
        if (isset($result[0]) &&
            isset($result[0]['ID'])
        ) {
            return intval($result[0]['ID']);
        }
        return 0;
    }

    /**
     * Get string content between two strings
     * @param string $content
     * @param string $str1
     * @param string $str2
     * @return string
     */
    function getStringBetween($content, $str1, $str2)
    {
        $strArray1 = preg_split("/{$str1}/i", $content);
        if (isset($strArray1[1])) {
            $strArray2 = preg_split("/{$str2}/i", $strArray1[1]);
            if (isset($strArray2[0]) &&
                count($strArray2) > 1
            ) {
                // count check ensures split was successful
                return preg_replace('/^\W+|\W+$/', '', $strArray2[0]);
            }
        }
        return '';
    }

    /**
     * Get string content before a word
     * @param string $content
     * @param string $str
     * @return string
     */
    function getStringBefore($content, $str)
    {
        $strArray = preg_split("/{$str}/i", $content);
        if (isset($strArray[0]) &&
            count($strArray) > 1
        ) {
            // count check ensures split was successful
            return trim($strArray[0]);
        }
        return '';
    }

    /**
     * Get string content after a word
     * @param string $content
     * @param string $str
     * @return string
     */
    function getStringAfter($content, $str)
    {
        $strArray = preg_split("/{$str}/i", $content);
        if (isset($strArray[1])) {
            return trim($strArray[1]);
        }
        return '';
    }
}
