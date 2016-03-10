<?php

namespace Foggyline\Office\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /* create our Department model entity table */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('foggyline_office_department'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false,
                    'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Name'
            )
            ->setComment('Foggyline Office Department Table');
        $setup->getConnection()->createTable($table);

        /* create the foggyline_office_employee_entity table for the Employee entity */
        $employeeEntity = \Foggyline\Office\Model\Employee::ENTITY;
        $table = $setup->getConnection()
            ->newTable($setup->getTable($employeeEntity . '_entity'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false,
                    'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'department_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Department Id'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Email'
            )
            ->addColumn(
                'first_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'First Name'
            )
            ->addColumn(
                'last_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                64,
                [],
                'Last Name'
            )
            ->setComment('Foggyline Office Employee Table');
        $setup->getConnection()->createTable($table);

        /* defining attribute value tables, we will focus only on a single, decimal type table. */
        $table = $setup->getConnection()
            ->newTable($setup->getTable($employeeEntity . '_entity_decimal'))
            ->addColumn(
                'value_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary' => true],
                'Value ID'
            )
            ->addColumn(
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Attribute ID'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Store ID'
            )
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Entity ID'
            )
            ->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                [],
                'Value'
            )

                /*
                 * ->addIndex
                 * UNIQUE KEY 'FOGGYLINE_OFFICE_EMPLOYEE_ENTT_DEC_ENTT_ID_ATTR_ID_STORE_ID' ('entity_id','attribute_id','store_id')
                 * KEY 'FOGGYLINE_OFFICE_EMPLOYEE_ENTITY_DECIMAL_STORE_ID' ('store_id')
                 * KEY 'FOGGYLINE_OFFICE_EMPLOYEE_ENTITY_DECIMAL_ATTRIBUTE_ID' ('attribute_id')
                 * */
            ->addIndex(
                $setup->getIdxName(
                    $employeeEntity . '_entity_decimal',
                    ['entity_id', 'attribute_id', 'store_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['entity_id', 'attribute_id', 'store_id'],
                ['type' =>
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )->addIndex(
                $setup->getIdxName($employeeEntity . '_entity_decimal', ['store_id']),
                ['store_id']
            )->addIndex(
                $setup->getIdxName($employeeEntity . '_entity_decimal',
                    ['attribute_id']),
                ['attribute_id']
            )

            /*
             * ->addForeignKey
             * CONSTRAINT 'FK_D17982EDA1846BAA1F40E30694993801' FOREIGN KEY ('entity_id') REFERENCES 'foggyline_office_employee_entity' ('entity_id') ON DELETE CASCADE,
             * CONSTRAINT 'FOGGYLINE_OFFICE_EMPLOYEE_ENTITY_DECIMAL_STORE_ID_STORE_STORE_ID' FOREIGN KEY ('store_id') REFERENCES 'store' ('store_id') ON DELETE CASCADE,
             * CONSTRAINT 'FOGGYLINE_OFFICE_EMPLOYEE_ENTT_DEC_ATTR_ID_EAV_ATTR_ATTR_ID' FOREIGN KEY ('attribute_id') REFERENCES 'eav_attribute' ('attribute_id') ON DELETE CASCADE
             * */
            ->addForeignKey(
                $setup->getFkName(
                    $employeeEntity . '_entity_decimal',
                    'attribute_id',
                    'eav_attribute',
                    'attribute_id'
                ),
                'attribute_id',
                $setup->getTable('eav_attribute'),
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            ) ->addForeignKey(
                $setup->getFkName(
                    $employeeEntity . '_entity_decimal',
                    'entity_id',
                    $employeeEntity . '_entity',
                    'entity_id'
                ),
                'entity_id',
                $setup->getTable($employeeEntity . '_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            ) ->addForeignKey(
                $setup->getFkName($employeeEntity . '_entity_decimal', 'store_id',
                    'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )

            ->setComment('Employee Decimal Attribute Backend Table');
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}