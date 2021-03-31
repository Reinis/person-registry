<?php

namespace PersonRegistry\Views;

use Twig\Environment;

class TwigView implements View
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(string $name, array $context = []): string
    {
        if (!str_ends_with($name, '.twig')) {
            $name .= '.twig';
        }

        return $this->twig->render($name, $context);
    }
}
