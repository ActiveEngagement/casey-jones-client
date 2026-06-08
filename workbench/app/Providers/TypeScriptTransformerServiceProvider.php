<?php

namespace Workbench\App\Providers;

use Spatie\LaravelTypeScriptTransformer\LaravelData\LaravelDataTypeScriptTransformerExtension;
use Spatie\LaravelTypeScriptTransformer\TypeScriptTransformerApplicationServiceProvider as BaseServiceProvider;
use Spatie\TypeScriptTransformer\Transformers\AttributedClassTransformer;
use Spatie\TypeScriptTransformer\Transformers\EnumTransformer;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfigFactory;
use Spatie\TypeScriptTransformer\Writers\GlobalNamespaceWriter;

class TypeScriptTransformerServiceProvider extends BaseServiceProvider
{
    protected function configure(TypeScriptTransformerConfigFactory $config): void
    {
        // workbench/app/Providers -> package root
        $root = dirname(__DIR__, 3);

        $config
            ->transformer(AttributedClassTransformer::class)
            ->transformer(EnumTransformer::class)
            ->transformDirectories($root.'/src')
            ->extension(new LaravelDataTypeScriptTransformerExtension)
            ->outputDirectory($root.'/resources/types')
            ->writer(new GlobalNamespaceWriter('casey-jones.d.ts'));
    }
}
