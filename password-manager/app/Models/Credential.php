<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class Credential extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'username',
        'encrypted_password',
        'url',
        'notes',
        'tags',
        'custom_fields',
        'group_id',
        'created_by',
        'updated_by',
        'password_changed_at',
        'expires_at',
        'is_favorite',
        'access_count',
        'last_accessed_at',
    ];

    protected $hidden = [
        'encrypted_password',
    ];

    protected $casts = [
        'tags' => 'array',
        'custom_fields' => 'array',
        'password_changed_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_favorite' => 'boolean',
    ];

    /**
     * Get the group that owns the credential.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * Get the user who created the credential.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the credential.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Set the password attribute (encrypt it).
     */
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['encrypted_password'] = Crypt::encrypt($value);
        $this->attributes['password_changed_at'] = now();
    }

    /**
     * Get the decrypted password.
     */
    public function getPasswordAttribute(): ?string
    {
        if (!$this->encrypted_password) {
            return null;
        }

        try {
            return Crypt::decrypt($this->encrypted_password);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Check if password has expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if password will expire soon (within 30 days).
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        return $this->expires_at && $this->expires_at->isBefore(now()->addDays($days));
    }

    /**
     * Increment access count and update last accessed time.
     */
    public function recordAccess(): void
    {
        $this->increment('access_count');
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Scope to get favorites.
     */
    public function scopeFavorites($query)
    {
        return $query->where('is_favorite', true);
    }

    /**
     * Scope to get expired credentials.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    /**
     * Scope to get expiring soon credentials.
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('expires_at', '<', now()->addDays($days))
                     ->where('expires_at', '>', now());
    }

    /**
     * Search credentials by title, username, url, or tags.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('username', 'like', "%{$term}%")
              ->orWhere('url', 'like', "%{$term}%")
              ->orWhere('notes', 'like', "%{$term}%")
              ->orWhereJsonContains('tags', $term);
        });
    }

    /**
     * Get password strength score (0-100).
     */
    public function getPasswordStrength(): int
    {
        $password = $this->password;
        if (!$password) {
            return 0;
        }

        $score = 0;
        $length = strlen($password);

        // Length
        if ($length >= 8) $score += 25;
        if ($length >= 12) $score += 25;

        // Character variety
        if (preg_match('/[a-z]/', $password)) $score += 10;
        if (preg_match('/[A-Z]/', $password)) $score += 10;
        if (preg_match('/[0-9]/', $password)) $score += 15;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $score += 15;

        return min(100, $score);
    }

    /**
     * Generate a strong password.
     */
    public static function generatePassword(int $length = 16, bool $includeSymbols = true): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        if ($includeSymbols) {
            $chars .= '!@#$%^&*()_+-=[]{}|;:,.<>?';
        }

        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }

        return $password;
    }
}
