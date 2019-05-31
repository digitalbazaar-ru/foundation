<?php

namespace Foundation\Traits;

use XMLWriter;

trait WithXmlWriter
{
    /** @var XMLWriter */
    protected $xmlWriter;
    protected $startedElements = [];

    protected function initXmlWriter()
    {
        $this->xmlWriter = new XMLWriter();
        $this->xmlWriter->openMemory();
        $this->xmlWriter->startDocument('1.0', 'UTF-8');
    }

    protected function getXmlWriter()
    {
        return $this->xmlWriter;
    }

    /**
     * @param $name
     * @param null $value
     * @return bool
     * @throws \Exception
     */
    protected function writeXmlElement($name, $value = null)
    {
        if (is_callable($value)) {

            $this->startXmlElement($name);

            try {

                call_user_func($value, $this);

            } catch (\Exception $exception) {

                $this->endXmlElement($name);

                throw $exception;
            }

            return $this->endXmlElement($name);
        }

        return $this->getXmlWriter()->writeElement($name, $value);
    }

    protected function startXmlElement($name)
    {
        $this->startedElements[] = $name;

        return $this->getXmlWriter()->startElement($name);
    }

    /**
     * @param null $toName
     * @return bool
     * @throws XmlStructureException
     */
    protected function endXmlElement($toName = null)
    {
        if (!is_null($toName)) {

            if (! in_array($toName, $this->startedElements)) {
                throw new XmlStructureException('Некорректная структура xml: попытка закрыть элемент ' . $toName . ', которого нет среди открытых: ' . print_r($this->startedElements, true));
            }

        }

        array_pop($this->startedElements);

        return $this->getXmlWriter()->endElement();
    }

    protected function flushXml()
    {
        return $this->getXmlWriter()->flush();
    }

    protected function currentElement()
    {
        return array_last($this->startedElements);
    }

}