<?php

namespace RabbitMQ\SampleBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Publish a message to RabbitMQ.
 */
class PublishCommand extends ContainerAwareCommand
{
	/**
	 * @see Command
	 */
	protected function configure()
	{
		$this
			->setName('rabbit:publish')
			->setDescription('Publishes a message to RabbitMQ')
		;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute( InputInterface $input, OutputInterface $output )
	{
		$connection = new AMQPConnection( 'localhost', 5672, 'guest', 'guest' );
		$channel = $connection->channel();

		$data = 'Hello';
		$msg = new AMQPMessage(
			$data,
			array(
				'delivery_mode' => 2
			)
		);

		$channel->basic_publish( $msg, '', 'task_queue' );

		$output->writeln( " [x] Sent " . $data );
	}
}
