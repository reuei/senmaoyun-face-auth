<?php
namespace app\model;
use think\Model;
class Admin extends Model { protected $name = 'admin'; protected $hidden = ['password']; }