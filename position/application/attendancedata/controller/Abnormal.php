<?php
/*--------------------------------------------------------------------
 oa系统 - 让工作更加灵活便捷
异常考勤-oa考勤

 Copyright (c) 2017 http://car.qqxio.com All rights reserved.

 Author:  jiajun.lin<13629618142@163.com>,shaosen.Yu<yushaosen@163.com>,guanyu.Shi<shiguanyu@126.com>

 --------------------------------------------------------------*/

namespace app\attendancedata\controller;
use app\base\controller\Base;
use think\Db;
use think\Request;

class Abnormal extends Base{

    public function index(){
        
        $where = array();
        if(Request::instance()->isPost()){
            $data = input('param.');
       
            $where = $this->search($data);
            $this->assign('data',$data);
            
        }
        $userinfo = session('user');
        if($userinfo['id'] != 1){   //非系统管理员
            $com_data = Db::name('pos_com')->where('emp_no',$userinfo['emp_no'])->find();
            if(empty($com_data)){
                $where['com_id']=$userinfo['com_id'];
            }else{
                $where['com_id'] = ['IN',$com_data['com_ids']];
            }  
        }
        $post_m = Db::name("pos_abnormal")->where($where)->field('*')->limit(400)->select(); 
        
        //分页
        
        $this->assign('data_list',$post_m);
        
        return view();
    }
    
    //搜索
    public function search($data){
        
        $map = array();
        
        if(!empty($data['user_name'])){
            $map['user_name|emp_no'] = ['like','%'.$data['user_name'].'%'];
        }if(!empty($data['start']) && !empty($data['end'])){     //开始和结束都不为空
            $map['day'][0]  = ['>=',(int)date('d',strtotime($data['start']))];
            $map['day'][1]  = ['<=',(int)date('d',strtotime($data['end']))];
            $map['month'][0]  = ['>=',(int)date('m',strtotime($data['start']))];
            $map['month'][1]  = ['<=',(int)date('m',strtotime($data['end']))];
            $map['year'][0]  = ['>=',date('Y',strtotime($data['start']))];
            $map['year'][1]  = ['<=',date('Y',strtotime($data['end']))];
        }else if(!empty($data['start'])){                        //开始不为空，结束为空
            $map['day']  = ['>=',(int)date('d',strtotime($data['start']))];
            $map['month']  = ['>=',(int)date('m',strtotime($data['start']))];
            $map['year']  = ['>=',date('Y',strtotime($data['start']))];
        }else if(!empty($data['end'])){                          //开始为空，结束不为空
            $map['day']  = ['<=',(int)date('d',strtotime($data['end']))];
            $map['month']  = ['<=',(int)date('m',strtotime($data['end']))];
            $map['year']  = ['<=',date('Y',strtotime($data['end']))];
        }
        
        
        return $map;
    }

    //搜索
    public function bqk(){
        $id = input('param.id');
        $data = Db::name('pos_abnormal')->where('id',$id)->find();
        $this->assign('data',$data);
        return view();
    }

    
    
    
}