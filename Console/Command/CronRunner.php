<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author Alex P <alexander@codelegacy.com> <@>
 * @copyright Copyright (c) 2019 Codelegacy (http://codelegacy.com)
 */

namespace Codelegacy\Utils\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CronRunner extends Command {

    /**
     * @var State
     */
    private $state;

    /**
     * CronRunner constructor.
     * @param State $state
     */
    public function __construct(
        State $state
    ){
        $this->state = $state;
        parent::__construct();
    }

    protected function configure() {
        $this->setName('codelegacy:utils:cronrunner');
        $this->setDescription('Cron runner');
        $options = [
            new InputOption('cronClass', null, InputOption::VALUE_REQUIRED, 'please enter the class \My\CronClass\toRun')
        ];
        $this->setDefinition($options);
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ){
        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);
        } catch (\Exception $e) {
            $output->writeln('<comment>An exception was thrown during area code setting:</comment>');
            $output->writeln($e->getMessage());
        }

        if (!is_null($input->getOption('cronClass'))) {
            $cronClass = $input->getOption('cronClass');
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $object = $objectManager->get($cronClass);
            $object->execute();
        }
    }
}