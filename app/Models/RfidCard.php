<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class RfidCard extends Model {
 protected $fillable=['user_id','uid_hash','uid_last4','status'];
 protected $hidden=['uid_hash'];
 public function user(){return $this->belongsTo(User::class);}
 public static function normalizeUid(string $raw):string {
  $uid=strtoupper(str_replace([':', '-', ' '],'',$raw));
  if(!preg_match('/\A[A-F0-9]{8,32}\z/D',$uid))throw new \InvalidArgumentException('UID harus 8-32 karakter hex (4-16 byte).');
  return $uid;
 }
 public static function digest(string $uid):string {return hash_hmac('sha256',self::normalizeUid($uid),config('app.key'));}
}
