<?php

namespace Phpvcn\Sms;

use Illuminate\View\Factory;

class OutgoingMessage
{
    /**
     * The Illuminate view factory.
     *
     * @var \Illuminate\View\Factory
     */
    protected $views;

    /**
     * The view file to be used when composing a message.
     *
     * @var string
     */
    protected $view;

    /**
     * The data that will be passed into the Illuminate View Factory.
     *
     * @var array
     */
    protected $data;

    /**
     * Array of numbers a message is being sent to.
     *
     * @var array
     */
    protected $to;


    /**
     * Create a OutgoingMessage Instance.
     *
     * @param Factory $views
     */
    public function __construct(Factory $views)
    {
        $this->views = $views;
    }

    /**
     * Composes a message.
     *
     * @return \Illuminate\View\Factory
     */
    public function composeMessage()
    {
        // Attempts to make a view.
         // If a view can not be created; it is assumed that simple message is passed through.
        try {
            return $this->views->make($this->view, $this->data)->render();
        } catch (\InvalidArgumentException $e) {
            return $this->view;
        }
    }

    /**
     * Sets the to addresses.
     *
     * @param string $number  Holds the number that a message will be sent to.
     * @param string $carrier The carrier the number is on.
     *
     * @return $this
     */
    public function to($number)
    {
        if (is_array($number)) {
            foreach ($number as $value) {
                $this->to[] = $value;
            }
        } else {
            $this->to[] = $number;
        }
        
        return $this;
    }

    /**
     * Returns the To addresses.
     *
     * @return array
     */
    public function getTos()
    {
        $numbers = [];
        foreach ($this->to as $number) {
            $numbers[] = $number;
        }

        return $numbers;
    }

    /**
     * Sets the view file to be loaded.
     *
     * @param string $view The desired view file
     */
    public function view($view)
    {
        $this->view = $view;
    }

    /**
     * Sets the data for the view file.
     *
     * @param array $data An array of values to be passed to the View Factory.
     */
    public function data($data)
    {
        $this->data = $data;
    }

    /**
     * Returns the current view file.Returns.
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Returns the view data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
