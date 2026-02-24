<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_member_id',
        'date_range',
        'project',  
        'days',
        'salary',
    ];

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class);
    }
}