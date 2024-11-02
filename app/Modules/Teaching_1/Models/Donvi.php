<?php
namespace App\Modules\Teaching_1\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donvi extends Model
{
    use HasFactory;

    protected $table = 'donvi'; // Specify the table name if it's different from the model name

    protected $fillable = [
        'title',        // Name of the unit
        'slug',         // Unique slug for the unit
        'parent_id',    // Foreign key to the parent unit
        'children_id',  // Child units in JSON format
        'status',       // Status of the unit
    ];

    protected $casts = [
        'children_id' => 'array', // Cast JSON to array
    ];

    // Define a relationship to the parent unit
    public function parent()
    {
        return $this->belongsTo(Donvi::class, 'parent_id');
    }

    // Define a relationship to the child units
    public function children()
    {
        return $this->hasMany(Donvi::class, 'parent_id');
    }
}