<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LoanScanToken extends Model {
 protected $table='loan_scan_tokens';
 protected $fillable=['peminjaman_id','token_hash','expires_at','used_at'];
 protected $casts=['expires_at'=>'datetime','used_at'=>'datetime'];
}
