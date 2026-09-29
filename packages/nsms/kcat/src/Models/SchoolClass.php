<?php
namespace Nsms\Kcat\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    protected $table = 'kcat_school_classes';
    protected $fillable = ['name', 'section'];
    public function students() { return $this->hasMany(Student::class, 'class_id'); }
}
