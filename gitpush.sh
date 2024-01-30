#!/bin/bash

current_branch=$(git rev-parse --abbrev-ref HEAD)

# Get commit message from user input
read -p "Enter your new local branch name: " branch_name
read -p "Enter your commit message: " commit_message

# Check if branch exists before deleting
git branch -d $branch_name

git checkout -b $branch_name

# Add all changes to the $branch_name area
git add .

# Commit changes with the provided message
git commit -m "$commit_message"

# Switch back to the original branch
git checkout $current_branch

# Pull latest changes from the remote repository
git pull origin $current_branch

# Merge the changes from the new branch
git merge --no-ff $branch_name

# Push changes to the remote repository
git push origin $current_branch
