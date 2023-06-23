<?php
namespace mainSRC;
class main{
    public function __construct(){
        $this->DB=PDODB::instance();
    }
}