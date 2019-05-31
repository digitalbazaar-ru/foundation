<?php
namespace Foundation\Traits\Http;

use Foundation\Http\IpUtil;

trait IpFilterable
{
    public function availableForIp($ip, $ips = [])
    {
        return (new IpUtil($ips))->checkIp($ip);
    }
}

