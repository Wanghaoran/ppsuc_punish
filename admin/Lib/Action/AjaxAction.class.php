<?php
class AjaxAction extends Action {

    public function getChildClass(){
        $q = R('Class/getChildClass', array($_GET['key']), 'Widget');
        $result = array();
        foreach($q as $key => $value){
            $result[] = array($key, $value);
        }
        echo json_encode($result);

    }

}