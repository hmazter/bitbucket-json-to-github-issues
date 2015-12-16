<?php

namespace App\Services;

use App\Comment;
use App\Issue;
use App\User;

class IssueParser
{
    private $users = [];

    public function parseJsonFileToIssues($filename)
    {
        $data = json_decode(file_get_contents($filename), true);

        /*
         * parse issues
         */
        $issues = [];
        foreach ($data['issues'] as $issueData) {
            $issue = new Issue($issueData['title'], $issueData['content'], $this->getUser($issueData['assignee']));
            $issue->parseAndSetState($issueData['status']);
            $issue->setCreatedOn($issueData['created_on']);
            $issue->setReporter($this->getUser($issueData['reporter']));

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
            if (!empty($commentData['content'])) {
                $issueId = $commentData['issue'];
                $comment = new Comment($commentData['content'], $this->getUser($commentData['user']), $commentData['created_on']);
                /** @var Issue $issue */
                $issue = $issues[$issueId];
                $issue->addComment($comment);
            }
        }

        ksort($issues);
        return $issues;
    }

    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param string $name
     * @return User
     */
    private function getUser($name)
    {
        if (!isset($this->users[$name])) {
            $assignee = new User($name);
            $this->users[$name] = $assignee;
        }

        return $this->users[$name];
    }
}
