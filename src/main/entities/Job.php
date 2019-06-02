<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Job
 *
 * @author Khubaib-LT
 */
require_once '../data/DBManager.php';

class Job
{

    public $id = '';
    public $jobName = '';
    private $tableName = 'jobs';

    public function __construct($jobName = '')
    {
        $this->jobName = $jobName;
    }

    /**
     * Insert record into
     * @return boolean
     */
    public function save()
    {
        if (empty($this->jobName)) {
            return false;
        }
        if ($this->loadJob()) {
            return $this->id;
        }
        $this->id = DBManager::insert([
                'table' => $this->tableName,
                'columnValuePairs' => [
                    'name' => $this->jobName,
                ],
        ]);
        return $this->id;
    }

    /**
     * Load job if exists in database
     * @return boolean
     */
    public function loadJob()
    {
        $sql = "SELECT id FROM {$this->tableName} WHERE name = ?";
        $result = DBManager::executeRawQuery($sql, [
                $this->jobName,
        ]);
        if (isset($result[0]['id']) && !empty($result[0]['id'])) {
            $this->id = $result[0]['id'];
            return true;
        }
        $this->id = '';
        return false;
    }
}
