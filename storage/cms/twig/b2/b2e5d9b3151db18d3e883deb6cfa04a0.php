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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/nav-links.htm */
class __TwigTemplate_7ebf478794edd0692297231d61a5e6dc extends Template
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
        $context["activeNavLink"] = ((array_key_exists("activeNavLink", $context)) ? (Twig\Extension\CoreExtension::default(($context["activeNavLink"] ?? null), System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["this"] ?? null), "page", [], "any", false, false, false, 1), "id", [], "any", false, false, false, 1))) : (System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["this"] ?? null), "page", [], "any", false, false, false, 1), "id", [], "any", false, false, false, 1)));
        // line 2
        yield "
<li class=\"nav-item\">
    <a class=\"nav-link ";
        // line 4
        yield (string) (((($context["activeNavLink"] ?? null) == "blog")) ? ("active") : (""));
        yield "\" href=\"";
        yield (string) $this->extensions['Cms\Twig\Extension']->pageFilter("blog/index");
        yield "\">Blog</a>
</li>
<li class=\"nav-item\">
    <a class=\"nav-link ";
        // line 7
        yield (string) (((($context["activeNavLink"] ?? null) == "wiki")) ? ("active") : (""));
        yield "\" href=\"";
        yield (string) $this->extensions['Cms\Twig\Extension']->pageFilter("wiki/index");
        yield "\">Wiki</a>
</li>
<li class=\"nav-item\">
    <a class=\"nav-link ";
        // line 10
        yield (string) (((($context["activeNavLink"] ?? null) == "about")) ? ("active") : (""));
        yield "\" href=\"";
        yield (string) $this->extensions['Cms\Twig\Extension']->pageFilter("about");
        yield "\">About</a>
</li>
<li class=\"nav-item\">
    <a class=\"nav-link ";
        // line 13
        yield (string) (((($context["activeNavLink"] ?? null) == "contact")) ? ("active") : (""));
        yield "\" href=\"";
        yield (string) $this->extensions['Cms\Twig\Extension']->pageFilter("contact");
        yield "\">Contact</a>
</li>
<li class=\"nav-item\">
    <a class=\"nav-link ";
        // line 16
        yield (string) (((($context["activeNavLink"] ?? null) == "components")) ? ("active") : (""));
        yield "\" href=\"";
        yield (string) $this->extensions['Cms\Twig\Extension']->pageFilter("components");
        yield "\">Components Demo</a>
</li>
";
        // line 18
        if ((($tmp = System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["sitePicker"] ?? null), "isEnabled", [], "any", false, false, false, 18)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 19
            yield "    <li class=\"nav-item dropdown\">
        <a class=\"btn btn-outline-light dropdown-toggle\" href=\"";
            // line 20
            yield (string) $this->extensions['Cms\Twig\Extension']->pageFilter(($context["this"] ?? null));
            yield "\" id=\"navbarDropdown\" role=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
            ";
            // line 21
            yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["this"] ?? null), "site", [], "any", false, true, false, 21), "name", [], "any", true, true, false, 21)) ? (Twig\Extension\CoreExtension::default(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["this"] ?? null), "site", [], "any", false, false, false, 21), "name", [], "any", false, false, false, 21), "Sites")) : ("Sites")), "html", null, true);
            yield "
        </a>
        <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdown\">
            ";
            // line 24
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["sitePicker"] ?? null), "sites", [], "any", false, false, false, 24));
            foreach ($context['_seq'] as $context["_key"] => $context["site"]) {
                // line 25
                yield "                <li><a class=\"dropdown-item ";
                yield (string) (((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["this"] ?? null), "site", [], "any", false, false, false, 25), "code", [], "any", false, false, false, 25) == System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["site"], "code", [], "any", false, false, false, 25))) ? ("active") : (""));
                yield "\" href=\"";
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["site"], "url", [], "any", false, false, false, 25), "html", null, true);
                yield "\">";
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["site"], "name", [], "any", false, false, false, 25), "html", null, true);
                yield "</a></li>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['site'], $context['_parent']);
            $context = array_intersect_key($context, $_parent);
            $context += $_parent;
            // line 27
            yield "            ";
            if ((($tmp = ($context["backendUrl"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 28
                yield "                <li><hr class=\"dropdown-divider\"></li>
                <li><a class=\"dropdown-item\" href=\"";
                // line 29
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["backendUrl"] ?? null), "html", null, true);
                yield "\">Backend Area</a></li>
            ";
            }
            // line 31
            yield "        </ul>
    </li>
";
        } elseif ((($tmp =         // line 33
($context["backendUrl"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 34
            yield "    <li class=\"nav-item\">
        <a class=\"btn btn-outline-light\" href=\"";
            // line 35
            yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["backendUrl"] ?? null), "html", null, true);
            yield "\" target=\"backend\">Backend Area</a>
    </li>
";
        }
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/nav-links.htm";
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
        return array (  141 => 35,  138 => 34,  136 => 33,  132 => 31,  127 => 29,  124 => 28,  121 => 27,  107 => 25,  103 => 24,  97 => 21,  93 => 20,  90 => 19,  88 => 18,  81 => 16,  73 => 13,  65 => 10,  57 => 7,  49 => 4,  45 => 2,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% set activeNavLink = activeNavLink|default(this.page.id) %}

<li class=\"nav-item\">
    <a class=\"nav-link {{ activeNavLink == \x27blog\x27 ? \x27active\x27 }}\" href=\"{{ \x27blog/index\x27|page }}\">Blog</a>
</li>
<li class=\"nav-item\">
    <a class=\"nav-link {{ activeNavLink == \x27wiki\x27 ? \x27active\x27 }}\" href=\"{{ \x27wiki/index\x27|page }}\">Wiki</a>
</li>
<li class=\"nav-item\">
    <a class=\"nav-link {{ activeNavLink == \x27about\x27 ? \x27active\x27 }}\" href=\"{{ \x27about\x27|page }}\">About</a>
</li>
<li class=\"nav-item\">
    <a class=\"nav-link {{ activeNavLink == \x27contact\x27 ? \x27active\x27 }}\" href=\"{{ \x27contact\x27|page }}\">Contact</a>
</li>
<li class=\"nav-item\">
    <a class=\"nav-link {{ activeNavLink == \x27components\x27 ? \x27active\x27 }}\" href=\"{{ \x27components\x27|page }}\">Components Demo</a>
</li>
{% if sitePicker.isEnabled %}
    <li class=\"nav-item dropdown\">
        <a class=\"btn btn-outline-light dropdown-toggle\" href=\"{{ this|page }}\" id=\"navbarDropdown\" role=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">
            {{ this.site.name|default(\x27Sites\x27) }}
        </a>
        <ul class=\"dropdown-menu\" aria-labelledby=\"navbarDropdown\">
            {% for site in sitePicker.sites %}
                <li><a class=\"dropdown-item {{ this.site.code == site.code ? \x27active\x27 }}\" href=\"{{ site.url }}\">{{ site.name }}</a></li>
            {% endfor %}
            {% if backendUrl %}
                <li><hr class=\"dropdown-divider\"></li>
                <li><a class=\"dropdown-item\" href=\"{{ backendUrl }}\">Backend Area</a></li>
            {% endif %}
        </ul>
    </li>
{% elseif backendUrl %}
    <li class=\"nav-item\">
        <a class=\"btn btn-outline-light\" href=\"{{ backendUrl }}\" target=\"backend\">Backend Area</a>
    </li>
{% endif %}", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/nav-links.htm", "");
    }
}
