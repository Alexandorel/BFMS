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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/head/head-scripts.htm */
class __TwigTemplate_ddbe99549d61123b871024a3cc59a549 extends Template
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
        yield "<script src=\"";
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/vendor/jquery.min.js");
        yield "\"></script>
<script src=\"";
        // line 3
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/vendor/bootstrap/bootstrap.min.js");
        yield "\"></script>
<script src=\"";
        // line 4
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/vendor/codeblocks/codeblocks.min.js");
        yield "\"></script>
<script src=\"";
        // line 5
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/vendor/slick-carousel/slick.min.js");
        yield "\"></script>

<script type=\"module\">
    import PhotoSwipeLightbox from \"";
        // line 8
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/vendor/photoswipe/photoswipe-lightbox.esm.min.js");
        yield "\";
    import PhotoSwipeModule from \"";
        // line 9
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/vendor/photoswipe/photoswipe.esm.min.js");
        yield "\"
    import PhotoSwipeDynamicCaption from \"";
        // line 10
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/vendor/photoswipe-dynamic-caption-plugin/photoswipe-dynamic-caption-plugin.esm.js");
        yield "\";

    window.PhotoSwipeLightbox = PhotoSwipeLightbox;
    window.PhotoSwipeModule = PhotoSwipeModule;
    window.PhotoSwipeDynamicCaption = PhotoSwipeDynamicCaption;
</script>

";
        // line 18
        $_minify = System\Classes\CombineAssets::instance()->useMinify;
        yield '<script src="' . Request::getBasePath() . '/modules/system/assets/js/framework-bundle'.($_minify ? '.min' : '').'.js"></script>' . PHP_EOL;
        yield '<link rel="stylesheet" property="stylesheet" href="' . Request::getBasePath() .'/modules/system/assets/css/framework-extras.css">' . PHP_EOL;
        unset($_minify);
        // line 19
        yield "<script src=\"";
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/js/controls/alert-dialog.js");
        yield "\"></script>
<script src=\"";
        // line 20
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/js/controls/password-dialog.js");
        yield "\"></script>
<script src=\"";
        // line 21
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/js/controls/gallery-slider.js");
        yield "\"></script>
<script src=\"";
        // line 22
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/js/controls/card-slider.js");
        yield "\"></script>
<script src=\"";
        // line 23
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/js/controls/quantity-input.js");
        yield "\"></script>
<script src=\"";
        // line 24
        yield (string) $this->extensions['Cms\Twig\Extension']->themeFilter("assets/js/app.js");
        yield "\"></script>

";
        // line 27
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->assetsFunction('js');
        yield $this->env->getExtension(\Cms\Twig\Extension::class)->displayBlock('scripts');
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/head/head-scripts.htm";
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
        return array (  111 => 27,  106 => 24,  102 => 23,  98 => 22,  94 => 21,  90 => 20,  85 => 19,  80 => 18,  70 => 10,  66 => 9,  62 => 8,  56 => 5,  52 => 4,  48 => 3,  43 => 2,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{# Vendor #}
<script src=\"{{ \x27assets/vendor/jquery.min.js\x27|theme }}\"></script>
<script src=\"{{ \x27assets/vendor/bootstrap/bootstrap.min.js\x27|theme }}\"></script>
<script src=\"{{ \x27assets/vendor/codeblocks/codeblocks.min.js\x27|theme }}\"></script>
<script src=\"{{ \x27assets/vendor/slick-carousel/slick.min.js\x27|theme }}\"></script>

<script type=\"module\">
    import PhotoSwipeLightbox from \"{{ \x27assets/vendor/photoswipe/photoswipe-lightbox.esm.min.js\x27|theme }}\";
    import PhotoSwipeModule from \"{{ \x27assets/vendor/photoswipe/photoswipe.esm.min.js\x27|theme }}\"
    import PhotoSwipeDynamicCaption from \"{{ \x27assets/vendor/photoswipe-dynamic-caption-plugin/photoswipe-dynamic-caption-plugin.esm.js\x27|theme }}\";

    window.PhotoSwipeLightbox = PhotoSwipeLightbox;
    window.PhotoSwipeModule = PhotoSwipeModule;
    window.PhotoSwipeDynamicCaption = PhotoSwipeDynamicCaption;
</script>

{# App #}
{% framework extras %}
<script src=\"{{ \x27assets/js/controls/alert-dialog.js\x27|theme}}\"></script>
<script src=\"{{ \x27assets/js/controls/password-dialog.js\x27|theme}}\"></script>
<script src=\"{{ \x27assets/js/controls/gallery-slider.js\x27|theme}}\"></script>
<script src=\"{{ \x27assets/js/controls/card-slider.js\x27|theme}}\"></script>
<script src=\"{{ \x27assets/js/controls/quantity-input.js\x27|theme}}\"></script>
<script src=\"{{ \x27assets/js/app.js\x27|theme}}\"></script>

{# Placeholder #}
{% scripts %}", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/head/head-scripts.htm", "");
    }
}
