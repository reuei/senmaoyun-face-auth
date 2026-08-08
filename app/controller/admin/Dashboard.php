<?php
namespace app\controller\admin;
use app\controller\Base;
class Dashboard extends Base { public function index() { return $this->fetch('admin/dashboard'); } }