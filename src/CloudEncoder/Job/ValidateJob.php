<?php
/**
 * cloudxxx-api (http://www.cloud.xxx)
 *
 * Copyright (C) 2014 Really Useful Limited.
 * Proprietary code. Usage restrictions apply.
 *
 * @copyright  Copyright (C) 2014 Really Useful Limited
 * @license    Proprietary
 */

namespace CloudEncoder\Job;

use FFMpeg\FFProbe;
use Cloud\Job\AbstractJob;
use CloudEncoder\PHPFFmpeg\VideoValidator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ValidateJob
 *
 */
class ValidateJob extends AbstractJob
{
    /**
     * Configures this job
     */
    protected function configure()
    {
        $this
            ->setDefinition([
                new InputArgument('input', InputArgument::REQUIRED, 'The url of the video to validate'),
            ])
            ->setName('job:encoder:validate')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $video = $input->getArgument('input');
        $output->writeln(sprintf('<info>Validating %s</info>', $video));
        $validator = new VideoValidator();
        $videoMetadata = $validator->process($video);
        foreach ($videoMetadata as $key => $value) {
            $output->writeLn(sprintf('<info>%s: %s</info>', $key, $value));
        }
    }
}
