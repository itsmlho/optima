#!/bin/bash

# Production Server Quick Commands
# After SSH: source production_commands.sh

alias prod-composer='composer install --no-dev --optimize-autoloader'
alias prod-permissions='chmod -R 755 app/ && chmod -R 777 writable/'
alias prod-logs='tail -f writable/logs/*.log'
alias prod-clear-cache='rm -rf writable/cache/* writable/session/*'
alias prod-git-pull='git pull origin main'
alias prod-git-status='git status'

echo "✅ Production aliases loaded:"
echo "  - prod-composer      : Install dependencies"
echo "  - prod-permissions   : Fix permissions"
echo "  - prod-logs          : View live logs"
echo "  - prod-clear-cache   : Clear cache & sessions"
echo "  - prod-git-pull      : Pull latest code"
echo "  - prod-git-status    : Check git status"
