<?php

namespace WA\Events;

/**
 * Class BaseFailureHandler.
 */
abstract class BaseFailureHandler implements EventHandlerInterface
{
    protected $context = 'default';
    protected $dump;
    protected $carrierDump;

    /**
     * @param $data
     *
     * @return mixed|void
     *
     * @throws \Exception
     */
    public function handle($data)
    {
        $this->dump = isset($data['dump']) ? $data['dump'] : null;

        $this->carrierDump = isset($data['carrierDump']) ? $data['carrierDump'] : null;

        if (isset($this->carrierDump) && !isset($this->dump)) {
            $this->dump = $this->carrierDump->dump;
        }
        $dumpId = isset($this->dump) ? $this->dump->id : null;
        $carrierDumpId = isset($this->carrierDump) ? $this->carrierDump->id : null;

        $code = isset($data['code']) ? $data['code'] : null;
        $message = isset($data['message']) ? $data['message'] : null;

        /* @var $exception \Exception */
        $exception = isset($data['exception']) ? $data['exception'] : new \Exception($message);

        $this->processFailure();

        if (isset($this->dumpExceptions)) {
            $deData = [
                'carrierDumpId' => $carrierDumpId,
                'dumpId' => $dumpId,
                'context' => $this->context,
                'presentation' => $exception->getMessage(),
                'stacktrace' => $exception->getTraceAsString(),
            ];

            $this->dumpExceptions->create($deData);
        }

        if (in_array(\App::environment(), ['dev', 'local', 'homestead'])) {
            throw $exception;
        }

        $this->throwUp($message, $code, $exception);

        return true;
    }

    abstract public function processFailure();

    /**
     * @param $message
     * @param $code
     * @param $exception
     *
     * @return mixed
     */
    abstract public function throwUp($message, $code, $exception);
}
