<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'role',
    'profile_image',
    'verification_code',
    'verification_code_expires_at',
    'banned_at',
    'ban_reason',
    'ban_until',
    'warning_count',
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
        'banned_at' => 'datetime',
        'ban_until' => 'datetime',
        ];
    }


    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }

    public function savedProjects()
    {
        return $this->hasMany(SavedProject::class);
    }

    public function classes()
    {
        return $this->belongsToMany(Classroom::class, 'class_student', 'user_id', 'class_id');
    }

    public function isInstructor()
    {
        return $this->role === 'instructor';
    }

    public function isAdministrator()
    {
        return $this->role === 'administrator';
    }

    public function canManageAdminArea()
    {
        return in_array($this->role, ['instructor', 'administrator'], true);
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    /**
     * Generate and save a verification code
     */
    public function generateVerificationCode()
    {
        $this->verification_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->verification_code_expires_at = now()->addMinutes(10);
        $this->save();
        return $this->verification_code;
    }

    /**
     * Verify if the provided code matches and is not expired
     */
    public function verifyCode($code)
    {
        return $this->verification_code === $code && 
               $this->verification_code_expires_at && 
               now()->lessThan($this->verification_code_expires_at);
    }

    /**
     * Clear the verification code after successful verification
     */
    public function clearVerificationCode()
    {
        $this->verification_code = null;
        $this->verification_code_expires_at = null;
        $this->email_verified_at = now();
        $this->save();
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function submissions()
    {
        return $this->hasMany(AssessmentSubmission::class);
    }
    public function isBanned(): bool
    {
        if ($this->ban_until && now()->lt($this->ban_until)) {
            return true;
        }
        return $this->banned_at !== null && $this->ban_until === null;
    }

    public function addWarning(string $reason): void
    {
        $this->increment('warning_count');
        
        if ($this->warning_count >= 3) {
            $this->update([
                'banned_at' => now(),
                'ban_reason' => 'Auto-banned after 3 warnings. Last warning: ' . $reason,
                'ban_until' => null,
            ]);
        }
    }

    public function ban(string $reason, $until = null): void
    {
        $this->update([
            'banned_at' => now(),
            'ban_reason' => $reason,
            'ban_until' => $until,
        ]);
    }

    public function unban(): void
    {
        $this->update([
            'banned_at' => null,
            'ban_reason' => null,
            'ban_until' => null,
            'warning_count' => 0,
        ]);
    }
}
