<?php

namespace App;

class User
{

    /**
     * @var string
     */
    private $bitbucket;

    /**
     * @var string
     */
    private $github;

    /**
     * User constructor.
     * @param string $bitbucket
     */
    public function __construct($bitbucket)
    {
        $this->bitbucket = $bitbucket;
    }

    /**
     * @return string
     */
    public function getBitbucket()
    {
        return $this->bitbucket;
    }

    /**
     * @param string $github
     */
    public function setGithub($github)
    {
        $this->github = $github;
    }

    /**
     * @return string
     */
    public function getGithub()
    {
        return $this->github;
    }

    /**
     * Get a GitHub mention (@username), defaults to BitBucket username if no GitHub user is set
     *
     * @return string
     */
    public function getGitHubMention()
    {
        if (!empty($this->getGithub())) {
            return '@' . $this->getGithub();
        }

        return $this->getBitbucket();
    }
}
