# BitBucket json to GitHub issues

Import issues from BitBucket json to GitHub

## Features

* Match BitBucket usernames to GitHib usernames for assignees and reporters
* Posts BitBucket comments as comments on GitHub issues with all previous comment data in body
* Use GitHub mentions to link issue and comment actions to GitHub users

Missing

* Does not preserve issue id from BitBucket to GitHub if some ids is missing

## Install

1. `git clone git@github.com:hmazter/bitbucket-json-to-github-issues.git`
1. `cd bitbucket-json-to-github-issues`
1. `composer install`

## Usage

1. Follow https://confluence.atlassian.com/bitbucket/export-or-import-issue-data-330797432.html
to export issue json from BitBucket.
1. Unpack downloaded export zipfile
1. run this script:
  `./app import -f [filename.json] -o [owner of repo (an org or a user)] -r [repo name] -u [username]`
1. Follow output on screen
