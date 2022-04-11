<?php

namespace Hcode;

class Model {

    private $values = [];

    public function __call($name, $args) { //metodo, arguntos do metodo

        $method = substr($name, 0, 3); //partir de 0, contar 3 posições
        $fieldName = substr($name, 3, strlen($name));//a partir de 3, até a ultima posição

        switch ($method) {
            case "get":
                return (isset($this->values["$fieldName"])) ?
                    $this->values[$fieldName] : NULL; //$this->iduser;
                break;
            
            case "set":
                $this->values[$fieldName] = $args[0]; //$this->iduser = iduser;
                break;
        }
    }

    public function setData($data = array()) {

        foreach ($data as $key => $value) {

            $this->{"set".$key}($value);

        }
    }

    public function getValues() {

        return $this->values;

    }
}

?>