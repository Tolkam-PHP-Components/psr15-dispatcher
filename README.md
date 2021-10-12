# tolkam/psr15-dispatcher

PSR-15 middleware dispatcher.

## Documentation

The code is rather self-explanatory and API is intended to be as simple as possible. Please, read the sources/Docblock if you have any questions. See [Usage](#usage) for quick start.

## Usage

````php
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tolkam\PSR15\Dispatcher\Dispatcher;

$requestHandler = new class implements RequestHandlerInterface {
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return (new ResponseFactory())->createResponse(418);
    }
};

$middleware = new class implements MiddlewareInterface {
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface
    {
        $response = $handler->handle($request);
        
        return $response->withStatus(
            $response->getStatusCode(),
            $response->getReasonPhrase() . ', and I am too!'
        );
    }
};

$dispatcher = Dispatcher::create($requestHandler);
$dispatcher->middleware($middleware);
$response = $dispatcher->handle(ServerRequestFactory::fromGlobals());

echo $response->getReasonPhrase();
````

## License

Proprietary / Unlicensed 🤷
