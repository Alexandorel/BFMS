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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/how-its-made.htm */
class __TwigTemplate_3e4d4de1d2b083c57fb413ebf9d6a633 extends Template
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
        if ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["this"] ?? null), "page", [], "any", false, false, false, 1), "id", [], "any", false, false, false, 1) == "blog-category")) {
            // line 2
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:blog/category.htm";
            // line 3
            yield "    ";
            $context["howItsMadeTailorBlueprint"] = "tailor:tailor-blueprint:blog/category.yaml";
            // line 4
            yield "    ";
            $context["howItsMadeTailorContent"] = ("entries/blog_category/" . System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["category"] ?? null), "id", [], "any", false, false, false, 4));
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 5
($context["this"] ?? null), "page", [], "any", false, false, false, 5), "id", [], "any", false, false, false, 5) == "404")) {
            // line 6
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:404.htm";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 7
($context["this"] ?? null), "page", [], "any", false, false, false, 7), "id", [], "any", false, false, false, 7) == "error")) {
            // line 8
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:error.htm";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 9
($context["this"] ?? null), "page", [], "any", false, false, false, 9), "id", [], "any", false, false, false, 9) == "about")) {
            // line 10
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:about.htm";
            // line 11
            yield "    ";
            $context["howItsMadeTailorBlueprint"] = "tailor:tailor-blueprint:landing/landing-page.yaml";
            // line 12
            yield "    ";
            $context["howItsMadeTailorContent"] = "entries/page_about";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 13
($context["this"] ?? null), "page", [], "any", false, false, false, 13), "id", [], "any", false, false, false, 13) == "ajax")) {
            // line 14
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:ajax.htm";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 15
($context["this"] ?? null), "page", [], "any", false, false, false, 15), "id", [], "any", false, false, false, 15) == "components")) {
            // line 16
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:components.htm";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 17
($context["this"] ?? null), "page", [], "any", false, false, false, 17), "id", [], "any", false, false, false, 17) == "contact")) {
            // line 18
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:contact.htm";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 19
($context["this"] ?? null), "page", [], "any", false, false, false, 19), "id", [], "any", false, false, false, 19) == "index")) {
            // line 20
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:index.htm";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 21
($context["this"] ?? null), "page", [], "any", false, false, false, 21), "id", [], "any", false, false, false, 21) == "blog-archive")) {
            // line 22
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:blog/archive.htm";
            // line 23
            yield "    ";
            $context["howItsMadeTailorBlueprint"] = "tailor:tailor-blueprint:blog/post.yaml";
            // line 24
            yield "    ";
            $context["howItsMadeTailorContent"] = "entries/blog_post";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 25
($context["this"] ?? null), "page", [], "any", false, false, false, 25), "id", [], "any", false, false, false, 25) == "blog-author")) {
            // line 26
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:blog/author.htm";
            // line 27
            yield "    ";
            $context["howItsMadeTailorBlueprint"] = "tailor:tailor-blueprint:blog/author.yaml";
            // line 28
            yield "    ";
            $context["howItsMadeTailorContent"] = ("entries/blog_author/" . System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["author"] ?? null), "id", [], "any", false, false, false, 28));
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 29
($context["this"] ?? null), "page", [], "any", false, false, false, 29), "id", [], "any", false, false, false, 29) == "blog-index")) {
            // line 30
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:blog/index.htm";
            // line 31
            yield "    ";
            $context["howItsMadeTailorBlueprint"] = "tailor:tailor-blueprint:blog/post.yaml";
            // line 32
            yield "    ";
            $context["howItsMadeTailorContent"] = "entries/blog_post";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 33
($context["this"] ?? null), "page", [], "any", false, false, false, 33), "id", [], "any", false, false, false, 33) == "blog-post")) {
            // line 34
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:blog/post.htm";
            // line 35
            yield "    ";
            $context["howItsMadeTailorBlueprint"] = "tailor:tailor-blueprint:blog/post.yaml";
            // line 36
            yield "    ";
            $context["howItsMadeTailorContent"] = ("entries/blog_post/" . System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["blog"] ?? null), "id", [], "any", false, false, false, 36));
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 37
($context["this"] ?? null), "page", [], "any", false, false, false, 37), "id", [], "any", false, false, false, 37) == "blog-search")) {
            // line 38
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:blog/search.htm";
            // line 39
            yield "    ";
            $context["howItsMadeTailorBlueprint"] = "tailor:tailor-blueprint:blog/post.yaml";
            // line 40
            yield "    ";
            $context["howItsMadeTailorContent"] = "entries/blog_post";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 41
($context["this"] ?? null), "page", [], "any", false, false, false, 41), "id", [], "any", false, false, false, 41) == "wiki-article")) {
            // line 42
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:wiki/article.htm";
            // line 43
            yield "    ";
            $context["howItsMadeTailorBlueprint"] = "tailor:tailor-blueprint:wiki/article.yaml";
            // line 44
            yield "    ";
            $context["howItsMadeTailorContent"] = ("entries/page_article/" . System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, ($context["wiki"] ?? null), "id", [], "any", false, false, false, 44));
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 45
($context["this"] ?? null), "page", [], "any", false, false, false, 45), "id", [], "any", false, false, false, 45) == "wiki-index")) {
            // line 46
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:wiki/index.htm";
            // line 47
            yield "    ";
            $context["howItsMadeTailorBlueprint"] = "tailor:tailor-blueprint:wiki/article.yaml";
            // line 48
            yield "    ";
            $context["howItsMadeTailorContent"] = "entries/page_article";
        } elseif ((System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source, System\Twig\Node\GetAttrNode::customGetAttribute($this->env, $this->source,         // line 49
($context["this"] ?? null), "page", [], "any", false, false, false, 49), "id", [], "any", false, false, false, 49) == "wiki-search")) {
            // line 50
            yield "    ";
            $context["howItsMadeCmsTemplate"] = "cms:cms-page:wiki/search.htm";
            // line 51
            yield "    ";
            $context["howItsMadeTailorBlueprint"] = "tailor:tailor-blueprint:wiki/article.yaml";
            // line 52
            yield "    ";
            $context["howItsMadeTailorContent"] = "entries/page_article";
        }
        // line 54
        yield "
";
        // line 55
        if (((($tmp = ($context["backendUrl"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp) && (($tmp = ($context["howItsMadeCmsTemplate"] ?? null)) && $tmp instanceof Markup ? (string) $tmp : $tmp))) {
            // line 56
            yield "    <div class=\"how-its-made init\">
        <div>
            <p>Wondering how this page is made? View the
                ";
            // line 59
            if ((($tmp = ((array_key_exists("howItsMadeCmsTemplate", $context)) ? (Twig\Extension\CoreExtension::default(($context["howItsMadeCmsTemplate"] ?? null), false)) : (false))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 60
                yield "                    <a target=\"_blank\" href=\"";
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((($context["backendUrl"] ?? null) . "/editor?open=") . ($context["howItsMadeCmsTemplate"] ?? null)), "html", null, true);
                yield "\">CMS Template</a>
                ";
            }
            // line 62
            yield "                ";
            if ((($tmp = ((array_key_exists("howItsMadeTailorBlueprint", $context)) ? (Twig\Extension\CoreExtension::default(($context["howItsMadeTailorBlueprint"] ?? null), false)) : (false))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 63
                yield "                    •  <a target=\"_blank\" href=\"";
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((($context["backendUrl"] ?? null) . "/editor?open=") . ($context["howItsMadeTailorBlueprint"] ?? null)), "html", null, true);
                yield "\">Tailor Blueprint</a>
                ";
            }
            // line 65
            yield "                ";
            if ((($tmp = ((array_key_exists("howItsMadeTailorContent", $context)) ? (Twig\Extension\CoreExtension::default(($context["howItsMadeTailorContent"] ?? null), false)) : (false))) && $tmp instanceof Markup ? (string) $tmp : $tmp)) {
                // line 66
                yield "                    •  <a target=\"_blank\" href=\"";
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(((($context["backendUrl"] ?? null) . "/tailor/") . ($context["howItsMadeTailorContent"] ?? null)), "html", null, true);
                yield "\">Tailor Content</a>
                ";
            }
            // line 68
            yield "            </p>
        </div>
    </div>
";
        }
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/how-its-made.htm";
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
        return array (  220 => 68,  214 => 66,  211 => 65,  205 => 63,  202 => 62,  196 => 60,  194 => 59,  189 => 56,  187 => 55,  184 => 54,  180 => 52,  177 => 51,  174 => 50,  172 => 49,  169 => 48,  166 => 47,  163 => 46,  161 => 45,  158 => 44,  155 => 43,  152 => 42,  150 => 41,  147 => 40,  144 => 39,  141 => 38,  139 => 37,  136 => 36,  133 => 35,  130 => 34,  128 => 33,  125 => 32,  122 => 31,  119 => 30,  117 => 29,  114 => 28,  111 => 27,  108 => 26,  106 => 25,  103 => 24,  100 => 23,  97 => 22,  95 => 21,  92 => 20,  90 => 19,  87 => 18,  85 => 17,  82 => 16,  80 => 15,  77 => 14,  75 => 13,  72 => 12,  69 => 11,  66 => 10,  64 => 9,  61 => 8,  59 => 7,  56 => 6,  54 => 5,  51 => 4,  48 => 3,  45 => 2,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% if this.page.id == \x27blog-category\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:blog/category.htm\x27 %}
    {% set howItsMadeTailorBlueprint = \x27tailor:tailor-blueprint:blog/category.yaml\x27 %}
    {% set howItsMadeTailorContent = \x27entries/blog_category/\x27 ~ category.id %}
{% elseif this.page.id == \x27404\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:404.htm\x27 %}
{% elseif this.page.id == \x27error\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:error.htm\x27 %}
{% elseif this.page.id == \x27about\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:about.htm\x27 %}
    {% set howItsMadeTailorBlueprint = \x27tailor:tailor-blueprint:landing/landing-page.yaml\x27 %}
    {% set howItsMadeTailorContent = \x27entries/page_about\x27 %}
{% elseif this.page.id == \x27ajax\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:ajax.htm\x27 %}
{% elseif this.page.id == \x27components\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:components.htm\x27 %}
{% elseif this.page.id == \x27contact\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:contact.htm\x27 %}
{% elseif this.page.id == \x27index\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:index.htm\x27 %}
{% elseif this.page.id == \x27blog-archive\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:blog/archive.htm\x27 %}
    {% set howItsMadeTailorBlueprint = \x27tailor:tailor-blueprint:blog/post.yaml\x27 %}
    {% set howItsMadeTailorContent = \x27entries/blog_post\x27 %}
{% elseif this.page.id == \x27blog-author\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:blog/author.htm\x27 %}
    {% set howItsMadeTailorBlueprint = \x27tailor:tailor-blueprint:blog/author.yaml\x27 %}
    {% set howItsMadeTailorContent = \x27entries/blog_author/\x27 ~ author.id %}
{% elseif this.page.id == \x27blog-index\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:blog/index.htm\x27 %}
    {% set howItsMadeTailorBlueprint = \x27tailor:tailor-blueprint:blog/post.yaml\x27 %}
    {% set howItsMadeTailorContent = \x27entries/blog_post\x27 %}
{% elseif this.page.id == \x27blog-post\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:blog/post.htm\x27 %}
    {% set howItsMadeTailorBlueprint = \x27tailor:tailor-blueprint:blog/post.yaml\x27 %}
    {% set howItsMadeTailorContent = \x27entries/blog_post/\x27 ~ blog.id %}
{% elseif this.page.id == \x27blog-search\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:blog/search.htm\x27 %}
    {% set howItsMadeTailorBlueprint = \x27tailor:tailor-blueprint:blog/post.yaml\x27 %}
    {% set howItsMadeTailorContent = \x27entries/blog_post\x27 %}
{% elseif this.page.id == \x27wiki-article\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:wiki/article.htm\x27 %}
    {% set howItsMadeTailorBlueprint = \x27tailor:tailor-blueprint:wiki/article.yaml\x27 %}
    {% set howItsMadeTailorContent = \x27entries/page_article/\x27 ~ wiki.id %}
{% elseif this.page.id == \x27wiki-index\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:wiki/index.htm\x27 %}
    {% set howItsMadeTailorBlueprint = \x27tailor:tailor-blueprint:wiki/article.yaml\x27 %}
    {% set howItsMadeTailorContent = \x27entries/page_article\x27 %}
{% elseif this.page.id == \x27wiki-search\x27 %}
    {% set howItsMadeCmsTemplate = \x27cms:cms-page:wiki/search.htm\x27 %}
    {% set howItsMadeTailorBlueprint = \x27tailor:tailor-blueprint:wiki/article.yaml\x27 %}
    {% set howItsMadeTailorContent = \x27entries/page_article\x27 %}
{% endif %}

{% if backendUrl and howItsMadeCmsTemplate %}
    <div class=\"how-its-made init\">
        <div>
            <p>Wondering how this page is made? View the
                {% if howItsMadeCmsTemplate|default(false) %}
                    <a target=\"_blank\" href=\"{{ backendUrl ~ \x27/editor?open=\x27 ~ howItsMadeCmsTemplate }}\">CMS Template</a>
                {% endif %}
                {% if howItsMadeTailorBlueprint|default(false) %}
                    •  <a target=\"_blank\" href=\"{{ backendUrl ~ \x27/editor?open=\x27 ~ howItsMadeTailorBlueprint }}\">Tailor Blueprint</a>
                {% endif %}
                {% if howItsMadeTailorContent|default(false) %}
                    •  <a target=\"_blank\" href=\"{{ backendUrl ~ \x27/tailor/\x27 ~ howItsMadeTailorContent }}\">Tailor Content</a>
                {% endif %}
            </p>
        </div>
    </div>
{% endif %}", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/how-its-made.htm", "");
    }
}
