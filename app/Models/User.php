<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }

    /**
     * フォローしているユーザー.
     */
    public function followings(): BelongsToMany
    {
        //tableはテーブル名だけでなくモデルで指定してもいい

        return $this->belongsToMany(
            related: User::class,
            table: Follow::class,
            foreignPivotKey: 'user_id',
            relatedPivotKey: 'follow_id'
        )->withTimestamps()
            ->using(Follow::class);

        //using()は使っても使わなくてもサンプル程度では影響ない
    }

    /**
     * フォローされているユーザー.
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: Follow::class,
            foreignPivotKey: 'follow_id',
            relatedPivotKey: 'user_id'
        )->withTimestamps();
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(Status::class);
    }

    /**
     * 自分自身とフォローしているユーザーの最新ポスト.
     */
    public function timeline(): Builder
    {
        $users = $this->followings()
            ->pluck('id')
            ->add($this->id);

        return Status::with([
            'user' => fn (Builder $query) => $query->whereIn('user_id', $users)
        ])->latest();
    }

    /**
     * 相互フォロー.
     */
    public function friends(): BelongsToMany
    {
        return $this->followings()
            ->orderByPivot('created_at', 'desc')
            ->wherePivotIn('follow_id', $this->followers()->pluck('id'));

        // wherePivotInの代わりにwhereInでも大体同じ
        //->whereIn('id', $this->followers()->pluck('id'))
    }
}
