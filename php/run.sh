#!/bin/bash

# Check if PHP is installed
if command -v php &>/dev/null; then
    echo "PHP is already installed."
else
    echo "PHP is not installed. Installing PHP..."
    
    # Install PHP (Ubuntu)
    if [ -f /etc/lsb-release ]; then
        sudo apt update
        sudo apt install php
    fi
    
    # Install PHP (macOS)
    if [ "$(uname)" == "Darwin" ]; then
        brew install php
    fi
    
    # Add PHP to PATH
    echo 'export PATH="$PATH:/usr/bin"' >> ~/.bashrc
    source ~/.bashrc
    
    echo "PHP installation complete."
fi

# Run PHP Server
echo "Application is accessible on http://localhost:8000"
php -S localhost:8000 index.php > /dev/null 2>&1