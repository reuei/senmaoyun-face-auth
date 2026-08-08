<?php
namespace app\model;
use think\Model;
class AuditLog extends Model { protected $name = 'audit_log'; protected $autoWriteTimestamp = false; }