<?php

namespace Spatie\ImageOptimizer;

class OptimizerChainFactory
{
    public static function create(): OptimizerChain
    {
        return new OptimizerChain();
    }
}
