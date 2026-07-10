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

/* D:\HolisunInternship\BFMS\themes/demo/partials/blog/post-card.htm */
class __TwigTemplate_e528e3f01b73a0bd6250ecc9c836089d extends Template
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
        yield "<div class=\"col ";
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("cssClass", $context)) ? (Twig\Extension\CoreExtension::default(($context["cssClass"] ?? null), "")) : ("")), "html", null, true);
        yield "\">
    <div class=\"card card-post mb-5\">
        ";
        // line 3
        if ((($tmp = System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "banner", [], "any", false, false, false, 3)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 4
            yield "            <div class=\"card-banner ";
            yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("bannerCss", $context)) ? (Twig\Extension\CoreExtension::default(($context["bannerCss"] ?? null), "")) : ("")), "html", null, true);
            yield "\" style=\"background-image:url(\x27";
            yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "banner", [], "any", false, false, false, 4), "path", [], "any", false, false, false, 4), "html", null, true);
            yield "\x27)\"></div>
        ";
        } else {
            // line 6
            yield "            <div class=\"card-banner ";
            yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((array_key_exists("bannerCss", $context)) ? (Twig\Extension\CoreExtension::default(($context["bannerCss"] ?? null), "")) : ("")), "html", null, true);
            yield "\" style=\"background-image:url(\x27";
            yield (string) $this->extensions['Cms\Twig\Extension']->partialFunction("site/helpers/random-stock-image");
            yield "\x27)\"></div>
        ";
        }
        // line 8
        yield "
        <div class=\"card-body\">
            ";
        // line 10
        if ((($tmp = System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "categories", [], "any", false, false, false, 10)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 11
            yield "                <div class=\"blog-post-categories\">
                    <ul class=\"list-inline\">
                        ";
            // line 13
            $context['_parent'] = $context;
            $context['_seq'] = CoreExtension::ensureTraversable(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "categories", [], "any", false, false, false, 13));
            foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
                // line 14
                yield "                            <li class=\"list-inline-item\">
                                &mdash; <a href=\"";
                // line 15
                yield (string) $this->extensions['Cms\Twig\Extension']->pageFilter("blog/category", ["slug" => System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["category"], "slug", [], "any", false, false, false, 15), "id" => System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["category"], "id", [], "any", false, false, false, 15)]);
                yield "\">";
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, $context["category"], "title", [], "any", false, false, false, 15), "html", null, true);
                yield "</a>
                            </li>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_key'], $context['category'], $context['_parent']);
            $context = array_intersect_key($context, $_parent);
            $context += $_parent;
            // line 18
            yield "                    </ul>
                </div>
            ";
        }
        // line 21
        yield "
            <h4 class=\"card-title\">
                <a href=\"";
        // line 23
        yield (string) $this->extensions['Cms\Twig\Extension']->pageFilter("blog/post", ["slug" => System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "slug", [], "any", false, false, false, 23), "id" => System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "id", [], "any", false, false, false, 23)]);
        yield "\" class=\"stretched-link\">";
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "title", [], "any", false, false, false, 23), "html", null, true);
        yield "</a>
            </h4>

            <div class=\"featured-text\">
                <p>";
        // line 27
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "featured_text", [], "any", false, false, false, 27), "html", null, true);
        yield "</p>
            </div>
        </div>

        <div class=\"card-footer\">
            <div class=\"card-meta\">
                <div class=\"meta-item meta-date text-icon text-icon-date\">
                    ";
        // line 34
        yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape($this->extensions['Twig\Extension\CoreExtension']->formatDate(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "published_at_date", [], "any", false, false, false, 34), "j M Y"), "html", null, true);
        yield "
                </div>
                ";
        // line 36
        if ((($tmp = System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "author", [], "any", false, false, false, 36)) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
            // line 37
            yield "                    <div class=\"meta-item meta-divider\">
                        &bull;
                    </div>
                    <div class=\"meta-item meta-author text-icon text-icon-author\">
                        By <a href=\"";
            // line 41
            yield (string) $this->extensions['Cms\Twig\Extension']->pageFilter("blog/author", ["slug" => System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "author", [], "any", false, false, false, 41), "slug", [], "any", false, false, false, 41)]);
            yield "\">
                            ";
            // line 42
            yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "author", [], "any", false, true, false, 42), "title", [], "any", true, true, false, 42)) ? (Twig\Extension\CoreExtension::default(System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["post"] ?? null), "author", [], "any", false, false, false, 42), "title", [], "any", false, false, false, 42), "")) : ("")), "html", null, true);
            yield "
                        </a>
                    </div>
                ";
        }
        // line 46
        yield "            </div>
        </div>
    </div>
</div>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/blog/post-card.htm";
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
        return array (  148 => 46,  141 => 42,  137 => 41,  131 => 37,  129 => 36,  124 => 34,  114 => 27,  105 => 23,  101 => 21,  96 => 18,  84 => 15,  81 => 14,  77 => 13,  73 => 11,  71 => 10,  67 => 8,  59 => 6,  51 => 4,  49 => 3,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"col {{ cssClass|default(\x27\x27) }}\">
    <div class=\"card card-post mb-5\">
        {% if post.banner %}
            <div class=\"card-banner {{ bannerCss|default(\x27\x27) }}\" style=\"background-image:url(\x27{{ post.banner.path }}\x27)\"></div>
        {% else %}
            <div class=\"card-banner {{ bannerCss|default(\x27\x27) }}\" style=\"background-image:url(\x27{{ partial(\x27site/helpers/random-stock-image\x27) }}\x27)\"></div>
        {% endif %}

        <div class=\"card-body\">
            {% if post.categories %}
                <div class=\"blog-post-categories\">
                    <ul class=\"list-inline\">
                        {% for category in post.categories %}
                            <li class=\"list-inline-item\">
                                &mdash; <a href=\"{{ \x27blog/category\x27|page({ slug: category.slug, id: category.id }) }}\">{{ category.title }}</a>
                            </li>
                        {% endfor %}
                    </ul>
                </div>
            {% endif %}

            <h4 class=\"card-title\">
                <a href=\"{{ \x27blog/post\x27|page({ slug: post.slug, id: post.id }) }}\" class=\"stretched-link\">{{ post.title }}</a>
            </h4>

            <div class=\"featured-text\">
                <p>{{ post.featured_text }}</p>
            </div>
        </div>

        <div class=\"card-footer\">
            <div class=\"card-meta\">
                <div class=\"meta-item meta-date text-icon text-icon-date\">
                    {{ post.published_at_date|date(\x27j M Y\x27) }}
                </div>
                {% if post.author %}
                    <div class=\"meta-item meta-divider\">
                        &bull;
                    </div>
                    <div class=\"meta-item meta-author text-icon text-icon-author\">
                        By <a href=\"{{ \x27blog/author\x27|page({ slug: post.author.slug }) }}\">
                            {{ post.author.title|default(\x27\x27) }}
                        </a>
                    </div>
                {% endif %}
            </div>
        </div>
    </div>
</div>", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/blog/post-card.htm", "");
    }
}
