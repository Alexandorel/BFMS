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

/* D:\HolisunInternship\BFMS\themes/demo/partials/site/flash-messages.htm */
class __TwigTemplate_1e97ff7db7fff647f494a00c43bda90d extends Template
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
        $_type = $context['type'] ?? null;
        $_message = $context['message'] ?? null;
        foreach (Flash::messages() as $type => $messages) {
            foreach ($messages as $message) {
                $context['type'] = $type;
                $context['message'] = $message;
                // line 2
                yield "    <p
        data-control=\"flash-message\"
        data-type=\"";
                // line 4
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["type"] ?? null), "html", null, true);
                yield "\"
        data-interval=\"5\">
        ";
                // line 6
                yield (string) $this->env->getRuntime('Twig\Runtime\EscaperRuntime')->escape(($context["message"] ?? null), "html", null, true);
                yield "
    </p>
";
            }
        }
        $context['type'] = $_type;
        $context['message'] = $_message;
        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/flash-messages.htm";
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
        return array (  59 => 6,  54 => 4,  50 => 2,  43 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("{% flash %}
    <p
        data-control=\"flash-message\"
        data-type=\"{{ type }}\"
        data-interval=\"5\">
        {{ message }}
    </p>
{% endflash %}", "D:\\HolisunInternship\\BFMS\\themes/demo/partials/site/flash-messages.htm", "");
    }
}
