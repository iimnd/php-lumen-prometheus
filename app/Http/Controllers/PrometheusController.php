<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\Redis;
use Prometheus\Storage\InMemory;

class PrometheusController extends Controller
{

    public $registry;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      \Prometheus\Storage\Redis::setDefaultOptions(
        [
            'host' => 'redis',
            'port' => 6379,
            'password' => null,
            'timeout' => 0.1, // in seconds
            'read_timeout' => '10', // in seconds
            'persistent_connections' => false
        ]
    );

      $this->registry = \Prometheus\CollectorRegistry::getDefault();
       
     //contoh gauge, ambil data syslog buat dimasukin ke prometheus
      $syslog= sys_getloadavg();
      $gauge = $this->registry->registerGauge('myapp', 'syslog_gauge', 'it sets', ['type']);
      $gauge->set($syslog[0], ['last_one_minute_syslog']);

    }



    public function getProme(Request $request)
    {
  $renderer = new RenderTextFormat();
  $result = $renderer->render($this->registry->getMetricFamilySamples());
  
  header('Content-type: ' . RenderTextFormat::MIME_TYPE);
  echo $result;
    }
    
  
    public function get_trx(Request $request)
    {
  
      $executionStartTime = microtime(true);
      $count = preg_match('/^[0-9]+$/', $_GET['trx_id'])
      ? intval($_GET['trx_id'])
      : floatval($_GET['trx_id']);
  
      sleep($count);
      $executionEndTime = microtime(true);
  
      $seconds = $executionEndTime - $executionStartTime;
      echo $seconds;

      //sample membuat histogram prometheus
      $histogram = $this->registry->registerHistogram('myapp', 'process_time', 'it observes', ['type'], [ 1, 2, 5]);
      $histogram->observe($seconds, ['get_trx']);


      //sample membuat counter prometheus
      $counter = $this->registry->registerCounter('myapp', 'method_counter', 'it increases', ['type']);
      $counter->incBy(1, ['get_trx']);


      //sample membuat summary prometheus, sementara belom support
      //$summary = $this->registry->registerSummary('myapp', 'process_time', 'it observes', ['type'], 600, [0.01, 0.05, 0.5, 0.95, 0.99]);
     // $summary->observe($seconds, ['get_trx']);
      //echo "ok";
    }
  
    public function get_report(Request $request)
    {
  
      $executionStartTime = microtime(true);
      
      $ch = curl_init(); 
  
     
      curl_setopt($ch, CURLOPT_URL, "https://httpstat.us/200?sleep=5000");
  
      // return the transfer as a string 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
      $output = curl_exec($ch); 
  
      curl_close($ch);      
      
      $executionEndTime = microtime(true);
  
      $seconds = $executionEndTime - $executionStartTime;
      echo $seconds;

      //sample membuat histogram prometheus
      $histogram = $this->registry->registerHistogram('myapp', 'process_time', 'it observes', ['type'], [ 1, 2, 5]);
      $histogram->observe($seconds, ['get_report']);



      //sample membuat counter prometheus
      $counter = $this->registry->registerCounter('myapp', 'method_counter', 'it increases', ['type']);
      $counter->incBy(1, ['get_report']);

      //sample membuat summary prometheus, sementara blum support
      //$summary = $this->registry->registerSummary('myapp', 'process_time', 'it observes', ['type'], 600, [0.01, 0.05, 0.5, 0.95, 0.99]);
      //$summary->observe($seconds, ['get_report']);
      //echo "ok";
    }
  
  
    public function getFlush(Request $request){
       $syslog= sys_getloadavg();
       echo $syslog[0];
     echo "flush";
      
  
    }

// ref: https://prometheus.io/docs/practices/histograms/

    //
}
