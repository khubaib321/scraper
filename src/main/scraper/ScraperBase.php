<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ScraperBase
 *
 * @author Khubaib-LT
 */
require_once '../lib/simple_html_dom.php';

class ScraperBase
{

    public $htmlDom = NULL;
    protected $baseUrl = ''; // This can help avoid navigating to external links
    protected $currentPage = '';

    /**
     * Find and set base url
     * @param string $url
     */
    protected function setBaseUrl($url)
    {
        $urlParts = parse_url($url);
        $this->baseUrl = $urlParts['scheme'] . '://' . $urlParts['host'];
    }

    /**
     * Get host url
     * @return string $url
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Given a url, load the webpage dom into the $htmlDom object
     * @param string $url
     * @return boolean
     */
    public function loadPage($url)
    {
        if (empty($url)) {
            $this->htmlDom = NULL;
            return false;
        }
        try {
            $this->htmlDom = file_get_html($url);
        } catch (Exception $ex) {
            $this->htmlDom = NULL;
            echo $ex->getMessage();
        }

        return ($this->htmlDom === NULL) ? false : true;
    }
}
