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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/nav-mobile.htm */
class __TwigTemplate_db7d257aa343858f890655e289cf4311 extends Template
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
        yield "<div class=\"navbar-mobile\">
    <div class=\"collapse navbar-collapse\" id=\"navbarSupportedContent\">
        <div class=\"text-end\">
            <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarSupportedContent\" aria-controls=\"navbarSupportedContent\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">
                <span class=\"bi bi-x-circle\"></span>
            </button>
        </div>
        <ul class=\"nav flex-column mb-2 mb-lg-0\">
            ";
        // line 9
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/nav-links"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 10
        yield "        </ul>
    </div>
</div>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/nav-mobile.htm";
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
        return array (  56 => 10,  53 => 9,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"navbar-mobile\">
    <div class=\"collapse navbar-collapse\" id=\"navbarSupportedContent\">
        <div class=\"text-end\">
            <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarSupportedContent\" aria-controls=\"navbarSupportedContent\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">
                <span class=\"bi bi-x-circle\"></span>
            </button>
        </div>
        <ul class=\"nav flex-column mb-2 mb-lg-0\">
            {% partial \x27site/nav-links\x27 %}
        </ul>
    </div>
</div>", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/nav-mobile.htm", "");
    }
}
