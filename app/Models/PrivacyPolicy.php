<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivacyPolicy extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'version',
        'content',
        'is_active',
        'effective_date',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'effective_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the active privacy policy.
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('effective_date', 'desc')
            ->first();
    }

    /**
     * Activate this policy and deactivate others.
     */
    public function activate()
    {
        // Deactivate all other policies
        self::where('id', '!=', $this->id)->update(['is_active' => false]);

        // Activate this policy
        $this->update(['is_active' => true]);
    }
}
