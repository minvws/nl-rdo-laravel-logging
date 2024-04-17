<?php

declare(strict_types=1);

namespace MinVWS\Logging\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * MinVWS\Logging\Models\AuditLog
 *
 * @property string $email
 * @property array $context
 * @property string $pii_context
 * @property Carbon $created_at
 * @property string $event_code
 * @property string $action_code
 * @property string $source
 * @property bool $allowed_admin_view
 * @property bool $failed
 * @property ?string $failed_reason
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
        'context',
        'pii_context',
        'created_at',
        'event_code',
        'action_code',
        'source',
        'allowed_admin_view',
        'failed',
        'failed_reason',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'context' => 'json',
        'allowed_admin_view' => 'boolean',
        'failed' => 'boolean'
    ];
}
