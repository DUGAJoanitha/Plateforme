<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'org_id',
        'role',
        'phone',
        'bio',
        'avatar_url',
        'is_active',
        'fcm_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
    ];

    /**
     * Get the organisation this user belongs to
     */
    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'org_id');
    }

    /**
     * Get all activities assigned to this user
     */
    public function assignedActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'responsible_id');
    }

    /**
     * Get all KPI measurements collected by this user
     */
    public function collectedMeasures(): HasMany
    {
        return $this->hasMany(KPIMeasure::class, 'collected_by');
    }

    /**
     * Get all expenses submitted by this user
     */
    public function submittedExpenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'submitted_by');
    }

    /**
     * Get all expenses validated by this user
     */
    public function validatedExpenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'validated_by');
    }

    /**
     * Get all field submissions made by this agent
     */
    public function fieldSubmissions(): HasMany
    {
        return $this->hasMany(FieldSubmission::class, 'agent_id');
    }

    /**
     * Get all notifications for this user
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Get all audit logs for this user
     */
    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Check if user can perform an action (permission check)
     */
    public function can($ability, $arguments = []): bool
    {
        // This can be expanded with a full permission system
        $rolePermissions = [
            'super_admin' => ['*'],
            'coordinator' => ['view_all_projects', 'manage_activities', 'validate_expenses', 'manage_teams'],
            'se_manager' => ['view_projects', 'manage_kpis', 'generate_reports', 'analyze_data'],
            'field_agent' => ['submit_data', 'view_own_activities', 'collect_kpis'],
            'accountant' => ['manage_expenses', 'view_budgets', 'generate_financial_reports'],
            'funder' => ['view_reports', 'view_dashboards'],
            'partner' => ['view_projects', 'view_dashboards'],
        ];

        $userPermissions = $rolePermissions[$this->role] ?? [];
        
        return in_array('*', $userPermissions) || in_array($ability, $userPermissions);
    }

    /**
     * Route notifications for the Vonage channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string|null
     */
    public function routeNotificationForVonage($notification)
    {
        return $this->phone;
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string|null
     */
    public function routeNotificationForFcm($notification)
    {
        return $this->fcm_token;
    }
}
