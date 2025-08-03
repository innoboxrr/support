<?php

namespace Innoboxrr\Support\Jobs;

use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Queue\ShouldQueue;

class DispatchJob
{
    private string $connection;
    private string $queue;
    private bool $isLocal;
    private bool $forceAsync;
    private ?CarbonInterval $delay = null;
    private ?int $priority = null;

    public function __construct(string $connection = 'redis', string $queue = 'default')
    {
        $this->connection = $connection;
        $this->queue = $queue;
        $this->isLocal = App::environment('local');
        $this->forceAsync = config('innoboxrr-support.jobs.force_async', false);
    }

    /**
     * Despacha un job con las configuraciones actuales.
     *
     * @param string $jobClass Clase del job a despachar.
     * @param mixed ...$params Parámetros del constructor del job.
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function dispatch(string $jobClass, ...$params): mixed
    {
        if (!class_exists($jobClass)) {
            throw new InvalidArgumentException("La clase del job {$jobClass} no existe.");
        }

        $job = new $jobClass(...$params);

        // Si es local, ejecuta el job de forma síncrona
        if ($this->isLocal && !$this->forceAsync) {
            // Compatibilidad con versiones de Laravel
            return function_exists('dispatch_sync')
                ? dispatch_sync($job)
                : dispatch_now($job);
        }

        // Configurar conexión y cola
        if (method_exists($job, 'onConnection')) {
            $job->onConnection($this->connection);
        }
        if (method_exists($job, 'onQueue')) {
            $job->onQueue($this->queue);
        }

        // Configurar retraso
        if ($this->delay && method_exists($job, 'delay')) {
            $job->delay($this->delay);
        }

        // Configurar prioridad
        if (!is_null($this->priority) && method_exists($job, 'setPriority')) {
            $job->setPriority($this->priority);
        }

        return dispatch($job);
    }

    public function setConnection(string $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function setDelay(CarbonInterval|int $delay): self
    {
        $this->delay = $delay instanceof CarbonInterval ? $delay : CarbonInterval::seconds($delay);
        return $this;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function setConfig(string $connection, string $queue): self
    {
        $this->connection = $connection;
        $this->queue = $queue;
        return $this;
    }

    public static function config(string $connection = 'redis', string $queue = 'default'): self
    {
        return new self($connection, $queue);
    }

    /**
     * Despacha un job de forma rápida.
     *
     * @param string $class
     * @param string $connection
     * @param string $queue
     * @param mixed ...$params
     * @return mixed
     * @throws InvalidArgumentException
     */
    public static function run($class, $connection = 'redis', $queue = 'default', ...$params): mixed
    {
        $instance = new self();
        $instance->setConfig($connection, $queue);
        return $instance->dispatch($class, ...$params);
    }
}