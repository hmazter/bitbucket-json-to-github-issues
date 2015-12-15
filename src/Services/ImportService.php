<?php

namespace App\Services;

use App\Auth;
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
     * Create a GitHub issue and update with correct state for each issue in the array
     *
     * @param array $issues
     */
    public function createAndUpdateIssues($issues)
    {
        /** @var Issue $issue */
        foreach ($issues as $issue) {
            $this->createAndUpdateIssue($issue);
        }
    }

    /**
     * Create a GitHub issue and update with correct state
     *
     * @param Issue $issue
     */
    public function createAndUpdateIssue(Issue $issue)
    {
        $issueId = $this->createIssue($issue);
        $this->updateIssue($issueId, $issue);
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
}
