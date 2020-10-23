<?php
declare(strict_types = 1);

namespace Jalismrs\CommandBundle;

use Exception;
use Jalismrs\ErrorBundle\AssertionError;
use Jalismrs\ExceptionBundle\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function array_combine;
use function array_map;
use function array_merge;

/**
 * Class MetaCommandAbstract
 *
 * @package Jalismrs\CommandBundle
 *
 * @codeCoverageIgnore
 */
abstract class MetaCommandAbstract extends
    CommandAbstract
{
    /**
     * runCommand
     *
     * @param string                                            $name
     * @param array                                             $parameters
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     *
     * @throws \Symfony\Component\Console\Exception\CommandNotFoundException
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    protected function runCommand(
        string $name,
        array $parameters,
        InputInterface $input,
        OutputInterface $output
    ) : int {
        $application = $this->getApplication();
        if ($application === null) {
            throw new AssertionError(
                'Application instance is null [should never happen]'
            );
        }
        
        $command = $application->get($name);
        
        $arrayInput = self::createInput(
            $parameters,
            $input
        );
        
        try {
            $code = $command->run(
                $arrayInput,
                $output
            );
        } catch (ExceptionInterface $exception) {
            $this->logger->error($exception);
            $this->style
                ->getErrorStyle()
                ->error($exception);
            
            $code = 2;
        } catch (Exception $exception) {
            $this->logger->critical($exception);
            $this->style
                ->getErrorStyle()
                ->error($exception);
            
            $code = 1;
        }
        
        return $code;
    }
    
    /**
     * createInput
     *
     * @static
     *
     * @param array                                           $parameters
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return \Symfony\Component\Console\Input\ArrayInput
     *
     * @throws \Symfony\Component\Console\Exception\InvalidArgumentException
     */
    private static function createInput(
        array $parameters,
        InputInterface $input
    ) : ArrayInput {
        $optionNames  = [
            'no-debug',
            'quiet',
            'verbose',
        ];
        $optionKeys   = array_map(
            static function(
                string $name
            ) : string {
                return "--{$name}";
            },
            $optionNames
        );
        $optionValues = array_map(
            static function(
                string $name
            ) use
            (
                $input
            ) {
                return $input->getOption($name);
            },
            $optionNames
        );
        $options      = array_combine(
            $optionKeys,
            $optionValues
        );
        
        $parametersWithOptions = array_merge(
            $parameters,
            $options
        );
        
        return new ArrayInput(
            $parametersWithOptions
        );
    }
}
