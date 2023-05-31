<?php

// config for Tiktoken
return [
    // Cache folder for vocab files
    'cache_dir' => storage_path('framework/cache/tiktoken'),

    /**
     * The default encoder
     * cl100k_base: gpt-4, gpt-3.5-turbo, text-embedding-ada-002
     * p50k_base: Codex models, text-davinci-002, text-davinci-003
     * r50k_base: text-davinci-001
     */
    'default_encoder' => 'cl100k_base',
];
