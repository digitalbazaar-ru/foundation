<?php

namespace Foundation\Bx\Traits;

use Bitrix\Main\Loader;
use CFile;
use Illuminate\Support\Str;

/**
 * Class IBElementList
 * @package Foundation\Bx\Traits
 */
trait IBElementList
{
    /**
     * @var bool
     */
    protected $checkPermissions = false;
    /**
     * @var bool
     */
    protected $onlyActive = true;
    /**
     * @var bool
     */
    protected $checkActiveDate = true;

    /**
     * @var array
     */
    protected $order = [];
    /**
     * @var bool
     */
    protected $group = false;
    /**
     * @var bool
     */
    protected $navigation = false;
    /**
     * @var array
     */
    protected $filter = [];
    /**
     * @var array
     */
    protected $select = [];

    /**
     * @var array
     */
    protected $imageFields = [
        'DETAIL_PICTURE',
        'PREVIEW_PICTURE'
    ];

    /**
     * @var array
     */
    protected $defaultFields = [
        'ID',
        'CODE',
        'SECTION_ID',
        'IBLOCK_ID',
        'IBLOCK_SECTION_ID'
    ];


    /**
     * @var \CDBResult
     */
    protected $result;

    /**
     * @return array|mixed
     */
    public function getItem()
    {
        $items = $this->getElements();

        if (! empty($items)) {
            $items = array_first($items);
        }

        return $items;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        $this->result = $this->loadResult();

        if ($this->result->SelectedRowsCount() === 0) {
//            if ($params['SHOW_404']) $this->throwPageNotFound();
            return [];
        }

        return $this->processResult($this->result);
    }

    /**
     * @param \CDBResult $result
     * @return array
     */
    protected function processResult(\CDBResult $result)
    {
        $return = [];

        while ($item = $result->GetNext(true, true)) {

            $return[] = $this->elementMapper($item);

        }

        return $return;
    }

    /**
     * @return \CIBlockResult|int
     */
    protected function loadResult()
    {
        Loader::includeModule('iblock');

        /* @noinspection PhpDynamicAsStaticMethodCallInspection */
        return \CIBlockElement::GetList(
            $this->order,
            $this->compileFilter(),
            $this->group,
            $this->navigation,
            $this->compileSelect()
        );
    }

    /**
     * @return array
     */
    protected function compileFilter()
    {
        $filter = [];

        $filter['IBLOCK_ID'] = $this->getIblockId();
        $filter['CHECK_PERMISSIONS'] = $this->checkPermissions ? 'Y' : 'N';

        $filter = array_merge($filter, $this->filter);

        if ($this->onlyActive) {
            $filter['ACTIVE'] = 'Y';
        }

        if ($this->checkActiveDate) {
            $filter['ACTIVE_DATE'] = 'Y';
        }

        return $filter;

    }

    /**
     * @return array
     */
    protected function compileSelect()
    {
        $select = array_merge($this->defaultFields, $this->select);
        return $select;
    }

    /**
     * @param $element
     * @return array
     */
    protected function elementMapper($element)
    {

        $result = [];

        foreach ($element as $key => $value) {

            $isImage = in_array($key, $this->imageFields, true);

            if (strpos($key, 'PROPERTY_') !== false) {
                $key = substr_replace($key, '', $key[0] == '~' ? 1 : 0, 9);
            }

            $camelCaseKey = Str::camel(strtolower($key));

            if ($isImage) {
                $result['has'.ucfirst($camelCaseKey)] = !empty($value);
                $result[$camelCaseKey] = !empty($value) ? CFile::GetFileArray($value) : false;
                $result[$camelCaseKey.'Description'] = !$result[$camelCaseKey] ?: $result[$camelCaseKey]['DESCRIPTION'];
                continue;
            }

            $result[$camelCaseKey] = $value;

        }

        return $result;

    }


    /**
     * @param array $filter
     * @return $this
     */
    public function setFilter($filter = [])
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @param array $order
     * @return $this
     */
    public function setOrder($order = [])
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param bool $group
     * @return $this
     */
    public function setGroup($group = false)
    {
        $this->group = $group;
        return $this;
    }

    /**
     * @param bool $navigation
     * @return $this
     */
    public function setNavigation($navigation = false)
    {
        $this->navigation = $navigation;
        return $this;
    }

    /**
     * @param array $select
     * @return $this
     */
    public function setSelect($select = null)
    {
        if (! is_array($select)) {
            return $this;
        }
        $this->select = $select;
        return $this;
    }

    /**
     * @return int
     * @throws IBImplementationException
     */
    protected function getIblockId()
    {
        throw new IBImplementationException('Не указан ID ИБ');
    }

    /**
     * @param array $fields
     * @return array
     */
    protected function prepareProperties($fields = [])
    {
        $props = [];

        foreach ($fields as $key => $value) {

            if (isset($fields['PROPERTY_VALUES'])) {
                $props = $fields['PROPERTY_VALUES'];
            }

            if (strpos($key, 'PROPERTY_') === 0 && $key != 'PROPERTY_VALUES') {
                $props[substr($key, 9)] = $value;
                unset($fields[$key]);
            }
        }

        if (! empty($props)) {
            $enums = $this->getEnumProperties();

            foreach ($props as $code => $value) {
                if (isset($enums[$code])) {
                    $props[$code] = array_get($enums[$code], 'byXmlId.' . $value. '.id');
                }
            }
        }

        return $props;
    }

    /**
     * @var array
     */
    protected static $enumProperties = null;

    /**
     * @return array
     */
    protected function getEnumProperties()
    {
        if (! is_null(static::$enumProperties)) {
            return static::$enumProperties;
        }

        $dbListProps = \CIBlockPropertyEnum::GetList([], ['IBLOCK_ID' => $this->getIblockId()]);

        static::$enumProperties = [];

        while ($prop = $dbListProps->Fetch()) {
            static::$enumProperties[$prop['PROPERTY_CODE']]['byXmlId'][$prop['XML_ID']] = [
                'id'    => $prop['ID'],
                'value' => $prop['VALUE'],
                'xmlId' => $prop['XML_ID']
            ];
            static::$enumProperties[$prop['PROPERTY_CODE']]['byId'][$prop['ID']] = &static::$enumProperties[$prop['PROPERTY_CODE']]['byXmlId'][$prop['XML_ID']];
        }

        return static::$enumProperties;
    }

    /**
     * @param $active
     * @return $this
     */
    public function onlyActive($active)
    {
        $this->onlyActive = $active;
        return $this;
    }

    /**
     * @param $active
     * @return $this
     */
    public function checkActiveDate($active)
    {
        $this->checkActiveDate = $active;
        return $this;
    }

    /**
     * @param $permissions
     * @return $this
     */
    public function checkPermissions($permissions)
    {
        $this->checkPermissions = $permissions;
        return $this;
    }

}
