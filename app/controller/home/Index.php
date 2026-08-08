<?php
namespace app\controller\home;

use app\controller\Base;

class Index extends Base
{
    public function index()
    {
        return $this->fetch('home/index');
    }
}