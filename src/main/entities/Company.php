<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Company
 *
 * @author Khubaib-LT
 */
require_once '../data/DBManager.php';

class Company
{

    public $id = '';
    public $companyName = '';
    public $companyLocation = '';
    private $tableName = 'companies';

    public function __construct($name = '', $location = '')
    {
        $this->companyName = $name;
        $this->companyLocation = $location;
    }

    /**
     * Insert record into companies table
     * @return boolean | string
     */
    public function save()
    {
        if (empty($this->companyName)) {
            return false;
        }
        if ($this->loadCompany()) {
            return $this->id;
        }
        $dbMan = new DBManager();
        $this->id = $dbMan->insert([
            'table' => $this->tableName,
            'columnValuePairs' => [
                'name' => $this->companyName,
                'location' => $this->companyLocation,
            ],
        ]);
        return $this->id;
    }

    /**
     * Add a job in this company
     * @param string $id
     * @param Job $job
     * @return boolean | integer
     */
    public function linkJob($id, &$job)
    {
        if (empty($id) ||
            empty($job->id) ||
            empty($this->id)
        ) {
            return false;
        }
        if ($this->isJobLinked($job)) {
            return true;
        }
        $table = 'companies_jobs';
        $dbMan = new DBManager();
        $dbMan->insert([
            'table' => $table,
            'columnValuePairs' => [
                'id' => $id,
                'company_id' => $this->id,
                'job_id' => $job->id,
            ],
        ]);
        return true;
    }

    /**
     * Load company if exists in database
     * @return boolean
     */
    public function loadCompany()
    {
        $sql = "SELECT id FROM {$this->tableName} WHERE name = ? AND location = ?";
        $dbMan = new DBManager();
        $result = $dbMan->executeRawQuery($sql, [
            $this->companyName,
            $this->companyLocation,
        ]);
        if (isset($result[0]['id']) && !empty($result[0]['id'])) {
            $this->id = $result[0]['id'];
            return true;
        }
        $this->id = '';
        return false;
    }

    /**
     * Check if job has already been linked to this company
     * @param Job $job
     * @return boolean
     */
    public function isJobLinked(&$job)
    {
        $table = 'companies_jobs';
        $sql = "SELECT id FROM {$table} WHERE company_id = ? AND job_id = ?";
        $dbMan = new DBManager();
        $result = $dbMan->executeRawQuery($sql, [
            $this->id,
            $job->id,
        ]);
        return isset($result[0]['id']) && !empty($result[0]['id']) ?
            true : false;
    }
}
