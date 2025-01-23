<?php

namespace App\Utils;

use App\Utils\Enums\TermOutType;
use Error;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;

class TerminalOut
{
    private string $URL = "https://github.com/ahmadAria001/";

    public function __construct(string $message, TermOutType $type, string $link = "")
    {
        if (strlen($link) > 0)
            $this->URL = $link;

        switch ($type) {
            case TermOutType::INFO:
                $this->info($message);
                break;

            case TermOutType::ERR:
                $this->error($message);
                break;

            case TermOutType::WARN:
                $this->warning($message);
                break;

            default:
                throw new Error('Type is not valid');
                break;
        }
    }

    private function info(string $message)
    {
        $styleOutput = new OutputFormatterStyle('#2176ff', '#fff', ['bold', 'reverse']);
        $styleOutput->setHref($this->URL);

        $output = new ConsoleOutput();
        $output->getFormatter()->setStyle('info', $styleOutput);

        $output->writeln("<info> INFO </> $message");
    }

    private function error(string $message)
    {
        $styleOutput = new OutputFormatterStyle('#cc023f', '#fff', ['bold', 'reverse']);
        $styleOutput->setHref($this->URL);

        $output = new ConsoleOutput();
        $output->getFormatter()->setStyle('error', $styleOutput);

        $output->writeln("<error> ERROR </> $message");
    }

    private function warning(string $message)
    {
        $styleOutput = new OutputFormatterStyle('#d99b00', '#fff', ['bold', 'reverse']);
        $styleOutput->setHref($this->URL);

        $output = new ConsoleOutput();
        $output->getFormatter()->setStyle('warn', $styleOutput);

        $output->writeln("<warn> WARNING </> $message");
    }
}
