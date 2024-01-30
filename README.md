# Patient-Administration Git Flow

This document outlines the recommended Git workflow for working with the `patient-administration` repository. Follow these steps to ensure a structured and organized development process.

## Cloning the repository

To clone the repository, execute the following command in your terminal:
```
git clone http://192.168.130.127/hmis/patient-administration.git
```

## Start from the Default Branch

Ensure that you are on the default branch (`develop` in our case) before starting any work. To verify your current branch, use the following command:

```
git branch
```

If you are on a different branch, switch to the default branch using:

```
git checkout develop
```

Once on the default branch, update it to the latest version by executing:

```
git pull
```

## Creating a New Feature Branch

If you have already pulled the repository and are starting work on a new feature, create a new branch using the following command:

```
git checkout -b new-feature
```

## Working on a Feature

When working on a new feature, it is essential to add and commit your changes regularly. This ensures that you can revert back to previous states if necessary. Follow these steps:

```
git add .
git commit -m "message to what you have done in this feature or changes or bug fix"
```
## Pushing Changes to the Remote Repository

Push your committed changes to the remote repository using the following command:

```
git push --set-upstream origin new-feature
```

The output of this command will include a link to create a merge request for the new feature

```
git push --set-upstream origin new-feature
Enumerating objects: 29, done.
Counting objects: 100% (29/29), done.
Delta compression using up to 8 threads
Compressing objects: 100% (19/19), done.
Writing objects: 100% (19/19), 2.34 KiB | 800.00 KiB/s, done.
Total 19 (delta 13), reused 0 (delta 0), pack-reused 0
remote: 
remote: To create a merge request for new-feature, visit:
remote:   http://192.168.130.127/hmis/patient-administration/-/merge_requests/new?merge_request%5Bsource_branch%5D=new-feature
remote:
To http://192.168.130.127/hmis/patient-administration.git
 * [new branch]      new-feature -> new-feature
branch 'new-feature' set up to track 'origin/new-feature'.
```

## Creating a merge request

After pushing your changes to the remote repository, you can follow the link generated in the output of the `git push` command to create a merge request. A merge request is a request to merge your changes from the feature branch into the main branch of the repository.

By following this git flow, you can clone the repository, create and switch to a new feature branch, make changes, commit them, push to the remote repository, and create a merge request for your changes. This workflow helps maintain a structured and organized development process.

