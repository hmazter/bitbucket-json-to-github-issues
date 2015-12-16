<?php

namespace App\Services;

use App\Auth;
use App\Comment;
use App\Issue;
use GuzzleHttp\Client;

class GitHubService
{
    /**
     * @var string
     */
    private $baseUrl = 'https://api.github.com';

    /**
     * @var string
     */
    private $owner;

    /**
     * @var string
     */
    private $repo;

    /**
     * @var Auth
     */
    private $auth;

    /**
     * @var int
     */
    private $rateLimitRemaining = 0;

    /**
     * GitHubService constructor.
     * @param string $owner
     * @param string $repo
     * @param Auth $auth
     */
    public function __construct($owner, $repo, Auth $auth)
    {
        $this->owner = $owner;
        $this->repo = $repo;
        $this->auth = $auth;
    }

    /**
     * @param Issue $issue
     * @return array
     */
    public function createIssue(Issue $issue)
    {
        $data = [
            'title' => $issue->getTitle(),
            'body' => $issue->getBody(),
            'assignee' => $issue->getAssignee()->getGithub(),
            'milestone' => $issue->getMilestone(),
            'labels' => $issue->getLabels(),
        ];

        return $this->post('issues', $data);
    }

    /**
     * @param int $issueId
     * @param Issue $issue
     */
    public function updateIssue($issueId, Issue $issue)
    {
        $data = [
            'state' => $issue->getState(),
        ];
        $this->post('issues/' . $issueId, $data);
    }

    /**
     * @param int $issueId
     * @param Comment $comment
     */
    public function createComment($issueId, Comment $comment)
    {
        $data = [
            'body' => $comment->getFormatted(),
        ];

        $this->post("issues/$issueId/comments", $data);
    }

    private function getUrl($endpoint)
    {
        return $this->baseUrl . "/repos/{$this->owner}/{$this->repo}/{$endpoint}";
    }

    private function post($endpoint, array $data)
    {
        $client = new Client();
        $response = $client->post(
            $this->getUrl($endpoint),
            [
                'auth' => $this->auth->getBasicHeaderArray(),
                'headers' => [
                    'Accept' => 'application/vnd.github.v3+json',
                ],
                'json' => $data
            ]
        );

        $this->rateLimitRemaining = (int)$response->getHeaderLine('X-RateLimit-Remaining');
        return json_decode($response->getBody()->getContents(), true);
    }
}
