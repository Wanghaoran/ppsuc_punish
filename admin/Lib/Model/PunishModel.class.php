<?php
class PunishModel extends Model {

    protected $_auto = array (
        array('entranceTime','strtotime',3,'function'),
        array('graduationTime','strtotime',3,'function'),
        array('addTime','time',1,'function'),
    );

}