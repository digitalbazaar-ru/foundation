<?php

namespace Foundation\Bx\Traits;

use Bitrix\Main\Loader;

/**
 * Class IBBlock
 * @package Foundation\Bx\Traits
 */
trait IBBlock
{
    /**
     * @var array
     */
    private $cache = [];

    /**
     * @param string $code
     * @return array
     * @throws IBImplementationException
     */
    protected function loadIblockByCode($code = '')
    {
        Loader::includeModule('iblock');

        if (empty($code)) {
            throw new IBImplementationException('Код ИБ не указан');
        }

        $hash = md5($code);

        if (isset($cache[$hash])) {
            return $cache[$hash];
        }

        return $cache[$hash] = \CIBlock::GetList([], ['CODE' => $code, 'LID' => SITE_ID == 'ru' ? 's1' : SITE_ID, 'ACTIVE' => 'Y'])->Fetch();
    }

    /**
     * @param $id
     * @return array
     * @throws IBImplementationException
     */
    protected function loadIblockById($id)
    {
        Loader::includeModule('iblock');

        $id = (int) $id;
        if ($id <= 0) {
            throw new IBImplementationException('Id ИБ не указан');
        }

        $hash = md5($id);

        if (isset($cache[$hash])) {
            return $cache[$hash];
        }

        return $cache[$hash] = \CIBlock::GetList([], ['ID' => $id, 'LID' => SITE_ID == 'ru' ? 's1' : SITE_ID, 'ACTIVE' => 'Y'])->Fetch();
    }
}
