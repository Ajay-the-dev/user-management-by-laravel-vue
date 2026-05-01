<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'mobile',
        'dob',
        'gender',
        'address',
        'name',
        'profile_picture',
        'role',
        'course',
        'rollNo',
        'university',
        'location',
        'visaType',
        'visaStatus',
        'visaExpiryDate',
        'insuranceStatus',
        'insuranceExpiryDate',
        'designation',
        'department',
        'batchId',
        'parentDetails',
        'passportNumber',
        'passportExpiryDate',
        'passportIssuingCountry',
        'passportIssueDate',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'date',
            'visaExpiryDate' => 'date',
            'insuranceExpiryDate' => 'date',
            'address' => 'array',
            'parentDetails' => 'array',
        ];
    }

    public function batches()
    {
        return $this->belongsTo(batches::class, 'batchId');
    }

    public function StudentDoc()
    {
        return $this->hasMany(StudentDoc::class, 'userId');
    }
}
