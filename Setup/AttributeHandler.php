<?php

namespace SgateShipFromStore\Setup;

use Shopware\Bundle\AttributeBundle\Service\CrudService;
use Shopware\Bundle\AttributeBundle\Service\TypeMapping;
use Shopware\Components\Model\ModelManager;

class AttributeHandler
{
    /**
     * @var CrudService
     */
    protected $attributeService;

    /**
     * @var ModelManager
     */
    protected $modelManager;

    public function __construct(
        CrudService $attributeService,
        ModelManager $modelManager
    ) {
        $this->attributeService = $attributeService;
        $this->modelManager = $modelManager;
    }

    /**
     * Creates or updates all attributes for the current plugin version.
     * Attributes which are not in the current version will be deleted.
     */
    public function updateAttributes()
    {
        $currentAttributes = $this->getCurrentAttributes();
        $tables = [];
        $currentAttributesKeys = [];

        foreach ($currentAttributes as $attribute) {
            $table = $attribute['table'];
            $name = $attribute['name'];

            if (
                isset($attribute['createOnly']) && boolval($attribute['createOnly']) === true &&
                $this->attributeService->get($table, $name) != null
            ) {
                continue;
            }

            $newColumnName = null;

            if (isset($attribute['newName'])) {
                if ($this->attributeService->get($table, $attribute['newName'])) {
                    $name = $attribute['newName'];
                    $currentAttributesKeys["$table.$name"] = "$table.$name";
                } else {
                    $newColumnName = $attribute['newName'];
                    $currentAttributesKeys["$table.$newColumnName"] = "$table.$newColumnName";
                }
            } else {
                $currentAttributesKeys["$table.$name"] = "$table.$name";
            }

            $tables[$table] = $table;

            $updateTables = isset($attribute['updateDependingTables']) && boolval($attribute['updateDependingTables']);

            $defaultValue = null;

            if (isset($attribute['defaultValue'])) {
                $defaultValue = $attribute['defaultValue'];
            }

            $this->attributeService->update(
                $table,
                $name,
                $attribute['type'],
                $attribute['config'],
                $newColumnName,
                $updateTables,
                $defaultValue
            );
        }

        $this->rebuildModels(array_keys($tables));

        $obsoleteAttributes = [];

        foreach ($this->getAttributesHistory() as $attribute) {
            $table = $attribute['table'];
            $name = $attribute['name'];

            if (
                !isset($currentAttributesKeys["$table.$name"])
            ) {
                $obsoleteAttributes[] = $attribute;
            }
        }

        $this->deleteAttributes($obsoleteAttributes);
    }

    /**
     * Deletes attributes if exist. If an array is available in parameter this attributes will be deleted.
     * Calling the function without any parameters will delete all attributes which where available since first plugin release.
     */
    public function deleteAttributes(array $attributes = null)
    {
        if ($attributes === null) {
            $attributes = $this->getAttributesHistory();
        }

        $tables = [];

        foreach ($attributes as $attribute) {
            $table = $attribute['table'];
            $name = $attribute['name'];

            if ($this->attributeService->get($table, $name)) {
                $this->attributeService->delete($table, $name);

                $tables[$table] = $table;
            }
        }

        $this->rebuildModels(array_keys($tables));
    }

    /**
     * rebuilds the models of all tables in parameter array.
     */
    protected function rebuildModels(array $tables)
    {
        $this->modelManager->getConfiguration()->getMetaDataCacheImpl()->deleteAll();
        $this->modelManager->generateAttributeModels($tables);
    }

    /**
     * Must return an array of all attributes which will be in the current plugin release. All attributes from here will be created or updated.
     * If an attribute is not returned here while updating, it will be automatically deleted.
     *
     * Format:
     * [
     *  'table' => 's_articles_attributes'      - Name of the attribute table                                                           #required
     *  'name' => 'sgate_test'                  - Name of the attribute column                                                          #required
     *  'type' => TypeMapping::TYPE_STRING      - Data type of column                                                                   #required
     *  'config' => [
     *      ...                                 - The default attribute configuration array                                             #required
     *  ],
     *  'createOnly' => true,                   - If the attribute already exists, it will not be updated.                              #optional
     *  'newName' => 'sgate_new_test',          - A new name if you want to rename an attribute                                         #optional
     *  'updateDependingTables' => true,        - Updating the depending tables                                                         #optional
     *  'defaultValue' => 'Hello!'              - A default value which will be set to new articles when leaving the attribute empty    #optional
     * ]
     */
    protected function getCurrentAttributes(): array
    {
        return [
            [
                'table' => 's_order_attributes',
                'name' => 'sgate_ship_from_store_exported',
                'type' => TypeMapping::TYPE_BOOLEAN,
                'config' => [
                    'displayInBackend' => false,
                    'custom' => false,
                ],
            ],
        ];
    }

    /**
     * Must return all attributes which where created by this plugin since first release.
     * New attributes must be added here. Never remove an attribute from this list after it's created in shopware!
     * If you renamed an attribute, both names should occur in here.
     *
     * Format (one item):
     *
     * [
     *  'table' => 's_articles_attributes',     - The name of the attribute table
     *  'name' => 'sgate_test'                  - The name of the attribute
     * ]
     */
    protected function getAttributesHistory(): array
    {
        return [
            [
                'table' => 's_order_attributes',
                'name' => 'sgate_ship_from_store_exported',
            ],
        ];
    }
}
