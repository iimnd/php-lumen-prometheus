<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\Redis;
use Prometheus\Storage\InMemory;

// include prometheus class
use App\Prometheus;

class PrometheusController extends Controller
{

    public $counter;
    public $histogram;
    public $registry;
    public $gauge; 
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

      // register metrics
      $prefix="canopus";
      $this->counter= (new Prometheus)->constructorCounter($prefix,"_counter","ini deskripsi");
      $this->gauge= (new Prometheus)->constructorGauge($prefix,"_gauge_version","ini deskripsi");
      $this->histogram= (new Prometheus)->constructorHistogram($prefix,"_histogram","ini deskripsi");
      $this->registry= (new Prometheus)->getRegistry();

      $syslog= sys_getloadavg();
      $this->gauge->set($syslog[0], ['200','GET','last_one_minute','v.1.0.0']);
  
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

      // add histogram and counter metrics
      $this->histogram->observe($seconds, ['200','GET','get_trx','v.1.0.0']);
      $this->counter->incBy(1, ['200','GET','get_trx','v.1.0.0']);

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

    
       // add histogram and counter metrics
      $this->histogram->observe($seconds, ['200','GET','get_report','v.1.0.0']);
      $this->counter->incBy(1, ['200','GET','get_report','v.1.0.0']);
      
    }
  
  
    public function getFlush(Request $request){
      echo "not supported";
      
  
    }

    // ref: https://prometheus.io/docs/practices/histograms/
    //
}
