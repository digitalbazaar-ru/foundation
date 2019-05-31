<?php
namespace Foundation\Http;

use Symfony\Component\HttpFoundation\IpUtils;

class IpUtil
{
    protected $ips = [];

    public function __construct($ips = [])
    {
        $this->ips = $ips;
    }

    public function checkIp($clientIp)
    {
        if (count($this->ips) == 0) {
            return true;
        }

        return IpUtils::checkIp($clientIp, $this->ips);
    }
}

