<?php

namespace App\Modules\Teaching_3\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Teaching_3\Models\Enrollment;
use App\Modules\Teaching_1\Models\Student;
use app\Modules\Teaching_3\Models\Enrollment as ModelsEnrollment;

class EnrollResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'enroll_id',
        'student_id',
        'diem30',
        'diem70',
    ];

    /**
     * Relationship with Enrollment.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enroll_id');
    }

    /**
     * Relationship with Student.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}

