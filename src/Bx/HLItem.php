<?php

namespace Foundation\Bx;

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Loader;
use Foundation\Bx\Traits\HLElementList;

/**
 * Class HLItem
 * @package Foundation\Bx
 */
abstract class HLItem
{
    use HLElementList;

    public function __construct()
    {
        Loader::includeModule('highloadblock');
    }

    protected $hlIblockId = 0;

    /**
     * @return int
     * @throws BxItemException
     */
    protected function getHLIblockId()
    {
        if ($this->hlIblockId <= 0) {
            throw new BxItemException('Не указан ID HL блока');
        }

        return $this->hlIblockId;
    }

    /**
     * @param array $fields
     * @return int
     * @throws BxItemException
     */
    public function create(array $fields = [])
    {
        if (empty($fields)) {
            return false;
        }

        $hlObj = &$this->getHLBlockEntity();
        $result = $hlObj::add($fields);

        if (! $result->isSuccess()) {
            throw new BxItemException(implode('<br>', $result->getErrorMessages()));
        }

        return $result->getId();
    }

    /**
     * @param int $id
     * @param array $fields
     * @return bool
     * @throws BxItemException
     */
    public function update($id = 0, array $fields = [])
    {
        if ($id <= 0 || ! count($fields)) {
            return false;
        }

        $hlObj = &$this->getHLBlockEntity();

        $result = $hlObj::update((int) $id, $fields);

        if (! $result->isSuccess()) {
            throw new BxItemException(implode('<br>', $result->getErrorMessages()));
        }

        return $result->isSuccess();
    }

    /**
     * @param int $id
     * @return bool
     * @throws BxItemException
     */
    public function delete($id = 0)
    {
        if ( $id <= 0) {
            return false;
        }

        $hlObj = &$this->getHLBlockEntity();
        $result = $hlObj::delete($id);

        if (! $result->isSuccess()) {
            throw new BxItemException(implode('<br>', $result->getErrorMessages()));
        }

        return $result->isSuccess();
    }
}

