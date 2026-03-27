<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * @method \Illuminate\Database\Eloquent\Relations\MorphMany notifications()
 * @method \Illuminate\Database\Eloquent\Relations\MorphMany unreadNotifications()
 * @method \Illuminate\Database\Eloquent\Relations\MorphMany readNotifications()
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'staff_id',
        'phone',
        'must_change_password',
        'password_changed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
        'password_changed_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Generate school email: ksamuel@asms.ac.ug
     */
    public static function generateSchoolEmail(string $name, ?string $staffId = null): string
    {
        $domain = config('app.school_domain', 'asms.ac.ug');

        if ($staffId) {
            $username = strtolower($staffId);
        } else {
            $parts = explode(' ', $name);
            $firstName = strtolower($parts[0]);
            $lastName = isset($parts[1]) ? strtolower($parts[1]) : '';
            $username = $firstName . ($lastName ? '.' . $lastName : '');
        }

        // Remove special characters
        $username = preg_replace('/[^a-z0-9.]/', '', $username);
        $email = $username . '@' . $domain;

        // Ensure uniqueness
        $counter = 1;
        while (self::where('email', $email)->exists()) {
            $email = $username . $counter . '@' . $domain;
            $counter++;
        }

        return $email;
    }

    /**
     * Generate default password: ASMS@2025
     */
    public static function generateDefaultPassword(): string
    {
        return 'ASMS@' . date('Y');
    }

    /**
     * Check if needs password change
     */
    public function needsPasswordChange(): bool
    {
        return $this->must_change_password === true;
    }

    /**
     * Mark password as changed
     */
    public function markPasswordChanged(): void
    {
        $this->update([
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get primary role name (from Spatie roles)
     */
    public function getPrimaryRoleAttribute(): ?string
    {
        return $this->roles->first()?->name;
    }

    /**
     * Check if user is an admin (using Spatie)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Check if user is staff (using Spatie)
     */
    public function isStaff(): bool
    {
        return $this->hasRole('Admin Staff');
    }

    /**
     * Check if user is a teacher (using Spatie)
     */
    public function isTeacher(): bool
    {
        return $this->hasRole('Teacher');
    }

    /**
     * Check if user is a parent (using Spatie)
     */
    public function isParent(): bool
    {
        return $this->hasRole('Parent');
    }

    /**
     * Check if user is a student (using Spatie)
     */
    public function isStudent(): bool
    {
        return $this->hasRole('Student');
    }

    /**
     * Get role name for display (accessor)
     * Note: The Spatie package provides the 'roles' relationship
     */
    public function getRoleNameAttribute(): string
    {
        return $this->roles->first()?->name ?? 'No Role';
    }

    /**
     * Accessor for backward compatibility with 'role' field
     * This returns the first role name from Spatie roles
     */
    public function getRoleAttribute(): string
    {
        return $this->roles->first()?->name ?? 'user';
    }

    /**
     * Mutator for backward compatibility with 'role' field
     * This assigns the role using Spatie package
     */
    public function setRoleAttribute(string $role): void
    {
        $this->syncRoles([$role]);
    }

    /**
     * Relationship for notification preferences
     */
    public function notificationPreferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }

    /**
     * Check if user has notification preference enabled
     */
    public function hasNotificationPreference($type, $category)
    {
        $preference = $this->notificationPreferences()
            ->where('type', $type)
            ->where('category', $category)
            ->first();

        return $preference ? $preference->enabled : true; // Default to true if no preference
    }

    /**
     * Get user's notification preferences
     */
    public function getNotificationPreferences()
    {
        return $this->notificationPreferences()->get()->keyBy(function($item) {
            return $item->type . '_' . $item->category;
        });
    }

    /**
     * Update notification preference
     */
    public function updateNotificationPreference($type, $category, $enabled)
    {
        return $this->notificationPreferences()->updateOrCreate(
            [
                'type' => $type,
                'category' => $category
            ],
            ['enabled' => $enabled]
        );
    }

    /**
     * Initialize default notification preferences for new users
     */
    public function initializeNotificationPreferences()
    {
        $defaultPreferences = [
            // Email preferences
            ['type' => 'email', 'category' => 'system', 'enabled' => true],
            ['type' => 'email', 'category' => 'academic', 'enabled' => true],
            ['type' => 'email', 'category' => 'financial', 'enabled' => false],
            ['type' => 'email', 'category' => 'security', 'enabled' => true],
            ['type' => 'email', 'category' => 'announcements', 'enabled' => true],

            // In-app preferences
            ['type' => 'in_app', 'category' => 'system', 'enabled' => true],
            ['type' => 'in_app', 'category' => 'academic', 'enabled' => true],
            ['type' => 'in_app', 'category' => 'financial', 'enabled' => false],
            ['type' => 'in_app', 'category' => 'security', 'enabled' => true],
            ['type' => 'in_app', 'category' => 'announcements', 'enabled' => true],

            // SMS preferences (default to false for cost reasons)
            ['type' => 'sms', 'category' => 'system', 'enabled' => false],
            ['type' => 'sms', 'category' => 'academic', 'enabled' => false],
            ['type' => 'sms', 'category' => 'financial', 'enabled' => false],
            ['type' => 'sms', 'category' => 'security', 'enabled' => true], // Security alerts via SMS
            ['type' => 'sms', 'category' => 'announcements', 'enabled' => false],
        ];

        foreach ($defaultPreferences as $preference) {
            $this->notificationPreferences()->create($preference);
        }
    }

    /**
     * Check if user should receive a specific type of notification
     * This checks both permission and preference
     */
    public function shouldReceiveNotification($notificationType, $category = null, $permission = null)
    {
        // Check permission if required
        if ($permission && !$this->can($permission)) {
            return false;
        }

        // Check notification preference
        if ($category && !$this->hasNotificationPreference($notificationType, $category)) {
            return false;
        }

        return true;
    }

    /**
     * Get users who should receive a specific notification type
     * Static method for bulk operations
     */
    public static function getUsersForNotification($permission = null, $notificationType = 'in_app', $category = null)
    {
        $query = self::query();

        // Filter by permission if specified
        if ($permission) {
            $query->whereHas('roles.permissions', function($q) use ($permission) {
                $q->where('name', $permission);
            });
        }

        return $query->get()->filter(function($user) use ($notificationType, $category) {
            return $user->shouldReceiveNotification($notificationType, $category);
        });
    }

    /**
     * Send notification to users with specific permission
     * Helper method for controllers
     */
    public static function notifyUsersWithPermission($permission, $notification, $category = null)
    {
        $users = self::getUsersForNotification($permission, 'in_app', $category);

        foreach ($users as $user) {
            $user->notify($notification);
        }
    }

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        // Return default avatar with initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

    /**
     * The "booted" method of the model
     * Initialize notification preferences when user is created
     */
    protected static function booted()
    {
        static::created(function ($user) {
            // Initialize default notification preferences for new users
            $user->initializeNotificationPreferences();
        });
    }
}
