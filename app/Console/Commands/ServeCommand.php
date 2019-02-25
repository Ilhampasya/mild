<?php

namespace App\Console\Commands;

class ServeCommand extends Command
{
    /**
     * @var string
     */
    protected $path = 'public';
    /**
     * @var string
     */
    protected $name = 'serve';
    /**
     * @var string
     */
    protected $description = 'Serve the application on the PHP development server';
    /**
     * @var array
     */
    protected $options = [
        'host' => [
            self::MODE => self::OPT_VALUE_OPTIONAL,
            self::DESCRIPTION => 'The host address to serve the application on',
            self::DEFAULT => '127.0.0.1'
        ],
        'port' => [
            self::MODE => self::OPT_VALUE_OPTIONAL,
            self::DESCRIPTION => 'The port to serve the application on',
            self::DEFAULT => 8000
        ]
    ];

    /**
     * @return mixed
     */
    public function handle()
    {
        chdir($this->mild->getPath($this->path));
        $host = $this->getOption('host');
        $port = $this->getOption('port');
        $this->output->writeln('<info>Mild development server listening on:</info> <http://'.$host.':'.$port.'>');
        passthru(sprintf('%s -S %s:%s', 'php', $host, $port), $status);
        return $status;
    }
}