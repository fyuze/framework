<?php
namespace Fyuze\Debug\Collectors;

use Fyuze\Http\Response as BaseResponse;

class Response implements Collector
{
    /**
     * @var BaseResponse
     */
    protected $response;

    /**
     * Response constructor.
     * @param \Fyuze\Http\Response $response
     */
    public function __construct(BaseResponse $response)
    {
        $this->response = $response;
    }

    /**
     * @return int
     */
    public function tab()
    {
        $title = $this->response->getStatusCode();

        $class = ($this->response->getStatusCode() >= 400) ? 'danger' : 'success';

        return [
            'title' => $title,
            'class' => sprintf('response-code %s', $class)
        ];
    }
}
