<?php

namespace Innoboxrr\Support\Jobs;

use Illuminate\Support\Facades\App;
use InvalidArgumentException;
use Carbon\CarbonInterval;

class DispatchJob
{
    private string $connection;
    private string $queue;
    private bool $isLocal;
    private ?CarbonInterval $delay = null;
    private ?int $priority = null;

    public function __construct(string $connection = 'redis', string $queue = 'default')
    {
        $this->connection = $connection;
        $this->queue = $queue;
        $this->isLocal = App::environment('local');
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
    
        // Instanciar el job con los parámetros
        $job = new $jobClass(...$params);
    
        // Si es local, ejecuta el job de forma síncrona
        if ($this->isLocal) {
            return dispatch_sync($job); // Usar `dispatch_sync` con la instancia correcta
        }
    
        // Configurar conexión y cola
        $job->onConnection($this->connection)
            ->onQueue($this->queue);
    
        // Configurar retraso
        if ($this->delay) {
            $job->delay($this->delay);
        }
    
        // Configurar prioridad (si el sistema de colas lo soporta)
        if (!is_null($this->priority)) {
            $job->setPriority($this->priority);
        }
    
        // Despachar el job
        return dispatch($job);
    }    

    /**
     * Configura la conexión del job.
     *
     * @param string $connection
     * @return $this
     */
    public function setConnection(string $connection): self
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Configura la cola del job.
     *
     * @param string $queue
     * @return $this
     */
    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    /**
     * Configura el retraso del job.
     *
     * @param CarbonInterval|int $delay Tiempo en segundos o instancia de CarbonInterval.
     * @return $this
     */
    public function setDelay(CarbonInterval|int $delay): self
    {
        $this->delay = $delay instanceof CarbonInterval ? $delay : CarbonInterval::seconds($delay);
        return $this;
    }

    /**
     * Configura la prioridad del job.
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Método estático para crear una instancia configurada.
     *
     * @param string $connection
     * @param string $queue
     * @return self
     */
    public static function config(string $connection = 'redis', string $queue = 'default'): self
    {
        return new self($connection, $queue);
    }
}
