#!/bin/bash

# Fix Permissions Script for Criminal Records Management System
# Run this script with sudo to fix directory permissions

echo "Fixing permissions for CRMS..."

# Get the script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

# Detect web server user (common: www-data, apache, nginx)
if id "www-data" &>/dev/null; then
    WEB_USER="www-data"
elif id "apache" &>/dev/null; then
    WEB_USER="apache"
elif id "nginx" &>/dev/null; then
    WEB_USER="nginx"
else
    echo "Warning: Could not detect web server user. Using www-data as default."
    WEB_USER="www-data"
fi

echo "Using web server user: $WEB_USER"

# Create directories if they don't exist
mkdir -p storage/logs
mkdir -p storage/cache
mkdir -p public/uploads/criminals
mkdir -p public/uploads/evidence

# Set ownership
chown -R $WEB_USER:$WEB_USER storage/
chown -R $WEB_USER:$WEB_USER public/uploads/

# Set permissions
chmod -R 755 storage/
chmod -R 755 public/uploads/
chmod -R 775 storage/logs/
chmod -R 775 storage/cache/
chmod -R 775 public/uploads/

echo "Permissions fixed!"
echo ""
echo "If you still have issues, you may need to:"
echo "1. Check SELinux context (if enabled): chcon -R -t httpd_sys_rw_content_t storage/ public/uploads/"
echo "2. Or disable SELinux temporarily for testing"
echo ""
echo "To verify, run as web server user:"
echo "sudo -u $WEB_USER touch storage/logs/test.log && sudo -u $WEB_USER rm storage/logs/test.log"

