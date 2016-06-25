<?php defined('BASEPATH') OR exit('No direct script access allowed');


class ApiRequest
{
    public $lang;

    public $method;

    public $type;

    public $filterColumn;

    public $filterValue;

    public function __construct($queryPath)
    {
        $matchesFound = preg_match('/^.*?\/api\/([a-z]{2})\/(\w+)(?:\/(\w+)(?:\/(\w+)\/(\w+))?)?$$/', $queryPath, $matches);
        if (!$matchesFound) {
            throw new Exception("Invalid url");
        }

        $splMatches = SplFixedArray::fromArray($matches);
        $splMatches->setSize(6);
        list(, $this->lang, $this->method, $this->type, $this->filterColumn, $this->filterValue) = $splMatches->toArray();
    }

    public function getFullMethodName() {
        return 'handle' . ucfirst($this->method);
    }
}