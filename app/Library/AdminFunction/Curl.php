<?php
/**
 * Created by PhpStorm.
 * User: ThinhLK
 * Date: 7/9/14
 * Time: 10:55 AM
 */
namespace App\Library\AdminFunction;

class Curl {

    protected static  $ch = null;
    protected static $instance = null;
    protected  $site = null;
    public  $build = array();
    public  $timeTotal = 0;
    private $mess_log = '';
    /*function __construct() {
            $this->init();
    }*/
    public static function getInstance(){
        if(self::$ch === null){
            self::$instance = new self();
            self::init();
            return self::$instance;
        }else{
            return self::$instance;
        }
    }
    public static function init() {
        if (!isset(self::$ch)) {
            $options = array(
                CURLOPT_RETURNTRANSFER => true,     // return web page
                CURLOPT_HEADER         => 0,    // don't return headers
                CURLOPT_FOLLOWLOCATION => 1,     // follow redirects
                CURLOPT_ENCODING       => "",       // handle all encodings
                //CURLOPT_USERAGENT      =>  $_SERVER['HTTP_USER_AGENT'], // who am i
                CURLOPT_AUTOREFERER    => true,     // set referer on redirect
                CURLOPT_CONNECTTIMEOUT => 60,      // timeout on connect
                CURLOPT_TIMEOUT        => 60,      // timeout on response
                CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
                CURLOPT_RETURNTRANSFER => 1
            );

            self::$ch = curl_init();
            curl_setopt_array( self::$ch, $options );
        }
    }

   public static  function timeCall(){
        $time = microtime();
        $time = explode(" ",$time);
        $time = $time[1] + $time[0];
       return $time;
    }
   public function get($url, $flagHttps = false) {
       $this->startRequest($url);
        if (isset(self::$ch)) {
            $startTime = self::timeCall();
            curl_setopt(self::$ch, CURLOPT_URL, $url);
            curl_setopt(self::$ch, CURLOPT_HTTPGET, 1);
            if($flagHttps) {
                curl_setopt(self::$ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt(self::$ch, CURLOPT_SSL_VERIFYPEER, 0);
            }
            $data= curl_exec(self::$ch);
            $endTime = self::timeCall();
            $load_time = round(($endTime - $startTime),5)."s";
            //$str = ' Total time call api ['.$url.' ] with method GET is ['.$load_time.']' ;
            $this->timeTotal = $this->timeTotal+$load_time;
            $this->build[]=array(
                'method'=>'GET',
                'url'=>$url,
                'time'=>$load_time
            );
            $this->write_log($data);
            return $data;
        }
    }

   public function post($url, $vars, $flagHttps = false, $flgJson = false) {
       $this->startRequest($url);
        if(isset(self::$ch)){
            $startTime = $this->timeCall();
            curl_setopt(self::$ch, CURLOPT_URL, $url);
            curl_setopt(self::$ch, CURLOPT_URL, $url);
            curl_setopt(self::$ch, CURLOPT_POST, 1);
            curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $vars);
            if($flgJson) {
                curl_setopt(self::$ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $vars);
                curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(self::$ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($vars))
                );
            }
            if($flagHttps) {
                curl_setopt(self::$ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt(self::$ch, CURLOPT_SSL_VERIFYPEER, 0);
            }
            $data= curl_exec(self::$ch);
            $endTime = self::timeCall();
            $load_time = round(($endTime - $startTime),5)."s";
            //$str = ' Total time call api ['.$url.' ] with method POST is ['.$load_time.']' ;
            $this->build[]=array(
                'method'=>'POST',
                'url'=>$url,
                'time'=>$load_time
            );
            $this->write_log($data);
            return $data;
        }
    }
   public function getSource() {
        if (isset(self::$ch)) {
            curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(self::$ch, CURLOPT_HEADER, false);
            return curl_exec(self::$ch);
        }
    }

   public function getHeaders() {
        if (isset(self::$ch)) {
            curl_setopt(self::$ch, CURLOPT_RETURNTRANSFER, false);
            curl_setopt(self::$ch, CURLOPT_HEADER, true);
            return curl_exec(self::$ch);
        }
    }

   public function getInfo() {
        if (isset(self::$ch)) {
            return curl_getinfo(self::$ch);
        }
    }

   public function close() {
        if (isset(self::$ch)) {
            curl_close(self::$ch);
        }
    }

   public function __destruct() {
        $this->close();
    }

    public function startRequest($url) {
        $this->mess_log = 'Call API: ' . $url;
    }

    function write_log($data) {
        $path = __DIR__ . '/../../storage/logs/';
        $filename = 'plaza-' . date('d_m_Y');
        $logfile = $path . $filename;
        $date = date("H:i:s", time());

        if( ($time = $_SERVER['REQUEST_TIME']) == '') {
            $time = time();
        }
        if( ($remote_addr = $_SERVER['REMOTE_ADDR']) == '') {
            $remote_addr = "REMOTE_ADDR_UNKNOWN";
        }
        if( ($request_uri = $_SERVER['REQUEST_URI']) == '') {
            $request_uri = "REQUEST_URI_UNKNOWN";
        }
        //echo"<pre>"; print_r($data); die;
        if($data != null) {
            $response = json_decode($data, true);
            $message = isset($response['message']) ? $response['message'] : 'error';
            $this->mess_log .= ', Response: ' . $message;
        } else {
            $this->mess_log .= ', Response: Unable call API';
        }
        if($fd = @fopen($logfile, "a")) {
            $result = fputcsv($fd, array($date, $remote_addr, $request_uri, $this->mess_log));
            fclose($fd);
        }
    }
} 