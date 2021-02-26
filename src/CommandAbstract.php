<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Common;

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use UnexpectedValueException;
use function vsprintf;

/**
 * Class CommandAbstract
 *
 * @package Jalismrs\Symfony\Common
 */
abstract class CommandAbstract extends
    Command
{
    public const DESCRIPTION = 'DESCRIPTION';
    public const HELP        = 'DESCRIPTION';
    
    /**
     * logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected LoggerInterface $logger;
    /**
     * style
     *
     * @var \Symfony\Component\Console\Style\SymfonyStyle|null
     */
    private ?SymfonyStyle $style;
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
     * configure
     *
     * @return void
     */
    protected function configure() : void
    {
        parent::configure();
        
        $this
            ->setDescription(static::DESCRIPTION)
            ->setHelp(static::HELP);
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
        
        $style = $this->getStyle();
        
        $style->title("{$appName} v{$appVersion} - {$name}");
        $style->text($this->getDescription());
        $style->newLine();
        
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
    
    /**
     * getStyle
     *
     * @return \Symfony\Component\Console\Style\SymfonyStyle
     */
    public function getStyle() : SymfonyStyle
    {
        if ($this->style === null) {
            $message = vsprintf(
                '%1s::$style has not been set. Use %1s::setStyle(%2s)',
                [
                    static::class,
                    SymfonyStyle::class,
                ],
            );
            
            throw new UnexpectedValueException(
                $message
            );
        }
        
        return $this->style;
    }
    
    /**
     * setStyle
     *
     * @param \Symfony\Component\Console\Style\SymfonyStyle $style
     *
     * @return void
     */
    public function setStyle(
        SymfonyStyle $style
    ) : void {
        $this->style = $style;
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
        
        if ($this->style === null) {
            $style = new SymfonyStyle(
                $input,
                $output
            );
            
            $this->setStyle($style);
        }
    }
}
