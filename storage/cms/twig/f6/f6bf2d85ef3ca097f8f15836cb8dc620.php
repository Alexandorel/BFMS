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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/footer.htm */
class __TwigTemplate_140b347f44ce34f43579ba92b5aa1ed9 extends Template
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
        yield "<div class=\"element-footer ";
        yield (string) (((($tmp = ((array_key_exists("blueFooterStyle", $context)) ? (Twig\Extension\CoreExtension::default(($context["blueFooterStyle"] ?? null), false)) : (false))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("footer-bluezone") : (""));
        yield "\">
    <div class=\"container\">
        <div class=\"row\">
            <div class=\"col-md-10 col-lg-8 col-xl-7\">
                <div class=\"footer-brand\">
                    <img src=\"";
        // line 6
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/images/logo.svg");
        yield "\" alt=\"October CMS Demo\" width=\"270\" />
                </div>
                <div class=\"footer-nav\">
                    ";
        // line 9
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/nav-footer"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 10
        yield "                </div>
            </div>
        </div>
        <div class=\"row\">
            <div class=\"col-sm-6\">
                <div class=\"footer-social\">
                    <ul class=\"nav\">
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" aria-current=\"page\" href=\"https://dribbble.com/\" target=\"_blank\">
                                <img src=\"";
        // line 19
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/images/social-icons-white/dribbble-white.png");
        yield "\" alt=\"Dribbble\"  />
                            </a>
                        </li>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" aria-current=\"page\" href=\"https://www.linkedin.com/company/october-cms/\" target=\"_blank\">
                                <img src=\"";
        // line 24
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/images/social-icons-white/linkedin-white.png");
        yield "\" alt=\"LinkedIn\"  />
                            </a>
                        </li>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" aria-current=\"page\" href=\"https://twitter.com/octobercms\" target=\"_blank\">
                                <img src=\"";
        // line 29
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/images/social-icons-white/twitter-white.png");
        yield "\" alt=\"Twitter\" />
                            </a>
                        </li>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" aria-current=\"page\" href=\"https://www.facebook.com/octobercms\" target=\"_blank\">
                                <img src=\"";
        // line 34
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/images/social-icons-white/facebook-white.png");
        yield "\" alt=\"Facebook\" />
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class=\"col-sm-6\">
                <div class=\"footer-copyright\">
                    <p>&copy; ";
        // line 42
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate("now", "Y"), "html", null, true);
        yield " Your Company Name</p>
                    <p>All rights reserved</p>
                </div>
            </div>
        </div>
        <div class=\"footer-decoration-1\"></div>
        <div class=\"footer-decoration-2\"></div>
    </div>
</div>

";
        // line 52
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/modals/alert-dialog"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 53
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/modals/password-dialog"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        // line 54
        $cmsPartialParams = [];
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->partialFunction("site/modals/ajax-modal"        , array_merge($context, ['__cms_partial_params' => $cmsPartialParams], $cmsPartialParams)        , true);
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/footer.htm";
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
        return array (  126 => 54,  123 => 53,  120 => 52,  107 => 42,  96 => 34,  88 => 29,  80 => 24,  72 => 19,  61 => 10,  58 => 9,  52 => 6,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"element-footer {{ blueFooterStyle|default(false) ? \x27footer-bluezone\x27 }}\">
    <div class=\"container\">
        <div class=\"row\">
            <div class=\"col-md-10 col-lg-8 col-xl-7\">
                <div class=\"footer-brand\">
                    <img src=\"{{ \x27assets/images/logo.svg\x27|theme }}\" alt=\"October CMS Demo\" width=\"270\" />
                </div>
                <div class=\"footer-nav\">
                    {% partial \x27site/nav-footer\x27 %}
                </div>
            </div>
        </div>
        <div class=\"row\">
            <div class=\"col-sm-6\">
                <div class=\"footer-social\">
                    <ul class=\"nav\">
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" aria-current=\"page\" href=\"https://dribbble.com/\" target=\"_blank\">
                                <img src=\"{{ \x27assets/images/social-icons-white/dribbble-white.png\x27|theme }}\" alt=\"Dribbble\"  />
                            </a>
                        </li>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" aria-current=\"page\" href=\"https://www.linkedin.com/company/october-cms/\" target=\"_blank\">
                                <img src=\"{{ \x27assets/images/social-icons-white/linkedin-white.png\x27|theme }}\" alt=\"LinkedIn\"  />
                            </a>
                        </li>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" aria-current=\"page\" href=\"https://twitter.com/octobercms\" target=\"_blank\">
                                <img src=\"{{ \x27assets/images/social-icons-white/twitter-white.png\x27|theme }}\" alt=\"Twitter\" />
                            </a>
                        </li>
                        <li class=\"nav-item\">
                            <a class=\"nav-link\" aria-current=\"page\" href=\"https://www.facebook.com/octobercms\" target=\"_blank\">
                                <img src=\"{{ \x27assets/images/social-icons-white/facebook-white.png\x27|theme }}\" alt=\"Facebook\" />
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class=\"col-sm-6\">
                <div class=\"footer-copyright\">
                    <p>&copy; {{ \"now\"|date(\"Y\") }} Your Company Name</p>
                    <p>All rights reserved</p>
                </div>
            </div>
        </div>
        <div class=\"footer-decoration-1\"></div>
        <div class=\"footer-decoration-2\"></div>
    </div>
</div>

{% partial \x27site/modals/alert-dialog\x27 %}
{% partial \x27site/modals/password-dialog\x27 %}
{% partial \x27site/modals/ajax-modal\x27 %}", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/footer.htm", "");
    }
}
