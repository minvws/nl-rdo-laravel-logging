<?php

return [
    'dblog_enabled' => env('AUDIT_DBLOG_ENABLED', false),
    'dblog_encrypt' => env('AUDIT_DBLOG_THEIR_PUB_KEY') != null,
    'dblog_pubkey' => env('AUDIT_DBLOG_THEIR_PUB_KEY'),
    'dblog_secret' => env('AUDIT_DBLOG_OUR_PRIV_KEY'),
    'syslog_enabled' => env('AUDIT_SYSLOG_ENABLED', false),
    'rabbitmq_enabled' => env('AUDIT_RABBITMQ_ENABLED', false),

    // The model we use to write data to the database via the dblogger
    'auditlog_model' => env('AUDIT_MODEL', MinVWS\Logging\Laravel\Models\AuditLog::class),

    // Automatically logs the complete HTTP request
    'log_full_request' => env('AUDIT_LOG_FULL_REQUEST', false),

    // Keys for encrypted logging
    'syslog_encrypt' => env('AUDIT_SYSLOG_THEIR_PUB_KEY') != null,
    'syslog_pubkey' => env('AUDIT_SYSLOG_THEIR_PUB_KEY'),
    'syslog_secret' => env('AUDIT_SYSLOG_OUR_PRIV_KEY'),
    'syslog_channel' => env('AUDIT_SYSLOG_CHANNEL'),

    'rabbitmq_additional_allowed_events' => [],
    'rabbitmq_log_pii' => env('AUDIT_RABBITMQ_LOG_PII', false),
];
