#!/bin/bash

# Script to find all files that don't have a .py extension
# Created: $(date)

# Default search directory is current directory
SEARCH_DIR="${1:-.}"

echo "Searching for non-Python files in: $SEARCH_DIR"
echo "----------------------------------------"

# Find all files that don't end with .py
find "$SEARCH_DIR" -type f -not -name "*.py" | sort

echo "----------------------------------------"
echo "Search complete."
