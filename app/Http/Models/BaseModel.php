<?php
namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {

    public  function  getDataResource($request, $flgEncode = true) {
        $fields = $this->fillable;
        $data = array();
        if(!empty($fields)) {
            foreach($fields as $field) {
               if($request::exists($field)) {
                   $data[$field] = ($flgEncode) ? urldecode($request::get($field)) : $request::get($field);
               }
            }
        }
        return $data;
    }



    public function getDataArray($aryData = array(), $flgEncode = true) {
        $fields = $this->fillable;
        if(empty($fields) && empty($aryData)) {
            return array();
        }
        $data = array();
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(isset($aryData->$field)) {
                    $data[$field] = ($flgEncode) ? urldecode($aryData->$field) : $aryData->$field;
                }
            }
        }
        return $data;
    }
}