<?php

namespace App;

class Comment
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var User
     */
    private $user;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * Comment constructor.
     * @param string $content
     * @param User $user
     * @param \DateTime|string $created
     */
    public function __construct($content, $user, $created)
    {
        $this->content = $content;
        $this->user = $user;
        if ($created instanceof \DateTime) {
            $this->created = $created;
        } else {
            $this->created = new \DateTime($created);
        }
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    public function getFormatted()
    {
        return $this->getUser()->getGitHubMention() .
            " commented on " . $this->getCreated()->format('Y-m-d H:i:s') . "\n" .
            $this->getContent();
    }
}
