<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  if(!Schema::hasColumn('users','remember_token'))Schema::table('users',fn(Blueprint $t)=>$t->rememberToken());
 }
 public function down():void {
  // Prevent destructive rollback against existing sessions/accounts.
 }
};
