<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMemberDocumentRequest extends Model
{
    protected $fillable = [
        'team_member_id',
        'name',
        'size',
        'type',
        'path',
        'status',
        'remarks',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class);
    }
}

