<?php
namespace Foundation\Bx;


use Bitrix\Main\Application;
use Foundation\Traits\Singleton;

/**
 * Class TransactionHelper
 * @package Foundation\Bx
 */
class TransactionHelper
{
    /**
     * @var bool
     */
    private $transactionStarted = false;

    use Singleton;

    /**
     * @return bool
     */
    public function isTransactionStarted()
    {
        return $this->transactionStarted === true;
    }

    /**
     *
     */
    public function start()
    {
        Application::getConnection()->startTransaction();
        $this->transactionStarted = true;
    }


    /**
     *
     */
    public function rollback()
    {
        Application::getConnection()->rollbackTransaction();
        $this->transactionStarted = false;
    }

    /**
     *
     */
    public function commit()
    {
        Application::getConnection()->commitTransaction();
        $this->transactionStarted = false;
    }

}
