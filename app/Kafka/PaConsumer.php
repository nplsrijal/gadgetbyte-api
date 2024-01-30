<?php

namespace App\Kafka;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\SubOrganization;
use App\Models\User;

class PaConsumer
{

    const CREATE = 'c';
    const UPDATE = 'u';

    private $consumedData = [];

    public function __construct($data)
    {
        $this->consumedData = $data;
    }


    private function getOpertaion(): string
    {
        return $this->consumedData['op'];
    }

    public function directSyncModel(string $modelSlug)
    {
        try {
            if ($this->getOpertaion() == self::CREATE) {
                $data = $this->getCurrentData();
                info("Direct" . $data);
                // $model = $this->getModelBySlug($modelSlug);
                // $modelData = $model::create($data);
            } else {
                $modelData = [];
            }

            return $modelData;
        } catch (\Exception $e) {
            info($e->getMessage());
            return $e->getMessage();
        }
    }

    public function syncUser()
    {
        try {
            if ($this->getOpertaion() == self::CREATE) {
                $data = $this->getCurrentData();
                $user = User::create($data);
            } else {
                $user = [];
            }
            return $user;
        } catch (\Exception $e) {
            info($e->getMessage());
            return $e->getMessage();
        }
    }

    public function syncDepartment()
    {
        try {
            if ($this->getOpertaion() == self::CREATE) {
                $data = $this->getCurrentData();
                $department = Department::create($data);
            } else {
                $department = [];
            }
            return $department;
        } catch (\Exception $e) {
            info($e->getMessage());
            return $e->getMessage();
        }
    }

    public function syncEmployee()
    {
        try {
            if ($this->getOpertaion() == self::CREATE) {
                $data = $this->getCurrentData();
                $employee = Employee::create($data);
            } else {
                $employee = [];
            }
            return $employee;
        } catch (\Exception $e) {
            info($e->getMessage());
            return $e->getMessage();
        }
    }

    public function syncOrganization()
    {
        try {
            if ($this->getOpertaion() == self::CREATE) {
                $data = $this->getCurrentData();
                $organization = Organization::create($data);
            } else {
                $organization = [];
            }
            return $organization;
        } catch (\Exception $e) {
            info($e->getMessage());
            return $e->getMessage();
        }
    }

    public function syncSubOrganization()
    {
        try {
            if ($this->getOpertaion() == self::CREATE) {
                $data = $this->getCurrentData();
                $subOrganization = SubOrganization::create($data);
            } else {
                $subOrganization = [];
            }
            return $subOrganization;
        } catch (\Exception $e) {
            info($e->getMessage());
            return $e->getMessage();
        }
    }

    private function getCurrentData(): ?array
    {
        return array_key_exists('after', $this->consumedData) ? $this->consumedData['after'] : [];
    }
}
