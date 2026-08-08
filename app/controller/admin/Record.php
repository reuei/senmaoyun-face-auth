<?php
namespace app\controller\admin;
use app\controller\Base;
class Record extends Base { public function index() { return $this->fetch('admin/record'); } }