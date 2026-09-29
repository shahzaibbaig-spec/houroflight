<?php
namespace Nsms\Kcat\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'kcat_students';
    protected $fillable = ['user_id', 'student_id', 'name', 'father_name', 'class_id', 'status'];
    public function classRoom() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
    public function user() { return $this->belongsTo(config('auth.providers.users.model')); }
}
