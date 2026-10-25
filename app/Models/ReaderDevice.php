<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ReaderDevice extends Model {
 protected $fillable=['serial','name','secret','active'];
 protected $casts=['secret'=>'encrypted','active'=>'boolean'];
 protected $hidden=['secret'];
}
