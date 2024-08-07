<?php

namespace App\Models;


//use App\Enums\UserRoleEnum;
//use App\Enums\UserStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable implements JWTSubject
{
    use Billable;
    use SoftDeletes;
    use Notifiable;

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';
    const ROLE_OWNER = 'business_owner';
    const ROLE_STAFF = 'staff';


    protected static function boot()
    {
        parent::boot();
        // Adding default page permissions when a new user is created
        static::created(function ($user) {
            $slugs = array_keys(get_user_slugs());
            // Insert default page permissions for the new user
            $timestamp = Carbon::now();
            $pagePermissions = array_map(function ($slug) use ($user,$timestamp) {
                return [
                    'user_id' => $user->id,
                    'page_slug' => $slug,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }, $slugs);

            DB::table('page_permissions')->insert($pagePermissions);
        });
    }

    /**
     *
     *
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'google_id',
        'facebook_id',
        'password',
        'phone',
        'role',
        'email_verified',
        'phone_verified',
        'status',
        'verification_token',
        'reset_token',
        'reset_token_expires',
        'password_reset',
    ];


    use HasFactory, Notifiable;


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'reset_token_expires' => 'date',
        'password_reset' => 'date'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser()
    {
        return $this->role === self::ROLE_USER;
    }

    public function isOwner()
    {
        return $this->role === self::ROLE_OWNER;
    }

    public function isStaff()
    {
        return $this->role === self::ROLE_STAFF;
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }


    public function clinics()
    {
        return $this->hasMany(Clinics::class, 'owner_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'owner_id');
    }


    /**
     * Get the page permissions for the user.
     */
    public function pagePermissions()
    {
        return $this->hasMany(PagePermission::class);
    }

    /**
     * Get the allowed page slugs for the user.
     *
     * @return array
     */
    public function allowedPageSlugs()
    {
        return $this->pagePermissions->pluck('page_slug')->toArray();
    }
}
