<?php

namespace App\Support;

class Svelte
{
    /**
     * Render a Svelte component with the given attributes.
     *
     * @param string $componentName The name of the Svelte component
     * @param array $attributes The data attributes to pass to the component
     * @param array $options Additional options for rendering (like container class, etc.)
     * @return string The rendered HTML
     */
    public static function render(string $componentName, array $attributes = [], array $options = []): string
    {
        // Encode and escape all attributes
        $encodedAttributes = [];
        foreach ($attributes as $key => $value) {
            $encodedAttributes["data-$key"] = e(is_array($value) || is_object($value) ? json_encode($value) : $value);
        }

        // Add the component name
        $encodedAttributes['data-svelte'] = $componentName;

        // Add CSRF token by default if not provided
        if (!isset($encodedAttributes['data-csrf'])) {
            $encodedAttributes['data-csrf'] = csrf_token();
        }

        // Build the HTML attributes string
        $htmlAttributes = '';
        foreach ($encodedAttributes as $key => $value) {
            $htmlAttributes .= " $key=\"$value\"";
        }

        $containerClass = $options['class'] ?? 'contents';

        return <<<HTML
        <div>
            <div class="$containerClass" {$htmlAttributes}></div>
        </div>
        HTML;
    }
}
