<?php
namespace Foundation\Bx;

use Bitrix\Main\Loader;
use Foundation\Bx\Traits\IBElementList;

/**
 * Class Item
 * @package Foundation\Bx
 */
abstract class Item
{
    use IBElementList;

    /**
     * @var \CIBlockElement
     */
    protected $obj;
    /**
     * @var array
     */
    protected $defaultSelect = ['ID', 'IBLOCK_ID', 'CODE'];

    /**
     * Item constructor.
     */
    public function __construct()
    {
        Loader::includeModule('iblock');
        $this->obj = new \CIBlockElement();
        $this->setSelect(['ID', 'IBLOCK_ID', 'NAME', 'CODE']);
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function byId($id)
    {
        if ($id <= 0) {
            return ['id' => $id];
        }

        return $this->setFilter(['ID' => $id])->getItem();
    }

    /**
     * @param $fields
     * @return bool
     * @throws BxItemException
     */
    public function create($fields)
    {

        $transactionHelper = TransactionHelper::getInstance();

        $outerTransactionExists = $transactionHelper->isTransactionStarted();

        if (! $outerTransactionExists) {
            $transactionHelper->start();
        }

        $fields['IBLOCK_ID'] = $this->getIblockId();


        $props = $this->prepareProperties($fields);

        try {

            $this->beforeAdd($fields, $props);

        } catch (\Exception $e) {
            $transactionHelper->rollback();
            throw new BxItemException('Ошибка создания: ' . $e->getMessage());
        }

        if (! empty($props)) {
            $fields['PROPERTY_VALUES'] = $props;
        }

        $id = $this->obj->Add($fields);

        if (! $id) {
            $transactionHelper->rollback();
            throw new BxItemException('Ошибка создания: ' . $this->obj->LAST_ERROR);
        }

        try {

            $this->afterAdd($id, $fields, $props);

        } catch (\Exception $e) {
            $transactionHelper->rollback();
            throw new BxItemException('Ошибка создания: ' . $e->getMessage());
        }

        if (! $outerTransactionExists) {
            $transactionHelper->commit();
        }

        return $id;
    }

    /**
     * @param $id
     * @param $fields
     * @return bool
     * @throws BxItemException
     */
    public function update($id, $fields)
    {
        $transactionHelper = TransactionHelper::getInstance();

        $outerTransactionExists = $transactionHelper->isTransactionStarted();

        if (! $outerTransactionExists) {
            $transactionHelper->start();
        }

        $fields['IBLOCK_ID'] = $this->getIblockId();
        $props = $this->prepareProperties($fields);

        try {

            $this->beforeUpdate($id, $fields, $props);

        } catch (\Exception $e) {
            $transactionHelper->rollback();
            throw new BxItemException('Ошибка Обновления: ' . $e->getMessage());
        }

        $res = $this->obj->Update($id, $fields);

        if (! $res) {
            $transactionHelper->rollback();
            throw new BxItemException($this->obj->LAST_ERROR);
        }

        if (! empty($props)) {
            $this->obj->SetPropertyValuesEx($id, $this->getIblockId(), $props);
        }


        try {

            $this->afterUpdate($id, $fields, $props);

        } catch (\Exception $e) {
            $transactionHelper->rollback();
            throw new BxItemException('Ошибка Обновления: ' . $e->getMessage());
        }

        if (! $outerTransactionExists) {
            $transactionHelper->commit();
        }
        return $res;
    }

    /**
     * @param $id
     * @param $fields
     * @return bool
     * @throws BxItemException
     */
    public function delete($id)
    {
        $transactionHelper = TransactionHelper::getInstance();

        $outerTransactionExists = $transactionHelper->isTransactionStarted();

        if (! $outerTransactionExists) {
            $transactionHelper->start();
        }

        try {

            $this->beforeDelete($id);

        } catch (\Exception $e) {
            $transactionHelper->rollback();
            throw new BxItemException('Ошибка Удаления: ' . $e->getMessage());
        }

        $res = $this->obj->Delete($id);

        if (! $res) {
            $transactionHelper->rollback();
            throw new BxItemException($this->obj->LAST_ERROR);
        }

        if (! empty($props)) {
            $this->obj->SetPropertyValuesEx($id, $this->getIblockId(), $props);
        }


        try {

            $this->afterDelete($id);

        } catch (\Exception $e) {
            $transactionHelper->rollback();
            throw new BxItemException('Ошибка Обновления: ' . $e->getMessage());
        }

        if (! $outerTransactionExists) {
            $transactionHelper->commit();
        }
        return $res;
    }


    /**
     * @param $fields
     * @param $props
     */
    protected function beforeAdd($fields, $props) {}

    /**
     * @param $id
     * @param $fields
     * @param $props
     */
    protected function afterAdd($id, $fields, $props) {}

    /**
     * @param $id
     * @param $fields
     * @param $props
     */
    protected function beforeUpdate($id, $fields, $props) {}

    /**
     * @param $id
     * @param $fields
     * @param $props
     */
    protected function afterUpdate($id, $fields, $props) {}

    /**
     * @param $id
     */
    protected function beforeDelete($id) {}

    /**
     * @param $id
     */
    protected function afterDelete($id) {}
}

/**
 * Class BxItemException
 * @package Foundation\Bx
 */
class BxItemException extends \Exception {}
