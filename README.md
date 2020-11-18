# symfony.common.command

Adds Symfony command abstract classes

## Test

`phpunit` or `vendor/bin/phpunit`

coverage reports will be available in `var/coverage`

## Use

### CommandAbstract
```php
use Jalismrs\Symfony\Common\CommandAbstract;

class SomeCommand extends CommandAbstract {

}
```

### MetaCommandAbstract
```php
use Jalismrs\Symfony\Common\MetaCommandAbstract;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SomeCommand extends MetaCommandAbstract {
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        return $this->runCommand(
            'app:command',
            [
                'parameter' => 'value',
            ],
            $input,
            $output,
        );
    }
}
```
