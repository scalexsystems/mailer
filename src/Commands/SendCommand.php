<?php namespace Scalex\Mailer\Commands;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Mail\Transport\MailgunTransport;
use PHPExcel_Reader_CSV;
use Scalex\Mailer\MailingList;
use Scalex\Mailer\Member;
use Scalex\Mailer\Project;
use Scalex\Mailer\Track;
use Scalex\Mailer\View\Blade;
use Swift_Mailer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SendCommand extends Command
{
    protected $config = [];
    protected $test = false;
    protected $step = 1;
    protected $resend = false;
    protected $projectRoot;
    protected $projectInfoPath;
    protected $configPath;
    protected $dataPath;
    /**
     * @var Blade
     */
    protected $blade;
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var Project
     */
    protected $project;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var MailingList
     */
    protected $list;

    protected function checkProject() {
        if (!file_exists($this->projectInfoPath)) {
            $this->output->writeln(
                "<error>This is not a mailer project.</error> User <info>mailer new</info> to create a project in this directory."
            );

            die(-1);
        }

        $info = json_decode(file_get_contents($this->projectInfoPath), true);
        $id = $info['id'];
        $lists = $info['lists'];

        $this->project = Project::find($id);
        $this->list = MailingList::find($lists[0]);
    }

    protected function checkInstallation() {
        if (!file_exists(__DIR__.'/../../storage/database.sqlite')) {
            $this->output->writeln("<error>Mailer is not installed.</error> User <info>mailer init</info> to install.");

            die(-1);
        }
    }

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure() {
        $this
            ->setName('send')
            ->setDescription('Create new project')
            ->addOption('no-test', null, InputOption::VALUE_OPTIONAL, 'Send test mail first.', false)
            ->addOption('step', null, InputOption::VALUE_OPTIONAL, 'Number of mails to send in one go.', -1)
            ->addOption('resend', null, InputOption::VALUE_OPTIONAL, 'Resend mail.', false);
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $this->input = $input;
        $this->output = $output;
        $this->parseInputs();
        $this->getPaths();

        $this->checkInstallation();
        $this->checkProject();
        $this->checkConfig();
        $this->init();
        $this->loadDataAndSendMails();
    }

    protected function parseInputs() {
        $this->test = !$this->input->getOption('no-test');
        $this->step = $this->input->getOption('step');
        $this->resend = $this->input->getOption('resend');
    }

    protected function getPaths() {
        $this->projectRoot = getcwd();
        $this->projectInfoPath = $this->projectRoot."/.mailer.json";
        $this->configPath = $this->projectRoot."/config.php";
        $this->dataPath = $this->projectRoot."/data.csv";
    }

    protected function checkConfig() {
        if (!file_exists($this->configPath)) {
            $this->output->writeln(
                "<error>Config file is not present.</error> Put data in mail configuration in <info>config.php</info>"
            );

            die(-1);
        }
        $this->config = require_once($this->configPath);
    }

    protected function init() {
        $this->blade = new Blade($this->projectRoot, __DIR__.'/../../storage/views');

        $transport = null;
        $connection_name = array_get($this->config, 'default');
        if ($connection_name) {
            $connection = array_get($this->config, 'drivers.'.$connection_name);
            switch (array_get($connection, 'driver', $connection_name)) {
                case 'smtp':
                    $transport = \Swift_SmtpTransport::newInstance(
                        array_get($connection, 'host'),
                        array_get($connection, 'port'),
                        array_get($connection, 'encryption')
                    )
                        ->setAuthMode('login')
                        ->setUsername(array_get($connection, 'user'))
                        ->setPassword(array_get($connection, 'pass'));
                    break;
                case 'mailgun':
                    $transport = new MailgunTransport(
                        new Client(),
                        array_get($connection, 'key'),
                        array_get($connection, 'domain')
                    );
                    break;
                default:
                    $transport = new MailgunTransport(
                        new Client(),
                        array_get($this->config, 'key'),
                        array_get($this->config, 'domain')
                    );
            }
        }


        $this->mailer = new Mailer(
            $this->blade->view(),
            new Swift_Mailer($transport)
        );
    }

    /**
     * @param array $item
     * @param \Scalex\Mailer\Member $member
     */
    protected function send(array $item, Member $member) {
        list($email, $name) = $member->address;

        if (filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {
            $this->output->writeln('Invalid email address: '.$email);
            return;
        }
        $this->mailer->send(
            [],
            [],
            function (Message $message) use ($item, $member) {
                list($email, $name) = $member->address;

                $message->to(trim($email), $name);
                // Add from
                list($email, $name) = $this->config['from'];
                $message->from($email, $name);
                // Add reply to
                if (array_has($this->config, 'reply')) {
                    list($email, $name) = $this->config['reply'];
                    $message->replyTo($email, $name);
                }
                $message->subject($this->config['subject']);
                $message->setBody($this->blade->view()->make('html', $item + compact('message'))->render(), 'text/html');
//                $message->addPart($this->blade->view()->make('text', $item + compact('message'))->render(), 'text/plain');
            }
        );
    }

    protected function loadDataAndSendMails() {
        $reader = new PHPExcel_Reader_CSV();

        if (!file_exists($this->dataPath)) {
            $this->output->writeln("<error>Data source is not present.</error> Put data in <info>data.csv</info>");

            die(-1);
        }

        $excel = $reader->load($this->dataPath);

        foreach ($excel->getWorksheetIterator() as $worksheet) {
            $rawData = $worksheet->toArray();
            $headers = array_shift($rawData);
            foreach ($rawData as $row) {
                $item = [];
                for ($i = 0; $i < count($row); ++$i) {
                    $item[$headers[$i]] = $row[$i];
                }

                if (isset($item['name'])) {
                    $item['first_name'] = $item['name'];
                    $item['last_name'] = ' ';
                }
                $fields = array_only($item, ['first_name', 'last_name', 'email']);
                $member = Member::look(array_get($item, 'email'), $this->list->getKey());
                if (!$member) {
                    $member = new Member($fields);
                    $this->list->members()->save($member);
                }

                $tracks = $member->tracks;

                if ($this->shouldSend($item, $member, $tracks)) {
                    $this->send($item, $member);
                }
            }
        }
    }

    protected function shouldSend(array $item, Member $member, Collection $tracks) {
        if ($tracks->isEmpty()) {
            $this->output->writeln('New member: '.$member);
            $member->tracks()->save(
                new Track(
                    [
                        'label' => 'action',
                        'data' => 'send',
                        'project_id' => $this->project->getKey(),
                    ]
                )
            );

            return true;
        } else {
            $this->output->writeln('Member: '.$member);
            $this->output->writeln('History:');
            foreach ($tracks as $track) {
                $this->output->writeln('    '.$track);
            }
            if ($this->resend) {
                $member->tracks()->save(
                    new Track(
                        [
                            'label' => 'action',
                            'data' => 'resend',
                            'project_id' => $this->project->getKey(),
                        ]
                    )
                );
                $this->output->writeln('<info>sending again</info>');

                return true;
            } else {
                $this->output->writeln('<error>not sending again</error>');
            }
        }

        return false;
    }
}
