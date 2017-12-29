<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\SmsCustomer;
use App\Http\Models\SmsSendTo;
use App\Http\Models\User;
use App\Http\Models\SmsTemplate;
use App\Http\Models\CarrierSetting;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use View;

class AdminSMSTemplateController extends BaseAdminController
{
    private $permission_view = 'sendSmsTemplate_view';
    private $permission_full = 'sendSmsTemplate_full';
    private $permission_delete = 'sendSmsTemplate_delete';
    private $permission_create = 'sendSmsTemplate_create';
    private $permission_edit = 'sendSmsTemplate_edit';

    private $error = array();
    private $viewPermission = array();//check quyen

    public function __construct()
    {
        parent::__construct();
        CGlobal::$pageAdminTitle = 'Quản lý menu';
    }

    public function getPermissionPage(){
        return $this->viewPermission = [
            'is_root'=> $this->is_root ? 1:0,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_delete'=>in_array($this->permission_delete, $this->permission) ? 1 : 0,
            'permission_full'=>in_array($this->permission_full, $this->permission) ? 1 : 0,
        ];
    }

    public function view() {
        //Check phan quyen.
        if(!$this->is_root && !in_array($this->permission_full,$this->permission)&& !in_array($this->permission_view,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $page_no = (int) Request::get('page_no',1);
        $dataSearch['template_name'] = addslashes(Request::get('name_template_s',''));
        $limit = CGlobal::number_limit_show;
        $total = 0;
        $offset = ($page_no - 1) * $limit;
        $data = SmsTemplate::searchByCondition($dataSearch, $limit, $offset, $total);
        $paging = $total > 0 ? Pagging::getNewPager(3,$page_no,$total,$limit,$dataSearch) : '';

        $this->viewPermission = $this->getPermissionPage();

        return view('admin.AdminSMSTemplate.view',array_merge([
            'data'=>$data,
            'search'=>$dataSearch,
            'size'=>$total,
            'start'=>($page_no - 1) * $limit,
            'paging'=>$paging,
        ],$this->viewPermission));
    }

    public function addTemplate(){

        $name_template = isset($_POST['name_template'])?$_POST['name_template']:"";
        $content = isset($_POST['content'])?$_POST['content']:"";
        $update_at = date("Y-m-d H:i",time());
        $customer_id = $this->user_id;

        $id = isset($_POST['id'])?FunctionLib::outputId($_POST['id']):0;

        $data = array(
            "template_name"=>$name_template,
            "content"=>$content,
            "updated_date"=>$update_at,
            "created_date"=>$update_at,
            "customer_id"=>$customer_id,
        );

        if ($id!=0 && $id!="0" && $id>0){
            unset($data['created_date']);
            SmsTemplate::updateItem($id,$data);
        }else{
            SmsTemplate::createItem($data);
        }
        $data_full = SmsTemplate::getAll();
        $data_view = [
            'view' => View::make('admin.AdminSMSTemplate.list')
                ->with('data', $data_full)
                ->render()
        ];

        return Response::json($data_view, 200);
    }

    public function deleteTemplate(){
        $id = isset($_GET['id'])?FunctionLib::outputId($_GET['id']):0;
        if ($id>0){
            SmsTemplate::deleteItem($id);
        }
        $data_full = SmsTemplate::getAll();
        $data_view = [
            'view' => View::make('admin.AdminSMSTemplate.list')
                ->with('data', $data_full)
                ->render()
        ];
        return Response::json($data_view, 200);
    }
}
