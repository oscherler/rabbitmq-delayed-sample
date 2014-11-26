<?php

namespace RabbitMQ\SampleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Publish a message to RabbitMQ.
 */
class TestCommand extends ContainerAwareCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
			->setName('sample:test')
			->setDescription('Publishes a delayed message')
			->addOption(
				'delay',
				'd',
				InputOption::VALUE_REQUIRED,
				'Delay, in miliseconds.'
			)
			->addOption(
				'routing-key',
				'r',
				InputOption::VALUE_OPTIONAL,
				'Routing key.',
				''
			)
		;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$producer = $this->getContainer()->get('delayed_producer');
		
		$delay = (int) $input->getOption('delay');
		
		$body = json_encode( array(
			'time' => microtime( true ),
			'delay' => $delay
		) );

		$producer->delayedPublish( $delay, $body, $input->getOption('routing-key') );
	}
}
