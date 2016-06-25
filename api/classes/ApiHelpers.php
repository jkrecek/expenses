<?php defined('BASEPATH') OR exit('No direct script access allowed');

trait ApiHelpers {

    /* @internal */
    private $dbInstance = NULL;

    private $supportedValues = [];

    /**
     * @return PDO
     */
    protected function getDbConnection() {

        if ($this->dbInstance === NULL) {
            $dsn = sprintf("%s:host=%s;dbname=%s;charset=utf8mb4", Configuration::PDO_DRIVER, Configuration::DB_HOST, Configuration::DB_NAME);
            $this->dbInstance = new PDO($dsn, Configuration::DB_USER, Configuration::DB_PASS, [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ] );
        }

        return $this->dbInstance;
    }

    /**
     * @return array
     */
    protected function getSupportedLanguages() {
        return $this->getSupportedValues('lang');
    }

    /**
     * @return array
     */
    protected function getSupportedTypes() {
        return $this->getSupportedValues('type');
    }

    /**
     * @internal
     * @param $type string
     * @return array
     */
    private function getSupportedValues($type) {
        if (!isset($this->supportedValues[$type])) {
            $sql = "SELECT `{$type}` FROM `translation` GROUP BY `{$type}`";
            $this->supportedValues[$type] = $this->getDbConnection()->query($sql)->fetchAll(PDO::FETCH_COLUMN);
        }

        return $this->supportedValues[$type];
    }
}