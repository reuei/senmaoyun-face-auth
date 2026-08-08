<?php
namespace app\controller\admin;
use app\controller\Base;
class Plugin extends Base { public function index() { return $this->fetch('admin/plugin'); } }