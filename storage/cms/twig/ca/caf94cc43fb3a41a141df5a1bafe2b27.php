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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/modals/alert-dialog.htm */
class __TwigTemplate_f721f55ae38fc7b4a2268166b5801621 extends Template
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
        yield "<script type=\"text/template\" data-control=\"alert-dialog\">
    <div class=\"modal\" data-bs-backdrop=\"static\" data-bs-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-centered\">
            <div class=\"modal-content\">
                <div class=\"modal-body\">
                    <p class=\"modal-title lead\" data-message></p>
                </div>
                <div class=\"modal-footer\">
                    <button type=\"button\" class=\"btn btn-secondary\" data-cancel>
                        Cancel
                    </button>
                    <button type=\"button\" class=\"btn btn-primary\" data-ok>
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</script>";
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/modals/alert-dialog.htm";
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
        return new Source("<script type=\"text/template\" data-control=\"alert-dialog\">
    <div class=\"modal\" data-bs-backdrop=\"static\" data-bs-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-centered\">
            <div class=\"modal-content\">
                <div class=\"modal-body\">
                    <p class=\"modal-title lead\" data-message></p>
                </div>
                <div class=\"modal-footer\">
                    <button type=\"button\" class=\"btn btn-secondary\" data-cancel>
                        Cancel
                    </button>
                    <button type=\"button\" class=\"btn btn-primary\" data-ok>
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</script>", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/modals/alert-dialog.htm", "");
    }
}
