# BitBucket json to GitHub issues

Import issues from BitBucket json to GitHub

## Features

* Match BitBucket usernames to GitHib usernames for assignees
* Merges comments from bitbucket issues to body of GitHub issues

## Usage

* Follow https://confluence.atlassian.com/bitbucket/export-or-import-issue-data-330797432.html
to export issue json from BitBucket.
* Unpack downloaded export zipfile
* run this script: `./app import [filename.json] [owner of repo (an org or a user)] [repo name]`
* Follow output on screen
