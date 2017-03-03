<?php

namespace Phpvcn\Sms;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Str;
use Phpvcn\Sms\Drivers\DriverInterface;
use SuperClosure\Serializer;

class Sms
{
    /**
     * The Driver Interface instance.
     *
     * @var \Phpvcn\Sms\Drivers\DriverInterface
     */
    protected $driver;

    /**
     * The IOC Container.
     *
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * The view factory instance.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $views;
    /**
     * The event dispatcher instance.
     *
     * @var \Illuminate\Contracts\Events\Dispatcher|null
     */
    protected $events;

    /**
     * The global to address and name.
     *
     * @var array
     */
    protected $to = [];

    /**
     * Holds the queue instance.
     *
     * @var \Illuminate\Queue\QueueManager
     */
    protected $queue;

    /**
     * Creates the SMS instance.
     *
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver, Factory $views, Dispatcher $events = null)
    {
        $this->driver = $driver;
        $this->views = $views;
        $this->events = $events;
    }

    /**
     * Changes the set SMS driver.
     *
     * @param $driver
     */
    public function driver($driver)
    {
        $this->container['sms.sender'] = $this->container->share(function ($app) use ($driver) {
            return (new DriverManager($app))->driver($driver);
        });

        $this->driver = $this->container['sms.sender'];
        return $this;
    }

    /**
     * 发送到的手机号码
     *
     * @param  mixed  $mobile
     * @return Sms instance
     */
    public function to($mobile)
    {
        $this->to[] = $mobile;
        return $this;
    }

    /**
     * Send a SMS.
     *
     * @param string   $view     The desired view.
     * @param array    $data     The data that needs to be passed into the view.
     * @param \Closure $callback The methods that you wish to fun on the message.
     *
     * @return \Phpvcn\Sms\OutgoingMessage The outgoing message that was sent.
     */
    public function send($view, array $data = [], $callback = null)
    {
        $data['message'] = $message = $this->createOutgoingMessage();

        $message->view($view);
        $message->to($this->to);
        $message->data($data);

        if ($callback instanceof Closure) {
            call_user_func($callback, $message);
        }

        $response = $this->driver->send($message);

        if ($this->events) {
            $this->events->fire(new Events\SmsSending($message, $response));
        }

        return $message;
    }

    /**
     * Creates a new Message instance.
     *
     * @return \Phpvcn\Sms\OutgoingMessage
     */
    protected function createOutgoingMessage()
    {
        return new OutgoingMessage($this->views);
    }

    /**
     * Sets the IoC container.
     *
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Queues a SMS message.
     *
     * @param string          $view     The desired view.
     * @param array           $data     An array of data to fill the view.
     * @param \Closure|string $callback The callback to run on the Message class.
     * @param null|string     $queue    The desired queue to push the message to.
     */
    public function queue($view, array $data = [], $callback = null, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);
        $to = $this->to;

        $this->queue->push('sms@handleQueuedMessage', compact('view', 'to', 'data', 'callback'), $queue);
    }

    /**
     * Queues a SMS message to a given queue.
     *
     * @param null|string     $queue    The desired queue to push the message to.
     * @param string          $view     The desired view.
     * @param array           $data     An array of data to fill the view.
     * @param \Closure|string $callback The callback to run on the Message class.
     */
    public function queueOn($queue, $view, $data, $callback)
    {
        $this->queue($view, $data, $callback, $queue);
    }

    /**
     * Queues a message to be sent a later time.
     *
     * @param int             $delay    The desired delay in seconds
     * @param string          $view     The desired view.
     * @param array           $data     An array of data to fill the view.
     * @param \Closure|string $callback The callback to run on the Message class.
     * @param null|string     $queue    The desired queue to push the message to.
     */
    public function later($delay, $view, array $data = [], $callback = null, $queue = null)
    {
        $callback = $this->buildQueueCallable($callback);
        $to = $this->to;

        return $this->queue->later($delay, 'sms@handleQueuedMessage', compact('view', 'to', 'data', 'callback'), $queue);
    }

    /**
     * Queues a message to be sent a later time on a given queue.
     *
     * @param null|string     $queue    The desired queue to push the message to.
     * @param int             $delay    The desired delay in seconds
     * @param string          $view     The desired view.
     * @param array           $data     An array of data to fill the view.
     * @param \Closure|string $callback The callback to run on the Message class.
     */
    public function laterOn($queue, $delay, $view, array $data, $callback)
    {
        $this->later($delay, $view, $data, $callback, $queue);
    }

    /**
     * Builds the callable for a queue.
     *
     * @param \Closure|string $callback The callback to be serialized
     *
     * @return string
     */
    protected function buildQueueCallable($callback)
    {
        if (!$callback instanceof Closure) {
            return $callback;
        }

        return (new Serializer())->serialize($callback);
    }

    /**
     * Handles a queue message.
     *
     * @param \Illuminate\Queue\Jobs\Job $job
     * @param array                      $data
     */
    public function handleQueuedMessage($job, $data)
    {
        $this->to = $data['to'];
        $this->send($data['view'], $data['data'], $this->getQueuedCallable($data));

        $job->delete();
    }

    /**
     * Gets the callable for a queued message.
     *
     * @param array $data
     *
     * @return mixed
     */
    protected function getQueuedCallable(array $data)
    {
        if (Str::contains($data['callback'], 'SerializableClosure')) {
            return unserialize($data['callback'])->getClosure();
        }

        return $data['callback'];
    }

    /**
     * Set the queue manager instance.
     *
     * @param \Illuminate\Queue\QueueManager $queue
     *
     * @return $this
     */
    public function setQueue(QueueManager $queue)
    {
        $this->queue = $queue;
    }
}
