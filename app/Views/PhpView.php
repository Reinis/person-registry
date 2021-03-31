<?php

namespace PersonRegistry\Views;

class PhpView implements View
{
    public function render(string $name, array $context = []): string
    {
        if (!str_ends_with($name, '.php')) {
            $name .= '.php';
        }

        extract($context, EXTR_OVERWRITE);

        ob_start();
        require_once __DIR__ . '/php/' . $name;

        return ob_get_clean();
    }
}
