<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * MinVWS\Logging\Models\AuditLog
 *
 * @property string $email
 * @property array $request
 * @property Carbon $created_at
 * @property string $event_code
 * @property string $action_code
 * @property bool $allowed_admin_view
 * @property bool $failed
 */
class AuditLog extends Model
{
    /**
     * @var string
     */
    protected $table = 'audit_logs';

    // No PK
    public $primaryKey = null;
    public $incrementing = false;

    // No timestamps
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'email',
        'request',
        'created_at',
        'event_code',
        'action_code',
        'allowed_admin_view',
        'failed',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'request' => 'json',
    ];
}
