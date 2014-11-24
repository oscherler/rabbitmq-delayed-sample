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
		
		$channel->queue_declare('task_queue');
		
		$channel->queue_declare( 'delay', false, true, false, false, false, array(
			'x-message-ttl' => array( 'I', 10000 ),
			'x-dead-letter-exchange' => array( 'S', '' ),
			'x-dead-letter-routing-key' => array( 'S', 'task_queue' )
		) );

		$data = 'Hello';
		$msg = new AMQPMessage(
			$data,
			array(
				'delivery_mode' => 2
			)
		);

		$channel->basic_publish( $msg, '', 'delay' );

		$output->writeln( " [x] Sent " . $data );
	}
}
