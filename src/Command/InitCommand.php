<?php
/**
 * Copyright © 2018-2018 NewRehtse.
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */

namespace App\Command;

use App\Controller\GameController;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Esther Ibáñez González <newrehtse@gmail.com>
 */
class InitCommand extends Command
{
    /** @var GameController $gameController */
    private $gameController;

    /**
     * InitCommand constructor.
     *
     * @param GameController $gameController
     * @param string|null    $name
     */
    public function __construct(
        GameController $gameController,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->gameController = $gameController;
    }

    /**
     * @inheritdoc
     */
    protected function configure(): void
    {
        $this->setName('iacontest')
            ->setDescription('Init the game.')
            ->addArgument('robot', InputArgument::OPTIONAL, 'The name of the robot.')
            ->addOption('strategy', 's', InputArgument::OPTIONAL, 'Strategy to use')
            ->setHelp('This command allows you to init the game...')
        ;
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $robot = '';
        $strategy = 'AStar';

        if ($input->hasOption('strategy')) {
            $strategy = $input->getOption('strategy');

            if (null === $strategy) {
                $strategy = 'AStar';
            }
        }

        if ($input->hasArgument('robot')) {
            $robot = $input->getArgument('robot');

            if (null === $robot) {
                $robot = '';
            }
        }

        $this->gameController->initialize($robot, $strategy);
        $this->gameController->run();
    }
}
