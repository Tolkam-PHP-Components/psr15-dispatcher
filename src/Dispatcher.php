<?php declare(strict_types=1);

namespace Tolkam\PSR15\Dispatcher;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplQueue;

class Dispatcher implements RequestHandlerInterface
{
    /**
     * @var SplQueue
     */
    private SplQueue $queue;
    
    /**
     * @var RequestHandlerInterface
     */
    private RequestHandlerInterface $requestHandler;
    
    /**
     * @param RequestHandlerInterface $requestHandler
     */
    private function __construct(RequestHandlerInterface $requestHandler)
    {
        $this->requestHandler = $requestHandler;
        $this->queue = new SplQueue;
    }
    
    /**
     * Creates dispatcher instance
     *
     * @param RequestHandlerInterface $requestHandler
     *
     * @return Dispatcher
     */
    public static function create(RequestHandlerInterface $requestHandler): Dispatcher
    {
        return new static($requestHandler);
    }
    
    /**
     * Tells if queue is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->queue->isEmpty();
    }
    
    /**
     * Reports queue length
     *
     * @return int
     */
    public function count(): int
    {
        return $this->queue->count();
    }
    
    /**
     * Puts middleware into queue
     *
     * @param MiddlewareInterface $middleware
     *
     * @return self
     */
    public function middleware(MiddlewareInterface $middleware): self
    {
        $this->queue->enqueue($middleware);
        
        return $this;
    }
    
    /**
     * Puts array of middlewares into queue
     *
     * @param array $middlewares
     *
     * @return self
     */
    public function middlewares(array $middlewares): self
    {
        foreach ($middlewares as $middleware) {
            $this->middleware($middleware);
        }
        
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->queue->isEmpty()) {
            return $this->requestHandler->handle($request);
        }
        
        return $this->queue->dequeue()->process($request, $this);
    }
}
