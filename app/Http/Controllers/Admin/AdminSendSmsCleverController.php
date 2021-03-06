<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\CarrierSetting;
use App\Http\Models\MenuSystem;
use App\Http\Models\SmsCustomer;
use App\Http\Models\SmsLog;
use App\Http\Models\SmsSendTo;
use App\Http\Models\SmsCleverSendTo;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;

use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_PageSetup;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
use PHPExcel_Settings;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;

class AdminSendSmsCleverController extends BaseAdminController
{
    private $permission_view = 'sendSmsClever_view';
    private $permission_full = 'sendSmsClever_full';
    private $permission_delete = 'sendSmsClever_delete';
    private $permission_create = 'sendSmsClever_create';
    private $permission_edit = 'sendSmsClever_edit';
    private $arrStatus = array();
    private $error = array();
    private $viewPermission = array();//check quyen

    public function __construct()
    {
        parent::__construct();
        CGlobal::$pageAdminTitle = 'Send SMS Clever';
        FunctionLib::link_js(array(
            'admin/js/user.js'
        ));
    }

    public function getDataDefault()
    {
        $this->arrStatus = array(
            CGlobal::status_block => FunctionLib::controLanguage('status_choose', $this->languageSite),
            CGlobal::status_show => FunctionLib::controLanguage('status_show', $this->languageSite),
            CGlobal::status_hide => FunctionLib::controLanguage('status_hidden', $this->languageSite));
    }

    public function getPermissionPage()
    {
        return $this->viewPermission = [
            'is_root' => $this->is_root ? 1 : 0,
            'permission_edit' => in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create' => in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_delete' => in_array($this->permission_delete, $this->permission) ? 1 : 0,
            'permission_full' => in_array($this->permission_full, $this->permission) ? 1 : 0,
        ];
    }

    public function getSendSms()
    {
        if (!$this->is_root && !in_array($this->permission_full, $this->permission) && !in_array($this->permission_edit, $this->permission) && !in_array($this->permission_create, $this->permission)) {
            return Redirect::route('admin.dashboard', array('error' => Define::ERROR_PERMISSION));
        }
        $data = array();
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSendSmsClever.add', array_merge([
            'data' => $data,
            'id' => 0,
            'key_action' => 0,
            'nameFileUpload' => '',
            'error' => $this->error,
            'user_id' => $this->user_id,
            'arrStatus' => $this->arrStatus,
        ], $this->viewPermission));
    }

    public function postSendSms()
    {
        if (!$this->is_root && !in_array($this->permission_full, $this->permission) && !in_array($this->permission_edit, $this->permission) && !in_array($this->permission_create, $this->permission)) {
            return Redirect::route('admin.dashboard', array('error' => Define::ERROR_PERMISSION));
        }
        $key_action = date('Ydmhis', time());
        $data = $_POST;
        $nameFileUpload = '';
        if ((int)trim($data['submit']) == 1) {//import excel in table SmsCleverSendTo
            $dataExcel = $this->importSmsToExcel($nameFileUpload);
            if (empty($dataExcel)){
                $this->viewPermission = $this->getPermissionPage();
                $this->error[] = 'No File Inport';
                return view('admin.AdminSendSmsClever.add', array_merge([
                    'data' => $data,
                    'id' => 0,
                    'key_action' => 0,
                    'error' => $this->error,
                    'user_id' => $this->user_id,
                    'arrStatus' => $this->arrStatus,
                ], $this->viewPermission));
            }

            //check số hợp lệ
            $dataSend = array();
            $dataPhone = array();
            $dataCarriesInput = array();

            //nội dung tin nhắn
            $send_sms_deadline = (trim($data['send_sms_deadline']) != '') ? $data['send_sms_deadline'] : '';
            //FunctionLib::debug($dataExcel);
            $arr_numberFone = array_keys($dataExcel);

            if (!empty($arr_numberFone)) {
                $dataPhone = $arr_numberFone;
            } else {
                $this->error[] = FunctionLib::controLanguage('phone_number', $this->languageSite) . ' null';
            }

            //đẩy dữ liệu theo nhà mạng
            if (empty($this->error)) {
                $carrier = CarrierSetting::getInfoCarrier();
                $arrNumberCarries = array();
                foreach ($carrier as $kc => $val_carr) {
                    $number_firsr = (trim($val_carr['first_number']) != '') ? explode(',', $val_carr['first_number']) : array();
                    if (!empty($number_firsr)) {
                        foreach ($number_firsr as $kk => $number_firsr_carr) {
                            $arrNumberCarries[$number_firsr_carr] = array(
                                'first_number' => $number_firsr_carr,
                                'carrier_id' => $val_carr['carrier_setting_id'],
                                'carrier_name' => $val_carr['carrier_name'],
                                'slipt_number' => $val_carr['slipt_number'],
                                'min_number' => $val_carr['min_number'],
                                'max_number' => $val_carr['max_number']);
                        }
                    }
                }
                //check số có phù hợp với nhà mạng
                if (!empty($carrier)) {
                    $arrMsg = array();
                    $infoPhone = array();
                    foreach ($dataPhone as $kkk => $phone_number) {
                        $lenghtNumber = strlen($phone_number);
                        foreach ($arrNumberCarries as $kk => $dauso) {
                            $pos = strpos(trim($phone_number), trim($dauso['first_number']));
                            if ($pos === 0) {
                                if ($dauso['min_number'] <= $lenghtNumber && $lenghtNumber <= $dauso['max_number']) {
                                    $infoPhone[trim($phone_number)] = array(
                                        'phone_number' => $phone_number,
                                        'lenght' => strlen($phone_number),
                                        'slipt_number' => $dauso['slipt_number'],
                                        'carrier_id' => $dauso['carrier_id'],
                                        'carrier_name' => $dauso['carrier_name']);
                                    $arrMsg[$dauso['carrier_id']][trim($phone_number)] = FunctionLib::splitStringSms($dataExcel[$phone_number], $dauso['slipt_number']);
                                } else {
                                    $this->error[] = trim($phone_number) . ' not valiable';
                                }
                            }
                        }
                        if (!empty($infoPhone) && !in_array(trim($phone_number), array_keys($infoPhone))) {
                            $this->error[] = trim($phone_number) . ' not number first';
                        }
                    }

                    //FunctionLib::debug($arrMsg);
                    //ghep data
                    if (!empty($infoPhone) && !empty($arrMsg)) {
                        foreach ($infoPhone as $k => $phone) {
                            foreach ($arrMsg[$phone['carrier_id']][$phone['phone_number']] as $kk => $msgSms) {
                                $dataSend[] = array(
                                    'phone_number' => $phone['phone_number'],
                                    'content' => $msgSms,
                                    'carrier_id' => $phone['carrier_id'],
                                    'carrier_name' => $phone['carrier_name']);

                                $dataCarriesInput[$phone['carrier_id']] = array(
                                    'carrier_id' => $phone['carrier_id'],
                                    'carrier_name' => $phone['carrier_name']);
                            }
                        }
                    }
                }
            }
            //FunctionLib::debug($dataSend);
            if (!empty($dataSend) && empty($this->error)) {

                //web_sms_clever_sendTo
                $dataInsertSmsCleverSendTo = array();
                foreach ($dataSend as $kk => $val) {
                    $dataInsertSmsCleverSendTo[] = array(
                        'user_customer_id' => $this->user_id,
                        'key_action' => $key_action,
                        'carrier_id' => $val['carrier_id'],
                        'carrier_name' => $val['carrier_name'],//mới
                        'phone_receive' => $val['phone_number'],
                        'content' => $val['content'],
                        'send_sms_deadline' => $send_sms_deadline,
                        'created_date' => FunctionLib::getDateTime(),
                    );
                }
                //FunctionLib::debug($dataInsertSmsSendTo);
                if (!empty($dataInsertSmsCleverSendTo)) {
                    SmsCleverSendTo::insertMultiple($dataInsertSmsCleverSendTo);
                }
                $this->error[] = 'Lấy thông tin gửi SMS thành công';
                //$data = array();
            }

        } elseif ((int)trim($data['submit']) == 2) {//export excel
            $dataSendClever = [];
            $totalClever = 0;
            $key_action2 = $data['key_action'];
            if ($key_action2 > 0) {
                $dataSearch['key_action'] = $key_action2;
                $dataSearch['user_customer_id'] = $this->user_id;
                $limit = CGlobal::number_show_1000;

                $offset = 0;
                $dataSendClever = SmsCleverSendTo::searchByCondition($dataSearch, $limit, $offset, $totalClever);
            }
            $this->exportData($dataSendClever);
            $this->viewPermission = $this->getPermissionPage();
            return view('admin.AdminSendSmsClever.add', array_merge([
                'data' => $data,
                'dataSendClever' => $dataSendClever,
                'totalClever' => $totalClever,
                'id' => 0,
                'key_action' => $key_action2,
                'error' => $this->error,
                'user_id' => $this->user_id,
                'arrStatus' => $this->arrStatus,
            ], $this->viewPermission));

        } elseif ((int)trim($data['submit']) == 4){//excel mẫu
            $this->exportExcelForm();
        }elseif ((int)trim($data['submit']) == 3) {//input data send
            $key_get = $data['key_action'];
            $send_sms_deadline = $data['send_sms_deadline'];
            if ($key_get > 0) {
                if ($key_get > 0) {
                    $dataSearch['key_action'] = $key_get;
                    $dataSearch['user_customer_id'] = $this->user_id;
                    $limit = CGlobal::number_show_1000;

                    $offset = 0;
                    $dataSendClever = SmsCleverSendTo::searchByCondition($dataSearch, $limit, $offset, $totalClever);
                    if ($dataSendClever && count($dataSendClever) > 0) {
                        $this->inputDataToSmsSendTo($dataSendClever, $key_get, $totalClever, $send_sms_deadline);
                    }
                }
            }
        }

        $dataSendClever = [];
        $totalClever = 0;
        $key_action3 = ($data['key_action'] > 0 && (int)$data['submit'] != 1 && (int)$data['submit'] > 0) ? $data['key_action'] : $key_action;
        if ($key_action > 0) {
            $dataSearch['key_action'] = $key_action3;
            $dataSearch['user_customer_id'] = $this->user_id;
            $limit = CGlobal::number_show_1000;

            $offset = 0;
            $dataSendClever = SmsCleverSendTo::searchByCondition($dataSearch, $limit, $offset, $totalClever);
        }
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSendSmsClever.add', array_merge([
            'data' => $data,
            'dataSendClever' => $dataSendClever,
            'totalClever' => $totalClever,
            'nameFileUpload' => $nameFileUpload,
            'id' => 0,
            'key_action' => ($totalClever > 0) ? $key_action3 : 0,
            'error' => $this->error,
            'user_id' => $this->user_id,
            'arrStatus' => $this->arrStatus,
        ], $this->viewPermission));
    }

    public function inputDataToSmsSendTo($dataSend, $key_action, $totalSend, $send_sms_deadline)
    {
        if (!empty($dataSend)) {
            $dataCarriesInput = [];

            //get tổng send SMS theo nhà mạng
            foreach ($dataSend as $kkk => $valu) {
                if (isset($dataCarriesInput[$valu['carrier_id']]['tong_sms'])) {
                    $dataCarriesInput[$valu['carrier_id']]['tong_sms'] = $dataCarriesInput[$valu['carrier_id']]['tong_sms'] + 1;
                } else {
                    $dataCarriesInput[$valu['carrier_id']]['carrier_id'] = $valu['carrier_id'];
                    $dataCarriesInput[$valu['carrier_id']]['carrier_name'] = $valu['carrier_name'];
                    $dataCarriesInput[$valu['carrier_id']]['tong_sms'] = 1;
                }
            }

            //web_sms_customer
            $dataInsertSmsCustomer = array(
                'user_customer_id' => $this->user_id,
                'status' => Define::SMS_STATUS_PROCESSING,
                'status_name' => Define::$arrSmsStatus[Define::SMS_STATUS_PROCESSING],
                'correct_number' => $totalSend,
                'created_date' => FunctionLib::getDateTime(),);
            if (trim($send_sms_deadline) != '') {
                $dataInsertSmsCustomer['sms_deadline'] = FunctionLib::getDateTime($send_sms_deadline);
            }
            $sms_customer_id = SmsCustomer::createItem($dataInsertSmsCustomer);

            //web_sms_log: bao nhiêu nhà mạng thì co bấy nhiêu bản ghi
            foreach ($dataCarriesInput as $carrier_id => &$val_carr) {
                $dataInsertSmsLog = array(
                    'user_customer_id' => $this->user_id,
                    'user_manager_id' => 0,
                    'sms_customer_id' => $sms_customer_id,
                    'carrier_id' => $val_carr['carrier_id'],
                    'carrier_name' => $val_carr['carrier_name'],
                    'total_sms' => $val_carr['tong_sms'],
                    'status' => Define::SMS_STATUS_PROCESSING,
                    'status_name' => Define::$arrSmsStatus[Define::SMS_STATUS_PROCESSING],
                    'send_date' => FunctionLib::getIntDate(),
                    'sms_deadline' => FunctionLib::getDateTime($send_sms_deadline),
                    'created_date' => FunctionLib::getDateTime(),);
                $sms_log_id = SmsLog::createItem($dataInsertSmsLog);
                $val_carr['sms_log_id'] = $sms_log_id;
            }

            //web_sms_sendTo
            $dataInsertSmsSendTo = array();
            foreach ($dataSend as $kk => $val) {
                $dataInsertSmsSendTo[] = array(
                    'sms_log_id' => isset($dataCarriesInput[$val['carrier_id']]['sms_log_id']) ? $dataCarriesInput[$val['carrier_id']]['sms_log_id'] : 0,
                    'sms_customer_id' => $sms_customer_id,
                    'user_customer_id' => $this->user_id,
                    'carrier_id' => $val['carrier_id'],
                    'phone_receive' => $val['phone_receive'],
                    'status' => Define::SMS_STATUS_PROCESSING,
                    'status_name' => Define::$arrSmsStatus[Define::SMS_STATUS_PROCESSING],
                    'content' => $val['content'],
                    'content_grafted' => $val['content'],
                    'created_date' => FunctionLib::getDateTime(),
                );
            }
            if (!empty($dataInsertSmsSendTo)) {
                SmsSendTo::insertMultiple($dataInsertSmsSendTo);
            }
            $this->error[] = 'Danh sách tin nhắn đang được chờ xử lý';
            //admin.getSendSmsClever
            //unset($_POST['send_sms_deadline']);
            $_POST['send_sms_deadline'] = '';
            $_POST['key_action'] = -1;
            $_POST['submit'] = -1;

            DB::table(Define::TABLE_SMS_CLEVER)->where('key_action', $key_action)->delete();
        }
    }

    /**
     * File excel mẫu
     */
    public function exportExcelForm()
    {
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Set Orientation, size and scaling
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // Set font
        /*$sheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB('000000');
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue("A1", "SMS Form Clever " . date('d-m-Y H:i'));
        $sheet->getRowDimension("1")->setRowHeight(32);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);*/

        // setting header
        $position_hearder = 1;
        $sheet->getRowDimension($position_hearder)->setRowHeight(30);
        $val10 = 18;
        $val35 = 35;
        $ary_cell = array(
            'A' => array('w' => $val10, 'val' => 'Phone number', 'align' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'B' => array('w' => $val35, 'val' => 'Content SMS 1', 'align' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'C' => array('w' => $val35, 'val' => 'Content SMS 2', 'align' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'D' => array('w' => $val35, 'val' => 'Content SMS 3', 'align' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'E' => array('w' => $val35, 'val' => 'Content SMS 4', 'align' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'F' => array('w' => $val35, 'val' => 'Content SMS 5', 'align' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        );

        //build header title
        foreach ($ary_cell as $col => $attr) {
            $sheet->getColumnDimension($col)->setWidth($attr['w']);
            $sheet->setCellValue("$col{$position_hearder}", $attr['val']);
            $sheet->getStyle($col)->getAlignment()->setWrapText(true);
            $sheet->getStyle($col . $position_hearder)->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '05729C'),
                        'style' => array('font-weight' => 'bold')
                    ),
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => 'FFFFFF'),
                        'size' => 10,
                        'name' => 'Verdana'
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '333333')
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => $attr['align'],
                    )
                )
            );
        }
        //hien thị dũ liệu
        $rowCount = $position_hearder + 1; // hang bat dau xuat du lieu
        $i = 1;

        // output file
        ob_clean();
        $filename = "SMS_Form_Clever_" . "_" . date("_d/m_") . '.xls';
        @header("Cache-Control: ");
        @header("Pragma: ");
        @header("Content-type: application/octet-stream");
        @header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }
    public function exportData($data)
    {
        if (empty($data)) {
            return;
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Set Orientation, size and scaling
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // Set font
        $sheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB('000000');
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue("A1", "List SMS sent " . date('d-m-Y H:i'));
        $sheet->getRowDimension("1")->setRowHeight(32);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        // setting header
        $position_hearder = 3;
        $sheet->getRowDimension($position_hearder)->setRowHeight(30);
        $val10 = 10;
        $val18 = 18;
        $val35 = 35;
        $val45 = 60;
        $val25 = 25;
        $val55 = 55;
        $ary_cell = array(
            'A' => array('w' => $val10, 'val' => 'STT', 'align' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'B' => array('w' => $val18, 'val' => 'Phone number', 'align' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'C' => array('w' => $val45, 'val' => 'Content SMS', 'align' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
            'D' => array('w' => $val18, 'val' => 'Carrier name', 'align' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
        );

        //build header title
        foreach ($ary_cell as $col => $attr) {
            $sheet->getColumnDimension($col)->setWidth($attr['w']);
            $sheet->setCellValue("$col{$position_hearder}", $attr['val']);
            $sheet->getStyle($col)->getAlignment()->setWrapText(true);
            $sheet->getStyle($col . $position_hearder)->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '05729C'),
                        'style' => array('font-weight' => 'bold')
                    ),
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => 'FFFFFF'),
                        'size' => 10,
                        'name' => 'Verdana'
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '333333')
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => $attr['align'],
                    )
                )
            );
        }
        //hien thị dũ liệu
        $rowCount = $position_hearder + 1; // hang bat dau xuat du lieu
        $i = 1;
        $break = "\r";
        foreach ($data as $k => $v) {
            $sheet->getRowDimension($rowCount)->setRowHeight(30);//chiều cao của row

            $sheet->getStyle('A' . $rowCount)->getAlignment()->applyFromArray(
                array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            $sheet->SetCellValue('A' . $rowCount, $i);

            $sheet->getStyle('B' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            $sheet->SetCellValue('B' . $rowCount, $v['phone_receive']);

            $sheet->getStyle('C' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,));
            $sheet->SetCellValue('C' . $rowCount, $v['content']);

            $sheet->getStyle('D' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            $sheet->SetCellValue('D' . $rowCount, $v['carrier_name']);

            $rowCount++;
            $i++;
        }

        // output file
        ob_clean();
        $filename = "List SMS Sent" . "_" . date("_d/m_") . '.xls';
        @header("Cache-Control: ");
        @header("Pragma: ");
        @header("Content-type: application/octet-stream");
        @header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }

    public function importSmsToExcel(&$nameFileUpload)
    {
        require(dirname(__FILE__) . '/../../../Library/ClassPhpExcel/PHPExcel/IOFactory.php');
        $rowsExcel = [];
        if (Input::hasFile('file_excel_sms_clever')) {
            $file = Input::file('file_excel_sms_clever');
            $nameFileUpload = Input::file('file_excel_sms_clever')->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            switch ($ext) {
                case 'xls':
                case 'xlsx':
                    $objPHPExcel = PHPExcel_IOFactory::load($file);
                    $objPHPExcel->setActiveSheetIndex(0);
                    $rowsExcel = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                    break;
                default:
                    $error[] = "Invalid file type";
            }
        } else {
            $error[] = "Not found file input";
        }
        //FunctionLib::debug($rowsExcel);
        if (empty($rowsExcel))
            return array();
        $arrDataInput = array();
        if (!empty($rowsExcel)) {
            unset($rowsExcel[1]);
            foreach ($rowsExcel as $key => $val) {
                if (isset($val['A']) && trim($val['A']) != '') { //phone number
                    $checkNumber = FunctionLib::checkNumberPhone(trim($val['A']));
                    if($checkNumber > 0){
                        $arrDataInput[$checkNumber] = '';
                        $content_sms = '';
                        if (isset($val['B']) && trim($val['B']) != '') {
                            $content_sms = $content_sms . ' ' . trim($val['B']);
                        }
                        if (isset($val['C']) && trim($val['C']) != '') {
                            $content_sms = $content_sms . ' ' . trim($val['C']);
                        }
                        if (isset($val['D']) && trim($val['D']) != '') {
                            $content_sms = $content_sms . ' ' . trim($val['D']);
                        }
                        if (isset($val['E']) && trim($val['E']) != '') {
                            $content_sms = $content_sms . ' ' . trim($val['E']);
                        }
                        if (isset($val['F']) && trim($val['F']) != '') {
                            $content_sms = $content_sms . ' ' . trim($val['F']);
                        }
                        $arrDataInput[$checkNumber] = $content_sms;
                    }
                }
            }
        }
        return $arrDataInput;
    }

    //ajax get nội dung gửi SMS
    public function getContentSms()
    {
        $data = array('isIntOk' => 0, 'data' => array(), 'msg' => '');
        if (!$this->is_root && !in_array($this->permission_full, $this->permission)) {
            return Response::json($data);
        }
        $sms_clever_id = (int)Request::get('sms_clever_id', 0);
        $sms_sendTo = SmsCleverSendTo::find($sms_clever_id);
        if (!empty($sms_sendTo)) {
            $data['isIntOk'] = 1;
            $data['sms_clever_id'] = $sms_clever_id;
            $data['content'] = (isset($sms_sendTo->content) && trim($sms_sendTo->content) != '') ? $sms_sendTo->content : '';
        }
        return Response::json($data);
    }

    //ajax
    public function submitContentSms()
    {
        $data = array('isIntOk' => 0, 'data' => array(), 'msg' => '');
        if (!$this->is_root && !in_array($this->permission_full, $this->permission)) {
            return Response::json($data);
        }
        $sms_clever_id = (int)Request::get('sms_clever_id', 0);
        $content_clever = Request::get('content_clever', 0);
        $key_action = Request::get('key_action', 0);
        if ($sms_clever_id > 0 && trim($content_clever) != '') {
            $dataUpdate['content'] = $content_clever;
            $id = SmsCleverSendTo::updateItem($sms_clever_id, $dataUpdate);
            if($id > 0){
                //get lại view
                $dataSearch['key_action'] = $key_action;
                $dataSearch['user_customer_id'] = $this->user_id;
                $limit = CGlobal::number_show_1000;
                $offset = $totalClever = 0;
                $dataSendClever = SmsCleverSendTo::searchByCondition($dataSearch, $limit, $offset, $totalClever);
                $html =  view('admin.AdminSendSmsClever.viewListAjax',[
                    'dataSendClever'=>$dataSendClever
                ])->render();
                $data['isIntOk'] = 1;
                $data['html'] = $html;
            }
        }
        return response()->json( $data );
    }

    //ajax
    public function remove(){
        $ids = Request::get('sms_clever_id', '');
        $id = FunctionLib::outputId($ids);
        $key_action = Request::get('key_action', 0);
        $data = array('isIntOk' => 0, 'data' => array(), 'msg' => '');
        if(!$this->is_root && !in_array($this->permission_full, $this->permission)){
            return Response::json($data);
        }
        if($id > 0){
            if(SmsCleverSendTo::deleteItem($id)){
                //get lại view
                $dataSearch['key_action'] = $key_action;
                $dataSearch['user_customer_id'] = $this->user_id;
                $limit = CGlobal::number_show_1000;
                $offset = $totalClever = 0;
                $dataSendClever = SmsCleverSendTo::searchByCondition($dataSearch, $limit, $offset, $totalClever);
                $html =  view('admin.AdminSendSmsClever.viewListAjax',[
                    'dataSendClever'=>$dataSendClever
                ])->render();
                $data['isIntOk'] = 1;
                $data['html'] = $html;
            }
        }
        return response()->json( $data );
    }
}
