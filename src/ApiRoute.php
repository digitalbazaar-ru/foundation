<?php

namespace Foundation;


use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class ApiRoute
{
    protected $authorize = true;
    protected $authorizeType = 'Bearer';
    protected $token = '';

    private $dispatcher;
    private $router;

    public function __construct($authorize = true, $token = '', $authorizeType = 'Bearer')
    {
        $this->authorize     = $authorize;
        $this->token         = $token;
        $this->authorizeType = $authorizeType;

        $this->dispatcher = new \Illuminate\Events\Dispatcher();
        $this->router = new \Illuminate\Routing\Router($this->dispatcher);

    }

    public function route($prefix, \Closure $callback = null)
    {
        $this->router->group(['prefix' => $prefix], $callback);

        return $this;
    }

    public function dispatch()
    {
        try {
            $request = \Illuminate\Http\Request::createFromGlobals();

            if ($this->authorize) {
                $this->checkAuth($request);
            }

            $response = $this->router->dispatch($request);

            if ($response instanceof Responsible) {
                return $response->send();
            }

            $content = $response->getContent();
            $json = json_decode($response->getContent(), 1);

            $data = [
                'status' => true,
                'data' => is_null($json) ? $content : $json,
            ];

            return JsonResponse::create($data, $response->getStatusCode(), $response->headers->all())->send();

        } catch (ResponsibleException $e) {

            return $e->send();

        } catch (HttpException $e) {

            return JsonResponse::create(['status' => false, 'error' => $e->getMessage(), 'error_code' => $e->getStatusCode()], $e->getStatusCode())->send();

        }
    }


    private function checkAuth($request)
    {
        $auth = $request->headers->get('Authorization');

        list($type, $token) = explode(' ', $auth);

        if ($type !== $this->authorizeType || $token !== $this->token) {
            throw new UnauthorizedHttpException('');
        }

        return true;
    }
}

