<?php

namespace Foggyline\Office\Model\ResourceModel;
class Employee extends \Magento\Eav\Model\Entity\AbstractEntity
{
    protected function _construct()
    {
        $this->_read = 'foggyline_office_employee_read';
        $this->_write = 'foggyline_office_employee_write';
    }
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\Foggyline\Office\Model\Employee::ENTITY);
        }
        return parent::getEntityType();
    }
}