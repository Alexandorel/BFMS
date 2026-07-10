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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/head/head-links.htm */
class __TwigTemplate_44a412f7b69f0f99e5e2fe8d6a332c14 extends Template
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
        // line 2
        yield "<link rel=\"icon\" type=\"image/png\" href=\"";
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/images/favicon.png");
        yield "\">

";
        // line 5
        yield "<link href=\"";
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/css/vendor.css");
        yield "\" rel=\"stylesheet\">
<link href=\"";
        // line 6
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/css/theme.css");
        yield "\" rel=\"stylesheet\">

";
        // line 9
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->assetsFunction('css');
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->displayBlock('styles');
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/head/head-links.htm";
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
        return array (  59 => 9,  54 => 6,  49 => 5,  43 => 2,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{# Favicon #}
<link rel=\"icon\" type=\"image/png\" href=\"{{ \x27assets/images/favicon.png\x27|theme }}\">

{# Styles #}
<link href=\"{{ \x27assets/css/vendor.css\x27|theme }}\" rel=\"stylesheet\">
<link href=\"{{ \x27assets/css/theme.css\x27|theme }}\" rel=\"stylesheet\">

{# Placeholder #}
{% styles %}", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/head/head-links.htm", "");
    }
}
