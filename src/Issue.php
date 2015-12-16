<?php

namespace App;

class Issue
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $body;

    /**
     * @var User
     */
    private $assignee;

    /**
     * @var \DateTime
     */
    private $createdOn;

    /**
     * @var User
     */
    private $reporter;

    /**
     * @var  int
     */
    private $milestone;

    /**
     * @var string
     */
    private $state = 'open';

    /**
     * @var array
     */
    private $labels = [];

    /**
     * @var array
     */
    private $comments = [];

    /**
     * Issue constructor.
     * @param string $title
     * @param string $body
     * @param User $assignee
     */
    public function __construct($title, $body, $assignee)
    {
        $this->title = $title;
        $this->body = $body;
        $this->assignee = $assignee;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setName($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return "Created by {$this->reporter->getGitHubMention()} at {$this->createdOn->format('Y-m-d H:i:s')}\n\n"
            . "---\n"
            . $this->body;
    }

    public function getBodyWithComments()
    {
        $return = $this->getBody();

        /** @var Comment $comment */
        foreach ($this->comments as $comment) {
            $return .= "\n\n---\n";
            $return .= $comment->getFormatted();
        }

        return $return;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * @param User $assignee
     */
    public function setAssignee($assignee)
    {
        $this->assignee = $assignee;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param \DateTime|string $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        if ($createdOn instanceof \DateTime) {
            $this->createdOn = $createdOn;
        } else {
            $this->createdOn = new \DateTime($createdOn);
        }
    }

    /**
     * @return User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * @param User $reporter
     */
    public function setReporter($reporter)
    {
        $this->reporter = $reporter;
    }

    /**
     * @return int
     */
    public function getMilestone()
    {
        return $this->milestone;
    }

    /**
     * @param int $milestone
     */
    public function setMilestone($milestone)
    {
        $this->milestone = $milestone;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    public function parseAndSetState($state)
    {
        $closedStates = ['resolved', 'wontfix', 'invalid', 'duplicate', 'invalid'];
        $openStates = ['new', 'open', 'on hold'];

        if (in_array($state, $closedStates)) {
            $this->setState('closed');
            return;
        }

        if (in_array($state, $openStates)) {
            $this->setState('open');
            return;
        }
    }

    /**
     * @return array
     */
    public function getLabels()
    {
        return $this->labels;
    }

    /**
     * @param array $labels
     */
    public function setLabels($labels)
    {
        $this->labels = $labels;
    }

    /**
     * @param string $label
     */
    public function addLabel($label)
    {
        $this->labels[] = $label;
    }

    /**
     * @return array
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     */
    public function addComment($comment)
    {
        $this->comments[] = $comment;
    }
}
