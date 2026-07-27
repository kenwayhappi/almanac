<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'file_path',
        'content',
        'views',
        'position', // Assuré que position est inclus
        'end_date',
        'end_time',
        'owner_contact',
        'owner_name',
    ];

    protected $appends = ['file_url'];

    protected $casts = [
        'end_date' => 'date',
        'end_time' => 'datetime:H:i',
        'views' => 'integer',
        'position' => 'string', // Cast pour position
    ];

    public function getFileUrlAttribute()
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    /**
     * Record a view only if it's unique to this machine/session
     */
    public function recordUniqueView(?string $ipAddress = null, ?string $sessionId = null): bool
    {
        $ip = $ipAddress ?? request()->ip();
        $session = $sessionId ?? session()->getId();
        $userAgentHash = md5(request()->userAgent() ?? '');

        $alreadyViewed = \DB::table('ad_views')
            ->where('advertisement_id', $this->id)
            ->where(function ($q) use ($ip, $session, $userAgentHash) {
                $q->where('session_id', $session)
                  ->orWhere(function ($sub) use ($ip, $userAgentHash) {
                      $sub->where('ip_address', $ip)
                          ->where('user_agent_hash', $userAgentHash);
                  });
            })
            ->exists();

        if (!$alreadyViewed) {
            \DB::table('ad_views')->insert([
                'advertisement_id' => $this->id,
                'ip_address' => $ip,
                'session_id' => $session,
                'user_agent_hash' => $userAgentHash,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->increment('views');
            return true;
        }

        return false;
    }
}