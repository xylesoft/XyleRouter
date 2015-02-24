<?php

namespace Tests\stubs;

use Xylesoft\XyleRouter\Interfaces\RequestInterface;

class DummyRequest implements RequestInterface
{
    private $parameters = [
        'year' => '1982',
        'name' => 'aDummy',
    ];

    private $headers = [
        'accept' => 'application/json',
    ];

    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Get the URL provided in the request.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the GET parameters from the Request.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Get a single GET parameter from the request.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function getParameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     * Get all the request Headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }
}
