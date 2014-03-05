<?php
class PunishAction extends CommonAction {

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

    //高级检索
    public function search(){
        $Punish = M('Punish');
        $where = array();
        if(!empty($_POST['name'])){
            $where['name'] = array('LIKE', '%' . $this -> _post('name') . '%');
        }
        if(!empty($_POST['policeId'])){
            $where['policeId'] = array('LIKE', '%' . $this -> _post('policeId') . '%');
        }
        if(!empty($_POST['studentId'])){
            $where['studentId'] = array('LIKE', '%' . $this -> _post('studentId') . '%');
        }
        if(!empty($_POST['teamName'])){
            $where['teamName'] = array('LIKE', '%' . $this -> _post('teamName') . '%');
        }
        if(!empty($_POST['className'])){
            $where['className'] = array('LIKE', '%' . $this -> _post('className') . '%');
        }
        if(!empty($_POST['punlishType'])){
            $where['punlishType'] = array('LIKE', '%' . $this -> _post('punlishType') . '%');
        }
        //记录总数
        $count = $Punish -> where($where) -> count('id');
        import('ORG.Util.Page');
        if(! empty ( $_REQUEST ['listRows'] )){
            $listRows = $_REQUEST ['listRows'];
        } else {
            $listRows = 10;
        }
        $page = new Page($count, $listRows);
        //当前页数
        $pageNum = !empty($_REQUEST['pageNum']) ? $_REQUEST['pageNum'] : 1;
        $page -> firstRow = ($pageNum - 1) * $listRows;
        $result = $Punish-> field('id,name,policeId,studentId,teamName,className,entranceTime,punlishType,punlishDecision,punlishTime,isUndo') -> where($where) -> limit($page -> firstRow . ',' . $page -> listRows) -> order('addtime DESC') -> select();
        $this -> assign('result', $result);
        //每页条数
        $this -> assign('listRows', $listRows);
        //当前页数
        $this -> assign('currentPage', $pageNum);
        $this -> assign('count', $count);
        $this -> display();
    }

    //撤销处分
    public function undo(){
        $Punish = M('Punish');
        if(!empty($_POST['isUndo'])){
            $_POST['undoTime'] = strtotime($_POST['undoTime']);
            if(!$Punish -> create()){
                $this -> error($Punish -> getError());
            }

            if($Punish -> save()){
                $this -> success(L('DATA_UPDATE_SUCCESS'));
            }else{
                $this -> error(L('DATA_UPDATE_ERROR'));
            }
        }
        $result = $Punish -> field('id,name,punlishTime,isUndo,undoTime,undoRemark') -> find($this -> _get('id', 'intval'));
        $this -> assign('result', $result);

        $datetime1 = new DateTime(date('Y-m-d', $result['punlishTime']));
        $datetime2 = new DateTime();
        $interval = $datetime1->diff($datetime2);
        $diff = $interval->format('%y年%m月%d天%');
        $this -> assign('diff', $diff);
        $this -> display();
    }

    //学生信息详情
    public function studentinfo(){
        $Punish = M('Punish');
        $result = $Punish -> find($this -> _get('mid', 'intval'));
        $this -> assign('result', $result);
        $this -> display();
    }

    //编辑学生信息
    public function editstudent(){
        $Punish = D('Punish');

        if(!empty($_POST['name'])){
            if(!$Punish -> create()){
                $this -> error($Punish -> getError());
            }

            if($Punish -> save()){
                $this -> success(L('DATA_UPDATE_SUCCESS'));
            }else{
                $this -> error(L('DATA_UPDATE_ERROR'));
            }
        }

        $result = $Punish -> find($this -> _get('id', 'intval'));
        $this -> assign('result', $result);
        //班级信息
        $class = R('Class/getChildClass', array($result['teamName']), 'Widget');
        $this -> assign('class', $class);
        $this -> display();
    }

    //批量导入
    public function input(){
        $this -> display();
    }

    //执行导入
    public function checkinput(){
        import('ORG.Net.UploadFile');
        $upload = new UploadFile();// 实例化上传类
//        $upload->maxSize  = 3145728 ;// 设置附件上传大小
//        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->savePath =  './Uploads/';// 设置附件上传目录
        if(!$upload->upload()) {// 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        }else{// 上传成功 获取上传文件信息
            $info =  $upload->getUploadFileInfo();
        }
        //文件路径
        $path = $info[0]['savepath'] . $info[0]['savename'];

        //导入类库
        Vendor('PHPExcel.IOFactory');
        $fileType = PHPExcel_IOFactory::identify($path); //文件名自动判断文件类型
        $objReader = PHPExcel_IOFactory::createReader($fileType);
        $objPHPExcel = $objReader->load($path);

        $currentSheet = $objPHPExcel->getSheet(0); //第一个工作簿
        $allRow = $currentSheet->getHighestRow(); //行数

        $Punish = M('Punish');
        $success_num = 0;
        $error_num = 0;


        for($i = 3; $i < $allRow; $i++){
            $add_data = array();
            $add_data['name'] = $currentSheet -> getCell('D' . $i) -> getValue();
            $add_data['sex'] = $currentSheet -> getCell('J' . $i) -> getValue();
            $add_data['politicalStatus'] = $currentSheet -> getCell('F' . $i) -> getValue();
            $add_data['number'] = $currentSheet -> getCell('K' . $i) -> getValue();
            $add_data['studentId'] = $currentSheet -> getCell('E' . $i) -> getValue();
            $add_data['policeId'] = $currentSheet -> getCell('G' . $i) -> getValue();
            $add_data['national'] = $currentSheet -> getCell('H' . $i) -> getValue();
            $add_data['telephone'] = $currentSheet -> getCell('L' . $i) -> getValue();
            $add_data['nativePlace'] = $currentSheet -> getCell('I' . $i) -> getValue();
            $add_data['teamName'] = $currentSheet -> getCell('B' . $i) -> getValue();
            $add_data['className'] = $currentSheet -> getCell('C' . $i) -> getValue();
            $add_data['entranceTime'] = strtotime($currentSheet -> getCell('Q' . $i) -> getValue());
            $add_data['punlishType'] = $currentSheet -> getCell('N' . $i) -> getValue();
            $add_data['content'] = $currentSheet -> getCell('M' . $i) -> getValue();
            $add_data['punlishDecision'] = $currentSheet -> getCell('O' . $i) -> getValue();
            $add_data['punlishTime'] = strtotime($currentSheet -> getCell('R' . $i) -> getValue());
            $add_data['addTime'] = time();

            if($Punish -> add($add_data)){
                $success_num++;
            }else{
                $error_num++;
            }
        }

        $this -> success('导入完成，共成功导入 ' . $success_num . ' 条数据，失败 ' . $error_num . ' 条数据');

    }
}