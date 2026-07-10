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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/modals/password-dialog.htm */
class __TwigTemplate_15cfc1acd094da5b3bd5a198ac311fc1 extends Template
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
        yield "<script type=\"text/template\" data-control=\"password-dialog\">
    <div class=\"modal\" data-bs-backdrop=\"static\" data-bs-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-centered\">
            <form method=\"post\" class=\"modal-content\" data-request=\"onConfirmPassword\" data-request-flash>
                <div class=\"modal-header\">
                    <h5 class=\"modal-title fs-5\" data-message>Confirm password</h5>
                </div>
                <div class=\"modal-body\">
                    <p>For your security, please confirm your password to continue.</p>
                    <input
                        name=\"confirmable_password\"
                        type=\"password\"
                        value=\"\"
                        class=\"form-control\"
                        placeholder=\"Current password\"
                    />
                </div>
                <div class=\"modal-footer\">
                    <button type=\"button\" class=\"btn btn-secondary\" data-cancel>
                        Cancel
                    </button>
                    <button type=\"submit\" class=\"btn btn-primary\" data-confirm data-attach-loading>
                        Confirm
                    </button>
                </div>
            </form>
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
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/modals/password-dialog.htm";
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
        return new Source("<script type=\"text/template\" data-control=\"password-dialog\">
    <div class=\"modal\" data-bs-backdrop=\"static\" data-bs-keyboard=\"false\">
        <div class=\"modal-dialog modal-dialog-centered\">
            <form method=\"post\" class=\"modal-content\" data-request=\"onConfirmPassword\" data-request-flash>
                <div class=\"modal-header\">
                    <h5 class=\"modal-title fs-5\" data-message>Confirm password</h5>
                </div>
                <div class=\"modal-body\">
                    <p>For your security, please confirm your password to continue.</p>
                    <input
                        name=\"confirmable_password\"
                        type=\"password\"
                        value=\"\"
                        class=\"form-control\"
                        placeholder=\"Current password\"
                    />
                </div>
                <div class=\"modal-footer\">
                    <button type=\"button\" class=\"btn btn-secondary\" data-cancel>
                        Cancel
                    </button>
                    <button type=\"submit\" class=\"btn btn-primary\" data-confirm data-attach-loading>
                        Confirm
                    </button>
                </div>
            </form>
        </div>
    </div>
</script>", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/modals/password-dialog.htm", "");
    }
}
