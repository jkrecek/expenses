<?php defined('BASEPATH') OR exit('No direct script access allowed');

trait ApiBase {
    private $result = [];

    /** @var ApiRequest */
    protected $request;

    public function handle() {
        try {
            $this->request = new ApiRequest($_SERVER['REQUEST_URI']);
            $this->validateRequest();
            $this->result = $this->{$this->request->getFullMethodName()}();
        } catch (Exception $e) {
            $this->result = [
                "success" => false,
                "error" => $e->getMessage()
            ];
        }
    }

    public function sendResponse() {
        header('Content-type: application/json');
        echo json_encode($this->result);
        exit;

    }

    private function validateRequest() {
        if (!($this->request->lang && in_array($this->request->lang, $this->getSupportedLanguages()))) {
            throw new Exception("Unsupported language '{$this->request->lang}'.");
        }

        if (!method_exists($this, $this->request->getFullMethodName())) {
            throw new Exception("Invalid API method '{$this->request->method}'");
        }


        if ($this->request->filterColumn) {
            if (!($this->request->filterColumn && in_array($this->request->filterColumn, $this->getSupportedTypes()))) {
                throw new Exception("Unsupported filter type '{$this->request->filterColumn}'.");
            }
        }
        
    }
}