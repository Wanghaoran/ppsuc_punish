<?php
class PunishAction extends CommonAction {

    public function student(){
        $Punish = M('Punish');
        $where = array();
        if(!empty($_POST['name'])){
            $where['name'] = array('LIKE', '%' . $this -> _post('name') . '%');
        }
        //记录总数
        $count = $Punish -> where($where) -> count('id');
        import('ORG.Util.Page');
        if(! empty ( $_REQUEST ['listRows'] )){
            $listRows = $_REQUEST ['listRows'];
        } else {
            $listRows = 15;
        }
        $page = new Page($count, $listRows);
        //当前页数
        $pageNum = !empty($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
        $page -> firstRow = ($pageNum - 1) * $listRows;
        $result = $Punish-> field('id,name,number,studentId,teamName,className,entranceTime,punlishType,punlishDecision,addTime') -> where($where) -> limit($page -> firstRow . ',' . $page -> listRows) -> order('addtime DESC') -> select();
        $this -> assign('result', $result);
        //每页条数
        $this -> assign('listRows', $listRows);
        //当前页数
        $this -> assign('currentPage', $pageNum);
        $this -> assign('count', $count);
        $this -> display();
    }

    public function addstudent(){
        if(!empty($_POST['name'])){
            $Punish = D('Punish');
            if(!$a = $Punish -> create()){
                $this -> error($Punish -> getError());
            }
            if($Punish -> add()){
                $this -> success(L('DATA_ADD_SUCCESS'));
            }else{
                $this -> error(L('DATA_ADD_ERROR'));
            }
        }
        $this -> display();
    }
}