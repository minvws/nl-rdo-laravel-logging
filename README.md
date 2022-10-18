# nl-rdo-laravel-logging


### Event codes

| Event code | routing key | Description |
|------------|-------------|-------------|
| 080001 | declaration | Declaration event |
| 080001 | registartion | Registration event |
| 080002 | log_access | Accessing logs |
| 080003 | verification_code_disabled | Disabled verification code |
|------------|-------------|-------------|
| 090001 | account_change | generic user account changes |
| 090002 | user_created | created new user |
| 090003 | reset_credentials | reset user credentials |
| 090004 | activate_account | Activate account |
| 090005 | admin_password_reset | Admin password reset |
| 090006 | amp_upload | AMP upload |
|------------|-------------|-------------|
| 090012 | organisation_created | Created new organisation |
| 090013 | organisation_changed | Updated organisation (name) |
|------------|-------------|-------------|
| 900101 | account_change | changed user data |
| 900102 | account_change | changed roles |
| 900103 | account_change | changed timeslot |
| 900104 | account_change | changed active enabled/disabled |
| 900105 | account_change | reset credentials |
|------------|-------------|-------------|
| 900201 | account_change | changed kvtb user data |
| 900202 | account_change | changed kvtb roles |
| 900203 | account_change | reset kvtb credentials |
|------------|-------------|-------------|
| 091111 | user_login | user login |
| 092222 | user_logout | user logout |
| 093333 | user_login_two_factor_failed | user login 2fa failed |
