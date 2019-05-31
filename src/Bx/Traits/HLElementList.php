<?php

namespace Foundation\Bx\Traits;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

/**
 * Class HLElementList
 * @package Foundation\Bx\Traits
 */
trait HLElementList
{
    /**
     * @var Entity\DataManager
     */
    protected $entity = null;

    /**
     * @throws IBImplementationException
     * @return int
     */
    protected function getHLIblockId()
    {
        throw new IBImplementationException('Не указан ID ИБ');
    }

    /**
     * @param array $order
     * @param array $filter
     * @param array $select
     * @param array $nav
     * @return array
     */
    public function getList(array $order = ['ID' => 'ASC'], array $filter = [], array $select = [], $nav = [])
    {
        $Query = new Entity\Query($this->getHLBlockEntity());
        $dbRes = $Query
            ->setOrder($order)
            ->setFilter($filter)
            ->setSelect($select)
            ->exec()
        ;

        return $dbRes->fetchAll();
    }

    /**
     * @return Entity\DataManager
     */
    public function getHLBlockEntity()
    {

        if (! is_null($this->entity)) {
            return $this->entity;
        }

        $HLBlock	   = HL\HighloadBlockTable::getById((int) $this->getHLIblockId())->fetch();
        $HLBlockEntity = HL\HighloadBlockTable::compileEntity($HLBlock);
        $this->entity  = $HLBlockEntity->getDataClass();

        return $this->entity;
    }

}
