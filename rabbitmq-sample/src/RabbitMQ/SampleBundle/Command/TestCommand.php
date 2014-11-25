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
		;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$producer = $this->getContainer()->get('delayed_producer');
		
		$producer->publish('Hello');
	}
}
