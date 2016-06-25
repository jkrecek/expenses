<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @property ApiRequest request
 */
trait ApiMethods {

    use ApiHelpers;

    public function handleTotal() {
        $this->checkTypeParameter();
        $sql = <<<EOD
            SELECT
                `t`.`translation`, SUM(`price`) AS `amount`, `{columnName}` as `code`
            FROM `expenses` `e`
            LEFT JOIN `translation` `t` ON
                `e`.`{columnName}` = `t`.`code` AND
                `t`.`type` = '{columnName}' AND
                `t`.`lang` = '{lang}' 
            WHERE `yolo` = 1 {additionalFilters}
            GROUP BY `{columnName}`
EOD;
        $columnName = $this->request->type;
        $additionalFilters = $this->request->filterColumn ? "AND `{$this->request->filterColumn}` = '{$this->request->filterValue}' " : "";

        $sql = strtr($sql, [
            "{columnName}" => $columnName,
            "{lang}" => $this->request->lang,
            "{additionalFilters}" => $additionalFilters
        ]);

        $selection = $this->getDbConnection()->query($sql, PDO::FETCH_ASSOC)->fetchAll(PDO::FETCH_ASSOC);

        return [
            "success" => true,
            "results" => $selection
        ];
    }

    private function checkTypeParameter() {
        if (!($this->request->type && in_array($this->request->type, $this->getSupportedTypes()))) {
            throw new Exception("Unsupported filter type '{$this->request->type}'.");
        }
    }
}