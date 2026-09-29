<?php
namespace KcatTransferTests;
class TestUser extends \Illuminate\Foundation\Auth\User
{
    protected $table = 'users';
    protected $guarded = [];
    protected $hidden = ['password'];
}
