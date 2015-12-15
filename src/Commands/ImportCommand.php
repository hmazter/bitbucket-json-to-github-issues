<?php

namespace App\Commands;

use App\Auth;
use App\Services\ImportService;
use App\Services\IssueParser;
use App\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class ImportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('import')
            ->setDescription('Import issues from BitBucket json to GitHub')
            ->addArgument('filename', InputArgument::REQUIRED, 'json file to import')
            ->addArgument('owner', InputArgument::REQUIRED, 'owner of repository, organization or a person')
            ->addArgument('repo', InputArgument::REQUIRED, 'repository name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $owner = $input->getArgument('owner');
        $repo = $input->getArgument('repo');
        $filename = $input->getArgument('filename');
        if (!file_exists($filename)) {
            throw new \Exception('File not found');
        }

        /** @var QuestionHelper $helper */
        $helper = $this->getHelper('question');
        $username = $helper->ask($input, $output, new Question('GitHub username:'));
        $auth = new Auth($username);

        if ($helper->ask($input, $output, new Question('Do you use 2 factor auth for GitHub? (y/N)'))) {
            $question = new Question('GitHub personal token (https://github.com/settings/tokens) with repo access:');
            $token = $helper->ask($input, $output, $question);
            $auth->setToken($token);
        } else {
            $password = $helper->ask($input, $output, new Question('GitHub password:'));
            $auth->setPassword($password);
        }

        $issueParser = new IssueParser();
        $importService = new ImportService($owner, $repo, $auth);
        $issues = $issueParser->parseJsonFileToIssues($filename);

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
        $question = new ConfirmationQuestion("Continue to import these to $owner/$repo? (y/N)", false);
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
}
