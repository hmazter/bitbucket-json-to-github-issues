<?php

namespace App\Services;

use App\Comment;
use App\Issue;
use App\User;

class IssueParser
{
    private $assignees = [];

    public function parseJsonFileToIssues($filename)
    {
        $data = json_decode(file_get_contents($filename), true);

        /*
         * parse issues
         */
        $issues = [];
        foreach ($data['issues'] as $issueData) {
            $issue = new Issue($issueData['title'], $issueData['content'], $this->getAssignee($issueData['assignee']));
            $issue->parseAndSetState($issueData['status']);

            if (isset($issueData['priority'])) {
                $issue->addLabel($issueData['priority']);
            }
            if (isset($issueData['kind'])) {
                $issue->addLabel($issueData['kind']);
            }

            $issues[$issueData['id']] = $issue;
        }

        /*
         * Parse comments
         */
        foreach ($data['comments'] as $commentData) {
            $issueId = $commentData['issue'];
            $comment = new Comment($commentData['content'], $commentData['user'], $commentData['created_on']);
            /** @var Issue $issue */
            $issue = $issues[$issueId];
            $issue->addComment($comment);
        }

        ksort($issues);
        return $issues;
    }

    public function getAssignees()
    {
        return $this->assignees;
    }

    /**
     * @param string $name
     * @return User
     */
    private function getAssignee($name)
    {
        if (!isset($this->assignees[$name])) {
            $assignee = new User($name);
            $this->assignees[$name] = $assignee;
        }

        return $this->assignees[$name];
    }
}
