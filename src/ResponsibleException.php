<?php

namespace Foundation;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ResponsibleException extends HttpException
{
    public function send()
    {
        return JsonResponse::create(['status' => false, 'error' => $this->getMessage(), 'error_code' => $this->getStatusCode()], $this->getStatusCode())->send();
    }
}

