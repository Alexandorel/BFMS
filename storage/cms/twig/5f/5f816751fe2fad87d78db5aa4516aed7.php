<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Sandbox\SecurityNotAllowedTestError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/helpers/random-stock-image.htm */
class __TwigTemplate_7af52a7f849ad4f9fbc1052ca98806e8 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        $context["images"] = ["workspace", "desktop", "pancakes", "doughnuts"];
        // line 6
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter((("assets/images/stock/" . Twig\Extension\CoreExtension::random($this->env->getCharset(), ($context["images"] ?? null))) . ".png"));
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/helpers/random-stock-image.htm";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  45 => 6,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% set images = [
    \x27workspace\x27,
    \x27desktop\x27,
    \x27pancakes\x27,
    \x27doughnuts\x27
] %}{{ (\x27assets/images/stock/\x27 ~ random(images) ~ \x27.png\x27)|theme }}", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/helpers/random-stock-image.htm", "");
    }
}
