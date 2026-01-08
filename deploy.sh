#!/bin/bash

#######################################################################
# Codexse Production Deployment Script
#
# Usage:
#   ./deploy.sh              - Full deployment
#   ./deploy.sh --quick      - Quick deploy (skip composer)
#   ./deploy.sh --rollback   - Rollback to previous release
#   ./deploy.sh --maintenance-on   - Enable maintenance mode
#   ./deploy.sh --maintenance-off  - Disable maintenance mode
#######################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
NPM_BIN="${NPM_BIN:-npm}"
GIT_BRANCH="${GIT_BRANCH:-main}"
BACKUP_DIR="${APP_DIR}/storage/backups"
LOG_FILE="${APP_DIR}/storage/logs/deploy.log"

# Functions
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

success() {
    echo -e "${GREEN}✓${NC} $1" | tee -a "$LOG_FILE"
}

warning() {
    echo -e "${YELLOW}⚠${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}✗${NC} $1" | tee -a "$LOG_FILE"
    exit 1
}

header() {
    echo ""
    echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
    echo -e "${BLUE}  $1${NC}"
    echo -e "${BLUE}═══════════════════════════════════════════════════════════${NC}"
    echo ""
}

# Pre-deployment checks
pre_deploy_checks() {
    header "Pre-Deployment Checks"

    # Check if running as root (not recommended)
    if [ "$EUID" -eq 0 ]; then
        warning "Running as root is not recommended"
    fi

    # Check PHP version
    PHP_VERSION=$($PHP_BIN -r "echo PHP_VERSION;")
    if [[ "${PHP_VERSION}" < "8.2" ]]; then
        error "PHP 8.2+ required. Current: ${PHP_VERSION}"
    fi
    success "PHP version: ${PHP_VERSION}"

    # Check if .env exists
    if [ ! -f "${APP_DIR}/.env" ]; then
        error ".env file not found"
    fi
    success ".env file exists"

    # Check environment
    APP_ENV=$(grep "^APP_ENV=" "${APP_DIR}/.env" | cut -d '=' -f2)
    if [ "$APP_ENV" != "production" ]; then
        warning "APP_ENV is '${APP_ENV}', not 'production'"
    fi

    # Check debug mode
    APP_DEBUG=$(grep "^APP_DEBUG=" "${APP_DIR}/.env" | cut -d '=' -f2)
    if [ "$APP_DEBUG" == "true" ]; then
        warning "APP_DEBUG is enabled - should be 'false' in production"
    fi

    # Check disk space (require at least 1GB free)
    FREE_SPACE=$(df -m "${APP_DIR}" | awk 'NR==2 {print $4}')
    if [ "$FREE_SPACE" -lt 1024 ]; then
        warning "Low disk space: ${FREE_SPACE}MB free"
    else
        success "Disk space: ${FREE_SPACE}MB free"
    fi

    # Check if git is clean
    if [ -n "$(git -C ${APP_DIR} status --porcelain)" ]; then
        warning "Git working directory is not clean"
    else
        success "Git working directory is clean"
    fi
}

# Create backup
create_backup() {
    header "Creating Backup"

    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_PATH="${BACKUP_DIR}/${TIMESTAMP}"

    mkdir -p "$BACKUP_PATH"

    # Backup database
    log "Backing up database..."
    $PHP_BIN artisan db:backup --filename="${BACKUP_PATH}/database.sql" 2>/dev/null || {
        # Fallback: use mysqldump if artisan command doesn't exist
        DB_NAME=$(grep "^DB_DATABASE=" "${APP_DIR}/.env" | cut -d '=' -f2)
        DB_USER=$(grep "^DB_USERNAME=" "${APP_DIR}/.env" | cut -d '=' -f2)
        DB_PASS=$(grep "^DB_PASSWORD=" "${APP_DIR}/.env" | cut -d '=' -f2)
        DB_HOST=$(grep "^DB_HOST=" "${APP_DIR}/.env" | cut -d '=' -f2)

        if command -v mysqldump &> /dev/null; then
            mysqldump -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" > "${BACKUP_PATH}/database.sql" 2>/dev/null
            success "Database backed up"
        else
            warning "Could not backup database (mysqldump not found)"
        fi
    }

    # Backup .env
    cp "${APP_DIR}/.env" "${BACKUP_PATH}/.env.backup"
    success ".env backed up"

    # Store current git commit for rollback
    git -C "${APP_DIR}" rev-parse HEAD > "${BACKUP_PATH}/git_commit"
    success "Git commit stored: $(cat ${BACKUP_PATH}/git_commit)"

    # Cleanup old backups (keep last 5)
    ls -dt ${BACKUP_DIR}/*/ 2>/dev/null | tail -n +6 | xargs rm -rf 2>/dev/null || true

    success "Backup created at: ${BACKUP_PATH}"
}

# Enable maintenance mode
maintenance_on() {
    log "Enabling maintenance mode..."
    $PHP_BIN "${APP_DIR}/artisan" down --retry=60 --refresh=15 --secret="codexse-maintenance-bypass" || true
    success "Maintenance mode enabled (bypass: /codexse-maintenance-bypass)"
}

# Disable maintenance mode
maintenance_off() {
    log "Disabling maintenance mode..."
    $PHP_BIN "${APP_DIR}/artisan" up || true
    success "Maintenance mode disabled"
}

# Pull latest code
pull_code() {
    header "Pulling Latest Code"

    log "Fetching from origin..."
    git -C "${APP_DIR}" fetch origin "$GIT_BRANCH"

    log "Pulling changes..."
    git -C "${APP_DIR}" pull origin "$GIT_BRANCH"

    CURRENT_COMMIT=$(git -C "${APP_DIR}" rev-parse --short HEAD)
    success "Code updated to: ${CURRENT_COMMIT}"
}

# Install dependencies
install_dependencies() {
    header "Installing Dependencies"

    log "Installing Composer dependencies..."
    cd "${APP_DIR}"
    COMPOSER_ALLOW_SUPERUSER=1 $COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction
    success "Composer dependencies installed"

    log "Installing NPM dependencies..."
    $NPM_BIN ci --production 2>/dev/null || $NPM_BIN install --production
    success "NPM dependencies installed"
}

# Build assets
build_assets() {
    header "Building Assets"

    log "Building production assets..."
    cd "${APP_DIR}"
    $NPM_BIN run build
    success "Assets built"
}

# Run migrations
run_migrations() {
    header "Running Migrations"

    log "Running database migrations..."
    $PHP_BIN "${APP_DIR}/artisan" migrate --force
    success "Migrations completed"
}

# Clear and rebuild caches
optimize_app() {
    header "Optimizing Application"

    log "Clearing old caches..."
    $PHP_BIN "${APP_DIR}/artisan" cache:clear
    $PHP_BIN "${APP_DIR}/artisan" config:clear
    $PHP_BIN "${APP_DIR}/artisan" route:clear
    $PHP_BIN "${APP_DIR}/artisan" view:clear
    $PHP_BIN "${APP_DIR}/artisan" event:clear
    success "Old caches cleared"

    log "Building new caches..."
    $PHP_BIN "${APP_DIR}/artisan" config:cache
    $PHP_BIN "${APP_DIR}/artisan" route:cache
    $PHP_BIN "${APP_DIR}/artisan" view:cache
    $PHP_BIN "${APP_DIR}/artisan" event:cache
    success "New caches built"

    log "Optimizing autoloader..."
    $PHP_BIN "${APP_DIR}/artisan" optimize
    success "Application optimized"

    # Filament specific
    log "Caching Filament components..."
    $PHP_BIN "${APP_DIR}/artisan" filament:cache-components 2>/dev/null || true
    $PHP_BIN "${APP_DIR}/artisan" icons:cache 2>/dev/null || true
    success "Filament optimized"
}

# Restart queue workers
restart_queues() {
    header "Restarting Queue Workers"

    log "Restarting queue workers..."
    $PHP_BIN "${APP_DIR}/artisan" queue:restart
    success "Queue restart signal sent"

    # If using Supervisor
    if command -v supervisorctl &> /dev/null; then
        log "Restarting Supervisor workers..."
        supervisorctl restart all 2>/dev/null || warning "Supervisor restart failed (may need sudo)"
    fi
}

# Set permissions
set_permissions() {
    header "Setting Permissions"

    log "Setting directory permissions..."
    chmod -R 755 "${APP_DIR}/storage"
    chmod -R 755 "${APP_DIR}/bootstrap/cache"

    # Set ownership if running as root
    if [ "$EUID" -eq 0 ]; then
        WEB_USER="${WEB_USER:-www-data}"
        chown -R "$WEB_USER:$WEB_USER" "${APP_DIR}/storage"
        chown -R "$WEB_USER:$WEB_USER" "${APP_DIR}/bootstrap/cache"
    fi

    success "Permissions set"
}

# Health check
health_check() {
    header "Health Check"

    # Check if app responds
    APP_URL=$(grep "^APP_URL=" "${APP_DIR}/.env" | cut -d '=' -f2)

    log "Checking application health..."

    # Test artisan
    if $PHP_BIN "${APP_DIR}/artisan" --version > /dev/null 2>&1; then
        success "Artisan is working"
    else
        error "Artisan check failed"
    fi

    # Test database connection
    if $PHP_BIN "${APP_DIR}/artisan" db:show > /dev/null 2>&1; then
        success "Database connection OK"
    else
        warning "Database connection check failed"
    fi

    # Test HTTP if curl available
    if command -v curl &> /dev/null && [ -n "$APP_URL" ]; then
        HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" "${APP_URL}/up" 2>/dev/null || echo "000")
        if [ "$HTTP_STATUS" == "200" ]; then
            success "HTTP health check passed (${APP_URL}/up)"
        else
            warning "HTTP health check returned: ${HTTP_STATUS}"
        fi
    fi

    success "Health check completed"
}

# Rollback
rollback() {
    header "Rolling Back"

    # Find latest backup
    LATEST_BACKUP=$(ls -dt ${BACKUP_DIR}/*/ 2>/dev/null | head -1)

    if [ -z "$LATEST_BACKUP" ]; then
        error "No backup found to rollback to"
    fi

    log "Rolling back to: ${LATEST_BACKUP}"

    # Get stored commit
    if [ -f "${LATEST_BACKUP}/git_commit" ]; then
        ROLLBACK_COMMIT=$(cat "${LATEST_BACKUP}/git_commit")
        log "Reverting to commit: ${ROLLBACK_COMMIT}"
        git -C "${APP_DIR}" checkout "$ROLLBACK_COMMIT"
        success "Code reverted"
    else
        warning "No git commit found in backup"
    fi

    # Restore database if backup exists
    if [ -f "${LATEST_BACKUP}/database.sql" ]; then
        log "Restoring database..."
        DB_NAME=$(grep "^DB_DATABASE=" "${APP_DIR}/.env" | cut -d '=' -f2)
        DB_USER=$(grep "^DB_USERNAME=" "${APP_DIR}/.env" | cut -d '=' -f2)
        DB_PASS=$(grep "^DB_PASSWORD=" "${APP_DIR}/.env" | cut -d '=' -f2)
        DB_HOST=$(grep "^DB_HOST=" "${APP_DIR}/.env" | cut -d '=' -f2)

        mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" < "${LATEST_BACKUP}/database.sql"
        success "Database restored"
    fi

    # Clear caches
    $PHP_BIN "${APP_DIR}/artisan" optimize:clear

    success "Rollback completed"
}

# Print summary
print_summary() {
    header "Deployment Summary"

    echo -e "${GREEN}Deployment completed successfully!${NC}"
    echo ""
    echo "  Commit:      $(git -C ${APP_DIR} rev-parse --short HEAD)"
    echo "  Branch:      ${GIT_BRANCH}"
    echo "  Time:        $(date '+%Y-%m-%d %H:%M:%S')"
    echo "  Log:         ${LOG_FILE}"
    echo ""

    # Show any warnings from .env
    APP_DEBUG=$(grep "^APP_DEBUG=" "${APP_DIR}/.env" | cut -d '=' -f2)
    APP_ENV=$(grep "^APP_ENV=" "${APP_DIR}/.env" | cut -d '=' -f2)

    if [ "$APP_DEBUG" == "true" ] || [ "$APP_ENV" != "production" ]; then
        echo -e "${YELLOW}Warnings:${NC}"
        [ "$APP_DEBUG" == "true" ] && echo "  - APP_DEBUG is still enabled"
        [ "$APP_ENV" != "production" ] && echo "  - APP_ENV is not 'production'"
        echo ""
    fi
}

# Main deployment function
deploy() {
    header "Starting Deployment"
    log "Deployment started"

    pre_deploy_checks
    create_backup
    maintenance_on

    if [ "$1" != "--quick" ]; then
        pull_code
        install_dependencies
        build_assets
    else
        log "Quick deploy - skipping composer/npm"
        pull_code
    fi

    run_migrations
    optimize_app
    set_permissions
    restart_queues

    maintenance_off
    health_check
    print_summary

    log "Deployment completed"
}

# Parse arguments
case "$1" in
    --quick)
        deploy --quick
        ;;
    --rollback)
        maintenance_on
        rollback
        maintenance_off
        ;;
    --maintenance-on)
        maintenance_on
        ;;
    --maintenance-off)
        maintenance_off
        ;;
    --health)
        health_check
        ;;
    --help)
        echo "Codexse Deployment Script"
        echo ""
        echo "Usage:"
        echo "  ./deploy.sh                  Full deployment"
        echo "  ./deploy.sh --quick          Quick deploy (skip composer/npm)"
        echo "  ./deploy.sh --rollback       Rollback to previous release"
        echo "  ./deploy.sh --maintenance-on Enable maintenance mode"
        echo "  ./deploy.sh --maintenance-off Disable maintenance mode"
        echo "  ./deploy.sh --health         Run health check only"
        echo ""
        echo "Environment variables:"
        echo "  PHP_BIN        Path to PHP binary (default: php)"
        echo "  COMPOSER_BIN   Path to Composer (default: composer)"
        echo "  NPM_BIN        Path to NPM (default: npm)"
        echo "  GIT_BRANCH     Branch to deploy (default: main)"
        echo "  WEB_USER       Web server user (default: www-data)"
        ;;
    *)
        deploy
        ;;
esac
