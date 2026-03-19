<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_member_id',
        'document_id',
        'project_id',
        'project',
        'date',
        'status',
        'remarks',
        'admin_response_name',
        'admin_response_path',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function projectItem()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
