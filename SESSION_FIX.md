# ShareKoro 500 Error Resolution - Session Fix

## Issue Identified
The 500 Internal Server Error was caused by attempting to modify session configuration after a session had already been started. The error message was:
```
Warning: ini_set(): Session ini settings cannot be changed when a session is active
Warning: session_set_cookie_params(): Session cookie parameters cannot be changed when a session is active
```

## Root Cause
In the `init.php` file, we were trying to set session parameters (like `session.gc_maxlifetime` and cookie parameters) after a session might have already been started by the server or another script.

## Solution Implemented
Modified the `init.php` file to check the session status before attempting to configure session parameters:

```php
// Handle session configuration properly
if (session_status() === PHP_SESSION_NONE) {
    // Only set session parameters if no session is active
    require_once 'config.php';
    
    // Set session lifetime after config is loaded
    if (defined('SESSION_LIFETIME')) {
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
        session_set_cookie_params([
            'lifetime' => SESSION_LIFETIME,
            'path' => '/',
            'domain' => '',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    
    // Now start the session
    session_start();
} else {
    // Session already started, just load config
    require_once 'config.php';
}
```

## Files Updated
1. `init.php` - Main initialization file with proper session handling
2. `init-minimal.php` - Minimal initialization file with same fix

## Testing
Created additional test files to verify the fix:
- `session-fix-test.php` - Tests the session configuration fix
- `index-session-fix-test.php` - Tests index loading with the fix

## Verification
After implementing this fix:
1. No more session-related warnings
2. Site should load without 500 errors
3. Session functionality should work correctly
4. All existing functionality should remain intact

## Additional Benefits
The fix also improves the robustness of the application by:
1. Properly handling cases where sessions might already be started
2. Using modern session cookie parameter syntax
3. Adding security improvements with httponly and samesite settings
4. Ensuring configuration is only set when appropriate

This fix resolves the 500 Internal Server Error and should get your ShareKoro installation working properly on InfinityFree hosting.