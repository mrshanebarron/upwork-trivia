#!/bin/bash

###############################################################################
# Production Deployment Script for Poop Bag Trivia
# Server: SiteGround (ssh.poopbagtrivia.com:18765)
# Usage: ./deploy-to-production.sh
###############################################################################

set -e  # Exit on any error

echo "=========================================="
echo "🚀 Deploying to Production"
echo "=========================================="
echo ""

# Configuration
SSH_HOST="ssh.poopbagtrivia.com"
SSH_PORT="18765"
SSH_USER="upwokfnm"
REMOTE_DIR="public_html"
BRANCH="master"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Check if SSH key exists
if [ ! -f ~/.ssh/id_rsa ] && [ ! -f ~/.ssh/id_ed25519 ]; then
    print_error "No SSH key found. Please set up SSH key authentication with SiteGround."
    echo ""
    echo "To set up SSH access:"
    echo "1. Generate SSH key: ssh-keygen -t ed25519 -C 'your_email@example.com'"
    echo "2. Add to SiteGround: Site Tools > Devs > SSH Keys Manager"
    echo "3. Test connection: ssh -p $SSH_PORT $SSH_USER@$SSH_HOST"
    exit 1
fi

# Confirm deployment
echo "This will deploy to: https://poopbagtrivia.com"
echo "Branch: $BRANCH"
echo ""
read -p "Continue with deployment? (y/n) " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_warning "Deployment cancelled."
    exit 0
fi

echo ""
print_status "Starting deployment..."
echo ""

# SSH and run deployment commands
ssh -p $SSH_PORT $SSH_USER@$SSH_HOST << 'ENDSSH'
set -e

echo "=========================================="
echo "📦 Pulling Latest Code"
echo "=========================================="
cd public_html

# Stash any local changes (shouldn't be any)
git stash 2>/dev/null || true

# Pull latest changes
git fetch origin
git checkout master
git pull origin master

echo ""
echo "=========================================="
echo "🧹 Clearing Laravel Caches"
echo "=========================================="

# Clear all Laravel caches
php artisan config:clear
echo "✓ Config cache cleared"

php artisan route:clear
echo "✓ Route cache cleared"

php artisan view:clear
echo "✓ View cache cleared"

php artisan cache:clear
echo "✓ Application cache cleared"

echo ""
echo "=========================================="
echo "⚡ Rebuilding Optimized Caches"
echo "=========================================="

# Rebuild caches for production performance
php artisan config:cache
echo "✓ Config cached"

php artisan route:cache
echo "✓ Routes cached"

php artisan view:cache
echo "✓ Views cached"

echo ""
echo "=========================================="
echo "✅ Deployment Complete!"
echo "=========================================="
echo ""
echo "Please verify:"
echo "1. Homepage: https://poopbagtrivia.com"
echo "2. Admin Panel: https://poopbagtrivia.com/admin"
echo ""

ENDSSH

if [ $? -eq 0 ]; then
    echo ""
    print_status "Deployment successful!"
    echo ""
    echo "=========================================="
    echo "📋 Post-Deployment Checklist"
    echo "=========================================="
    echo ""
    echo "[ ] 1. Visit https://poopbagtrivia.com"
    echo "    → Verify no login/register buttons visible"
    echo ""
    echo "[ ] 2. Visit https://poopbagtrivia.com/admin"
    echo "    → Verify Filament admin panel loads"
    echo "    → Test Rick can log in"
    echo ""
    echo "[ ] 3. Check browser console for errors"
    echo ""
    echo "[ ] 4. Test navigation links (About, Terms, Privacy)"
    echo ""
else
    print_error "Deployment failed!"
    exit 1
fi
