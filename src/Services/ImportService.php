<?php

namespace App\Services;

use App\Auth;
use App\Comment;
use App\Issue;

class ImportService
{
    private $githubService;

    /**
     * ImportService constructor.
     * @param $owner
     * @param $repo
     * @param Auth $auth
     */
    public function __construct($owner, $repo, Auth $auth)
    {
        $this->githubService = new GitHubService($owner, $repo, $auth);
    }

    /**
     * Create a GitHub issue and update with correct state and attach comments
     *
     * @param Issue $issue
     */
    public function createAndUpdateIssue(Issue $issue)
    {
        $issueId = $this->createIssue($issue);
        foreach ($issue->getComments() as $comment) {
            $this->createComment($issueId, $comment);
        }
        $this->updateIssue($issueId, $issue);
    }

    public function listUsers()
    {
        $return = [];
        foreach($this->githubService->listUsers() as $user) {
            $return[] = $user['login'];
        }

        return $return;
    }

    private function createIssue(Issue $issue)
    {
        $response = $this->githubService->createIssue($issue);
        return $response['number'];
    }

    private function updateIssue($issueId, Issue $issue)
    {
        $this->githubService->updateIssue($issueId, $issue);
    }

    private function createComment($issueId, Comment $comment)
    {
        $this->githubService->createComment($issueId, $comment);
    }
}
