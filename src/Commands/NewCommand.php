<?php namespace Scalex\Mailer\Commands;

use Scalex\Mailer\MailingList;
use Scalex\Mailer\Project;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class NewCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure() {
        $this
            ->setName('new')
            ->setDescription('Create new project')
            ->addArgument('name', null, 'The name of the virtual machine.', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        if (!file_exists(__DIR__.'/../../storage/database.sqlite')) {
            $output->writeln("<error>Mailer is not installed.</error> User <info>mailer init</info> to install.");

            return;
        }

        $name = $input->getArgument('name');

        if (empty($name)) {
            $name = getcwd();
        }

        if (count(glob("${name}/*")) !== 0) {
            $helper = $this->getHelper('question');
            $question = new ConfirmationQuestion('This directory is not empty. Do you want to continue? [y/N] ', false);
            if (!$helper->ask($input, $output, $question)) {
                return;
            }
        }

        $project = new Project(['name' => $name, 'directory' => realpath($name)]);
        $mailingList = new MailingList(['name' => $name]);

        if (!$project->save() || !$mailingList->save()) {
            $output->writeln("<error>Cannot create project :(</error>");
            die(-1);
        }

        if (!file_exists($name)) {
            mkdir($name);
        }

        file_put_contents(
            $name.'/.mailer.json',
            json_encode(
                [
                    'id' => $project->getKey(),
                    'lists' => [
                        $mailingList->getKey(),
                    ],
                ]
            )
        );

        if (!file_exists($name.'/html.blade.php')) copy(__DIR__.'/../../stubs/html.blade.php', $name.'/html.blade.php');
        if (!file_exists($name.'/text.blade.php')) copy(__DIR__.'/../../stubs/text.blade.php', $name.'/text.blade.php');
        if (!file_exists($name.'/data.csv')) copy(__DIR__.'/../../stubs/data.csv', $name.'/data.csv');
        if (!file_exists($name.'/config.php')) copy(__DIR__.'/../../stubs/config.php', $name.'/config.php');

        $output->writeln("Write your mail content in <info>html.blade.php</info>.");
        $output->writeln(
            "You should also provide a fallback mail if the client does not support HTML, use  <info>text.blade.php</info> for that."
        );
        $output->writeln("Export you contact list as <info>.csv</info> file.");
        $output->writeln("<info>data.csv</info> should have name and email columns.");
        $output->writeln("After sending mails report would be dumped in <info>report.csv</info>.");
        $output->writeln("");
        $output->writeln("Project created in <info>${name}</info>");
    }
}
