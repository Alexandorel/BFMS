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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/modals/ajax-modal.htm */
class __TwigTemplate_2287e00c8051569a1b7c09a762e94fe4 extends Template
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
        yield "<div class=\"modal\" id=\"siteModal\">
    <div class=\"modal-dialog modal-dialog-centered\" id=\"siteModalContent\">
        <!-- Partial Contents Will Go Here -->
    </div>

    <div class=\"modal-dialog modal-dialog-centered modal-loading\">
        <div class=\"spinner-border text-light mx-auto\"></div>
    </div>
</div>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/modals/ajax-modal.htm";
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("<div class=\"modal\" id=\"siteModal\">
    <div class=\"modal-dialog modal-dialog-centered\" id=\"siteModalContent\">
        <!-- Partial Contents Will Go Here -->
    </div>

    <div class=\"modal-dialog modal-dialog-centered modal-loading\">
        <div class=\"spinner-border text-light mx-auto\"></div>
    </div>
</div>", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/modals/ajax-modal.htm", "");
    }
}
