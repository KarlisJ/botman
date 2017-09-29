<?php

namespace BotMan\BotMan\Commands;

use BotMan\BotMan\Closure;
use Illuminate\Support\Collection;
use BotMan\BotMan\Interfaces\DriverInterface;
use BotMan\BotMan\Interfaces\MiddlewareInterface;

class Command
{
    /** @var string */
    protected $pattern;

    /** @var Closure|string */
    protected $callback;

    /** @var string */
    protected $in;

    /** @var string */
    protected $driver;
composer require --dev phpunit/phpunit ^6.2
    /** @var string */
    protected $recipient;

    /** @var string */
    protected $sender;

    /** @var array */
    protected $middleware = [];

    /** @var bool */
    protected $stopsConversation = false;

    /** @var bool */
    protected $skipsConversation = false;

    /**
     * Command constructor.
     *
     * @param string $pattern
     * @param Closure|string $callback
     * @param string|null $recipient
     * @param string|null $driver
     */
    public function __construct($pattern, $callback, $recipient = null, $driver = null, $sender = null)
    {
        $this->pattern = $pattern;
        $this->callback = $callback;
        $this->driver = $driver;
        $this->recipient = $recipient;
        $this->sender = $sender;
    }

    /**
     * Apply possible group attributes.
     *
     * @param  array $attributes
     */
    public function applyGroupAttributes(array $attributes)
    {
        if (isset($attributes['middleware'])) {
            $this->middleware($attributes['middleware']);
        }

        if (isset($attributes['driver'])) {
            $this->driver($attributes['driver']);
        }

        if (isset($attributes['recipient'])) {
            $this->recipient($attributes['recipient']);
        }

        if (isset($attributes['sender'])) {
            $this->sender($attributes['sender']);
        }
    }

    /**
     * @param $driver
     * @return $this
     */
    public function driver($driver)
    {
        $this->driver = Collection::make($driver)->transform(function ($driver) {
            if (class_exists($driver) && is_subclass_of($driver, DriverInterface::class)) {
                $driver = basename(str_replace('\\', '/', $driver));
                $driver = preg_replace('/(.*)(Driver)$/', '$1', $driver);
            }

            return $driver;
        });

        return $this;
    }

    /**
     * With this command a current conversation should be stopped.
     */
    public function stopsConversation()
    {
        $this->stopsConversation = true;
    }

    /**
     * Tells if a current conversation should be stopped through this command.
     *
     * @return bool
     */
    public function shouldStopConversation()
    {
        return $this->stopsConversation;
    }

    /**
     * With this command a current conversation should be stopped.
     */
    public function skipsConversation()
    {
        $this->skipsConversation = true;
    }

    /**
     * Tells if a current conversation should be skipped through this command.
     *
     * @return bool
     */
    public function shouldSkipConversation()
    {
        return $this->skipsConversation;
    }

    /**
     * @param $recipient
     * @return $this
     */
    public function recipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * @param $sender
     * @return $this
     */
    public function sender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @param array|MiddlewareInterface $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        if (! is_array($middleware)) {
            $middleware = [$middleware];
        }

        $this->middleware = Collection::make($middleware)->filter(function ($item) {
            return $item instanceof MiddlewareInterface;
        })->merge($this->middleware)->toArray();

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'pattern' => $this->pattern,
            'callback' => $this->callback,
            'driver' => $this->driver,
            'middleware' => $this->middleware,
            'recipient' => $this->recipient,
        ];
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @return Closure|string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @return string
     */
    public function getSender()
    {
        return $this->sender;
    }
}
