#!/bin/bash
set -e

# Inicialize o Apache
apache2-foreground &

# Execute o Laravel
php artisan serve --host=0.0.0.0 --port=8000
