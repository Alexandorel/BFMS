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

/* D:\HolisunInternship\BFMS\themes/demo/layouts/home.htm */
class __TwigTemplate_b5ed4eb189c25936f051ea5b1399fabe extends Template
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
        yield "<!DOCTYPE html>
<html>
    <head>
        ";
        // line 4
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/head/head-meta"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 5
        yield "        <title>October CMS - ";
        yield (string) (((($tmp = System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["this"] ?? null), "page", [], "any", false, false, false, 5), "meta_title", [], "any", false, false, false, 5)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ($this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["this"] ?? null), "page", [], "any", false, false, false, 5), "meta_title", [], "any", false, false, false, 5), "html", null, true)) : ($this->extensions['Cms\Twig\Extension']->placeholderFunction("pageTitle")));
        yield "</title>
        <meta name=\"description\" content=\"";
        // line 6
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["this"] ?? null), "page", [], "any", false, false, false, 6), "meta_description", [], "any", false, false, false, 6), "html", null, true);
        yield "\">
        <meta name=\"title\" content=\"";
        // line 7
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["this"] ?? null), "page", [], "any", false, false, false, 7), "meta_title", [], "any", false, false, false, 7), "html", null, true);
        yield "\">
        ";
        // line 8
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/head/head-links"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 9
        yield "        ";
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/head/head-scripts"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 10
        yield "        ";
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/head/analytics-code"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 11
        yield "        <meta name=\"turbo-visit-control\" content=\"reload\" />
    </head>
    <body class=\"layout-home\">

        <!-- Nav -->
        <nav id=\"layout-nav\" class=\"navbar navbar-expand-lg navbar-dark\">
            <div class=\"container\">
                <a class=\"navbar-brand\" href=\"";
        // line 18
        yield (string) $this->extensions['Cms\Twig\Extension']->pageFilter("index");
        yield "\">
                    <img src=\"";
        // line 19
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/images/logo.svg");
        yield "\" alt=\"October CMS Demo\" width=\"190\" />
                </a>
                <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarSupportedContent\" aria-controls=\"navbarSupportedContent\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">
                    <span class=\"navbar-toggler-icon\"></span>
                </button>
                <div class=\"collapse navbar-collapse\">
                    <ul class=\"navbar-nav ms-auto mb-2 mb-lg-0\">
                        ";
        // line 26
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/nav-links"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 27
        yield "                    </ul>
                </div>
            </div>
        </nav>

        ";
        // line 32
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/flash-messages"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 33
        yield "
        <!-- Content -->
        <section id=\"layout-content\">
            ";
        // line 36
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->pageFunction($context);
        // line 37
        yield "        </section>

        <!-- Footer -->
        <footer id=\"layout-footer\">
            ";
        // line 41
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/footer"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 42
        yield "        </footer>

        <!-- Mobile -->
        ";
        // line 45
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/nav-mobile"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 46
        yield "
        <!-- How the page is made -->
        ";
        // line 48
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/how-its-made"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 49
        yield "
    </body>
</html>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/layouts/home.htm";
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
        return array (  142 => 49,  139 => 48,  135 => 46,  132 => 45,  127 => 42,  124 => 41,  118 => 37,  116 => 36,  111 => 33,  108 => 32,  101 => 27,  98 => 26,  88 => 19,  84 => 18,  75 => 11,  71 => 10,  67 => 9,  64 => 8,  60 => 7,  56 => 6,  51 => 5,  48 => 4,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<!DOCTYPE html>
<html>
    <head>
        {% partial \x27site/head/head-meta\x27 %}
        <title>October CMS - {{ this.page.meta_title ?: placeholder(\x27pageTitle\x27) }}</title>
        <meta name=\"description\" content=\"{{ this.page.meta_description }}\">
        <meta name=\"title\" content=\"{{ this.page.meta_title }}\">
        {% partial \x27site/head/head-links\x27 %}
        {% partial \x27site/head/head-scripts\x27 %}
        {% partial \x27site/head/analytics-code\x27 %}
        <meta name=\"turbo-visit-control\" content=\"reload\" />
    </head>
    <body class=\"layout-home\">

        <!-- Nav -->
        <nav id=\"layout-nav\" class=\"navbar navbar-expand-lg navbar-dark\">
            <div class=\"container\">
                <a class=\"navbar-brand\" href=\"{{ \x27index\x27|page }}\">
                    <img src=\"{{ \x27assets/images/logo.svg\x27|theme }}\" alt=\"October CMS Demo\" width=\"190\" />
                </a>
                <button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarSupportedContent\" aria-controls=\"navbarSupportedContent\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">
                    <span class=\"navbar-toggler-icon\"></span>
                </button>
                <div class=\"collapse navbar-collapse\">
                    <ul class=\"navbar-nav ms-auto mb-2 mb-lg-0\">
                        {% partial \x27site/nav-links\x27 %}
                    </ul>
                </div>
            </div>
        </nav>

        {% partial \x27site/flash-messages\x27 %}

        <!-- Content -->
        <section id=\"layout-content\">
            {% page %}
        </section>

        <!-- Footer -->
        <footer id=\"layout-footer\">
            {% partial \x27site/footer\x27 %}
        </footer>

        <!-- Mobile -->
        {% partial \x27site/nav-mobile\x27 %}

        <!-- How the page is made -->
        {% partial \x27site/how-its-made\x27 %}

    </body>
</html>", "D:\\HolisunInternship\\BFMS\\themes/demo/layouts/home.htm", "");
    }
}
