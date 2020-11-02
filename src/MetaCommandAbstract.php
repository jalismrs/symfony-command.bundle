<?php
declare(strict_types = 1);

namespace Jalismrs\Symfony\Common;

use Exception;
use Jalismrs\Common\Exception\AppException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;
use function array_combine;
use function array_map;
use function array_merge;

/**
 * Class MetaCommandAbstract
 *
 * @package Jalismrs\Symfony\Common
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
        if (!$application instanceof Application) {
            throw new UnexpectedValueException(
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
        } catch (AppException $appException) {
            $this->logger->error($appException);
            $this->style
                ->getErrorStyle()
                ->error($appException);
            
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
