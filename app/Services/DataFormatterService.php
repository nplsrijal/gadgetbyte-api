<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class DataFormatterService
{
    private $data;
    private $operation = 'c';
    private $returnType = 'array';
    private $tableName = null;
    private $mappedData = [];

    public function __construct($data)
    {
        $this->data = $data;
    } 

    public function formatData()
    {
        $this->mappedData['before'] = null;
        $this->mappedData['after'] = json_decode($this->data,true);
        $this->mappedData['source']['db'] = 'pa';
        $this->mappedData['source']['schema'] = 'public';
        $this->mappedData['source']['table'] = $this->getTableName();
        $this->mappedData['op'] = $this->getOperation();
        return $this->getreturnType() == 'array' ? $this->mappedData : response()->json($this->mappedData);
    }


    private function getOperation() : ?string
    {
        return $this->operation;
    }

    public function setOperation($operation) : self
    {
        $this->operation = $operation;
        return $this;
    }

    private function getreturnType() : ?string
    {
        return $this->returnType;
    }

    public function setreturnType($returnType) : self
    {
        $this->returnType = $returnType;
        return $this;
    }

    private function getTableName() : ?string
    {
        return $this->tableName;
    }

    public function setTableName($tableName) : self
    {
        $this->tableName = $tableName;
        return $this;
    }
}
