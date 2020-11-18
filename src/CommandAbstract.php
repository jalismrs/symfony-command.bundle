<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Common;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class CommandAbstract
 *
 * @package Jalismrs\Symfony\Common
 *
 * @codeCoverageIgnore
 */
abstract class CommandAbstract extends
    Command
{
    /**
     * logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;
    /**
     * style
     *
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    protected SymfonyStyle $style;
    /**
     * parameterBag
     *
     * @var \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface
     */
    private ParameterBagInterface $parameterBag;
    
    /**
     * CommandAbstract constructor.
     *
     * @param \Psr\Log\LoggerInterface                                                  $logger
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag
     * @param string|null                                                               $name
     *
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(
        LoggerInterface $logger,
        ParameterBagInterface $parameterBag,
        string $name = null
    ) {
        $this->logger       = $logger;
        $this->parameterBag = $parameterBag;
        
        parent::__construct(
            $name
        );
    }
    
    /**
     * initialize
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    protected function initialize(
        InputInterface $input,
        OutputInterface $output
    ) : void {
        parent::initialize(
            $input,
            $output
        );
        
        $this->style = new SymfonyStyle(
            $input,
            $output
        );
    }
    
    /**
     * execute
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) : int {
        $appName    = $this->parameterBag->get('app.name');
        $appVersion = $this->parameterBag->get('app.version');
        $name       = $this->getName();
        
        $this->style->title("{$appName} v{$appVersion} - {$name}");
        $this->style->text($this->getDescription());
        $this->style->newLine();
        
        $this->logger->info(
            $name,
            [
                'input' => [
                    'arguments' => $input->getArguments(),
                    'options'   => $input->getOptions(),
                ],
            ],
        );
        
        return Command::SUCCESS;
    }
}
