<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

class Pipeline
{
    protected array $steps = [];
    protected mixed $passable;

    public function send(mixed $passable): self
    {
        $this->passable = $passable;
        return $this;
    }

    public function through(array $steps): self
    {
        $this->steps = $steps;
        return $this;
    }

    public function then(\Closure $destination): mixed
    {
        $pipeline = array_reduce(
            array_reverse($this->steps),
            $this->carry(),
            $this->prepareDestination($destination)
        );

        return $pipeline($this->passable);
    }

    public function thenReturn(): mixed
    {
        return $this->then(function ($passable) {
            return $passable;
        });
    }

    protected function carry(): \Closure
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                if ($passable instanceof JsonResponse) {
                    return $passable;
                }

                if (is_string($pipe)) {
                    $pipe = app($pipe);
                }

                return $pipe->handle($passable, $stack);
            };
        };
    }

    protected function prepareDestination(\Closure $destination): \Closure
    {
        return function ($passable) use ($destination) {
            return $destination($passable);
        };
    }
}
