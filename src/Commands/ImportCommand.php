<?php

namespace App\Commands;

use App\Auth;
use App\Services\ImportService;
use App\Services\IssueParser;
use App\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class ImportCommand extends Command
{
    /**
     * @var string
     */
    private $filename;

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

    protected function configure()
    {
        $this
            ->setName('import')
            ->setDescription('Import issues from BitBucket json to GitHub')
            ->addOption('filename', 'f', InputOption::VALUE_REQUIRED, 'Json file to import')
            ->addOption('owner', 'o', InputOption::VALUE_REQUIRED, 'Owner of GitHub repository, organization or a person')
            ->addOption('repo', 'r', InputOption::VALUE_REQUIRED, 'GitHub repository name')
            ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'GitHub username to log in with');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $this->parseInput($input, $output);

        $issueParser = new IssueParser();
        $importService = new ImportService($this->owner, $this->repo, $this->auth);
        $issues = $issueParser->parseJsonFileToIssues($this->filename);

        $output->writeln('<info>Found ' . count($issueParser->getAssignees()) . ' assignees in BitBucket</info>');
        $output->writeln('<info>Match these BitBucket usernames to GitHub, or leave empty to skip assign</info>');

        /** @var User $assignee */
        foreach ($issueParser->getAssignees() as $assignee) {
            $username = $helper->ask(
                $input,
                $output,
                new Question('BitBucket user ' . $assignee->getBitbucket() . ': ')
            );
            if ($username != null) {
                $assignee->setGithub($username);
            }
        }

        $output->writeln('<info>Parsed ' . count($issues) . ' from the json file</info>');
        $question = new ConfirmationQuestion("Continue to import these to {$this->owner}/{$this->repo}? (y/N)", false);
        if (!$helper->ask($input, $output, $question)) {
            $output->writeln('Aborting');
            return;
        }

        $progress = new ProgressBar($output, count($issues));
        $progress->start();
        foreach ($issues as $issue) {
            $importService->createAndUpdateIssue($issue);
            $progress->advance();
        }
        $progress->finish();

        $output->writeln('Import done!');
    }

    private function parseInput(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');

        /*
         * Filename
         */
        $this->filename = $input->getOption('filename');
        if (empty($this->filename)) {
            $this->filename = $helper->ask($input, $output, new Question('Json file to import: '));
        }
        if (!file_exists($this->filename)) {
            throw new \Exception('File not found');
        }

        /*
         * Owner
         */
        $this->owner = $input->getOption('owner');
        if (empty($this->owner)) {
            $this->owner = $helper->ask($input, $output,
                new Question('Owner of GitHub repository, organization or a person: '));
        }
        if (empty($this->owner)) {
            throw new \Exception('Empty owner');
        }

        /*
         * Repo
         */
        $this->repo = $input->getOption('repo');
        if (empty($this->repo)) {
            $this->repo = $helper->ask($input, $output, new Question('GitHub repository name: '));
        }
        if (empty($this->repo)) {
            throw new \Exception('Empty repo');
        }

        /*
         * username
         */
        $username = $input->getOption('username');
        if (empty($username)) {
            $username = $helper->ask($input, $output, new Question('GitHub username to log in with: '));
        }
        if (empty($username)) {
            throw new \Exception('Empty username');
        }


        $this->auth = new Auth($username);

        if ($helper->ask($input, $output, new Question('Do you use 2 factor auth for GitHub? (y/N)'))) {
            $question = new Question('GitHub personal token (https://github.com/settings/tokens) with repo access:');
            $token = $helper->ask($input, $output, $question);
            $this->auth->setToken($token);
        } else {
            $password = $helper->ask($input, $output, new Question('GitHub password:'));
            $this->auth->setPassword($password);
        }
    }
}
