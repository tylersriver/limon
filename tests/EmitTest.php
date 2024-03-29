<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

use function Limon\emit;

it('emits response', function() {
    $response = Mockery::mock(ResponseInterface::class);
    $response->allows('getHeaders')->once()->andReturns([
        'foo' => ['bar']
    ]);
    $response->allows('getProtocolVersion')->once()->andReturns('1.1');
    $response->allows('getStatusCode')->twice()->andReturns('200');
    $response->allows('getReasonPhrase')->once()->andReturns('Ok');

    $body = Mockery::mock(StreamInterface::class);
    $body->allows('isSeekable')->once()->andReturns(true);
    $body->allows('rewind')->once();
    $body->allows('read')->once()->andReturns('output');
    $body->allows('eof')->twice()->andReturns(false, true);
    $response->allows('getBody')->once()->andReturns($body);

    ob_flush();
    ob_start();
    emit($response);
    $body = ob_get_clean();
    expect($body)->toEqual('output');
});