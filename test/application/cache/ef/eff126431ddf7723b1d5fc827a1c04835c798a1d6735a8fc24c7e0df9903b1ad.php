<?php

/* index.html */
class __TwigTemplate_5be0d3513600b36bb059c67a9523d0d65e3a406c2b4b2c559e783c8e1c108457 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!doctype html>
<html lang=\"ja\">
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
    <link rel=\"stylesheet\" href=\"";
        // line 6
        echo twig_escape_filter($this->env, ($context["base_url"] ?? null), "html", null, true);
        echo "node_modules/bootstrap/dist/css/bootstrap.min.css\" />
    <title>CodeIgniterExtension - Test</title>
</head>
<body>
    <div class=\"jumbotron text-center\">
        <h1>CodeIgniterExtension - Test</h1>
    </div>
    <script defer src=\"https://use.fontawesome.com/releases/v5.0.13/js/solid.js\" integrity=\"sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ\" crossorigin=\"anonymous\"></script>
    <script defer src=\"https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js\" integrity=\"sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY\" crossorigin=\"anonymous\"></script>
    <script src=\"";
        // line 15
        echo twig_escape_filter($this->env, ($context["base_url"] ?? null), "html", null, true);
        echo "node_modules/jquery/dist/jquery.min.js\"></script>
    <script src=\"";
        // line 16
        echo twig_escape_filter($this->env, ($context["base_url"] ?? null), "html", null, true);
        echo "node_modules/popper.js/dist/umd/popper.min.js\"></script>
    <script src=\"";
        // line 17
        echo twig_escape_filter($this->env, ($context["base_url"] ?? null), "html", null, true);
        echo "node_modules/bootstrap/dist/js/bootstrap.min.js\"></script>
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  50 => 17,  46 => 16,  42 => 15,  30 => 6,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("<!doctype html>
<html lang=\"ja\">
<head>
    <meta charset=\"utf-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
    <link rel=\"stylesheet\" href=\"{{ base_url }}node_modules/bootstrap/dist/css/bootstrap.min.css\" />
    <title>CodeIgniterExtension - Test</title>
</head>
<body>
    <div class=\"jumbotron text-center\">
        <h1>CodeIgniterExtension - Test</h1>
    </div>
    <script defer src=\"https://use.fontawesome.com/releases/v5.0.13/js/solid.js\" integrity=\"sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ\" crossorigin=\"anonymous\"></script>
    <script defer src=\"https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js\" integrity=\"sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY\" crossorigin=\"anonymous\"></script>
    <script src=\"{{ base_url }}node_modules/jquery/dist/jquery.min.js\"></script>
    <script src=\"{{ base_url }}node_modules/popper.js/dist/umd/popper.min.js\"></script>
    <script src=\"{{ base_url }}node_modules/bootstrap/dist/js/bootstrap.min.js\"></script>
</body>
</html>", "index.html", "/var/www/html/project/codeIgniter-extension/test/application/views/index.html");
    }
}
