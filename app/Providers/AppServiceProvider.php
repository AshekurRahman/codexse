<?php

namespace App\Providers;

use App\Listeners\LogFailedLogin;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Listeners\LogUserRegistered;
use App\Models\Dispute;
use App\Models\JobContract;
use App\Models\JobPosting;
use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Subscription;
use App\Observers\ReviewObserver;
use App\Policies\DisputePolicy;
use App\Policies\JobContractPolicy;
use App\Policies\JobPostingPolicy;
use App\Policies\ProductPolicy;
use App\Policies\ServiceOrderPolicy;
use App\Policies\ServicePolicy;
use App\Policies\SubscriptionPolicy;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force root URL for subdirectory installations
        URL::forceRootUrl(config('app.url'));

        // Set strong password defaults for all password validations
        Password::defaults(function () {
            return Password::min(12)
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(); // Check against breached password databases
        });

        // Register observers
        Review::observe(ReviewObserver::class);

        // Register policies - all defined policies must be registered
        Gate::policy(Subscription::class, SubscriptionPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Service::class, ServicePolicy::class);
        Gate::policy(JobPosting::class, JobPostingPolicy::class);
        Gate::policy(JobContract::class, JobContractPolicy::class);
        Gate::policy(Dispute::class, DisputePolicy::class);
        Gate::policy(ServiceOrder::class, ServiceOrderPolicy::class);

        // Register activity log event listeners
        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogSuccessfulLogout::class);
        Event::listen(Failed::class, LogFailedLogin::class);
        Event::listen(Registered::class, SendEmailVerificationNotification::class);
        Event::listen(Registered::class, LogUserRegistered::class);

        // Register Blade directive for CSP nonce
        // Usage in templates: <script nonce="@cspNonce">...</script>
        Blade::directive('cspNonce', function () {
            return "<?php echo request()->attributes->get('cspNonce', ''); ?>";
        });

        // Database query monitoring for slow queries
        $this->registerQueryMonitoring();
    }

    /**
     * Register database query monitoring for slow queries.
     */
    protected function registerQueryMonitoring(): void
    {
        // Only enable if configured
        if (!config('database.log_slow_queries', true)) {
            return;
        }

        $slowQueryThreshold = (float) config('database.slow_query_threshold', 1000); // ms

        DB::listen(function ($query) use ($slowQueryThreshold) {
            // Skip if query is fast enough
            if ($query->time < $slowQueryThreshold) {
                return;
            }

            // Wrap in try-catch to prevent logging errors from breaking the app
            try {
                // Get the caller location for debugging
                $caller = $this->getQueryCaller();

                // Determine severity based on query time
                $severity = match (true) {
                    $query->time >= 5000 => 'critical', // 5+ seconds
                    $query->time >= 3000 => 'error',    // 3+ seconds
                    default => 'warning',               // 1+ seconds (threshold)
                };

                $logData = [
                    'sql' => $query->sql,
                    'bindings' => $this->formatBindings($query->bindings, $query->sql),
                    'time_ms' => round($query->time, 2),
                    'connection' => $query->connectionName,
                    'caller' => $caller,
                ];

                // Log based on severity
                match ($severity) {
                    'critical' => Log::critical('CRITICAL: Extremely slow query detected', $logData),
                    'error' => Log::error('Slow query detected (3s+)', $logData),
                    default => Log::warning('Slow query detected', $logData),
                };
            } catch (\Throwable $e) {
                // Silently fail - don't let monitoring break the app
            }
        });
    }

    /**
     * Get the file and line that triggered the query.
     */
    protected function getQueryCaller(): ?string
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 50);

        foreach ($trace as $frame) {
            if (!isset($frame['file'])) {
                continue;
            }

            // Skip framework and vendor files
            if (str_contains($frame['file'], '/vendor/') ||
                str_contains($frame['file'], 'AppServiceProvider')) {
                continue;
            }

            return basename($frame['file']) . ':' . ($frame['line'] ?? '?');
        }

        return null;
    }

    /**
     * Format query bindings for logging (mask sensitive values).
     */
    protected function formatBindings(array $bindings, string $sql = ''): array
    {
        // Patterns that indicate sensitive data in SQL context
        $sensitivePatterns = [
            'password', 'passwd', 'pwd',
            'secret', 'token', 'api_key', 'apikey',
            'credit', 'card', 'cvv', 'cvc',
            'ssn', 'social_security',
            'auth', 'bearer',
        ];

        // Check if SQL contains sensitive column references
        $sqlLower = strtolower($sql);
        $hasSensitiveContext = false;
        foreach ($sensitivePatterns as $pattern) {
            if (str_contains($sqlLower, $pattern)) {
                $hasSensitiveContext = true;
                break;
            }
        }

        return array_map(function ($binding) use ($hasSensitiveContext) {
            // Handle null
            if ($binding === null) {
                return null;
            }

            // Handle DateTime objects
            if ($binding instanceof \DateTimeInterface) {
                return $binding->format('Y-m-d H:i:s');
            }

            // Handle objects that can't be cast to string
            if (is_object($binding)) {
                if (method_exists($binding, '__toString')) {
                    $binding = (string) $binding;
                } else {
                    return '[object:' . get_class($binding) . ']';
                }
            }

            // Handle arrays
            if (is_array($binding)) {
                return '[array:' . count($binding) . ' items]';
            }

            // Handle binary data
            if (is_string($binding) && !mb_check_encoding($binding, 'UTF-8')) {
                return '[binary:' . strlen($binding) . ' bytes]';
            }

            // Mask if in sensitive context and looks like sensitive data
            if ($hasSensitiveContext && is_string($binding) && strlen($binding) >= 8) {
                // Mask anything that looks like it could be a password/token
                if (preg_match('/^[\$2aby\$]/', $binding) || // bcrypt hash
                    preg_match('/^[a-f0-9]{32,}$/i', $binding) || // hex hash
                    strlen($binding) > 20) { // long strings in sensitive context
                    return '[MASKED]';
                }
            }

            // Truncate long strings
            if (is_string($binding) && strlen($binding) > 100) {
                return substr($binding, 0, 100) . '...[truncated]';
            }

            return $binding;
        }, $bindings);
    }
}
