<?php
declare(strict_types=1);

namespace template;

const VIEWS = __DIR__ . '/../resources/views';
class TemplateEngine
{
    public static function render(string $template, array $context): string {
        $replacedString = $template;
        foreach ($context as $key => $value) {
            $replacedString = str_replace('{{'.$key.'}}', (string)$value, $replacedString);
        }
        return $replacedString;
    }

    public static function render_view(string $view, array $context): string {
        $file = VIEWS . '/' . $view . '.html'; // FIXME: remove hardcoded extension
        if (!file_exists($file)) {
            throw new \Exception("file $file does not exist");
        }

        $contents = file_get_contents($file);

        return TemplateEngine::render($contents, $context);
    }
}