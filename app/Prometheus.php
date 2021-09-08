<?php

namespace App;

use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\Redis;
use Prometheus\Storage\InMemory;


class Prometheus
{
 
    //protected static $registry = \Prometheus\CollectorRegistry::getDefault();
    // public $gauge = $registry->registerGauge('myapp', 'version_gauge3', 'it sets', ['code','method','path','version']);
    // public $histogram = $registry->registerHistogram('myapp', 'process_time', 'it observes', ['code','method','path','version'], [ 1, 2, 5]);
    // public $counter = $registry->registerCounter('myapp', 'method_counter', 'it increases', ['code','method','path','version']);

    public $registry;
    public $gauge;
    public $counter;
    public $histogram;

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
 

    }

    public function constructorGauge($prefix='myapp',$gauge_name='gauge_version', $desc='it is desc'){
        return $this->gauge = $this->registry->registerGauge($prefix, $gauge_name, $desc, ['code','method','path','version']);

    }

    public function constructorCounter($prefix='myapp',$counter_name='my_counter_', $desc='it is desc'){
        return $this->counter = $this->registry->registerCounter($prefix, $counter_name, $desc,['code','method','path','version']);

    }

    public function constructorHistogram($prefix='myapp',$histogram_name='histogram_app', $desc='it is desc'){
        return $this->histogram = $this->registry->registerHistogram($prefix, $histogram_name, $desc, ['code','method','path','version'], [ 1, 2, 5]);

    }



    public function setCounter($code="200",$method="default", $path="path-default", $version="00" ){
        $res = $this->counter->incBy(1, [$code,$method,$path,$version]);

        if($res){
            return "success";
        }else {
            return "error";
        }


    }

    public function setHistogram($code="200",$method="default", $path="path-default", $version="00", $var=1 ){
        $res = $this->histogram->observe($var, [$code,$method,$path,$version]);

        if($res){
            return "success";
        }else {
            return "error";
        }


    }

    public function setGauge($code="200",$method="default", $path="path-default", $version="00", $var=1 ){
        $res = $this->gauge->set($var, [$code,$method,$path,$version]);

        if($res){
            return "success";
        }else {
            return "error";
        }


    }

    public function getRegistry(){
        return $this->registry;
    }


    function myname(){
        return "jahahaha";

    }
}
