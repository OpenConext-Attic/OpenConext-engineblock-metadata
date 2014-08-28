<?php

namespace OpenConext\Component\EngineBlockMetadata\Translator;


interface EntityTranslatorInterface
{
    public function accept($entity);
    public function translate($entity);
}

