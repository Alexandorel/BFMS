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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/nav-footer.htm */
class __TwigTemplate_9988da1d131fcdda6f9d739de0197fb7 extends Template
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
        yield "<div class=\"row gx-0\">
    ";
        // line 2
        $context['_parent'] = $context;
        $context['_seq'] = CoreExtension::ensureTraversable(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["footerNav"] ?? null), "items", [], "any", false, false, false, 2), "toNested", [], "any", false, false, false, 2));
        foreach ($context['_seq'] as $context["_key"] => $context["headerItem"]) {
            // line 3
            yield "        <div class=\"col-md-4 mb-3\">
            <ul class=\"nav flex-column\">
                <li class=\"nav-item nav-item-header\">
                    <a class=\"nav-link\" ";
            // line 6
            if ((($tmp = System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["headerItem"], "reference", [], "any", false, false, false, 6)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                yield "href=\"";
                yield (string) $this->env->getFilter('link')->getCallable()(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["headerItem"], "reference", [], "any", false, false, false, 6));
                yield "\"";
            }
            yield " ";
            yield (string) (((($tmp = System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["item"] ?? null), "is_external", [], "any", false, false, false, 6)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("target=\"_blank\"") : (""));
            yield ">
                        ";
            // line 7
            yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["headerItem"], "title", [], "any", false, false, false, 7), "html", null, true);
            yield "
                    </a>
                </li>
                ";
            // line 10
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["headerItem"], "children", [], "any", false, false, false, 10));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 11
                yield "                    <li class=\"nav-item\">
                        <a class=\"nav-link\" ";
                // line 12
                if ((($tmp = System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["item"], "reference", [], "any", false, false, false, 12)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                    yield "href=\"";
                    yield (string) $this->env->getFilter('link')->getCallable()(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["item"], "reference", [], "any", false, false, false, 12));
                    yield "\"";
                }
                yield " ";
                yield (string) (((($tmp = System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["item"], "is_external", [], "any", false, false, false, 12)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) ? ("target=\"_blank\"") : (""));
                yield ">
                            ";
                // line 13
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["item"], "title", [], "any", false, false, false, 13), "html", null, true);
                yield "
                        </a>
                    </li>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['item'], $context['_parent']);
            $context = array_intersect_key($context, $_parent);
            $context += $_parent;
            // line 17
            yield "            </ul>
        </div>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_key'], $context['headerItem'], $context['_parent']);
        $context = array_intersect_key($context, $_parent);
        $context += $_parent;
        // line 20
        yield "</div>
";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/nav-footer.htm";
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
        return array (  108 => 20,  99 => 17,  88 => 13,  78 => 12,  75 => 11,  71 => 10,  65 => 7,  55 => 6,  50 => 3,  46 => 2,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"row gx-0\">
    {% for headerItem in footerNav.items.toNested %}
        <div class=\"col-md-4 mb-3\">
            <ul class=\"nav flex-column\">
                <li class=\"nav-item nav-item-header\">
                    <a class=\"nav-link\" {% if headerItem.reference %}href=\"{{ headerItem.reference|link }}\"{% endif %} {{ item.is_external ? \x27target=\"_blank\"\x27 }}>
                        {{ headerItem.title }}
                    </a>
                </li>
                {% for item in headerItem.children %}
                    <li class=\"nav-item\">
                        <a class=\"nav-link\" {% if item.reference %}href=\"{{ item.reference|link }}\"{% endif %} {{ item.is_external ? \x27target=\"_blank\"\x27 }}>
                            {{ item.title }}
                        </a>
                    </li>
                {% endfor %}
            </ul>
        </div>
    {% endfor %}
</div>
{# Example Footer Nav Markup

<div class=\"row gx-0\">
    <div class=\"col-md-4 mb-3\">
        <ul class=\"nav flex-column\">
            <li class=\"nav-item nav-item-header\">
                <a class=\"nav-link\" href=\"{{ \x27index\x27|page }}\">Company</a>
            </li>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"{{ \x27blog/index\x27|page }}\">Blog</a>
            </li>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"{{ \x27wiki/index\x27|page }}\">Wiki</a>
            </li>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"{{ \x27about\x27|page }}\">About</a>
            </li>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"{{ \x27contact\x27|page }}\">Contact</a>
            </li>
        </ul>
    </div>
    <div class=\"col-md-4 mb-3\">
        <ul class=\"nav flex-column\">
            <li class=\"nav-item nav-item-header\">
                <a class=\"nav-link\" href=\"{{ \x27index\x27|page }}\">Technology</a>
            </li>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"{{ \x27ajax\x27|page }}\">AJAX Demo</a>
            </li>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"{{ \x27components\x27|page }}\">Components Demo</a>
            </li>
        </ul>
    </div>
    <div class=\"col-md-4 mb-3\">
        <ul class=\"nav flex-column\">
            <li class=\"nav-item nav-item-header\">
                <a class=\"nav-link\" href=\"https://octobercms.com\" target=\"_blank\">October CMS</a>
            </li>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"https://docs.octobercms.com\" target=\"_blank\">Documentation</a>
            </li>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"https://octobercms.com/pricing\" target=\"_blank\">Buy a License</a>
            </li>
            <li class=\"nav-item\">
                <a class=\"nav-link\" href=\"https://www.youtube.com/c/OctoberCMSOfficial\" target=\"_blank\">YouTube Channel</a>
            </li>
        </ul>
    </div>
</div>
#}", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/nav-footer.htm", "");
    }
}
