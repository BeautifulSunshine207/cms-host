<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'location',
        'salary',
        'email',
        'phone',
        'avatar',
        'gender',
        'date_of_birth',
        'nationality',
        'address_line',
        'address_city',
        'address_state',
        'workload',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_team_member')
            ->withTimestamps();
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function payrollRecords()
    {
        return $this->hasMany(PayrollRecord::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function verificationHistories()
    {
        return $this->hasMany(VerificationHistory::class);
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        return strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
    }

    public function updateRequests()
    {
        return $this->hasMany(TeamMemberUpdateRequest::class);
    }

    public function pendingUpdateRequest()
    {
        return $this->hasOne(TeamMemberUpdateRequest::class)
            ->where('status', 'pending')
            ->latestOfMany();
    }

    public function documentRequests()
    {
        return $this->hasMany(TeamMemberDocumentRequest::class);
    }

    public function pendingDocumentRequests()
    {
        return $this->hasMany(TeamMemberDocumentRequest::class)
            ->where('status', 'pending')
            ->latest();
    }
}

